function topBoxPage(){
    alert('topBoxPage function');
}

function topBoxMedia(){
    alert('topBoxMedia function');
}

function topBoxLogout(){
    alert('topBoxLogout function');
    let topBox=document.getElementById('topbox');
    hxl_send_form('admin.php/logout', null, topBox);
}
