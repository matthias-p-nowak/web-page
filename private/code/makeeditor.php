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
        // document.body.prepend(document.getElementById('topbox'));
        addScript('tinymce/tinymce.min.js');
        addScript('js/editor.js'); 
        </script>
        <style id="edit_style" x-action="replace">
        body { cursor: crosshair; }
        body [id] { cursor: context-menu; }
        body [contenteditable]{ cursor: text; }  
        body::before { content: ""; display: block; height: 1rem; }
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
        <style id="edit_style" x-action="remove"></style>
        EOM;
    }
}