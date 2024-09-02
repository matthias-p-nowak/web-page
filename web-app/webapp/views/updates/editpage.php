<?php
/**
 * @var $arg the page selected, from calling script
 * @var $sc the configuration
 */
?>
<script>
let d=document.getElementById('description');
d.value = '<?= $arg->Description ?>';
d=document.getElementById('picture');
d.value = '<?= $arg->Picture ?>';
debugger;
let data="<?= base64_encode($arg->Content) ?>";
data=atob(data);
data = escape(data);
data=decodeURIComponent(data);
tinymce.activeEditor.setContent(data);
</script>