<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<form method="post" action="{url link='draw.edit'}">
    <div class ="table">
        <div class ="table_left">
            <label for="title">{required}{phrase var='draw.title'}:</label>
        </div>
        <div class ="table_right">
            <input type="text" name="val[title]" value="" id="title" size="60" />
        </div>
    </div>
    <div class ="table">
        <div class ="table_left">
            <label for="text">{required}{phrase var='draw.draw_image'}:</label>
        </div>
        <div class="table_right">
        </div>
   </div>
    <div class="table">
        <div class="table_left">
            <label for="text">{phrase var='draw.draw_description'}:</label>
        </div>
        <div class="table_right">
            {editor id='description'}
            <!--<textarea name="val[description]" value="" id="description" rows="5" cols="80" /></textarea>-->
        </div>
    </div>
    <div class ="table">
        <div class="table_left">
            {phrase var='draw.privacy'}:
        </div>
        <div class ="table_right">
            {module name='privacy.form' privacy_name='privacy' privacy_info='draw.control_who_can_see_this_draw' default_privacy='draw.default_privacy_setting'}
        </div>
    </div>
    <div class="table">
        <div class="table_left">
            {phrase var='draw.comment_privacy'}:
        </div>
        <div class ="table_right">
            {module name='privacy.form' privacy_name='privacy_comment' privacy_info='draw.control_who_can_comment_on_this_draw' privacy_no_custom=true}
        </div>
    </div>
    <div class="table_clear">
        <ul class="table_clear_button">
            <li><input type ="submit" class="button" value="{phrase var='draw.publish'}" name="val[publish]" id="submit"></li>
        </ul>
        <div class="clear"></div>
    </div>
    {if Phpfox::getParam('core.display_required')}
    <div class="table_clear">
        {required} {phrase var='core.required_fields'}
    </div>
    {/if}
</form>
