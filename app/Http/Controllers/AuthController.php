<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'no_authenticated']]);
    }

    /**
     * @OA\Post(
     ** path="/auth/login",
     *   tags={"Auth"},
     *   summary="Login - get a JWT via given credentials",
     *   operationId="login",
     *   @OA\RequestBody(
     *      required=true,
     *      description="Pass user credentials",
     *      @OA\JsonContent(
     *         required={"email","password"},
     *         @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *         @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *      ),
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *   @OA\Response(
     *      response=403,
     *      description="Forbidden"
     *   )
     *)
     **/
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user && ! Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Incorrect password'], 401);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * @OA\Post(
     ** path="/auth/register",
     *   tags={"Auth"},
     *   summary="Register a User",
     *   operationId="register",
     *
     *  @OA\RequestBody(
     *      required=true,
     *      description="Pass user credentials",
     *      @OA\JsonContent(
     *         required={"first_name","last_name","email","password","password_confirmation"},
     *         @OA\Property(property="first_name", type="string", format="string", example="john"),
     *         @OA\Property(property="last_name", type="string", format="string", example="smith"),
     *         @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *         @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *         @OA\Property(property="password_confirmation", type="string", format="password", example="PassWord12345"),
     *      ),
     *   ),
     *
     *   @OA\Response(
     *      response=201,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *   @OA\Response(
     *      response=403,
     *      description="Forbidden"
     *   ),
     *   @OA\Response(
     *      response=422,
     *      description="validation errors"
     *   )
     *)
     **/

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:1,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            $errorFirstName = $validator->errors()->get('first_name');
            $errorLastName = $validator->errors()->get('last_name');
            $errorEmail = $validator->errors()->get('email');
            $errorPassword = $validator->errors()->get('password');
            return response()->json(['error' => $errorFirstName ? $errorFirstName :
                ($errorLastName ? $errorLastName :
                ($errorEmail ? $errorEmail :
                ($errorPassword ? $errorPassword : '')))], 422);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'data' => $user,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     tags={"Auth"},
     *     summary="Log the user out (Invalidate the token)",
     *     operationId="logout",
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *          response=200,
     *          description="Log the user out (Invalidate the token)"
     *     )
     * )
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }
    
    /**
     * @OA\Get(
     *     path="/auth/user-profile",
     *     security={{"bearer":{}}},
     *     tags={"Auth"},
     *     summary="Get the authenticated User",
     *     @OA\Response(response="200", description="Get the authenticated User"),
     * )
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    /**
     * @OA\Post(
     *     path="/auth/refresh",
     *     security={{"bearer":{}}},
     *     tags={"Auth"},
     *     summary="Get the refresh token",
     *     @OA\Response(response="200", description="Get the refresh token"),
     * )
     */
    public function refresh(Request $request)
    {
        return $this->createNewToken($request->bearerToken());
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'refresh_token' => auth()->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }

    public function no_authenticated()
    {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

}
