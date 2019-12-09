<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/20/18
 * Time: 11:00 AM
 */

namespace App\Helpers;


use Maatwebsite\Excel\Facades\Excel;

class ImportExcel
{

    /**
     * get Data from excel
     * @param $file
     * @return mixed
     */
    public function getData($file)
    {
        ini_set('memory_limit', '-1');


        // get data from excel file
        $data = Excel::selectSheetsByIndex(0)->load($file, function ($reader) {
//            dd($reader->toArray());
            $reader->formatDates(false);
        })->get();
        // remove null data from excel
//        dd($data);
        $data = $data->filter(function ($value, $key) {

            $flagCheckNull = false;
            foreach ($value as $v) {
                $flagCheckNull = $flagCheckNull || $v != '';
            }
            return $flagCheckNull;
        });
        return $data;
    }

    public function getManySheetData($file)
    {
        ini_set('memory_limit', '-1');


        // get data from excel file
        $data = Excel::load($file, function ($reader) {
//            dd($reader->toArray());
            $reader->formatDates(false);
        })->get();
        return $data;
    }

    public function getDataSalary($file)
    {
        config(['excel.import.startRow' => 1]);
        config(['excel.import.heading' => false]);

        // get data from excel
        $dataExcel = $this->getManySheetData($file);
        $data = $dataExcel[0];
        $dataConfig = [];
        $dataTable = [];
        $dataFtt = ['Com', 'Thưởng', "Khác"];
        $config = $dataExcel[1];
        if (!$config) {
            throw new \Exception("File bị sai mẫu (không có Sheet2)");
        }
        foreach ($config as $row => $c) {
            if ($row > 0)
                $dataConfig[$c[0]][$c[1]] = $c[2];
        }
//        dd($dataConfig);
        $returnData = [];
        $listData = [];
        $firstRow = $data[0];
        $secondRow = $data[1];
        $titleRow = $data[2];
//        dd($secondRow);
        $dataTable[] = [
            'title' => "Line",
            'field' => 'line',
            'visible'=>true
        ];
        $temp_data_summary = [
            2 => "Tên NV",
            3 => 'Mã NV',
            4 => 'ID',
            5 => 'Mã số thuế',
            6 => 'Tổng TN trước thuế',
            7 => 'Tổng Non tax',
            8 => 'Tổng TNCT',
            9 => 'BHXH',
            10 => 'Thuế tạm trích',
            11 => 'Thực nhận',
            12 => 'Giảm trừ bản thân',
            13 => 'Giảm trừ gia cảnh',
            14 => 'Note',
            15 => 'Ref',
            16 => 'Nội dung',
            17 => 'Loại thẻ',
            18 => 'Tình trạng cư trú',
            19 => 'Quốc tịch',
            20 => 'Đã thanh toán',
            21 => 'Còn lại cần thanh toán',
            22 => 'Thuế đã trích',
        ];
        if ($dataConfig[2] != $temp_data_summary) {
            $dataConfig[2] = $temp_data_summary;
        }
        foreach ($data as $k => $d) {
            if ($k > 2) {
                foreach ($d as $key => $value) {
//                    dd($firstRow->$key);
                    if (isset($dataConfig[1][$firstRow->$key])) {
                        $returnData[$k][$dataConfig[1][$firstRow->$key]][$titleRow->$key] = $value;
                    }
                    if (isset($dataConfig[2][$secondRow->$key])) {
                        if (isset($listData[$k][str_slug($dataConfig[2][$secondRow->$key], '_')])) {
                            $listData[$k][str_slug($dataConfig[2][$secondRow->$key], '_')] .= " " . $value;
                        } else {
                            $listData[$k][str_slug($dataConfig[2][$secondRow->$key], '_')] = $value;
                        }
//                        dd($dataConfig[2]);
                        if (str_slug($dataConfig[2][$secondRow->$key], '_') != 'ma_nv' && is_numeric($value)) {
                            $dataTable[$secondRow->$key] = [
                                'title' => $dataConfig[2][$secondRow->$key],
                                'field' => str_slug($dataConfig[2][$secondRow->$key], '_'),
                                'formatter' => "money",
                                'formatterParams' => ['precision' => 0],
                                'visible'=>true
                            ];
                        } else {
                            $dataTable[$secondRow->$key] = [
                                'title' => $dataConfig[2][$secondRow->$key],
                                'field' => str_slug($dataConfig[2][$secondRow->$key], '_'),
                                'visible'=>true
                            ];
                        }
                    }
                    if (in_array($titleRow->$key, $dataFtt)) {
                        $listData[$k][str_slug($titleRow->$key)] = $value;
                        $dataTable[str_slug($titleRow->$key)] = [
                            'title' => $titleRow->$key,
                            'field' => str_slug($titleRow->$key, '_'),
                            'cssClass' => 'alert-primary',
                            'visible'=>true
                        ];
                    }
                }
                $listData[$k]['data'] = view('includes.viewEmployee', ['employee' => $returnData[$k]])->render();
                $listData[$k]['value'] = $returnData[$k];
            }
        }
        return [
            'dataTable' => array_values($dataTable),
            'listData' => array_values($listData)
        ];

//        config(['excel.import.startRow' => IMPORT_START_ROW]);
//        config(['excel.import.heading' => false]);
//
//        // get data from excel
//        $data = $this->getData($file);
//
//
//        // define data
//        $mappingData = config('global.mappingDataSalary');
//
//
//        // mapping data
//        $returnData = [];
//        foreach ($data as $key => $d) {
//            $flagCheckNull = false;
//            foreach ($mappingData as $k => $m) {
//                if ($m['required']) {
//                    $flagCheckNull = $flagCheckNull || $d[$m['position']] != '';
//                }
//                $returnData[$key][$k] = $d[$m['position']];
//            }
//            if (!$flagCheckNull) {
//                unset($returnData[$key]);
//            }
//        }
//        return $returnData;
    }

    public function getDataFTT($file)
    {
        config(['excel.import.startRow' => IMPORT_START_ROW_FTT]);
        config(['excel.import.heading' => false]);

        // get data from excel
        $data = $this->getData($file);

        // mappding data
        $mappdingData = config('global.mappingDataFTT');

        return $this->mappingData($data, $mappdingData);
    }

    private function mappingData($data, $mappingData)
    {
        // mapping data
        $returnData = [];
        foreach ($data as $key => $d) {
//            dd($d);
            $flagCheckNull = false;
            foreach ($mappingData as $k => $m) {
                if (isset($m['required']) && $m['required']) {
                    $flagCheckNull = $flagCheckNull || $d[$m['position']] != '';
                }
                if (isset($m['type']) && $m['type']) {
                    if ($m['type'] == "pattern") {
                        preg_match($m['pattern'], $d[$m['sourceCollumn']], $match);
                        if (empty($returnData[$key][$k."_xbeat"])){
                            $returnData[$key][$k] = isset($match[0]) ? $match[0] : null;
                        } else {
                            $returnData[$key][$k] = $returnData[$key][$k."_xbeat"];
                        }
                        continue;
                    }
                }
                if (isset($d[$m['position']]))
                    $returnData[$key][$k] = $d[$m['position']];
            }
            if (isset($m['required']) && !$flagCheckNull) {
                unset($returnData[$key]);
            }
        }
        return $returnData;
    }

    public function getDataCrossCheck($file)
    {
        $data = $this->getData($file);

        foreach ($data as $key => $value) {
            $psNo = intval(preg_replace('/[^0-9]/', '', $value['ps_no']));

//            if(!startsWith($value['tk_d.ung'], "1")
//                && !startsWith($value['tk_d.ung'], "3387") && !startsWith($value['tk_d.ung'], "3335") ) {
//
//                unset($data[$key]);
//            }
            if ($psNo == 0 || $value['ngay_c.tu'] == null) {
                unset($data[$key]);
            }
        }
        // mappding data
        $mappdingData = config('global.mappingDataCrossCheck');

        return $this->mappingData($data, $mappdingData);
    }
}