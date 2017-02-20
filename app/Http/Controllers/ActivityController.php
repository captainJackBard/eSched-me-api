<?php

namespace App\Http\Controllers;

use App\Activity;
use App\GroupChat;
use App\Http\Controllers\Controller;
use App\Transformers\ActivityTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Auth;

use DateTime;

class ActivityController extends Controller
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
        // $activities = Activity::all();
        $user = Auth::user();
        $activities = Activity::where('user_id', $user->id)->get();
        $resource = new Collection($activities, new ActivityTransformer());

        $data = $this->fractal->parseIncludes(['modules'])->createData($resource)->toArray();

        return response()->json($data);
    }

    public function show($id) 
    {
        $activity = Activity::findOrFail($id);
        $resource = new Item($activity, new ActivityTransformer());
        return response()->json($this->fractal->createData($resource)->toArray());
    }

    public function store(Request $request)
    {
        if($activity = Activity::create($request->all())) {
            $resource = new Item($activity, new ActivityTransformer());
            $activity->users()->attach($request->user_id);
            $data = $this->fractal->createData($resource)->toArray();
            $response = [
                "message" => "Activity Created",
                "activity" => $data,
            ];
            GroupChat::create([

            ]);
            return response()->json($response);
        }
    }

    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        if($activity->update($request->all()))
        {
            $resource = new Item($activity, new ActivityTransformer());
            $data = $this->fractal->createData($resource)->toArray();
            $response = [
                "message" => "Activity Updated!",
                "activity" => $data,
            ];
            return response()->json($response);
        }
    }

    public function delete($id)
    {
        $activity = Activity::findOrFail($id);

        $resource = new Item($activity, new ActivityTransformer());
        $data = $this->fractal->createData($resource)->toArray();

        if($activity->delete()) {
            $response = [
                "message" => "Activity Deleted!",
                "activity" => $data,
            ];
            return response()->json($response);
        } else  {
            $response = [
                "message" => "Activity not Deleted!",
                "activity" => $data,
            ];
            return response()->json($response);
        }
        
    }

    public function tag(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        $activity->users()->attach($request->user_id);
        $data = fractal()->item($activity, new ActivityTransformer())->includeTagged()->toArray();
        $response = [
            "message" => "User tagged!",
            "activity" => $data,
        ];
        return response()->json($response);
    }

    public function untag(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        $activity->users()->detach($request->user_id);
        $data = fractal()->item($activity, new ActivityTransformer())->includeTagged()->toArray();
        $response = [
            "message" => "User untagged!",
            "activity" => $data,
        ];
        return response()->json($response);
    }
}
