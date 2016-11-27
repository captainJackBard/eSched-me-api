<?php
namespace App\Transformers;

use App\Module;
use League\Fractal;

class ModuleTransformer extends Fractal\TransformerAbstract
{
	/**
     * List of resources possible to include
     *
     * @var array
     */
	protected $availableIncludes = [
		'submodules'
	];

    protected $defaultIncludes = [
        'submodules'
    ];

	public function transform(Module $module)
	{
		return [
			'id' => $module->id,
            'activty_id' => $module->activity_id,
            'title' => $module->title,
            'description' => $module->description,
            'status' => $module->status,
            'start' => $module->start,
            'end' => $module->end,
            'priority' => (int) $module->priority,
            'percentage' => (double) $module->percentage . '%',
            'links' => [
            	'rel' => 'self',
            	'uri' => '/module/' . $module->id,
            ],
		];
	}

    public function includeSubmodules(Module $module)
    {
        $submodules = $module->submodules;
        return $this->collection($submodules, new SubmoduleTransformer());
    }

}