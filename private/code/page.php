<?php

namespace Code;

use DOMDocument;
use DOMElement;

class Page
{

    /** determined filename (long) */
    private string $fn;
    /** short name */
    private string $sn;
    /** index.html is linked to this */
    private bool $isHome;

    private DOMDocument $doc;
    private $title;
    private $description;

    private string $saved;

    /**
     * @return void
     */
    public static function Handle(): void
    {
        $p = new Page();
        if (isset($_POST['title'])) {
            $title = $_POST['title'];
            $p->title = $title;
            $tns = $p->doc->getElementsByTagName('title');
            if ($tns->length == 0) {
                $tn = $p->doc->createElement('title', htmlentities($title));
                $head = $p->doc->getElementsByTagName('head')[0];
                $head->append($tn);
            } else { 
                $tn = $tns[0];
                $tn->textContent = htmlentities($title);
            }
            $tn->setAttribute('id', '_title_');
            $p->save();
            $tn->setAttribute('x-action', 'replace');
            $tn->setAttribute('x-id', 'head');
            $p->show($tn);
            $p->showTitleForm(true);
            return;
        }
        $p->showDialog();
    }

    public function __construct()
    {
        global $htmlDir;
        error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__);
        $loc = $_SERVER["HTTP_REFERER"];
        $urlPath = explode('/', $loc);
        $fn = $urlPath[count($urlPath) - 1];
        $ih = readlink($htmlDir . DIRECTORY_SEPARATOR . 'index.html');
        if ($fn == "") {
            $fn = $ih;
        }
        $this->sn = $fn;
        $this->isHome = $ih == $this->sn;
        $this->fn = $htmlDir . DIRECTORY_SEPARATOR . $fn;
        //
        $content = file_get_contents($this->fn);
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
        $this->doc = new \DOMDocument();
        $this->doc->encoding = 'utf-8';
        libxml_clear_errors();
        $this->doc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $tn = $this->doc->getElementsByTagName('title');
        $this->title = $tn[0]?->nodeValue ?? '';
        $xp = new \DOMXPath($this->doc);
        $dn = $xp->query('/head/meta[name="description"]');
        $this->description = $dn[0]?->nodeValue ?? '';
        $this->saved = date("Y-m-d H:i:s", filemtime($this->fn));
    }
    /**
     * @return void
     */
    private function showDialog(): void
    {
        global $scriptURL;

        echo <<< EOM
        <dialog id="show_page" x-action="replace">
        <h1>Page data '$this->sn'</h1>
        Page details:
        <div class="formtable">
        <div>
        <span>Setting</span>
        <span>Data</span>
        <span>Saved</span>
        </div>
        EOM;
        $this->showTitleForm(false);
        $this->showDescriptionForm(false);
        echo <<< EOM
        </div>
        </dialog>
        <script>
        let dialog=document.getElementById('show_page');
        dialog.showModal();
        </script>
        EOM;
    }
    /**
     * @return void
     */
    private function save(): void
    {
        $this->doc->saveHTMLFile($this->fn);
        $this->saved = date("Y-m-d H:i:s");
    }
    /**
     * @return void
     */
    private function showTitleForm(bool $replace): void
    {
        global $scriptURL;
        $title = htmlentities($this->title);
        $strReplace = $replace ? 'x-action="replace"' : '';
        echo <<< EOM
        <form id="form_title" action="$scriptURL/page" onsubmit="return false;" $strReplace>
        <label for="title">Page title </label>
        <input id="title" name="title" placeholder="text to be displayed in web browser title line"
        value="$title" onchange="hxl_submit_form(event)">
        <span>$this->saved</span>
        </form>
        EOM;
    }
    /**
     * @return void
     */
    private function show(DOMElement $node): void
    {
        echo ($this->doc->saveHTML($node));
    }

    private function showDescriptionForm(bool $replace)
    {
        global $scriptURL;
        $description = htmlentities($this->description);
        $strReplace = $replace ? 'x-action="replace"' : '';
        echo <<< EOM
        <form id="form_description" action="$scriptURL/page" onsubmit="return false;" $strReplace>
        <label for="description">Description </label>
        <input id="description" name="description" placeholder="description used by search engines" value="$description" 
        onchange="hxl_submit_form(event)">
        <span>$this->saved</span>
        </form>
        EOM;
    }
}
