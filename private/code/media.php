<?php

namespace Code;

class Media
{
    const CHAR2REMOVE = '&<>\':!';
    /**
     * @return void
     */
    public static function Handle(): void
    {
        global $htmlDir;
        error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__);
        if (count($_POST) == 0) {
            self::Show();
            return;
        }
        Login::Check();
        if (isset($_FILES) && count($_FILES) > 0) {
            error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__);
            $files=$_FILES['files'];
            $files=(object) $files;
            for($i=0;$i<count($files->name);$i+=1) 
            {
                $newPath = implode(DIRECTORY_SEPARATOR, [$htmlDir, 'media', $files->name[$i]]);
                if (rename($files->tmp_name[$i], $newPath)) {
                    self::ShowMediaForm($newPath);
                    continue;
                }
                http_response_code(409);
                echo 'can\'t upload that file';
                return;
            }
            self::ShowUpload();
            return;
        }
        if (isset($_POST['filename'])) {
            error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__);
            if($_POST['name']=='delete'){
                self::Remove();
                return;
            }
            self::SafeRename();
            return;
        }
        if (isset($_POST['name'])) {
            error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__);
            match($_POST['name']){
                
                default => self::CantDo(),
            };
        }
    }
    /**
     * @return void
     */
    private static function Show(): void
    {
        global $htmlDir;
        $mediaFiles = $htmlDir . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . '*.*';
        echo <<< EOM
        <dialog id="media_picture" x-action="replace">
        <h1>Pictures and Media</h1>
        <h2>Upload new files</h2>
        EOM;
        self::ShowUpload();
        echo <<< EOM
        <h2>Existing files</h2>
        <div class="formtable" id="file_table">
        <div><span>URL</span><span>Name (click to change)</span><span></span><span>Preview</span></div>
        EOM;
        foreach (glob($mediaFiles) as $filename) {
            self::ShowMediaForm($filename);

        }
        echo <<< EOM
        </div>
        </dialog>
        EOM;
    }
    /**
     * @return void
     */
    private static function ShowUpload(): void
    {
        global $scriptURL;
        echo <<< EOM
        <form id="drop_Area" class="borderbox" action="$scriptURL/media"
            enctype="multipart/form-data" onsubmit="return false;" x-action="replace">
        <label for="files">Choose files or drop files here to upload:</label>
        <br>
        <input type="file" id="files" name="files[]" multiple>
        <input type="submit" value="Upload" name="submit" onclick="hxl_submit_form(event);" >
        </form>
        EOM;
    }
    /**
     * @return void
     * @param mixed $filename
     */
    private static function ShowMediaForm($filename): void
    {
        global $baseURL, $scriptURL;
        $path = explode(DIRECTORY_SEPARATOR, $filename);
        $shortName = $path[count($path) - 1];
        $hash = md5($shortName);
        $shortName = htmlentities($shortName);
        $url = $baseURL . '/media/' . $shortName;
        echo <<< EOM
        <form id="$hash" x-action="append" x-id="file_table" action="$scriptURL/media" onsubmit="return false;">
        <input type="hidden" name="original" value="$shortName">
        <a href="$url">$shortName</a>
        <input name="filename" type="text" onchange="hxl_submit_form(event);" value="$shortName">
        <span><span name="delete" onclick="hxl_submit_form(event);">delete</span></span>
        <img src="$url" alt="$shortName">
        </form>
        EOM;
    }
    /**
     * @return void
     */
    private static function SafeRename(): void
    {
        global $htmlDir;
        error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__ . ' ' . print_r($_POST, true));
        $newName = $_POST['filename'];
        $orig = $_POST['original'];
        $replacementMap = array_fill_keys(str_split(self::CHAR2REMOVE), '');
        $newName = strtr($newName, $replacementMap);
        $hash = md5($orig);
        $origPath = implode(DIRECTORY_SEPARATOR, [$htmlDir, 'media', $orig]);
        $newPath = implode(DIRECTORY_SEPARATOR, [$htmlDir, 'media', $newName]);
        register_shutdown_function([Archive::class, 'SaveState']);
        if (rename($origPath, $newPath)) {
            echo <<<EOM
            <form id="$hash" x-action="remove"></form>
            EOM;
            self::ShowMediaForm($newPath);
            return;
        }
        http_response_code(409);
        echo 'can\'t rename that file';
    }
    /**
     * @return void
     */
    private static function CantDo(): void
    {
        http_response_code(409);
        echo 'can\'t execute that action';
    }
    /**
     * @return void
     */
    private static function Remove(): void
    {
        global $htmlDir;
        error_log(__FILE__.':'.__LINE__. ' '. __FUNCTION__.' '.print_r($_POST,true));
        $orig=$_POST['original'];
        $path=implode(DIRECTORY_SEPARATOR,[$htmlDir,'media',$orig]);
        $hash=md5($orig);
        $rp=realpath($path);
        if(str_starts_with($rp,$htmlDir)){
            unlink($rp);
            echo <<< EOM
            <form id="$hash" x-action="remove"></form>
            EOM;
            return;
        }
        http_response_code(409);
        echo 'can\'t execute that action';
    }
}
