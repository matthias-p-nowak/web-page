<?php
/**
 * @var $scriptURL the url of index.php
 */
?>
<!-- adminbar.php -->
<div id="admin_bar">
<div >
   <h3>Admin menu</h3>
<?php if (($_SESSION['Level'] ?? -1) > -1): ?>
   <a href="<?= $scriptURL ?>/logout">logout</a>
   <a href="<?= $scriptURL ?>/permissions">permissions</a>
<?php if($_SESSION['Level'] == WebApp\Db\AppUser::Admin): ?>
   <a href="<?= $scriptURL ?>/useradmin">user administration</a>
   <a href="<?= $scriptURL ?>/siteconfig">site configuration</a>
   <a href="<?= $scriptURL ?>/pictures">picture management</a>
<?php endif;?>
<?php else: ?>
    <a href="<?= $scriptURL ?>/login">login</a>
<?php endif;?>
</div>
</div>