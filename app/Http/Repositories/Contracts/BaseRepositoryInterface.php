<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/12/18
 * Time: 2:41 PM
 */

namespace App\Http\Repositories\Contracts;


interface BaseRepositoryInterface
{
    /**
     * get All Data
     * @return mixed
     */
    public function getData();

}