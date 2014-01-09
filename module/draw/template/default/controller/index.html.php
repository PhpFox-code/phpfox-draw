<?php
defined('PHPFOX') or exit('NO DICE!'); 
?>
{literal}
<style type="text/css">
    .drawing_item_list .item_view_content .draw_image{
        float:left;
        width:150px;
        margin-right:7px;
    }
    .drawing_item_list .item_view_content .extra_info{
        float:left;
    }
</style>
{/literal}
{if !count($aItems)}
<div class="extra_info">
	{phrase var='draw.no_draws_found'}
</div>
{else}

{foreach from=$aItems name=draw item=aItem}
    {template file='draw.block.entry'}
{/foreach}
{if Phpfox::getUserParam('draw.delete_user_draw')}
{moderation}
{/if}
{unset var=$aItems}
{pager}
{/if}
