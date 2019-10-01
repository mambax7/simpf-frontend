<?php

if (defined('XOOPS_CUBE_LEGACY')) {
    // for cubeUtils
    if (isset($GLOBALS['cubeUtilMlang'])) {
        $this->assign('mlang_use_ml', true);
        $this->assign('mlang_has_en', '' != $GLOBALS['cubeUtilMlang']->getLangName('en'));
        $this->assign('mlang_has_ja', '' != $GLOBALS['cubeUtilMlang']->getLangName('ja'));
        if (empty($_SERVER['QUERY_STRING'])) {
            $pagenquery = $_SERVER['PHP_SELF'].'?'.CUBE_UTILS_ML_PARAM_NAME.'=';
        } elseif (isset($_SERVER['QUERY_STRING'])) {
            $query = explode('&', $_SERVER['QUERY_STRING']);
            $langquery = $_SERVER['QUERY_STRING'];
            // If the last parameter of the QUERY_STRING is sel_lang,
            // delete it so we don't have repeating sel_lang=...
            if (0 === strpos($query[count($query) - 1], CUBE_UTILS_ML_PARAM_NAME.'=')) {
                $langquery = str_replace('&'.$query[count($query) - 1], '', $langquery);
            }
            $pagenquery = $_SERVER['PHP_SELF'].'?'.$langquery.'&'.CUBE_UTILS_ML_PARAM_NAME.'=';
            $pagenquery = str_replace('?&', '?', $pagenquery);
            $pagenquery = str_replace('&', '&amp;', $pagenquery);
        }
        $this->assign('mlang_pagenquery', $pagenquery);
    }
} elseif (function_exists('sysutil_get_xoops_option')) {
    // for sysutil
    if (sysutil_get_xoops_option('sysutil', 'sysutil_use_ml')) {
        $this->assign('mlang_use_ml', true);
        $this->assign('mlang_has_en', '' != sysutil_ml_getlangname('en'));
        $this->assign('mlang_has_ja', '' != sysutil_ml_getlangname('ja'));
        if (empty($_SERVER['QUERY_STRING'])) {
            $pagenquery = $_SERVER['PHP_SELF'].'?'.SYSUTIL_ML_PARAM_NAME.'=';
        } elseif (isset($_SERVER['QUERY_STRING'])) {
            $query = explode('&', $_SERVER['QUERY_STRING']);
            $langquery = $_SERVER['QUERY_STRING'];
            // If the last parameter of the QUERY_STRING is sel_lang,
            // delete it so we don't have repeating sel_lang=...
            if (0 === strpos($query[count($query) - 1], SYSUTIL_ML_PARAM_NAME.'=')) {
                $langquery = str_replace('&'.$query[count($query) - 1], '', $langquery);
            }
            $pagenquery = $_SERVER['PHP_SELF'].'?'.$langquery.'&'.SYSUTIL_ML_PARAM_NAME.'=';
            $pagenquery = str_replace('?&', '?', $pagenquery);
            $pagenquery = str_replace('&', '&amp;', $pagenquery);
        }
        $this->assign('mlang_pagenquery', $pagenquery);
    }
}
