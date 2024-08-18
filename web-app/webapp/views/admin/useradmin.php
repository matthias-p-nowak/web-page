<?php
/**
 * @var $baseURL the url without the script name
 * @var $arg from the calling script
 * @var $scriptURL the url of the index.php
 */
?>
<script src="<?=$baseURL?>/js/htmx-lite.js"></script>
<div>
<h2>User administration</h2>
    <div class="tableform">
        <div><span>Email</span><span>Registered</span><span>Changing to</span><span>Created</span></div>
        <?php foreach ($arg->users as $user): $h = idHash('Admin/UserLevel' . $user->UserId);?>
	            <form id="<?=idHash('Admin/UserId' . $user->UserId)?>" action="<?=$scriptURL?>/useradmin">
	                <input type="hidden" name="UserId" value="<?=$user->UserId?>">
	                <label for="<?=$h?>"><?=$user->Email?></label>
	                <span id="<?=$h?>"><?=$user->LevelStr?></span>
	                <span> <select name="NewLevel" onchange="hxl_submit_form(event)">
	        <?php foreach (\WebApp\Db\AppUser::Levels as $level => $levelStr): ?>
	            <option value="<?=$levelStr?>" <?=$level == $user->Level ? 'selected' : ''?> ><?=$levelStr?></option>
            <?php endforeach;?>
        </select> </span>
    <span><?=$user->Created?></span>
</form>
<?php endforeach;?>
</div>
</div>
