<?php

defined('PHPFOX') or exit('NO DICE!');

class Draw_Component_Controller_View extends Phpfox_Component {

    public function process() {
        if ($this->request()->getInt('id')) {
            return Phpfox::getLib('module')->setController('error.404');
        }
        $aItem = Phpfox::getService('draw')->getDraw($this->request()->getInt('req2'));
        if ((!isset($aItem['draw_id'])) ||
                (isset($aItem['module_id']) && Phpfox::isModule($aItem['module_id']) != true)) {
            return Phpfox_Error::display(Phpfox::getPhrase('draw.draw_not_found'));
        }
        if (Phpfox::isModule('privacy')) {
           $bReturn = Phpfox::getService('privacy')->check('draw', $aItem['draw_id'], $aItem['user_id'], $aItem['privacy'], $aItem['is_friend'], true);
            if (!$bReturn) {
                Phpfox_Error::display(Phpfox::getPhrase('privacy.the_item_or_section_you_are_trying_to_view_has_specific_privacy_settings_enabled_and_cannot_be_viewed_at_this_time'));
                //$bIsView = false;
                return true;
            }
        }
        
        if (Phpfox::isModule('track') && Phpfox::isUser() && Phpfox::getUserId() != $aItem['user_id'] && !$aItem['is_viewed'])
		{
			Phpfox::getService('track.process')->add('draw', $aItem['draw_id']);
			Phpfox::getService('draw')->updateView($aItem['draw_id']);
		}
        
        $this->setParam(array(
            'sTrackType' => 'draw',
            'iTrackId' => $aItem['draw_id'],
            'iTrackUserId' => $aItem['user_id']
                )
        );
        $aItem['bookmark_url'] = Phpfox::permalink('draw', $aItem['draw_id'], $aItem['title']);
        $this->setParam('aFeed', array(
            'comment_type_id' => 'draw',
            'privacy' => $aItem['privacy'],
            'comment_privacy' => $aItem['privacy_comment'],
            'like_type_id' => 'draw',
            'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
            'feed_is_friend' => $aItem['is_friend'],
            'item_id' => $aItem['draw_id'],
            'user_id' => $aItem['user_id'],
            'total_comment' => $aItem['total_comment'],
            'total_like' => $aItem['total_like'],
            'feed_link' => $aItem['bookmark_url'],
            'feed_title' => $aItem['title'],
            'feed_display' => 'view',
            'feed_total_like' => $aItem['total_like'],
            'report_module' => 'draw',
            'report_phrase' => Phpfox::getPhrase('draw.report_this_draw'),
            'time_stamp' => $aItem['time_stamp']
                )
        );
        $sBreadcrumb = $this->url()->makeUrl('draw');
        $bIsProfile = $this->getParam('bIsProfile');
        $sFileName = $aItem['link'];
        $aItem['file_name'] = $sFileName;
        $aItem['dir'] = PHPFOX_DIR_FILE;
        $this->template()->setTitle($aItem['title'])
                ->setBreadCrumb(Phpfox::getPhrase('draw.draws_title'), $sBreadcrumb)
                ->setBreadCrumb($aItem['title'], $this->url()->permalink('draw', $aItem['draw_id'], $aItem['title']), true)
                ->setMeta('description', $aItem['title'] . '.')
                ->setMeta('description', $aItem['description'] . '.')
                ->setMeta('keywords', $this->template()->getKeywords($aItem['title']))
                ->assign(array(
                    'aItem' => $aItem,
                    'bBlogView' => true,
                    'bIsProfile' => $bIsProfile,
                  )
                )->setHeader('cache', array(
            'jquery/plugin/jquery.highlightFade.js' => 'static_script',
            'jquery/plugin/jquery.scrollTo.js' => 'static_script',
            'comment.css' => 'style_css',
            'pager.css' => 'style_css',
            'feed.js' => 'module_feed'
                )
        );
    }

}

?>
