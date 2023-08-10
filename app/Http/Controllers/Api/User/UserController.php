<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getAuthenticatedUser()
    {
        $user_id = Auth::id();
        $user = User::find($user_id);

        return response()->json([
            'id' => $user_id,
            'authenticated' => 1,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'user_name' => $user->user_name,
                'email' => $user->email,
            ],
        ]);
    }
}
