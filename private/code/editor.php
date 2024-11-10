<?php
namespace Code;

class Editor
{

    private $filename;

    private HtmlDoc $doc;

    public function __construct()
    {
        global $htmlDir;
        error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__);
        $loc = $_SERVER['HTTP_REFERER'];
        $urlPath = explode('/', $loc);
        $fn = $urlPath[count($urlPath) - 1];
        if ($fn == "") {
            $fn = 'index.html';
        }
        $this->filename = $htmlDir . DIRECTORY_SEPARATOR . $fn;
        $this->doc = HtmlDoc::fromFile($this->filename);
    }

    public static function Edit(): void
    {
        Login::Check();
        error_log(print_r($_POST, true));
        $editor = new Editor();
        if (is_null($editor->doc)) {
            http_response_code(404);
            echo 'file not found ' . $_SERVER['HTTP_REFERER'];
            return;
        }
        if (isset($_POST['start'])) {
            $id = $_POST['id'];
            $srcElem = $editor->doc->document->getElementById($id);
            $more_id = '';
            for ($e = $srcElem->parentNode; $e instanceof \DOMElement; $e = $e->parentElement) {
                $pid = $e->getAttribute('id');
                if ($pid != null) {
                    $more_id = $pid;
                    break;
                }
            }
            $html = $editor->doc->getHtmlForId($id);
            $tag = $srcElem->tagName;
            $path = $_SERVER['HTTP_REFERER'] . ': ' . $tag . '#' . $id;
            $editor->showEditor($html, $more_id, $id, $path);
        }

    }
    /**
     * @return void
     */
    public static function Duplicate(): void
    {
    }
    /**
     * @return void
     */
    public static function SaveText(): void
    {
        Login::Check();
        error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__ . '\n' . print_r($_POST, true));
        $editor = new Editor();
        if (is_null($editor->doc)) {
            http_response_code(404);
            echo 'can\'t save this file: ' . $_SERVER['HTTP_REFERER'];
            return;
        }
        $gotContent = $_POST['content'];
        $id=$_POST['id'];
        $newdoc = $editor->save($gotContent,$id);
        echo <<< EOM
        <dialog id="edi_tor" x-action="remove"></dialog>
        <script>makeEditable();</script>
        EOM;
        $newNodes=$newdoc->document->childNodes;
        foreach ($newNodes ?? [] as $node) {
            if (isset($preNode)) {
                $node->setAttribute('x-action', 'after');
                $node->setAttribute('x-id', $preNode);
            } else {
                $node->setAttribute('x-action', 'replace');
            }
            $preNode = $node->getAttribute('id');
            echo $newdoc->getNodeOuterHtml($node);
        }
    }

    /**
     * @return void
     * @param string|bool $content
     * @param mixed $id
     * @param mixed $path
     */
    private function showEditor(string | bool $content, string $more_id, $id, $path): void
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
     * @param mixed $content
     * @param mixed $id
     * @return HtmlDoc
     */
    private function save($content, $id): HtmlDoc
    {
        global $htmlDir;
        error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__);
        $newDoc=new HtmlDoc($content);
        $newDoc->removeEditable();
        foreach (glob($htmlDir . DIRECTORY_SEPARATOR . '*.html', GLOB_NOSORT) as $fn) {
            if (is_link($fn)) {
                continue;
            }
            $htmldoc = HtmlDoc::fromFile($fn);
            $doc=$htmldoc->document;
            $tn = $doc->getElementById($id);
            if (is_null($tn)) {
                continue;
            }
            $newNodes = [];
            foreach ($newDoc->document->childNodes as $nn) {
                $n = $doc->importNode($nn, true);
                $newNodes[] = $n;
            }
            $tn->replaceWith(...$newNodes);
            // local id need to be local for each saved file
            $htmldoc->addMissingIds();
            $htmldoc->save2file();
        }
        return $newDoc;
    }

}
