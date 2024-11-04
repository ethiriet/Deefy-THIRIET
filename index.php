<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';
iutnc\deefy\repository\DeefyRepository::setConfig('db.config.ini');

session_start();

$d = new \iutnc\deefy\dispatch\Dispatcher();
$d->run();
