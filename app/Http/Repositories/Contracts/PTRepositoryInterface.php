<?php

namespace App\Http\Repositories\Contracts;


interface PTRepositoryInterface
{
    /**
     * get data paginator
     * @param array $conditions
     * @return object
     */
    public function getDataBy($conditions);

    public function saveMany($data);
}

?>