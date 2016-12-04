<?php
namespace App\Transformers;

use App\Activity;
use League\Fractal;
use DateTime;

class ActivityTransformer extends Fractal\TransformerAbstract
{
	/**
     * List of resources possible to include
     *
     * @var array
     */
	protected $defaultIncludes = [
		'user',
		'modules',
	];


	protected $availableIncludes = [
		'user',
		'tagged',
		'modules',
	];

	public function transform(Activity $activity)
	{
		$start = new DateTime($activity->start);
		$end = new DateTime($activity->end);
		$start_date = $start->format(DateTime::ATOM);
		$end_date = $end->format(DateTime::ATOM);
		return [
			'id' => $activity->id,
            'title' => $activity->title,
            'desc' => $activity->desc,
            'status' => $activity->status,
            'start' => $start_date,
            'end' => $end_date,
            'priority' => (int) $activity->priority,
            'links' => [
            	'rel' => 'self',
            	'uri' => '/activity/' . $activity->id,
            	'next' => '/activity/' . ($activity->id + 1)
            ],
		];
	}

	/**
     * Include User
     *
     * @return League\Fractal\ItemResource
     */
	public function includeUser(Activity $activity)
	{
		$user = \App\User::find($activity->user_id);

		return $this->item($user, new UserTransformer());
	}
	/**
     * Include Tagged
     *
     * @return League\Fractal\ItemResource
     */
	public function includeTagged(Activity $activity)
	{
		$users = $activity->users()->get();

		return $this->collection($users, new UserTransformer());
	}

	public function includeModules(Activity $activity)
	{
		$modules = $activity->modules;

		return $this->collection($modules, new ModuleTransformer());
	}
}