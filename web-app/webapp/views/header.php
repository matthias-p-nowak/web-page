<?php
/**
 * @var $baseURL base URL
 * @var $scriptURL url of index.php
 */
$sc = \WebApp\Config::GetConfig();
$pages = $sc->pages ?? [];
?>
<!-- header.php -->
<div class="logoline">
    <a href="<?= $scriptURL ?>">
    <h1 id="logolinelogo"><?=$sc->logo ?? 'no logo defined'?></h1>
    </a>
</div>
<details id="menu_details">
    <summary><img src="<?=$baseURL?>assets/Hamburger_icon.png" alt="menu"></summary>
    <!-- web-app/webapp/views/header.php:<?= __LINE__ ?> 1724778266 -->
    <?php foreach ($pages as $page): ?><a href="<?=$scriptURL . '/pg?' . $page->PageId?>"><?=$page->Name?></a><?php endforeach;?>
</details>
<div id="menu_top" class="topnav"><nav id="nav-page-list">
    <!-- web-app/webapp/views/header.php:<?= __LINE__ ?> 1724778165 -->
<?php foreach ($pages as $page): ?><a href="<?=$scriptURL . '/pg?' . $page->PageId?>"><?=$page->Name?></a><?php endforeach;?>
</nav></div>
