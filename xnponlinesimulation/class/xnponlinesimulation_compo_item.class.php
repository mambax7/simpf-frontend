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

require_once XOOPS_ROOT_PATH.'/modules/xoonips/class/xoonips_compo_item.class.php';
require_once XOOPS_ROOT_PATH.'/modules/xnponlinesimulation/iteminfo.php';

/**
 * Handler object that create,insert,update,get,delete
 * XNPOnlinesimulationCompo object.
 */
class XNPOnlinesimulationCompoHandler extends XooNIpsItemInfoCompoHandler
{
    /**
     * constructor.
     *
     * @param object &$db xoops db instance
     */
    public function __construct(&$db)
    {
        parent::__construct($db, 'xnponlinesimulation');
    }

    /**
     * create compo object.
     *
     * @return &object created compo object
     */
    public function &create()
    {
        $onlinesimulation = new XNPOnlinesimulationCompo();

        return $onlinesimulation;
    }

    /**
     * return template filename.
     *
     * @param string $type defined symbol
     *                     - XOONIPS_TEMPLATE_TYPE_TRANSFER_ITEM_DETAIL
     *                     - XOONIPS_TEMPLATE_TYPE_TRANSFER_ITEM_LISTL
     *                     - XOONIPS_TEMPLATE_TYPE_ITEM_DETAIL
     *                     - XOONIPS_TEMPLATE_TYPE_ITEM_LIST
     *
     * @return template filename
     */
    public function getTemplateFileName($type)
    {
        switch ($type) {
        case XOONIPS_TEMPLATE_TYPE_TRANSFER_ITEM_DETAIL:
        case XOONIPS_TEMPLATE_TYPE_TRANSFER_ITEM_LIST:
        case XOONIPS_TEMPLATE_TYPE_ITEM_DETAIL:
        case XOONIPS_TEMPLATE_TYPE_ITEM_LIST:
            return 'xnponlinesimulation_embed_block.html';
        default:
            return '';
        }
    }

    /**
     * return template variables of item.
     *
     * @param string $type    defined symbol
     *                        - XOONIPS_TEMPLATE_TYPE_TRANSFER_ITEM_DETAIL
     *                        - XOONIPS_TEMPLATE_TYPE_TRANSFER_ITEM_LIST
     *                        - XOONIPS_TEMPLATE_TYPE_ITEM_DETAIL
     *                        - XOONIPS_TEMPLATE_TYPE_ITEM_LIST
     * @param int    $item_id item id
     * @param int    $uid     user id who get item
     *
     * @return array of template variables
     */
    public function getTemplateVar($type, $item_id, $uid)
    {
        include_once XOONIPS_PATH.'/include/AL.php';
        include_once dirname(dirname(__FILE__)).'/include/view.php';
        $result = array();
        switch ($type) {
        case XOONIPS_TEMPLATE_TYPE_TRANSFER_ITEM_DETAIL:
            $result['html'] = xnponlinesimulationGetPrinterFriendlyDetailBlock($item_id);
            break;
        case XOONIPS_TEMPLATE_TYPE_TRANSFER_ITEM_LIST:
            $basic = array();
            if (RES_OK == xnp_get_item($_SESSION['XNPSID'], $item_id, $basic)) {
                $result['html'] = xnponlinesimulationGetPrinterFriendlyListBlock($basic);
            }
            break;
        case XOONIPS_TEMPLATE_TYPE_ITEM_DETAIL:
            $result['html'] = xnponlinesimulationGetDetailBlock($item_id);
            break;
        case XOONIPS_TEMPLATE_TYPE_ITEM_LIST:
            $basic = array();
            if (RES_OK == xnp_get_item($_SESSION['XNPSID'], $item_id, $basic)) {
                $result['html'] = xnponlinesimulationGetListBlock($basic);
            }
            break;
        }

        return $result;
    }
}

/**
 * Data object that have one ore more XooNIpsTableObject for
 * Online Simulation type.
 */
class XNPOnlinesimulationCompo extends XooNIpsItemInfoCompo
{
    public function __construct()
    {
        parent::__construct('xnponlinesimulation');
    }
}
