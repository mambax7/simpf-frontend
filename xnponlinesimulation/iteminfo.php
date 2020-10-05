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

require XOOPS_ROOT_PATH.'/modules/xoonips/include/iteminfo.inc.php';

// general information
$iteminfo['description'] = 'XooNIps Online Simulation Item Type';
$iteminfo['files']['main'] = null;
$iteminfo['files']['preview'] = 'preview';
$iteminfo['files']['others'] = [];

// define compo
$iteminfo['ormcompo']['module'] = 'xnponlinesimulation';
$iteminfo['ormcompo']['name'] = 'item';
$iteminfo['ormcompo']['primary_orm'] = 'basic';
$iteminfo['ormcompo']['primary_key'] = 'item_id';

// define orm of compo
$iteminfo['orm'][] = [
    'module' => 'xnponlinesimulation',
    'name' => 'item_detail',
    'field' => 'detail',
    'foreign_key' => 'onlinesimulation_id',
    'multiple' => false,
];
$iteminfo['orm'][] = [
    'module' => 'xoonips',
    'name' => 'file',
    'field' => 'preview',
    'foreign_key' => 'item_id',
    'criteria' => iteminfo_file_criteria('preview'),
    'multiple' => true,
];

// define database table information
$iteminfo['ormfield']['detail'] = [
    ['name' => 'onlinesimulation_id', 'type' => 'int', 'required' => false],
    ['name' => 'vm_type', 'type' => 'string', 'required' => true],
    ['name' => 'download_url', 'type' => 'string', 'required' => true],
    ['name' => 'contents_count', 'type' => 'int', 'required' => false],
    ['name' => 'model_contents_url', 'type' => 'string', 'required' => true],
    ['name' => 'model_contents_count', 'type' => 'int', 'required' => false],
    ['name' => 'model_site_name', 'type' => 'string', 'required' => true],
    ['name' => 'simulator_name', 'type' => 'string', 'required' => false],
    ['name' => 'simulator_version', 'type' => 'string', 'required' => false],
];

// detail information (modify below for each item types)
$iteminfo['io']['xmlrpc']['item'][] = [
    'orm' => [
        'field' => [['orm' => 'detail', 'field' => 'onlinesimulation_id']],
    ],
    'xmlrpc' => [
        'field' => ['detail_field', 'onlinesimulation_id'],
        'display_name' => '_MD_XNPONLINESIMULATION_ONLINESIMULATION_ID',
        'type' => 'string',
        'multiple' => false,
        'readonly' => true,
    ],
];
$iteminfo['io']['xmlrpc']['item'][] = [
    'orm' => [
        'field' => [['orm' => 'detail', 'field' => 'vm_type']],
    ],
    'xmlrpc' => [
        'field' => ['detail_field', 'vm_type'],
        'display_name' => '_MD_XNPONLINESIMULATION_VM_TYPE',
        'type' => 'string',
        'multiple' => false,
        'required' => true,
    ],
];
$iteminfo['io']['xmlrpc']['item'][] = [
    'orm' => [
        'field' => [['orm' => 'detail', 'field' => 'download_url']],
    ],
    'xmlrpc' => [
        'field' => ['detail_field', 'download_url'],
        'display_name' => '_MD_XNPONLINESIMULATION_DOWNLOAD_URL',
        'type' => 'string',
        'multiple' => false,
        'required' => true,
    ],
];
$iteminfo['io']['xmlrpc']['item'][] = [
    'orm' => [
        'field' => [['orm' => 'detail', 'field' => 'contents_count']],
    ],
    'xmlrpc' => [
        'field' => ['detail_field', 'contents_count'],
        'display_name' => '_MD_XNPONLINESIMULATION_CONTENTS_HITS',
        'type' => 'int',
        'multiple' => false,
        'required' => false,
    ],
];
$iteminfo['io']['xmlrpc']['item'][] = [
    'orm' => [
        'field' => [['orm' => 'detail', 'field' => 'model_contents_url']],
    ],
    'xmlrpc' => [
        'field' => ['detail_field', 'model_contents_url'],
        'display_name' => '_MD_XNPONLINESIMULATION_MODEL_CONTENTS_URL',
        'type' => 'string',
        'multiple' => false,
        'required' => true,
    ],
];
$iteminfo['io']['xmlrpc']['item'][] = [
    'orm' => [
        'field' => [['orm' => 'detail', 'field' => 'model_contents_count']],
    ],
    'xmlrpc' => [
        'field' => ['detail_field', 'model_contents_count'],
        'display_name' => '_MD_XNPONLINESIMULATION_RUNS',
        'type' => 'int',
        'multiple' => false,
        'required' => false,
    ],
];
$iteminfo['io']['xmlrpc']['item'][] = [
    'orm' => [
        'field' => [['orm' => 'detail', 'field' => 'model_site_name']],
    ],
    'xmlrpc' => [
        'field' => ['detail_field', 'model_site_name'],
        'display_name' => '_MD_XNPONLINESIMULATION_MODEL_SITE_NAME',
        'type' => 'string',
        'multiple' => false,
        'required' => true,
    ],
];
$iteminfo['io']['xmlrpc']['item'][] = [
    'orm' => [
        'field' => [['orm' => 'detail', 'field' => 'simulator_name']],
    ],
    'xmlrpc' => [
        'field' => ['detail_field', 'simulator_name'],
        'display_name' => '_MD_XNPONLINESIMULATION_SIMULATOR_NAME',
        'type' => 'string',
        'multiple' => false,
        'required' => false,
    ],
];
$iteminfo['io']['xmlrpc']['item'][] = [
    'orm' => [
        'field' => [['orm' => 'detail', 'field' => 'simulator_version']],
    ],
    'xmlrpc' => [
        'field' => ['detail_field', 'simulator_version'],
        'display_name' => '_MD_XNPONLINESIMULATION_SIMULATOR_VERSION',
        'type' => 'string',
        'multiple' => false,
        'required' => false,
    ],
];
$iteminfo['io']['xmlrpc']['item'][] = [
    'orm' => [
        'field' => [['orm' => 'preview', 'field' => 'file_id']],
    ],
    'xmlrpc' => [
        'field' => ['detail_field', 'preview'],
        'display_name' => '_MD_XNPONLINESIMULATION_SCREENSHOT',
        'type' => 'int',
        'multiple' => true,
        'required' => false,
    ],
];

//-------------------------
// SimpleItem
//-------------------------
$iteminfo['io']['xmlrpc']['simpleitem'][] = [
    'orm' => [
        'field' => [['orm' => 'basic', 'field' => 'item_id']],
    ],
    'xmlrpc' => [
        'field'    => ['item_id'],
        'type'     => 'int',
        'multiple' => false,
    ],
];
$iteminfo['io']['xmlrpc']['simpleitem'][] = [
    'orm' => [
        'field' => [['orm' => 'basic', 'field' => 'item_type_id']],
    ],
    'xmlrpc' => [
        'field' => ['itemtypeid'],
        'type' => 'int',
        'multiple' => false,
    ],
];
$iteminfo['io']['xmlrpc']['simpleitem'][] = [
    'orm' => [
        'field' => [['orm' => 'basic', 'field' => 'uid']],
    ],
    'xmlrpc' => [
        'field' => ['username'],
        'type' => 'string',
        'multiple' => false,
    ],
    'eval' => [
        'orm2xmlrpc' => '$u_handler =& xoops_gethandler("user");'.
            '$user =& $u_handler->get($in_var[0]);'.
            '$out_var[0] = $user->getVar("uname");',
        'xmlrpc2orm' => ';',
    ],
];
$iteminfo['io']['xmlrpc']['simpleitem'][] = [
    'orm' => [
        'field' => [['orm' => 'titles', 'field' => 'title']],
    ],
    'xmlrpc' => [
        'field' => ['titles'],
        'type' => 'string',
        'multiple' => true,
    ],
];
$iteminfo['io']['xmlrpc']['simpleitem'][] = [
    'orm' => [
        'field' => [['orm' => 'basic', 'field' => 'last_update_date']],
    ],
    'xmlrpc' => [
        'field' => ['last_modified_date'],
        'type' => 'dateTime.iso8601',
        'multiple' => false,
    ],
];
$iteminfo['io']['xmlrpc']['simpleitem'][] = [
    'orm' => [
        'field' => [['orm' => 'basic', 'field' => 'creation_date']],
    ],
    'xmlrpc' => [
        'field' => ['registration_date'],
        'type' => 'dateTime.iso8601',
        'multiple' => false,
    ],
];
$iteminfo['io']['xmlrpc']['simpleitem'][] = [
    'orm' => [
        'field' => [
            ['orm' => 'titles', 'field' => 'title'],
            ['orm' => 'detail', 'field' => 'vm_type'],
            ['orm' => 'detail', 'field' => 'download_url'],
            ['orm' => 'detail', 'field' => 'contents_count'],
            ['orm' => 'detail', 'field' => 'model_contents_url'],
            ['orm' => 'detail', 'field' => 'model_contents_url_count'],
            ['orm' => 'detail', 'field' => 'model_site_name'],
            ['orm' => 'detail', 'field' => 'simulator_name'],
            ['orm' => 'detail', 'field' => 'simulator_version'],
        ],
    ],
    'xmlrpc' => [
        'field' => ['text'],
        'type' => 'string',
    ],
    'eval' => [
        'orm2xmlrpc' => '$in_var[0] = implode(";", $in_var[0]);'.
            '$out_var[0] = implode("/", $in_var);',
        'xmlrpc2orm' => ';',
    ],
];
