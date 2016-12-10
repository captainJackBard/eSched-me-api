<?php

namespace App\Http\Controllers;

use App\SubModule;
use App\Http\Controllers\Controller;
use App\Transformers\SubmoduleTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class SubmoduleController extends Controller
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
        $sub = SubModule::all();
        $data = fractal()->collection($sub, new SubmoduleTransformer())->toArray();
        return response()->json($data);
    }

    public function show($id) 
    {
        $sub = SubModule::findOrFail($id);
        $data = fractal()->item($sub, new SubmoduleTransformer())->toArray();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $message = "";
        $submodule = null;
        if($submodule = SubModule::create($request->all())) {
            $message = "Submodule created!";
        } else {
            $message = "Error Submodule not Created!";
        }
        $data = fractal()->item($submodule, new SubmoduleTransformer())->toArray();
        $response = [
            "message" => $message,
            "submodule" => $data
        ];
        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        $submodule = SubModule::findOrFail($id);
        $message = "";

        if($submodule->update($request->all())) {
            $message = "Submodule Updated!";
        } else {
            $message = "Error, Submodule not Updated!";
        }

        $data = fractal()->item($submodule, new SubmoduleTransformer())->toArray();
        $response = [
            "message" => $message,
            "submodule" => $data
        ];
        return response()->json($response);
    }

    public function delete($id)
    {
       $submodule = SubModule::findOrFail($id);
       $message = "";

        if($submodule->delete()) {
            $message = "Submodule Deleted!";
        } else {
            $message = "Error! Submodule not deleted!";
        }
        $data = fractal()->item($submodule, new SubmoduleTransformer())->toArray();
        $response = [
            "message" => $message,
            "submodule" => $data
        ];
        return response()->json($response);
    }

}
