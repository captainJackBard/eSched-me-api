<?php
namespace App\Transformers;

use App\User;
use League\Fractal;

class UserTransformer extends Fractal\TransformerAbstract
{
	/**
     * List of resources possible to include
     *
     * @var array
     */
	protected $availableIncludes = [
		'activities',
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
            'occupation' => $user->occupation,
            'links' => [
            	'rel' => 'self',
            	'uri' => '/user/' . $user->id,
            ],
		];
	}

	public function includeActivities(User $user)
	{
		$activities = $user->activities()->get();

		return $this->collection($activities, new ActivityTransformer());
	}

}