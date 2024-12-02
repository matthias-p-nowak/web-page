function topBoxPage() {
    let topBox = document.getElementById('topbox');
    let formData = new FormData();
    hxl_send_form('admin.php/page', formData, topBox);
}

function topBoxMedia() {
    let topBox = document.getElementById('topbox');
    let formData = new FormData();
    hxl_send_form('admin.php/media', formData, topBox);
}

function topBoxLogout() {
    let topBox = document.getElementById('topbox');
    hxl_send_form('admin.php/logout', null, topBox);
}

function topBoxRewind() {
    let topBox = document.getElementById('topbox');
    hxl_send_form('admin.php/rewind', null, topBox);
}

function topBoxHelp() {
    let topBox = document.getElementById('topbox');
    hxl_send_form('admin.php/help', null, topBox);
}

function dealWithInput(event) {
    let elem = event.target;
    elem.onblur = null;
    let formData = new FormData();
    formData.append('id', elem.id);
    formData.append('content', elem.outerHTML);
    hxl_send_form('admin.php/saveText', formData, elem);
}

function saveEditorContent(event) {
    let elem = event.target;
    let formData = new FormData();
    let id = elem.getAttribute('saving');
    formData.append('id', id);
    formData.append('content', tinymce.activeEditor.getContent());
    hxl_send_form('admin.php/saveText', formData, elem);
}

function makeDuplicate(event) {
    let elem = event.target;
    let formData = new FormData();
    let id = elem.getAttribute('from');
    formData.append('id', id);
    hxl_send_form('admin.php/duplText', formData, elem);
}

function gotInput(event) {
    event.preventDefault();
    event.stopPropagation();
    let elem = event.target;
    if (elem.onblur == null) {
        elem.onblur = dealWithInput;
    }
}

function showContextMenu(event) {
    event.preventDefault();
    console.log(event);
    let t = event.target;
    let fd = new FormData();
    fd.append('id', t.id);
    fd.append('start', true);
    hxl_send_form('admin.php/edit', fd, t);
}

function stopEditor(event) {
    let editor = document.getElementById('edi_tor');
    tinymce.remove();
    editor.close();
    editor.remove();
}

async function startEditor() {
    let editor = document.getElementById('edi_tor');
    await addScript('tinymce/tinymce.min.js');
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
    if (next == '') {
        em.remove();
    } else {
        em.onclick = function () {
            let fd = new FormData();
            fd.append('id', next);
            fd.append('start', true);
            hxl_send_form('admin.php/edit', fd, em);
        };
    }
    let es = document.getElementById('editor_save');
    es.onclick = saveEditorContent;
    let md = document.getElementById('make_duplicate');
    md.onclick = makeDuplicate;
}

function makeEditable() {
    // runs when it is loaded
    let page = document.querySelector('div.page');
    if (page != null) {
        document.querySelectorAll('div.page [id]').forEach(element => {
            if (element.childNodes.length >= 1 && element.firstChild.nodeType === Node.TEXT_NODE && element.firstChild.nodeValue.trim() != '') {
                element.setAttribute('contenteditable', 'true');
            }
        });
        page.oninput = gotInput;
        page.oncontextmenu = showContextMenu;
    }
}

makeEditable();