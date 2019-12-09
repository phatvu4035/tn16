<?php 

namespace App\Http\Repositories\Contracts;

interface TypeRepositoryInterface
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
}

?>