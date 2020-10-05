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

//  Update script for XooNIps Online Simulation item type module
function xoops_module_update_xnponlinesimulation($xoopsMod, $oldversion)
{
    global $xoopsDB;
    $mydirname = basename(dirname(__DIR__));
    $mid = intval($xoopsMod->getVar('mid', 'n'));

    $version = intval($xoopsMod->getVar('version', 'n'));

    // for Cube 2.1
    global $ret;
    if (defined('XOOPS_CUBE_LEGACY')) {
        $root = &XCube_Root::getSingleton();
        $root->mDelegateManager->add('Legacy.Admin.Event.ModuleUpdate.'.ucfirst($mydirname).'.Success', $mydirname.'_message_append_onupdate');
        $root->mDelegateManager->add('Legacy.Admin.Event.ModuleUpdate.'.ucfirst($mydirname).'.Fail', $mydirname.'_message_append_onupdate');
    }
    if (!isset($ret) || !is_array($ret)) {
        $ret = [];
    }

    // greetings
    $ret[] = 'Run update script';

    $detail = $mydirname.'_item_detail';
    $table = $xoopsDB->prefix($detail);

    switch ($oldversion) {
    case 349:
        // ALTER TABLE `xnponlinesimulation_item_detail`
        // ADD COLUMN `vm_type` VARCHAR(32) NOT NULL AFTER `model_site_name`,
        // ADD COLUMN `download_url` VARCHAR(255) NOT NULL AFTER `vm_type`;
    case 345:
    default:
    }

    return true;
}

function xnponlinesimulation_message_append_onupdate(&$xoopsMod, &$log)
{
    if (is_array(@$GLOBALS['ret'])) {
        foreach ($GLOBALS['ret'] as $message) {
            $log->add(strip_tags($message));
        }
    }
}
