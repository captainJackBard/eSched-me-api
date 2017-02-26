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
		'tagged',
        'meetings',
	];


	protected $availableIncludes = [
		'user',
		'tagged',
		'modules',
        'meetings',
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
            'risk' => $activity->risk,
            'status' => $activity->status,
            'budget' => $activity->budget,
            'vendor' => $activity->vendor,
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

    public function includeUser(Activity $activity)
	{
		$user = \App\User::find($activity->user_id);

		return $this->item($user, new UserTransformer());
	}

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

	public function includeMeetings(Activity $activity)
    {
        $meetings = $activity->locations;

        return $this->collection($meetings, new LocationTransformer());
    }
}