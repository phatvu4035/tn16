<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\UserController;

Auth::routes();


Route::get('users', 'UserController@index');
//Route::get('logout', function () {
//    auth()->logout();
//
//    session()->flash('message', 'Some goodbye message');
//
//    return redirect('/login');
//});


Route::group(['middleware' => ['web']], function () {


    Route::get('/auth/google', 'SocialAuthController@redirectToProvider');
    Route::get('/callback/google', 'SocialAuthController@handleProviderCallback');

    Route::group(['middleware' => ['auth', 'active']], function () {

        Route::get('/', 'HomeController@index');

        Route::get('them-chung-tu', 'VoucherController@insert')->name('order.insert');
        Route::get('sua-chung-tu/{order_id}', 'VoucherController@edit')->name('order.edit');

        Route::get('vouchers/by-order/{order_id}','VoucherController@getByOrderId')->name('order.getByOrderId');

        Route::resource('employees', 'EmployeeController');

        Route::get('orders/get-data','OrderController@getData')->name('order.getData');
        Route::get('bo-thanh-toan/{order_id}','OrderController@orderInfo')->name('order.orderInfo');
        Route::get('huy-bo-thanh-toan/{order_id}','OrderController@deleteOrder')->name('order.deleteOrder');
        Route::post('bo-thanh-toan/{order_id}/update','OrderController@updateOrderInfo')->name('order.updateOrderInfo');
        Route::get('danh-sach-bo-thanh-toan','OrderController@listOrders')->name('order.listOrders');
        Route::get('tao-bo-thanh-toan','OrderController@createUI')->name('order.create');
        Route::post('luu-tru-bo-thanh-toan','OrderController@saveOrder')->name('order.save');

        Route::post('import-bo-thanh-toan','OrderController@importFileFTT')->name('order.import.ftt');
        Route::get('import-bo-thanh-toan',function (){
            return redirect()->route('order.create.salary');
        });
        Route::post('import-bo-thanh-toan/save','OrderController@saveFileFTT')->name('order.import.ftt.save');

        Route::get('tao-bo-thanh-toan-luong','OrderController@createOrderInfoSalary')->name('order.create.salary');
        Route::post('tao-bo-thanh-toan-luong','OrderController@createOrderInfoSalary')->name('post.order.create.salary');

        Route::get('orders/import-salary', function () {
            return redirect()->route('order.create.salary');
        });
        Route::post('orders/validate-phap-nhan-month-year', 'OrderController@validatePhapNhanMonthYear')->name('order.validate.phap_nhan');

        Route::post('orders/import-salary', 'OrderController@importSalaryFile')->name('order.import.file');
        Route::post('orders/import-salary/save', 'OrderController@saveSalaryFile')->name('order.import.save');


        // nhân sự thuê khoán
        Route::get('nhan-su-thue-khoan', 'EmployeeRentController@index')->name('emp_rent.index');
        Route::get('tao-nhan-su-thue-khoan', 'EmployeeRentController@create')->name('emp_rent.create');
        Route::post('tao-nhan-su-thue-khoan', 'EmployeeRentController@store')->name('emp_rent.store');
        Route::get('nhan-su-thue-khoan/{emp_rent_id}/sua', 'EmployeeRentController@edit')->name('emp_rent.edit');
        Route::put('nhan-su-thue-khoan/{emp_rent_id}', 'EmployeeRentController@update')->name('emp_rent.update');
        Route::delete('nhan-su-thue-khoan/{emp_rent_id}/delete', 'EmployeeRentController@destroy')->name('emp_rent.destroy');
        Route::post('nhan-su-thue-khoan/{emp_rent_id}/restore', 'EmployeeRentController@restoreEmpRent')->name('emp_rent.restore');

        // api nhan su thuê khoán
        Route::get('emp-rent/list',"\App\Http\Controllers\Api\EmpRentController@getList")->name('emp_rent.api.list');

        // api chứng từ
        Route::get('orders/search-employee',"\App\Http\Controllers\Api\OrderController@searchByCode")->name('order.api.search_by_code');

        // api nhân sự
        Route::get('employee/get',"\App\Http\Controllers\Api\EmployeeController@getEmployee")->name('employee.api.getEmployee');


        Route::get('employee/viewEmployee',"\App\Http\Controllers\EmployeeController@viewEmployee")->name('employee.viewEmployee');

        /* Manage roles, permission */
        Route::get('roles/create', 'RoleController@create')->name('roles.create');

        Route::post('roles/create', 'RoleController@store')->name('roles.store');

        Route::get('roles', 'RoleController@index')->name('roles.index');

        Route::get('roles/edit/{id}', 'RoleController@edit')->name('roles.edit');

        Route::post('roles/edit/{id}', 'RoleController@update')->name('roles.update');


        Route::delete('roles/delete/{id}', 'RoleController@destroy')->name('roles.delete');

        /**
        * Manage topican account
        */
        Route::get('topican/create', 'TopicanController@create')->name('topican.create');

        Route::post('topican/create', 'TopicanController@store')->name('topican.store');

        Route::get('topican', 'TopicanController@index')->name('topican.index');

        Route::get('topican/edit/{id}', 'TopicanController@edit')->name('topican.edit');

        Route::post('topican/edit/{id}', 'TopicanController@update')->name('topican.update');

        Route::delete('topican/delete/{id}', 'TopicanController@destroy')->name('topican.delete');
        Route::post('topican/active/{id}', 'TopicanController@active')->name('topican.active');

        // api topican
        Route::get('topican/list',"\App\Http\Controllers\Api\TopicanApiController@getList")->name('topican.api.list');

        // Doi soat
        Route::get('doi-soat/import/{luong}/{phap_nhan}/{thang}/{nam}',"\App\Http\Controllers\CrossCheckController@importPanel")->name('cross_check.importPanel');
        Route::post('doi-soat/import/{luong}/{phap_nhan}/{thang}/{nam}',"\App\Http\Controllers\CrossCheckController@importHandle")->name('cross_check.importHandle');
        Route::get('doi-soat/bo-doi-soat/{luong}/{phap_nhan}/{thang}/{nam}',"\App\Http\Controllers\CrossCheckController@showByMonth")->name('cross_check.showByMonth');
        Route::get('doi-soat/export/ngoai-luong/{phap_nhan}/{thang}/{nam}',"\App\Http\Controllers\CrossCheckController@export")->name('cross_check.export');
        Route::get('doi-soat/export/luong/{phap_nhan}/{thang}/{nam}',"\App\Http\Controllers\CrossCheckController@exportSalary")->name('cross_check.exportSalary');
        Route::post('doi-soat/bo-doi-soat/{luong}/{phap_nhan}/{thang}/{nam}',"\App\Http\Controllers\CrossCheckController@getByMoth")->name('cross_check.getByMoth');
        Route::post('doi-soat/done-salary',"\App\Http\Controllers\CrossCheckController@doneSalary")->name('cross_check.doneSalary');
        Route::get('doi-soat/thu-cong',"\App\Http\Controllers\CrossCheckController@pickOrders")->name('cross_check.pickOrders');
        Route::post('doi-soat/thu-cong',"\App\Http\Controllers\CrossCheckController@mergeOrder")->name('cross_check.mergeOrder');
        Route::post('doi-soat/remove-orderId',"\App\Http\Controllers\CrossCheckController@removeOrderId")->name('cross_check.removeOrderId');
        Route::get('doi-soat-nam', "\App\Http\Controllers\CrossCheckController@listCrossCheckYear")->name('cross_check.listCrossCheckYear');
        Route::post('doi-soat-nam', "\App\Http\Controllers\CrossCheckController@getListCrossCheckYear")->name('cross_check.getListCrossCheckYear');
        Route::get('doi-soat-nam/{phap_nhan}/{nam}', "\App\Http\Controllers\CrossCheckController@crossCheckYear")->name('cross_check.crossCheckYear');
        Route::post('doi-soat-nam/{phap_nhan}/{nam}', "\App\Http\Controllers\CrossCheckController@importHandleYear")->name('cross_check.importHandleYear');
        Route::get('doi-soat/bo-doi-soat-nam/{phap_nhan}/{nam}',"\App\Http\Controllers\CrossCheckController@showByYear")->name('cross_check.showByYear');
        Route::post('doi-soat/bo-doi-soat-nam/{phap_nhan}/{nam}',"\App\Http\Controllers\CrossCheckController@getByYear")->name('cross_check.getByYear');
        
        // Export excel doi xoat nam
        Route::get('doi-soat/export/bo-doi-soat-nam/{phap_nhan}/{nam}',"\App\Http\Controllers\CrossCheckController@exportYear")->name('cross_check.exportYear');

        Route::get('type/list',"\App\Http\Controllers\Api\TypeController@getList")->name('type.api.list');
        Route::resource('type','TypeController');


        // export view
        Route::get('export/401','ExportController@export401')->name('export.401');
        Route::post('export/401','ExportController@getExport401')->name('export.401.post');
        Route::get('export/402','ExportController@export402')->name('export.402');
        Route::post('export/402','ExportController@getExport402')->name('export.402.post');
        Route::get('export/403','ExportController@export403')->name('export.403');
        Route::post('export/403','ExportController@getExport403')->name('export.403.post');
        Route::post('export/o1','ExportController@getExportO1')->name('export.o1.post');

        Route::get('danh-sach-thu-nhap','SummaryController@index')->name('summary.index');

        Route::get('export/danh-sach-thu-nhap','\App\Http\Controllers\Api\SummaryController@exportDSTN')->name('export.dstn');

        Route::get('summary/list','\App\Http\Controllers\Api\SummaryController@getList')->name('summary.api.list');
        Route::get('summary/tn18','\App\Http\Controllers\Api\SummaryController@getTNEmployee')->name('summary.api.tn18');
        Route::get('summary/employee-info','\App\Http\Controllers\Api\SummaryController@getEmployeeInfo')->name('order.view.employee');
        Route::get('summary/employee-info/ftt','\App\Http\Controllers\Api\SummaryController@getEmployeeInfoFtt')->name('order.view.employee.ftt');

		Route::get('doi-soat/danh-sach',"\App\Http\Controllers\CrossCheckController@listCrossCheck")->name('cross_check.listCrossCheck');
        Route::post('doi-soat/danh-sach',"\App\Http\Controllers\CrossCheckController@getListCrossCheck")->name('cross_check.getListCrossCheck');
        Route::get('doi-soat/hoan-thanh/{cross_id}',"\App\Http\Controllers\CrossCheckController@doneAccounter")->name('cross_check.doneAccounter');
        Route::get('hoan-thanh-doi-soat-nam/{cross_id}',"\App\Http\Controllers\CrossCheckController@doneAccounterYear")->name('cross_check.doneAccounterYear');
        Route::get('/doi-soat/cancel-cross-check/{id}',"\App\Http\Controllers\CrossCheckController@cancelCrossCheck")->name('cross_check.cancelCrossCheck');
        Route::get('/doi-soat/remove-cross-check/{id}',"\App\Http\Controllers\CrossCheckController@removeCrossCheck")->name('cross_check.removeCrossCheck');
        Route::post('doi-soat/set-active/{cross_id}',"\App\Http\Controllers\CrossCheckController@setActive")->name('cross_check.setActive');

        //api DM4C
        Route::get('dm4c/cdt','\App\Http\Controllers\Api\Dm4cController@getCDT')->name('dm4c.getCDT');
        Route::get('dm4c/sp','\App\Http\Controllers\Api\Dm4cController@getSP')->name('dm4c.getSP');
        Route::get('dm4c/pt','\App\Http\Controllers\Api\Dm4cController@getPT')->name('dm4c.getPT');
        Route::get('dm4c/pt/me','\App\Http\Controllers\Api\Dm4cController@getMyPT')->name('dm4c.getMyPT');
        Route::get('dm4c/pt/{short_code}','\App\Http\Controllers\Api\Dm4cController@getSinglePT')->name('dm4c.getSinglePT');
        Route::get('dm4c/sp/{shortened_code}','\App\Http\Controllers\Api\Dm4cController@getSingleSP')->name('dm4c.getSingleSP');
        Route::get('dm4c/cdt/{shortened_code}','\App\Http\Controllers\Api\Dm4cController@getSingleCDT')->name('dm4c.getSingleCDT');

        //setting sync api data
        Route::get('setting/sync-data',"\App\Http\Controllers\SyncController@index")->name('sync.index');
        Route::get('sync/execute',"\App\Http\Controllers\SyncController@execute")->name('sync.execute');
        Route::get('sync/create_permission','PermissionController@createPermission')->name('create_permission');


        Route::post('/check/serial','\App\Http\Controllers\Api\OrderController@checkSerial')->name('check.serial');

        Route::get('/import/old-value','ImportOldValueController@index')->name('import.old.value');
        Route::post('/import/old-value','ImportOldValueController@importOldSalary')->name('import.old.value.post');
        Route::post('/import/old-value/save','ImportOldValueController@saveImportOldSalary')->name('save.old.value.post');
        Route::get('/import/old-value/success',function (){
            return "import thành công";
        })->name('save.old.value.success');
    });


});