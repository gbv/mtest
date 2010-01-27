<?php

include_once('mocai.php');
include_once('header.php');

if (!$query) $query = "Suche"
?>
<body>
<?php
include_once('searchform.php');

 /*if ($resultlist) { ?>
<div id="exttopbar">
  <div style="padding: 5px;">
    <div style="float:right">ELN:
    <input value="<?php print htmlspecialchars($gbveln)?>" style="width:4em"
           type="text" name="gbveln" />
    </div>
    <div><!-- TODO: show arrow -->
      <select name="i">
<?php foreach ($mo->config['sruindices'] as $v => $l) {
    print "<option value=\"$v\"".($searchindex==$v?" selected='1'":"").">".htmlspecialchars($l)."</option>\n";
} ?>
      </select>
    </div>
  </div>
</div>
<?php } else { ?>
<input type="hidden" name="gbveln" value="<?php echo $gbveln ?>" />
<?php } */?>
</form>
<?php
/* elseif ($page == "catalog" ) {
?>
<form action="" method="get" id="catalog-search">
    <input type="hidden" name="p" value="catalog" />
    <input type="hidden" name="gbveln" value="<?php echo $gbveln ?>" />
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
<span class="graytitle">ELN: (Suche auf eine Bibliothek beschränken)</span>
        </li>
      <li class='form'>
     
        <input placeholder="<?php htmlspecialchars($gbveln)?>" type="text" name="gbveln" value="<?php htmlspecialchars($gbveln)?>"/></li>
      </li>
    </ul>
</form>
<?php } 

*/?>
<ul class="pageitem">
    <li class="textbox">
        <span class="header"><?php print htmlspecialchars($mo->config['title']); ?></span>
        <p>Willkommen auf der mobilen Webseite!</p>
    </li>
    <!--li class="menu"><a href="?p=catalog">
    <img alt="changelog" src="thumbs/start.png" />
        <span class="name">Suche im Katalog</span>
        <span class="arrow"></span>
    </a></li-->
    <li class="menu"><a href="help">
    <img alt="changelog" src="thumbs/help.png" />
        <span class="name">Hilfe</span>
        <span class="arrow"></span>
    </a></li>
</ul>

</div>
<?php include_once('footer.php'); ?>