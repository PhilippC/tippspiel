function init() {
 // wird beim Laden aufgerufen
 // registriert fÃ¼r alle sup-Tags, die nur Ziffern
 // enthalten, ein Klick-Ereignis
 var sups = document.getElementsByTagName("sup");
 if (!sups) return false;
 for (var i = 0; i < sups.length; i++) {
  // sup enthÃ¤lt die FuÃŸnotennummer
  var sup = parseInt(sups[i].firstChild.nodeValue);
  if (sup != null && !isNaN(sup)) {
   // Beim Registrieren als Ereignis lassen sich keine
   // Argumente Ã¼bergeben -> das Skript erzeugt fÃ¼r jedes
   // Ereignis eine Funktion
   sups[i].onclick = new Function("ereignis", "FN.position_ermitteln(ereignis); fussnote(" + sup + ");");
  }
 }
}

function fussnote(nr) {
 // erzeugt den FuÃŸnoten-Container und holt die Inhalte
 if (FN.container) FN.schliessen();
 document.getElementsByTagName("body")[0].appendChild(FN.erzeugen());
 FN.fussnotenueber.nodeValue = FN.fussnotenvorspann + nr;
 FN.holen(nr);
}

// FuÃŸnoten-Layer-Objekt
var FN = {
 // Die FuÃŸnote soll von auÃŸen zugÃ¤nglich sein:
 container: null,		// DIV-Container
 fussnotenvorspann: "FuÃŸnote ",	// Ãœberschrift-Vorspann
 fussnotenueber: null,		// Ãœberschriftentext
 fussnotenabsatz: null,		// Absatz fÃ¼r Fussnotentext
 ajax: false,			// XMLHttpRequest-Objekt
 mausX: 0,			// horizontale und ...
 mausY: 0,			// vertikale Mausposition
 div_breite: 300,		// Breite und ...
 div_hoehe: 150,			// HÃ¶he des FuÃŸnoten-Containers
 abstandX: 10,			// horizontaler und ...
 abstandY: 10,			// vertikaler Abstand von Mauszeiger
 rollX: 0,			// horizontale und ...
 rollY: 0,			// vertikale Scrollposition

 erzeugen: function() {
  // Der FuÃŸnoten-Layer wird bei jedem Aufruf neu erzeugt.
  // Alternativ kÃ¶nnte er mit den Stylesheet-Eigenschaften
  // visibility oder display aus- und eingeblendet werden.
  // erzeugt Div-Container
  FN.container = document.createElement("div");
  FN.container.id = "fussnoten";
  FN.container.style.width = FN.div_breite + "px";
  FN.container.style.height = FN.div_hoehe + "px";
  // Positionieren, abhÃ¤ngig von Mauszeiger und Browserfenster
  // Der Kasten soll rechts vom Mauszeiger erscheinen, wenn dort
  // noch Platz ist oder links kein Platz ist
  var pos_x = (FN.mausX + FN.abstandX + FN.div_breite < window.innerWidth || FN.div_breite + FN.abstandX > FN.mausX)?
   FN.mausX + FN.abstandX :	// rechts von Mauszeiger
   FN.mausX - FN.div_breite - FN.abstandX;	// links
  // Der Kasten soll Ã¼ber dem Mauszeiger erschein, wenn Platz ist
  var pos_y = (FN.div_hoehe + FN.abstandY > FN.mausY)?
   FN.mausY + FN.abstandY :	// unter Mauszeiger
   FN.mausY - FN.div_hoehe - FN.abstandY;	// darÃ¼ber
  //alert('roll: '+FN.rollX + ' ' + FN.rollY);// + "\npos: "+ pos_x + ' ' + pos_y + "\nmaus: "+ FN.mausX + ' ' + FN.mausY);
  FN.container.style.left = FN.rollX + pos_x + "px";
  FN.container.style.top = FN.rollY + pos_y + "px";
  // FuÃŸnoten-Ãœberschrift
  var fn_h = document.createElement("h1");
  FN.fussnotenueber = document.createTextNode(FN.fussnotenvorspann);
  fn_h.appendChild(FN.fussnotenueber);
  // Link zum SchlieÃŸen
  var fn_link = document.createElement("a");
  fn_link.setAttribute("href", "javascript:FN.schliessen()");
  // Absatz fÃ¼r FuÃŸnotentext vorbereiten
  FN.fussnotenabsatz = document.createElement("p");
  // zusammensetzen
  fn_h.appendChild(fn_link);
  FN.container.appendChild(fn_h);
  FN.container.appendChild(FN.fussnotenabsatz);
  // Ereignis registrieren
  FN.container.onmousedown = FN.ziehen_vorbereiten;
  FN.container.style.cursor = "move";
  // zurÃ¼ckgeben
  return FN.container;
 },

 holen: function(nr) {
  // Ajax-Verbindung herstellen
  try {			// W3C-Standard
   FN.ajax = new XMLHttpRequest();
  } catch(w3c) {
   try {			// Internet Explorer
    FN.ajax = new ActiveXObject("Msxml2.XMLHTTP");
   } catch(msie) {
    try {		// Internet Explorer alt
     FN.ajax = new ActiveXObject("Microsoft.XMLHTTP");
    } catch(msie_alt) {
     alert("Ihr Browser kann keine FuÃŸnoten anzeigen.");
     return false;	// !!! Link auf XML-Dokument
    }
   }
  }
  // Datei anfordern (asynchron)
  FN.ajax.open('GET', 'fussnoten.xml', true);
  FN.ajax.setRequestHeader('Content-Type', 'text/xml');
  // umgeht Internet Explorers Caching von GET-Anfragen
  FN.ajax.setRequestHeader('If-Modified-Since', 'Sat, 1 Jan 2000 00:00:00 GMT');
  FN.ajax.send(null);
  // nach Status-Ã„nderungen der Verbindung
  // werden die empfangenen Inhalte geparst
  FN.ajax.onreadystatechange = function() {

    return false;
   }
  }
 },

 schliessen: function() {
  // lÃ¶scht den FuÃŸnoten-Container
  document.getElementsByTagName("body")[0].removeChild(FN.container);
  FN.container = null;
 },

 ziehen_vorbereiten: function(ereignis) {
  // registriert die ziehen-Funktion fÃ¼r Mausbewegungen,
  // Abbruch bei Loslassen der Maustaste
  if (!ereignis) var ereignis = window.event;
  FN.position_ermitteln(ereignis);
  document.onmousemove = FN.ziehen;
  document.onmouseup = FN.stopp;
 },

 position_ermitteln: function(ereignis) {
  // ermittelt die Position des Mauszeigers
  // (Pixel von der linken oberen Fensterecke)
  if (!ereignis) var ereignis = window.event;
  FN.mausX = ereignis.clientX;
  FN.mausY = ereignis.clientY;
  FN.roll();
  // Safari rechnet clientX/Y vom Dokumentenanfang aus
  if (FN.mausX > FN.rollX && FN.rollX >= window.innerWidth) FN.mausX -= FN.rollX;
  if (FN.mausY > FN.rollY && FN.rollX >= window.innerHeight) FN.mausY -= FN.rollY;
 },

 roll: function() {
  // Gibt die horizontale oder vertikale Scroll-Verschiebung zurÃ¼ck
  if (isFinite(self.pageYOffset)) {	// DOM
   FN.rollX = self.pageXOffset;
   FN.rollY = self.pageYOffset;
  } else if (isFinite(document.documentElement && document.documentElement.scrollTop)) {	// IE neu
   FN.rollX = document.documentElement.scrollLeft;
   FN.rollY = document.documentElement.scrollTop;
  } else if (isFinite(document.body.scrollTop)) {	// IE alt
   FN.rollX = document.body.scrollLeft;
   FN.rollY = document.body.scrollTop;
  }
 },

 ziehen: function(ereignis) {
  // verschiebt den FuÃŸnoten-Container parallel zu Mausbewegungen
  if (!ereignis) var ereignis = window.event;
  // SicherheitsmaÃŸnahme: manche Browser kommen mit den Ereignissen
  // durcheinander, z.B. beim Scrollen in Ã¼berlangen FuÃŸnoten
  // (Safari) oder bei Maus-Hektik (IE); in diesem Fall lÃ¶st ein
  // Mausklick den am Zeiger klebenden Container.
  document.onmousedown = FN.stopp;
  // 1.) aktuelle Position ermitteln
  var kastenX = parseInt(FN.container.style.left.slice(0,-2));
  var kastenY = parseInt(FN.container.style.top.slice(0,-2));
  // 2.) alte Mausposition speichern
  var mausX_alt = FN.mausX;
  var mausY_alt = FN.mausY;
  // 3.) neue Mausposition ermitteln
  FN.position_ermitteln(ereignis);
  // 4.) um die Differenz verschieben
  FN.container.style.left = kastenX + FN.mausX - mausX_alt + "px";
  FN.container.style.top = kastenY + FN.mausY - mausY_alt + "px";
 },

 stopp: function() {
  // lÃ¶scht Event-Handler, lÃ¤sst FuÃŸnoten-Container los
  document.onmousemove = null;
  document.onmouseup = null;
  // gleicht SicherheitsmaÃŸnahme in FN.ziehen() aus
  if (document.onmousedown) document.onmousedown = FN.ziehen_vorbereiten;
 }

};

function klonen(quelle, ziel) {
 // Hilfsfunktion fÃ¼r FuÃŸnoten-Darstellung im DOM
 // Einfaches cloneNode() funktioniert nicht bei komplexen
 // Gebilden; eine Schleife baut den Teilbaum nach.
 for(var i = 0; i < quelle.childNodes.length; i++) {
  var knoten = quelle.childNodes[i];
  switch (knoten.nodeType) {
   case 1:	// Elementknoten
    var neu = ziel.appendChild(document.createElement(knoten.nodeName));
    for (var j = 0; j < knoten.attributes.length; j++) {
     neu.setAttribute(knoten.attributes[j].nodeName, knoten.attributes[j].nodeValue);
    }
    klonen(knoten, neu);
    break;
   case 3:	// Textknoten
    subknoten = document.createTextNode(knoten.nodeValue);
    ziel.appendChild(subknoten);
   // andere Knotentypen sind nicht relevant
  }
 }
}

// Aufruf der init-Funktion beim Laden
window.onload = init;