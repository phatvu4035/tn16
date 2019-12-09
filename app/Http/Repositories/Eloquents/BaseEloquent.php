<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/12/18
 * Time: 2:42 PM
 */

namespace App\Http\Repositories\Eloquents;


use App\Http\Repositories\Contracts\BaseRepositoryInterface;

abstract class BaseEloquent implements BaseRepositoryInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    /**
     * Get the model
     * @return Void
     */
    abstract public function getModel();


    /**
     * Set the model
     * @return Void
     */
    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    public function getData(){
        return $this->paginate(PAGE_NUMBER);
    }

}