<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrder extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ma_osscar' => '',
            'ma_du_toan' => 'required|string',
            'serial' => 'required|string',
            'phap_nhan' => 'required|string',
            'nguoi_de_xuat' => 'required|string',
            'phong_ban' => 'required|string',
            'ngay_de_xuat' => 'required|date_format:"d/m/Y"',
            'nguoi_huong' => '',
            'san_pham' => 'required|string',
            'noi_dung' => 'required|string',
            'so_tien' => 'required|numeric',
            'loai_tien' => 'required|string',
            'ty_gia' => 'required|numeric',
            'quy_doi' => 'required|numeric'
        ];
    }
}
