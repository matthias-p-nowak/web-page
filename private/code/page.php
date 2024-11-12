<?php

namespace Code;

use DOMElement;

class Page
{

    const ALFABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ23456789';

    private HtmlDoc $doc;
    private $title;
    private $description;

    private string $saved;

    /**
     * @return void
     */
    public static function Handle(): void
    {
        error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__);
        $p = new Page();
        if (isset($_POST['title'])) {
            $title = $_POST['title'];
            $p->title = $title;
            $tns = $p->doc->get('//title');
            $doc = $p->doc->document;
            // $tns = $p->doc->getElementsByTagName('title');
            if ($tns->length == 0) {
                $tn = $doc->createElement('title', htmlentities($title));
                $head=$p->doc->get('//head')[0];
                $head->append($tn);
            } else {

            }
            //     $tn = $p->doc->
            //     $head = $p->doc->getElementsByTagName('head')[0];
            //     $head->append($tn);
            // } else {
            //     $tn = $tns[0];
            //     $tn->textContent = htmlentities($title);
            // }
            // $tn->setAttribute('id', '_title_');
            // $p->save();
            // $tn->setAttribute('x-action', 'replace');
            // $tn->setAttribute('x-id', 'head');
            // $p->show($tn);
            // $p->showTitleForm(true);
            return;
        }
        if (isset($_POST['description'])) {
            // $p->description = $_POST['description'];
            // $xp = new \DOMXPath($p->doc);
            // $metas = $xp->query('//meta[@name="description"]');
            // if ($metas->length == 0) {
            //     $dn = $p->doc->createElement('meta');
            //     $dn->setAttribute('name', 'description');
            //     $head = $p->doc->getElementsByTagName('head')[0];
            //     $head->append($dn);
            // } else {
            //     $dn = $metas[0];
            // }
            // $dn->setAttribute('content', htmlspecialchars($p->description));
            // $dn->setAttribute('id', '_descr_');
            // $p->save();
            // $dn->setAttribute('x-action', 'replace');
            // $dn->setAttribute('x-id', 'head');
            // $p->show($dn);
            // $p->showDescriptionForm(true);
            return;
        }
        if (isset($_POST['filename'])) {
            $fn = $_POST['filename'];
            $p->safeRename($fn);
            return;
        }
        if ($_POST['name'] ?? '' == 'duplicate') {
            $p->createDuplicate();
            $p->showAllHtmls(true);
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
        $doc = HtmlDoc::fromUrl($loc);
        if (is_null($doc)) {
            return;
        }

        $this->doc = $doc;
        $tns = $doc->get('//title');
        $this->title = $tns[0]?->nodeValue ?? '';
        $metas = $doc->get('//meta[@name="description"]');
        $this->description = $metas?->item(0)?->getAttribute('content') ?? '';
        $this->saved = date("Y-m-d H:i:s", filemtime($this->doc->filename));
    }

    /**
     * @return void
     */
    private function showDialog(): void
    {
        global $scriptURL;
        echo <<< EOM
        <dialog id="show_page" x-action="replace">
        <h1>Page data for '{$this->doc->shortName}'</h1>
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
        EOM;
        if ($this->doc->isIndex) {

        } else {
            echo <<< EOM
            <span name="delete_page" onclick="hxl_submit_form(event)">Delete this page</span>
            <span name="make_home" onclick="hxl_submit_form(event)">Make this the home page</span>
            EOM;
        }
        echo <<<EOM
        </form>
        </div>
        <div>
        <h2>Existing pages:</h2>
        EOM;
        $this->showAllHtmls(false);
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
    /**
     * @return void
     */
    private function showDescriptionForm(bool $replace): void
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
    /**
     * @return void
     */
    private function showFileName(bool $replace): void
    {
        global $scriptURL;
        $strReplace = $replace ? 'x-action="replace"' : '';
        $filename = htmlentities($this->doc->shortName);
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
    /**
     * @return void
     * @param mixed $fn
     */
    private function safeRename($fn): void
    {
        global $htmlDir, $baseURL;
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
            // $this->fn = $newFn;
            echo <<< EOM
            <script>window.location.href= '${baseURL}${fn}';</script>
            EOM;
        }

    }
    /**
     * @return void
     */
    private function showAllHtmls(bool $replace): void
    {
        global $htmlDir, $baseURL;
        $strReplace = $replace ? 'x-action="replace"' : '';
        echo "<ul id=\"html_list\" $strReplace>";
        foreach (glob($htmlDir . DIRECTORY_SEPARATOR . '*.html') as $fn) {
            if (is_link($fn)) {
                continue;
            }
            $path = explode(DIRECTORY_SEPARATOR, $fn);
            $sn = $path[count($path) - 1];
            echo "<li><a href=\"${baseURL}${sn}\">$sn</a></li>";
        }
        echo "</ul>";
    }

    /**
     * @return void
     */
    private function createDuplicate(): void
    {
        $fn = $this->fn;
        $dir = dirname($fn);
        $p = '/^(.+?)(\d*)\.(.*)$/';
        preg_match($p, $this->sn, $matches);
        $n = (int) $matches[2];
        do {
            $n += 1;
            $tn = $dir . DIRECTORY_SEPARATOR . sprintf('%s%03d.%s', $matches[1], $n, $matches[3]);
        } while (file_exists($tn));
        error_log("duplicating $fn onto $tn");
        // copy($fn,$tn); - need to load and replace _id's
        // $xp=new \DOMXPath($this->doc);
        // $nodes=$xp->query('//*[@id[starts-with(.,"_")]]');
        // foreach($nodes as $node){
        //     $node->setAttribute('id','_'.Editor::getRandomId());
        // }
    }

}
