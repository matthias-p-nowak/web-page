<?php

?>
<h2>User administration</h2>
<table class="thinborder">
<tr><th>Email</th><th>current level</th><th>new level</th><th>Created</th></tr>
<?php foreach($arg->users as $user): ?>
<tr>
<form>
    <input type="hidden" name="UserId" value="<?= $user->UserId ?>">
    <td><?= $user->Email ?></td>
    <td><?= $user->LevelStr ?></td>
    <td>
    <select name="NewLevel" onchange="submit_form(event)">
        <?php foreach($arg->levels as $level): ?>
            <option value="<?= $level ?>" <?= strcmp($level,$user->LevelStr)==0 ? 'selected': '' ?> ><?= $level ?></option>
        <?php endforeach; ?>
    </select>
    </td>
    <td><?= $user->Created ?></td>
</form>    
</tr>
<?php endforeach; ?>
</table>
<script>
    function submit_form(event){
        event.target.form.classList.add('requested');
        event.target.classList.add('requested');
        var formData=new FormData(event.target.form);
        fetch("<?= $scriptURL ?>/useradmin", { method: "Post" , body: formData})
        .then(response => { 
            console.log(response) ; 
            event.target.classList.remove('requested');
        })
        .catch(error => { console.error("An error occurred:", error); })
        ;
    }
</script>