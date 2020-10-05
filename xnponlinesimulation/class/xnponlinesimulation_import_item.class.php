<?php

//  $Id:$
//  ------------------------------------------------------------------------ //
//  XooNIps Online Simulator Item Type Module                                //
//                                                                           //
//  Copyright (C) 2011 RIKEN BSI Neuroinformatics Japan Center               //
//  All rights reserved.                                                     //
//  http://sim.neuroinf.jp/                                                  //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

require_once dirname(dirname(__DIR__)) . '/xoonips/class/xoonips_import_item.class.php';

class XNPOnlinesimulationImportItem extends XooNIpsImportItem
{
    /**
     * flag for preview exists.
     */
    public $_has_preview = false;

    /**
     * constructor.
     */
    public function __construct()
    {
        $handler = &xoonips_getormcompohandler('xnponlinesimulation', 'item');
        $this->_item = &$handler->create();
    }

    /**
     * set flag for preview exists.
     */
    public function setHasPreview()
    {
        $this->_has_preview = true;
    }

    /**
     * unset flag for preview exists.
     */
    public function unsetHasPreview()
    {
        $this->_has_preview = false;
    }

    /**
     * get flag for preview exists.
     *
     * @return bool true if preview exists
     */
    public function hasPreview()
    {
        return $this->_has_preview;
    }

    /**
     * get total file size(bytes) of this item.
     *
     * @return int file size in bytes
     */
    public function getTotalFileSize()
    {
        $size = 0;
        foreach ($this->getVar('preview') as $preview) {
            $size += $preview->get('file_size');
        }

        return $size;
    }

    /**
     * get clone object.
     *
     * return &object cloned object
     */
    public function &getClone()
    {
        $clone = &parent::getClone();
        if ($this->_has_preview) {
            $clone->setHasPreview();
        } else {
            $clone->unsetHasPreview();
        }

        return $clone;
    }
}

class XNPOnlinesimulationImportItemHandler extends XooNIpsImportItemHandler
{
    /**
     * array of supported version of import file.
     */
    public $_import_file_version = ['1.00'];

    /**
     * version string of detail information.
     */
    public $_detail_version = '1.00';

    /**
     * attachment file object(XooNIpsFile).
     */
    public $_preview = null;

    /**
     * flag of attachment file parsed.
     */
    public $_preview_flag = false;

    /**
     * flag of file type attribute.
     */
    public $_file_type_attribute = null;

    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * create import item object.
     *
     * @return object created object
     */
    public function create()
    {
        return new XNPOnlinesimulationImportItem();
    }

    /**
     * handler for xml start element.
     *
     * @parem resource $parser parser resource
     *
     * @param string $name element name
     * @param array  $attrib attributes
     */
    public function xmlStartElementHandler($parser, $name, $attribs)
    {
        parent::xmlStartElementHandler($parser, $name, $attribs);
        $tags = implode('/', $this->_tag_stack);
        switch ($tags) {
        case 'ITEM/DETAIL':
            // detect and validate version
            if (isset($attribs['VERSION'])) {
                if (!in_array($attribs['VERSION'], $this->_supported_detail_version)) {
                    $error = sprintf('unsupported version %s %s', $attribs['VERSION'], $this->_get_parser_error_at());
                    $this->_import_item->setErrors(E_XOONIPS_INVALID_VERSION, $error);
                }
                $this->_detail_version = $attribs['VERSION'];
            }
            break;
        case 'ITEM/DETAIL/FILE':
            $this->_file_type_attribute = $attribs['FILE_TYPE_NAME'];
            $file_type_handler = &xoonips_getormhandler('xoonips', 'file_type');
            $file_handler = &xoonips_getormhandler('xoonips', 'file');
            $criteria = new Criteria('name', addslashes($attribs['FILE_TYPE_NAME']));
            $file_type = &$file_type_handler->getObjects($criteria);
            if (0 == count($file_type)) {
                $this->_import_item->setErrors(E_XOONIPS_ATTR_NOT_FOUND, 'file_type_id is not found:'.$attribs['FILE_TYPE_NAME'].$this->_get_parser_error_at());
                break;
            }
            if (strstr($attribs['FILE_NAME'], '..')) {
                $this->_import_item->setErrors(E_XOONIPS_ATTR_INVALID_VALUE, 'invalid file_name attribute:'.$attribs['FILE_NAME'].$this->_get_parser_error_at());
                break;
            }
            $unicode = &xoonips_getutility('unicode');
            if ('preview' == $this->_file_type_attribute) {
                $this->_preview = &$file_handler->create();
                $this->_preview->setFilepath($this->_attachment_dir.'/'.$attribs['FILE_NAME']);
                $this->_preview->set('original_file_name', $unicode->decode_utf8($attribs['ORIGINAL_FILE_NAME'], xoonips_get_server_charset(), 'h'));
                $this->_preview->set('mime_type', $attribs['MIME_TYPE']);
                $this->_preview->set('file_size', $attribs['FILE_SIZE']);
                $this->_preview->set('sess_id', session_id());
                $this->_preview->set('file_type_id', $file_type[0]->get('file_type_id'));
            } else {
                $this->_import_item->setErrors(E_XOONIPS_ATTR_NOT_FOUND, 'file_type_name is not found:'.$this->_file_type_attribute.$this->_get_parser_error_at());
            }
            break;
        }
    }

    /**
     * xml end element handler.
     *
     * @parem resource $parser parser resource
     *
     * @param string $name element name
     */
    public function xmlEndElementHandler($parser, $name)
    {
        $detail = &$this->_import_item->getVar('detail');
        $unicode = &xoonips_getutility('unicode');
        $value = trim($this->_cdata);
        $tags = implode('/', $this->_tag_stack);
        switch ($tags) {
        case 'ITEM/DETAIL':
            $requires = ['vm_type', 'download_url', 'contents_count', 'model_contents_url', 'model_contents_count', 'model_site_name'];
            foreach ($requires as $key) {
                if (null === $detail->get($key, 'n')) {
                    $this->_import_item->setErrors(E_XOONIPS_TAG_NOT_FOUND, ' no '.$key.' '.$this->_get_parser_error_at());
                }
            }
            break;
        case 'ITEM/DETAIL/VM_TYPE':
        case 'ITEM/DETAIL/DOWNLOAD_URL':
        case 'ITEM/DETAIL/CONTENTS_COUNT':
        case 'ITEM/DETAIL/MODEL_CONTENTS_URL':
        case 'ITEM/DETAIL/MODEL_CONTENTS_COUNT':
        case 'ITEM/DETAIL/MODEL_SITE_NAME':
        case 'ITEM/DETAIL/SIMULATOR_NAME':
        case 'ITEM/DETAIL/SIMULATOR_VERSION':
            $value = $unicode->decode_utf8($value, _CHARSET, 'h');
            $detail->set(strtolower($name), $value);
            break;
        case 'ITEM/DETAIL/FILE':
            $file_handler = &xoonips_getormhandler('xoonips', 'file');
            if ('preview' == $this->_file_type_attribute) {
                $this->_preview_flag = true;
                if (!$file_handler->insert($this->_preview)) {
                    $this->_import_item->setErrors(E_XOONIPS_DB_QUERY, 'can\'t insert attachment file:'.$this->_preview->get('original_file_name').$this->_get_parser_error_at());
                }
                $this->_preview = $file_handler->get($this->_preview->get('file_id'));
                $previews = &$this->_import_item->getVar('preview');
                $previews[] = $this->_preview;
                $this->_import_item->setHasPreview();
                $this->_file_type_attribute = null;
            }
            break;
        case 'ITEM/DETAIL/FILE/CAPTION':
            if ('preview' == $this->_file_type_attribute) {
                $this->_preview->set('caption', $unicode->decode_utf8($this->_cdata, xoonips_get_server_charset(), 'h'));
            }
            break;
        case 'ITEM/DETAIL/FILE/THUMBNAIL':
            if ('preview' == $this->_file_type_attribute) {
                $this->_preview->set('thumbnail_file', base64_decode($this->_cdata));
            }
            break;
        }
        parent::xmlEndElementHandler($parser, $name);
    }

    /**
     * insert item compo object.
     *
     * @param object &$item item compo object
     *
     * @return bool false if failure
     */
    public function insert(&$item)
    {
        $handler = &xoonips_getormcompohandler('xnponlinesimulation', 'item');

        return $handler->insert($item);
    }

    /**
     * set new flag to item compo object.
     *
     * @param object &$item item compo object
     *
     * @return bool false if failure
     */
    public function setNew(&$item)
    {
        $handler = &xoonips_getormcompohandler('xnponlinesimulation', 'item');

        return $handler->setNew($item);
    }

    /**
     * unset new flag to item compo object.
     *
     * @param object &$item item compo object
     *
     * @return bool false if failure
     */
    public function unsetNew(&$item)
    {
        $handler = &xoonips_getormcompohandler('xnponlinesimulation', 'item');

        return $handler->unsetNew($item);
    }

    /**
     * set dirty flag to item compo object.
     *
     * @param object &$item item compo object
     *
     * @return bool false if failure
     */
    public function setDirty(&$item)
    {
        $handler = &xoonips_getormcompohandler('xnponlinesimulation', 'item');

        return $handler->setDirty($item);
    }

    /**
     * unset dirty flag to item compo object.
     *
     * @param object &$item item compo object
     *
     * @return bool false if failure
     */
    public function unsetDirty(&$item)
    {
        $handler = &xoonips_getormcompohandler('xnponlinesimulation', 'item');

        return $handler->unsetDirty($item);
    }

    /**
     * import item.
     *
     * @param object &$item item
     */
    public function import(&$item)
    {
        if ($item->getUpdateFlag()) {
            $detail = &$item->getVar('detail');
            $detail->unsetNew();
            $detail->setDirty();
            // copy attachment file
            if ($item->hasPreview()) {
                $file_handler = &xoonips_getormhandler('xoonips', 'file');
                $previews = [];
                foreach ($item->getVar('preview') as $preview) {
                    $clonefile = &$file_handler->fileClone($preview);
                    $clonefile->setDirty();
                    $previews[] = &$preview;
                }
                $item->setVar('preview', $previews);
            }
        }
        parent::import($item);
    }

    /**
     * get imported log.
     *
     * @param object $item item compo object
     *
     * @return string text log
     */
    public function getImportLog($item)
    {
        $text = parent::getImportLog($item);
        $detail = &$item->getVar('detail');
        foreach ($detail->getArray() as $key => $value) {
            $value = str_replace(["\n", '\\'], ['\\n', '\\\\'], $value);
            $text .= "\n".sprintf('detail.%s %s', $key, $value);
        }

        return $text;
    }

    /**
     * call back handler for import finished.
     *
     * @param object &$item  imported item compo object
     * @param array  &$items array of all imported item compo objects
     */
    public function onImportFinished(&$item, &$items)
    {
        if ('xnponlinesimulationimportitem' != strtolower(get_class($item))) {
            return;
        }

        $this->_set_file_delete_flag($item);

        // nothing to do if no previews
        $previews = &$item->getVar('preview');
        foreach (array_keys($previews) as $key) {
            if ($previews[$key]->get('file_id') > 0) {
                $this->_fix_item_id_of_file($item, $previews[$key]);
                $this->_create_text_search_index($previews[$key]);
            }
        }

        parent::onImportFinished($item, $items);
    }
}
