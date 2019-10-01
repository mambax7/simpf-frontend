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

/**
 * get suggest list.
 *
 * XOONIPS_URL/backend.php?itemtype=xnponlinesimulation&action=suggest&\
 *   t=model_site_name&q=[keyword]
 *   t=simulator_name&q=[keyword]
 *   t=simulator_version&q=[keyword]
 */
if (!defined('XOONIPS_PATH')) {
    exit();
}

$types = array('model_site_name', 'simulator_name', 'simulator_version');
$minCount = 1; // minimum number of counts

$q = xoops_getrequest('q');
$t = xoops_getrequest('t');
if (!in_array($t, $types)) {
    exit();
}

// check login user
$root = &XCube_Root::getSingleton();
$user = &$root->mContext->mXoopsUser;
if (!is_object($user)) {
    exit();
}

$detail_handler = &xoonips_getormhandler('xnponlinesimulation', 'item_detail');

$join = new XooNIpsJoinCriteria('xoonips_index_item_link', 'onlinesimulation_id', 'item_id', 'INNER', 'iil');
$join->cascade(new XooNIpsJoinCriteria('xoonips_index', 'index_id', 'index_id', 'INNER', 'idx'), 'iil', true);
$criteria = new CriteriaCompo();
$criteria->add(new Criteria($t, '%'.$q.'%', 'LIKE'));
$criteria->add(new Criteria('open_level', OL_PUBLIC, '=', 'idx'));
$criteria->add(new Criteria('certify_state', CERTIFIED, '=', 'iil'));
$criteria->setGroupBy($t.' HAVING `cnt` >= '.$minCount);
$criteria->setLimit(10);
$criteria->setSort('cnt', 'DESC');
$field = sprintf('%s, COUNT(1) AS `cnt`', $t);
$distinct = false;

$ret = array();
$res = &$detail_handler->open($criteria, $field, $distinct, $join);
while ($obj = &$detail_handler->getNext($res)) {
    $ret[] = $obj->get($t);
}
$detail_handler->close($res);

echo implode("\n", $ret);
