

function dragwin()
{
 if (DragWin.container) DragWin.schliessen();
 document.getElementsByTagName("body")[0].appendChild(DragWin.erzeugen());
}

var DragWin = {

 container: null,		// DIV-Container
 mausX: 0,			// horizontale und ...
 mausY: 0,			// vertikale Mausposition
 div_breite: 300,		// Breite und ...
 div_hoehe: 150,			// Höhe des Containers
 rollX: 0,			// horizontale und ...
 rollY: 0,			// vertikale Scrollposition

 erzeugen: function() {
  // Der FuÃŸnoten-Layer wird bei jedem Aufruf neu erzeugt.
  // Alternativ kÃ¶nnte er mit den Stylesheet-Eigenschaften
  // visibility oder display aus- und eingeblendet werden.
  // erzeugt Div-Container
  DragWin.container = document.createElement("div");
  DragWin.container.id = "DragWin";
  DragWin.container.style.width = DragWin.div_breite + "px";
  DragWin.container.style.height = DragWin.div_hoehe + "px";

  var close_link = document.createElement("a");
  close_link.setAttribute("href", "javascript:DragWin.schliessen()");

  // Ereignis registrieren
  DragWin.container.onmousedown = DragWin.ziehen_vorbereiten;
  DragWin.container.style.cursor = "move";
  // zurückgeben
  return DragWin.container;
 },


 schliessen: function() {
  // löscht den Container
  document.getElementsByTagName("body")[0].removeChild(DragWin.container);
  DragWin.container = null;
 },

 ziehen_vorbereiten: function(ereignis) {
  // registriert die ziehen-Funktion fÃ¼r Mausbewegungen,
  // Abbruch bei Loslassen der Maustaste
  if (!ereignis) var ereignis = window.event;
  DragWin.position_ermitteln(ereignis);
  document.onmousemove = DragWin.ziehen;
  document.onmouseup = DragWin.stopp;
 },

 position_ermitteln: function(ereignis) {
  // ermittelt die Position des Mauszeigers
  // (Pixel von der linken oberen Fensterecke)
  if (!ereignis) var ereignis = window.event;
  DragWin.mausX = ereignis.clientX;
  DragWin.mausY = ereignis.clientY;
  DragWin.roll();
  // Safari rechnet clientX/Y vom Dokumentenanfang aus
  if (DragWin.mausX > DragWin.rollX && DragWin.rollX >= window.innerWidth) DragWin.mausX -= DragWin.rollX;
  if (DragWin.mausY > DragWin.rollY && DragWin.rollX >= window.innerHeight) DragWin.mausY -= DragWin.rollY;
 },

 roll: function() {
  // Gibt die horizontale oder vertikale Scroll-Verschiebung zurÃ¼ck
  if (isFinite(self.pageYOffset)) {	// DOM
   DragWin.rollX = self.pageXOffset;
   DragWin.rollY = self.pageYOffset;
  } else if (isFinite(document.documentElement && document.documentElement.scrollTop)) {	// IE neu
   DragWin.rollX = document.documentElement.scrollLeft;
   DragWin.rollY = document.documentElement.scrollTop;
  } else if (isFinite(document.body.scrollTop)) {	// IE alt
   DragWin.rollX = document.body.scrollLeft;
   DragWin.rollY = document.body.scrollTop;
  }
 },

 ziehen: function(ereignis) {
  // verschiebt den FuÃŸnoten-Container parallel zu Mausbewegungen
  if (!ereignis) var ereignis = window.event;
  // SicherheitsmaÃŸnahme: manche Browser kommen mit den Ereignissen
  // durcheinander, z.B. beim Scrollen in Ã¼berlangen FuÃŸnoten
  // (Safari) oder bei Maus-Hektik (IE); in diesem Fall lÃ¶st ein
  // Mausklick den am Zeiger klebenden Container.
  document.onmousedown = DragWin.stopp;
  // 1.) aktuelle Position ermitteln
  var kastenX = parseInt(DragWin.container.style.left.slice(0,-2));
  var kastenY = parseInt(DragWin.container.style.top.slice(0,-2));
  // 2.) alte Mausposition speichern
  var mausX_alt = DragWin.mausX;
  var mausY_alt = DragWin.mausY;
  // 3.) neue Mausposition ermitteln
  DragWin.position_ermitteln(ereignis);
  // 4.) um die Differenz verschieben
  DragWin.container.style.left = kastenX + DragWin.mausX - mausX_alt + "px";
  DragWin.container.style.top = kastenY + DragWin.mausY - mausY_alt + "px";
 },

 stopp: function() {
  // lÃ¶scht Event-Handler, lÃ¤sst FuÃŸnoten-Container los
  document.onmousemove = null;
  document.onmouseup = null;
  // gleicht SicherheitsmaÃŸnahme in DragWin.ziehen() aus
  if (document.onmousedown) document.onmousedown = DragWin.ziehen_vorbereiten;
 }

};

function klonen(quelle, ziel) {
 // Hilfsfunktion
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

