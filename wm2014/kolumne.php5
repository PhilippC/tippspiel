<?php
  require_once("./script/TippSpielUIWeb1.class.php5");
  require_once("./script/UIWeb1Kolumne.class.php5");


  require_once('./script/UIFactory.inc.php5');
 $TippSpielUI = createWebUi1();

  $TippSpielUI->OutputHeader("Die Kolumne zum Tippspiel");

   $Kolumne = new CUIWeb1Kolumne();
   $Kolumne->Output(true);
?>

<?php $TippSpielUI->OutputFooter(); ?>