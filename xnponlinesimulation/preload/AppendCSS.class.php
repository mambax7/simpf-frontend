<?php

class Xnponlinesimulation_AppendCSS extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add('XoopsTpl.New', array($this, 'appendCSS'));
    }

    /**
     * append css.
     *
     * @param object &$xoopsTpl instance of XoopsTpl object
     */
    public function appendCSS(&$xoopsTpl)
    {
        $xoops_module_header = sprintf('<link rel="stylesheet" type="text/css" href="%s/modules/xoonips/backend.php?itemtype=xnponlinesimulation&amp;action=css" />', XOOPS_URL);
        $xoops_module_header .= $xoopsTpl->get_template_vars('xoops_module_header');
        $xoopsTpl->assign('xoops_module_header', $xoops_module_header);
    }
}
