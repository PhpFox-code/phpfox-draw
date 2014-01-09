<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div style="word-wrap:break-word;" id="js_draw_entry{$aItem.draw_id}">    
    <div class="row_title drawing_item_list">    
        <div class="row_title_image">
            {img user=$aItem suffix='_50_square' max_width=50 max_height=50}
            {if Phpfox::getUserParam('draw.can_approve_draws')
                || (Phpfox::getUserParam('draw.edit_own_draw') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('draw.edit_user_draw')
                || (Phpfox::getUserParam('draw.delete_own_draw') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('draw.delete_user_draw')
            }    
            <div class="row_edit_bar_parent">
                <div class="row_edit_bar_holder">
                    <ul>
                        {template file='draw.block.link'}
                    </ul>            
                </div>
                <div class="row_edit_bar">                
                        <a href="#" class="row_edit_bar_action"><span>{phrase var='draw.actions'}</span></a>                            
                </div>
            </div>
            {/if}                
            {if Phpfox::getUserParam('draw.delete_user_draw')}<a href="#{$aItem.draw_id}" class="moderate_link" rel="blog">{phrase var='draw.moderate'}</a>{/if}        
        </div>
        <div class="row_title_info">
             <header>            
                <h1 id="js_drawing_edit_title{$aItem.draw_id}" itemprop="name">
                    <a href="{permalink module='draw' id=$aItem.draw_id title=$aItem.title}" id="js_draw_edit_inner_title{$aItem.draw_id}" class="link ajax_link" itemprop="url">{$aItem.title|clean|shorten:55:'...'|split:20}</a>
                </h1>
            </header>
            <div class="extra_info">
                {phrase var='draw.by_full_name' full_name=$aItem|user:'':'':50:'':'author'}
                {plugin call='draw.template_block_entry_date_end'}
            </div>
            <div class="draw_content">
                <div id="js_draw_edit_text{$aItem.draw_id}">    
                    <div class="item_content item_view_content" itemprop="articleBody">
                        <div class="draw_image">
                            {img server_id=0 path='draw.url_photo' file=$aItem.link suffix='_150' max_width=150 max_height=150 title=$aItem.title}
                        </div>
                        <div class="extra_info">
                            {$aItem.description|strip_tags|highlight:'search'|split:55|shorten:35'...'}
                        </div>
                        <div class="clear"></div>
                    </div>            
                </div>    
                {module name='feed.comment' aFeed=$aItem.aFeed}          
            </div>
        </div>                    
    </div>
 
</div>
{*
<div class="row_title_info">
    <div class="row_title_image">
        {img user=$aItem suffix='_50_square' max_width=50 max_height=50}
    </div>
    <div>
            <header>
            <h1 id="js_draw_edit_title{$aItem.draw_id}" itemprop="name">
                <a href="{permalink module='draw' id=$aItem.draw_id title=$aItem.title}" id="js_draw_edit_inner_title{$aItem.draw_id}" class="link ajax_link" itemprop="url">{$aItem.title|clean|shorten:55:'...'|split:20}</a>
            </h1>
            </header>
    </div>
    <div class="extra_info">
        {phrase var='draw.by_full_name' full_name=$aItem|user:'':'':50:'':'author'}
        {plugin call='draw.template_block_entry_date_end'}
    </div>
</div>
<div class="content">
    <img src="file/drawings/{$aItem.file_name}" height="350" width="750" alt="{phrase var='your_draw'}"> 
    <br>
    <label for="text">{$aItem.description}</label>
    {module name='feed.comment' aFeed=$aItem.aFeed}
</div>
<br>
<hr>
*}