<?php

namespace App\Http\Repositories\Contracts;


interface SPRepositoryInterface
{
    /**
     * get data paginator
     * @param array $conditions
     * @return object
     */
    public function getDataBy($conditions);
}

?>