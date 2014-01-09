<?php
defined('PHPFOX') or exit('NO DICE!');
class Draw_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function postDrawing()
    {
        $aVals = $this->get('val');
        $bPassValidation = true;
        $sMessage = Phpfox::getPhrase('draw.posted_new_image_successfully');
        if(!isset($aVals['title']) || empty($aVals['title']))
        {
            $sMessage = Phpfox::getPhrase('draw.title_cannot_be_empty');
            $bPassValidation = false;
        }
        if(!isset($aVals['title']) || empty($aVals['title']))
        {
            $sMessage = Phpfox::getPhrase('draw.image_cannot_be_empty');
            $bPassValidation = false;
        }
        if($bPassValidation == false)
        {
            $this->call('Drawing.showMessage("'.$sMessage.'","fail");');    
            $this->call('Drawing.hideLoading();');
        } else{
            if(phpfox::getService('draw')->add($aVals))
            {
               $this->call('Drawing.showMessage("'.$sMessage.'","success");');    
               $this->call('Drawing.hideLoading();'); 
               $this->call('Drawing.hideContent();');
            }else{
                $sMessage = Phpfox::getPhrase('draw.posted_new_image_fail');
                $this->call('Drawing.showMessage("'.$sMessage.'","fail");');    
                $this->call('Drawing.hideLoading();');
            }
            
        }
    }
    public function moderation()
    {
        Phpfox::isUser(true);
        
        switch ($this->get('action'))
        {
            case 'delete':
                Phpfox::getUserParam('draw.delete_user_draw', true);
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('draw')->delete($iId);
                    $this->slideUp('#js_draw_entry' . $iId);
                }                
                $sMessage = Phpfox::getPhrase('draw.image_s_successfully_deleted');
                break;
        }
        
        $this->alert($sMessage, 'Moderation', 300, 150, true);
        $this->hide('.moderation_process');            
    }
}

?>