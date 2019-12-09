<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/12/18
 * Time: 2:48 PM
 */

namespace App\Http\Repositories\Contracts;


interface OrderRepositoryInterface
{
    /**
     * get data paginator
     * @param array $conditions
     * @param boolean $pagination
     * @return object
     */
    public function getDataBy($conditions = [], $pagination = true);

    public function saveData($data);

    public function findById($id);

    public function delete($conditions);

    public function updateStatus($id, $data);
}