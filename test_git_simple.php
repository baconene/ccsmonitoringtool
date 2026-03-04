<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

$controller = new \App\Http\Controllers\GitHistoryController();
$request = new \Illuminate\Http\Request();
$request->merge(['limit' => 20, 'format' => 'detailed']);
$resp = $controller->getHistory($request);
echo $resp->getContent();
