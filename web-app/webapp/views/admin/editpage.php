<?php
$sc = \WebApp\Config::GetConfig();
foreach($sc->pages as $page){
    if($page->Hash === $arg->page2edit){
        break;
    }
}
if(file_exists($arg->pageFile)){
    $content=file_get_contents($arg->pageFile);
}else{
    $content='';
    $db=\WebApp\Db\DbCtx::GetInstance();
    $d='';
    $bgPic='';
    $description='';
    foreach($db->FindRows('PageContent',['Hash' => $arg->page2edit]) as $row){
        error_log(print_r($row,true));
        if(strcmp($d, $row->Created) < 0){
            $d=$row->Created;
            $content=$row->Content;
            $bgPic=$row->BackgroundPic;
            $description=$row->Description;
        }
    }
}
?>
<!-- editpage.php -->
<div>
<h2>Editing page &raquo;<?= $page->Name ?>&laquo;</h2>
<script src="<?=$baseURL?>js/htmx-lite.js"></script>
<script src="<?= $baseURL ?>js/editor.js"></script>
<script src="<?= $baseURL ?>js/tinymce/tinymce.min.js"></script>
<form action="<?=$scriptURL . '/editpage'?>" onsubmit="return false;">
<input type="hidden" name="page2edit" value="<?= $arg->page2edit ?>">
<table class="cfgTable">
    <tr><td class="right">Description</td>
    <td><input type="text" name="description" 
    placeholder="a good description what can be found on this page" value="<?= $description ?>"></td></tr>
    <tr><td class="right">Background picture</td><td><select name="bgPicture" id="bgPic">
        <?php 
        
        ?>
    </select></td></tr>
</table>
<h3>Content:</h3>
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