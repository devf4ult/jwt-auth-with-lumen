<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\User;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
    * Instantiate a new UserController instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * Get the authenticated User.
    *
    * @return Response
    */
    public function profile()
    {
        return response()->json(['user' => Auth::user()], 200);
    }

    /**
    * Get all User.
    *
    * @return Response
    */
    public function allUsers()
    {
         return response()->json(['users' =>  User::all()], 200);
    }

    /**
    * Get one user.
    *
    * @return Response
    */
    public function singleUser($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json(['user' => $user], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => 'user not found!'], 404);
        }

    }

    /**
    * Get a JWT via given credentials.
    *
    * @param  Request  $request
    * @return Response
    */

    public function login(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
    */

    public function register(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {

            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            //return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }

    }


}
