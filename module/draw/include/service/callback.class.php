<?php

defined('PHPFOX') or exit('NO DICE!');

class Draw_Service_Callback extends Phpfox_Service {

    public function __construct() {
        $this->_sTable = Phpfox::getT('draw');
    }

    //like
    public function addLike($iItemId, $bDoNotSendEmail = false) {
        $aRow = $this->database()->select('draw_id, title, user_id')
                ->from(Phpfox::getT('draw'))
                ->where('draw_id = ' . (int) $iItemId)
                ->execute('getSlaveRow');

        if (!isset($aRow['draw_id'])) {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'draw\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'draw', 'draw_id = ' . (int) $iItemId);

        if (!$bDoNotSendEmail) {
            $sLink = Phpfox::permalink('draw', $aRow['draw_id'], $aRow['title']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                    ->subject(array('draw.full_name_liked_your_draw_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
                    ->message(array('draw.full_name_liked_your_draw_link_title', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
                    ->notification('like.new_like')
                    ->send();

            Phpfox::getService('notification.process')->add('draw_like', $aRow['draw_id'], $aRow['user_id']);
        }
    }

    public function deleteLike($iItemId) {
        $this->database()->updateCount('like', 'type_id = \'draw\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'draw', 'draw_id = ' . (int) $iItemId);
    }

    //notification like
    public function getNotificationLike($aNotification) {
        $aRow = $this->database()->select('d.draw_id, d.title, d.user_id, u.gender, u.full_name')
                ->from(Phpfox::getT('draw'), 'd')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
                ->where('d.draw_id = ' . (int) $aNotification['item_id'])
                ->execute('getSlaveRow');

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id']) {
            $sPhrase = Phpfox::getPhrase('draw.users_liked_gender_own_draw_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = Phpfox::getPhrase('draw.users_liked_your_draw_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = Phpfox::getPhrase('draw.users_liked_span_class_drop_data_user_row_full_name_s_span_draw_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('draw', $aRow['draw_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'draw')
        );
    }

    //end like
    //comment
    public function addComment($aVals, $iUserId = null, $sUserName = null) {

        $aDraw = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, d.title, d.draw_id, d.privacy, d.privacy_comment')
                ->from($this->_sTable, 'd')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
                ->where('d.draw_id = ' . (int) $aVals['item_id'])
                ->execute('getSlaveRow');

        if ($iUserId === null) {
            $iUserId = Phpfox::getUserId();
        }

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment', $aVals['comment_id'], 0, 0, 0, $iUserId) : null);

        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id'])) {
            $this->database()->updateCounter('draw', 'total_comment', 'draw_id', $aVals['item_id']);
        }

        // Send the user an email
        $sLink = Phpfox::permalink('draw', $aDraw['draw_id'], $aDraw['title']);

        Phpfox::getService('comment.process')->notify(array(
            'user_id' => $aDraw['user_id'],
            'item_id' => $aDraw['draw_id'],
            'owner_subject' => Phpfox::getPhrase('draw.full_name_commented_on_your_draw_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aDraw['title'])),
            'owner_message' => Phpfox::getPhrase('draw.full_name_commented_on_your_draw_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aDraw['title'])),
            'owner_notification' => 'comment.add_new_comment',
            'notify_id' => 'comment_draw',
            'mass_id' => 'draw',
            'mass_subject' => (Phpfox::getUserId() == $aDraw['user_id'] ? Phpfox::getPhrase('draw.full_name_commented_on_gender_draw', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aDraw['gender'], 1))) : Phpfox::getPhrase('draw.full_name_commented_on_draw_full_name_s_draw', array('full_name' => Phpfox::getUserBy('full_name'), 'draw_full_name' => $aDraw['full_name']))),
            'mass_message' => (Phpfox::getUserId() == $aDraw['user_id'] ? Phpfox::getPhrase('draw.full_name_commented_on_gender_draw_message', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aDraw['gender'], 1), 'link' => $sLink, 'title' => $aDraw['title'])) : Phpfox::getPhrase('draw.full_name_commented_on_draw_full_name_s_draw_message', array('full_name' => Phpfox::getUserBy('full_name'), 'draw_full_name' => $aDraw['full_name'], 'link' => $sLink, 'title' => $aDraw['title'])))
                )
        );
    }

    public function updateCommentText($aVals, $sText) {
        
    }

    public function getCommentItemName() {
        return 'draw';
    }

    public function deleteComment($iId) {
        $this->database()->update($this->_sTable, array('total_comment' => array('= total_comment -', 1)), 'draw_id = ' . (int) $iId);
    }

    public function getActivityFeedComment($aRow) {
        if (Phpfox::isUser()) {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feed_mini\' AND l.item_id = c.comment_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aItem = $this->database()->select('d.draw_id, d.title, d.time_stamp, d.total_comment, d.total_like, c.total_like, ct.text_parsed AS text, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('comment'), 'c')
                ->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
                ->join(Phpfox::getT('draw'), 'd', 'c.type_id = \'draw\' AND c.item_id = d.draw_id AND c.view_id = 0')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
                ->where('c.comment_id = ' . (int) $aRow['item_id'])
                ->execute('getSlaveRow');

        if (!isset($aItem['draw_id'])) {
            return false;
        }

        $sLink = Phpfox::permalink('draw', $aItem['draw_id'], $aItem['title']);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aItem['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : 50));
        $sUser = '<a href="' . Phpfox::getLib('url')->makeUrl($aItem['user_name']) . '">' . $aItem['full_name'] . '</a>';
        $sGender = Phpfox::getService('user')->gender($aItem['gender'], 1);

        if ($aRow['user_id'] == $aItem['user_id']) {
            $sMessage = Phpfox::getPhrase('draw.posted_a_comment_on_gender_draw_a_href_link_title_a', array('gender' => $sGender, 'link' => $sLink, 'title' => $sTitle));
        } else {
            $sMessage = Phpfox::getPhrase('draw.posted_a_comment_on_user_name_s_draw_a_href_link_title_a', array('user_name' => $sUser, 'link' => $sLink, 'title' => $sTitle));
        }
        return array(
            'no_share' => true,
            'feed_info' => $sMessage,
            'feed_link' => $sLink,
            'feed_status' => $aItem['text'],
            'feed_total_like' => $aItem['total_like'],
            'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/draw.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'like_type_id' => 'feed_mini'
        );
    }

    public function getAjaxCommentVar() {
        return 'draw.can_post_comment_on_draw';
    }

    public function getCommentItem($iId) {
        $aRow = $this->database()->select('draw_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
                ->from($this->_sTable)
                ->where('draw_id = ' . (int) $iId)
                ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment'])) {
            Phpfox_Error::set(Phpfox::getPhrase('draw.unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        return $aRow;
    }

    public function getRedirectComment($iId) {
        return $this->getFeedRedirect($iId);
    }

    //notification comment
    public function getCommentNotification($aNotification) {
        $aRow = $this->database()->select('d.draw_id, d.title, d.user_id, u.gender, u.full_name')
                ->from(Phpfox::getT('draw'), 'd')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
                ->where('d.draw_id = ' . (int) $aNotification['item_id'])
                ->execute('getSlaveRow');

        if (!isset($aRow['draw_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users'])) {
            $sPhrase = Phpfox::getPhrase('draw.users_commented_on_gender_draw_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = Phpfox::getPhrase('draw.users_commented_on_your_draw_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = Phpfox::getPhrase('draw.users_commented_on_span_class_drop_data_user_row_full_name', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('draw', $aRow['draw_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'draw')
        );
    }

    //end comment
    //activity feed
    public function getTotalItemCount($iUserId) {
        return array(
            'field' => 'total_draw',
            'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('draw'))->where('user_id = ' . (int) $iUserId . ' AND is_approved = 1 AND post_status = 1 AND item_id = 0')->execute('getSlaveField')
        );
    }

    public function getNewsFeed($aRow, $iUserId = null) {

        $oUrl = Phpfox::getLib('url');
        $oParseOutput = Phpfox::getLib('parse.output');

        $aRow['text'] = Phpfox::getPhrase('draw.owner_full_name_added_a_new_draw_a_href_title_link_title_a', array(
                    'owner_full_name' => $aRow['owner_full_name'],
                    'title' => Phpfox::getService('feed')->shortenTitle($aRow['content']),
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link']
                        )
        );

        $aRow['icon'] = 'module/draw.png';
        $aRow['enable_like'] = true;
        $aRow['comment_type_id'] = 'draw';

        return $aRow;
    }

    public function getFeedDetails($iItemId) {
        return array(
            'module' => 'draw',
            'table_prefix' => 'draw_',
            'item_id' => $iItemId
        );
    }

    public function getActivityFeed($aRow, $aCallback = null, $bIsChildItem = false) {
       if (!Phpfox::getUserParam('draw.view_drawings')) {
            return false;
        }

        if (Phpfox::isUser()) {
            $this->database()->select('l.like_id AS is_liked, ')
                    ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'draw\' AND l.item_id = d.draw_id AND l.user_id = ' . Phpfox::getUserId());
        }

        if ($bIsChildItem) {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = d.user_id');
        }

        $aRow = $this->database()->select('d.draw_id, dl.link, d.title, d.time_stamp, d.total_comment, d.total_like, '.(Phpfox::getParam('core.allow_html') ? "dl.parsed" : "dl.description").' AS text, d.module_id, d.draw_id AS item_id')
                ->from(Phpfox::getT('draw'), 'd')
                ->join(Phpfox::getT('draw_link'), 'dl', 'dl.draw_id = d.draw_id')
                ->where('d.draw_id = ' . (int) $aRow['item_id'])
                ->execute('getSlaveRow');

        if (!isset($aRow['draw_id'])) {
            return false;
        }
        $sImage = "";
        $aReturn = array(
            'feed_title' => $aRow['title'],
            'feed_info' => Phpfox::getPhrase('draw.posted_a_draw'),
            'feed_link' => Phpfox::permalink('draw', $aRow['draw_id'], $aRow['title']),
            'feed_content' => $aRow['text'],
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/draw.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'draw',
            'like_type_id' => 'draw',
            'custom_data_cache' => $aRow,
        );
        if (!empty($aRow['link']))
        {
            $sImage = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => 0,
                    'path' => 'draw.url_photo',
                    'file' => $aRow['link'],
                    'suffix' => '_150',
                    'max_width' => 150,
                    'max_height' => 150                    
                )
            );
            
            $aReturn['feed_image'] = $sImage;
        }        
        return  $aReturn;
    }
    //profile
    public function getProfileLink() {
        return 'profile.draw';
    }

    public function getProfileMenu($aUser) {
        $aMenus[] = array(
            'phrase' => Phpfox::getPhrase('draw.draws'),
            'url' => 'profile.draw',
            'total' => (int) (isset($aUser['total_draw']) ? $aUser['total_draw'] : 0),
           // 'sub_menu' => $aSubMenu,
            'icon' => 'feed/draw.png'
        );

        return $aMenus;
    }
    
    public function addTrack($iId, $iUserId = null)
	{
		(($sPlugin = Phpfox_Plugin::get('draw.component_service_callback_addtrack__start')) ? eval($sPlugin) : false);
		$this->database()->insert(Phpfox::getT('draw_track'), array(
				'item_id' => (int) $iId,
				'user_id' => Phpfox::getUserBy('user_id'),
				'time_stamp' => PHPFOX_TIME
			)
		);
	}	
}

?>
