<?php
if(isset($arg->newPage)):
$page=$arg->newPage;
?>
<!-- removing old new_page -->
<div id="new_page" x-action="remove"></form>
<!-- adding added page -->
<form x-action="append" x-id="site_pages" id="<?= $page->Hash ?>" action="<?=$scriptURL . '/siteconfig'?>" onsubmit="return false;">
<input type="hidden" name="page_hash" value="<?=$page->Hash?>">
<span><input type="text" name="page_name" id="<?=$page->Hash?>" value="<?=$page->Name?>" onchange="hxl_submit_form(event);"></span>
</form>
<!-- append new_page form -->
<form id="new_page" x-action="append" x-id="site_pages" action="<?=$scriptURL . '/siteconfig'?>" onsubmit="return false;">
<input type="hidden" name="page_hash" value="new">
<span><input type="text" name="page_name" id="<?=$page->Hash?>" onchange="hxl_submit_form(event);"></span>
</form>
<!-- appending nav items -->
<a x-action="append" x-id="menu_details" id="nav-<?= $page->Hash ?>" href="<?= $scriptURL.'/pg?'.$page->Hash ?>"><?= $page->Name ?></a>
<a x-action="append" x-id="nav-page-list" id="menu-<?= $page->Hash ?>" href="<?= $scriptURL.'/pg?'.$page->Hash ?>"><?= $page->Name ?></a>
<?php endif;
if(isset($arg->deletedPage)):
    $page=$arg->deletedPage;
?>
<div id="menu-<?= $page->Hash ?>" x-action="remove">remove</div>
<div id="nav-<?= $page->Hash ?>" x-action="remove">remove</div>
<div id="<?= $page->Hash ?>" x-action="remove">remove</div>
<?php endif; ?>
