<?php

error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

const ROOT = __DIR__ . '/../';
const LOG_DIR = ROOT . 'log';

$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();