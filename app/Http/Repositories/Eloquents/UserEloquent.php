<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/12/18
 * Time: 2:48 PM
 */

namespace App\Http\Repositories\Eloquents;


use App\Http\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Auth;


class UserEloquent extends BaseEloquent implements UserRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return User::class;
    }

    /**
     * @param $email
     * @return mixed
     */
    public function getByEmail($email){
        return $this->model->where('email',$email)->first();
    }

    public function getDataBy($conditions = [], $pagination = true )
    {
        $data = $this->model;

        if ($conditions) {
            
            if (isset($conditions['search']) && $conditions['search']) {
                $data = $data->where(function ($q) use ($conditions) {
                    $q->where('name', 'like', '%' . $conditions['search'] . '%')
                        ->orWhere('email', 'like', '%' . $conditions['search'] . '%');
                });

            }

            if(isset($conditions['id']) && $conditions['id']) {
                $data = $data->where('id', $conditions['id']);
            }
            /**
            * Find by name
            */
            if (isset($conditions['name']) && $conditions['name']) {
                $data = $data->where('name', $conditions['name']);
            }
            /**
            * Find by email
            */
            if (isset($conditions['email']) && $conditions['email']) {
                $data = $data->where('email', $conditions['email']);
            }

            // Check page number
            if (isset($conditions['page']) && $conditions['page']) {
                $currentPage = $conditions['page'];
                Paginator::currentPageResolver(function () use ($currentPage) {
                    return $currentPage;
                });
            }
        }

        if ($pagination) {
            return $data->paginate(ITEM_NUMBER);
        } else {
            return $data->get();
        }
    }

    public function createOrUpdateUser($data)
    {
        $user = $this->model->firstOrNew( ['id' => $data['id'] ] )->fill($data);
        $user->save();
        return $user;
    }

    public function destroy($id)
    {
        $message = [];
        if(Auth::user()->id == $id) {
            $message['type'] = 'error';
            $message['message'] = 'Không thể khóa tài khoản của chính bạn';
            return $message;
        }
        
        $user = $this->getDataBy(['id' => $id])->first();

        //Delete role
//        $user->delete();
        $user->active = 0;
        $user->save();

        $message['type'] = 'success';
        $message['message'] = 'Đã khóa tài khoản '.$user->name;
        return $message;

    }


}