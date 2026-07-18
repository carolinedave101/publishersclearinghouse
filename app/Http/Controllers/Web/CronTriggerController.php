<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CronTriggerController extends Controller
{
    public function trigger(Request $request)
    {
        $token = $request->query('token');
        $expectedToken = Setting::getValue('cron_trigger_token');

        if (!$expectedToken || $token !== $expectedToken) {
            return response()->json(['status' => 'error', 'message' => 'Invalid token'], 403);
        }

        try {
            Artisan::call('queue:work', [
                'connection' => 'database',
                '--stop-when-empty' => true,
                '--max-time' => 55,
            ]);
            $output = Artisan::output();
            return response()->json([
                'status' => 'ok',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
