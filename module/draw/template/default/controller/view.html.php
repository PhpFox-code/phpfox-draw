<?php

defined('PHPFOX') or exit('NO DICE!');
?>

<div class="item_view">
    <div class="item_info">
        {phrase var='draw.by_user' full_name=$aItem|user:'':'':50:'':'author'}
    </div>
    {if Phpfox::getUserParam('draw.can_approve_draws')
        || (Phpfox::getUserParam('draw.edit_own_draw') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('draw.edit_user_draw')
        || (Phpfox::getUserParam('draw.delete_own_draw') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('draw.delete_user_draw')
        }
        <div class="item_bar">
            <div class="item_bar_action_holder">
                <a href="#" class="item_bar_action"><span>{phrase var='draw.actions'}</span></a>        
                <ul>
                        {template file='draw.block.link'}
                </ul>
            </div>
        </div>
    {/if}
    <div class="description">
        {$aItem.description}
    </div>
     <div class="image">
        {img server_id=0 path='draw.url_photo' file=$aItem.link suffix='_500' title=$aItem.title}
    </div> 
    <div class="js_moderation_on">
        {module name='feed.comment'}
    </div>
</div>