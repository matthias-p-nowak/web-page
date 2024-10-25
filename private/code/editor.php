<?php
namespace Code;

class Editor
{
    /**
     * @return void
     */
    public static function SaveText(): void
    {
        Login::Check();
        error_log('edit text: '.print_r($_POST,true));
    }

    public static function Edit(): void
    {
        global $htmlDir;
        Login::Check();
        error_log(print_r($_POST, true));
        $id = $_POST['id'];
        $loc = $_POST['loc'];
        $urlPath = explode('/', $loc);
        $fn = $urlPath[count($urlPath) - 1];
        if ($fn == "") {
            $fn = 'index.html';
        }
        if (isset($_POST['start'])) {
            $content = file_get_contents($htmlDir . DIRECTORY_SEPARATOR . $fn);
            $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
            $srcDoc = new \DOMDocument();
            $srcDoc->encoding = 'utf-8';
            libxml_clear_errors();
            $srcDoc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $srcElem = $srcDoc->getElementById($id);
            if ($srcElem == null) {
                http_response_code(404);
                echo "can't find the thing";
                return;
            }
            $str = $srcDoc->saveHTML($srcElem);
            $more_id = '';
            for ($sElem = $srcElem->parentNode; $sElem != null; $sElem = $sElem->parentNode) {
                if (!$sElem instanceof \DOMElement) {
                    break;
                }
                $pid = $sElem->getAttribute('id');
                if ($pid != null) {
                    $more_id = $pid;
                    break;
                }
            }
            echo <<< EOM
            <dialog id="edi_tor" x-action="replace" draggable >
            <div id="editor_choices">
            <span id="editor_more" next="${more_id}">More to edit</span>
            <span id="make_duplicate" from="${id}">Duplicate</span>
            <span id="editor_save" saving="${id}">Save</span>
            <span id="editor_cancel">Cancel</span>
            </div>
            <div id="editor_content">$str</div>
            <tiny>Editor for ${fn}:${id}</tiny>
            </dialog>
            <script>
            startEditor();
            </script>
            EOM;
            return;
        }else{
            error_log('no start');
        }
    }
    
    /**
     * @return void
     */
    public static function Duplicate(): void
    {
    }
}
