<?php 

/**
 * MOCAPI full record display.
 *
 * Licensed under Affero General Public License (AGPL)
 */

include_once('mocai.php');

$id = $_REQUEST['id'];

include_once('header.php');
include_once('searchform.php');

if ($query) {
?>
<div>
<ul class="pageitem">
  <li class="menu"><a href="<?php echo $mo->config['basepath']."search?q=$query"; ?>">
    <img alt="changelog" src="<?php echo $mo->config['basepath'] ?>thumbs/start.png" />
        <span class="name">Zurück zur Suche</span>
        <span class="arrow"></span>
    </a>
  </li>
</ul>
<?php
}

$result = $mo->SRUQuery( $id, "dc", "pica.ppn", $addtoquery );

$result = $result["result"];
if ($result) {
    foreach ( $result as $r ) {
        echo '<span class="graytitle">Treffer</span>';
        echo "<img class='cover' title='$id' style='float:left; display:none;'/>";
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
    print "<li>Titel $id nicht gefunden!</li>";
    echo '</ul>';
}

?>

<?php include_once('footer.php'); ?>