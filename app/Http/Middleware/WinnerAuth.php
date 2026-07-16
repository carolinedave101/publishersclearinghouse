<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WinnerAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('winner_id')) {
            return redirect()->route('home')->with('error', 'Please enter your winner code to access this page.');
        }

        $winner = \App\Models\Winner::find(session('winner_id'));
        if (!$winner || !$winner->is_active) {
            session()->forget('winner_id');
            return redirect()->route('home')->with('error', 'Winner account not found or inactive.');
        }

        $request->merge(['winner' => $winner]);
        return $next($request);
    }
}
