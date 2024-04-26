<?php

?>
<hr />
<div class="tinyfooter">
<?php $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]; ?>
<span>served in <?= number_format($time,4) ?> seconds</span>
</div>