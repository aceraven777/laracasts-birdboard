<?php

namespace App;

trait RecordsActivity
{

    /**
     * Old attributes of the model
     *
     * @var array
     */
    public $oldAttributes = [];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function bootRecordsActivity()
    {
        $recordableEvents = static::recordableEvents();

        foreach ($recordableEvents as $event) {
            static::$event(function ($model) use ($event) {
                $description = $model->activityDescription($event);

                $model->recordActivity($description);
            });

            if ($event == 'updated') {
                static::updating(function ($model) {
                    $model->oldAttributes = $model->getOriginal();
                });
            }
        }
    }

    /**
     * Get Activity Description
     *
     * @param string $event
     * @return string
     */
    protected function activityDescription($event)
    {
        $class = class_basename($this);

        return $event . '_' . strtolower($class);
    }

    /**
     * Get recordable events 
     *
     * @return array
     */
    protected static function recordableEvents()
    {
        if (isset(static::$recordableEvents)) {
            return static::$recordableEvents;
        }

        return ['created', 'updated', 'deleted'];
    }

    /**
     * Get project activities
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }
    
    /**
     * Record Activity
     *
     * @param string $description
     * @return void
     */
    public function recordActivity($description)
    {
        $this->activities()->create([
            'project_id' => class_basename($this) == 'Project' ? $this->id : $this->project_id,
            'description' => $description,
            'changes' => $this->activityChanges(),
        ]);
    }

    /**
     * Get activity changes
     *
     * @return array|null
     */
    protected function activityChanges()
    {
        $changes = null;

        if ($this->wasChanged()) {
            $changes = [
                'before' => array_except(array_diff($this->oldAttributes, $this->getAttributes()), 'updated_at'),
                'after' => array_except($this->getChanges(), 'updated_at'),
            ];
        }

        return $changes;
    }
}
