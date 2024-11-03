<?php
namespace Code;


class  MakeEditor{
    /**
     * @return void
     */
    public static function  Add(): void{
        error_log('showing topbox');
        Login::Check();
        echo <<< EOM
        <div id="topbox" x-action="replace">
        <span onclick="topBoxPage()">Page</span>
        <span onclick="topBoxMedia()">Pictures/Media</span>
        <span onclick="topBoxRewind()">Rewind</span>
        <span onclick="topBoxLogout()">Logout</span>
        <span onclick="topBoxHelp()">Help</span>
        </div>
        <script> 
        document.body.prepend(document.getElementById('topbox'));
        addScript('tinymce/tinymce.min.js');
        addScript('js/editor.js'); 
        </script>
        <style id="editStyle" x-action="replace">
        #_page { cursor: crosshair; }
        #_page [id] { cursor: context-menu; }
        #_page [contenteditable]{ cursor: text; }  
        </style>
        EOM;
    }
    /**
     * @return void
     */
    public static function Remove(): void
    {
        echo <<< EOM
        <div id="topbox" x-action="remove"></div>
        <style id="editStyle" x-action="remove"></style>
        EOM;
    }
}