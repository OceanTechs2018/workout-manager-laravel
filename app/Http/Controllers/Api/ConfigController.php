<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ConfigController extends Controller
{
    /**
     * Run specific allowed Artisan commands via API.
     */
    public function runCommand(Request $request)
    {
        $allowedCommands = [
            'config:cache',
            'config:clear',
            'route:cache',
            'route:clear',
            'view:cache',
            'view:clear',
            'cache:clear',
            'migrate',
            'migrate:fresh',
        ];

        $command = $request->input('command');

        if (!$command || !in_array($command, $allowedCommands)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or unauthorized command.',
            ], 400);
        }

        // Run command
        Artisan::call($command);

        return response()->json([
            'success' => true,
            'command' => $command,
            'output'  => Artisan::output(),
        ]);
    }
}
