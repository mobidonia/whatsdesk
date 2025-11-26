<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
       
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first(); 
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'errMsg' => __('The provided credentials are incorrect.')
            ]);
        }

        //If admin, don't allow login
        if ($user->hasRole('admin')) {
            return response()->json([
                'status' => false,
                'errMsg' => __('Admin user cannot login.')
            ]);
        }

        //If in the request we have expotoken, try to set it on the user record
        if ($request->has('expotoken')&&$request->expotoken!='') {
            try {
                $user->expotoken = $request->expotoken;
                $user->save();
            } catch (\Exception $e) {
            }
           
        }
        
        $token = $user->createToken('login_at_' . now()->format('d_m_Y'))->plainTextToken;

        return response()->json([
            'status' => true,
            'is_admin' => $user->hasRole('admin')?true:false,
            'is_owner' => $user->hasRole('owner')?true:false,
            'is_staff' => $user->hasRole('staff')?true:false,
            'is_client' => $user->hasRole('client')?true:false,
            'token' => $token,
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]);
    }
}
