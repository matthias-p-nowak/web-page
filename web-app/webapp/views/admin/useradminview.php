<?php

?>
<script src="<?= $baseURL ?>/js/htmx-lite.js"></script>
<h2>User administration</h2>
<table class="thinborder">
<tr><th>Email</th><th>current level</th><th>new level</th><th>Created</th></tr>
<?php foreach($arg->users as $user): ?>
<tr id="<?= idHash('Admin/UserId'.$user->UserId) ?>">
<form action="<?= $scriptURL ?>/useradmin">
    <input type="hidden" name="UserId" value="<?= $user->UserId ?>">
    <td><?= $user->Email ?></td>
    <td id="<?= idHash('Admin/UserLevel'.$user->UserId) ?>"><?= $user->LevelStr ?></td>
    <td>
    <select name="NewLevel" onchange="hxl_submit_form(event)">
        <?php foreach( \WebApp\Db\AppUser::Levels as $level => $levelStr): ?>
            <option value="<?= $levelStr ?>" <?= $level == $user->Level ? 'selected': '' ?> ><?= $levelStr ?></option>
        <?php endforeach; ?>
    </select>
    </td>
    <td><?= $user->Created ?></td>
</form>    
</tr>
<?php endforeach; ?>
</table>