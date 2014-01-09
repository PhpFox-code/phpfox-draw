<?php
defined('PHPFOX') or exit('NO DICE!');
class Draw_Component_Controller_Index extends Phpfox_Component
{
	public function process()
	{
            $aParentModule = $this->getParam('aParentModule');
            if ($this->request()->getInt('req2') > 0 && !isset($aParentModule['module_id']))
                {
                    return Phpfox::getLib('module')->setController('draw.view');			
                }
            $this->template()->setTitle(Phpfox::getPhrase('draw.draw'));
            $this->template()->setBreadcrumb(Phpfox::getPhrase('draw.draw'));

            $bIsProfile = PhpFox::getUserId();
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->search()->set(array(
                'type' => 'draw',
                'field' => 'draw.draw_id',
                'search_tool' => array(
                    'table_alias' => 'draw',
                    'search' => array(
                        'action' => ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('draw', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('draw', array('view' => $this->request()->get('view')))),
                        'default_value' => Phpfox::getPhrase('draw.search_draws_dot'),
                        'name' => 'search',
                        'field' => array('draw.title', 'draw_link.description', 'draw_link.link')
                    ),
                    'sort' => array(
                        'latest' => array('draw.time_stamp', Phpfox::getPhrase('draw.latest')),
                        'most-viewed' => array('draw.total_view', Phpfox::getPhrase('draw.most_viewed')),
                        'most-liked' => array('draw.total_like', Phpfox::getPhrase('draw.most_liked')),
                        'most-talked' => array('draw.total_comment', Phpfox::getPhrase('draw.most_discussed'))
                    ),
                    'show' => array(5, 10, 15, 20 ,25)
                )
                    )
            );

            $aBrowseParams = array(
                'module_id' => 'draw',
                'alias' => 'draw',
                'field' => 'draw_id',
                'table' => Phpfox::getT('draw'),
                'hide_view' => array('pending', 'my')
            );
            $this->search()->browse()->params($aBrowseParams)->execute();
            $aItems = $this->search()->browse()->getRows();
            Phpfox::getService('draw')->getExtra($aItems, 'user_profile');
            Phpfox::getLib('pager')->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $this->search()->browse()->getCount()));
            $sView = $this->request()->get('view');
            $this->template()->assign(array(
					'iCnt' => $this->search()->browse()->getCount(),
					'aItems' => $aItems,
					'sSearchBlock' => Phpfox::getPhrase('blog.search_blogs_'),
					'bIsProfile' => $bIsProfile,
					'sTagType' => ($bIsProfile === true ? 'blog_profile' : 'blog'),
					'sBlogStatus' => $this->request()->get('status'),
					'iShorten' => Phpfox::getParam('blog.length_in_index'),
					'sView' => $sView					
				)
			)
			->setHeader('cache', array(
				'quick_submit.js' => 'module_blog',
				'jquery/plugin/jquery.highlightFade.js' => 'static_script',				
				'quick_edit.js' => 'static_script',				
				'comment.css' => 'style_css',
				'pager.css' => 'style_css',
				'feed.js' => 'module_feed'
			)
		);
        $this->setParam('global_moderation', array(
                'name' => 'draw',
                'ajax' => 'draw.moderation',
                'menu' => array(
                    array(
                        'phrase' => Phpfox::getPhrase('draw.delete'),
                        'action' => 'delete'
                    ),
                )
            )
        );
       }
    }
?>
