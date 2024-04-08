<?php
?>
<div id="admin_bar">
<div >
<?php if (($_SESSION['Level'] ?? -1) > -1): ?>
   <a href="<?= $scriptURL ?>/logout">logout</a>
   <a href="<?= $scriptURL ?>/permissions">permissions</a>
<?php else: ?>
    <a href="<?= $scriptURL ?>/login">login</a>
<?php endif;?>
</div>
</div>