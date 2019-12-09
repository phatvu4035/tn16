<?php

namespace App\Models;

use App\Traits\TopicaUsers;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use Notifiable;
    use TopicaUsers;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','provider','provider_id', 'role_id', 'employee_code','avatar', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Role
    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::user()) {
                if (Schema::hasColumn($model->getTable(), 'created_by'))
                    $model->created_by = Auth::user()->id;
                if (Schema::hasColumn($model->getTable(), 'updated_by'))
                    $model->updated_by = Auth::user()->id;
            }
        });
        static::updating(function ($model) {
            if (Auth::user()) {
                if (Schema::hasColumn($model->getTable(), 'updated_by'))
                    $model->updated_by = Auth::user()->id;
            }
        });

        static::updated(function ($model) {
//            Log::useDailyFiles(storage_path('logs/data/'.$model->getTable().'/log_info'));
//            Log::info("Type:updated;Model:".$model->getTable().";Old Date:".json_encode($model->getOriginal()).";New Data:".json_encode($model->getDirty()).";User:".Auth::user()->id.";UserName:".Auth::user()->name);
        });
        static::created(function ($model) {
//            Log::useDailyFiles(storage_path('logs/data/'.$model->getTable().'/log_info'));
//            Log::info("Type:created;Model:".$model->getTable().";Old Date:".json_encode($model->getOriginal()).";New Data:".json_encode($model->getDirty()).";User:".Auth::user()->id.";UserName:".Auth::user()->name);
        });
        static::deleted(function ($model) {
//            Log::useDailyFiles(storage_path('logs/data/'.$model->getTable().'/log_info'));
//            Log::info("Type:deleted;Model:".$model->getTable().";Old Date:".json_encode($model->getOriginal()).";New Data:".json_encode($model->getDirty()).";User:".Auth::user()->id.";UserName:".Auth::user()->name);
        });
    }
}
