<?php
$pages=$arg->pages ?? [];
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
<?php endif;
if(isset($arg->deletedPage)):
    $page=$arg->deletedPage;
?>
<!-- remove the form with that id -->
<div id="<?= $page->Hash ?>" x-action="remove">remove</div>
<?php endif; ?>
<!-- always replace the nav items -->
<details id="menu_details" x-action="replace">
    <summary><img src="<?= $baseURL ?>assets/Hamburger_icon.png" alt="menu"></summary>
    <?php foreach($pages as $page): ?><a href="<?= $scriptURL.'/pg?'.$page->Hash ?>"><?= $page->Name ?></a><?php endforeach;?>
</details>
<nav id="nav-page-list" x-action="replace"> 
<?php foreach($pages as $page): ?><a href="<?= $scriptURL.'/pg?'.$page->Hash ?>"><?= $page->Name ?></a><?php endforeach;?>
</nav>