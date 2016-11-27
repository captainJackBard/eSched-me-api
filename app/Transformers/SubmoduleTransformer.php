<?php
namespace App\Transformers;

use App\SubModule;
use League\Fractal;

class SubmoduleTransformer extends Fractal\TransformerAbstract
{
	/**
     * List of resources possible to include
     *
     * @var array
     */
	protected $availableIncludes = [
		
	];

	public function transform(SubModule $submodule)
	{
		return [
			'id' => $submodule->id,
            'module_id' => $submodule->module_id,
            'title' => $submodule->title,
            'description' => $submodule->description,
            'status' => $submodule->status,
            'percentage' => $submodule->percentage . '%',
            'links' => [
            	'rel' => 'self',
            	'uri' => '/submodule/' . $submodule->id,
            ],
		];
	}

}