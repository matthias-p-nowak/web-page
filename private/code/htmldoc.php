<?php
namespace Code;

use DOMDocument;

class HtmlDoc
{

    /** easy to read different characters for id's */
    const ALFABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ23456789';
    const IgnoreIdElements = ['hr', 'br'];

    private string $filename;
    public DOMDocument $document;

    public function __construct(private string $content)
    {
        $this->document = new \DOMDocument();
        $this->document->encoding = 'utf-8';
        libxml_clear_errors();
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
        $this->document->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    }

    /**
     * @return null|HtmlDoc
     * @param mixed $filename
     */
    public static function fromFile($filename): ?HtmlDoc
    {
        error_log(__FILE__ . ':' . __LINE__ . ' ' . __FUNCTION__);
        if (!file_exists($filename)) {
            return null;
        }
        $content = file_get_contents($filename);
        $hd=new HtmlDoc($content);
        $hd->filename=$filename;
        return $hd;
    }

    public static function getRandomId(): string
    {
        $l = strlen(SELF::ALFABET);
        $rv = '';
        for ($j = 0; $j < 16; $j += 1) {
            $i = random_int(0, $l - 1);
            $rv .= SELF::ALFABET[$i];
        }
        return $rv;
    }

    public function addMissingIds(): void
    {
        $xp = new \DOMXPath($this->document);
        $nodes = $xp->query('//body//*[not(@id)]');
        if ($nodes) {
            foreach ($nodes as $node) {
                if (!in_array($node->tagName, self::IgnoreIdElements)) {
                    $node->setAttribute('id', '_' . self::getRandomId());
                }
            }
        }
    }
    /**
     * @return void
     */
    public function replaceAllLocalIds(): void
    {
        $xp = new \DOMXPath($this->document);
        $nodes = $xp->query('//body//*[not(@id) or starts-with(@id, \'_\')]');
        if ($nodes) {
            foreach ($nodes as $node) {
                if (!in_array($node->tagName, self::IgnoreIdElements)) {
                    $node->setAttribute('id', '_' . self::getRandomId());
                }
            }
        }
    }
    /**
     * returns plain HTML for the element that has that id
     * @return string|bool
     */
    public function getHtmlForId(string $id): string | bool
    {
        $e = $this->document->getElementById($id);
        return $this->document->saveHTML($e);
    }
    /**
     * @return void
     */
    public function removeEditable(): void
    {
        $xp = new \DOMXPath($this->document);
        foreach ($xp->query('//*[@contenteditable]') as $n) {
            $n->removeAttribute('contenteditable');
        }
    }
    /**
     * @return void
     */
    public function save2file(): void
    {
        $this->document->saveHTMLFile($this->filename);
        register_shutdown_function([Archive::class, 'SaveState']);
    }
    /**
     * @return void
     */
    public static function ReIndex(): void
    {
        global $htmlDir;
        error_log(__FILE__.':'.__LINE__. ' '. __FUNCTION__);
        foreach (glob($htmlDir . DIRECTORY_SEPARATOR . '*.html', GLOB_NOSORT) as $fn) {
            if (is_link($fn)) {
                continue;
            }
            $htmldoc = HtmlDoc::fromFile($fn);
            $htmldoc->replaceAllLocalIds();
            $htmldoc->save2file();
        }
    }
    /**
     * @param mixed $node
     * @return string|bool
     */
    public function getNodeOuterHtml($node): string|bool
    {
        error_log(__FILE__.':'.__LINE__. ' '. __FUNCTION__);
        return $this->document->saveHTML($node);
    }
}
