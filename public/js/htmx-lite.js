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

function hxl_send_form(action, formData, target) {
    fetch(action, { method: "POST", body: formData }).then(response => {
        if (response.status == 200) {
            target.classList.remove('requested');
            target.classList.remove('failed');
            return response.text();
        } else {
            target.classList.remove('requested');
            target.classList.add('failed');
            let se = document.getElementById('showerror');
            se.innerHTML = 'showing errors';
            se.show();
            response.text().then((text) => { se.innerHTML = text; });
            try {
                target.Focus();
            } catch (e) {
                console.error('Error executing target.Focus():', e);
            }
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
        // n.removeAttribute('x-action');
        if (n.hasAttribute('x-id')) {
            // for related elements
            let oid = n.getAttribute('x-id');
            var otherId = document.getElementById(oid);
            // n.removeAttribute('x-id');
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
                if (sameId != null)
                    sameId.replaceWith(n);
                else
                    if (otherId != null)
                        otherId.append(n);
                    else
                        document.body.appendChild(n);
                break;
            default:
                console.log('had no action defined for ', n);
        }
    }
    // run all scripts
    for (const n of div.getElementsByTagName('script')) {
        eval(n.innerText);
    }
}
