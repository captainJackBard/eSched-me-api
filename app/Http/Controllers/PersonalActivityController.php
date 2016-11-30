<?php

namespace App\Http\Controllers;

use App\PersonalActivity;
use App\Http\Controllers\Controller;
use App\Transformers\PersonalActivityTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class PersonalActivityController extends Controller
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
        $pa = PersonalActivity::all();
        $resource = new Collection($pa, new PersonalActivityTransformer());
        $data = $this->fractal->createData($resource)->toArray();
        return response()->json($data);
    }

    public function show($id) 
    {
        $pa = PersonalActivity::findOrFail($id);
        $resource = new Item($pa, new PersonalActivityTransformer());
        $data = $this->fractal->createData($resource)->toArray();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        if($pa = PersonalActivity::create($request->all())) {
            return response()->json(['Personal Activity Created!']);
        }
    }

    public function update(Request $request, $id)
    {
        $pa = PersonalActivity::findOrFail($id);
        if($pa->update($request->all())) {
            return response()->json(['Personal Activity Updated!']);
        }
    }

    public function delete($id)
    {
        $pa = PersonalActivity::findOrFail($id);
        if($pa->delete()) {
            return response()->json(['Personal Activity Deleted!']);
        }
    }

}
