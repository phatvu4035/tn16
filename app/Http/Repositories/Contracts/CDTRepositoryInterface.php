<?php

namespace App\Http\Repositories\Contracts;


interface CDTRepositoryInterface
{
    /**
     * get data paginator
     * @param array $conditions
     * @return object
     */
    public function getDataBy($conditions);
}

?>