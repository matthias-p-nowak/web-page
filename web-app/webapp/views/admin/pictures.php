<?php
/**
 * @var $scriptURL contains the index.php
 * @var $baseURL root url
 */
?>
<!-- picture.php -->
<div>
<script src="<?=$baseURL?>js/pictures.js"></script>
<script src="<?=$baseURL?>js/htmx-lite.js"></script>
<h2>Picture and ...</h2>

<h3>Upload pictures and files</h3>
<form id="dropArea" class="borderbox" action="<?=$scriptURL . '/pictures'?>"
  method="post" enctype="multipart/form-data" onsubmit="hxl_submit_form(event);">
  <label for="files">Choose files or drop files here to upload:</label>
  <br />
  <input type="file" id="files" name="files[]" multiple>
  <br />
  <input class="right_align" type="submit" value="Upload" name="submit" >
</form>

<h3>Existing pictures and files</h3>
<div id="filetab" class="tableform">
<div><span>File</span><span>Delete</span></div>
<?php
$destDir = dirname($_SERVER["SCRIPT_FILENAME"]) . DIRECTORY_SEPARATOR . 'media';
$files = scandir($destDir);
foreach ($files as $file):
    if ($file == '.' || $file == '..') {
        continue;
    }
  ?>
  <form method="post" action="<?= $scriptURL ?>/pictures">
    <a href="<?= $baseURL ?>/media/<?= $file ?>"><?= $file ?></a>
    <span>
      <input type="submit" name="del" value="x" style="color: red" title="delete this file" >
    </span>
  </form>
  <?php
  endforeach;
?>
</div>
</div>