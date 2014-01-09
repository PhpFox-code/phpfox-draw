<?php
?>
{if isset($aForms.draw_id)}
<div class="view_item_link">
    <a href="{permalink module='draw' id=$aForms.draw_id title=$aForms.title}">{phrase var='draw.view_draw'}</a>
</div>
{/if}
<div class="draw_body">
    {$sCreateJs}
    <form method="post" action="{url link='draw.add'}" onsubmit="{$sGetJsForm}" id="core_js_draw_form" enctype="multipart/form-data">
        <div class ="table">
            {if $bIsEdit}
            <div><input type="hidden" name="id" value="{$aForms.draw_id}" /></div>
            {/if}
            <div class ="table_left">
                <label for="title">{required}{phrase var='draw.title'}:</label>
            </div>
            <div class ="table_right">
                <input type="text" name="val[title]" value="{value type='input' id='title'}" id="title" size="60" />
            </div>
        </div>
        <div class="table">
            <div class="table_left">
                <label for="text">{phrase var='draw.draw_description'}:</label>
            </div>
            <div class="table_right">
            </div>
        </div>
        {editor id='description'}
        <div class ="table" >
            <div>
                <div class="table_left">
                    {phrase var='draw.privacy'}:
                </div>
                <div class ="table_right">
                    {module name='privacy.form' privacy_name='privacy' privacy_info='draw.control_who_can_see_this_draw' default_privacy='draw.default_privacy_setting'}
                </div>
            </div>
            <div>
                <div class="table_left">
                    {phrase var='draw.comment_privacy'}:
                </div>
                <div class ="table_right">
                    {module name='privacy.form' privacy_name='privacy_comment' privacy_info='draw.control_who_can_comment_on_this_draw' privacy_no_custom=true}
                </div>
            </div>
        </div>
        <div class="table_clear">
            <ul class="table_clear_button">
                <li><input type ="submit" class="button" value="{if $bIsEdit}{phrase var='draw.update'}{else}{phrase var='draw.publish'}{/if}" name="val[{if $bIsEdit}update{else}publish{/if}]" id="submit"></li>
            </ul>
            <div class="clear"></div>
        </div>
         <div class ="table">
            <div class ="table_left">
                <label for="text">{required}{phrase var='draw.draw_image'}:</label>
            </div>
            {if $bIsEdit}
                <img src="{$aForms.image_link}" > 
            {/if}
        </div>
        {if Phpfox::getParam('core.display_required')}
        <div class="table_clear">
            {required} {phrase var='core.required_fields'}
        </div>
        {/if}
    </form>
</div>