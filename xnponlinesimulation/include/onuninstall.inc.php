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

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

//  Uninstall script for XooNIps onlinesimulation item type module
function xoops_module_uninstall_xnponlinesimulation($xoopsMod)
{
    global $xoopsDB;

    $mydirname = basename(dirname(dirname(__FILE__)));
    $mid = intval($xoopsMod->getVar('mid', 'n'));

    // for Cube 2.1
    global $ret;
    if (defined('XOOPS_CUBE_LEGACY')) {
        $root = &XCube_Root::getSingleton();
        $root->mDelegateManager->add('Legacy.Admin.Event.ModuleUninstall.'.ucfirst($mydirname).'.Success', $mydirname.'_message_append_onuninstall');
        $root->mDelegateManager->add('Legacy.Admin.Event.ModuleUninstall.'.ucfirst($mydirname).'.Fail', $mydirname.'_message_append_onuninstall');
    }
    if (!isset($ret) || !is_array($ret)) {
        $ret = array();
    }

    // greeting
    $ret[] = 'Run uninstall script';

    // check xoonips module
    $module_handler = xoops_getHandler('module');
    $module_obj     = $module_handler->getByDirname('xoonips');
    if (!is_object($module_obj)) {
        $ret[] = '+ Warning: XooNIps not found';

        return true;
    }

    $it_table = $xoopsDB->prefix('xoonips_item_type');
    $ib_table = $xoopsDB->prefix('xoonips_item_basic');
    $ft_table = $xoopsDB->prefix('xoonips_file_type');

    // get item type id
    $sql = sprintf('SELECT item_type_id,display_name FROM %s WHERE mid=%u', $it_table, $mid);
    $res = $xoopsDB->query($sql);
    if (false === $res) {
        $ret[] = '+ Error: Failed to get item type id';

        return false;
    }
    $item_type_id = 0;
    while ($row = $xoopsDB->fetchRow($res)) {
        list($item_type_id, $item_type_name) = $row;
    }
    $xoopsDB->freeRecordSet($res);
    if (0 == $item_type_id) {
        $ret[] = '+ Warning: No item type found';

        return true;
    }

    // unregister XooNIps item type
    $ret[] = '+ Unregister XooNIps item type : '.$item_type_name;
    $table_maps = array(
        'xoonips_changelog' => array('item_id'),
        'xoonips_index_item_link' => array('item_id'),
        'xoonips_item_keyword' => array('item_id'),
        'xoonips_item_lock' => array('item_id'),
        'xoonips_item_show' => array('item_id'),
        'xoonips_item_title' => array('item_id'),
        'xoonips_related_to' => array('parent_id', 'item_id'),
        'xoonips_transfer_request' => array('item_id'),
    );
    foreach ($table_maps as $table_name => $keys) {
        $table = $xoopsDB->prefix($table_name);
        foreach ($keys as $key) {
            $sql = sprintf('DELETE tbl FROM %s AS tbl INNER JOIN %s AS ib ON tbl.%s=ib.item_id WHERE ib.item_type_id=%u', $table, $ib_table, $key, $item_type_id);
            if (false === $xoopsDB->query($sql)) {
                $ret[] = '+ Error: Failed to delete item information from '.$table_name;
            }
        }
    }

    // item_status - set item deleted flag for OAI-PMH repositories
    $table = $xoopsDB->prefix('xoonips_item_status');
    $sql = sprintf('UPDATE %s AS tbl INNER JOIN %s AS ib ON tbl.item_id=ib.item_id SET deleted_timestamp=UNIX_TIMESTAMP(NOW()), is_deleted=1 WHERE ib.item_type_id=%u', $table, $ib_table, $item_type_id);
    if (false === $xoopsDB->query($sql)) {
        $ret[] = '+ Error: Failed to update item information of xoonips_item_status';
    }

    // get file upload path
    $table = $xoopsDB->prefix('xoonips_config');
    $sql = sprintf('SELECT value FROM %s WHERE name=\'upload_dir\'', $table);
    $res = $xoopsDB->query($sql);
    if (false === $res) {
        $ret[] = '+ Error: Failed to get file upload directory';

        return false;
    }
    $file_upload_dir = '';
    while ($row = $xoopsDB->fetchRow($res)) {
        $file_upload_dir = $row[0];
    }
    $xoopsDB->freeRecordSet($res);

    // file body
    $table = $xoopsDB->prefix('xoonips_file');
    $sql = sprintf('SELECT file_id FROM %s AS tbl INNER JOIN %s AS ib ON tbl.item_id=ib.item_id WHERE ib.item_type_id=%u', $table, $ib_table, $item_type_id);
    $res = $xoopsDB->query($sql);
    if (false === $res) {
        $ret[] = '+ Error: Failed to get file information from xoonips_file';
    } else {
        while ($row = $xoopsDB->fetchRow($res)) {
            $file_id = $row[0];
            $fname = $file_upload_dir.'/'.$file_id;
            if ('' == $file_upload_dir || !file_exists($fname) || false === @unlink($fname)) {
                $ret[] = '+ Error: Failed to unlink file : '.$file_id;
            }
        }
        $xoopsDB->freeRecordSet($res);
    }

    // xoonips_file
    $sql = sprintf('DELETE tbl FROM %s AS tbl INNER JOIN %s AS ib ON tbl.item_id=ib.item_id WHERE ib.item_type_id=%u', $table, $ib_table, $item_type_id);
    if (false === $xoopsDB->query($sql)) {
        $ret[] = '+ Error: Failed to delete item information from '.$table_name;
    }

    // xoonips_item_basic
    $sql = sprintf('DELETE FROM %s WHERE item_type_id=%u', $ib_table, $item_type_id);
    if (false === $xoopsDB->query($sql)) {
        $ret[] = '+ Error: Failed to delete item information from xoonips_item_basic';
    }

    // xoonips_item_type
    $sql = sprintf('DELETE FROM %s WHERE item_type_id=%u', $it_table, $item_type_id);
    if (false === $xoopsDB->query($sql)) {
        $ret[] = '+ Error: Failed to delete item type information from xoonips_item_type';
    }

    // success
    return true;
}

function xnponlinesimulation_message_append_onuninstall(&$module_obj, &$log)
{
    if (is_array(@$GLOBALS['ret'])) {
        foreach ($GLOBALS['ret'] as $message) {
            $log->add(strip_tags($message));
        }
    }
}
