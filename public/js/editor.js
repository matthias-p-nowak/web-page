function topBoxPage() {
    alert('topBoxPage function');
}

function topBoxMedia() {
    alert('topBoxMedia function');
}

function topBoxLogout() {
    let topBox = document.getElementById('topbox');
    hxl_send_form('admin.php/logout', null, topBox);
}

function topBoxRewind() {
    alert('topBoxRewind function');
}

function dealWithInput(event) {
    let elem = event.target;
    elem.oninput = gotInput;
    elem.onblur = null;
    let formData = new FormData();
    // let admin=getAdminUrl();
    formData.append('saving', elem.id);
    formData.append('text', elem.innerText);
    formData.append('location', window.location.pathname);
    hxl_send_form('admin.php/saveText', formData, elem);
}

function saveEditorContent(event){
    let elem = event.target;
    let formData = new FormData();
    let id= elem.getAttribute('saving');
    formData.append('saving',id);
    formData.append('content',tinymce.activeEditor.getContent());
    formData.append('location', window.location.pathname);
    hxl_send_form('admin.php/saveText', formData, elem);
}

function makeDuplicate(event){
    let formData = new FormData();
    let id= elem.getAttribute('saving');
    formData.append('duplicate',id);
    formData.append('location', window.location.pathname);
    hxl_send_form('admin.php/duplText', formData, elem);
}

function gotInput(event) {
    console.log('got input');
    let elem = event.target;
    elem.oninput = null;
    elem.onblur = dealWithInput;
}

function showContextMenu(event) {
    event.preventDefault();
    console.log(event);
    let t = event.target;
    let fd = new FormData();
    fd.append('id', t.id);
    fd.append('start', true);
    fd.append('loc', window.location);
    hxl_send_form('admin.php/edit', fd, t);
}

function stopEditor(event) {
    let editor = document.getElementById('edi_tor');
    editor.showModal();
    tinymce.remove();
    editor.close();
    editor.remove();
}

function startEditor() {
    let editor = document.getElementById('edi_tor');
    editor.showModal();
    tinymce.remove();
    tinymce.init(
        {
            selector: 'div#editor_content',
            fixed_toolbar_container: 'dialog#edi_tor',
            plugins: [
                'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
                'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime',
                'media', 'table', 'emoticons', 'help'],
            toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | link image | preview media fullscreen | ' +
                'forecolor backcolor emoticons',
            menubar: 'edit view insert format table',
            skin: 'tinymce-5',
            content_css: 'main.css',
            promotion: false,
            branding: false
        }
    ).then(function () {
        const tinymcePopups = document.querySelectorAll('.tox-tinymce-aux, .tox-menu, .tox-dialog');
        tinymcePopups.forEach(function (popup) {
            editor.appendChild(popup);
        });
    });
    let ec = document.getElementById('editor_cancel');
    ec.onclick = stopEditor;
    let em = document.getElementById('editor_more');
    let next = em.getAttribute('next');
    if(next == ''){
        em.remove();
    }else{
        em.onclick = function () {
            let fd = new FormData();
            fd.append('id', next);
            fd.append('start', true);
            fd.append('loc', window.location);
            hxl_send_form('admin.php/edit', fd, em);
        };
    }
    let es=document.getElementById('editor_save');
    es.onclick=saveEditorContent;
    let md=document.getElementById('make_duplicate');
    md.onclick=makeDuplicate;
}


{
    let page = document.getElementById('_page');
    if (page != null) {
        page.querySelectorAll('[id]').forEach(element => {
            // Check if the element has exactly one child node and that node is a text node
            if (element.childNodes.length === 1 && element.firstChild.nodeType === Node.TEXT_NODE) {
                element.setAttribute('contenteditable', 'true');
                element.oninput = gotInput;
            }
            element.oncontextmenu = showContextMenu;
        });
    }
}