<?php
/**
 * Created by PhpStorm.
 * User: johna
 * Date: 8/7/2018
 * Time: 2:37 PM
 */

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class Topica extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'topica';
    }

}