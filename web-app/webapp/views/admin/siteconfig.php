<?php
/**
 * @var $baseURL is the base URL
 * @var $scriptURL is the url of the index.php script
 */
$sc = \WebApp\Config::GetConfig();
// $mediaDir = \dirname($_SERVER["SCRIPT_FILENAME"]) . DIRECTORY_SEPARATOR . 'media';
$allFiles = \scandir($mediaDir);
$approved_PictureExt = ['png', 'jpg', 'jpeg','svg','gif'];
$mediaFiles = [];
foreach ($allFiles as $mf) {
    $ext = \pathinfo($mf, PATHINFO_EXTENSION);
    if (in_array($ext, $approved_PictureExt)) {
        $mediaFiles[] = $mf;
    }

}
?>
<!-- web-app/webapp/views/admin/siteconfig.php:<?= __LINE__ ?> 1724178372 -->
<div>
<script src="<?=$baseURL?>js/htmx-lite.js"></script>
<h2>Site config</h2>

<div class="tableform">
<div><span>Setting</span><span>Value</span><span>Modified</span></div>
<form action="<?=$scriptURL . '/siteconfig'?>" onsubmit="return false;">
<!-- web-app/webapp/views/admin/siteconfig.php:<?= __LINE__ ?> 1725786062 -->
<label for="title">Title:</label>
<span><input id="title" type="text" name="title" value="<?=$sc->title?>" onchange="hxl_submit_form(event);"> </span>
<span id="title_date"><?=$sc->titleDate?></span>
</form>
<form action="<?=$scriptURL . '/siteconfig'?>" onsubmit="return false;">
<!-- web-app/webapp/views/admin/siteconfig.php:<?= __LINE__ ?> 1725786078 -->
<label for="logo">Logo:</label>
<span>
    <!-- <input id="logo" type="text" name="logo" value="<?=$sc->logo?>" onchange="hxl_submit_form(event);"> -->
    <select name="logo" id="logo"
            onchange="hxl_submit_form(event)">
        <?php foreach ($mediaFiles as $mf): ?>
            <option value="<?=$mf?>" <?= $mf===$sc->logo ? 'selected' : '' ?> ><?=$mf?></option>
        <?php endforeach;?>
    </select>
</span>
<span id="logo_date"><?=$sc->logoDate?></span>
</form>
<form action="<?=$scriptURL . '/siteconfig'?>" onsubmit="return false;">
<!-- web-app/webapp/views/admin/siteconfig.php:<?= __LINE__ ?> 1725786084 -->
<label for="slogan">Slogan:</label>
<span><input id="slogan" type="text" name="slogan" value="<?=$sc->slogan?>" onchange="hxl_submit_form(event);"></span>
<span id="slogan_date"><?=$sc->sloganDate?></span>
</form>
</div>

<p />
<h2>Pages</h2>

<div id="site_pages" class="tableform">
<div><span>Name</span></div>
<?php foreach ($sc->pages as $pos => $page): ?>
<form id="pi-<?= $page->PageId ?>" action="<?=$scriptURL . '/siteconfig'?>" onsubmit="return false;">
<input type="hidden" name="pageid" value="<?= $page->PageId ?>">
<span><input type="text" name="page_name" id="<?=$page->PageId?>" value="<?=$page->Name?>" onchange="hxl_submit_form(event);"></span>
</form>
<?php endforeach;?>
<form id="new_page" action="<?=$scriptURL . '/siteconfig'?>" onsubmit="return false;">
<!-- web-app/webapp/views/admin/siteconfig.php:<?= __LINE__ ?> 1724178301 -->
<input type="hidden" name="pageid" value="new">
<span><input type="text" name="page_name" id="new-page-id" onchange="hxl_submit_form(event);"></span>
</form>
</div>

</div>