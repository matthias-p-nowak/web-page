function addScript(url) {
    const script = document.createElement("script");
    script.src = url;
    script.type = "text/javascript";
    script.async = false; // Ensure script executes in order, if needed
    document.body.appendChild(script);
    return script;
}

window.onload = function () {

    function addAdminBox() {
        const adminBox = document.createElement('div');
        adminBox.id = 'adminBox';
        document.body.append(adminBox);

        var kickstart = function (event) {
            event.preventDefault();
            hxl_send_form('admin.php/login', null, adminBox);
        };

        adminBox.addEventListener('contextmenu', kickstart);
        console.log('admin box added');
        const se = document.createElement('dialog');
        se.id = 'showerror';
        se.onclick = se.close;
        se.innerText = 'no error';
        document.body.append(se);
        let c = document.cookie;
        let simple_web = c.split(';').some(cookie => cookie.trim().startsWith('simple-web='));
        if (simple_web) {
            hxl_send_form('admin.php/makeeditor', null, adminBox);
        }
    }

    if (typeof hxl_send_form === 'function') {
        console.log('htmx-lite was there already');
        addAdminBox();
    } else {
        console.log('adding htmx-lite script');
        addScript('js/htmx-lite.js').onload=addAdminBox;
    }

};
