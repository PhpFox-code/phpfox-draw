<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Draw_Component_Block_Drawpop extends Phpfox_Component {

    public function process() {
        if(!phpfox::isUser())
        {
            return false;
        }
        if(phpfox::isAdminPanel())
        {
            return false;
        }
        if ($this->request()->get('req2')=='add'){
            return false;
        }
        $this->template()
                ->assign(array(
                    'sCoreUrl' => phpfox::getParam('core.path')))
                
        ;
    }

}

?>