<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Xnponlinesimulation_AccessionIdMapping extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add('Legacypage.Misc.Access', 'Xnponlinesimulation_AccessionIdMapping::misc', XCUBE_DELEGATE_PRIORITY_FIRST);
    }

    public static function misc()
    {
        $type = xoops_getrequest('type');
        switch ($type) {
        case 'modeldb_mapping_list':
              self::show_modeldb_mapping_list();
            break;
        case 'content_resolver':
              self::resolve_content_url();
            break;
        }
    }

    /**
     * show accession id mapping list between SimPF and ModelDB.
     */
    private static function show_modeldb_mapping_list()
    {
        $mode = xoops_getrequest('mode');
        if ('url' != $mode) {
            $mode = 'id';
        }
        global $xoopsUser;
        if (!defined('XOONIPS_PATH')) {
            include_once XOOPS_ROOT_PATH.'/modules/xoonips/include/common.inc.php';
        }
        $handler = &xoonips_getormhandler('xnponlinesimulation', 'item_detail');
        $join = new XooNIpsJoinCriteria('xoonips_index_item_link', 'onlinesimulation_id', 'item_id', 'INNER', 'iil');
        $join->cascade(new XooNIpsJoinCriteria('xoonips_index', 'index_id', 'index_id', 'INNER', 'idx'), 'iil', true);
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('model_site_name', 'ModelDB'));
        $criteria->add(new Criteria('open_level', OL_PUBLIC, '=', 'idx'));
        $criteria->add(new Criteria('certify_state', CERTIFIED, '=', 'iil'));
        $criteria->setSort('onlinesimulation_id', 'ASC');

        $distinct = true;
        $res = &$handler->open($criteria, 'onlinesimulation_id, model_contents_url', $distinct, $join);
        header('Content-Type: text/plain');
        while ($obj = &$handler->getNext($res)) {
            $item_id = $obj->get('onlinesimulation_id');
            $item_url = XOONIPS_URL.'/detail.php?item_id='.$item_id;
            $model_url = $obj->get('model_contents_url');
            if (preg_match('/^http:\/\/senselab\.med\.yale\.edu\/modeldb\/ShowModel\.asp\?model=(\d+)$/', $model_url, $matches)) {
                $model_id = $matches[1];
                switch ($mode) {
                case 'id':
                        echo $item_id.' '.$model_id."\r\n";
                    break;
                case 'url':
                        echo $item_url.' '.$model_url."\r\n";
                    break;
                }
            }
        }
        $handler->close($res);
        exit();
    }

    /**
     * resolve SimPF item url from model contents url.
     */
    private static function resolve_content_url()
    {
        $url = xoops_getrequest('url');
        if (empty($url)) {
            self::error404();
        }
        $redirect = xoops_getrequest('redirect');
        $redirect = in_array($redirect, ['true', 'on', 'TRUE', 'ON', '1']) ? true : false;

        if (preg_match('/^http:/', $url)) {
            $url_http = $url;
            $url_https = preg_replace('/^http:/', 'https:', $url);
        } else {
            $url_https = $url;
            $url_http = preg_replace('/^https:/', 'http:', $url);
        }

        global $xoopsUser;
        if (!defined('XOONIPS_PATH')) {
            include_once XOOPS_ROOT_PATH.'/modules/xoonips/include/common.inc.php';
        }
        $handler = &xoonips_getormhandler('xnponlinesimulation', 'item_detail');
        $join = new XooNIpsJoinCriteria('xoonips_index_item_link', 'onlinesimulation_id', 'item_id', 'INNER', 'iil');
        $join->cascade(new XooNIpsJoinCriteria('xoonips_index', 'index_id', 'index_id', 'INNER', 'idx'), 'iil', true);
        $criteria_url = new CriteriaCompo(new Criteria('model_contents_url', $url_http));
        $criteria_url->add(new Criteria('model_contents_url', $url_https), 'OR');
        $criteria = new CriteriaCompo($criteria_url);
        $criteria->add(new Criteria('open_level', OL_PUBLIC, '=', 'idx'));
        $criteria->add(new Criteria('certify_state', CERTIFIED, '=', 'iil'));
        $distinct = true;
        $objs = &$handler->getObjects($criteria, false, 'onlinesimulation_id', $distinct, $join);
        if (empty($objs)) {
            self::error404();
        }
        $obj = $objs[0];
        $item_id = $obj->get('onlinesimulation_id');
        $item_url = XOONIPS_URL.'/detail.php?item_id='.$item_id;
        if ($redirect) {
            header('Location: '.$item_url);
        }
        header('Content-Type: text/plain');
        echo $item_url;
        exit();
    }

    private static function error404()
    {
        $code = '404 Not Found';
        header('Content-Type: text/plain');
        header('HTTP/1.1 '.$code);
        echo $code."\r\n";
        exit();
    }
}
