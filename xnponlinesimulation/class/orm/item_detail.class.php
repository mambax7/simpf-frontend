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

/**
 * Data object of Online Simulation detail information.
 */
class XNPOnlinesimulationOrmItemDetail extends XooNIpsTableObject
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('onlinesimulation_id', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('simulator_name', XOBJ_DTYPE_TXTBOX, '', false, 255);
        $this->initVar('simulator_version', XOBJ_DTYPE_TXTBOX, '', false, 255);
        $this->initVar('model_contents_url', XOBJ_DTYPE_TXTBOX, '', true, 255);
        $this->initVar('model_site_name', XOBJ_DTYPE_TXTBOX, '', true, 255);
        $this->initVar('vm_type', XOBJ_DTYPE_TXTBOX, '', true, 32);
        $this->initVar('download_url', XOBJ_DTYPE_TXTBOX, '', true, 255);
        $this->initVar('contents_count', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('model_contents_count', XOBJ_DTYPE_INT, 0, true);
    }
}

/**
 * Handler class that create, insert, update, get and delete detail information.
 */
class XNPOnlinesimulationOrmItemDetailHandler extends XooNIpsTableObjectHandler
{
    /**
     * constructor.
     *
     * @param object &$db xoops db instance
     */
    public function __construct(&$db)
    {
        parent::__construct($db);
        $this->__initHandler('XNPOnlinesimulationOrmItemDetail', 'xnponlinesimulation_item_detail', 'onlinesimulation_id', false);
    }
}
