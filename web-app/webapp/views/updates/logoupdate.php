<?php
/**
 * @var $baseURL url of the directory
 */
$sc=\WebApp\Config::GetConfig();
error_log(__FILE__.':'.__LINE__);
?>
<?php if(isset($sc->logo) && ! is_null($sc->logo)) { ?>
    <img id="headlogo" src="<?= $baseURL . '/media/' . $sc->logo ?>" x-action="replace" alt="<?= $sc->slogan ?>" />
<?php } else { ?>
    <span id="headlogo" x-action="replace" ></span>
<?php } ?>
