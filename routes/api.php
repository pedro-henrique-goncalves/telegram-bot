<?php

use Illuminate\Support\Facades\Route;

Route::post('telegram/webhook', 'TelegramBotController@webhook')->name('telegram.webhook');
