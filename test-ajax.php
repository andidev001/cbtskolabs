<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture()); // to boot auth

$request = Illuminate\Http\Request::create('/guru/bank-soal', 'GET');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
\Illuminate\Support\Facades\Auth::guard('guru')->loginUsingId(1);
$controller = app()->make(App\Http\Controllers\Guru\BankSoalController::class);
$response = $controller->index($request);
echo $response->getContent();

