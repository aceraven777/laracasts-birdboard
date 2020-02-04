<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['project_id', 'body'];

    protected $touches = ['project'];

    protected $casts = [
        'completed' => 'boolean'
    ];

    /**
     * Get the project of the task
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
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
     * Get task uri path
     *
     * @return string
     */
    public function path()
    {
        return "/projects/{$this->project->id}/tasks/{$this->id}";
    }

    /**
     * Mark project as complete
     *
     * @return void
     */
    public function complete()
    {
        $this->completed = true;
        $this->save();

        $this->recordActivity('completed_task');
    }

    /**
     * Mark project as incomplete
     *
     * @return void
     */
    public function incomplete()
    {
        $this->completed = false;
        $this->save();

        $this->recordActivity('incompleted_task');
    }

    /**
     * Record Task Activity
     *
     * @param string $description
     * @return void
     */
    public function recordActivity($description)
    {
        $this->activities()->create([
            'project_id' => $this->project_id,
            'description' => $description,
        ]);
    }
}
