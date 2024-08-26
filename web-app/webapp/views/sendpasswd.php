<?php
/**
 * @var $scriptURL  url of index.php
 * @var $arg from the calling scriptf
 */
global $config;
?>
<!-- sendpasswd view -->
<dialog class="sendpw">
<h2>Send me a password</h2>
<form method="post" action="<?=$scriptURL?>/login">
<label for="email">email:</label>
<input type="email" id="email" name="email"
  placeholder="your email address" required autofocus>
<label for="magic">Magic number: <?= 
   isset($config->magic_hint) ? ' (hint: '.$config->magic_hint.')' : '' ?></label>
<input type="number" id="magic" name="magic"
   placeholder="use the number the admin has told you" required>
<input type="submit" value="Send password">
<input type="hidden" value="<?= $arg->loginRN ?>" name="loginRN">
</form>
</dialog>