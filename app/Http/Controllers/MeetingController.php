<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Location;
use App\Http\Controllers\Controller;
use App\Transformers\LocationTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Auth;

class MeetingController extends Controller
{

    protected $fractal;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->fractal = new Manager();
        if (isset($_GET['include'])) {
            $this->fractal->parseIncludes($_GET['include']);
        }
    }

    public function index()
    {
        $user = Auth::user();
        $meetings = collect([]);
        $user->activities->each(function ($activity) use (&$meetings) {
            $meetings = $meetings->merge($activity->activeMeetings);
        });
        $resource = new Collection($meetings, new LocationTransformer());
        $data = $this->fractal->createData($resource)->toArray();
        return response()->json($data);
    }

    public function show($id)
    {
        $user = Auth::user();
        $location = Location::findOrFail($id);
        if ($location->activity->users->contains('id', $user->id)) {
            $resource = new Item($location, new LocationTransformer());
            $data = $this->fractal->createData($resource)->toArray();
            return response()->json($data);
        }

        return response()->json('Unauthorized', 401);
    }

    public function showByActivity($activity_id)
    {
        $user = Auth::user();
        $activity = Activity::where('id', $activity_id)->firstOrFail();
        if ($activity->users->contains('id', $user->id)) {
            $meetings = $activity->locations;
            $resource = new Collection($meetings, new LocationTransformer());
            $data = $this->fractal->createData($resource)->toArray();
            return response()->json(current($data));
        }
        return response()->json('Unauthorized', 401);
    }

    public function store(Request $request)
    {
        $message = "";
        $meeting = null;
        if($meeting = Location::create($request->all())) {
            $message = "Module created!";
            return response()->json($meeting);
        } else {
            $message = "Error Module not Created!";
        }
        $data = fractal()->collection($meeting, new LocationTransformer())->toArray();
        return response()->json($message);
    }

    public function update(Request $request, $id)
    {
        $meeting  = Location::findOrFail($id);
        $message = "";

        if($meeting->update($request->all())) {
            $message = "Meeting  Updated!";
        } else {
            $message = "Error, Meeting not Updated!";
        }

        $data = fractal()->item($meeting, new LocationTransformer())->toArray();
        $response = [
            "message" => $message,
            "module" => $data
        ];
        return response()->json($response);
    }

    public function delete($id)
    {
        $meeting = Location::findOrFail($id);
        $message = "";

        if($meeting->delete()) {
            $message = "Meeting Deleted!";
        } else {
            $message = "Error! Meeting not deleted!";
        }
        $data = fractal()->item($meeting, new LocationTransformer())->toArray();
        $response = [
            "message" => $message,
            "module" => $data
        ];
        return response()->json($response);
    }

}
