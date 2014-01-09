<?php

defined('PHPFOX') or exit('NO DICE!');
class Draw_Component_Controller_Add extends Phpfox_Component {

    public function process() {
        Phpfox::isUser(true);
        $bIsEdit = false;
        $bCanEditPersonalData = true;
        $this->template()->setTitle(Phpfox::getPhrase('draw.add_a_new_draw'));
        $this->template()->setBreadcrumb(Phpfox::getPhrase('draw.draws'), $this->url()->makeUrl('draw'));

        if (($iEditId = $this->request()->getInt('id'))) {
            $oDraw = Phpfox::getService('draw');

            $aRow = $oDraw->getDrawForEdit($iEditId);
            if ($aRow['is_approved'] != '1' &&
                    ($aRow['user_id'] != Phpfox::getUserId() && !Phpfox::getUserParam('draw.edit_user_draw'))) {
                return Phpfox_Error::display(Phpfox::getPhrase('draw.unable_to_edit_this_draw'));
            }

            if (Phpfox::isModule('tag')) {
                $aTags = Phpfox::getService('tag')->getTagsById('draw', $aRow['draw_id']);
                if (isset($aTags[$aRow['draw_id']])) {
                    $aRow['tag_list'] = '';
                    foreach ($aTags[$aRow['draw_id']] as $aTag) {
                        $aRow['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                    }
                    $aRow['tag_list'] = trim(trim($aRow['tag_list'], ','));
                }
            }

            (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getUserParam('draw.edit_own_draw', true) : Phpfox::getUserParam('draw.edit_user_draw', true));
            if (Phpfox::getUserParam('draw.edit_user_draw') && Phpfox::getUserId() != $aRow['user_id']) {
                $bCanEditPersonalData = false;
            }

            $bIsEdit = true;
            $this->template()->assign(array(
                'aForms' => $aRow
                    )
            );

            if (!empty($aRow['module_id'])) {
                $sModule = $aRow['module_id'];
                $iItemId = $aRow['item_id'];
            }
        } else {
            Phpfox::getUserParam('draw.add_new_draw', true);
        }
        if ($bIsEdit) {
            $aValidation = array(
                'title' => array(
                    'def' => 'required',
                    'title' => Phpfox::getPhrase('draw.fill_title_for_draw')
                ));
        } else {
            $aValidation = array(
                'title' => array(
                    'def' => 'required',
                    'title' => Phpfox::getPhrase('draw.fill_title_for_draw')
                ),
                'image' => array(
                    'def' => 'required',
                    'title' => Phpfox::getPhrase('draw.add_content_to_draw')
                )
            );
        }
        $oValid = Phpfox::getLib('validator')->set(array(
            'sFormName' => 'core_js_draw_form',
            'aParams' => $aValidation
                )
        );
        if (!empty($sModule) && Phpfox::hasCallback($sModule, 'getItem')) {
            $aCallback = Phpfox::callback($sModule . '.getItem', $iItemId);
            $sUrl = $sCrumb = '';

            if ($bIsEdit) {
                $sUrl = $this->url()->makeUrl('draw', array('draw', 'id' => $iEditId));
                $sCrumb = Phpfox::getPhrase('draw.editing_draw') . ': ' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getService('core')->getEditTitleSize(), '...');
            } else {
                $sUrl = $this->url()->makeUrl('draw', array('add', 'module' => $aCallback['module'], 'item' => $iItemId));
                $sCrumb = Phpfox::getPhrase('draw.adding_a_new_draw');
            }

            $this->template()
                    ->setBreadcrumb(Phpfox::getPhrase($sModule . '.' . $sModule), $this->url()->makeUrl($sModule))
                    ->setBreadCrumb($aCallback['title'], Phpfox::permalink($sModule, $iItemId))
                    ->setBreadCrumb(Phpfox::getPhrase('draw.draws'), $this->url()->makeUrl('pages', array($iItemId, 'draw')))
                    ->setBreadcrumb($sCrumb, $sUrl, true)
            ;
        } else {
            $this->template()
                    //->setBreadcrumb(Phpfox::getPhrase('draw.add_a_new_draw'))
                    ->setBreadcrumb((!empty($iEditId) ? Phpfox::getPhrase('draw.editing_draw') . ': ' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getService('core')->getEditTitleSize(), '...') : Phpfox::getPhrase('draw.adding_a_new_draw')), ($iEditId > 0 ? $this->url()->makeUrl('draw', array('add', 'id' => $iEditId)) : $this->url()->makeUrl('draw', array('add'))), true);
        }
        $this->template()->setHeader('cache', array(
                    //'draw.js' => 'module_draw',
                    //'colorpicker.js' => 'module_draw',
                    //'colorpicker.css' => 'module_draw',
                    'draw.css' => 'module_draw'
                ))
                ->assign(array(
                    'sCoreUrl' => phpfox::getParam('core.path')
        ));
        if ($aVals = $this->request()->getArray('val')) {
            if ($oValid->isValid($aVals)) {
                // Add the new draw
                if (isset($aVals['publish'])) {
                    $sMessage = Phpfox::getPhrase('draw.your_draw_has_been_added');

                    if (($iFlood = Phpfox::getUserParam('draw.flood_control_draw')) !== 0) {
                        $aFlood = array(
                            'action' => 'last_post', // The SPAM action
                            'params' => array(
                                'field' => 'time_stamp', // The time stamp field
                                'table' => Phpfox::getT('draw'), // Database table we plan to check
                                'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                                'time_stamp' => $iFlood * 60 // Seconds);	
                            )
                        );

                        // actually check if flooding
                        if (Phpfox::getLib('spam')->check($aFlood)) {
                            Phpfox_Error::set(Phpfox::getPhrase('draw.your_are_posting_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                        }
                    }

                    if (Phpfox_Error::isPassed()) {
                        $iId = Phpfox::getService('draw')->add($aVals);
                    }
                }
                // Update a draw
                if ((isset($aVals['update']))) {// && isset($aRow['draw_id']) && $bIsEdit) {
                    // Update the draw
                    $iId = Phpfox::getService('draw')->update($aRow['draw_id'], $aRow['user_id'], $aVals, $aRow);
                    $sMessage = Phpfox::getPhrase('draw.draw_updated');
                }

                if (isset($iId) && $iId) {
                    Phpfox::permalink('draw', $iId, $aVals['title'], true, $sMessage);
                }
            }
        }
        $this->template()
                ->setTitle((!empty($iEditId) ? Phpfox::getPhrase('draw.editing_draw') . ': ' . $aRow['title'] : Phpfox::getPhrase('draw.adding_a_new_draw')))
                ->setFullSite()
                ->assign(array(
                    'sCreateJs' => $oValid->createJS(),
                    'sGetJsForm' => $oValid->getJsForm(),
                    'bIsEdit' => $bIsEdit,
                    'bCanEditPersonalData' => $bCanEditPersonalData
                        )
                )
                ->setEditor(array('wysiwyg' => Phpfox::getUserParam('draw.can_use_editor_on_draw')))
                ->setHeader('cache', array(
                   // 'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                   // 'switch_legend.js' => 'static_script',
                   // 'switch_menu.js' => 'static_script',
                   // 'quick_edit.js' => 'static_script',
                   // 'pager.css' => 'style_css'
                        )
        );
    }

}

?>
