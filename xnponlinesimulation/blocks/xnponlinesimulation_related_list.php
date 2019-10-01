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

class Xnponlinesimulation_xnponlinesimulationRelated extends Legacy_BlockProcedure
{
    public $item_id;

    /**
     * constructor.
     */
    public function __construct(&$block)
    {
        parent::__construct($block);
        $this->_loadXooNIps();
        $this->item_id = $this->_getItemId();
    }

    /**
     * execute procedure.
     */
    public function execute()
    {
        $render = &$this->getRenderTarget();
        $render->setTemplateName($this->_mBlock->get('template'));
        $render->setAttribute('mid', $this->_mBlock->get('mid'));
        $render->setAttribute('bid', $this->_mBlock->get('bid'));
        $render->setAttribute('items', $this->_getRelatedItems());

        $root = &XCube_Root::getSingleton();
        $renderSystem = &$root->getRenderSystem($this->getRenderSystemName());
        $renderSystem->renderBlock($render);
    }

    /**
     * return show/hide condition.
     *
     * @return bool false if hide
     */
    public function isDisplay()
    {
        return false !== $this->item_id;
    }

    /**
     * load XooNIps.
     */
    private function _loadXooNIps()
    {
        $mydirname = basename(dirname(dirname(__FILE__)));
        global $xoopsUser;
        include_once XOOPS_ROOT_PATH.'/modules/xoonips/condefs.php';
        include_once XOOPS_ROOT_PATH.'/modules/xoonips/include/functions.php';
        $langman = &xoonips_getutility('languagemanager');
        $langman->read('main.php', $mydirname);
        $langman->read('block.php', $mydirname);
    }

    /**
     * get item id from form request.
     *
     * @return int item id
     */
    private function _getItemId()
    {
        // check detail.php
        if (false === strpos($_SERVER['REQUEST_URI'], '/modules/xoonips/detail.php')) {
            return false;
        }
        // get by doi
        $doi = xoops_getrequest(XNP_CONFIG_DOI_FIELD_PARAM_NAME);
        if (isset($doi)) {
            return $this->_searchItemId($doi, true);
        }
        // get by item id
        $item_id = xoops_getrequest('item_id');
        if (isset($item_id)) {
            return $this->_searchItemId(intval($item_id), false);
        }

        return false;
    }

    /**
     * search existing item id.
     *
     * @param mixed(int/string) $id     item id or doi
     * @param bool              $is_doi true if $id is doi
     *
     * @return int existing item id
     */
    private function _searchItemId($id, $is_doi)
    {
        $handler = &xoonips_getormhandler('xoonips', 'item_basic');
        $field = $is_doi ? 'doi' : 'item_id';
        $criteria = new Criteria($field, $id);
        $objs = &$handler->getObjects($criteria);
        if (1 != count($objs)) {
            return false;
        }

        return $objs[0]->get('item_id');
    }

    /**
     * get related to htmls
     * return array htmls.
     */
    private function _getRelatedItems()
    {
        if (false === $this->item_id) {
            return array();
        }
        include_once XOONIPS_PATH.'/include/lib.php';
        $basic_info = xnpGetBasicInformationArray($this->item_id);
        $related_to_ids = $basic_info['related_to'];
        $ret = itemid2ListBlock($related_to_ids);

        return $ret;
    }
}
