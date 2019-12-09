<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/12/18
 * Time: 2:48 PM
 */

namespace App\Http\Repositories\Contracts;


interface EmployeeRepositoryInterface
{
    /**
     * get data paginator
     * @param array $conditions
     * @param boolean $pagination
     * @return object
     */
    public function getDataBy($conditions = [], $pagination = true);

    public function saveData($data);

    public function createOrUpdateData($data);

    /**
     * @param $conditions
     * @return mixed
     */
    public function getReportData402($conditions);

    /**
     * @param $conditions
     * @return mixed
     */
    public function getReportDataO1($conditions);
}