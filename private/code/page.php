<?php

namespace Code;

use DOMElement;

class Page
{

    const ALFABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ23456789';
    /** all about one html file */
    private HtmlDoc $doc;
    /** title of the document */
    private $title;
    /** the meta name=description content */
    private $description;
    /** date and time of last saving */
    private string $saved;

    /**
     * @return void
     */
    public static function Handle(): void
    {
        error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__);
        $p = new Page();
        $doc = $p->doc->document;
        $head = $p->doc->get('//head')->item(0);
        if (isset($_POST['title'])) {
            $title = $_POST['title'];
            $p->title = $title;
            $tns = $p->doc->get('//title');
            // $tns = $p->doc->getElementsByTagName('title');
            if ($tns->length == 0) {
                $tn = $doc->createElement('title', htmlentities($title));
                $head->append($tn);
            } else {
                $tn = $tns->item(0);
                $tn->textContent = htmlentities($title);
            }
            $tn->setAttribute('id', '_title_');
            $p->save();
            $tn->setAttribute('x-action', 'replace');
            $tn->setAttribute('x-id', 'head');
            echo ($p->doc->document->saveHTML($tn));
            $p->showTitleForm(true);
            return;
        }
        if (isset($_POST['description'])) {
            $p->description = $_POST['description'];
            $metas = $p->doc->get('//meta[@name="description"]');
            if ($metas->length == 0) {
                $dn = $doc->createElement('meta');
                $dn->setAttribute('name', 'description');
                $head->append($dn);
            } else {
                $dn = $metas->item(0);
            }
            $dn->setAttribute('content', htmlspecialchars($p->description));
            $dn->setAttribute('id', '_descr_');
            $p->save();
            $dn->setAttribute('x-action', 'replace');
            $dn->setAttribute('x-id', 'head');
            echo( $p->doc->document->saveHTML($dn));
            $p->showDescriptionForm(true);
            return;
        }
        if (isset($_POST['filename'])) {
            $fn = $_POST['filename'];
            $p->safeRename($fn);
            return;
        }
        $postName=$_POST['name'] ?? '';
        if ($postName == 'duplicate') {
            $p->createDuplicate();
            $p->showAllHtmls(true);
            return;
        }
        if($postName=='delete_page'){
            if($p->doc->isIndex){
                http_response_code(403);
                echo "can't delete homepage";
                return;
            }
            $p->deletePage();
            return;
        }
        if($postName == 'make_home'){
            $p->makeHome();
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
        EOM;
    }
    /**
     * @return void
     */
    private function save(): void
    {
        $this->doc->save2file();
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
        if (rename($this->doc->filename, $newFn)) {
            echo <<< EOM
            <script>window.location.href= '${baseURL}${fn}';</script>
            EOM;
        }else{
            error_log(__FILE__.':'.__LINE__. ' '. __FUNCTION__." Can't rename the file $this->doc->filename");
            return;
        }
        if ($this->doc->isIndex) {
            $indexFn=implode(DIRECTORY_SEPARATOR,[$htmlDir, 'index.html']);
            if(is_link($indexFn)){
                if(!unlink($indexFn)){
                    error_log(__FILE__.':'.__LINE__. ' '. __FUNCTION__.' can\'t unlink index.html');
                    return;
                }
                if(!symlink($newFn,$indexFn)){
                    error_log(__FILE__.':'.__LINE__. ' '. __FUNCTION__.' can\'t link');
                    return;
                }
            }
        }
    }
    /**
     * shows a list of html files
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
        $fn = $this->doc->filename;
        $dir = dirname($fn);
        $p = '/^(.+?)(\d*)\.(.*)$/';
        preg_match($p, $this->doc->shortName, $matches);
        $n = (int) $matches[2];
        do {
            $n += 1;
            $tn = $dir . DIRECTORY_SEPARATOR . sprintf('%s%03d.%s', $matches[1], $n, $matches[3]);
        } while (file_exists($tn));
        error_log("duplicating $fn onto $tn");
        $this->doc->filename=$tn;
        $this->doc->replaceAllLocalIds();
        $this->doc->save2file();
    }
    /**
     * @return void
     */
    private function deletePage(): void
    {
        global $baseURL;
        $fn=$this->doc->filename;
        if(is_link($fn)){
            http_response_code(403);
            echo "can't delete a link";
            return;
        }
        if(!unlink($fn)){
            http_response_code(409);
            echo "unlink failed";
            return;
        }
        echo <<<EOM
        <script>window.location.href = "$baseURL";</script>
        EOM;
    }
    /**
     * @return void
     */
    private function makeHome()
    {
        global $htmlDir,$baseURL;
        $indexFn=implode(DIRECTORY_SEPARATOR,[$htmlDir, 'index.html']);
        if(is_link($indexFn)){
            if(!unlink($indexFn)){
                error_log(__FILE__.':'.__LINE__. ' '. __FUNCTION__.' can\'t unlink index.html');
                return;
            }
            if(!symlink($this->doc->filename,$indexFn)){
                error_log(__FILE__.':'.__LINE__. ' '. __FUNCTION__.' can\'t link');
                return;
            }
            echo <<<EOM
            <script>window.location.href = "$baseURL";</script>
            EOM;
        }

    }

}
