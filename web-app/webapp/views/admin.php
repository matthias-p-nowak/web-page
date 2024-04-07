<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<div id="admin_bar">
<div >
<?php if ($_SESSION['editor'] ?? 0 > 0): ?>
    already logged in
<?php else: ?>
    <a href="<?= $baseURL ?>index.php/login">login</a>
<?php endif;?>
</div>
</div>