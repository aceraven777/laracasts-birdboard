<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get user owned projects
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->hasMany('App\Project', 'owner_id')->latest('updated_at');
    }

    /**
     * Get user projects (including all project he invited to)
     *
     * @return void
     */
    public function accessibleProjects()
    {
        $user_id = $this->id;

        return Project::where('owner_id', $user_id)
            ->orWhereHas('members', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->get();
    }
}
