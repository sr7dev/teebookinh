<?php

namespace App\Http\Controllers\Api;

use App\Exports\EventsExport;
use App\Http\Controllers\Controller;
use App\Imports\EventsImport;
use App\Models\User;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Validator;

class CreditController extends Controller
{
    /**
     * @OA\Get(
     *     path="/credits",
     *     tags={"Credits"},
     *     security={{"bearer":{}}},
     *     summary="Get credits of current user",
     * 
     *  @OA\Response(
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
     */
    public function getCredits(Request $request)
    {
        $credits = intval(auth()->user()->credits);
        return response()->json([
            'credits' => $credits,
        ]);
    }

    /**
     * @OA\Put(
     **  path="/add-credits",
     *   tags={"Credits"},
     *   security={{"bearer":{}}},
     *   summary="Add credits of current user",
     *
     *   @OA\RequestBody(
     *      required=true,
     *      description="Pass number of credits",
     *      @OA\JsonContent(
     *         required={"credits"},
     *         @OA\Property(property="credits", type="integer", format="integer", example=5),
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
    public function addCredits(Request $request)
    {
        $user = auth()->user();
        $credits = $user->credits + $request->get('credits');
        User::where('id', $user->id)->update([
            'credits' => $credits,
        ]);

        return response()->json([
            'credits' => $credits,
        ]);
    }

    /**
     * @OA\Put(
     **  path="/deduct-credits",
     *   tags={"Credits"},
     *   security={{"bearer":{}}},
     *   summary="Deduct credits of current user",
     *
     *   @OA\RequestBody(
     *      required=true,
     *      description="Pass number of credits",
     *      @OA\JsonContent(
     *         required={"credits"},
     *         @OA\Property(property="credits", type="integer", format="integer", example=5),
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
    public function deductCredits(Request $request)
    {
        $user = auth()->user();
        $credits = $user->credits - $request->get('credits');
        User::where('id', $user->id)->update([
            'credits' => $credits,
        ]);

        return response()->json([
            'credits' => $credits,
        ]);
    }
}
