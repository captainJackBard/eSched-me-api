<?php

namespace App\Http\Controllers;

use App\SubModule;
use App\Module;
use App\Http\Controllers\Controller;
use App\Transformers\SubmoduleTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use App\Notification;

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
            $submodule->module->activity->users->each(function($user) use ($submodule) {
                if($user->id !== $submodule->module->activity->user_id) {
                    Notification::create([
                        "user_id" => $user->id,
                        "link" => "https://esched.me/activities/sub_modules/" . $submodule->module->activity->id . '/' . $submodule->module->id,
                        "message" => "Submodule has been created!",
                        "status" => "pending"
                    ]);
                }
            });
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
            $status = SubModule::where('module_id', $submodule->module_id)->select(['status'])->get();
            $not_complete = $status->where('status', '!=', 'completed');
            if ($not_complete->isEmpty()) { // Meaning every submodule is completed
                $module = Module::findOrFail($submodule->module_id);
                $module->status = "completed";
                $module->save();
                $message = 'Submodule Updated and Module is Complete!';
            }
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
