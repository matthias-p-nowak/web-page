<?php
$sc = \WebApp\Config::GetConfig();
$pages=$sc->pages ?? [];
?>
<div class="logoline"><h1 id="logolinelogo"><?= $sc->logo ?></h1></div>
<div class="topnav"><nav>
<?php foreach($pages as $page): ?>
<a href="<?= $scriptURL.'/pg?'.$page->Internal ?>"><?= $page->Title ?></a>
<?php endforeach;?>
</nav></div>
