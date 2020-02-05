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
    protected $fillable = ['project_id', 'description', 'changes'];

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
}
