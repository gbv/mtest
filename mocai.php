<?php

/**
 * MOCAI - MObile CAtalog Interface
 */
class MOCAI {
    var $config;

    function __construct($config) {
        $this->config = $config;
    }

    function SRUQuery( $query, $schema, $index, $addtoquery="") {
        if (!$index || !($this->config['sruindices'][$index] || $index == 'pica.ppn'))
            $index = $this->config['srudefaultindex'];

        $query = sprintf('%s="%s"', $index, str_replace('"','\"',$query));
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
        $url = $this->config['sru'] . "?" . http_build_query($sru_params);
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

    function recordURL( $ppn ) {
        return $this->config['basepath'] . 'record/' . $ppn;
    }

    // return a link to the previous query (if given)
    function queryURL() {
        return "";
        # "?p=catalog&amp;q=".urlencode($query)."&amp;a=search&amp;i=$searchindex&gbveln=$gbveln";
    }
}

// create a new MOCAI object with configuration
$mo = new MOCAI(array(
        'version' => '0.1.0a',
        'sru' => 'http://gso.gbv.de/sru/DB=2.1',
        'sruindices' => array(
            '' => 'nach allen Angaben',
            'pica.per' => 'nach Person',
            'pica.tit' => 'nach Titel',
            'pica.slw' => 'nach Schlagwort',
            'pica.num' => 'nach ISBN o.Ä.',
        ),
        'srudefaultindex' => 'pica.all',
        'daia' => '', # TODO
        'coverws' => 'http://ws.gbv.de/covers/',
        'title' => 'GVK Mobile beta',
        'baseurl' => 'http://ws.gbv.de/mtest/',
        'basepath' => '/mtest/',
        'jquery' => '/mtest/javascript/jquery-1.4.1.min.js'
));

# error_reporting(E_ALL);
# ini_set('display_errors', '1');
session_name('mocai'); # TODO: change name if multiple mocai instances per domain
session_start();

$query = @$_SESSION['query'] ? $_SESSION['query'] : "";

?>