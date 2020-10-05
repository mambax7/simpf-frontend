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

//  Install script for XooNIps onlinesimulation item type module
function xoops_module_install_xnponlinesimulation($xoopsMod)
{
    global $xoopsDB;

    $mydirname = basename(dirname(__DIR__));
    $mid = (int)$xoopsMod->getVar('mid', 'n');

    $required_version = 3.49;

    $item_type_info = [
        'name' => $mydirname,
        'display_name' => 'Online Simulation',
        'viewphp' => $mydirname.'/include/view.php',
    ];

    // for Cube 2.1
    global $ret;
    if (defined('XOOPS_CUBE_LEGACY')) {
        $root = &XCube_Root::getSingleton();
        $root->mDelegateManager->add('Legacy.Admin.Event.ModuleInstall.'.ucfirst($mydirname).'.Success', $mydirname.'_message_append_oninstall');
        $root->mDelegateManager->add('Legacy.Admin.Event.ModuleInstall.'.ucfirst($mydirname).'.Fail', $mydirname.'_message_append_oninstall');
    }
    if (!isset($ret) || !is_array($ret)) {
        $ret = [];
    }

    // greetings
    $ret[] = 'Run install script';

    // check xoonips module
    $module_handler = xoops_getHandler('module');
    $module_obj = $module_handler->getByDirname('xoonips');
    if (!is_object($module_obj)) {
        $ret[] = '+ Error: XooNIps is not installed';

        return false;
    }
    $version = (int)$module_obj->getVar('version', 'n');
    if ($version < $required_version * 100) {
        $ret[] = '+ Error: XooNIps '.$required_version.' or higher required';

        return false;
    }

    // register item type
    $table = $xoopsDB->prefix('xoonips_item_type');
    $ret[] = '+ Register XooNIps item type : '.$item_type_info['display_name'];
    $sql = sprintf('INSERT INTO %s (name, display_name, mid, viewphp) VALUES (%s, %s, %u, %s)', $table, $xoopsDB->quoteString($item_type_info['name']), $xoopsDB->quoteString($item_type_info['display_name']), $mid, $xoopsDB->quoteString($item_type_info['viewphp']));
    if (false === $xoopsDB->query($sql)) {
        $ret[] = '+ Error: Failed to insert xoonips item type';

        return false;
    }

    // delete 'module access rights' from all groups
    $ret[] = '+ Delete module access rights from all groups';
    $member_handler = xoops_getHandler('member');
    $gperm_handler = xoops_getHandler('groupperm');
    $groups = $member_handler->getGroupList();
    foreach ($groups as $groupid => $groupname) {
        if ($gperm_handler->checkRight('module_read', $mid, $groupid)) {
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_groupid', $groupid));
            $criteria->add(new Criteria('gperm_itemid', $mid));
            $criteria->add(new Criteria('gperm_name', 'module_read'));
            $objects = $gperm_handler->getObjects($criteria);
            if (1 == count($objects)) {
                $gperm_handler->delete($objects[0]);
            }
        }
    }

    // success
    return true;
}

function xnponlinesimulation_message_append_oninstall(&$xoopsMod, $log)
{
    if (is_array(@$GLOBALS['ret'])) {
        foreach ($GLOBALS['ret'] as $message) {
            $log->add(strip_tags($message));
        }
    }
}
