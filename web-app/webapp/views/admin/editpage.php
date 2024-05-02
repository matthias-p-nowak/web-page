<?php
foreach($arg->pages as $page){
    if($page->Hash === $arg->page2edit){
        break;
    }
}
if(file_exists($arg->pageFile)){
    $content=file_get_contents($arg->pageFile);
}else{
    $content='';
}
?>
<div>
<h2>Editing page &raquo;<?= $page->Name ?>&laquo;</h2>
<script src="<?=$baseURL?>js/htmx-lite.js"></script>
<script src="<?= $baseURL ?>js/editor.js"></script>
<script src="<?= $baseURL ?>js/tinymce/tinymce.min.js"></script>
<form action="<?=$scriptURL . '/editpage'?>" onsubmit="return false;">
<input type="hidden" name="page2edit" value="<?= $arg->page2edit ?>">
<textarea name="newContent" id="content" ><?= $content ?></textarea>
<input class="right_align" type="submit" value="update page" onclick="editor_submit(event);">
</form>
<fieldset><legend>Saved version</legend><div id="preview">nothing saved yet</div></fieldset>
<script>
tinymce.init({
    selector:'textarea#content',
    plugins: [
        'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
        'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime',
        'media', 'table', 'emoticons', 'help'],
    toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | ' +
    'bullist numlist outdent indent | link image | preview media fullscreen | ' +
    'forecolor backcolor emoticons',
    menubar: 'edit view insert format table',
    skin: 'tinymce-5',
    content_css: '<?= $baseURL ?>main.css',
    promotion: false,
    branding: false });
</script>
</div>