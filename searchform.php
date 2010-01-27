<?php
/**
 * Show a search form.
 * Requires $mo and $query
 */
?>
<form action="<?php echo $mo->config['basepath'] ?>search" method="get" id="catalog-search" >
<div id="topbar">
  <div style="padding: 5px;">
    <div class="search">
      <a href="<?php echo $mo->config['basepath'] ?>" style="float:left; padding-right:4px;"><img alt="home" src="<?php echo $mo->config['basepath']; ?>thumbs/home.png" /></a>
      <input style="width:72%;font-size:16px;font-weight:normal;padding:4px;" type="text" 
             value="<?php echo urlencode($query); ?>" onfocus="if( value == 'Suche' ){value='';}" name="q" id="q"  />
      <input type="image" src="<?php echo $mo->config['basepath']; ?>thumbs/search.png" alt="Go" style="float:right;width:32px;" id="searchGo"/>
    </div>
  </div>
</div>