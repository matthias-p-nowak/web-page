<?php

namespace Code;

class Rewind
{
    public static function Rewind(): void
    {
        global $htmlDir, $scriptURL;
        Login::Check();
        error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__);
        if(isset($_POST['rewind'])){
            $t= (int) $_POST['rewind'];
            $thisUrl=$_SERVER['HTTP_REFERER'];
            Archive::Rewind($t);
            echo <<< EOM
            <script>window.location.href = "$thisUrl";</script>
            EOM;
            return;
        }
        echo <<< EOM
        <dialog id="rewind" x-action="replace" x-id="body">
        <h2>Rewind to an earlier version</h2>
        Select a date and time, html, media and style files that existed at that time will be restored, overwriting the current version.
        
        <form action="$scriptURL/rewind" onsubmit="return false;">
        <label for="rewind_select">Select date/time</label>
        <select name="rewind" id="rewind_select">
        EOM;
        $mtimes=Archive::ReadMtimes();
        $dt = new \DateTime();
        foreach($mtimes as $idx => $t){
            $dt=$dt->setTimestamp($t);
            $dts=$dt->format('Y-m-d H:i:s');
            echo <<<EOM
            <option value="$t">$dts</option>
            EOM;
        }
        echo <<< EOM
        </select>
        <input type="submit" value="Submit" onclick="hxl_submit_form(event)">
        </form>
        </dialog>
        EOM;
    }
}
