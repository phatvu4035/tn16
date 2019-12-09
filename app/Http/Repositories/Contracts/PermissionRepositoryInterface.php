<?php 

namespace App\Http\Repositories\Contracts;

interface PermissionRepositoryInterface
{
    /**
     * get data paginator
     * @param array $conditions
     * @return object
     */
    public function getDataBy($conditions);

    public function saveData($data);
}

?>