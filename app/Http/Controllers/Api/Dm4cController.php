<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 7/5/18
 * Time: 10:10 AM
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Repositories\Contracts\CDTRepositoryInterface;
use App\Http\Repositories\Contracts\PTRepositoryInterface;
use App\Http\Repositories\Contracts\SPRepositoryInterface;
use App\Http\Repositories\Contracts\SummaryRepositoryInterface;
use Illuminate\Http\Request;

class Dm4cController extends Controller
{
    protected $cDTRepository;

    protected $sPRepository;

    protected $pTRepository;

    protected $summaryRepository;

    public function __construct(SummaryRepositoryInterface $summaryRepository, CDTRepositoryInterface $cDTRepository, SPRepositoryInterface $sPRepository, PTRepositoryInterface $pTRepository)
    {
        $this->cDTRepository = $cDTRepository;
        $this->sPRepository = $sPRepository;
        $this->pTRepository = $pTRepository;
        $this->summaryRepository = $summaryRepository;
    }

    function getCDT(Request $request)
    {
        $data = $request->all();

        $cdt = $this->cDTRepository->getDataBy([
            "search" => isset($data['q']) ? $data['q'] : ""
        ])->toArray();
        $cdtData = [];
        foreach ($cdt as $key => $value) {
            $cdtDataTemp = [
                "id" => $value['complete_code'],
                "text" => $value['complete_code'],
            ];

            $cdtData[] = $cdtDataTemp;
        }
        return response()->json([
            "results" => $cdtData
        ]);
    }

    function getSP(Request $request)
    {
        $data = $request->all();

        $sp = $this->sPRepository->getDataBy([
            "search" => isset($data['q']) ? $data['q'] : ""
        ])->toArray();
        $spData = [];
        foreach ($sp as $key => $value) {
            $spDataTemp = [
                "id" => $value['shortened_code'],
                "text" => $value['shortened_code'],
            ];

            $spData[] = $spDataTemp;
        }
        return response()->json([
            "results" => $spData
        ]);
    }

    function getPT(Request $request)
    {
        $data = $request->all();

        $conditions = [
            "search" => isset($data['q']['term']) ? $data['q']['term'] : ""
        ];
        $conditions["search"] = isset($data['q']) && is_string($data['q']) ? $data['q'] : $conditions["search"];
        if (isset($data['checkPermission']) && $data['checkPermission'] && auth()->user()->role->name != "Administrator") {
            $conditions['phap_nhan_in'] = array_column(auth()->user()->role->permissionPN->toArray(), "short_code");
        }

        $pt = $this->pTRepository->getDataBy($conditions)->toArray();
        $ptData = [];
        foreach ($pt as $key => $value) {
            $ptDataTemp = [
                "id" => $value['short_code'],
                "text" => $value['short_code'],
            ];

            $ptData[] = $ptDataTemp;
        }
        return response()->json([
            "results" => $ptData
        ]);
    }

    function getSinglePT(Request $request, $short_code)
    {
        $pt = $this->pTRepository->getDataBy([
            "short_code" => $short_code
        ])->toArray();

        if (empty($pt)) {
            $result = [
                'id' => $short_code,
                'text' => 'Không tìm thấy'
            ];
        } else {
            $result = [
                "id" => $pt[0]['short_code'],
                "text" => $pt[0]['short_code'],
            ];
        }

        return response()->json([
            "results" => $result
        ]);
    }

    function getSingleSP(Request $request, $shortened_code)
    {
        $sp = $this->sPRepository->getDataBy([
            "shortened_code" => $shortened_code
        ])->toArray();

        if (empty($sp)) {
            $result = [
                'id' => $shortened_code,
                'text' => 'Không tìm thấy'
            ];
        } else {
            $result = [
                "id" => $sp[0]['shortened_code'],
                "text" => $sp[0]['shortened_code'],
            ];
        }

        return response()->json([
            "results" => $result
        ]);
    }

    function getSingleCDT(Request $request, $shortened_code)
    {
        $cdt = $this->cDTRepository->getDataBy([
            "complete_code" => $shortened_code
        ])->toArray();

        if (empty($cdt)) {
            $result = [
                'id' => $shortened_code,
                'text' => 'Không tìm thấy'
            ];
        } else {
            $result = [
                "id" => $cdt[0]['complete_code'],
                "text" => $cdt[0]['complete_code'],
            ];
        }

        return response()->json([
            "results" => $result
        ]);
    }

    function getMyPT(Request $request)
    {
        $data = $request->all();

        $conditions = [
            "search" => isset($data['q']['term']) ? $data['q']['term'] : ""
        ];
        $conditions["search"] = isset($data['q']) && is_string($data['q']) ? $data['q'] : $conditions["search"];
        if (isset($data['checkPermission']) && $data['checkPermission'] && auth()->user()->role->name != "Administrator") {
            $conditions['phap_nhan_in'] = array_column(auth()->user()->role->permissionPN->toArray(), "short_code");
        }
        $conditions['phap_nhan_in'] = $this->summaryRepository->getPTofAuth();
        $pt = $this->pTRepository->getDataBy($conditions)->toArray();
        $ptData = [];
        foreach ($pt as $key => $value) {
            $ptDataTemp = [
                "id" => $value['short_code'],
                "text" => $value['short_code'],
            ];

            $ptData[] = $ptDataTemp;
        }
        return response()->json([
            "results" => $ptData
        ]);
    }
}


