<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @author  		ngontt
 * @package 		Module Draw
 */
class Draw_Service_Browse extends Phpfox_Service {

    /**
     * Class constructor
     */
    public function __construct() {
        $this->_sTable = Phpfox::getT('draw');
    }

    public function query() {
        $this->database()->select('draw_link.link,'.(Phpfox::getParam('core.allow_html') ? "draw_link.parsed" : "draw_link.description").' AS description, ')
                ->join(Phpfox::getT('draw_link'), 'draw_link', 'draw_link.draw_id = draw.draw_id');

        if (Phpfox::isUser() && Phpfox::isModule('like')) {
            $this->database()->select('lik.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'draw\' AND lik.item_id = draw.draw_id AND lik.user_id = ' . Phpfox::getUserId());
        }
    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false) {
        if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend)) {
            $this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = draw.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
        }

        if (Phpfox::getParam('core.section_privacy_item_browsing')) {
            if ($this->search()->isSearch()) {
                $this->database()->join(Phpfox::getT('draw_link'), 'draw_link', 'draw_link.draw_id = draw.draw_id');
            }
        } else {
            if ($bIsCount && $this->search()->isSearch()) {
                $this->database()->join(Phpfox::getT('draw_link'), 'draw_link', 'draw_link.draw_id = draw.draw_id');
            }
        }
    }
    
    public function getDraws($aParams = array(), $bLink = false) {
        $oSelect = $this->database()->select('*')
                        ->from($this->_sTable, 'd')
                        ->join(phpfox::getT('draw_link'), 'l', 'd.draw_id = l.draw_id')
                        ->join(phpfox::getT('user'), 'u', 'd.user_id = u.user_id');
        
        $where = array(
            ' AND d.is_approved = 1',
            ' AND d.post_status = 1'
        );
        if (isset($aParams['order'])) {
            $oSelect->order($aParams['order']);
        } else {
            $oSelect->order('RAND()');
        }
        
        if (isset($aParams['limit'])) {
            $oSelect->limit($aParams['limit']);
        }
        
        $oSelect->where($where);
        
        return $bLink ? $this->links($oSelect->execute('getSlaveRows')) : $oSelect->execute('getSlaveRows');
    }
    
    public function links($aDraws) {
        if (!count($aDraws)) return $aDraws;
        for ($i = 0; $i < count($aDraws); $i++) $aDraws[$i]['href'] = Phpfox::permalink('draw', $aDraws[$i]['draw_id'], $aDraws[$i]['title']);
        
        return $aDraws;
    }

    public function __call($sMethod, $aArguments) {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('draw.service_browse__call')) {
            eval($sPlugin);
            return;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

  

}

?>