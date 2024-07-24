<?php
// just in case http2 is supported
header('Link: <' . $baseURL . 'main.css>; rel=preload; as=style', false);
header('Link: <' . $baseURL . 'js/htmx-lite.js>; rel=preload; as=script', false);
$sc = \WebApp\Config::GetConfig();
?>
<!DOCTYPE html>
<!-- main.php -->
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="cleartype" content="on" />
<!-- Responsive and mobile friendly stuff -->
<meta name="HandheldFriendly" content="True" />
<meta name="MobileOptimized" content="320" />
<link rel="stylesheet" type="text/css" href="<?=$baseURL?>main.css" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="icon" type="image/x-icon" href="<?=$baseURL?>favicon.ico" />
<title><?=$sc->title ?? $arg->title ?? '-no title set-'?></title>
</head>
<body>
    <?=view('admin/adminbar', $arg)?>
 <header><?=view('header', $arg)?></header>
 <main>
 <?php
 if (!isset($arg->view) && isset($arg->content) && isset($_SESSION['Level']) && ($_SESSION['Level']>0)){
    error_log('editor?');
    echo '<div class=autohide><a href="' . $scriptURL . '/editpage?pg=' . $arg->content . '">edit</a></div>';
 } 
 ?>
<?=isset($arg->content) ? show($arg->content) :
    (isset($arg->view, $arg) ? view($arg->view, $arg) : 
    '### nothing to show')?>
</main>
<footer><?= view('footer',$arg) ?></footer>
</body>
</html> 