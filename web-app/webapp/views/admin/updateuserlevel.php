<?php 
/**
 * @var $arg from the calling php file
 */
?>
<table><tr>
<td id="<?=idHash('Admin/UserLevel' . $arg->UserId)?>" x-action="replace" ><?=$arg->LevelStr?></td>
</tr></table>