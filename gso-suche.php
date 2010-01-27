<html>
<head>
 <title>Einfache Suche im GSO-Katalog</title>
</head>
<body>
<h1>Einfache Suche im GSO-Katalog</h1>
<?php

$query = $_GET["query"];
if (get_magic_quotes_gpc()) $query = stripslashes($query);
$start = 1*$_GET["start"];
if(!$start || $start <= 0) $start = 1;
?>
<form>
<input
    type="text" size="50" id="cqlquery" name="query"
    value="<?php echo htmlspecialchars($query); ?>"/>
<input type="submit"/>
<?php
$sru_server = "http://gso.gbv.de/sru/DB=2.1";
$sru_params = array(
    "query" => $query,
    "version" => "1.1",
    "operation" => "searchRetrieve",
    "recordSchema" => "dc",
    "maximumRecords" => "10",
    "startRecord" => $start,
    "sortKeys" => "none"
);

if ($query) {
    print "<hr>";
    $url = $sru_server . "?" . http_build_query($sru_params);
    $xml = simplexml_load_file($url);

    $xml->registerXPathNamespace("srw","http://www.loc.gov/zing/srw/");
    $xml->registerXPathNamespace("dc","http://purl.org/dc/elements/1.1/");
    $xml->registerXPathNamespace("diag","http://www.loc.gov/zing/srw/diagnostic/");
    $xml->registerXPathNamespace("xcql","http://www.loc.gov/zing/cql/xcql/");

    $hits = $xml->xpath("srw:numberOfRecords");
    $hits = 1*$hits[0];

    $first = $xml->xpath("//srw:recordPosition");
    $first = 1*$first[0];

    $result = $xml->xpath("//srw:record/srw:recordData/dc:dc");
    $size = count($result);

    print "$hits Treffer insgesamt, zeige $size.";
    if ($first > 1) {
        $prev = $first - $size;
        if ($prev=0) $prev = 1;
        $link = "?" . http_build_query( array("query" => $query, "start" => $prev ) );
        print " <a href='" . htmlspecialchars($link) . "'>&lt;</a> ";
    }

    if ($first + $size < $hits) {
        $next = $first + $size;
        $link = "?" . http_build_query( array("query" => $query, "start" => $next ) );
        print " <a href='" . htmlspecialchars($link) . "'>&gt;</a> ";
    }


    while(list( , $record) = each($result)) {
        print "<hr>";
        $fields = array( "title", "creator", "publisher", "date", "identifier" );
        foreach ( $fields as $field) {
            $values = $record->xpath("dc:$field");
            if ($values) {
                print "<b>$field:</b> " . implode("; ", $values) . "<br>";
            }
        }
    }
}

#
# TODO: Mit MARC21 XSLT Stylesheets und BibUtils
# http://www.loc.gov/standards/mods/v3/MARC21slim2MODS3-2.xsl
# http://www.scripps.edu/~cdputnam/software/bibutils/
#
?>
</body>
</html>