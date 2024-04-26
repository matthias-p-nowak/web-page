<?php
$sc = \WebApp\Config::GetConfig();
?>
<div>
<h2>Site config</h2>

<div class="tableform">
<div><span>Setting</span><span>Value</span><span>Modified</span></div>
<form action="<?=$scriptURL . '/siteconfig'?>" onsubmit="return false;">
<label for="title">Title:</label>
<span><input id="title" type="text" name="title" value="<?=$sc->title?>" onchange="hxl_submit_form(event);"> </span>
<span id="title_date"><?=$sc->titleDate?></span>
</form>
<form action="<?=$scriptURL . '/siteconfig'?>" onsubmit="return false;">
<label for="logo">Logo:</label>
<span><input id="logo" type="text" name="logo" value="<?=$sc->logo?>" onchange="hxl_submit_form(event);"></span>
<span id="logo_date"><?=$sc->logoDate?></span>
</form>
</div>

<h2>Pages</h2>
</div>