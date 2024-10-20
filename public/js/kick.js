window.onload = function () {

  function addAdminBox(){
    const adminBox = document.createElement('div');
    adminBox.id = 'adminBox';
    document.body.append(adminBox);

    var kickstart = function (event) {
      event.preventDefault();
      hxl_send_form('admin.php/login', null, adminBox);
    };

    adminBox.addEventListener('contextmenu', kickstart);
    console.log('admin box added');
    const se= document.createElement('dialog');
    se.id='showerror';
    se.onclick=se.close;
    se.innerText='no error';
    document.body.append(se); 
  }

  if (typeof hxl_send_form === 'function'){
    console.log('htmx-lite was there already');
    addAdminBox();
  }else{
    console.log('adding htmx-lite script');
    const s1=document.createElement('script');
    s1.src='js/htmx-lite.js';
    s1.onload=addAdminBox;
    document.head.appendChild(s1);
  }
  
};
