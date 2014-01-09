<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<script type="text/javascript" src="{param var='core.path'}module/draw/static/jscript/pop_up.js"></script>
<script type="text/javascript" src="{param var='core.path'}module/draw/static/jscript/colorpicker.js"></script>
<link rel="stylesheet" type="text/css" href="{param var='core.path'}module/draw/static/css/default/default/colorpicker.css"/>
<link rel="stylesheet" type="text/css" href="{param var='core.path'}module/draw/static/css/default/default/draw.css"/>
<style type="text/css">
    .tool_bar{l}
    background: url({$sCoreUrl}module/draw/static/image/default/default/bar.png);
    {r}
    #draw_slider_holder .size{l}
    background: url({$sCoreUrl}module/draw/static/image/default/default/sep.png) no-repeat;
    {r}
    #draw_slider_holder .tool,
    #draw_slider_holder .clear_paint{l}
    background: url({$sCoreUrl}module/draw/static/image/default/default/sep.png) no-repeat;
    {r}
    .draw_close{l}
    background: url({$sCoreUrl}module/draw/static/image/default/default/close.png);
    {r}
    .draw_close:hover{l}
    background: url({$sCoreUrl}module/draw/static/image/default/default/close_hightlight.png);
    {r}
    .image1{l}
    background: url({$sCoreUrl}module/draw/static/image/default/default/pick.png);
    {r}
    .image2{l}
    background: url({$sCoreUrl}module/draw/static/image/default/default/size.png);
    {r}
    .image3{l}
    background: url({$sCoreUrl}module/draw/static/image/default/default/e.png);
    {r}
    .image4{l}
    background: url({$sCoreUrl}module/draw/static/image/default/default/close2.png) no-repeat center center;
    {r}
     .image5{l}
    background: url({$sCoreUrl}module/draw/static/image/default/default/save.png) no-repeat center center;
    {r}
    .image6{l}
    background: url({$sCoreUrl}module/draw/static/image/default/default/edit-clear.png) no-repeat center center;
    {r}
    #drawpop{l}
    background: url({$sCoreUrl}module/draw/static/image/default/default/button.png);
    {r}
    .sel select, #draw_slider_holder .sel select {l}
    	background: #007BBF url({$sCoreUrl}module/draw/static/image/default/default/bar.png) repeat;
    {r}
    .colorpicker {l}background: url({$sCoreUrl}module/draw/static/image/default/default/colorpicker_background.png);{r}
    .colorpicker_color div  {l}background: url({$sCoreUrl}module/draw/static/image/default/default/colorpicker_overlay.png);{r}
    .colorpicker_color div div {l}background: url({$sCoreUrl}module/draw/static/image/default/default/colorpicker_select.gif);{r}
    .colorpicker_hue div {l}background: url({$sCoreUrl}module/draw/static/image/default/default/colorpicker_indic.gif);{r}
    .colorpicker_hex {l}background: url({$sCoreUrl}module/draw/static/image/default/default/colorpicker_hex.png);{r}
    .colorpicker_rgb_r {l}background: url({$sCoreUrl}module/draw/static/image/default/default/colorpicker_rgb_r.png);{r}
    .colorpicker_rgb_g {l}background: url({$sCoreUrl}module/draw/static/image/default/default/colorpicker_rgb_g.png);{r}
    .colorpicker_rgb_b {l}background: url({$sCoreUrl}module/draw/static/image/default/default/colorpicker_rgb_b.png);{r}
    .colorpicker_hsb_h {l}background: url({$sCoreUrl}module/draw/static/image/default/default/colorpicker_hsb_h.png);{r}
    .colorpicker_hsb_s {l}background: url({$sCoreUrl}module/draw/static/image/default/default/colorpicker_hsb_s.png);{r}
    .colorpicker_hsb_b {l}background: url({$sCoreUrl}module/draw/static/image/default/default/colorpicker_hsb_b.png);{r}
    .colorpicker_submit {l}background: url({$sCoreUrl}module/draw/static/image/default/default/colorpicker_submit.png);{r}
    #canvasDraw{l}
        cursor: url({$sCoreUrl}module/draw/static/image/default/default/pen.png),default;
    {r}
     #canvasDraw.erase{l}
        cursor: url({$sCoreUrl}module/draw/static/image/default/default/e.png),default;
    {r}
</style>
<script type="text/javascript">
	var drawingWidthPanel = {php}echo phpfox::getParam('draw.default_width_of_slide_paint');{/php};
</script>     
<div id="drawpop">
</div>
<form method="post" action="{url link='draw.add'}" onsubmit="return false();" id="drawing_submit_form">
<div id="draw_slider_holder" style="width: {php}echo phpfox::getParam('draw.default_width_of_slide_paint');{/php}px;right:-{php}echo phpfox::getParam('draw.default_width_of_slide_paint');{/php}px;">
	<div class="holder_content_description" style="display: none;width: {php}echo phpfox::getParam('draw.default_width_of_slide_paint');{/php}px;">
		<div class="holder_content_description_item">
		<div class ="table">
            <div class ="table_left">
                <label for="title">{required}{phrase var='draw.title'}:</label>
            </div>
            <div class ="table_right">
                <input type="text" name="val[title]" value="{value type='input' id='title'}" id="title" size="60" />
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
            <div class ="table privacy_drawing_setting" >
                <div>
                    <div class="table_left">
                        {phrase var='draw.privacy'}:
                    </div>
                    <div class ="table_right">
                        {module name='privacy.form' privacy_name='privacy' privacy_info='draw.control_who_can_see_this_draw' default_privacy='draw.default_privacy_setting' privacy_type="normal"}
                    </div>
                    <div class="clear"></div>
                </div>
                <div>
                    <div class="table_left">
                        {phrase var='draw.comment_privacy'}:
                    </div>
                    <div class ="table_right">
                        {module name='privacy.form' privacy_name='privacy_comment' privacy_info='draw.control_who_can_comment_on_this_draw' privacy_no_custom=true privacy_type="normal"}
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="table_clear">
                <ul class="table_clear_button">
                    <li>
                        <span id="submit_drawing_submit">
                            <input type ="submit" class="button" value="{phrase var='draw.publish'}" name="val[publish]" id="submit_drawing_input">
                        </span>
                        
                    </li>
                     <li>
                        <span id="submit_drawing_submit">
                            <input type ="submit" class="button" value="{phrase var='draw.hide'}" name="val[hide]" id="hide_drawing_input" onclick="return false;">
                        </span>
                        
                    </li>
                    <li>
                        <span id="submit_drawing_loading">{img theme='ajax/add.gif'}</span>
                    </li>
                    <li>
                        <span id="submit_drawing_message"></span>
                    </li>
                </ul>
                <div class="clear"></div>
            </div>
        </div>
        </div>
	</div>
	<div class ="table">
        <div class="tool_bar" style="width: {php}echo phpfox::getParam('draw.default_width_of_slide_paint');{/php}px;">
            <div class="color_picker">
                <div class="image1"></div>
                <input type="hidden" class="getColor" id="color" value="#FF0000">
            </div>
            <div class="size">
                <div class="line">
                    <div class="sel">
                        <label class="cus_sel">
                            <select id="draw_size">
                                {php}
                                    for ($i = 1; $i <= phpfox::getParam('draw.limited_draw_pixel'); $i++){
                                        echo "<option value='".$i."'>".$i."px</option>";
                                    };
                                {/php}
                            </select>
                        </label>
                    </div>
                    <div class="image2" onclick="return $('#draw_size').click();"></div>
                </div>
            </div>
            <div class="tool">
                <div class="image3">
                </div>
            </div>
            <div class="clear_paint">
                <div class="image6">
                </div>
            </div>
            {*<div class="tool_right">
            	 {module name='privacy.form' privacy_name='privacy' privacy_info='draw.control_who_can_see_this_draw' default_privacy='draw.default_privacy_setting'  privacy_type='mini'}
            </div>*}
            <div class="tool_save">
            	<div class="image5"></div>
            </div>
            <div class="tool_close">
            	<div class="image4"></div>
            </div>
        </div>
        <div class="table_right" id="canvasID">
            <canvas id="canvasDraw" height="350" width="{php}echo phpfox::getParam('draw.default_width_of_slide_paint'){/php}"></canvas>
            <input type="hidden" name="val[image]" id="png"/>
        </div>
        </div>
</div>
</form>          