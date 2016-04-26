      //Konfiguration:
      var ImageDir = "bilder/countdown/blau/";
      // Ziel-Datum in MEZ
      var jahr=2014, monat=6, tag=12, stunde=22, minute=00, sekunde=00;
      var zielDatum=new Date(jahr,monat-1,tag,stunde,minute,sekunde);

      //Intern:
      var _DigitImgs;
      var _TextImgs;

      function countdown()
      {
        _loadImages();
        _countdownLoop(true);
      }

      function _loadImages()
      {
         _DigitImgs = new Array(10);
	for(i = 0; i < 10; i++)
         {
	  _DigitImgs[i]	= new Image();
	  _DigitImgs[i].src = ImageDir + i + '.gif';
	}
         _TextImgs = new Array(3);
         _TextImgs[0] = new Image();
         _TextImgs[0].src = ImageDir+"dp.gif";
         _TextImgs[1] = new Image();
         _TextImgs[1].src = ImageDir+"tag.gif";
         _TextImgs[2] = new Image();
         _TextImgs[2].src = ImageDir+"tage.gif";

      }

      function _countdownLoop(firstcall)
      {

        startDatum=new Date(); // Aktuelles Datum

        // Countdown berechnen und anzeigen, bis Ziel-Datum erreicht ist
        if(startDatum<=zielDatum)  {

          var tage=0, stunden=0, minuten=0, sekunden=0;

          // Tage
          restTage=Math.floor((zielDatum-startDatum)/(24*60*60*1000));
          startDatum.setTime(startDatum.getTime()+restTage*24*60*60*1000);
          tage+=restTage;

          // Stunden
          stunden=Math.floor((zielDatum-startDatum)/(60*60*1000));
          startDatum.setTime(startDatum.getTime()+stunden*60*60*1000);

          // Minuten
          minuten=Math.floor((zielDatum-startDatum)/(60*1000));
          startDatum.setTime(startDatum.getTime()+minuten*60*1000);

          // Sekunden
          sekunden=Math.floor((zielDatum-startDatum)/1000);

          //Verbleibende Zeit ausgeben:
          if (firstcall)
            _outputFirstCall(tage,stunden,minuten, sekunden);
          else
            _output(tage,stunden,minuten, sekunden);


          //Diese Funktion bald wieder aufrufen:
          setTimeout('_countdownLoop(false)',1000);
        }
        // Anderenfalls alles auf Null setzen
        else _outputFirstCall(0,0,0,0);
      }

      function _outputFirstCall(tage, stunden, minuten, sekunden)
      {
	 var InnerHTML = '<table bgcolor="#181818" cellspacing="16">'+
          '<tr><td align="center" colspan="3"> <font color="white">Noch</font></td></tr><tr><td>&nbsp;</td><td bgcolor="#000000" align="center">';

          InnerHTML+= _ErzeugeZahlHTML(tage, "Tage",2);

          InnerHTML += "&nbsp;&nbsp;&nbsp;";


          if (tage==1)
            InnerHTML += '<img src="' + _TextImgs[1].src+'" border="0" name="CD_Tage">';
          else
            InnerHTML += '<img src="' + _TextImgs[2].src+'" border="0" name="CD_Tage">';

          InnerHTML += "<br />";

          InnerHTML+= _ErzeugeZahlHTML(stunden, "Stunden",2);
	 InnerHTML += '<img src="' + _TextImgs[0].src+'" border="0">'; //Doppelpunkt

          InnerHTML+= _ErzeugeZahlHTML(minuten, "Minuten",2);
	 InnerHTML += '<img src="' + _TextImgs[0].src+'" border="0">'; //Doppelpunkt

          InnerHTML+= _ErzeugeZahlHTML(sekunden, "Sekunden",2);

          InnerHTML+='</td><td>&nbsp;</td><tr><td align="center" colspan="3"> <font color="white">bis zum Anpfiff des Eröffnungsspiels!</font></td></tr></table>';


          document.getElementById("countdownbox").innerHTML = InnerHTML;
      }

      function _output(tage,stunden,minuten, sekunden)
      {
        _SetzeZahl(tage, "Tage",2);

        if (tage==1)
          document.images["CD_Tage"].src=_TextImgs[1].src;
        else
	 document.images["CD_Tage"].src=_TextImgs[2].src;


        _SetzeZahl(stunden, "Stunden",2);
        _SetzeZahl(minuten, "Minuten",2);
        _SetzeZahl(sekunden, "Sekunden",2);




      }

      function _ErzeugeZahlHTML(Zahl, Name, AnzDigits)
      {
        var strZahl = String(Zahl);
        while (strZahl.length<AnzDigits)
          strZahl="0"+strZahl;
        var res = "";
        for(i = 0; i < strZahl.length; i++)
        {
          ImgNummer = strZahl.substr(i, 1);
	 res +=	'<img src="' + _DigitImgs[ImgNummer].src+'" border="0" id="CD_'+Name+i+'">';
        }

        return res;

      }

      function _SetzeZahl(Zahl, Name, AnzDigits)
      {
        var strZahl = String(Zahl);
        while (strZahl.length<AnzDigits)
          strZahl="0"+strZahl;
        var res = "";
        for(i = 0; i < strZahl.length; i++)
        {
          ImgNummer = strZahl.substr(i, 1);
          ImgName = "CD_"+Name+i;

          document.images[ImgName].src=_DigitImgs[ImgNummer].src;

        }


      }