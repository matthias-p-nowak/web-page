<?php
/**
 * @var $scriptURL contains the index.php
 * @var $baseURL root url
 */
$approved_PictureExt = ['png', 'jpg', 'jpeg','svg','gif'];
$sc = WebApp\Config::GetConfig();
?>
<!-- picture.php -->
<div>
<script src="<?=$baseURL?>js/pictures.js"></script>
<script src="<?=$baseURL?>js/htmx-lite.js"></script>
<h2>Picture and ...</h2>

<h3>Upload pictures and files</h3>
<form id="dropArea" class="borderbox" action="<?=$scriptURL . '/pictures'?>"
  method="post" enctype="multipart/form-data" onsubmit="return false;">
  <label for="files">Choose files or drop files here to upload:</label>
  <br />
  <input type="file" id="files" name="files[]" multiple>
  <br />
  <input class="right_align" type="submit" value="Upload" name="submit" onclick="hxl_submit_form(event);" >
</form>

<h3>Existing pictures and files</h3>
<!-- web-app/webapp/views/admin/pictures.php:<?= __LINE__ ?> 1725787248 -->
<div id="filetab" class="tableform">
<div><span>File</span><span>Delete</span><span>Thumbnail</span></div>
<?php
$files = scandir($sc->mediaDir);
foreach ($files as $file):
    if ($file == '.' || $file == '..') {
        continue;
    }
  ?>
  <form method="post" id="<?= idHash($file) ?>" action="<?= $scriptURL ?>/pictures" onsubmit="return false;">
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
  <?php
  endforeach;
?>
</div>
</div>