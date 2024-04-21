<?php 
$sc=\WebApp\Config::GetConfig();
?>

<div>
<h2>Site config</h2>
<table singleframe>
    <tr>
        <td colspan="2">
            <label for="site-title">Title (displayed on top of the browser):</label> 
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="text" name="site-title" value="<?= $arg->title ?>">
        </td>
    </tr>
</table>
<label for="title-modified">last modified:</label> 
<span id="title-modified">000</span>
</div>
<h2>Pages</h2>