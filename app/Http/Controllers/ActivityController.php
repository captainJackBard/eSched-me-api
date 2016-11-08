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
        
    }

    //
}
