<?php
/**
 * Created by PhpStorm.
 * User: jav
 * Date: 6/15/18
 * Time: 9:45 AM
 */

const ITEM_NUMBER = 15;
const IMPORT_START_ROW = 4;
const IMPORT_START_ROW_FTT = 3;
const SALARY = 'Lương NV';
const UPDATE_STATUS_SALARY = false;

function getListEmailTopica()
{
    return [
        'anhvn9@topica.edu.vn',
        'hoangld2@topica.edu.vn',
        'quanglm2@topica.edu.vn',
        'vantt2@topica.edu.vn',
        'phatvv@topica.edu.vn'
    ];
}

function getListTableEmpRent()
{
    return [
        'emp_name' => [
            'name' => 'Tên',
            'type' => 'string',
            'class' => ''
        ],
        'identity_type' => [
            'name' => 'Loại thẻ',
            'type' => 'option',
            'option' => [
                'cmt' => 'CMT',
                'hc' => 'Hộ chiếu'
            ],
            'class' => ''
        ],
        'identity_code' => [
            'name' => 'Số thẻ',
            'type' => 'string',
            'class' => ''
        ],
        'emp_code_date' => [
            'name' => 'Ngày đăng ký',
            'type' => 'datetime',
            'class' => 'text-center'
        ],
        'emp_code_place' => [
            'name' => 'Nơi đăng ký',
            'type' => 'string',
            'class' => ''
        ],
        'emp_tax_code' => [
            'name' => 'Mã số thuế',
            'type' => 'string',
            'class' => ''
        ],
        'emp_country' => [
            'name' => 'Quốc gia',
            'type' => 'string',
            'class' => ''
        ],
        'emp_live_status' => [
            'name' => 'Tình trạng cư trú',
            'type' => 'option',
            'option' => [
                '0' => 'Không cư trú',
                '1' => 'Cư trú'
            ],
            'class' => ''
        ],
        'emp_account_number' => [
            'name' => 'Số tài khoản',
            'type' => "string",
            'class' => ''
        ],
        'emp_account_bank' => [
            'name' => 'Ngân hàng',
            'type' => "string",
            'class' => ''
        ],
        'action' => [
            'name' => '',
            'type' => 'action',
            'action' => ['edit' => 'emp_rent.edit', 'delete' => 'emp_rent.destroy'],
            'class' => '',
            'checkRole' => [
                'edit' => 'edit.rent_employee',
                'delete' => 'delete.rent_employee'
            ]
        ]
    ];
}

function getListTableType()
{
    return [
        'name' => [
            'name' => 'Tên',
            'type' => 'string',
            'class' => ''
        ],
       'action' => [
           'name' => '',
           'type' => 'action',
           'action' => ['edit' => 'type.edit', 'delete' => 'type.destroy'],
           'class' => '',
           'checkRole' => [
                'edit' => 'edit.rent_employee',
                'delete' => 'delete.rent_employee'
            ]
       ],
    ];
}

function getListTableSummary()
{
    return [
        'month_year' => [
            'name' => 'Tháng',
            'type' => 'string',
            'class' => ''
        ],
        'name' => [
            'name' => 'Tên',
            'type' => 'string',
            'class' => '',
            'option' => [
                'value' => [
                    '1' => [
                        'table' => 'employees',
                        'value' => 'full_name'
                    ],
                    '2' => [
                        'table' => 'employeeRentWithDelete',
                        'value' => 'emp_name'
                    ]
                ]
            ]
        ],
        'employee_code' => [
            'name' => 'Mã NV',
            'type' => 'string',
            'class' => '',
            'option' => [
                'value' => 'employee_code',
                'conditions' => [
                    'employee_table', '=', 'employees'
                ]
            ]
        ],
        'identity_type' => [
            'name' => 'ID',
            'type' => 'string',
            'class' => '',
            'option' => [
                'value' => 'employee_code',
                'conditions' => [
                    'employee_table', '=', 'employee_rent'
                ]
            ]
        ],
        'ma_so_thue' => [
            'name' => 'MST',
            'type' => 'string',
            'class' => '',
            'display' => false
        ],
        'phap_nhan' => [
            'name' => 'Pháp nhân',
            'type' => 'string',
            'class' => '',
            'display' => false
        ],
        'tong_thu_nhap_truoc_thue' => [
            'name' => 'Tổng thu nhập trước thuế',
            'type' => 'number_format',
            'class' => ''
        ],
        'tong_non_tax' => [
            'name' => 'Tổng TN không chịu thuế',
            'type' => 'number_format',
            'class' => ''
        ],
        'tong_tnct' => [
            'name' => 'Tổng TNCT',
            'type' => 'number_format',
            'class' => ''
        ],
        'bhxh' => [
            'name' => 'BHXH',
            'type' => 'number_format',
            'class' => ''
        ],
        'thue_tam_trich' => [
            'name' => 'Thuế',
            'type' => 'number_format',
            'class' => ''
        ],
        'thuc_nhan' => [
            'name' => 'Thực nhận',
            'type' => 'number_format',
            'class' => ''
        ],
        'giam_tru_ban_than' => [
            'name' => 'Giảm trừ bản thân',
            'type' => 'number_format',
            'class' => '',
            'display' => false
        ],
        'giam_tru_gia_canh' => [
            'name' => 'Giảm trừ gia cảnh',
            'type' => 'number_format',
            'class' => '',
            'display' => false
        ],
//        'type' => [
//            'name' => 'Loại CT',
//            'type' => 'string',
//            'class' => '',
//            'option' => [
//                'value' => [
//                    [
//                        'table' => 'typeName',
//                        'value' => 'name'
//                    ]
//                ]
//            ]
//        ],
//        'noi_dung' => [
//            'name' => 'Diễn giải',
//            'type' => 'string',
//            'class' => 'w-100px'
//        ],


        'ajax_for_summary' => [
            'name' => '',
            'type' => 'ajax',
            'action' => [
                'view_ajax_modal_temp_1' => [
                    'temp' => 'order.view.employee',
                    'params' => ['employee_code']
                ],
                'view_ajax_modal_temp_2' => [
                    'temp' => 'order.view.employee.ftt',
                    'params' => ['employee_code']
                ]],
            'class' => ''
        ],
    ];
}

function getListTableSummaryTNCN()
{
    return [
        'month_year' => [
            'name' => 'Tháng',
            'type' => 'string',
            'class' => ''
        ],
        'ma_so_thue' => [
            'name' => 'MST',
            'type' => 'string',
            'class' => '',
            'display' => false
        ],
        'phap_nhan' => [
            'name' => 'Pháp nhân',
            'type' => 'string',
            'class' => '',
            'display' => true
        ],
        'tong_thu_nhap_truoc_thue' => [
            'name' => 'Tổng thu nhập trước thuế',
            'type' => 'number_format',
            'class' => ''
        ],
        'tong_non_tax' => [
            'name' => 'Tổng TN không chịu thuế',
            'type' => 'number_format',
            'class' => ''
        ],
        'tong_tnct' => [
            'name' => 'Tổng TNCT',
            'type' => 'number_format',
            'class' => ''
        ],
        'bhxh' => [
            'name' => 'BHXH',
            'type' => 'number_format',
            'class' => ''
        ],
        'thue_tam_trich' => [
            'name' => 'Thuế',
            'type' => 'number_format',
            'class' => ''
        ],
        'thuc_nhan' => [
            'name' => 'Thực nhận',
            'type' => 'number_format',
            'class' => ''
        ],
        'giam_tru_ban_than' => [
            'name' => 'Giảm trừ bản thân',
            'type' => 'number_format',
            'class' => '',
            'display' => false
        ],
        'giam_tru_gia_canh' => [
            'name' => 'Giảm trừ gia cảnh',
            'type' => 'number_format',
            'class' => '',
            'display' => false
        ],
//        'type' => [
//            'name' => 'Loại CT',
//            'type' => 'string',
//            'class' => '',
//            'option' => [
//                'value' => [
//                    [
//                        'table' => 'typeName',
//                        'value' => 'name'
//                    ]
//                ]
//            ]
//        ],
//        'noi_dung' => [
//            'name' => 'Diễn giải',
//            'type' => 'string',
//            'class' => 'w-100px'
//        ],

        'flag' => [
            'name' => '',
            'class' => '',
            'type' => "flag"
        ],
        'ajax_for_summary' => [
            'name' => '',
            'type' => 'ajax',
            'action' => [
                'view_ajax_modal_temp_1' => [
                    'temp' => 'order.view.employee',
                    'params' => ['employee_code']
                ],
                'view_ajax_modal_temp_2' => [
                    'temp' => 'order.view.employee.ftt',
                    'params' => ['employee_code']
                ]],
            'class' => ''
        ],
    ];
}

function getListTableTopican()
{
    return [
        'name' => [
            'name' => 'Tên tài khoản',
            'type' => 'string',
            'class' => ''
        ],
        'email' => [
            'name' => 'Email',
            'type' => 'string',
            'class' => ''
        ],
        'employee_code' => [
            'name' => 'Mã nhân viên',
            'type' => 'string',
            'class' => ''
        ],
        'avatar' => [
            'name' => 'Avatar',
            'type' => 'image',
            'class' => ''
        ],
        'role_id' => [
            'name' => 'Quyền',
            'type' => 'relationship',
            'class' => '',
            'relationship_type' => 'one_to_many_inverse',
            'relationship_name' => 'role',
            'desire_value' => 'name',
        ],
        'action' => [
            'name' => '',
            'type' => 'action',
            'action' => ['edit' => 'topican.edit', 'delete' => 'topican.delete', 'active' => 'topican.active'],
            'class' => '',
            'checkRole' => [
                'edit' => 'edit.user',
                'delete' => 'delete.user',
                'active' => 'delete.user',
            ]
        ],
    ];
}

function renderTableColTabulator()
{
    $mappingData = config('global.mappingDataSalary');

    $col = [];

    foreach ($mappingData as $k => $v) {
        $v['field'] = $k;
        if (!isset($v['title'])) {
            $v['title'] = $k;
        }
        $col[] = $v;
    }
    return $col;
}


function validateEmailTopica($email)
{
    $validatedEx = ['topica.edu.vn', 'topica.asia'];
    $EmailSplited = explode('@', $email);

    if (in_array($EmailSplited[1], $validatedEx)) {
        return true;
    }

    return false;
}

function renderTableColTabulatorFTT()
{
    $mappingData = config('global.mappingDataFTT');

    $col = [];

    foreach ($mappingData as $k => $v) {
        $v['field'] = $k;
        if (!isset($v['title'])) {
            $v['title'] = $k;
        }
        $col[] = $v;
    }
    return $col;

}

function renderTableEmpRent()
{
    $listTable = [
        'emp_name' => [
            'title' => 'Tên',
            'type' => 'string',
            'class' => ''
        ],
        'identity_type' => [
            'title' => 'Loại thẻ',
            'type' => 'option',
            'option' => [
                'cmt' => 'CMT',
                'hc' => 'Hộ chiếu'
            ],
            'class' => ''
        ],
        'identity_code' => [
            'title' => 'Số thẻ',
            'type' => 'string',
            'class' => ''
        ],
        'emp_code_date' => [
            'title' => 'Ngày đăng ký',
            'type' => 'datetime',
            'class' => 'text-center'
        ],
        'emp_code_place' => [
            'title' => 'Nơi đăng ký',
            'type' => 'string',
            'class' => ''
        ],
        'emp_tax_code' => [
            'title' => 'Mã số thuế',
            'type' => 'string',
            'class' => ''
        ],
        'emp_country' => [
            'title' => 'Quốc gia',
            'type' => 'string',
            'class' => ''
        ],
        'emp_live_status' => [
            'title' => 'Tình trạng cư trú',
            'type' => 'option',
            'option' => [
                '0' => 'Không cư trú',
                '1' => 'Cư trú'
            ],
            'class' => ''
        ],
        'emp_account_number' => [
            'title' => 'Số tài khoản',
            'type' => "string",
            'class' => ''
        ],
        'emp_account_bank' => [
            'title' => 'Ngân hàng',
            'type' => "string",
            'class' => ''
        ],
        'action' => [
            'title' => '',
            'type' => 'action',
            'action' => ['edit' => 'emp_rent.edit', 'delete' => 'emp_rent.destroy'],
            'class' => '',
            'formatter' => "html"
        ]
    ];


    $col = [];

    foreach ($listTable as $k => $v) {
        $v['field'] = $k;
        if (!isset($v['title'])) {
            $v['title'] = $k;
        }
        $col[] = $v;
    }
    return $col;

}


function checkIsNumber($array, &$data, &$dataError, $key, $mappingData)
{
    foreach ($array as $arr) {
        if ($data[$arr] && !is_numeric($data[$arr])) {
            $data['cssClass'] = 'error';
            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData[$arr]['title']) . '</b> phải là số';
        }
    }
}

function checkIsNotZero($array, &$data, &$dataError, $key, $mappingData)
{
    foreach ($array as $arr) {
        if ($data[$arr] == 0) {
            $data['cssClass'] = 'error';
            $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData[$arr]['title']) . '</b> phải khác 0';
        }
    }
}

function checkIsNotNull($array, &$data, &$dataError, $key, $mappingData)
{
    foreach ($array as $arr) {
        if ($data[$arr] === null) {
            $data['cssClass'] = 'error';
            $dataError[] = 'File exel dòng ' . ($key) . ' : <b>' . ($mappingData[$arr]) . '</b> không được để trống';
        }
    }
}

function checkIsNotNumber($array, &$data, &$dataError, $key, $mappingData)
{
    foreach ($array as $arr) {
        if (!is_numeric($data[$arr]) && $data[$arr] !== null) {
            $data['cssClass'] = 'error';
            $dataError[] = 'File exel dòng ' . ($key) . ' : <b>' . ($mappingData[$arr]) . '</b> phải là số';
        }
    }
}

function validateMoneyOfSalaryExcel($array, &$data, &$dataError, $key, $mappingData, $ftt)
{
    foreach ($array as $arr) {

        $sum = $ftt->where('employee_code', $data['employee_code'])->sum($arr);
        if ($sum) {
            if ($sum > $data[$arr]) {
                $data['cssClass'] = 'error';
                $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW_FTT) . ' : <b>' . ($mappingData[$arr]['title']) . '</b> đang nhỏ hơn so với các chứng từ khác thuộc nhân viên này';
            }
        }
    }
}

function validateMoneyOfSalaryExcelVer2($array, &$data, &$dataError, $key, $mappingData, $ftt)
{
    foreach ($array as $arr) {

        $sum = $ftt->where('employee_code', $data['ma_nv'])->sum($arr);

        if ($sum) {
            if ($sum > $data[$arr]) {
                $data['cssClass'] = 'error';
                $dataError[] = 'File exel dòng ' . ($key + IMPORT_START_ROW) . ' : <b>' . ($mappingData[$arr]) . '</b> đang nhỏ hơn so với các chứng từ khác thuộc nhân viên này';
            }
        }
    }
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);

    return $length === 0 ||
        (substr($haystack, -$length) === $needle);
}

function search($array, $key, $value, $first = false)
{
    $results = array();

    if (is_array($array)) {
        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }

        foreach ($array as $subarray) {
            $results = array_merge($results, search($subarray, $key, $value));
        }
    }

    if ($first && !empty($results)) {
        return $results[0];
    }

    return $results;
}

function slipName($name)
{
    $returnName = [];
    $name = explode(' ', $name);
    $first_name = $name[count($name) - 1];
    if ($first_name) $returnName['first_name'] = $first_name;
    unset($name[count($name) - 1]);
    $last_name = implode(' ', $name);
    if ($last_name) $returnName['last_name'] = $last_name;
    return $returnName;
}

function d($data)
{
    print_r($data);
    exit;
}

function getPN($fields = [])
{
    $data = [];
    $data['fields'] = implode(",", $fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://listmaster.topica.asia/pt_list" . "?" . http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = [
        'Authorization: Bearer anIUuelJI-Ex0nnXy4RaRTnEr_IJI2Q3',
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $server_output = curl_exec($ch);

    curl_close($ch);
//    var_dump(json_decode($server_output));exit();
    return json_decode($server_output);
}

function findByConditions($array, $conditions, $getKey = false, $multiple = false)
{
    if (!is_array($conditions) || !is_array($array)) {
        return false;
    }

    $results = [];

    foreach ($array as $key => $value) {
        $containsSearch = empty(array_diff_assoc($conditions, $value));
        if ($containsSearch) {
            if ($multiple) {
                $results[] =  $getKey ? $key : $value;
            } else {
                return $getKey ? $key : $value;
            }
        }
    }

    if ($multiple) return empty($results) ? false : $results;

    return false;
}

function string_compare($str_a, $str_b)
{
    $length = strlen($str_a);
    $length_b = strlen($str_b);

    $i = 0;
    $segmentcount = 0;
    $segmentsinfo = array();
    $segment = '';
    while ($i < $length) {
        $char = substr($str_a, $i, 1);
        if (strpos($str_b, $char) !== FALSE) {
            $segment = $segment . $char;
            if (strpos($str_b, $segment) !== FALSE) {
                $segmentpos_a = $i - strlen($segment) + 1;
                $segmentpos_b = strpos($str_b, $segment);
                $positiondiff = abs($segmentpos_a - $segmentpos_b);
                $posfactor = ($length - $positiondiff) / $length_b; // <-- ?
                $lengthfactor = strlen($segment) / $length;
                $segmentsinfo[$segmentcount] = array('segment' => $segment, 'score' => ($posfactor * $lengthfactor));
            } else {
                $segment = '';
                $i--;
                $segmentcount++;
            }
        } else {
            $segment = '';
            $segmentcount++;
        }
        $i++;
    }

    // PHP 5.3 lambda in array_map
    $totalscore = array_sum(array_map(function ($v) {
        return $v['score'];
    }, $segmentsinfo));
    return $totalscore;
}

function checkIsset ($array, $keys) {
    foreach ($keys as $key => $value) {
        if (!isset($array[$value])) {
            return $value;
        }
    }

    return true;
}

function findExcelCellByValue($objPHPExcel, $searchValue)
{
    $foundInCells = array();
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
        $ws = $worksheet->getTitle();
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            foreach ($cellIterator as $cell) {
                if ($cell->getValue() == $searchValue) {
                    $foundInCells[] = $cell->getCoordinate();
                }
            }
        }
    }

    return $foundInCells;
}

function ddie($data)
{
    var_dump($data);
    exit();
}

// Get fixed type ids
function getFixedTypeId()
{
    $fixedTypes = config('type.first_types');
    $ids = array_column($fixedTypes, 'id');

    $ids1 = $ids;
    array_map(function($value) use (&$ids1) {
       $ids1[] = "" . $value . "";
    }, $ids);

    return $ids1;
}

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}