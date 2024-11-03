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
        if (isset($_POST['description'])) {
            $p->description = $_POST['description'];
            $xp = new \DOMXPath($p->doc);
            $metas = $xp->query('//meta[@name="description"]');
            if ($metas->length == 0) {
                $dn = $p->doc->createElement('meta');
                $dn->setAttribute('name', 'description');
                $head = $p->doc->getElementsByTagName('head')[0];
                $head->append($dn);
            } else {
                $dn = $metas[0];
            }
            $dn->setAttribute('content', htmlspecialchars($p->description));
            $dn->setAttribute('id', '_descr_');
            $p->save();
            $dn->setAttribute('x-action', 'replace');
            $dn->setAttribute('x-id', 'head');
            $p->show($dn);
            $p->showDescriptionForm(true);
            return;
        }
        if (isset($_POST['filename'])) {
            $fn = $_POST['filename'];
            $p->SafeRename($fn);
            return;
        }
        if (isset($_POST['name'])) {
            error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__);
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
        $metas = $xp->query('//meta[@name="description"]');
        if ($metas->length > 0) {
            $dn = $metas[0];
            $descr = $dn->getAttribute('content');
            $this->description = $descr ?? '';
        } else {
            $this->description = '';
        }
        $this->saved = date("Y-m-d H:i:s", filemtime($this->fn));
    }
    /**
     * @return void
     */
    private function showDialog(): void
    {
        global $scriptURL, $htmlDir,$baseURL;
        echo <<< EOM
        <dialog id="show_page" x-action="replace">
        <h1>Page data for '$this->sn'</h1>
        <h2>Page details:</h2>
        <div class="formtable">
        <div>
        <span>Setting</span>
        <span>Data</span>
        <span>Saved</span>
        </div>
        EOM;
        $this->showTitleForm(false);
        $this->showDescriptionForm(false);
        $this->showFileName(false);
        echo <<< EOM
        </div>
        <h2>Further actions</h2>
        <div>
        <form action="$scriptURL/page" onsubmit="return false">
        <span name="duplicate" onclick="hxl_submit_form(event)">Duplicate</span>
        <span name="edit_style" onclick="hxl_submit_form(event)">Edit page style</span>
        <span name="delete_page" onclick="hxl_submit_form(event)">Delete this page</span>
        </form>
        </div>
        <div>
        <h2>Existing pages:</h2>
        <ul>
        EOM;
        foreach (glob($htmlDir . DIRECTORY_SEPARATOR . '*.html') as $fn) {
            if (is_link($fn)) {
                continue;
            }
            $path = explode(DIRECTORY_SEPARATOR, $fn);
            $sn = $path[count($path) - 1];
            echo "<li><a href=\"$baseURL/$sn\">$sn</a></li>";
        }
        echo <<< EOM
        </ul>
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
        register_shutdown_function([Archive::class, 'SaveState']);
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
        <label for="description">Description</label>
        <input id="description" name="description" placeholder="description used by search engines" value="$description"
        onchange="hxl_submit_form(event)">
        <span>$this->saved</span>
        </form>
        EOM;
    }
    private function showFileName(bool $replace)
    {
        global $scriptURL;
        $strReplace = $replace ? 'x-action="replace"' : '';
        $filename = htmlentities($this->sn);
        echo <<< EOM
        <form id="form_filename" action="$scriptURL/page" onsubmit="return false;" $strReplace>
        <label for="filename">Filename</label>
        <input id="filename" name="filename" placeholder="filename - is part of url" value="$filename"
        title="be careful, this changes the filename on the server"
        onchange="hxl_submit_form(event)">
        <span>$this->saved</span>
        </form>
        EOM;
    }

    private function SafeRename($fn)
    {
        global $htmlDir;
        if (str_contains($fn, '..')) {
            http_response_code(400);
            echo 'attempting to reach parent directory';
            return;
        }
        $newFn = implode(DIRECTORY_SEPARATOR, [$htmlDir, $fn]);
        if (!str_starts_with($newFn, $htmlDir)) {
            http_response_code(400);
            echo 'attempting move file to ' . $newFn;
            return;
        }
        if (file_exists($newFn)) {
            http_response_code(400);
            echo 'attempting to overwrite ' . $newFn;
            return;
        }
        if ($this->isHome) {
            http_response_code(400);
            echo 'attempting to change landing page';
            return;
        }
        if (rename($this->fn, $newFn)) {
            $this->fn = $newFn;
        }

    }
}
