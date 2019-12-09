<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/12/18
 * Time: 2:48 PM
 */

namespace App\Http\Repositories\Contracts;


interface CrossCheckInfoRepositoryInterface
{
    /**
     * get data paginator
     * @param array $conditions
     * @return object
     */
    public function getDataBy($conditions);

    public function saveData($data);

    public function delete($conditions);
}