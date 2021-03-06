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
 * Smarty simpf_escape modifier plugin for simpf modules.
 *
 * Type:     modifier<br>
 * Name:     simpf_escape<br>
 * Purpose:  Escape the string according to escapement type
 *
 * @param string $text input
 * @param string $type type of escape html, xml or javascript
 *
 * @return string
 */
function smarty_modifier_simpf_escape($text, $type = 'html')
{
    if (!function_exists('xoonips_getutility')) {
        // return empty string if xoonips function not loaded.
        return '';
    }
    $textutil = &xoonips_getutility('text');
    switch ($type) {
    case 'html':
        $text = $textutil->html_special_chars($text);
        break;
    case 'xml':
        $text = $textutil->xml_special_chars($text, _CHARSET);
        break;
    case 'javascript':
        $text = $textutil->javascript_special_chars($text);
        break;
    case 'url':
        $text = rawurlencode($text);
        break;
    }

    return $text;
}
