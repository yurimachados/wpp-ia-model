<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function (Request $request) {
    return response()->json(['message' => 'Hello World!']);
});

// Rota para obter o QR Code
Route::get('/whatsapp/qr-code', [WhatsAppController::class, 'getQrCode']);

// Rota para desconectar o WhatsApp
Route::post('/whatsapp/disconnect', [WhatsAppController::class, 'disconnect']);

// Rota para obter o status da instância
Route::get('/whatsapp/status', [WhatsAppController::class, 'getInstanceStatus']);

// Rota para obter os dados da instância
Route::get('/whatsapp/data', [WhatsAppController::class, 'getInstanceData']);

// Rota para enviar uma mensagem
Route::post('/whatsapp/send-message', [WhatsAppController::class, 'sendMessage']);
