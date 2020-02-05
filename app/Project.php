<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = ['owner_id', 'title', 'description', 'notes'];

    /**
     * Old value of the project (used in recording activity)
     *
     * @var array
     */
    public $old = [];

    /**
     * Get owner of project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get project tasks
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany('App\Task');
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
     * Get project uri path
     *
     * @return string
     */
    public function path()
    {
        return "/projects/{$this->id}";
    }

    /**
     * Add a task
     *
     * @param string $body
     * @return \App\Task
     */
    public function addTask($body)
    {
        return $this->tasks()->create(['body' => $body]);
    }

    /**
     * Record Project Activity
     *
     * @param string $description
     * @return void
     */
    public function recordActivity($description)
    {
        $this->activities()->create([
            'project_id' => $this->id,
            'description' => $description,
            'changes' => $this->activityChanges($description),
        ]);
    }

    /**
     * Get activity changes
     *
     * @param string $description
     * @return array|null
     */
    protected function activityChanges($description)
    {
        $changes = null;

        if ($description == 'updated') {
            $changes = [
                'before' => array_except(array_diff($this->old, $this->getAttributes()), 'updated_at'),
                'after' => array_except($this->getChanges(), 'updated_at'),
            ];
        }

        return $changes;
    }
}
