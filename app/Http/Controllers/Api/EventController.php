<?php

namespace App\Http\Controllers\Api;

use App\Exports\EventsExport;
use App\Http\Controllers\Controller;
use App\Imports\EventsImport;
use App\Models\Event;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Validator;

class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/courses",
     *     tags={"Course"},
     *     security={{"bearer":{}}},
     *     summary="Get list of courses",
     *     @OA\Response(response="200", description="Get list of courses")
     * )
     */
    public function index(Request $request)
    {
        $events = Event::all();
        if ($request->user()->is_admin != 1) {
            $events = Event::where('user_id', $request->user()->id)->get();
        }

        return response()->json([
            'data' => $events,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/courses/{course_id}",
     *     tags={"Course"},
     *     security={{"bearer":{}}},
     *     summary="Get person_id of current user by course_id",
     * 
     *  @OA\Parameter(
     *      name="course_id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     * 
     * @OA\Response(
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
    public function getPersonID(Request $request, $course_id)
    {
        $event = Event::where('user_id', $request->user()->id)
                    ->where('course_id', $course_id)
                    ->get();
        if ($event->count() < 1) {
            return response(['error' => true, 'error-msg' => 'Not found'], 404);
        }
        return response()->json([
            'person_id' => $event->first()->person_id,
        ]);
    }

    /**
     * @OA\Post(
     **  path="/courses",
     *   tags={"Course"},
     *   security={{"bearer":{}}},
     *   summary="Create a new",
     *
     *   @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *         required={"person_id","course_id"},
     *         @OA\Property(property="person_id", type="string", format="string", example="person-1"),
     *         @OA\Property(property="course_id", type="string", format="string", example="course-1"),
     *      ),
     *   ),
     *
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
            'person_id' => 'required|string',
            'course_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $newE = $request->all();
        $newE['user_id'] = $request->user()->id;
        $event = Event::create($newE);

        return response()->json([
            'message' => 'Event successfully created',
            'data' => $event,
        ], 201);
    }

    /**
     * @OA\Put(
     **  path="/courses/{course_id}",
     *   tags={"Course"},
     *   security={{"bearer":{}}},
     *   summary="Update person_id of current user by course_id",
     *
     *   @OA\Parameter(
     *      name="course_id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *         required={"person_id","course_id"},
     *         @OA\Property(property="person_id", type="string", format="string", example="person-1"),
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
    public function update(Request $request, $course_id)
    {
        $validator = Validator::make($request->all(), [
            'person_id' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $event = Event::where('user_id', $request->user()->id)
                    ->where('course_id', $course_id);
        if ($event->count() < 1) {
            return response()->json([
                'message' => 'PersonID is not existing',
            ]);            
        }
        $event->update($request->all());
        // Event::where('id', $id)->update($request->all());

        return response()->json([
            'message' => 'PersonID successfully updated',
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/courses/{course_id}",
     *     tags={"Course"},
     *     security={{"bearer":{}}},
     *     summary="Delete specified person_id",
     *     @OA\Parameter(
     *          name="course_id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(response="200", description="Deleted specified event")
     * )
     */
    public function destroy(Request $request, $course_id)
    {
        $deleted = Event::where('user_id', $request->user()->id)
                     ->where('course_id', $course_id);
        if ($deleted->count() < 1) {
            return response()->json([
                'message' => 'PersonID is not existing',
            ]);            
        }
        $deleted->delete();
        return response()->json([
            'message' => 'PersonID successfully deleted',
        ]);
    }
}
