<?php

namespace App\Http\Controllers;

use App\Module;
use App\Http\Controllers\Controller;
use App\Transformers\ModuleTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ModuleController extends Controller
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
        $modules = Module::all();
        $resource = new Collection($modules, new ModuleTransformer());
        $data = $this->fractal->createData($resource)->toArray();
        return response()->json($data);
    }

    public function show($id) 
    {
        $module = Module::findOrFail($id);
        $resource = new Item($module, new ModuleTransformer);
        $data = $this->fractal->createData($resource)->toArray();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $message = "";
        $module = null;
        if($module = Module::create($request->all())) {
            $message = "Module created!";
            return response()->json($module);
        } else {
            $message = "Error Module not Created!";
        }
        $data = fractal()->item($module, new ModuleTransformer())->toArray();
        return response()->json($message);
    }

    public function update(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        $message = "";

        if($module->update($request->all())) {
            $message = "Module Updated!";
        } else {
            $message = "Error, Module not Updated!";
        }

        $data = fractal()->item($module, new ModuleTransformer())->toArray();
        $response = [
            "message" => $message,
            "module" => $data
        ];
        return response()->json($response);
    }

    public function delete($id)
    {
       $module = Module::findOrFail($id);
       $message = "";

        if($module->delete()) {
            $message = "Module Deleted!";
        } else {
            $message = "Error! Module not deleted!";
        }
        $data = fractal()->item($module, new ModuleTransformer())->toArray();
        $response = [
            "message" => $message,
            "module" => $data
        ];
        return response()->json($response);
    }

    public function tag(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        $module->users()->attach($request->user_id);
        $data = fractal()->item($module, new ModuleTransformer())->toArray();
        $response = [
            "message" => "User tagged!",
            "activity" => $data,
        ];
        return response()->json($response);
    }

    public function untag(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        $module->users()->detach($request->user_id);
        $data = fractal()->item($module, new ModuleTransformer())->toArray();
        $response = [
            "message" => "User untagged!",
            "activity" => $data,
        ];
        return response()->json($response);
    }

}
