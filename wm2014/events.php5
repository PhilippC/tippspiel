<?php
  require_once('./script/UIWeb1Classes.inc.php5');
  require_once('./script/TippspielClasses.inc.php5');


  require_once('./script/UIFactory.inc.php5');
 $TippSpielUI = createWebUi1();

  $TippSpielUI->OutputHeader(TIPPSPIEL_NAME." - Events");

  require("mychurch/events.htmlsnippet");

  $TippSpielUI->OutputFooter();

?>