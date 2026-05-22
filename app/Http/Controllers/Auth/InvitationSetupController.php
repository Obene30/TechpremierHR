<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InvitationSetupController extends Controller
{
    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        if ($invitation->isUsed()) return redirect()->route('login')->with('error', 'This invitation has already been used.');
        if ($invitation->isExpired()) return redirect()->route('login')->with('error', 'This invitation has expired.');
        return view('auth.setup-account', compact('invitation'));
    }

    public function setup(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        if ($invitation->isUsed() || $invitation->isExpired()) return redirect()->route('login')->with('error', 'Invalid invitation.');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|confirmed|min:8',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $invitation->email,
            'password' => Hash::make($data['password']),
            'role' => $invitation->role,
            'department_id' => $invitation->department_id,
            'phone' => $data['phone'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'date_joined' => now()->toDateString(),
            'invited_by' => $invitation->invited_by,
            'account_setup_at' => now(),
            'status' => 'active',
        ]);

        $invitation->update(['used_at' => now()]);

        Auth::login($user);
        return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'employee.dashboard')
            ->with('success', 'Welcome to Fawthrite HR!');
    }
}
