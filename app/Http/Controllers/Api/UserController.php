<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\sendingEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Validator;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"User"},
     *     security={{"bearer":{}}},
     *     summary="Get list of users",
     *     @OA\Response(response="200", description="Get list of users")
     * )
     */
    public function index(Request $request)
    {
        $users = User::all();
        return response()->json([
            'data' => $users,
        ]);
    }

    /**
     * @OA\Post(
     **  path="/users",
     *   security={{"bearer":{}}},
     *   tags={"User"},
     *   summary="Create new User",
     *
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *         required={"name","email","password","password_confirmation"},
     *         @OA\Property(property="name", type="string", format="string", example="john"),
     *         @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *         @OA\Property(property="is_admin", type="integer", format="integer", example=0),
     *         @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *         @OA\Property(property="password_confirmation", type="string", format="password", example="PassWord12345"),
     *      ),
     *   ),
     *   @OA\Response(
     *      response=201,
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'is_admin' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        Mail::to($user->email)->send(new sendingEmail($request->password));

        return response()->json([
            'message' => 'User successfully created',
            'data' => $user,
        ], 201);
    }

    /**
     * @OA\Put(
     **  path="/users/{id}",
     *   security={{"bearer":{}}},
     *   tags={"User"},
     *   summary="Update a specified User",
     *
     *  @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *         required={"name","email","password","password_confirmation"},
     *         @OA\Property(property="name", type="string", format="string", example="john"),
     *         @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *         @OA\Property(property="is_admin", type="integer", format="integer", example=0),
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
     *   )
     *)
     **/
    public function update(Request $request, $id)
    {
        if (Gate::denies('is_owner', User::find($id))) {
            return response()->json([
                'message' => 'This action is not authorized',
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users,email,' . $id,
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        User::where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'is_admin' => $request->is_admin == null ? 0 : $request->is_admin,
        ]);

        return response()->json([
            'message' => 'User successfully updated',
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     security={{"bearer":{}}},
     *     tags={"User"},
     *     summary="Delete specified user",
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        @OA\Schema(
     *             type="string"
     *        )
     *     ),
     *     @OA\Response(response="200", description="Deleted specified user")
     * )
     */
    public function destroy($id)
    {
        if (Gate::denies('is_owner', User::find($id))) {
            return response()->json([
                'message' => 'This action is not authorized',
            ], 403);
        }
        $deleted = User::where('id', $id)->delete();

        return response()->json([
            'message' => 'User successfully deleted',
        ]);
    }
}
