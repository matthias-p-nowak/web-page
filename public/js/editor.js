function editor_submit(event){
    let form = event.target.form ?? event.target.closest('form');
    let action = form.action;
    let formData = new FormData(form);
    formData.append('content',tinymce.activeEditor.getContent());
    fetch(action, { method: "POST", body: formData }).then(response => {
        if (response.status == 200) {
            event.target.classList.remove('requested');
            event.target.classList.remove('failed');
            return response.text();
        } else {
            event.target.classList.remove('requested');
            event.target.classList.add('failed');
            throw new Error('Network response was not ok');
        }
    }).then(data => {
        if (data.trim().length > 0) {
            hxl_process_body(data);
        } else {
            console.log('empty body returned');
        }
    }).catch(error => {
        console.error("An error occurred:", error);
    });
}
