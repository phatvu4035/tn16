<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/12/18
 * Time: 2:48 PM
 */

namespace App\Http\Repositories\Contracts;


interface UserRepositoryInterface
{

    /**
     * @param $email
     * @return mixed
     */
    public function getByEmail($email);

}