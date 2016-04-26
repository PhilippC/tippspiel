<?php

/**

Diese Klasse liefert einen "Advertisement-Block"


**/
require_once("UIWeb1Classes.inc.php5");

class CUIWeb1Advertisement extends CUIWeb1Component
{
  public function Output()
  {
  	include('mychurch/advertBox.htmlsnippet');
  }
}

?>