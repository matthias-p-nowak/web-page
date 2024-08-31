<?php
/**
 * @var $scriptURL url of index.php
 * @var $baseURL url of the directory containing index.php
 * @var $arg from calling script
 */
$pages=$arg->pages ?? [];
if(isset($arg->newPage)):
$page=$arg->newPage;
?>
<!-- removing old new_page -->
<div id="new_page" x-action="remove"></form>
<!-- adding added page -->
<form x-action="append" x-id="site_pages" id="<?= $page->PageId ?>" action="<?=$scriptURL . '/siteconfig'?>" onsubmit="return false;">
<input type="hidden" name="pageid" value="<?=$page->PageId?>">
<span><input type="text" name="page_name" id="name-<?=$page->PageId?>" value="<?=$page->Name?>" onchange="hxl_submit_form(event);"></span>
</form>
<!-- append new_page form -->
<form id="new_page" x-action="append" x-id="site_pages" action="<?=$scriptURL . '/siteconfig'?>" onsubmit="return false;">
<input type="hidden" name="pageid" value="new">
<span><input type="text" name="page_name" id="name-<?=$page->PageId?>" onchange="hxl_submit_form(event);"></span>
</form>
<?php endif;
if(isset($arg->deletedPage)):
    $page=$arg->deletedPage;
?>
<!-- remove the form with that id -->
<div id="<?= $page->PageId ?>" x-action="remove">remove</div>
<?php endif; ?>
<!-- always replace the nav items -->
<details id="menu_details" x-action="replace">
    <summary><img src="<?= $baseURL ?>assets/Hamburger_icon.png" alt="menu"></summary>
    <?php foreach($pages as $page): ?><a href="<?= $scriptURL.'/pg?'.$page->PageId ?>"><?= $page->Name ?></a><?php endforeach;?>
</details>
<nav id="nav-page-list" x-action="replace"> 
<?php foreach($pages as $page): ?><a href="<?= $scriptURL.'/pg?'.$page->PageId ?>"><?= $page->Name ?></a><?php endforeach;?>
</nav>