<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Http\Controllers\Controller;
use App\Transformers\ActivityTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $fractal;

    public function __construct()
    {
        $this->fractal = new Manager();
        if (isset($_GET['include'])) {
            $this->fractal->parseIncludes($_GET['include']);
        }
    }

    public function index() 
    {
        $activities = Activity::all();
        $resource = new Collection($activities, new ActivityTransformer());

        $data = $this->fractal->createData($resource)->toArray();

        return response()->json($data);
    }

    public function show($id) 
    {
        $activity = Activity::find($id);
        $resource = new Item($activity, new ActivityTransformer());
        return response()->json($this->fractal->createData($resource)->toArray());
    }

    public function store(Request $request)
    {
        $activity = new Activity();
        $activity->user_id = $request->input('user_id');
        $activity->title = $request->input('title');
        $activity->desc = $request->input('desc');
        $activity->status = $request->input('status');
        $activity->priority = $request->input('priority');
        $activity->start = $request->input('start');
        $activity->end = $request->input('end');
        if($activity->save())
        {   
            $resource = new Item($activity, new ActivityTransformer());
            $data = $this->fractal->createData($resource)->toArray();
            $response = [
                "message" => "Activity Created",
                "activity" => $data,
            ];
            return response()->json($response);
        }
    }

    //
}
