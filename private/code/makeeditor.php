<?php
namespace Code;


class  MakeEditor{
    
    public static function  Add(){
        error_log('showing topbox');
        if(session_status() != PHP_SESSION_ACTIVE){
            http_response_code(404);
            echo <<< EOM
            please login
            EOM;
            return;
        }
        echo <<< EOM
        <div id="topbox" x-action="replace">topbox</div>
        <script> alert('topbox loaded');</script>
        EOM;
    }
}