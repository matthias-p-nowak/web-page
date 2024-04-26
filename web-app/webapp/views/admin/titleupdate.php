<?php
$sc=\WebApp\Config::GetConfig();
?>
<span id="title_date" x-action="replace"><?= $sc->titleDate ?></span>
<script>document.title="<?= $sc->title ?>"</script>