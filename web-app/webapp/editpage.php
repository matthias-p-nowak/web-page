<?php

namespace WebApp;

$approved_attributes = ['id', 'class', 'style', 'href', 'src', 'name'];
$approved_elements = [
    'div', 'span', 'img', 'pre',
    'table', 'tr', 'td', 'th', 'tbody',
    'ul', 'ol', 'li',
    'b', 'p', 'em', 'strong', 's', 'strong',
    'sub', 'sup', 'blockquote',
    'hr', 'h1', 'h2', 'h3', 'h4', 'h5',
    'code',
];

function cleanNodes($node)
{
    global $approved_elements, $approved_attributes;
    switch ($node->nodeType) {
        case XML_ELEMENT_NODE:
            foreach ($node->attributes as $attr) {
                $an = $attr->nodeName;
                if (in_array($an, $approved_attributes)) {
                    $av = $attr->value;
                    if (str_contains($av, 'javascript')) {
                        $node->removeAttribute($an);
                    }

                } else {
                    error_log('removing attribute ' . $an);
                    $node->removeAttribute($an);
                }
            }
            foreach ($node->childNodes as $cn) {
                if ($cn->nodeType == XML_TEXT_NODE) {
                    continue;
                }

                if (!in_array($cn->tagName, $approved_elements)) {
                    error_log('removing tag ' . $cn->tagName);
                    $cn->remove();
                    continue;
                }
                cleanNodes($cn);
            }
            break;
        default:
            return;
    }
}

class EditPage
{
    public function __construct()
    {
        Db\AppUser::EditorCheck();
    }
    public function EditPage()
    {
        if (isset($_POST['content'])) {
            error_log(print_r($_POST, true));
            $raw = $_POST['content'];
            $hash = $_POST['page2edit'];
            $temp_dom = new \DOMDocument('1.0', 'UTF-8');
            $temp_dom->loadHTML('<?xml encoding="UTF-8">' . $raw);
            $temp_dom->normalize();
            foreach ($temp_dom->getElementsByTagName('body') as $body) {
                cleanNodes($body);
                foreach ($body->childNodes as $node) {
                    // error_log(print_r($node,true));
                    $res[] = $temp_dom->saveHTML($node);
                }
            }
            $res = \implode('', $res);
            $db = Db\DbCtx::GetInstance();
            $pc = $db->FindRow('PageContent', ['Hash' => $hash]);
            if (!$pc) {
                $pc = new Db\PageContent();
                $pc->Hash = $hash;
            }
            $pc->Content = $res;
            $db->StoreRow($pc);
            $fn = strtolower($hash);
            $fn = __DIR__ . '/content/' . $fn . '.html';
            file_put_contents($fn, $res);
            echo '<div x-action="replace" id="preview">' . $res . '</div>';
        } else {
            Db\AppUser::EditorCheck();
            $view = new ShowView();
            $view->page2edit=$_GET['pg'];
            $view->ShowForm('admin/editpage');
        }
    }
}
