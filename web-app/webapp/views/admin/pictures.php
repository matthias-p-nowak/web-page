<?php
?>
<!-- picture.php -->
<div>
<h2>Picture</h2>

<form action="<?=$scriptURL . '/pictures'?>" method="post" enctype="multipart/form-data">
  <label for="files">Choose files or drop files here to upload:</label>
  <input type="file" id="files" name="files[]" multiple>
  <input type="submit" value="Upload Image" name="submit">
</form>

</div>