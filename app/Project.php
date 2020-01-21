<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['owner_id', 'title', 'description', 'notes'];

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
}
