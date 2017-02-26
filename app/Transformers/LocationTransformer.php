<?php
namespace App\Transformers;

use App\Location;
use League\Fractal;

class LocationTransformer extends Fractal\TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'activity',
    ];


    public function transform(Location $meeting)
    {
        return [
            'id' => $meeting->id,
            'activty_id' => $meeting->activity_id,
            'project_name' => $meeting->activity->title,
            'agenda' => $meeting->agenda,
            'title' => $meeting->title,
            'location' => $meeting->location,
            'long' => $meeting->long,
            'lat' => $meeting->lat,
            'status' => $meeting->status,
            'date' => $meeting->date,
        ];
    }

    public function includeActivity(Location $location)
    {
        $activity = $location->activity;
        return $this->item($activity, new ActivityTransformer());
    }

}