<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;

class TelegramBotController extends Controller
{
    private TelegramBotService $botService;

    public function __construct(TelegramBotService $botService)
    {
        $this->botService = $botService;
    }

    public function webhook(Request $request)
    {
        $this->botService->processUpdate($request->all());
        return response()->json(['status' => 'ok']);
    }
}
