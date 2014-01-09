<?php 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if (Phpfox::getUserParam('draw.edit_own_draw') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('draw.edit_user_draw')}
        <li><a href="{url link="draw.add" id=""$aItem.draw_id""}">{phrase var='core.edit'}</a></li>
{/if}
{if (Phpfox::getUserParam('draw.delete_own_draw') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('draw.delete_user_draw')}
        <li class="item_delete"><a href="{url link="draw.delete" id=""$aItem.draw_id""}" class="no_ajax_link" onclick="return confirm('{phrase var='draw.are_you_sure_you_want_to_delete_this_draw' phpfox_squote=true}');" title="{phrase var='draw.delete_draw'}">{phrase var='core.delete'}</a></li>
{/if}