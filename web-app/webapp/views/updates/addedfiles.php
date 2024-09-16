<?php
/**
 * @var $arg from calling file
 * @var $scriptURL url of the index.php
 * @var $baseURL url on the directory
 */
$approved_PictureExt = ['png', 'jpg', 'jpeg','svg','gif'];
error_log(__FILE__.':'.__LINE__. ' adding '.print_r($arg,true));
foreach($arg->files as $file):
?>
 <form method="post" id="<?= idHash($file) ?>" action="<?= $scriptURL ?>/pictures" onsubmit="return false;" x-action="append" x-id="filetab">
    <input type="hidden" name="file" value="<?= $file ?>" >
    <a href="<?= $baseURL ?>/media/<?= $file ?>"><?= $file ?></a>
    <span class="center">
      <input type="submit" name="del" value="x" style="color: red" title="delete this file" onclick="hxl_submit_form(event);"  >
    </span>
    <span><?php $ext = \pathinfo($file, PATHINFO_EXTENSION);
    if (in_array($ext, $approved_PictureExt)): ?>
    <img class="thumbnail" src="<?= $baseURL ?>/media/<?= $file ?>" alt="<?= $file ?>" >
      <?php endif; ?></span>
  </form>
<?php endforeach;
?>
<input type="file" id="files" name="files[]" multiple x-action="replace">