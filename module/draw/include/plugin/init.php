<?php
  if(phpfox::isModule('draw'))
  {
      $sPath =  phpfox::getParam('core.url_file') . 'draw/';
      phpfox::getLib('setting')->setParam('draw.url_photo',$sPath);
  }
  
?>
