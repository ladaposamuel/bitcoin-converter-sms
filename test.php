<?php
require __DIR__ . "/vendor/autoload.php";
require('functions.php');
$dotenv = Dotenv\Dotenv::create(__DIR__);

$dotenv->load();

$RawCommand ="convert 0.1 BTC to USD";



echo convertCurrency($RawCommand);