<?php

namespace App\Http\Repositories\Contracts;


interface HDRepositoryInterface
{
    /**
     * get data paginator
     * @param array $conditions
     * @return object
     */
    public function getDataBy($conditions);
}

?>