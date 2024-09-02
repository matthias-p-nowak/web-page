<?php
/**
 * showing the page
 * @var $baseURL base url
 * @var $scriptURL the index.php url
 * @var $arg - the page structure
 */
$sc = \WebApp\Config::GetConfig();
// foreach ($sc->pages as $page) {
//     if ($page->PageId === $arg->page2edit) {
//         break;
//     }
// }
$content = '';
$db = \WebApp\Db\DbCtx::GetInstance();
$bgPic = '';
$description = '';
$versions=[];
foreach ($db->FindRows('Page', ['PageId' => $arg->page2edit]) as $row) {
    $versions[$row->Created]=$row->IsActive;
    if($row->IsActive){
        error_log(__FILE__ . ':' . __LINE__ . ' ' . print_r($row, true));
        $content = $row->Content;
        $bgPic = $row->Picture ?? '';
        $description = $row->Description;
        $pagename = $row->Name;
    }
}
$versions=array_reverse($versions);
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
<h2>Editing page &raquo;<?=$pagename?>&laquo;</h2>
<script src="<?=$baseURL?>js/htmx-lite.js"></script>
<script src="<?=$baseURL?>js/editor.js"></script>
<script src="<?=$baseURL?>js/tinymce/tinymce.min.js"></script>
<!-- web-app/webapp/views/admin/editpage.php 1723382684 -->
<form action="<?=$scriptURL . '/editpage'?>" onsubmit="return false;">
<input type="hidden" name="page2edit" value="<?=$arg->page2edit?>">
<table class="cfgTable">
    <tr>
        <td class="right">
            <label for="versionselect"></label>
            past versions</td>
        <td>
        <form action="<?=$scriptURL . '/editpage'?>" onsubmit="return false;">
        <input type="hidden" name="page2edit" value="<?=$arg->page2edit?>">
        <select name="version" id="versionselect" onchange="hxl_submit_form(event)">
        <?php foreach ($versions as $dt => $active): ?>
            <option value="<?= $dt ?>" <?= $active ? 'selected' : '' ?> ><?= $dt ?></option>
        <?php endforeach;?>
    </select>
</form> 
        </td>
    </tr>
    <tr><td class="right">Description</td>
    <td>
        <!-- web-app/webapp/views/admin/editpage.php:<?=__LINE__?> 1724939984 -->
    <form action="<?=$scriptURL . '/editpage'?>" onsubmit="return false;">
        <input type="hidden" name="page2edit" value="<?=$arg->page2edit?>">
        <input type="text" name="description" id="description"
            placeholder="a good description what can be found on this page" value="<?=$description?>"
            onchange="hxl_submit_form(event)">
    </form>
    </td></tr>
    <tr><td class="right"><label for="picture">Background picture</label></td>
    <td>
        <!-- web-app/webapp/views/admin/editpage.php:<?=__LINE__?> 1724939993 -->
    <form action="<?=$scriptURL . '/editpage'?>" onsubmit="return false;">
        <input type="hidden" name="page2edit" value="<?=$arg->page2edit?>">
        <select name="picture" id="picture"
            onchange="hxl_submit_form(event)">
        <?php foreach ($mediaFiles as $mf): ?>
            <option value="<?=$mf?>" <?= $mf===$bgPic ? 'selected' : '' ?> ><?=$mf?></option>
        <?php endforeach;?>
    </select></form>
</td></tr>
</table>
<h3>Content:</h3>
    <!-- web-app/webapp/views/admin/editpage.php:<?=__LINE__?> 1724939999 -->
    <form action="<?=$scriptURL . '/editpage'?>" onsubmit="return false;">
        <input type="hidden" name="page2edit" value="<?=$arg->page2edit?>">
        <textarea name="content" id="content" ><?=$content?></textarea>
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
    var edithint = document.getElementById('edithint');
    edithint.remove();
</script>
</div>