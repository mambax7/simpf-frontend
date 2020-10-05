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
 * backend program to get xoonips item information
 *  - this script will get item information from other site, and
 *    return these information using text/javascirpt+json format
 *  - returning JSON structure is
 *    $ret =  array(
 *      'site_name' => 'XXX Platform',
 *      'item_id' => '100',
 *      'ext_id' => 'A20110228-100-1',
 *      'url' => 'http://example.com/modules/xoonips/detail.php?id=A20110228-100-1',
 *      'item_type' => 'Model',
 *      'last_modified_date' => '1204181166',
 *      'registration_date' => '1201002202',
 *      'title' => 'Example Model',
 *      'authors' => array(
 *        'Author 1',
 *        'Author 2',
 *        'Author 3',
 *         ...,
 *      ),
 *      'keywords' => array(
 *        'Keyword 1',
 *        'Keyword 2',
 *        'Keyword 3',
 *        ...,
 *      ),
 *      'rights' = 'CC BY',
 *      'description' => 'Description of Example Model',
 *      'readme' = 'Example Model Readme ...',
 *      'simulator' = 'MATLAB',
 *      'error' => 'error message' // if an error occured
 *    );.
 */
if (!defined('XOONIPS_PATH')) {
    exit();
}

// class file
require_once XOONIPS_PATH.'/class/base/JSON.php';
require_once dirname(dirname(__FILE__)).'/class/xmlrpc.class.php';
require_once dirname(dirname(__FILE__)).'/class/xoonips_client.class.php';

// change internal encoding to UTF-8
if (extension_loaded('mbstring')) {
    mb_language('uni');
    mb_internal_encoding('UTF-8');
    mb_http_output('pass');
}

// remove ob filters
$handlers = ob_list_handlers();
while (!empty($handlers)) {
    ob_end_clean();
    $handlers = ob_list_handlers();
}

$ret = false;
$is_error = false;
$error_message = '';
if (!isset($_SERVER['HTTP_REFERER']) || 0 == preg_match('/^'.preg_quote(XOOPS_URL, '/').'/', $_SERVER['HTTP_REFERER'])) {
    $is_error = true;
    $error_message = 'Turn REFERER on';
}
if (!$is_error) {
    $formdata = &xoonips_getutility('formdata');
    $url = $formdata->getValue('get', 'url', 's', false, '');
    if ('' == $url) {
        $is_error = true;
        $error_message = 'empty url';
    }
}

if (!$is_error) {
    $ret = getXooNIpsItemInformation($url, $error_message);
    if (false === $ret) {
        $is_error = true;
    }
}

$data = [];
if ($is_error) {
    $data['error'] = $error_message;
} else {
    $data = $ret;
}

// json
$json = new Services_JSON();
$encode = $json->encode($data);

// output
header('Content-Type: text/javascript+json; charset=utf-8');
echo $encode;
exit();

/**
 * get xoonips item information.
 *
 * @param string $url     xoonips item detail url
 * @param string &$errmes error message
 *
 * @return array item information, false if failure
 */
function getXooNIpsItemInformation($url, &$errmes)
{
    $purl = parse_url($url);
    if (!isset($purl['scheme']) || !isset($purl['host']) || !isset($purl['path']) || !isset($purl['query'])) {
        // invalid url string
        $errmes = 'invalid url string';

        return false;
    }

    // check whether url is xoonips item detail page
    if (!preg_match('/\/modules\/xoonips\/detail.php$/', $purl['path'])) {
        $errmes = 'not xoonips item detail url';

        return false;
    }

    // detect id and id type
    $id = false;
    $id_type = '';
    $queries = explode('&', $purl['query']);
    foreach ($queries as $q) {
        $kv = explode('=', $q);
        // ignore
        if (2 != count($kv) && 'ml_lang' == $kv[0]) {
            continue;
        }
        if ('item_id' == $kv[0] && preg_match('/^[1-9][0-9]*$/', $kv[1])) {
            $id_type = 'item_id';
            $id = $kv[1];
            break;
        } else {
            // use other key name for id
            $id_type = 'ext_id';
            $id = $kv[1];
        }
    }
    if (false === $id) {
        $errmes = 'item id not found';

        return false;
    }
    $port = isset($purl['port']) ? sprintf(':%u', $purl['port']) : '';
    $path = preg_replace('/detail.php$/', 'xoonipsapi.php', $purl['path']);
    $uri = sprintf('%s://%s%s%s', $purl['scheme'], $purl['host'], $port, $path);

    // get proxy information of XooNIps
    $proxy_configs = ['proxy_host', 'proxy_port', 'proxy_user', 'proxy_pass'];
    $config_handler = &xoonips_getormhandler('xoonips', 'config');
    $criteria = new CriteriaCompo();
    foreach ($proxy_configs as $config) {
        $criteria->add(new Criteria('name', $config), 'OR');
    }
    $objs = &$config_handler->getObjects($criteria);
    $proxy = [];
    foreach ($objs as $obj) {
        $key = $obj->getVar('name', 'n');
        $value = $obj->getVar('value', 'n');
        $proxy[$key] = $value;
    }

    // open xoonips
    $c = new XooNIpsClient($uri);
    $c->setCondition('user_agent', 'PHP XooNIps API Client');

    // set proxy
    if ('' != $proxy['proxy_host']) {
        $param = [
            $proxy['proxy_host'],
            $proxy['proxy_port'],
            $proxy['proxy_user'],
            $proxy['proxy_pass'],
            ('' == $proxy['proxy_user'] ? '' : 'basic'),
        ];
        $c->setCondition('proxy', $param);
    }

    // login
    if (!$c->login('', '')) {
        $errmes = 'failed to connect to xoonips';

        return false;
    }

    // get registered item types
    $item_types = $c->getItemtypes();
    if (false === $item_types) {
        $errmes = 'failed to get item type information';
        $c->logout();

        return false;
    }
    // get item information
    $item = &$c->getItem($id, $id_type);
    if (false === $item) {
        $errmes = 'failed to get item information';
        $c->logout();

        return false;
    }

    // normalize item type specific information
    $ret = [];
    $ret['site_name'] = '';
    $site_names = [
        'visiome.neuroinf.jp' => 'Visiome Platform',
        'bmi.neuroinf.jp' => 'BMI Platform',
        'ivb.neuroinf.jp' => 'Invertebrate Brain Platform',
        'ibr.neuroinf.jp' => 'Integrative Brain Research Platform',
        'cerebellum.neuroinf.jp' => 'Cerebellar Platform',
        'nimg.neuroinf.jp' => 'Neuro-Imaging Platform',
        'dynamicbrain.neuroinf.jp' => 'Dynamic Brain Platform',
        'sim.neuroinf.jp' => 'Simulation Platform',
    ];
    if (isset($site_names[$purl['host']])) {
        $ret['site_name'] = $site_names[$purl['host']];
    }
    $ret['item_id'] = trim($item['item_id']);
    $ret['ext_id'] = trim($item['ext_id']);
    $ret['url'] = trim($item['url']);
    foreach ($item_types as $item_type) {
        if ($item_type['id'] == $item['itemtype']) {
            $ret['item_type'] = trim($item_type['title']);
            break;
        }
    }
    $ret['last_modified_date'] = trim($item['last_modified_date']);
    $ret['registration_date'] = trim($item['registration_date']);
    $ret['title'] = trim(implode(' ', $item['titles']));
    $ret['author'] = [];
    $ret['keywords'] = array_map('trim', $item['keywords']);
    $ret['rights'] = '';
    $ret['description'] = trim($item['comment']);
    $ret['readme'] = '';
    $ret['simulator'] = '';
    $cc = [
        'use_cc' => 0,
        'cc_commercial_use' => 0,
        'cc_modification' => 0,
    ];
    $simulators = [
        'matlab' => 'MATLAB',
        'neuron' => 'Neuron',
        'satellite' => 'SATELLITE',
        'genesis' => 'Genesis',
        'a_cell' => 'A-Cell',
        'mathematica' => 'Mathematica',
    ];
    foreach ($item['detail_field'] as $detail) {
        $name = $detail['name'];
        $value = trim($detail['value']);
        switch ($name) {
        case 'author':
        case 'creator':
        case 'developer':
        case 'experimenter':
            $ret['author'][] = $value;
            break;
        case 'rights':
        case 'readme':
            $ret[$name] = $value;
            break;
        case 'use_cc':
        case 'cc_commercial_use':
        case 'cc_modification':
            $cc[$name] = $value;
            break;
        case 'model_type':
        case 'tool_type':
            if (isset($simulators[$value])) {
                $ret['simulator'] = $simulators[$value];
            }
            break;
        }
    }
    if (1 == $cc['use_cc']) {
        if (0 == $cc['cc_commercial_use']) {
            if (0 == $cc['cc_modification']) {
                $ret['rights'] = 'Creative Commons Attribution-NonCommercial-NoDerivs';
            } elseif (1 == $cc['cc_modification']) {
                $ret['rights'] = 'Creative Commons Attribution-NonCommercial-ShareAlike';
            } else {
                $ret['rights'] = 'Creative Commons Attribution-NonCommercial';
            }
        } else {
            if (0 == $cc['cc_modification']) {
                $ret['rights'] = 'Creative Commons Attribution-NoDerivs';
            } elseif (1 == $cc['cc_modification']) {
                $ret['rights'] = 'Creative Commons Attribution-ShareAlike';
            } else {
                $ret['rights'] = 'Creative Commons Attribution';
            }
        }
    }

    // logout
    $c->logout();

    return $ret;
}
