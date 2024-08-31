<?php 
/**
 * @var $arg from the calling php file
 */
?>
<table><tr>
<td id="l-<?=idHash('user-' . $arg->UserId)?>" x-action="replace" ><?=$arg->LevelStr?></td>
</tr></table>