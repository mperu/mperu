<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function toggleAdmin(Request $request, User $user)
    {
        // Protezione: non puoi toglierti admin da solo
        if ($user->id === auth()->id() && $user->is_admin) {
            return back()->withErrors([
                'admin' => 'Non puoi rimuovere i permessi admin dal tuo stesso account.',
            ]);
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        return back()->with('status', 'Permessi admin aggiornati.');
    }
}