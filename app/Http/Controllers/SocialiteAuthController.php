<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;

class SocialiteAuthController extends Controller
{
    /**
     * @OA\Post(
     ** path="/auth/google-callback",
     *   tags={"Social Auth"},
     *   summary="Google authentication",
     *   operationId="google",
     *
     *   @OA\RequestBody(
     *      required=true,
     *      description="Pass google token",
     *      @OA\JsonContent(
     *         required={"token"},
     *         @OA\Property(property="token", type="string", format="string", example="google-access-token"),
     *      ),
     *   ),
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
    public function loginWithGoogle(Request $request)
    {
        try {

            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->token);
            $user = User::where('google_id', $googleUser->id)->first();

            if($user){
                
                return $this->createNewToken(Auth::login($user));
            }

            else{
                $googleName = explode(" ", $googleUser->name);
                $firstName = $googleName[0];
                $lastName = "";
                if (count($googleName) > 1) {
                    $lastName = substr($googleUser->name,strlen($firstName) + 1);
                }
                $createUser = User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $googleUser->email,
                    'password' => bcrypt('test@123'),
                    'google_id' => strval($googleUser->id)
                ]);

                return $this->createNewToken(Auth::login($createUser));
            }

        } catch (Exception $exception) {
            dd($exception->getMessage());
        }
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
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }
}
