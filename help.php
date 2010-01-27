<?php 
include_once('config.php'); 
include_once('header.php');
include_once('searchform.php');
?>
<ul class="pageitem">
    <li class="textbox">
        <span class="header">Was ist das hier?</span>
        <p>
            Diese Seite ist ein Prototyp einer mobilen Weboberfläche für Bibliothekskataloge.
            Der Projektname ist <em>mocai</em> (MObile CAtalog Interface oder Mobile OpaC Interface).
        </p>
    </li>
    <li class="textbox">
        <span class="header">Wie funktioniert mocai?</span>
        <p>
            Die Oberfläche basiert auf <a href="http://iwebkit.net">iWebKit</a>.
            Die Suche wird über SRU an den <a href="http://gso.gbv.de/DB=2.1/">Verbundkatalog des GBV</a>
            weitergeleitet und die Ergebnisse im Dublin-Core-Format ausgewertet und angezeigt.
        </p>
    </li>
    <li class="textbox">
        <span class="header">Wieso haben einige Treffern keine Autoren oder Titel?</span>
        <p>
            Das liegt an der Dublin-Core-Ausgabe der SRU-Schnittstelle.
        </p>
    </li>
    <li class="textbox">
        <span class="header">Wann ist mocai fertig?</span>
        <p>
            Gar nicht, dies ist nur ein Prototyp als Designstudie! Der Quellcode steht
            unter der <a href="http://de.wikipedia.org/wiki/GNU_Affero_General_Public_License">Affero General Public License</a> (AGPL)
            <a href="mtest.tgz">zur Verfügung</a>, d.h. er kann weiterverwendet
            werden, muss aber dann auch unter der AGPL  veröffentlicht werden!
            Mocapi wird also nur dann Realität, wenn jeder, der seinen oder einen anderen
            Katalog als mobile Version anbieten, möchte, sich den Quellcode nimmt und
            mehr daraus macht.
        </p>
    </li>
    <li class="textbox">
        <span class="header">Was fehlt denn noch?</span>
        <p>
            Der Quellcode muss komplett <em>und sauber</em> neugeschrieben werden (sind allerdings weniger als 300 Zeilen).
            Es fehlt eine Auswahl der Bibliothek statt der ELN, die Anzeige von Bestandsdaten und möglichst
            aktuelle Verfügbarkeitsanzeige mit DAIA. Außerdem wäre ein dynamisches Nachladen von weiteren Treffern sinnvoll.
        </p>
    </li>
</li>
<?php include_once('footer.php'); ?>