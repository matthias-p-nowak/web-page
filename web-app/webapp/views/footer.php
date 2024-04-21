<?php

?>

<?php $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]; ?>
<span class="tinyfooter">served in <?= number_format($time,4) ?> seconds</span>