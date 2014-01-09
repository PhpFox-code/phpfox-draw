<?php
defined('PHPFOX') or exit('NO DICE!');

class Draw_Component_Block_Most_Likes extends Phpfox_Component {
    public function process() {
        $aDraws = phpfox::getService('draw.browse')->getDraws(array(
            'order' => 'd.total_like DESC',
            'limit' => 5
        ), true);
        
        $this->template()->assign(array(
            'aDraws' => $aDraws,
            'sHeader' => Phpfox::getPhrase('draw.most_like')
        ));
        
        return 'block';
    }
}