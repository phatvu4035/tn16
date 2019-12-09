<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/20/18
 * Time: 11:00 AM
 */

namespace App\Helpers;


use Maatwebsite\Excel\Facades\Excel;


class ExportExcel
{

    public function exportByTemplate($file_name, $data, $name = 'default')
    {

        Excel::load(storage_path('excel/exports_temp') . '/' . $file_name, function ($file) use ($data) {
            $sheet = $file->setActiveSheetIndex(0);
            foreach ($data as $k => $v) {
                $sheet->setCellValue($k, $v);
            }
        })->setFilename($name)->download('xlsx');
    }

    public function exportByView($view_name, $data)
    {

        //@TODO chưa làm
        Excel::create('newfile2', function ($excel) {

            $excel->sheet('Sheetname', function ($sheet) {

                $sheet->loadView('export.components.table401');


            });

        })->download();
    }

//    public function export401($data)
//    {
//        $config = [
//            'col' => [
//                'D' => 'tong_nv',
//                'E' => 'tong_tnct',
//                'F' => 'tong_nhan_su_nop_thue',
//                'G' => 'tong_tnct_ns_nop_thue',
//                'I' => 'thue_tncn'
//            ],
//            'row' => [
//                '10' => "Lãi vay",
//                '13' => "Lương NV",
//                '14' => 'GVNN có HĐLĐ',
//                '15' => 'com_thuong',
//                '16' => 'Thưởng NV',
//                '17' => 'Com NV',
//                '19' => 'ctv',
//                '20' => 'Lương CTV',
//                '21' => 'Com CTV',
//                '22' => 'Thưởng CTV',
//                '23' => 'Giảng viên Việt Nam',
//                '24' => 'TKCM',
//                '25' => 'Chia sẻ doanh thu',
//                '27' => 'Giảng viên Âu Mỹ1',
//                '28' => 'Giảng viên Philipine1',
//                '29' => 'Giảng viên Thái Lan1',
//                '31' => 'Giảng viên Âu Mỹ0',
//                '32' => 'Giảng viên Philipine0',
//                '33' => 'Giảng viên Thái Lan0',
//
//            ]
//        ];
//        $total_summary = [];
//        $data_temp = [];
//        $name = '401';
//        if (isset($data['data']) && $data['data']) {
//            $data_temp = [
//                'B1' => "BÁO CÁO KÊ KHAI THU NHẬP THÁNG (năm " . $data['data']['year'] . ")",
//                'B3' => $data['data']['month'],
//                'E3' => $data['data']['phap_nhan']
//            ];
//            $name = $data['data']['phap_nhan'] . '_401_' . $data['data']['month'] . $data['data']['year'];
//        }
//        if (isset($data['summary']) && $data['summary']) {
//            foreach ($data['summary'] as $summary) {
//                foreach ($config['row'] as $row_number => $row_name) {
//                    $endChar = substr($row_name, -1, 1);
//                    if (is_numeric($endChar)) {
//                        if ($summary['name'] . $summary['live_status'] == $row_name) {
//                            foreach ($config['col'] as $col_title => $col_name) {
//                                if (isset($data_temp[$col_title . $row_number])) {
//                                    $data_temp[$col_title . $row_number] += $summary[$col_name];
//                                } else {
//                                    $data_temp[$col_title . $row_number] = $summary[$col_name];
//                                }
//                            }
//                        }
//                    } else {
//
//                        if ($summary['name'] == $row_name) {
//                            if($row_name=="Thưởng NV"){
//                                $summary['tong_nv'] = $data['bonus']->tong_nv;
//                                $summary['tong_tnct'] = $data['bonus']->tong_tnct;
//                                $summary['tong_nhan_su_nop_thue'] = $data['bonus']->tong_nhan_su_nop_thue;
//                                $summary['tong_tnct_ns_nop_thue'] = $data['bonus']->tong_tnct_ns_nop_thue;
//                                $summary['thue_tncn'] = $data['bonus']->thue_tncn;
//                            }
//                            if($row_name=="Com NV"){
//                                $summary['tong_nv'] = $data['com']->tong_nv;
//                                $summary['tong_tnct'] = $data['com']->tong_tnct;
//                                $summary['tong_nhan_su_nop_thue'] = $data['com']->tong_nhan_su_nop_thue;
//                                $summary['tong_tnct_ns_nop_thue'] = $data['com']->tong_tnct_ns_nop_thue;
//                                $summary['thue_tncn'] = $data['com']->thue_tncn;
//                            }
//                            if($row_name=="com_thuong"){
//                                $summary['tong_nv'] = $data['com_thuong']->tong_nv;
//                                $summary['tong_tnct'] = $data['com_thuong']->tong_tnct;
//                                $summary['tong_nhan_su_nop_thue'] = $data['com_thuong']->tong_nhan_su_nop_thue;
//                                $summary['tong_tnct_ns_nop_thue'] = $data['com_thuong']->tong_tnct_ns_nop_thue;
//                                $summary['thue_tncn'] = $data['com_thuong']->thue_tncn;
//                            }
//
//                            foreach ($config['col'] as $col_title => $col_name) {
//                                if (isset($data_temp[$col_title . $row_number])) {
//                                    $data_temp[$col_title . $row_number] += $summary[$col_name];
//                                } else {
//                                    $data_temp[$col_title . $row_number] = $summary[$col_name];
//                                }
//
//                            }
//                        }
//
//
//                    }
//                }
//            }
//        }
//        $this->exportByTemplate('401.xlsx', $data_temp, $name);
//    }
    public function export401($data){
        $config = [
            'col' => [
                'D' => 'tong_nv',
                'E' => 'tong_tnct_cho_ca_nhan',
                'F' => 'tong_nv_chiu_thue',
                'G' => 'tong_tnct_cho_nv_nop_thue',
                'I' => 'thue_tncn'
            ],
            'row' => [
                '10' => "lai_vay",
                '13' => "luong_nv",
                '14' => 'gvnn_co_hdld',
                '15' => 'com_thuong',
                '16' => 'thuong_nv',
                '17' => 'com_nv',
                '19' => 'ctv',
                '20' => 'luong_ctv',
                '21' => 'com_ctv',
                '22' => 'thuong_ctv',
                '23' => 'giang_vien_viet_nam',
                '24' => 'tkcm',
                '25' => 'chia_se_doanh_thu',
                '27' => 'giang_vien_au_my_cu_tru',
                '28' => 'giang_vien_philipine_cu_tru',
                '29' => 'giang_vien_thai_lan_cu_tru',
                '31' => 'giang_vien_au_my_ko_cu_tru',
                '32' => 'giang_vien_philipine_ko_cu_tru',
                '33' => 'giang_vien_thai_lan_ko_cu_tru',

            ]
        ];
//        dd($data['summary']);
        $total_summary = [];
        $data_temp = [];
        $name = '401';
        if (isset($data['data']) && $data['data']) {
            $data_temp = [
                'B1' => "BÁO CÁO KÊ KHAI THU NHẬP THÁNG (năm " . $data['data']['year'] . ")",
                'B3' => $data['data']['month'],
                'E3' => $data['data']['phap_nhan']
            ];
            $name = $data['data']['phap_nhan'] . '_401_' . $data['data']['month'] . $data['data']['year'];
        }
        if (isset($data['summary']) && $data['summary']) {
            foreach ($config['row'] as $row_number => $row_name) {
                foreach ($config['col'] as $col_title => $col_name) {
                    $data_temp[$col_title . $row_number] = $data['summary'][$row_name][$col_name];
                }
            }
        }
//        dd($data_temp);
        $data_temp['G30'] = "=SUM(G31:G33)";

        $this->exportByTemplate('401.xlsx', $data_temp, $name);
    }


    public function export402($data)
    {

        $data_temp = [];
        $name = '402';
        $beginRow = 7;

        if (isset($data['data']) && $data['data']) {
            $data_temp = [
                'B1' => "QUYẾT TOÁN THUẾ " . $data['data']['year'] . " (FORM 05_1 có HĐLĐ)",
            ];
            $name = $data['data']['phap_nhan'] . '_402_' . $data['data']['year'];
        }

        $config = [
            'col' => [
                'C' => 'full',
                'B' => 'cmt',
                'D' => 'mst',
                'F' => 'tong_tnct',
                'I' => 'nguoi_phu_thuoc_giam_tru',
                'J' => "tong_so_tien_giam_tru_gia_canh",
                'L' => 'bao_hiem_duoc_tru',
                'N' => 'tong_tntt',
                'O' => 'tong_so_thue_tncn_da_khau_tru'
            ]
        ];
        $endRow = $beginRow;
        foreach ($data['summary'] as $k => $summary) {
            $endRow = $beginRow + $k;
            $data_temp['R' . ($beginRow + $k)] = "=if(O" . ($beginRow + $k) . "-Q" . ($beginRow + $k) . ">0,O" . ($beginRow + $k) . "-Q" . ($beginRow + $k) . ",0)";
            $data_temp['S' . ($beginRow + $k)] = "=if(O" . ($beginRow + $k) . "-Q" . ($beginRow + $k) . "<0,Q" . ($beginRow + $k) . "-O" . ($beginRow + $k) . ",0)";

            foreach ($config['col'] as $key_col => $value_col) {
                $data_temp[$key_col . ($beginRow + $k)] = isset($summary[$value_col]) ? $summary[$value_col] : 0;
            }
        }
        $data_temp['F6'] = "=SUM(F" . $beginRow . ":F" . $endRow . ")";
//        dd($data_temp);
        $this->exportByTemplate('402.xlsx', $data_temp, $name);

    }

    public function export403($data)
    {

        $data_temp = [];
        $name = '403';
        $beginRow = 6;

        if (isset($data['data']) && $data['data']) {
            $data_temp = [
                'B1' => "QUYẾT TOÁN THUẾ (FORM 05_2 ko có HĐLĐ)",
            ];
            $name = $data['data']['phap_nhan'] . '_403_' . $data['data']['year'];
        }

        $config = [
            'col' => [
                'B' => 'cmt1',
                'C' => 'full',
                'D' => 'mst',
                'E' => 'luu_tru',
                'F' => "tong_tnct",
                'J' => 'tong_so_thue_tncn_da_khau_tru'
            ]
        ];
        foreach ($data['summary'] as $k => $summary) {
            $data_temp['A' . ($beginRow + $k)] = $k + 1;
            foreach ($config['col'] as $key_col => $value_col) {
                $data_temp[$key_col . ($beginRow + $k)] = isset($summary[$value_col]) ? $summary[$value_col] : 0;
            }
        }
//        dd($data_temp);
        $this->exportByTemplate('403.xlsx', $data_temp, $name);

    }

    public function exportO1($data)
    {
        $data_temp = [];
        $name = 'O1';
        $beginRow = 10;

        if (isset($data['data']) && $data['data']) {
            $data_temp = [
                'B4' => $data['data']['phap_nhan'],
                'B5' => $data['data']['month'],
                'B6' => $data['data']['year'],
            ];
            $name = $data['data']['phap_nhan'] . '_O1_' . $data['data']['month'] . $data['data']['year'];
        }

        $config = [
            'col' => [
                'B' => 'full',
                'C' => 'employee_code',
                'D' => 'cmt',
                'E' => 'mst',
                'F' => "com_tnct",
                'G' => 'com_thue_tam_trich',
                'H' => 'com_thuc_nhan',
                "I" => 'thuong_tnct',
                'J' => 'thuong_thue_tam_trich',
                'K' => 'thuong_thuc_nhan',
                "L" => 'other_tnct',
                'M' => 'other_thue_tam_trich',
                'N' => 'other_thuc_nhan',
                'Q' => 'serial'
            ]
        ];
        foreach ($data['summary'] as $k => $summary) {
            $data_temp['A' . ($beginRow + $k)] = $k + 1;
            $data_temp['O' . ($beginRow + $k)] = "=sum(F" . ($beginRow + $k) . ",I" . ($beginRow + $k) . ",L" . ($beginRow + $k) . ")";
            $data_temp['P' . ($beginRow + $k)] = "=sum(G" . ($beginRow + $k) . ",J" . ($beginRow + $k) . ",M" . ($beginRow + $k) . ")";
            foreach ($config['col'] as $key_col => $value_col) {
                $data_temp[$key_col . ($beginRow + $k)] = isset($summary[$value_col]) ? $summary[$value_col] : 0;
            }
        }
//        dd($data_temp);
        $this->exportByTemplate('O1.xlsx', $data_temp, $name);
    }

}