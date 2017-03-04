<?php

namespace App\Http\Controllers;

use \App\User;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Carbon;

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

		$data = $this->fractal->parseIncludes(['activities', 'tagged_activities', 'personal_tasks'])
				->createData($resource)->toArray();
		return response()->json($data);
	}

	public function users()
	{
		$logged_in = Auth::user();
		$users = User::where('id', '!=', $logged_in->id)->get();
		$resource = new Collection($users, new UserTransformer());

		$data = $this->fractal->createData($resource)->toArray();
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

	public function getUser($id)
	{
		$user = User::findOrFail($id);
		$resource = new Item($user, new UserTransformer());
		$data = $this->fractal->createData($resource)->toArray();
		return response()->json($data);
	}

	public function checkRequest($id)
	{
		$myRequest = false;
		$requestOf = false;
		$friend = false;
		$norelation = false;

		$user = Auth::user();
		$checkingUser = User::findOrFail($id);
		$requests = $user->myRequests->merge($user->requestOf);
		$friends = $user->friendsOfMine->merge($user->friendOf);
		if($requests->contains($checkingUser)) {
			if($user->myRequests->contains($checkingUser)) {
				$myRequest = true;
			};
			if($user->requestOf->contains($checkingUser)) {
				$requestOf = true;
			}
		} 
		if($friends->contains($checkingUser)) {
			$friend = true;
		} 
		if(!$myRequest && !$requestOf && !$friend) {
			$norelation = true;
		}

		$data =  fractal()->item($checkingUser, new UserTransformer())
				->addMeta(['friends' => $friend, 'myRequest' => $myRequest, 'requestOf' => $requestOf, 'noRelation' => $norelation])
				->toArray();
		return response()->json($data);

	}

	public function add($id)
	{
		$user = User::findOrFail($id);
		Auth::user()->addFriend($user);
		return response()->json(['Request Sent!']);
	}

	public function approve($id)
	{
		// implement this function to approve pending requests.
		$user = User::findOrFail($id);
		Auth::user()->requestOf()->sync([$user->id => ['status' => 'Accepted']], false);
		return response()->json(['Request Accepted!']);
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
		$requests = $user->requestOf()->get();
		$response = [
			"data" => $requests,
		];
		$update_requests = $user->requestOf();
		$user->requestOf()->each(function($req) use ($user) {
			$user->requestOf()->updateExistingPivot($req->user_id, ['status' => 'Seen']);
		});
		return response()->json($response);
	}

	public function friends()
	{
		$user = Auth::user();
		$requests = $user->friendsOfMine->merge($user->friendOf);
		$response = [
			"data" => $requests,
		];
		return response()->json($response);
	}
}