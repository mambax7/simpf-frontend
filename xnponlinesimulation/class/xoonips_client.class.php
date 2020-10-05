<?php

class XooNIpsClient
{
    public $_client;
    public $_session;
    public $_fault_code = 0;
    public $_fault_string = '';
    public $_extra_errors = [];

    public function __construct($uri)
    {
        $this->_client = new XMLRPC_Client($uri);
        $this->_session = '';
    }

    public function setCondition($name, $value)
    {
        switch ($name) {
        case 'http_auth':
              [$user, $pass, $auth] = $value;
              $this->_client->setAuthentication($user, $pass, $auth);
            break;
        case 'proxy':
              [$host, $port, $uname, $pass, $auth] = $value;
              $this->_client->setProxy($host, $port, $uname, $pass, $auth);
            break;
        case 'user_agent':
              $this->_client->setUserAgent($value);
            break;
        case 'timeout':
              $this->_client->setTimeout($value);
            break;
        default:
            return false;
        }

        return true;
    }

    public function login($user, $pass)
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $user),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $pass),
        ];
        $method = new XMLRPC_Method('XooNIps.login', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            return false;
        }
        $obj = &$res->getParam();
        $this->_session = &$this->_decode_value($obj);

        return true;
    }

    public function logout()
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
        ];
        $method = new XMLRPC_Method('XooNIps.logout', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            return false;
        }
        $obj = &$res->getParam();
        $mes = &$this->_decode_value($obj);
        if ('logged out' != $mes) {
            $this->_fault_code = 999;
            $this->_fault_string = 'Unexpected message returned :'.$mes;

            return false;
        }

        return true;
    }

    public function &getItem($id, $id_type = 'item_id')
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $id),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $id_type),
        ];
        $method = new XMLRPC_Method('XooNIps.getItem', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            $ret = false;

            return $ret;
        }
        $obj = &$res->getParam();
        $item = &$this->_decode_value($obj);

        return $item;
    }

    public function &getSimpleItems($ids, $id_type = 'item_id')
    {
        $ids_array = [];
        foreach ($ids as $id) {
            $ids_array[] = new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $id);
        }
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_ARRAY, $ids_array),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $id_type),
        ];
        $method = new XMLRPC_Method('XooNIps.getSimpleItems', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            $ret = false;

            return $ret;
        }
        $obj = &$res->getParam();
        $item = &$this->_decode_value($obj);

        return $item;
    }

    public function putItem($item_obj, $files_obj)
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            $item_obj,
            $files_obj,
        ];
        $method = new XMLRPC_Method('XooNIps.updateItem', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            return false;
        }
        $obj = &$res->getParam();
        $item_id = &$this->_decode_value($obj);

        return $item_id;
    }

    public function updateItem($item_obj)
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            $item_obj,
        ];
        $method = new XMLRPC_Method('XooNIps.updateItem', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            return false;
        }
        $obj = &$res->getParam();
        $item_id = &$this->_decode_value($obj);

        return $item_id;
    }

    public function removeItem($id, $id_type = 'item_id')
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $id),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $id_type),
        ];
        $method = new XMLRPC_Method('XooNIps.removeItem', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            return false;
        }
        $obj = &$res->getParam();
        $item_id = &$this->_decode_value($obj);

        return $item_id;
    }

    public function &getFile($file_id, $agreement)
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_INTEGER, $file_id),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_BOOLEAN, $agreement),
        ];
        $method = new XMLRPC_Method('XooNIps.getFile', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            $ret = false;

            return $ret;
        }
        $obj = &$res->getParam();
        $file = &$this->_decode_value($obj);

        return $file;
    }

    public function updateFile($id, $id_type, $field_name, $file_obj)
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $id),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $id_type),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $field_name),
            $file_obj,
        ];
        $method = new XMLRPC_Method('XooNIps.updateFile', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            return false;
        }
        $obj = &$res->getParam();
        $file_id = &$this->_decode_value($obj);

        return $file_id;
    }

    public function removeFile($file_id)
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_INTEGER, $file_id),
        ];
        $method = new XMLRPC_Method('XooNIps.removeFile', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            return false;
        }
        $obj = &$res->getParam();
        $file_id = &$this->_decode_value($obj);

        return $file_id;
    }

    public function &getRootIndex($open_level)
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $open_level),
        ];
        $method = new XMLRPC_Method('XooNIps.getRootIndex', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            $ret = false;

            return $ret;
        }
        $obj = &$res->getParam();
        $index = &$this->_decode_value($obj);

        return $index;
    }

    public function &getIndex($index_id)
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_INTEGER, $index_id),
        ];
        $method = new XMLRPC_Method('XooNIps.getIndex', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            $ret = false;

            return $ret;
        }
        $obj = &$res->getParam();
        $index = &$this->_decode_value($obj);

        return $index;
    }

    public function &getChildIndexes($index_id)
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_INTEGER, $index_id),
        ];
        $method = new XMLRPC_Method('XooNIps.getChildIndex', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            $ret = false;

            return $ret;
        }
        $obj = &$res->getParam();
        $indexes = &$this->_decode_value($obj);

        return $indexes;
    }

    public function &searchItem($query, $start = 0, $limit = 20, $sort = 'title', $order = 'asc')
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $query),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_INTEGER, $start),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_INTEGER, $limit),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $sort),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $order),
        ];
        $method = new XMLRPC_Method('XooNIps.searchItem', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            $ret = false;

            return $ret;
        }
        $obj = &$res->getParam();
        $results = &$this->_decode_value($obj);

        return $results;
    }

    public function &getItemtypes()
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
        ];
        $method = new XMLRPC_Method('XooNIps.getItemtypes', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            $ret = false;

            return $ret;
        }
        $obj = &$res->getParam();
        $itemtypes = &$this->_decode_value($obj);

        return $itemtypes;
    }

    public function &getItemtype($itemtype_id)
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_INTEGER, $itemtype_id),
        ];
        $method = new XMLRPC_Method('XooNIps.getItemtype', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            $ret = false;

            return $ret;
        }
        $obj = &$res->getParam();
        $itemtype = &$this->_decode_value($obj);

        return $itemtype;
    }

    public function &getPreference()
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
        ];
        $method = new XMLRPC_Method('XooNIps.getPreference', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            $ret = false;

            return $ret;
        }
        $obj = &$res->getParam();
        $preferences = &$this->_decode_value($obj);

        return $preferences;
    }

    public function updateItem2($item_obj, $file_obj, $delete_item_ids)
    {
        $item_ids = [];
        foreach ($delete_item_ids as $item_id) {
            $item_ids[] = new XMLRPC_Value(XMLRPC_VALUE_TYPE_INTEGER, $item_id);
        }
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            $item_obj,
            $file_obj,
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_ARRAY, $item_ids),
        ];
        $method = new XMLRPC_Method('XooNIps.updateItem2', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            return false;
        }
        $obj = &$res->getParam();
        $item_id = &$this->_decode_value($obj);

        return $item_id;
    }

    public function &getFileMetadata($file_id)
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_INTEGER, $file_id),
        ];
        $method = new XMLRPC_Method('XooNIps.getFileMetadata', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            $ret = false;

            return $ret;
        }
        $obj = &$res->getParam();
        $metadata = &$this->_decode_value($obj);

        return $metadata;
    }

    public function &getIndexPathNames($index_ids)
    {
        $xids = [];
        foreach ($index_ids as $index_id) {
            $xids[] = new XMLRPC_Value(XMLRPC_VALUE_TYPE_INTEGER, $index_id);
        }
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_ARRAY, $xids),
        ];
        $method = new XMLRPC_Method('XooNIps.getIndexPathNames', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            $ret = false;

            return $ret;
        }
        $obj = &$res->getParam();
        $index_paths = &$this->_decode_value($obj);

        return $index_paths;
    }

    public function &getItemPermission($id, $id_type)
    {
        $params = [
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $this->_session),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $id),
            new XMLRPC_Value(XMLRPC_VALUE_TYPE_STRING, $id_type),
        ];
        $method = new XMLRPC_Method('XooNIps.getItemPermission', $params);
        $res = &$this->_send($method);
        if (false === $res) {
            $ret = false;

            return $ret;
        }
        $obj = &$res->getParam();
        $perm = &$this->_decode_value($obj);

        return $perm;
    }

    public function dumpError()
    {
        if (0 == $this->_fault_code) {
            echo "No Errors\n";

            return;
        }
        echo 'Error('.$this->_fault_code.') : "'.$this->_fault_string.'"'."\n";
    }

    public function &_send($method)
    {
        $this->_fault_code = 0;
        $this->_fault_string = '';
        $this->_extra_errors = [];
        $falsevar = false;
        $res = $this->_client->sendMessage($method);
        if (false === $res) {
            $this->_fault_code = 999;
            $this->_fault_string = implode("\n", $this->_client->getError());

            return $falsevar;
        }
        if ($res->isFault()) {
            $this->_fault_code = $res->getFaultCode();
            $this->_fault_string = $res->getFaultString();
            if (106 == $this->_fault_code) {
                // xoonips error
                $this->_set_xoonips_error($this->_fault_string);
            }

            return $falsevar;
        }

        return $res;
    }

    public function &_decode_value($obj)
    {
        $type = $obj->getType();
        $value = $obj->getValue();
        $ret = null;
        switch ($type) {
        case XMLRPC_VALUE_TYPE_ARRAY:
              $ret = [];
            foreach ($value as $tmp) {
                $ret[] = &$this->_decode_value($tmp);
            }
            break;
        case XMLRPC_VALUE_TYPE_STRUCT:
              $ret = [];
            foreach ($value as $name => $tmp) {
                $ret[$name] = &$this->_decode_value($tmp);
            }
            break;
        default:
              $ret = $value;
        }

        return $ret;
    }

    public function _set_xoonips_error($str)
    {
        $xoonips_errors = [
            100 => 'uncategolized error',
            101 => 'invalid session',
            102 => 'failed to login',
            103 => 'access forbidden',
            104 => 'contents not found',
            105 => 'incomplete required parameters',
            106 => 'missing arguments',
            107 => 'too meny arguments',
            108 => 'invalid argument type',
            109 => 'internal server error',
        ];
        if (!preg_match("/^(Method response error)\n(.*)/", $str, $matches)) {
            return false;
        }
        $messages[] = $matches[1];
        $this->_extra_errors = @unserialize($matches[2]);
        foreach ($this->_extra_errors as $err) {
            $mes = ''.$err['code'].' - ';
            $mes .= $xoonips_errors[$err['code']] ?? 'unexpected error';
            if ('' != $err['extra']) {
                $mes .= ' : '.$err['extra'];
            }
            $messages[] = $mes;
        }
        $this->_fault_string = implode("\n", $messages);

        return true;
    }
}
