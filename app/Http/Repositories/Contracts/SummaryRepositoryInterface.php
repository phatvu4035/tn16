<?php

namespace App\Http\Repositories\Contracts;

interface SummaryRepositoryInterface
{
    /**
     * @param array $conditions
     * @param bool $pagination
     * @return mixed
     */
    public function getDataBy($conditions = [], $pagination = true);

    /**
     * @param $data
     * @return mixed
     */
    public function saveData($data);

    public function delete($conditions);

    public function getReportData($conditions);

    /**
     * @param $conditions
     * @param bool $pagination
     * @return mixed
     */
    public function getDataFttOfEmployee($conditions, $pagination = true);

    public function getDataOfEmployee($conditions);

    public function saveDataByOrderId($orderId, $newData);

    public function get401ReportOfSalaryAndComBonus($conditions);
}

?>