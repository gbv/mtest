<?php

include_once('mocai.php');

$gbveln = @$_GET["gbveln"];
if ($gbveln) {
    $addtoquery = 'pica.bib="'.str_replace('"','\"',$gbveln).'"';
}

$query = @$_GET["q"];
if (get_magic_quotes_gpc()) $query = stripslashes($query);
$_SESSION["query"] = $query;

$searchindex = @$_GET["i"];
$getitem = @$_GET["g"];
if (get_magic_quotes_gpc()) $getitem = stripslashes($getitem);
$page = @$_GET['p'];

$gbveln = @$_GET["gbveln"];
if ($gbveln) {
    $addtoquery = 'pica.bib="'.str_replace('"','\"',$gbveln).'"';
}

$resultlist = array();

if ($query) {
    $r = $mo->SRUQuery( $query, "dc", $searchindex, $addtoquery );
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

    # Nochmal die gleiche Suche nur im PICA+ Format :-(
    $r = $mo->SRUQuery( $query, "pica", $searchindex, $addtoquery );
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

    # nur ein Treffer? Zur vollanzeige!
    if ( count($result) == 1 ) {
        # TODO: cookies
       header("Location: " . $mo->config['baseurl']."record/".$resultlist[0]['ppn']);
       exit; 
    }
}

include_once('header.php');
include_once('searchform.php');

if ($resultlist) { ?>
<!-- TODO: hier suche nach was und worin (erweiterte Optionen) -->
<div id="content">
    <ul class="resultlist">
<?php foreach ($resultlist as $record) {
    echo '<li class="store">';
    $url=""; # TODO: link auf volltitelanzeige
    $ppn = $record["ppn"];
    echo "<a class=\"noeffect smallcover\" href=\"" . $mo->recordURL($ppn) . ' ">';
    echo "<img src='cover.png' width='60' class='smallcover' title='$ppn' style='float:left;' />";
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

} else { ?>
<ul class="pageitem">
    <li class="textbox">Die Suche ergab leider keine Treffer<li>
</ul>
<?php } ?>
</div>
<?php include_once('footer.php'); ?>
