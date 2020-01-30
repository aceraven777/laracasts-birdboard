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
     * Model booting method
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($task) {
            $task->project->recordActivity('created_task');
        });
    }

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

        $this->project->recordActivity('completed_task');
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
    }
}
