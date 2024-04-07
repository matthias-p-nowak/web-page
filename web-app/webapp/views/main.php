<?php
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="cleartype" content="on" />
<!-- Responsive and mobile friendly stuff -->
<meta name="HandheldFriendly" content="True" />
<meta name="MobileOptimized" content="320" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" type="text/css" href="main.css" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<title><?= $arg->title ?? '-no title set-' ?></title>
</head>
<body>
    <?= view('admin', $arg) ?>
    <?= show($arg->content) ?>
</body>
</html>