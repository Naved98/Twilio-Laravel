<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MsgController;
use App\Http\Controllers\WhatsAppMessageController;


Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});
Route::post('/msg-what',[MsgController::class,'sendWhatsAppMessage']);
Route::post('/send-whatsapp-message', [WhatsAppMessageController::class, 'sendWhatsAppMessage']);