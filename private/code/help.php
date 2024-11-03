<?php
namespace Code;

class Help{
    public static function ShowHelp(){
        echo <<< EOM
        <dialog id="show_help" x-action="replace">
        <h1>Simple web page help</h1>
        <ul>
        <li><b>Page:</b> edit metadata like file name, title, description, styles, home page and also gives an overview over pages</li>
        <li><b>Pictures/Media:</b> manage pictures and media like PDF's including their url</li>
        <li><b>Logout:</b> return to normal view of the web-site like any other visitor</li>
        <li><b>Rewind:</b> restore web-site to an earlier state</li>
        </ul>
        Press 'Escape' to get out of editor or this help!
        </dialog>
        <script>
        let d=document.getElementById('show_help');
        d.showModal();
        </script>
        EOM;
    }
}