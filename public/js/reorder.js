function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    const form = ev.target.form ?? ev.target.closest('form');
    console.log('setting data to ' + form.id);
    ev.dataTransfer.setData("text", form.id);
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
    let form = ev.target.form ?? ev.target.closest('form');
    form.classList.add('requested');
    form.classList.remove('failed');
    console.debug('dropped ' + data + ' onto ' + form.id);
    let action = form.action;
    let formData = new FormData(form);
    formData.append('moved', data);
    formData.append('onto', form.id);
    hxl_send_form(action, formData, form);
}

function addDrops() {
    const allDrops = document.querySelectorAll('[draggable]');
    allDrops.forEach(elem => {
        elem.ondragstart = drag;
        elem.ondragover = allowDrop;
        elem.ondrop = drop;
    });
}
addDrops();