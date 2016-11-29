<?php

namespace App\Http\Controllers;

use \App\User;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

use Auth;

class UserController extends Controller
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


	public function me()
	{
		$user = Auth::user();
		$resource = new Item($user, new UserTransformer());

		$data = $this->fractal->parseIncludes(['activities', 'tagged_activities'])
				->createData($resource)->toArray();
		return response()->json($data);
	}

	public function updateInfo(Request $request)
	{
		$user = Auth::user();
		$user->first_name = $request->input('first_name');
		$user->last_name = $request->input('last_name');
		$user->img_name = $request->input('img_name');
		$user->skills = $request->input('skills');
		$user->about_me = $request->input('about_me');
		$user->occupation = $request->input('occupation');
		if($user->save()) {
			return response()->json(['Your information is updated successfully!']);
		}
	}

	public function add($id)
	{
		$user = User::findOrFail($id);
		Auth::user()->addFriend($user);
		return response()->json(['Request Sent!']);
	}

	public function approve($id)
	{
		
	}

	public function remove($id)
	{
		$user = User::findOrFail($id);
		Auth::user()->removeFriend($user);
		return response()->json(['Friend Removed']);
	}

	public function pendingRequests()
	{
		$user = Auth::user();
		$requests = $user->friends()->wherePivot('status', 'pending')->get();
		$response = [
			"data" => $requests,
		];
		return response()->json($response);
	}

	public function friends()
	{
		$user = Auth::user();
		$requests = $user->friends()->wherePivot('status', 'accepted')->get();
		$response = [
			"data" => $requests,
		];
		return response()->json($response);
	}
}