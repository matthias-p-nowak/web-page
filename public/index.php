<?php

$site='test-site';
require('../web-app/prep.php');

$entry=new WebApp\Entry();


$res = $_SERVER['PATH_INFO'] ?? '_nothing_';
try { match ($res) {
        '/upgrade' => $entry->Upgrade(),
        default => $entry->Default(),
    };
} catch (Exception $ex) {
    error_log("got exception $ex");
}