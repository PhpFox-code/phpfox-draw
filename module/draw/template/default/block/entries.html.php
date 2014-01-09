<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aDraws)}
{template file='draw.block.css'}
<ul class="draw_block">
    {foreach from=$aDraws key=iKey item=aDraw}
    <li>
        <a href="{$aDraw.href}" class="draw_image">{img server_id=0 path='draw.url_photo' file=$aDraw.link suffix='_500' title=$aDraw.title}</a>
        <div class="draw_title"><a href="{$aDraw.href}">{$aDraw.title}</a></div>
        <div class="draw_user_info">{phrase var='draw.by_user' full_name=$aDraw|user:'':'':50:'':'author'}</div>
    </li>
    {/foreach}
</ul>
{/if}