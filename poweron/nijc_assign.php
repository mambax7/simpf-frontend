<?php

// $Id: nijc_assign.php,v 1.1.1.1 2009/05/29 13:42:31 okumura Exp $
// FILE		::	nijc_assign.php
// AUTHOR	::	Yoshihiro OKUMURA <okumura@brain.riken.jp>
// WEB		::	RIKEN BSI NIJC <http://nijc.brain.riken.jp/>
//

global $xoopsUser, $xoopsModule;

// override xoops_pagetitle
//if (is_object($xoopsModule)) {
//  $xoops_pagetitle = $this->get_template_vars('xoops_pagetitle');
//  if (strpos($xoops_pagetitle, $xoopsModule->getVar('name')) !== 0) {
//    $xoops_pagetitle = $xoopsModule->getVar('name').' - '.$xoops_pagetitle;
//    $this->assign('xoops_pagetitle', $xoops_pagetitle);
//  }
//}

// follow search query keyword
$keyword = xoops_getrequest('keyword');
if (!empty($keyword)) {
    $this->assign('xoonips_search_query', $keyword);
}
