<?php

//  $Id:$
//  ------------------------------------------------------------------------ //
//  XooNIps Online Simulator Item Type Module                                //
//                                                                           //
//  Copyright (C) 2011-2013 RIKEN BSI Neuroinformatics Japan Center          //
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

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

$itemtype_path = dirname(dirname(__FILE__));
$itemtype_dirname = basename($itemtype_path);

$langman = &xoonips_getutility('languagemanager');
$langman->read('main.php', $itemtype_dirname);

/**
 * Public Functions.
 */

/**
 * get detail information.
 *
 * @param int $item_id item id
 *
 * @return array detail informations
 */
function xnponlinesimulationGetDetailInformation($item_id)
{
    $detail_handler = &xoonips_getormhandler('xnponlinesimulation', 'item_detail');
    $detail_obj = false;
    if (0 != $item_id) {
        $detail_obj = &$detail_handler->get($item_id);
    }
    if (empty($detail_obj)) {
        $detail_obj = &$detail_handler->create();
    }
    $detail = $detail_obj->getArray();

    return $detail;
}

/**
 * get top block.
 *
 * @param array $itemtype item type information
 *
 * @return string html
 */
function xnponlinesimulationGetTopBlock($itemtype)
{
    $mydirname = basename(dirname(dirname(__FILE__)));
    $mod_path = XOOPS_ROOT_PATH.'/modules/'.$mydirname;
    $mod_url = XOOPS_URL.'/modules/'.$mydirname;

    // assign template
    global $xoopsTpl;
    $tpl = new XoopsTpl();
    $tpl->plugins_dir[] = $mod_path.'/class/smarty/plugins';
    $tpl->assign($xoopsTpl->get_template_vars());
    $tpl->assign('mydirname', $mydirname);
    $tpl->assign('mod_path', $mod_path);
    $tpl->assign('mod_url', $mod_url);
    $tpl->assign('xoonips_path', XOONIPS_PATH);
    $tpl->assign('xoonips_url', XOONIPS_URL);
    $tpl->assign('itemtype', $itemtype);
    $tpl->assign('vm_types', _xnponlinesimulationGetVmTypes());
    $html = $tpl->fetch('db:'.$mydirname.'_top_block.html');

    return $html;
}

/**
 * get list block.
 *
 * @param array $basic item basic information
 *
 * @return string html
 */
function xnponlinesimulationGetListBlock($basic)
{
    $item_id = $basic['item_id'];
    $mydirname = basename(dirname(dirname(__FILE__)));
    $mod_path = XOOPS_ROOT_PATH.'/modules/'.$mydirname;
    $mod_url = XOOPS_URL.'/modules/'.$mydirname;
    $detail = xnponlinesimulationGetDetailInformation($item_id);
    $doi = $basic['doi'];
    $item_url = _xnponlinesimulationGetItemUrl($item_id, $doi);
    $screenshot = _xnponlinesimulationGetScreenshotInformation($item_id);
    static $is_first;
    $is_first = is_null($is_first);

    // fixed bug for XooNIpsItemLibraryObject::getBasicInformationArray()
    if (!is_numeric($basic['creation_date'])) {
        $basic['creation_date'] = strtotime($basic['creation_date']);
    }

    // assign template
    global $xoopsTpl;
    $tpl = new XoopsTpl();
    $tpl->plugins_dir[] = $mod_path.'/class/smarty/plugins';
    $tpl->assign($xoopsTpl->get_template_vars());
    $tpl->assign('item_id', $item_id);
    $tpl->assign('mydirname', $mydirname);
    $tpl->assign('mod_path', $mod_path);
    $tpl->assign('mod_url', $mod_url);
    $tpl->assign('xoonips_path', XOONIPS_PATH);
    $tpl->assign('xoonips_url', XOONIPS_URL);
    $tpl->assign('item_url', $item_url);
    $tpl->assign('is_first', $is_first);
    $tpl->assign('screenshot', $screenshot);
    $tpl->assign('basic', $basic);
    $tpl->assign('detail', $detail);
    $tpl->assign('is_pending', xnpIsPending($item_id));
    $tpl->assign('vm_types', _xnponlinesimulationGetVmTypes());

    return $tpl->fetch('db:'.$mydirname.'_list_block.html');
}

/**
 * get printer friendly list block.
 *
 * @param array $basic item basic information
 *
 * @return string html
 */
function xnponlinesimulationGetPrinterFriendlyListBlock($basic)
{
    return xnponlinesimulationGetListBlock($basic);
}

/**
 * get detail block.
 *
 * @param int $item_id item id
 *
 * @return string html
 */
function xnponlinesimulationGetDetailBlock($item_id)
{
    $mydirname = basename(dirname(dirname(__FILE__)));
    $mod_path = XOOPS_ROOT_PATH.'/modules/'.$mydirname;
    $mod_url = XOOPS_URL.'/modules/'.$mydirname;
    $basic = xnpGetBasicInformationDetailBlock($item_id);
    $detail = xnponlinesimulationGetDetailInformation($item_id);
    $preview = xnpGetPreviewDetailBlock($item_id);
    $index = xnpGetIndexDetailBlock($item_id);
    $doi = $basic['doi']['value'];
    $item_url = _xnponlinesimulationGetItemUrl($item_id, $doi);
    $screenshot = _xnponlinesimulationGetScreenshotInformation($item_id);

    // get autorun flag
    $autorun = xoops_getrequest('autorun');
    $autorun = in_array($autorun, array('on', 'ON', '1', 'true', 'TRUE'));

    // get item viewed count
    $ranking_handler = &xoonips_gethandler('xoonips', 'ranking');
    $viewed_count = $ranking_handler->get_count_viewed_item($item_id);

    // assign template
    global $xoopsTpl;
    // override ShadowBox into xoops_module_header
    $xoops_module_header = sprintf('<script type="text/javascript" src="%s/js/shadowbox/shadowbox.js"></script>', $mod_url);
    $xoops_module_header .= sprintf('<link rel="stylesheet" type="text/css" href="%s/js/shadowbox/shadowbox.css" />', $mod_url);
    $xoops_module_header .= $xoopsTpl->get_template_vars('xoops_module_header');
    $xoopsTpl->assign('xoops_module_header', $xoops_module_header);
    $tpl = new XoopsTpl();
    $tpl->plugins_dir[] = $mod_path.'/class/smarty/plugins';
    $tpl->assign($xoopsTpl->get_template_vars());
    $tpl->assign('item_id', $item_id);
    $tpl->assign('mydirname', $mydirname);
    $tpl->assign('mod_path', $mod_path);
    $tpl->assign('mod_url', $mod_url);
    $tpl->assign('xoonips_path', XOONIPS_PATH);
    $tpl->assign('xoonips_url', XOONIPS_URL);
    $tpl->assign('item_url', $item_url);
    $tpl->assign('screenshot', $screenshot);
    $tpl->assign('basic', $basic);
    $tpl->assign('detail', $detail);
    $tpl->assign('index', $index);
    $tpl->assign('preview', $preview);
    $tpl->assign('autorun', $autorun);
    $tpl->assign('viewed_count', $viewed_count);
    $tpl->assign('is_printable', false);
    $tpl->assign('is_editable', xnp_get_item_permission($_SESSION['XNPSID'], $item_id, OP_MODIFY));
    $tpl->assign('vm_types', _xnponlinesimulationGetVmTypes());

    return $tpl->fetch('db:'.$mydirname.'_detail_block.html');
}

/**
 * get printer friendly detail block.
 *
 * @param int $item_id item id
 *
 * @return string html
 */
function xnponlinesimulationGetPrinterFriendlyDetailBlock($item_id)
{
    $mydirname = basename(dirname(dirname(__FILE__)));
    $mod_path = XOOPS_ROOT_PATH.'/modules/'.$mydirname;
    $mod_url = XOOPS_URL.'/modules/'.$mydirname;
    $basic = xnpGetBasicInformationPrinterFriendlyBlock($item_id);
    $detail = xnponlinesimulationGetDetailInformation($item_id);
    $preview = xnpGetPreviewPrinterFriendlyBlock($item_id);
    $index = xnpGetIndexPrinterFriendlyBlock($item_id);
    $doi = $basic['doi']['value'];
    $item_url = _xnponlinesimulationGetItemUrl($item_id, $doi);
    $screenshot = _xnponlinesimulationGetScreenshotInformation($item_id);

    // get autorun flag
    $autorun = false;

    // get item viewed count
    $ranking_handler = &xoonips_gethandler('xoonips', 'ranking');
    $viewed_count = $ranking_handler->get_count_viewed_item($item_id);

    // assign template
    global $xoopsTpl;
    $tpl = new XoopsTpl();
    $tpl->plugins_dir[] = $mod_path.'/class/smarty/plugins';
    $tpl->assign($xoopsTpl->get_template_vars());
    $tpl->assign('item_id', $item_id);
    $tpl->assign('mydirname', $mydirname);
    $tpl->assign('mod_path', $mod_path);
    $tpl->assign('mod_url', $mod_url);
    $tpl->assign('xoonips_path', XOONIPS_PATH);
    $tpl->assign('xoonips_url', XOONIPS_URL);
    $tpl->assign('item_url', $item_url);
    $tpl->assign('screenshot', $screenshot);
    $tpl->assign('basic', $basic);
    $tpl->assign('detail', $detail);
    $tpl->assign('preview', $preview);
    $tpl->assign('index', $index);
    $tpl->assign('autorun', $autorun);
    $tpl->assign('viewed_count', $viewed_count);
    $tpl->assign('is_printable', true);
    $tpl->assign('is_editable', false);
    $tpl->assign('vm_types', _xnponlinesimulationGetVmTypes());

    return $tpl->fetch('db:'.$mydirname.'_detail_block.html');
}

/**
 * get register block.
 *
 * @return string html
 */
function xnponlinesimulationGetRegisterBlock()
{
    return xnponlinesimulationGetEditBlock(0);
}

/**
 * get edit block.
 *
 * @param int $item_id item id
 *
 * @return string html
 */
function xnponlinesimulationGetEditBlock($item_id)
{
    $mydirname = basename(dirname(dirname(__FILE__)));
    $mod_path = XOOPS_ROOT_PATH.'/modules/'.$mydirname;
    $mod_url = XOOPS_URL.'/modules/'.$mydirname;

    $detail = xnponlinesimulationGetDetailInformation($item_id);
    if (0 == $item_id) {
        // register
        $basic = xnpGetBasicInformationRegisterBlock();
        $detail = _xnponlinesimulationFetchDetailInformation($detail);
        $preview = xnpGetPreviewRegisterBlock();
        $index = xnpGetIndexRegisterBlock();
        $is_register = true;
    } else {
        // edit
        $basic = xnpGetBasicInformationEditBlock($item_id);
        $detail = _xnponlinesimulationFetchDetailInformation($detail);
        $preview = xnpGetPreviewEditBlock($item_id);
        $index = xnpGetIndexEditBlock($item_id);
        $is_register = false;
    }

    // assign to template
    global $xoopsTpl;
    $tpl = new XoopsTpl();
    $tpl->plugins_dir[] = $mod_path.'/class/smarty/plugins';
    $tpl->assign($xoopsTpl->get_template_vars());
    $tpl->assign('item_id', $item_id);
    $tpl->assign('mydirname', $mydirname);
    $tpl->assign('mod_path', $mod_path);
    $tpl->assign('mod_url', $mod_url);
    $tpl->assign('xoonips_path', XOONIPS_PATH);
    $tpl->assign('xoonips_url', XOONIPS_URL);
    $tpl->assign('basic', $basic);
    $tpl->assign('detail', $detail);
    $tpl->assign('preview', $preview);
    $tpl->assign('index', $index);
    $tpl->assign('is_register', $is_register);
    $tpl->assign('vm_types', _xnponlinesimulationGetVmTypes());

    return $tpl->fetch('db:'.$mydirname.'_register_block.html');
}

/**
 * check register parameters.
 *
 * @param string &$message message
 *
 * @return bool false if error occured
 */
function xnponlinesimulationCheckRegisterParameters(&$message)
{
    return xnponlinesimulationCheckEditParameters($message);
}

/**
 * check edit parameters.
 *
 * @param string &$message message
 *
 * @return bool false if error occured
 */
function xnponlinesimulationCheckEditParameters(&$message)
{
    $messages = array();
    $formdata = &xoonips_getutility('formdata');
    // model contents url
    $model_contents_url = $formdata->getValue('post', 'model_contents_url', 's', false);
    if (empty($model_contents_url)) {
        $messages[] = _MD_XNPONLINESIMULATION_ERR_MODEL_CONTENTS_URL_REQUIRED;
    }
    // vm type
    $vm_type = $formdata->getValue('post', 'vm_type', 's', false);
    if (empty($vm_type)) {
        $messages[] = _MD_XNPONLINESIMULATION_ERR_VM_TYPE_REQUIRED;
    }
    // download url
    $download_url = $formdata->getValue('post', 'download_url', 's', false);
    if (empty($download_url)) {
        $messages[] = _MD_XNPONLINESIMULATION_ERR_DOWNLOAD_URL_REQUIRED;
    }
    // model site name
    $model_site_name = $formdata->getValue('post', 'model_site_name', 's', false);
    if (empty($model_site_name)) {
        $messages[] = _MD_XNPONLINESIMULATION_ERR_MODEL_SITE_NAME_REQUIRED;
    }

    $message = "<span style=\"color: red;\"><br />\n".implode("<br />\n", $messages).'</span>';

    return 0 == count($messages);
}

/**
 * get confirm block.
 *
 * @param int $item_id item id
 *
 * @return string html
 */
function xnponlinesimulationGetConfirmBlock($item_id)
{
    $mydirname = basename(dirname(dirname(__FILE__)));
    $mod_path = XOOPS_ROOT_PATH.'/modules/'.$mydirname;
    $mod_url = XOOPS_URL.'/modules/'.$mydirname;

    $basic = xnpGetBasicInformationConfirmBlock($item_id);
    $preview = xnpGetPreviewConfirmBlock($item_id);
    $index = xnpGetIndexConfirmBlock($item_id);
    $detail = xnponlinesimulationGetDetailInformation($item_id);
    $detail = _xnponlinesimulationFetchDetailInformation($detail);
    foreach (array_keys($detail) as $key) {
        $detail[$key] = array('value' => $detail[$key]);
    }
    xnpConfirmHtml($detail, $mydirname.'_item_detail', null, _CHARSET);

    // trim strings
    if (xnpHasWithout($basic) || xnpHasWithout($preview) || xnpHasWithout($detail)) {
        global $system_message;
        $system_message .= "\n".'<br /><span style="color: red;">'._MD_XOONIPS_ITEM_WARNING_FIELD_TRIM.'</span><br />';
    }

    // assign to template
    global $xoopsTpl;
    $tpl = new XoopsTpl();
    $tpl->plugins_dir[] = $mod_path.'/class/smarty/plugins';
    $tpl->assign($xoopsTpl->get_template_vars());
    $tpl->assign('item_id', $item_id);
    $tpl->assign('mydirname', $mydirname);
    $tpl->assign('mod_path', $mod_path);
    $tpl->assign('mod_url', $mod_url);
    $tpl->assign('xoonips_path', XOONIPS_PATH);
    $tpl->assign('xoonips_url', XOONIPS_URL);
    $tpl->assign('basic', $basic);
    $tpl->assign('detail', $detail);
    $tpl->assign('preview', $preview);
    $tpl->assign('index', $index);
    $tpl->assign('vm_types', _xnponlinesimulationGetVmTypes());

    return $tpl->fetch('db:'.$mydirname.'_confirm_block.html');
}

/**
 * insert item.
 *
 * @param int &$item_id inserted item id
 *
 * @return bool false if failure
 */
function xnponlinesimulationInsertItem(&$item_id)
{
    $mydirname = basename(dirname(dirname(__FILE__)));
    $mod_path = XOOPS_ROOT_PATH.'/modules/'.$mydirname;
    $mod_url = XOOPS_URL.'/modules/'.$mydirname;

    $is_new = (0 == $item_id);
    $formdata = &xoonips_getutility('formdata');
    $detail_handler = &xoonips_getormhandler($mydirname, 'item_detail');
    // get form request
    $detail = xnponlinesimulationGetDetailInformation($item_id);
    $detail = _xnponlinesimulationFetchDetailInformation($detail);
    // sanitize url
    $replace = array('/javascript:/i', '/[\\x00-\\x1f]/');
    foreach (array('vm_type', 'download_url', 'model_contents_url') as $key) {
        $detail[$key] = trim(preg_replace($replace, '', $detail[$key]));
    }
    // trim oversized data
    xnpTrimColumn($detail, $mydirname.'_item_detail', array_keys($detail), _CHARSET);
    if ($is_new) {
        // insert
        $item_id = 0;
        if (!xnpInsertBasicInformation($item_id)) {
            return false;
        }
        $result = xnpUpdateIndex($item_id);
        if ($result) {
            $result = xnpUpdatePreview($item_id);
        }
        if (!$result) {
            xnpDeleteBasicInformation($_SESSION['XNPSID'], $item_id);
        }
        $detail_obj = &$detail_handler->create();
        $detail['onlinesimulation_id'] = $item_id;
    } else {
        // update
        if (!xnpUpdateBasicInformation($item_id)) {
            return false;
        }
        if (!xnpUpdateIndex($item_id)) {
            return false;
        }
        if (!xnpUpdatePreview($item_id)) {
            return false;
        }
        if (xnp_insert_change_log($_SESSION['XNPSID'], $item_id, $formdata->getValue('post', 'change_log', 's', false))) {
            return false;
        }
        $detail_obj = &$detail_handler->get($item_id);
    }
    foreach ($detail as $key => $val) {
        $detail_obj->set($key, $val);
    }
    if (!$detail_handler->insert($detail_obj)) {
        return false;
    }

    return true;
}

/**
 * update item.
 *
 * @param int $item_id updating item id
 *
 * @return bool false if failure
 */
function xnponlinesimulationUpdateItem($item_id)
{
    return xnponlinesimulationInsertItem($item_id);
}

/**
 * get modified fields.
 *
 * @param int $item_id item id
 *
 * @return array label of modified fields
 */
function xnponlinesimulationGetModifiedFields($item_id)
{
    $ret = array();
    $detail_old = xnponlinesimulationGetDetailInformation($item_id);
    $detail_new = _xnponlinesimulationFetchDetailInformation($detail_old);
    $fields = array(
        'vm_type' => _MD_XNPONLINESIMULATION_VM_TYPE,
        'download_url' => _MD_XNPONLINESIMULATION_DOWNLOAD_URL,
        'model_contents_url' => _MD_XNPONLINESIMULATION_MODEL_CONTENTS_URL,
        'model_site_name' => _MD_XNPONLINESIMULATION_MODEL_SITE_NAME,
        'simulator_name' => _MD_XNPONLINESIMULATION_SIMULATOR_NAME,
        'simulator_version' => _MD_XNPONLINESIMULATION_SIMULATOR_VERSION,
    );
    foreach ($fields as $key => $label) {
        if ($detail_old[$key] != $detail_new[$key]) {
            array_push($ret, $label);
        }
    }

    return $ret;
}

/**
 * get detail information quick search query.
 *
 * @param array  &$wheres  WHERE condition
 * @param string &$join    JOIN condition
 * @param array  $keywords search keywords
 *                         return bool false if faiure
 */
function xnponlinesimulationGetDetailInformationQuickSearchQuery(&$wheres, &$join, $keywords)
{
    $mydirname = basename(dirname(dirname(__FILE__)));
    global $xoopsDB;
    $detail_table = $xoopsDB->prefix($mydirname.'_item_detail');
    $keyword_fields = array(
        sprintf('%s.model_site_name', $detail_table),
        sprintf('%s.simulator_name', $detail_table),
        sprintf('%s.simulator_version', $detail_table),
    );
    $wheres = xnpGetKeywordsQueries($keyword_fields, $keywords);

    return true;
}

/**
 * get advanced search block.
 *
 * @param array &$search_vars array of search variables
 *
 * @return string html
 */
function xnponlinesimulationGetAdvancedSearchBlock(&$search_vars)
{
    $mydirname = basename(dirname(dirname(__FILE__)));
    $mod_path = XOOPS_ROOT_PATH.'/modules/'.$mydirname;
    $mod_url = XOOPS_URL.'/modules/'.$mydirname;
    // search variables
    $basic = xnpGetBasicInformationAdvancedSearchBlock($mydirname, $search_vars);
    $keys = array('vm_type', 'download_url', 'model_contents_url', 'model_site_name', 'simulator_name', 'simulator_version');
    foreach ($keys as $key) {
        $search_vars[] = sprintf('%s_%s', $mydirname, $key);
    }

    // assign to template
    global $xoopsTpl;
    $tpl = new XoopsTpl();
    $tpl->plugins_dir[] = $mod_path.'/class/smarty/plugins';
    $tpl->assign($xoopsTpl->get_template_vars());
    $tpl->assign('mydirname', $mydirname);
    $tpl->assign('mod_path', $mod_path);
    $tpl->assign('mod_url', $mod_url);
    $tpl->assign('xoonips_path', XOONIPS_PATH);
    $tpl->assign('xoonips_url', XOONIPS_URL);
    $tpl->assign('basic', $basic);
    $tpl->assign('module_name', $mydirname);
    $tpl->assign('module_display_name', xnpGetItemTypeDisplayNameByDirname($mydirname, 'n'));
    $tpl->assign('vm_types', _xnponlinesimulationGetVmTypes());

    return $tpl->fetch('db:'.$mydirname.'_search_block.html');
}

/**
 * get advanced search query.
 *
 * @param string &$where WHERE clause
 * @param string &$join  JOIN clause
 */
function xnponlinesimulationGetAdvancedSearchQuery(&$where, &$join)
{
    $mydirname = basename(dirname(dirname(__FILE__)));
    global $xoopsDB;
    $detail_table = $xoopsDB->prefix($mydirname.'_item_detail');
    $wheres = array();
    // basic
    $w = xnpGetBasicInformationAdvancedSearchQuery($mydirname);
    if ($w) {
        $wheres[] = $w;
    }
    // detail
    $keys = array('simulator_name', 'simulator_version', 'vm_type', 'download_url', 'model_contents_url', 'model_site_name');
    foreach ($keys as $key) {
        $field = sprintf('%s.%s', $detail_table, $key);
        $name = sprintf('%s_%s', $mydirname, $key);
        $w = xnpGetKeywordQuery($field, $name);
        if ($w) {
            $wheres[] = $w;
        }
    }
    // finished
    $where = implode(' AND ', $wheres);
    $join = '';
}

/**
 * export item.
 *
 * @param string   $path      export file path
 * @param resource $fp        file handle
 * @param int      $item_id   item id
 * @param bool     $with_file true if export item with attachment
 *
 * @return bool false if failure
 */
function xnponlinesimulationExportItem($path, $fp, $item_id, $with_file)
{
    $mydirname = basename(dirname(dirname(__FILE__)));
    $mod_path = XOOPS_ROOT_PATH.'/modules/'.$mydirname;
    $mod_url = XOOPS_URL.'/modules/'.$mydirname;
    if (!$fp) {
        return false;
    }
    // screenshot
    $screenshot_xml = '';
    if ($with_file) {
        $tfp = tmpfile();
        if (false === $tfp) {
            // failed to open temporary file
            return false;
        }
        if (!xnpExportFile($path, $tfp, $item_id)) {
            // failed to export attachment file
            fclose($tfp);

            return false;
        }
        fseek($tfp, 0, SEEK_SET);
        while (!feof($tfp)) {
            $screenshot_xml .= fread($tfp, 8192);
        }
        fclose($tfp);
    }
    $detail = xnponlinesimulationGetDetailInformation($item_id);

    // assign to template
    global $xoopsTpl;
    $tpl = new XoopsTpl();
    $tpl->plugins_dir[] = $mod_path.'/class/smarty/plugins';
    $tpl->assign($xoopsTpl->get_template_vars());
    $tpl->assign('item_id', $item_id);
    $tpl->assign('mydirname', $mydirname);
    $tpl->assign('mod_path', $mod_path);
    $tpl->assign('mod_url', $mod_url);
    $tpl->assign('xoonips_path', XOONIPS_PATH);
    $tpl->assign('xoonips_url', XOONIPS_URL);
    $tpl->assign('item_id', $item_id);
    $tpl->assign('detail', $detail);
    $tpl->assign('screenshot_xml', $screenshot_xml);
    $tpl->assign('vm_types', _xnponlinesimulationGetVmTypes());
    $xml = $tpl->fetch('db:'.$mydirname.'_export.xml');
    if (false === fwrite($fp, $xml)) {
        return false;
    }

    return true;
}

/**
 * get detail information total size.
 *
 * @param array $iids array of item ids
 *
 * @return float data size
 */
function xnponlinesimulationGetDetailInformationTotalSize($iids)
{
    return xnpGetTotalFileSize($iids);
}

/**
 * get metadata information for file download.
 *
 * @param int $item_id item id
 *
 * @return array metadata text array
 *               array(
 *               'name 1' => 'metadata value 1',
 *               'name 2' => 'metadata value 2',
 *               ...
 *               )
 */
function xnponlinesimulationGetMetaInformation($item_id)
{
    $ret = array();
    $basic = xnpGetBasicInformationArray($item_id);
    $detail = xnponlinesimulationGetDetailInformation($item_id);
    $ret[_MD_XOONIPS_ITEM_DOI_LABEL] = $basic['doi'];
    $ret[_MD_XOONIPS_ITEM_TITLE_LABEL] = implode("\n", $basic['titles']);
    $ret[_MD_XOONIPS_ITEM_KEYWORDS_LABEL] = implode("\n", $basic['keywords']);
    $ret[_MD_XOONIPS_ITEM_DESCRIPTION_LABEL] = $basic['description'];
    $ret[_MD_XOONIPS_ITEM_LAST_UPDATE_DATE_LABEL] = $basic['last_update_date'];
    $ret[_MD_XOONIPS_ITEM_CREATION_DATE_LABEL] = $basic['creation_date'];
    $ret[_MD_XOONIPS_ITEM_CONTRIBUTOR_LABEL] = $basic['contributor'];
    $ret[_MD_XNPONLINESIMULATION_VM_TYPE] = $detail['vm_type'];
    $ret[_MD_XNPONLINESIMULATION_DOWNLOAD_URL] = $detail['download_url'];
    $ret[_MD_XNPONLINESIMULATION_MODEL_CONTENTS_URL] = $detail['model_contents_url'];
    $ret[_MD_XNPONLINESIMULATION_MODEL_SITE_NAME] = $detail['model_site_name'];
    $ret[_MD_XNPONLINESIMULATION_SIMULATOR_NAME] = $detail['simulator_name'];
    $ret[_MD_XNPONLINESIMULATION_SIMULATOR_VERSION] = $detail['simulator_version'];

    return $ret;
}

/**
 * check OAI-PMH metadata scheme supportment.
 *
 * @param string $prefix  metadata scheme
 * @param int    $item_id item id
 *
 * @return bool false if not supported
 */
function xnponlinesimulationSupportMetadataFormat($prefix, $item_id)
{
    static $schemes = array('oai_dc', 'junii2');

    return in_array($prefix, $schemes);
}

/**
 * get OAI-PMH metadata entry.
 *
 * @param string $prefix  metadata scheme
 * @param int    $item_id item id
 *
 * @return text metadata
 */
function xnponlinesimulationGetMetadata($prefix, $item_id)
{
    $mydirpath = dirname(dirname(__FILE__));
    $mydirname = basename($mydirpath);
    if (!in_array($prefix, array('oai_dc', 'junii2'))) {
        return false;
    }

    // detail information
    $detail_handler = &xoonips_getormhandler($mydirname, 'item_detail');
    $detail_obj = &$detail_handler->get($item_id);
    if (empty($detail_obj)) {
        return false;
    }
    $detail = $detail_obj->getArray();
    // basic information
    $basic = xnpGetBasicInformationArray($item_id);
    $basic['publication_date_iso8601'] = xnpISO8601($basic['publication_year'], $basic['publication_month'], $basic['publication_mday']);
    // indexes
    $indexes = array();
    if (RES_OK == xnp_get_index_id_by_item_id($_SESSION['XNPSID'], $item_id, $xids)) {
        foreach ($xids as $xid) {
            if (RES_OK == xnp_get_index($_SESSION['XNPSID'], $xid, $index)) {
                $indexes[] = xnpGetIndexPathServerString($_SESSION['XNPSID'], $xid);
            }
        }
    }
    // previews
    $file_handler = &xoonips_gethandler('xoonips', 'file');
    $previews = $file_handler->getFilesInfo($item_id, 'preview');
    // related to
    $related_to_handler = &xoonips_getormhandler('xoonips', 'related_to');
    $related_to_ids = $related_to_handler->getChildItemIds($item_id);
    $related_tos = array();
    foreach ($related_to_ids as $related_to_id) {
        $related_tos[] = array(
        'item_id' => $related_to_id,
        'item_url' => XOOPS_URL.'/modules/xoonips/detail.php?item_id='.$related_to_id,
        );
    }
    // repository configs
    $xconfig_handler = &xoonips_getormhandler('xoonips', 'config');
    $myxoopsConfigMetaFooter = &xoonips_get_xoops_configs(XOOPS_CONF_METAFOOTER);
    $repository = array(
        'download_file_compression' => $xconfig_handler->getValue('download_file_compression'),
        'nijc_code' => $xconfig_handler->getValue('repository_nijc_code'),
        'publisher' => $xconfig_handler->getValue('repository_publisher'),
        'institution' => $xconfig_handler->getValue('repository_institution'),
        'meta_author' => $myxoopsConfigMetaFooter['meta_author'],
    );
    // assign template
    global $xoopsTpl;
    $tpl = new XoopsTpl();
    $tpl->plugins_dir[] = XOONIPS_PATH.'/class/smarty/plugins';
    $tpl->assign($xoopsTpl->get_template_vars());
    $tpl->assign('basic', $basic);
    $tpl->assign('detail', $detail);
    $tpl->assign('indexes', $indexes);
    $tpl->assign('previews', $previews);
    $tpl->assign('related_tos', $related_tos);
    $tpl->assign('repository', $repository);
    $tpl->assign('vm_types', _xnponlinesimulationGetVmTypes());
    $xml = $tpl->fetch('db:'.$mydirname.'_oaipmh_'.$prefix.'.xml');

    return $xml;
}

/**
 * Private Functions.
 */

/**
 * get item detail url.
 *
 * @param int    $item_id item id
 * @param string $doi     item DOI
 *
 * @return string url
 */
function _xnponlinesimulationGetItemUrl($item_id, $doi)
{
    $key = 'item_id';
    $value = $item_id;
    if (!empty($doi)) {
        $key = XNP_CONFIG_DOI_FIELD_PARAM_NAME;
        $value = $doi;
    }

    return sprintf('%s/detail.php?%s=%s', XOONIPS_URL, $key, $value);
}

/**
 * get screenshot information.
 *
 * @param int $item_id item id
 *
 * @return array screenshot information, false if not available
 *               array(
 *               'file_id' => int
 *               'caption' => string
 *               'url' => string
 *               'thumbnail_url' => string
 *               );
 */
function _xnponlinesimulationGetScreenshotInformation($item_id)
{
    $xf_handler = &xoonips_getormhandler('xoonips', 'file');
    $criteria = new CriteriaCompo(new Criteria('item_id', $item_id));
    $criteria->add(new Criteria('is_deleted', '0'));
    $criteria->add(new Criteria('ISNULL(`sess_id`)', 1));
    $criteria->add(new Criteria('name', 'preview', '=', 'ft'));
    $criteria->setSort('timestamp', 'DESC');
    $join = new XooNIpsJoinCriteria('xoonips_file_type', 'file_type_id', 'file_type_id', 'INNER', 'ft');
    $xf_objs = &$xf_handler->getObjects($criteria, false, 'file_id, caption', false, $join);
    if (0 == count($xf_objs)) {
        return false;
    }
    // return newest preview data
    $file_id = $xf_objs[0]->get('file_id');
    $caption = $xf_objs[0]->get('caption');
    $url = sprintf('%s/image.php?file_id=%u', XOONIPS_URL, $file_id);
    $thumbnail_url = $url.'&thumbnail=1';

    return array(
        'file_id' => $file_id,
        'caption' => $caption,
        'url' => $url,
        'thumbnail_url' => $thumbnail_url,
    );
}

/**
 * fetch detail information form request.
 *
 * @param array $detail default detail information
 *
 * @return array fetched detail information
 */
function _xnponlinesimulationFetchDetailInformation($detail)
{
    $formdata = &xoonips_getutility('formdata');
    $ret = $detail;
    foreach (array_keys($ret) as $key) {
        $value = $formdata->getValue('post', $key, 's', false);
        if (isset($value)) {
            $ret[$key] = $value;
        }
    }

    return $ret;
}

/**
 * get vm types.
 *
 * @return array vm types
 */
function _xnponlinesimulationGetVmTypes()
{
    static $vmTypes = array(
        'centos5' => 'centos5',
        'neurodebian6' => 'neurodebian6',
        'win2008r2' => 'win2008r2',
    );

    return $vmTypes;
}
