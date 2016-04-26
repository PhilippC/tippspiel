<?php

/**

Diese Klasse liefert einen Countdown bis zum Eröffnungsspiel. Dazu muss UI->OutputHeader mit true als zweitem
Parameter aufgerufen werden.


**/

class CUIWeb1Countdown extends CUIWeb1Component
{
  public function Output()
  {
  echo '
   <p align="center">
   <span id="countdownbox"> </span>  </p>  ';
  }
}