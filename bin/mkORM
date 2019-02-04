#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
(\Dotenv\Dotenv::create(__DIR__ . '/../'))->overload();

use Symfony\Component\Console\Application;

$app = new Application();
$app->add(new \MkOrm\Commands\MakeModel());
$app->add(new \MkOrm\Commands\MakeAllModel());
//$app->add(new \MkOrm\Commands\ClearCacheCommand());
$app->run();
