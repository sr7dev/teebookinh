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
     *     path="/events",
     *     tags={"Event"},
     *     security={{"bearer":{}}},
     *     summary="Get list of events",
     *     @OA\Response(response="200", description="Get list of events")
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
     * @OA\Post(
     **  path="/events",
     *   security={{"bearer":{}}},
     *   tags={"Event"},
     *   summary="Create new Event",
     *
     *   @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *         required={"name","description","date"},
     *         @OA\Property(property="name", type="string", format="string", example="event-1"),
     *         @OA\Property(property="description", type="string", format="string", example="description-1"),
     *         @OA\Property(property="date", type="string", format="date", example="2021-04-30"),
     *         @OA\Property(property="number_of_attendees", type="integer", format="string"),
     *         @OA\Property(property="unit_of_attendees", type="string", format="string"),
     *         @OA\Property(property="dimension", type="string", format="string"),
     *         @OA\Property(property="type", type="string", format="string"),
     *         @OA\Property(property="whether_virtual", type="boolean", format="boolean"),
     *         @OA\Property(property="languages", type="string", format="string"),
     *         @OA\Property(property="is_internal", type="string", format="string"),
     *         @OA\Property(property="any_partners", type="string", format="string"),
     *         @OA\Property(property="component_covid19", type="string", format="string"),
     *         @OA\Property(property="component_addressing", type="string", format="string"),
     *         @OA\Property(property="leadership_level", type="string", format="string"),
     *         @OA\Property(property="resources", type="string", format="string"),
     *         @OA\Property(property="optional_1", type="string", format="string"),
     *         @OA\Property(property="optional_2", type="string", format="string"),
     *         @OA\Property(property="optional_3", type="string", format="string"),
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
            'name' => 'required|string',
            'description' => 'required|string',
            'date' => 'required',
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
     **  path="/events/{id}",
     *   security={{"bearer":{}}},
     *   tags={"Event"},
     *   summary="Update a specified Event",
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
     *   @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *         required={"name","description","date"},
     *         @OA\Property(property="name", type="string", format="string", example="event-1"),
     *         @OA\Property(property="description", type="string", format="string", example="description-1"),
     *         @OA\Property(property="date", type="string", format="date", example="2021-04-30"),
     *         @OA\Property(property="number_of_attendees", type="integer", format="string"),
     *         @OA\Property(property="unit_of_attendees", type="string", format="string"),
     *         @OA\Property(property="dimension", type="string", format="string"),
     *         @OA\Property(property="type", type="string", format="string"),
     *         @OA\Property(property="whether_virtual", type="boolean", format="boolean"),
     *         @OA\Property(property="languages", type="string", format="string"),
     *         @OA\Property(property="is_internal", type="string", format="string"),
     *         @OA\Property(property="any_partners", type="string", format="string"),
     *         @OA\Property(property="component_covid19", type="string", format="string"),
     *         @OA\Property(property="component_addressing", type="string", format="string"),
     *         @OA\Property(property="leadership_level", type="string", format="string"),
     *         @OA\Property(property="resources", type="string", format="string"),
     *         @OA\Property(property="optional_1", type="string", format="string"),
     *         @OA\Property(property="optional_2", type="string", format="string"),
     *         @OA\Property(property="optional_3", type="string", format="string"),
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
        if (Gate::denies('is_owner', Event::find($id))) {
            return response()->json([
                'message' => 'This action is not authorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'date' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        Event::where('id', $id)->update($request->all());

        return response()->json([
            'message' => 'Event successfully updated',
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/events/{id}",
     *     security={{"bearer":{}}},
     *     tags={"Event"},
     *     summary="Delete specified event",
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        @OA\Schema(
     *             type="string"
     *        )
     *     ),
     *     @OA\Response(response="200", description="Deleted specified event")
     * )
     */
    public function destroy($id)
    {
        if (Gate::denies('is_owner', Event::find($id))) {
            return response()->json([
                'message' => 'This action is not authorized',
            ], 403);
        }
        $deleted = Event::where('id', $id)->delete();
        return response()->json([
            'message' => 'Event successfully deleted',
        ]);
    }

    /**
     * @OA\Post(
     **  path="/events/import-csv",
     *   security={{"bearer":{}}},
     *   tags={"Event"},
     *   summary="Import events using CSV file",
     *
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="file to upload",
     *                     property="events_csv",
     *                     type="file",
     *                ),
     *                 required={"events_csv"}
     *             )
     *         )
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
    public function importCsv(Request $request)
    {
        if ($request->file('events_csv')) {
            Excel::import(new EventsImport, $request->file('events_csv'), \Maatwebsite\Excel\Excel::CSV);

            return response()->json([
                'message' => 'Events successfully added',
            ]);
        }
        return response()->json([
            'message' => 'File not selected',
        ], 422);
    }

    /**
     * @OA\Get(
     *     path="/events/export-csv",
     *     security={{"bearer":{}}},
     *     tags={"Event"},
     *     summary="Export events of authenticated user",
     *     @OA\Response(response="200", description="Success")
     * )
     */
    public function exportCsv(Request $request)
    {
        return Excel::download(new EventsExport, 'events-' . date('Y-m-d-H-i-s') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
