<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';
session_start();

iutnc\deefy\repository\DeefyRepository::setConfig('config.db.ini');

$d = new \iutnc\deefy\dispatch\Dispatcher();
$d->run();

