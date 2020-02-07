<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = ['user_id', 'project_id', 'description', 'changes'];

    /**
     * Casts properties
     *
     * @var array
     */
    protected $casts = [
        'changes' => 'array'
    ];

    /**
     * Get subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Get user who did the activity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
