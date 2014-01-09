<?php
defined('PHPFOX') or exit('NO DICE!');
class Draw_Component_Controller_Delete extends Phpfox_Component {

    public function process() {
        Phpfox::isUser(true);		
        if ($iId = $this->request()->getInt('id'))
        {
            $mReturn = Phpfox::getService('draw')->deleteInLine($iId);
            if (isset($mReturn['module_id']) && $mReturn['module_id'] == 'pages')
            {
                    $this->url()->send($mReturn['module_id'] . '.' . $mReturn['item_id'] . '.draw', array(), Phpfox::getPhrase('draw.draw_successfully_deleted'));
            }

            $this->url()->send('draw', array(), Phpfox::getPhrase('draw.draw_successfully_deleted'));
        }
    }
}
?>