<?php
namespace Code;


class  MakeEditor{
    /**
     * @return void
     */
    public static function  Add(): void{
        error_log('showing topbox');
        if(session_status() != PHP_SESSION_ACTIVE){
            http_response_code(404);
            echo <<< EOM
            please login
            EOM;
            return;
        }
        echo <<< EOM
        <div id="topbox" x-action="replace">
        <span onclick="topBoxPage()">Page</span>
        <span onclick="topBoxMedia()">Pictures/Media</span>
        <span onclick="topBoxLogout()">Logout</span>
        </div>
        <script> 
        document.body.prepend(document.getElementById('topbox'));
        addScript('js/editor.js'); 
        </script>
        EOM;
    }
    /**
     * @return void
     */
    public static function Remove(): void
    {
        echo <<< EOM
        <div id="topbox" x-action="remove"></div>
        EOM;
    }
}