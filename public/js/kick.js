function addScript(url) {
    if ([...document.getElementsByName('script').values()].some(s => s.src == url))
        return new Promise((resolve, reject) => resolve('already loaded'));
    return new Promise((resolve, reject) => {
        const script = document.createElement("script");
        script.src = url;
        script.type = "text/javascript";
        script.async = false; // Ensure script executes in order, if needed
        script.onload = () => resolve(`Script loaded: ${url}`);
        // Reject the promise if there is an error loading the script
        script.onerror = () => reject(new Error(`Script load error: ${url}`));
        document.head.appendChild(script);
    });
}

window.onload = async function () {
    await addScript('js/htmx-lite.js');
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
    se.id = 'show_error';
    se.onclick = se.close;
    se.innerText = 'no error';
    document.body.append(se);
    let c = document.cookie;
    let simple_web = c.split(';').some(cookie => cookie.trim().startsWith('simple-web='));
    if (simple_web) 
        hxl_send_form('admin.php/makeeditor', null, adminBox);
    
};
