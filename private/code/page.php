<?php

namespace Code;

use DOMDocument;

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

    /**
     * @return void
     */
    public static function Handle(): void
    {
        $p = new Page();
        
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
        $tn= $this->doc->getElementsByTagName('title');
        $this->title = $tn[0]?->nodeValue ?? '';
        $xp=new \DOMXPath($this->doc);
        $dn=$xp->query('/head/meta[name="description"]');
        $this->description=$dn[0]?->nodeValue ?? '';
    }
    /**
     * @return void
     */
    private function showDialog(): void
    {
        global $scriptURL;
        $title=htmlentities($this->title);
        $description=htmlentities($this->description);
        echo <<< EOM
        <dialog id="show_page" x-action="replace">
        <h1>Page data '$this->sn'</h1>
        Page details:
        <div class="formtable"> 
        <form action="$scriptURL/page" onsubmit="return false;">
        <label for="title">Page title </label>
        <input id="title" name="title" placeholder="text to be displayed in web browser title line" value="$title" onchange="hxl_submit_form(event)">
        </form>
        <form action="$scriptURL/page" onsubmit="return false;">
        <label for="description">Description </label>
        <input id="description" name="description" placeholder="desription used by search engines" value="$description" onchange="hxl_submit_form(event)">
        </form>
   
        </div>
        </dialog>
        <script>
        let dialog=document.getElementById('show_page');
        dialog.showModal();
        </script>
        EOM;
    }
}
