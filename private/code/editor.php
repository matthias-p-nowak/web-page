<?php
namespace Code;

use DOMDocument;
use DOMElement;

class Editor
{
    const ALFABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ23456789';
    private string $fn; // determined filename 
    private DOMDocument $srcDoc; // complete loaded document
    private ?DOMElement $srcElem; // specified source element
    private string $id; // specified id

    private function __construct()
    {
        error_log('creating an object');
    }

    /**
     * @return void
     */
    public static function SaveText(): void
    {
        Login::Check();
        error_log('edit text: ' . print_r($_POST, true));
        $editor=new Editor();
        $editor->fetch();
    }

    public static function Edit(): void
    {
        Login::Check();
        error_log(print_r($_POST, true));
        $editor=new Editor();
        $editor->fetch();
        if (isset($_POST['start'])) {
            $str = $editor->srcDoc->saveHTML($editor->srcElem);
            $more_id = '';
            for ($sElem = $editor->srcElem->parentNode; $sElem != null; $sElem = $sElem->parentNode) {
                if (!$sElem instanceof \DOMElement) {
                    break;
                }
                $pid = $sElem->getAttribute('id');
                if ($pid != null) {
                    $more_id = $pid;
                    break;
                }
            }
            $tag=$editor->srcElem->tagName;
            $path=$editor->fn . ': '. $editor->srcElem->tagName .'#'. $editor->id;
            self::showEditor($str, $more_id, $editor->id, $path);
            return;
        } else {
            error_log('no start');
        }
    }

    /**
     * @return void
     */
    public static function Duplicate(): void
    {
        Login::Check();
        $editor=new Editor();
        $editor->fetch();
        // $newElem= $srcDoc->importNode($srcElem, true);
        $newElem = $editor->srcElem->cloneNode(true);
        if ($newElem) {
            self::ReplaceIds($newElem);
            $editor->srcElem->after($newElem);
            $editor->srcDoc->saveHTMLFile($editor->fn);
        }
        $parent = $editor->srcElem->parentNode;
        if ($parent instanceof \DOMElement  && $parent != null) {
            $parent->setAttribute('x-action', 'replace');
            $src = $editor->srcDoc->saveHTML($parent);
            echo <<< EOM
           <dialog id="edi_tor" x-action="remove" ></dialog>
           EOM;
            echo $src;
            return;
        }

    }

    public static function ReplaceIds(\DOMElement $node): void
    {
        $id = $node->getAttribute('id');
        if ($id != null && str_starts_with($id, '_')) {
            $node->setAttribute('id', '_' . self::GetRandomId());
        }
        foreach ($node->childNodes as $cn) {
            if ($cn instanceof \DOMElement) {
                self::ReplaceIds($cn);
            }
        }
    }

    public static function GetRandomId(): string
    {
        $l = strlen(SELF::ALFABET);
        $rv = '';
        for ($j = 0; $j < 16; $j += 1) {
            $i = random_int(0, $l - 1);
            $rv .= SELF::ALFABET[$i];
        }
        return $rv;
    }

    private static function showEditor(string $content, string $more_id, string $id, string $path): void
    {
        echo <<< EOM
        <dialog id="edi_tor" x-action="replace" draggable >
        <div id="editor_choices">
        <span id="editor_more" next="${more_id}">More to edit</span>
        <span id="make_duplicate" from="${id}">Duplicate</span>
        <span id="editor_save" saving="${id}">Save</span>
        <span id="editor_cancel">Cancel</span>
        </div>
        <div id="editor_content">$content</div>
        <tiny>Editor for ${path}</tiny>
        </dialog>
        <script>
        startEditor();
        </script>
        EOM;
    }
    /**
     * @return void
     */
    private function fetch(): void
    {
        global $htmlDir;
        $this->id = $_POST['id'];
        $loc = $_POST['loc'];
        $urlPath = explode('/', $loc);
        $fn = $urlPath[count($urlPath) - 1];
        if ($fn == "") {
            $fn = 'index.html';
        }
        $this->fn = $htmlDir . DIRECTORY_SEPARATOR . $fn;
        $content = file_get_contents($this->fn);
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
        $this->srcDoc = new \DOMDocument();
        $this->srcDoc->encoding = 'utf-8';
        libxml_clear_errors();
        $this->srcDoc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $this->srcElem = $this->srcDoc->getElementById($this->id);
    }

}
