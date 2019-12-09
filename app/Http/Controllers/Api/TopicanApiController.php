<?php 
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;


class TopicanApiController
{
	protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getList(Request $request)
    {
        $getData = $request->all();

        $data = $this->userRepository->getDataBy( $getData );

        if (isset($getData['isHtml']) && $getData['isHtml']) {
            $header = getListTableTopican();

            $r = view('includes.component.table', compact('data', 'header'))->render();
            return response()->json($r);
        }

        return response()->json($data);

    }
}



?>