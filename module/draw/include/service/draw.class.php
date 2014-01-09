<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
defined('PHPFOX') or exit('NO DICE!');

class Draw_Service_Draw extends Phpfox_Service {

    private $_sDestination;

    public function __construct() {
        $this->_sTable = Phpfox::getT('draw');
    }

    public function add($aVals) {
        $oFilter = Phpfox::getLib('parse.input');
        //Save draw 
        $sData = $aVals['image'];
        $aImage = explode('base64,', $sData);
        if(!isset($aImage[1]))
        {
            return false;
        }
        $base64Image = base64_decode($aImage[1]);
        $sFileName = $this->uploadImage($base64Image);
        if(empty($sFileName))
        {
            return false;
        }
        //add link
        $aInsert = array(
            'user_id' => Phpfox::getUserId(),
            'title' => $aVals['title'],
            'time_stamp' => PHPFOX_TIME,
            'is_approved' => 1,
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'post_status' => (isset($aVals['post_status']) ? $aVals['post_status'] : '1')
        );
        $iId = $this->database()->insert(Phpfox::getT('draw'), $aInsert);
        $this->database()->insert(Phpfox::getT('draw_link'), array(
            'draw_id' => $iId,
            'description' => $oFilter->clean($aVals['description']),
            'parsed' => $oFilter->prepare($aVals['description']),
            'link' => $sFileName
                )
        );

        //add activity feed
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('draw', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0)) : null);
        //Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'draw', '+');
        return $iId;
    }

    public function uploadImage($sImageBase64) {
        $sDestinationPath = PHPFOX_DIR_FILE.'draw'.PHPFOX_DS;
        $sDestination = $this->_buildDir($sDestinationPath);
        $sFileName = md5(PHPFOX_TIME.uniqid().$sDestination);
        $fp = fopen($sDestination.$sFileName.'.png','w');
        fwrite($fp,$sImageBase64);
        fclose($fp);
        $sFileName =  str_replace($sDestinationPath,'',$sDestination).$sFileName.'%s.png';
        $oImage = Phpfox::getLib('image');
        // Loop thru all the other smaller images
        foreach(Phpfox::getParam('draw.drawing_image_sizes') as $iSize)
        {
            // Make sure the image exists
           if ($oImage->createThumbnail($sDestinationPath . sprintf($sFileName, ''), $sDestinationPath. sprintf($sFileName, '_' . $iSize), $iSize, $iSize, true))
           {
                
                continue;
           }    
        }
    
        return $sFileName;
        
    }

    private function _buildDir($sDestination) {
        if (!PHPFOX_SAFE_MODE && Phpfox::getParam('core.build_file_dir'))
        {
            $aParts = explode('/', Phpfox::getParam('core.build_format'));
            $this->_sDestination = $sDestination;
            foreach ($aParts as $sPart)
            {
                $sDate = date($sPart) . PHPFOX_DS;
                $this->_sDestination .=    $sDate;
                if (!is_dir($this->_sDestination))
                {
                    @mkdir($this->_sDestination, 0777);
                    @chmod($this->_sDestination, 0777);
                }
            }
            
            // Make sure the directory was actually created, if not we use the default dir we know is working
            if (is_dir($this->_sDestination))
            {
                return $this->_sDestination;
            }    
        }
        $this->_sDestination = $sDestination;
        return $this->_sDestination;
    }

    public function getDraw($iId) {
        if (Phpfox::isModule('track'))
		{
			$this->database()->select("draw_track.item_id AS is_viewed, ")->leftJoin(Phpfox::getT('draw_track'), 'draw_track', 'draw_track.item_id = draw.draw_id AND draw_track.user_id = ' . Phpfox::getUserBy('user_id'));
		}
        
        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = draw.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }

        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'draw\' AND l.item_id = draw.draw_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select("draw.*,dl.link, " . (Phpfox::getParam('core.allow_html') ? "dl.parsed" : "dl.description") . " AS description, " . Phpfox::getUserField())
                ->from($this->_sTable, 'draw')
                ->join(Phpfox::getT('draw_link'), 'dl', 'dl.draw_id = draw.draw_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = draw.user_id')
                ->where('draw.draw_id = ' . (int) $iId)
                ->execute('getSlaveRow');


        if (!isset($aRow['is_friend'])) {
            $aRow['is_friend'] = 0;
        }

        return $aRow;
    }

    public function getDrawForEdit($iId) {
        $aRow =  $this->database()->select("draw.*,draw_link.link, draw_link.description AS description, u.user_name")
                        ->from($this->_sTable, 'draw')
                        ->join(Phpfox::getT('draw_link'), 'draw_link', 'draw_link.draw_id = draw.draw_id')
                        ->join(Phpfox::getT('user'), 'u', 'u.user_id = draw.user_id')
                        ->where('draw.draw_id = ' . (int) $iId)
                        ->execute('getSlaveRow');
        if(isset($aRow['draw_id']))
        {
            if (!empty($aRow['link']))
            {
                $sImage = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => 0,
                        'path' => 'draw.url_photo',
                        'file' => $aRow['link'],
                        'suffix' => '_500',
                        'return_url' => true,
                    )
                );
                
                $aRow['image_link'] = $sImage;
            }        
        }
        return $aRow;
    }

    public function update($iId, $iUserId, $aVals, &$aRow = null) {

        if (!isset($aVals['post_status'])) {
             $aVals['post_status'] = 1;
        }

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        $oFilter = Phpfox::getLib('parse.input');

        Phpfox::getService('ban')->checkAutomaticBan($aVals['title'] . ' ' . $aVals['description']);

        if (defined('PHPFOX_RAN_ALTER_TITLE_FIELDS') && PHPFOX_RAN_ALTER_TITLE_FIELDS) {
            $sTitle = $oFilter->clean($aVals['title']);
        } else {
            $sTitle = $oFilter->clean($aVals['title'], 255);
        }

        $aUpdate = array(
            'title' => $sTitle,
            'time_update' => PHPFOX_TIME,
            'is_approved' => 1,
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'post_status' => (isset($aVals['post_status']) ? $aVals['post_status'] : '1'),
        );

        if ($aRow !== null && isset($aVals['post_status']) && $aRow['post_status'] == '2' && $aVals['post_status'] == '1') {
            $aUpdate['time_stamp'] = PHPFOX_TIME;
        }

        if (Phpfox::getUserParam('draw.approve_draws')) { // if the draws added by this user group need to be approved...
            $aVals['is_approved'] = $aUpdate['is_approved'] = 0;
        }


        $bIsSpam = false;
        if (Phpfox::getParam('draw.spam_check_draws')) {
            if (Phpfox::getLib('spam')->check(array(
                        'action' => 'isSpam',
                        'params' => array(
                            'module' => 'draw',
                            'content' => $oFilter->prepare($aVals['description'])
                        )
                            )
                    )
            ) {
                $aInsert['is_approved'] = '9';
                $bIsSpam = true;
            }
        }

        $this->database()->update(Phpfox::getT('draw'), $aUpdate, 'draw_id = ' . (int) $iId);
        $this->database()->update(Phpfox::getT('draw_link'), array(
            'description' => $oFilter->clean($aVals['description']),
            'parsed' => $oFilter->prepare($aVals["description"])
                ), 'draw_id = ' . (int) $iId);

        if ($aRow !== null && $aRow['post_status'] == '2' && $aVals['post_status'] == '1') {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('draw', $iId, $aVals['privacy'], $aVals['privacy_comment'], 0, $iUserId) : null);

            // Update user activity
            Phpfox::getService('user.activity')->update($iUserId, 'draw');
        } else {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('draw', $iId, $aVals['privacy'], $aVals['privacy_comment'], 0, $iUserId) : null);
        }

        if (Phpfox::isModule('privacy')) {
            if ($aVals['privacy'] == '4') {
                Phpfox::getService('privacy.process')->update('draw', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
            } else {
                Phpfox::getService('privacy.process')->delete('draw', $iId);
            }
        }

        // $this->cache()->remove(array('user/' . $iUserId, 'draw_browse'), 'substr');

        return $iId;
    }

    public function getExtra(&$aItems, $sType = null) {
        if (!is_array($aItems)) {
            $aItems = array();
        }

        $aIds = array();
        foreach ($aItems as $iKey => $aValue) {
            $aIds[] = $aValue['draw_id'];
        }

        foreach ($aItems as $iKey => $aValue) {

            $aItems[$iKey]['bookmark_url'] = Phpfox::permalink('draw', $aValue['draw_id'], $aValue['title']);
            $aItems[$iKey]['file_name'] = $aItems[$iKey]['link'];
            $aItems[$iKey]['aFeed'] = array(
                'feed_display' => 'mini',
                'comment_type_id' => 'draw',
                'privacy' => $aValue['privacy'],
                'comment_privacy' => $aValue['privacy_comment'],
                'like_type_id' => 'draw',
                'feed_is_liked' => (isset($aValue['is_liked']) ? $aValue['is_liked'] : false),
                'feed_is_friend' => (isset($aValue['is_friend']) ? $aValue['is_friend'] : false),
                'item_id' => $aValue['draw_id'],
                'user_id' => $aValue['user_id'],
                'total_comment' => $aValue['total_comment'],
                'feed_total_like' => $aValue['total_like'],
                'total_like' => $aValue['total_like'],
                'feed_link' => $aItems[$iKey]['bookmark_url'],
                'feed_title' => $aValue['title'],
                'time_stamp' => $aValue['time_stamp'],
                'type_id' => 'draw'
            );
        }
    }

    public function delete($iId) {
        (($sPlugin = Phpfox_Plugin::get('draw.service_delete__start')) ? eval($sPlugin) : false);
        $aDraw = Phpfox::getService('draw')->getDraw($iId);

        $this->database()->delete(Phpfox::getT('draw'), "draw_id = " . (int) $iId);
        $this->database()->delete(Phpfox::getT('draw_link'), "draw_id = " . (int) $iId);

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('draw', (int) $iId) : null);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_draw', $iId) : null);

        // Update user activity
       // Phpfox::getService('user.activity')->update($aDraw['user_id'], 'draw', '-');

        (($sPlugin = Phpfox_Plugin::get('draw.service_delete')) ? eval($sPlugin) : false);
    }

    public function deleteInLine($iId) {
        if (($iUserId = Phpfox::getService('draw')->hasAccess($iId, 'delete_own_draw', 'delete_user_draw'))) {
            $aDraw = $this->database()->select('*')
                    ->from(Phpfox::getT('draw'))
                    ->where('draw_id = ' . (int) $iId)
                    ->execute('getSlaveRow');

            $this->delete($iId);

            (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem($iUserId, $iId, 'draw') : null);

            // Update user activity
            Phpfox::getService('user.activity')->update($iUserId, 'draw', '-');

            return $aDraw;
        }
        return false;
    }
    public function hasAccess($iId, $sUserPerm, $sGlobalPerm) {
        (($sPlugin = Phpfox_Plugin::get('draw.service_draw_hasaccess_start')) ? eval($sPlugin) : false);

        $aRow = $this->database()->select('u.user_id')
                ->from($this->_sTable, 'd')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
                ->where('d.draw_id = ' . (int) $iId)
                ->execute('getSlaveRow');

        (($sPlugin = Phpfox_Plugin::get('draw.service_draw_hasaccess_end')) ? eval($sPlugin) : false);

        if (!isset($aRow['user_id'])) {
            return false;
        }

        if ((Phpfox::getUserId() == $aRow['user_id'] && Phpfox::getUserParam('draw.' . $sUserPerm)) || Phpfox::getUserParam('draw.' . $sGlobalPerm)) {
            return $aRow['user_id'];
        }
        (($sPlugin = Phpfox_Plugin::get('draw.component_service_draw_getdraw__end')) ? eval($sPlugin) : false);
        return false;
    }

    public function updateView($iId)
	{
		$this->database()->query("
			UPDATE " . $this->_sTable . "
			SET total_view = total_view + 1
			WHERE draw_id = " . (int) $iId . "
		");			
		
		return true;
	}
}

?>
