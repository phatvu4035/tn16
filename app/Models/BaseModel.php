<?php
/**
 * Created by PhpStorm.
 * User: johna
 * Date: 8/9/2018
 * Time: 10:14 AM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BaseModel extends Model
{
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
            //Log::useDailyFiles(storage_path('logs/data/'.$model->getTable().'/log_info'));
            //Log::info("Type:updated;Model:".$model->getTable().";Old Date:".json_encode($model->getOriginal()).";New Data:".json_encode($model->getDirty()).";User:".Auth::user()->id.";UserName:".Auth::user()->name);
        });
        static::created(function ($model) {
            //Log::useDailyFiles(storage_path('logs/data/'.$model->getTable().'/log_info'));
            //Log::info("Type:created;Model:".$model->getTable().";Old Date:".json_encode($model->getOriginal()).";New Data:".json_encode($model->getDirty()).";User:".Auth::user()->id.";UserName:".Auth::user()->name);
        });
        static::deleted(function ($model) {
            //Log::useDailyFiles(storage_path('logs/data/'.$model->getTable().'/log_info'));
            //Log::info("Type:deleted;Model:".$model->getTable().";Old Date:".json_encode($model->getOriginal()).";New Data:".json_encode($model->getDirty()).";User:".Auth::user()->id.";UserName:".Auth::user()->name);
        });
    }
}