<?php
/**
 * showing the page
 * @var $baseURL base url
 * @var $scriptURL the index.php url
 * @var $arg - the page structure
 */
$sc = \WebApp\Config::GetConfig();
foreach ($sc->pages as $page) {
    if ($page->PageId === $arg->page2edit) {
        break;
    }
}
$content = '';
$db = \WebApp\Db\DbCtx::GetInstance();
$created = '';
$bgPic = '';
$description = '';
foreach ($db->FindRows('Page', ['PageId' => $arg->page2edit]) as $row) {
    error_log(print_r($row, true));
    if (strcmp($created, $row->Created) < 0) {
        $created = $row->Created;
        $content = $row->Content;
        $bgPic = $row->BackgroundPic;
        $description = $row->Description;
        $pagename = $row->Name;
    }
}

$mediaDir = \dirname($_SERVER["SCRIPT_FILENAME"]) . DIRECTORY_SEPARATOR . 'media';
$allFiles = \scandir($mediaDir);
$approved_PictureExt = ['png', 'jpg', 'jpeg'];
$mediaFiles = [];
foreach ($allFiles as $mf) {
    $ext = \pathinfo($mf, PATHINFO_EXTENSION);
    if (in_array($ext, $approved_PictureExt)) {
        $mediaFiles[] = $mf;
    }

}
?>
<div>
<h2>Editing page &raquo;<?=$page->Name?>&laquo;</h2>
<script src="<?=$baseURL?>js/htmx-lite.js"></script>
<script src="<?=$baseURL?>js/editor.js"></script>
<script src="<?=$baseURL?>js/tinymce/tinymce.min.js"></script>
<form action="<?=$scriptURL . '/editpage'?>" onsubmit="return false;">
<!-- web-app/webapp/views/admin/editpage.php 1723382684 -->
<input type="hidden" name="page2edit" value="<?=$arg->page2edit?>">
<input type="hidden" name="created" value="<?=$created?>">
<table class="cfgTable">
    <tr><td class="right">Description</td>
    <td>
        <input type="text" name="Description"
            placeholder="a good description what can be found on this page" value="<?=$description?>"
            onchange="hxl_submit_form(event)">
    </td></tr>
    <tr><td class="right">Background picture</td><td>
        <select name="Picture" id="bgPic"
            onchange="hxl_submit_form(event)">
        <?php foreach ($mediaFiles as $mf): ?>
            <option value="<?=$mf?>"><?=$mf?></option>
        <?php endforeach;?>
    </select></td></tr>
</table>
<h3>Content:</h3>
<textarea name="newContent" id="content" ><?=$content?></textarea>
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
    content_css: '<?=$baseURL?>main.css',
    promotion: false,
    branding: false });
</script>
</div>