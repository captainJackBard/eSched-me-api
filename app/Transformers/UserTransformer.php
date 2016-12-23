<?php
namespace App\Transformers;

use App\User;
use League\Fractal;
use App\Activity;

class UserTransformer extends Fractal\TransformerAbstract
{
	/**
     * List of resources possible to include
     *
     * @var array
     */

	protected $availableIncludes = [
		'activities',
		'skills',
		'tagged_activities',
		'friends',
		'requests',
		'personal_tasks',
	];

	public function transform(User $user)
	{
		return [
			'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'img_name' => $user->img_name,
            'skills' => $user->skills,
            'about_me' => $user->about_me,
            'email' => $user->email,
            'fuid' => $user->fuid,
            'occupation' => $user->occupation,
            'links' => [
            	'rel' => 'self',
            	'uri' => '/user/' . $user->id,
            ],
		];
	}

	public function includeActivities(User $user)
	{
		$activities = Activity::where('user_id', $user->id)->get();
		return $this->collection($activities, new ActivityTransformer());
	}

	public function includeTaggedActivities(User $user)
	{
		$activities = $user->activities()->where('user_id', '!=', $user->id);

		return $this->collection($activities, new ActivityTransformer());
	}

	public function includeFriends(User $user)
	{
		$friends = $user->friendsOfMine->merge($user->friendOf);

		return $this->collection($friends, new UserTransformer());
	}

	public function includeRequests(User $user)
	{
		$requests = $user->myRequests->merge($user->requestOf);

		return $this->collection($requests, new UserTransformer());
	}

	public function includePersonalTasks(User $user)
	{
		$personal_tasks = $user->personalActivities;

		return $this->collection($personal_tasks, new PersonalActivityTransformer());
	}

	public function includeSkills(User $user)
	{
		$skills = $user->skills;

		return $this->collection($skills, new SkillsTransfromer());
	}
}