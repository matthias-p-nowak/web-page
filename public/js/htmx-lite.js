// add this to the input elements of the form that should commence the htmx-lite action
function hxl_submit_form(event) {
    event.target.classList.add('requested');
    event.target.classList.remove('failed');
    let form = event.target.form ?? event.target.closest('form');
    let action = form.action;
    let formData = new FormData(form);
    if (event.target.hasAttribute('name')) {
        formData.append('name', event.target.getAttribute('name'));
    }
    hxl_send_form(action, formData, event.target);
}

async function hxl_send_form(action, formData, target) {
    try {
        let response = await fetch(action, { method: "POST", body: formData });
        if (response.status == 200) {
            target.classList.remove('requested');
            target.classList.remove('failed');
            let data = await response.text();
            if (data.trim().length > 0) {
                hxl_process_body(data);
            } else {
                console.log('empty body returned');
            }
        } else {
            target.classList.remove('requested');
            target.classList.add('failed');
            const activeDialog = document.querySelector('dialog[open]');
            if (activeDialog) {
                activeDialog.close();
            }
            let se = document.getElementById('show_error');
            se.innerHTML = 'showing errors';
            let text = await response.text();
            se.innerHTML = text;
            se.showModal();
            target?.Focus?.();
        }
    }
    catch (error) {
        console.log(error);
        let se = document.getElementById('show_error');
        se.innerHTML = `${error.name}: ${error.message}`;
        se.show();
    }
}

// the returned text is html with additional tags
function hxl_process_body(body) {
    // create an unattached element
    const div = document.createElement('div');
    // parses html but also drops tags that are not proper in this structure like tr without a table-parent
    div.innerHTML = body;
    // go through those with actions
    for (const n of div.querySelectorAll("[x-action]")) {
        let attr = n.getAttribute('x-action');
        if (n.id) {
            // for replacements/deletes
            var sameId = document.getElementById(n.id);
        }
        var oid = '';
        if (n.hasAttribute('x-id')) {
            // for related elements
            oid = n.getAttribute('x-id');
            var otherId = document.getElementById(oid);
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
                if (sameId != null)
                    sameId.remove();
                else
                    console.log(`element ${n.id} was not found`);
                break;
            case 'replace':
                if (sameId != null)
                    sameId.replaceWith(n);
                else
                    if (otherId != null)
                        otherId.append(n);
                    else
                        if (oid == 'head')
                            document.head.appendChild(n);
                        else
                            document.body.appendChild(n);
                break;
            default:
                console.log('had no action defined for ', n);
        }
        if(n.tagName == 'DIALOG' && attr != 'remove'){
            n.showModal();
        }
    }
    for (const n of div.getElementsByTagName('script')) {
        eval(n.innerText);
    }
}
