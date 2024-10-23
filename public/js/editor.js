function topBoxPage() {
    alert('topBoxPage function');
}

function topBoxMedia() {
    alert('topBoxMedia function');
}

function topBoxLogout() {
    alert('topBoxLogout function');
    let topBox = document.getElementById('topbox');
    hxl_send_form('admin.php/logout', null, topBox);
}

function topBoxRewind() {
    alert('topBoxRewind function');
}

// function getAdminUrl(){
//     let loc=window.location.pathname;
//     let pathSegments = loc.split('/');
//     pathSegments[pathSegments.length - 1] = 'admin.php';
//     let admin = pathSegments.join('/');
//     console.log(window.location,admin);
//     return admin;
// }

function dealWithInput(event){
    console.log('got all input');
    let elem=event.target;
    elem.oninput=gotInput;
    elem.onblur=null;
    let fd=new FormData();
    // let admin=getAdminUrl();
    fd.append('id',elem.id);
    fd.append('text',elem.innerText);
    fd.append('location', window.location.pathname);
    console.log(elem);
    hxl_send_form('admin.php/editText', fd, elem);
}

function gotInput(event){
    console.log('got input');
    let elem= event.target;
    elem.oninput=null;
    elem.onblur= dealWithInput;
}

document.querySelectorAll('[id]').forEach(element => {
    // Check if the element has exactly one child node and that node is a text node
    if (element.childNodes.length === 1 && element.firstChild.nodeType === Node.TEXT_NODE) {
        element.setAttribute('contenteditable', 'true');
        element.oninput=gotInput;
    }
});