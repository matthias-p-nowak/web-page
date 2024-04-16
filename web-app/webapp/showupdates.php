<?php
namespace WebApp;

require_once 'functions.php';

class ShowUpdates{
    function ShowUpdate($form)
    {
        \view($form,$this);
    }
}
