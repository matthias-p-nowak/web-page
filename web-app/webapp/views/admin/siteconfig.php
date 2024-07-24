<?php
$sc = \WebApp\Config::GetConfig();
?>
<!-- siteconfig.php -->
<div>
<script src="<?=$baseURL?>js/htmx-lite.js"></script>
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

<p />
<h2>Pages</h2>

<div id="site_pages" class="tableform">
<div><span>Name</span><span>Background picture</span><span>Thumbnail</span></div>
<?php foreach ($sc->pages as $pos => $page): ?>
<form id="<?= $page->Hash ?>" action="<?=$scriptURL . '/siteconfig'?>" onsubmit="return false;">
<input type="hidden" name="page_hash" value="<?=$page->Hash?>">
<span><input type="text" name="page_name" id="<?=$page->Hash?>" value="<?=$page->Name?>" onchange="hxl_submit_form(event);"></span>
</form>
<?php endforeach;?>
<form id="new_page" action="<?=$scriptURL . '/siteconfig'?>" onsubmit="return false;">
<input type="hidden" name="page_hash" value="new">
<span><input type="text" name="page_name" id="<?=$page->Hash?>" onchange="hxl_submit_form(event);"></span>
</form>
</div>

</div>