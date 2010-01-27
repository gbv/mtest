<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php

# mopaci - Einstellungen
$mVersion    = "0.01c";
$mSRUServer  = "http://gso.gbv.de/sru/DB=2.1";
$mSRUIndices = array(
    '' => 'nach allen Angaben',
    'pica.per' => 'nach Person',
    'pica.tit' => 'nach Titel',
    'pica.slw' => 'nach Schlagwort',
    'pica.num' => 'nach ISBN o.Ä.',
);
$mDefaultSRUIndex = 'pica.all'; # '' => 'pica.all' if addtoquery is set
$mIDIndex    = "pica.ppn";
$mDAIAServer = ""; # TODO
$mTitle      = "GVK Mobile beta";

?><head>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="css/uncompressed_style.css" rel="stylesheet" type="text/css" />
<script src="javascript/functions.js" type="text/javascript"></script>

<title><?php print htmlspecialchars($mTitle); ?></title>
<!--meta content="keyword1,keyword2,keyword3" name="keywords" />
<meta content="Description of your site" name="description" /-->
</head>
<?php

$gbveki = @$_GET["gbveki"];
if ($gbveki) {
    $addtoquery = 'pica.bib="'.str_replace('"','\"',$gbveki).'"';
}

$query = @$_GET["q"];
if (get_magic_quotes_gpc()) $query = stripslashes($query);
$action = @$_GET["a"];
$searchindex = @$_GET["i"];
$getitem = @$_GET["g"];
if (get_magic_quotes_gpc()) $getitem = stripslashes($getitem);
$page = @$_GET['p'];

$resultlist = array();

#$query ="Hallo"; $action ="A"; $page = 'catalog';

function SRUQuery($query, $schema, $index) {
    global $mSRUIndices, $mSRUServer, $addtoquery;
    if ($index && ($mSRUIndices[$index] || $inde == $mIDIndex)) {
        $query = sprintf('%s="%s"', $index, str_replace('"','\"',$query));
    } elseif ($addtoquery) {
        $query = sprintf('%s="%s"', $mDefaultSRUIndex, str_replace('"','\"',$query));
    }
    if ($addtoquery) $query .= " and $addtoquery";

    $sru_params = array(
        "query" => $query,
        "version" => "1.1",
        "operation" => "searchRetrieve",
        "recordSchema" => $schema,
        "maximumRecords" => "30",
        "startRecord" => 1,
        "sortKeys" => "none"
    );
    $url = $mSRUServer . "?" . http_build_query($sru_params);
    $xml = simplexml_load_file($url);
    $xml->registerXPathNamespace("srw","http://www.loc.gov/zing/srw/");
    $xml->registerXPathNamespace("dc","http://purl.org/dc/elements/1.1/");
    $xml->registerXPathNamespace("diag","http://www.loc.gov/zing/srw/diagnostic/");
    $xml->registerXPathNamespace("xcql","http://www.loc.gov/zing/cql/xcql/");
    $hits = $xml->xpath("srw:numberOfRecords");

    #$first = $xml->xpath("//srw:recordPosition");
    #$first = 1*$first[0];
    #print "$query";
    return array(
        "hits" => 1*$hits[0], 
        "result" => $xml->xpath("//srw:record/srw:recordData/*")
    );
}

if ($page == 'catalog' && $action && $query) {
    $r = SRUQuery( $query, "dc", $searchindex );
    $hits = $r["hits"];
    $result = $r["result"];
    $size = count($result);

    foreach ( $result as $r ) {
        # TODO: PPN übernehmen
        $fields = array( "title", "creator", "publisher", "date", "identifier" );
        $record = array();
        foreach ( $fields as $field) {
            $values = $r->xpath("dc:$field");
            if ($values) {
                $record[$field] = implode("; ", $values);
            }
        }
        if ($record) {
            $resultlist[] = $record;
        }
    }

    # Nochmal die gleiche Suche nur im PICA+ Format
    $r = SRUQuery( $query, "pica", $searchindex );
    $result = $r["result"];
    if ($r) {
        $i=0;
        foreach ( $result as $r ) {
            foreach ( $r->datafield as $d ) {
                if ((string)$d['tag'] == '003@') {
                    $ppn = (string)$d->subfield;
                    break;
                }
            }
            $resultlist[$i++]["ppn"] = $ppn;
        }
    }
}

echo "<body>";
?>
<div id="topbar">
    <div id="leftnav">
        <a href="?p="><img alt="home" src="images/home.png" />
        <?php print htmlspecialchars($mTitle); ?></a>
    </div>
</div>
<div id="content">

<?php
if ($resultlist) {
?>
<ul class="pageitem">
    <li class="menu"><a href="<?php
        print "?p=catalog&amp;q=".urlencode($query)."&amp;i=$searchindex";
    ?>">
    <img alt="changelog" src="thumbs/start.png" />
        <span class="name">Zurück zur Suche</span>
        <span class="arrow"></span>
    </a></li>
</ul>
    <ul class="pageitem">
<?php foreach ($resultlist as $record) {
    echo '<li class="store">';
    $url=""; # TODO: link auf volltitelanzeige
    $ppn = $record["ppn"];
    echo "<a class=\"noeffect\" href=\"$url?p=fullitem&g=$ppn&q=$query&$i=searchindex&gbveki=$gbveki\">";
    echo '<span class="image"></span>';
         //style="background-image: url('pics/stadiumarcadium.jpg');">
    $c = $record['creator'];
    if ($record['date']) $c .= " (".$record['date'].")";
    echo '<span class="comment">'.htmlspecialchars($c).'</span>';
    echo '<span class="name">'.htmlspecialchars($record['title']).'</span>';
    #echo '<img alt="rating" class="stars" src="images/4stars.png" /><span class="starcomment">13 Reviews</span>';
    echo '<span class="arrow"></span>';
    echo '</a></li>';
    }
echo '<li class="textbox">' . count($result) . " Treffer von $hits</li>";
echo '</ul>';
} elseif ($page == "fullitem" && $getitem) {
?>
<ul class="pageitem">
    <li class="menu"><a href="<?php
        print "?p=catalog&amp;q=".urlencode($query)."&amp;a=search&amp;i=$searchindex&gbveki=$gbveki";
    ?>">
    <img alt="changelog" src="thumbs/start.png" />
        <span class="name">Zurück zur Suche</span>
        <span class="arrow"></span>
    </a></li>
</ul>
<?php
    $result = SRUQuery( $getitem, "dc", $mIDIndex );
    $result = $result["result"];
    if ($result) {
        foreach ( $result as $r ) {
            echo '<span class="graytitle">Treffer</span>';
            print '<ul class="pageitem">';
        $fields = array( "title", "creator", "publisher", "date", "identifier" );
        $record = array();
        foreach ( $fields as $field) {
            $values = $r->xpath("dc:$field");
            $values = implode("; ", $values);
            print "<li class='textbox'<p>";
            print "<b>$field:</b> " . htmlspecialchars($values) . "</p></li>";
         }
            echo '</ul>';
            break;
        }
    } else {
        print '<ul class="pageitem">';
        print "<li>Titel nicht gefunden!</li>";
        echo '</ul>';
    }
} elseif ($page == "catalog" ) {
?>
<form action="" method="get" id="catalog-search">
    <input type="hidden" name="p" value="catalog" />
    <input type="hidden" name="gbveki" value="<?php echo $gbveki ?>" />
    <span class="graytitle">Suche im Katalog</span>
    <ul class="pageitem">
      <li class="form">
        <input placeholder="<?php htmlspecialchars($query)?>" type="text" name="q" value="<?php htmlspecialchars($query)?>"/></li>
        <li class="form">
        <input name="a" type="submit" value="Suche" /></li>
        <li class="form"><select name="i">
<?php foreach ($mSRUIndices as $v => $l) {
print "<option value=\"$v\"".($searchindex==$v?" selected='1'":"").">".htmlspecialchars($l)."</option>\n";
} ?>
        </select><span class="arrow"></span> 
      </li>
        <li class="textbox">
<span class="graytitle">EKI: (Suche auf eine Bibliothek beschränken)</span>
        </li>
      <li class='form'>
     
        <input placeholder="<?php htmlspecialchars($gbveki)?>" type="text" name="gbveki" value="<?php htmlspecialchars($gbveki)?>"/></li>
      </li>
    </ul>
</form>
<?php } else if ($page == "help" ) { ?>
<ul class="pageitem">
    <li class="textbox">
        <span class="header">Was ist das hier?</span>
        <p>
            Diese Seite ist ein Prototyp einer mobilen Weboberfläche für Bibliothekskataloge.
            Der Projektname ist <em>mopaci</em> (Mobiles OPAC Interface).
        </p>
    </li>
    <li class="textbox">
        <span class="header">Wie funktioniert mocapi?</span>
        <p>
            Die Oberfläche basiert auf <a href="http://iwebkit.net">iWebKit</a>.
            Die Suche wird über SRU an den <a href="http://gso.gbv.de/DB=2.1/">Verbundkatalog des GBV</a>
            weitergeleitet und die Ergebnisse im Dublin-Core-Format ausgewertet und angezeigt.
        </p>
    </li>
    <li class="textbox">
        <span class="header">Wieso haben einige Treffern keite Autoren oder Titel?</span>
        <p>
            Das liegt an der Dublin-Core-Ausgabe der SRU-Schnittstelle.
        </p>
    </li>
    <li class="textbox">
        <span class="header">Wann ist mopaci fertig?</span>
        <p>
            Gar nicht, dies ist nur ein Prototyp als Designstudie! Der Quellcode steht
            unter der <a href="http://de.wikipedia.org/wiki/GNU_Affero_General_Public_License">Affero General Public License</a> (AGPL)
            <a href="mtest.tgz">zur Verfügung</a>, d.h. er kann weiterverwendet
            werden, muss aber dann auch unter der AGPL  veröffentlicht werden!
            Mocapi ist also dann fertig, wenn <em>DU</em> (oder deine Einrichtung)
            daraus etwas machst und nicht wenn irgend jemand anderes etwas macht!
        </p>
    </li>
    <li class="textbox">
        <span class="header">Was fehlt denn noch?</span>
        <p>
            Der Quellcode muss komplett <em>und sauber</em> neugeschrieben werden (sind allerdings weniger als 300 Zeilen).
            Es fehlt eine Auswahl der Bibliothek statt der EKI, die Anzeige von Bestandsdaten und möglichst
            aktuelle Verfügbarkeitsanzeige mit DAIA. Außerdem wäre ein Nachladen von weiteren Treffern sinnvoll.
        </p>
    </li>
</li>
<?php } else { ?>
<ul class="pageitem">
    <li class="textbox">
        <span class="header"><?php print htmlspecialchars($mTitle); ?></span>
        <p>Willkommen auf der mobilen Webseite!</p>
    </li>
    <li class="menu"><a href="?p=catalog">
    <img alt="changelog" src="thumbs/start.png" />
        <span class="name">Suche im Katalog</span>
        <span class="arrow"></span>
    </a></li>
    <li class="menu"><a href="?p=help">
    <img alt="changelog" src="thumbs/help.png" />
        <span class="name">Hilfe</span>
        <span class="arrow"></span>
    </a></li>
</ul>
<?php } ?>

</div>
<div id="footer">
  Powered by <?php echo "mopaci $mVersion"; ?>
  with <a class="noeffect" href="http://iwebkit.net">iWebKit</a>
  and <a href="<?php print $mSRUServer; ?>">SRU</a>
</div>
</body>

</html>
