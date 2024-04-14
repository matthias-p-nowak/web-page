

function hxl_submit_form(event) {
    let form = event.target.form ?? event.target.closest('form');
    let action=form.action;
    let formData = new FormData(form);
    if(event.target.hasAttribute('name')){
        formData.append('name',event.target.getAttribute('name'));
    }
    event.target.classList.add('requested');
    fetch(action, { method: "POST", body: formData })
        .then(response => {
            if (response.status == 200) {
                event.target.classList.remove('requested');
                event.target.classList.remove('failed');
                return response.text();
            } else {
                event.target.classList.remove('requested');
                event.target.classList.add('failed');
                throw new Error('Network response was not ok');
            }
        })
        .then(data => {
            if (data.trim().length > 0) {
                hxl_process_body(data);
            } else {
                console.log('empty body returned');
            }
        })
        .catch(error => { console.error("An error occurred:", error); })
        ;
}

function hxl_process_body(body) {
    const div = document.createElement('div');
    div.innerHTML = body;
    var nodes = div.querySelectorAll("[x-action]");
    for (const n of nodes) {
            let attr = n.getAttribute('x-action');
            let sameId = document.getElementById(n.id);
        n.removeAttribute('x-action');
            if (n.hasAttribute('x-id')) {
                let oid = n.getAttribute('x-id');
                var otherId = document.getElementById(oid);
            n.removeAttribute('x-id');
            }
            switch (attr) {
                case 'after':
                    otherId.after(n);
                    break;
                case 'append':
                    otherId.append(n);
                    break;
                case 'before':
                    otherId.before(n);
                    break;
                case 'prepend':
                    otherId.prepend(n);
                    break;
                case 'remove':
                    sameId.remove();
                    break;
                case 'replace':
                    sameId.replaceWith(n);
                    break;
                default:
                    console.log('had no action defined for ', n);
        }
    }

}

console.log('htmx-lite loaded');

