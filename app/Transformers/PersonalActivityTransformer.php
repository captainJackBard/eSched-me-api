<?php
namespace App\Transformers;

use App\PersonalActivity;
use League\Fractal;

class PersonalActivityTransformer extends Fractal\TransformerAbstract
{
	/**
     * List of resources possible to include
     *
     * @var array
     */
	protected $availableIncludes = [
		
	];

	public function transform(PersonalActivity $pa)
	{
		return [
			'id' => $pa->id,
			'user_id' => $pa->user_i,
            'title' => $pa->title,
            'description' => $pa->desc,
            'status' => $pa->status,
            'created' => $pa->created,
            'modified' => $pa->modified,
            'priority' => (int) $pa->priority,
            'links' => [
            	'rel' => 'self',
            	'uri' => '/personal_activity/' . $pa->id,
            	'next' => '/personal_activity/' . ($pa->id + 1)
            ],
		];
	}

}