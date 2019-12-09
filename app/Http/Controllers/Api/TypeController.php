<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 7/5/18
 * Time: 10:10 AM
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Repositories\Contracts\EmpRentRepositoryInterface;
use App\Http\Repositories\Contracts\TypeRepositoryInterface;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    protected $typeRepository;

    public function __construct(TypeRepositoryInterface $typeRepository)
    {
        $this->typeRepository = $typeRepository;
    }

    public function getList(Request $request)
    {
        $getData = $request->all();

        $data = $this->typeRepository->getDataBy($getData);

        if (isset($getData['isHtml']) && $getData['isHtml']) {
            $header = getListTableType();

            $r = view('includes.component.table_chungtu', compact('data', 'header'))->render();
            return response()->json($r);
        }

        return response()->json($data);

    }
}


