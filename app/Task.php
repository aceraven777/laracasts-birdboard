<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use RecordsActivity;

    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = ['project_id', 'body'];

    /**
     * Update also the updated_at fields in the relationships
     *
     * @var array
     */
    protected $touches = ['project'];

    /**
     * Fields to casts
     *
     * @var array
     */
    protected $casts = [
        'completed' => 'boolean'
    ];

    /**
     * Recordable events
     *
     * @var array
     */
    protected static $recordableEvents = ['created', 'deleted'];

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
}
