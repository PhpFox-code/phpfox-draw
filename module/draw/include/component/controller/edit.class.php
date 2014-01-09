<?php
defined('PHPFOX') or exit('NO DICE!');
class Draw_Component_Controller_Edit extends Phpfox_Component {

    public function process() {
        Phpfox::isUser(true);
        $this->template()->setTitle(Phpfox::getPhrase('draw.edit_your_draw'));
        $iId = $this->request()->getInt('id');
        $aRow = Phpfox::getService('draw')->getDraw($iId);
        if(!isset($aRow['draw_id']))
        {
            $this->url()->send('draw',null,Phpfox::getPhrase('draw.there_are_no_photo_found'));
        }
        $this->template()->assign(array(
            'aDrawItem' => $aRow,
        ));
    }
}
?>