<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Contracts\TypeRepositoryInterface;
use Illuminate\Http\Request;
use App\Facades\Topica;

class TypeController extends Controller
{
    protected $typeRepository;

    public function __construct(TypeRepositoryInterface $typeRepository)
    {
        $this->typeRepository = $typeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Topica::canOrRedirect('index.type');
        // create when Null
        $this->firstTypes();

        return view('type.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Topica::canOrRedirect('add.type');
        //
        return view('type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Topica::canOrRedirect('add.type');
        // Validate 
        $this->typeValidate($request);

        $data = $request->all();
        // Bởi vì đặt id của các type cố định là từ 1 - 1000 => kiểm tra xem có id nào lớn hơn 1000 chưa nếu
        // chưa có thì sẽ bắt đầu từ 1000
        if( !$this->typeRepository->checkTypeFrom1000() ) {
            $data['id'] = 1001;
        }

        $arr = [$data];
        $this->typeRepository->saveData($arr);
        return redirect()->route('type.index')->with('message', [
            "content" =>  $request->name . ' đã được tạo !',
            'title' => "Thành công",
            'type' => 'primary'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Topica::canOrRedirect('edit.type');

        $fixedTypeIds = getFixedTypeId();
        // Kiểm tra xem nó có nằm trong chứng từ cố định không
        if( in_array($id, $fixedTypeIds) ) {
            return redirect()->route('type.index')->with('errors', 'Không thể sửa chứng từ cố định');
        }
        //
        $data = $this->typeRepository->getDataBy(['id' => $id])[0];
        return view('type.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Topica::canOrRedirect('edit.type');
        // Validate 
        $this->typeValidate($request);

        $data = $request->all();

        $arr = [$data];
        $this->typeRepository->saveData($arr);
        return redirect()->route('type.index')->with('message', [
            "content" =>  $request->name . ' đã được cập nhật !',
            'title' => "Thành công",
            'type' => 'primary'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Topica::canOrRedirect('delete.type');

        // get array of fixed type ids
        $fixedTypeIds = getFixedTypeId();
        // Kiểm tra xem nó có nằm trong chứng từ cố định không 
        if( in_array($id, $fixedTypeIds) ) {
            return redirect()->route('type.index')->with('errors', 'Không thể xóa chứng từ cố định');;
        }

        

        $data = $this->typeRepository->getDataBy(['id' => $id]);
        if( count($data) > 0) {
            $data = $data[0];
            // Kiểm tra nếu chứng từ đã được reference đến summary thì không được phép xóa
            if(count($data->summarys) > 0) {
                return redirect()->route('type.index')->with('errors', $data->name . ' không thể xóa !');
            }

            $data->delete();
            
        }

        return redirect()->route('type.index')->with('message', [
            "content" =>  $data->name . ' đã được xóa !',
            'title' => "Thành công",
            'type' => 'primary'
        ]);
        
    }

    /*
    * Khoi tao nhung chung tu ban dau
     */
    public function firstTypes()
    {
        $type = $this->typeRepository->getDataBy([], false);

        if(count($type) <= 0) {
            // Tao nhung type co dinh
            $arr = config('type.first_types');

            $this->typeRepository->saveData($arr);
        }
    }

    public function typeValidate($request)
    {
        $this->validate($request, array(
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
        ), array(
            'name.required' => "Vui lòng điền tên chứng từ",
            'title.required' => "Trường title là bắt buộc",
        ));
    }
}
