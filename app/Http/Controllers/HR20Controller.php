<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Contracts\EmployeeRepositoryInterface;
use Illuminate\Http\Request;

class HR20Controller extends Controller
{
    protected $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function authenticateHr20()
    {
        $config = config("global.hr20");
        $url = $config['url']['getToken'];
        $params = $config['params']['getToken'];
        $mail = $params['Email'];
        $password = $params['Password'];
        $postField = '{"Email": "'.$mail.'", '.'"Password": "'.$password.'"}';
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_USERAGENT => 'TN18',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $postField,
          CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          return json_decode($response, true)['data'];
        }
    }

    public function getFullData() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $em = $this->employeeRepository->getMaxUpdateDateEmployee();
        if(is_null($em)) {
          echo 'Không có dữ liệu employee hoặc cột api_updated_time không tồn tại';
          return;
        }
        // Lấy thời gian cập nhật api gần nhất
        $api_updated_time = $em->api_updated_time;
        if($api_updated_time == '0000-00-00 00:00:00') {
          $api_updated_time = '2000-12-30 00:00:00';
        }
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $api_updated_time);
        $currentDate = $date->format('m/d/Y');

        $token = $this->authenticateHr20();
        // Lay ngay cap nhat lon nhat nhan vien
        $index = 0;
        $end = 1;
        $config = config("global.hr20");
        $skip = 0;
        do {
          $url = $config['url']['getFullData'];
          $skip = 500 * $index;
          $take = 500;
          $params = '?type=employee&skip='.$skip.'&take='.$take.'&is_include_statement=true&is_get_latest_statement=true'.'&cutoff_date='.$currentDate;
          $url = $url . $params;       
          $data = $this->curlDataHR($url, $token);
          if(count($data)) {
            $this->handleData( $data );
          } else {
            $end = 0;
            echo 'Đã cập nhật hết';
          }
          $index = $index + 1;

        } while ($end > 0);
        exit();
    }

    /*
    * Query API tu HR
    */
    public function curlDataHR($url, $token) {
      $curl = curl_init();
      curl_setopt_array($curl, array( 
        CURLOPT_URL => $url,
        CURLOPT_USERAGENT => 'TN18',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
          "Authorization: Bearer ".$token,
        ),
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        echo "cURL Error #:" . $err;
        return array();
      } else {
        $data = json_decode($response, true)['data'];
        return $data;
      }
    }

    /*
    * Update hoac tao moi nhan vien khi nhan duoc api tu hr20
    */
    public function handleData($data) {
        foreach($data as $em) {
          // Xu li status data va vi tri
          $status = 0;
          $vi_tri = '';
          if(isset($em['statements']) && $em['statements'] && count($em['statements']) ) {
              if(isset($em['statements'][0]['hrb']) && $em['statements'][0]['hrb']) {
                $status = $this->statusHR20( $em['statements'][0]['hrb'] );
              }
              if(isset($em['statements'][0]['ng']) && $em['statements'][0]['ng']) {
                $vi_tri = $em['statements'][0]['ng'];
                if(strrpos($vi_tri, '-') != false) {
                  $vi_tri = trim(explode('-', $vi_tri)[0]);
                }
              }
          }
          
          $updatedData = [];
          $updatedData['employee_code'] = (isset($em['employee_code']) && $em['employee_code']) ? $em['employee_code'] : '';
          $updatedData['first_name'] = (isset($em['employee_last_name']) && $em['employee_last_name']) ? $em['employee_last_name'] : '';
          $updatedData['last_name'] = (isset($em['employee_first_name']) && $em['employee_first_name'] ) ? $em['employee_first_name'] : '';
          $updatedData['email'] = (isset($em['employee_work_email']) && $em['employee_work_email'] ) ? $em['employee_work_email'] : '';
          $updatedData['cmt'] = (isset($em['employee_identification']) && $em['employee_identification'] ) ? $em['employee_identification'] : '';
          $updatedData['phap_nhan'] = (isset($em['employee_corporation']) && $em['employee_corporation'] ) ? $em['employee_corporation'] : '';
          $updatedData['birthday'] = (isset($em['employee_dob']) && $em['employee_dob'] ) ? $this->getBirthDay($em['employee_dob']) : '0000-00-00 00:00:00';
          $updatedData['bank'] = '';
          $updatedData['bank_account'] = '';
          $updatedData['status_hr20'] = $status;
          $updatedData['mst'] = (isset($em['employee_tax_number']) && $em['employee_tax_number'] ) ? $em['employee_tax_number'] : '';
          $updatedData['vi_tri'] = $vi_tri;
          $updatedData['api_updated_time'] = date('Y-m-d');
          $this->employeeRepository->createOrUpdateData($updatedData);
        }
    }

    public function getBirthDay($dob)
    {
        $d = new \DateTime($dob);
        return $d->format('Y-m-d');
    }
    
    public function statusHR20($state) 
    {
        $status = 0;
        if(strrpos($state, 'HRB9000') == false) {
          $status = 1;
        }
        return $status;
    }
}
