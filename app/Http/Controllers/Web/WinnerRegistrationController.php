<?php

namespace App\Http\Controllers\Web;

use App\Helpers\EmailHelper;
use App\Http\Controllers\Controller;
use App\Mail\WinnerNotification;
use App\Models\RegistrationLink;
use App\Models\Winner;
use App\Services\CodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WinnerRegistrationController extends Controller
{
    public function __construct(
        protected CodeGenerator $codeGenerator,
    ) {}

    public function showRegister(): View|RedirectResponse
    {
        if (session()->has('winner_id')) {
            return redirect()->route('winner.dashboard');
        }

        return view('auth.winner-register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:winners,email'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:100'],
            'zip' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $source = Str::limit(
            strtolower(preg_replace('/[^A-Za-z0-9_-]/', '', (string) $request->query('source', ''))),
            50,
            ''
        );

        $link = RegistrationLink::query()
            ->where('is_active', true)
            ->where('source', $source)
            ->first();

        $winner = Winner::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'zip' => $data['zip'],
            'password' => $data['password'],
            'unique_code' => $this->codeGenerator->generateUniqueCode(),
            'prize_amount' => 5500000,
            'status' => 'new',
            'is_active' => true,
            'is_claimed' => false,
            'source' => $source ?: null,
            'registration_link_id' => $link?->id,
        ]);

        session(['winner_id' => $winner->id]);

        EmailHelper::send(
            new WinnerNotification(
                $winner,
                "Your Winner Code: {$winner->unique_code}",
                "Congratulations {$winner->first_name}! Your registration is complete and your prize of \$5,500,000 is confirmed. Your personal winner code is: {$winner->unique_code}. Keep this code safe — you'll use it every time you log in to your winner dashboard."
            ),
            $winner->email,
            $winner->first_name
        );

        return redirect()
            ->route('winner.dashboard')
            ->with('success', "Congratulations! Your winner code is {$winner->unique_code}. Keep it safe — you'll need it to log in.");
    }
}