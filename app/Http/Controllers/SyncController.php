<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Contracts\HDRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Repositories\Contracts\CDTRepositoryInterface;
use App\Http\Repositories\Contracts\PTRepositoryInterface;
use App\Http\Repositories\Contracts\SPRepositoryInterface;

class SyncController extends Controller
{
    protected $cDTRepository;

    protected $sPRepository;

    protected $pTRepository;

    protected $hDRepository;

    public function __construct(CDTRepositoryInterface $cDTRepository, SPRepositoryInterface $sPRepository, PTRepositoryInterface $pTRepository, HDRepositoryInterface $hDRepository)
    {
        $this->cDTRepository = $cDTRepository;
        $this->sPRepository = $sPRepository;
        $this->pTRepository = $pTRepository;
        $this->hDRepository = $hDRepository;
    }
    public function index()
    {
        return view("sync.index");
    }

    private function sync($slug, $repository)
    {
        $maxUpdate = $this->$repository->getDataBy(["maxDate" => true]);
        $data = [
            'filter' => []
        ];


        if (!empty($maxUpdate->items)) {
            $data['filter'] = [
                "updated_at" => [
                    ">" => $maxUpdate->updated_at->format('Y-m-d H:i:s')
                ]
            ];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://listmaster.topica.asia/".$slug."/search");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            'Authorization: Bearer anIUuelJI-Ex0nnXy4RaRTnEr_IJI2Q3',
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $server_output = json_decode(curl_exec ($ch), true);
        $items = $server_output['items'];
        while (isset($server_output['_links']['next'])) {
            curl_setopt($ch, CURLOPT_URL, $server_output['_links']['next']['href']);
            $server_output = json_decode(curl_exec ($ch), true);
            $items = array_merge($items, $server_output['items']);
        }

        curl_close ($ch);
        $this->$repository->saveMany($items);
    }

    public function execute()
    {
        $listSync = [
            [
                'slug' => 'pt_list',
                'repository' => 'pTRepository'
            ],
            [
                'slug' => 'sp_list',
                'repository' => 'sPRepository'
            ],
            [
                'slug' => 'cdt_list',
                'repository' => 'cDTRepository'
            ],
            [
                'slug' => 'hd_list',
                'repository' => 'hDRepository'
            ],
        ];

        foreach ($listSync as $key => $value) {
            $this->sync($value['slug'], $value['repository']);
        }
        return 123;
    }
}
