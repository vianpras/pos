<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Artisan;
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

Route::get('/ping', function (Request $request) {
    Helper::loggingApp($request->ip(), Auth::id(), '');
})->name('ping');

// Routing Public
Route::get('/', 'AuthControllers@login');
Route::get('login', 'AuthControllers@login')->name('login'); //index login
Route::post('authenticate', 'AuthControllers@authenticate')->name('authenticate'); //post data login
Route::get('auth/forgot', 'AuthControllers@forgot')->name('forgot'); //index forgot login
Route::post('auth/forgotRequest', 'AuthControllers@forgotRequest')->name('forgotRequest'); //post forgot login
Route::get('auth/recover/{hash}', 'AuthControllers@recover')->name('recover'); //index edit auth/password forgot login
Route::post('auth/recoverRequest', 'AuthControllers@recoverRequest')->name('recoverRequest'); //post edit auth/password login

// Firebase Notif
Route::post('auth/saveToken', 'AuthControllers@saveToken')->name('saveToken');
Route::post('auth/sendNotif', 'AuthControllers@sendNotif')->name('sendNotif');
Route::get('auth/sendNotif', 'AuthControllers@sendNotif')->name('sendNotif');

// Approve Persetujuan
Route::get('membership_approve/{member}', 'persetujuanControllers@approve')->name('memberApprove');
Route::get('membership_reject/{member}', 'persetujuanControllers@reject')->name('memberReject');

Route::get('/view_mail', function () {
    return view('membership.mail');
});

// Routing middleware auth
Route::middleware(['auth'])->group(function () {

    // Global Route Dashboard
    Route::get('dashboard', 'HomeController@index')->name('dashboard');
    Route::get('dashboardAjax', 'HomeController@dashboardAjax')->name('dashboardAjax');
    Route::get('iframe', 'HomeController@iframe')->name('iframe');
    Route::get('gambar/{type}/{id}', 'HomeController@gambar')->name('gambar');
    Route::get('logout', 'AuthControllers@logout')->name('logout');
    // .GLobal Route Dashboard

    // Image view
    Route::get('img/{type?}/{id?}', function ($type = null, $id = null) {

        $img = Storage::path('' . $type . '/' . $id);
        if (!(file_exists($img)) || $type == null || $id == null) {
            $img = Storage::path('noImage.png');
        }
        $mime = mime_content_type($img);
        if($mime =="image/png" || $mime =="image/jpg" || $mime =="image/jpeg"){
            $headers = array(
                'Content-Type:' . $mime,
            );
            $response = response()->file($img, $headers);
            return $response;
        }else{
            return abort(404);
        }

    });
    // .Image view

    // User Session
    Route::prefix('profile')->group(function () {
        Route::post('update', 'ProfileControllers@update')->name('updateProfile');
    });
    // .User Session

    // DATA INDUK 
    Route::prefix('dataInduk')->group(function () {
        // Hak Akses ACL Route
        Route::get('acl', 'UserACLControllers@index')->name('acl'); // index acl
        Route::post('acl/datatable', 'UserACLControllers@datatable')->name('acl.datatable'); // index acl
        Route::get('acl/create', 'UserACLControllers@create')->name('acl.new'); //modal create acl
        Route::post('acl/store', 'UserACLControllers@store')->name('acl.store'); //post save acl
        Route::get('acl/edit/{id}', 'UserACLControllers@edit')->name('acl.edit'); //page edit
        Route::post('acl/update/{id}', 'UserACLControllers@update')->name('acl.update'); //post update acl
        // .Hak Akses ACL Route

        // Item 
        Route::get('barang', 'ItemControllers@index')->name('item'); // index item
        Route::post('barang/datatable', 'ItemControllers@datatable')->name('item.datatable'); // index item
        Route::get('barang/create', 'ItemControllers@create')->name('item.new'); //modal create item
        Route::post('barang/store', 'ItemControllers@store')->name('item.store'); //post save item
        Route::get('barang/edit/{id}', 'ItemControllers@edit')->name('item.edit'); //page edit
        Route::post('barang/update/{id}', 'ItemControllers@update')->name('item.update'); //post update item
        Route::post('barang/disable/', 'ItemControllers@disable')->name('disableItem'); //post disable item
        // .Item 

        // Master Items
        Route::get('item/select', 'MasterItemController@listItem')->name('master.item.list.selected');
        Route::post('item/pricing', 'MasterItemController@itemPriceByCode')->name('master.item.pricing');
        // .Master Items

        // Master Customer
        Route::post('customer/get_by_code', 'CustomerControllers@custByCode')->name('master.customer.bycode');
        // .Master Customer

        // Company 
        Route::get('perusahaan', 'CompanyControllers@index')->name('company'); // index company
        Route::post('perusahaan/store', 'CompanyControllers@store')->name('company.store'); //post save company
        Route::post('perusahaan/update/{id}', 'CompanyControllers@update')->name('company.update'); //post update company
        // .Company 
        
        // Kategory 
        Route::get('kategori', 'CategoryControllers@index')->name('category'); // index category
        Route::post('kategori/datatable', 'CategoryControllers@datatable')->name('category.datatable'); // index category
        Route::get('kategori/create', 'CategoryControllers@create')->name('category.new'); //modal create category
        Route::post('kategori/store', 'CategoryControllers@store')->name('category.store'); //post save category
        Route::get('kategori/edit/{id}', 'CategoryControllers@edit')->name('category.edit'); //page edit
        Route::post('kategori/update/{id}', 'CategoryControllers@update')->name('category.update'); //post update category
        Route::post('kategori/disable/', 'CategoryControllers@disable')->name('disableCategory'); //post disable category
        // .Kategory 

        // User Route
        Route::get('pengguna', 'UserControllers@index')->name('pengguna'); // index user
        Route::post('pengguna/datatable', 'UserControllers@datatable')->name('pengguna.datatable'); // index user
        Route::get('pengguna/create', 'UserControllers@create')->name('pengguna.new'); //modal create user
        Route::post('pengguna/store', 'UserControllers@store')->name('pengguna.store'); //post save user
        Route::get('pengguna/edit/{id}', 'UserControllers@edit')->name('pengguna.edit'); //page edit
        Route::post('pengguna/update/{id}', 'UserControllers@update')->name('pengguna.update'); //post update user
        Route::post('pengguna/disable/', 'UserControllers@disable')->name('disableUser'); //post disable user
        // .User Route

        // Satuan 
        Route::get('satuan', 'UnitControllers@index')->name('satuan'); // index satuan
        Route::post('satuan/datatable', 'UnitControllers@datatable')->name('satuan.datatable'); // index satuan
        Route::get('satuan/create', 'UnitControllers@create')->name('satuan.new'); //modal create satuan
        Route::post('satuan/store', 'UnitControllers@store')->name('satuan.store'); //post save satuan
        Route::get('satuan/edit/{id}', 'UnitControllers@edit')->name('satuan.edit'); //page edit
        Route::post('satuan/update/{id}', 'UnitControllers@update')->name('satuan.update'); //post update satuan
        Route::post('satuan/disable/', 'UnitControllers@disable')->name('disableSatuan'); //post disable satuan
        // .Satuan

        // kategoriPenjualan 
        Route::get('kategoriPenjualan', 'SalesCategoryControllers@index')->name('kategoriPenjualan'); // index kategoriPenjualan
        Route::post('kategoriPenjualan/datatable', 'SalesCategoryControllers@datatable')->name('kategoriPenjualan.datatable'); // index kategoriPenjualan
        Route::get('kategoriPenjualan/create', 'SalesCategoryControllers@create')->name('kategoriPenjualan.new'); //modal create kategoriPenjualan
        Route::post('kategoriPenjualan/store', 'SalesCategoryControllers@store')->name('kategoriPenjualan.store'); //post save kategoriPenjualan
        Route::get('kategoriPenjualan/edit/{id}', 'SalesCategoryControllers@edit')->name('kategoriPenjualan.edit'); //page edit
        Route::post('kategoriPenjualan/update/{id}', 'SalesCategoryControllers@update')->name('kategoriPenjualan.update'); //post update kategoriPenjualan
        Route::post('kategoriPenjualan/disable/', 'SalesCategoryControllers@disable')->name('disablekategoriPenjualan'); //post disable satuan
        // .kategoriPenjualan 

        // docPrefix 
        Route::get('docPrefix', 'docPrefixControllers@index')->name('docPrefix'); // index docPrefix
        Route::post('docPrefix/datatable', 'docPrefixControllers@datatable')->name('docPrefix.datatable'); // index docPrefix
        Route::get('docPrefix/create', 'docPrefixControllers@create')->name('docPrefix.new'); //modal create docPrefix
        Route::post('docPrefix/store', 'docPrefixControllers@store')->name('docPrefix.store'); //post save docPrefix
        Route::get('docPrefix/edit/{prefix}', 'docPrefixControllers@edit')->name('docPrefix.edit'); //page edit
        Route::post('docPrefix/update/{prefix}', 'docPrefixControllers@update')->name('docPrefix.update'); //post update docPrefix
        Route::post('docPrefix/disable/', 'docPrefixControllers@disable')->name('disabledocPrefix'); //post disable docPrefix
        // .docPrefix 

        // ChartOfAccount
        Route::get('coa', 'COAControllers@index')->name('coa'); // index coa
        Route::post('coa/datatable', 'COAControllers@datatable')->name('coa.datatable'); // datatable coa
        Route::get('coa/create', 'COAControllers@create')->name('coa.new'); //modal create coa
        Route::post('coa/store', 'COAControllers@store')->name('coa.store'); //post save coa
        // .ChartOfAccount

        // ProfitSetting
        Route::get('profitsetting', 'ProfitSettingControllers@index')->name('profitsetting'); // index profitsetting
        Route::get('profitsetting/create', 'ProfitSettingControllers@create')->name('profitsetting.new'); //view create profitsetting
        Route::post('profitsetting/store', 'ProfitSettingControllers@store')->name('profitsetting.store'); //view create profitsetting
        Route::get('profitsetting/edit/{id}', 'ProfitSettingControllers@edit')->name('profitsetting.edit'); //view create profitsetting
        Route::post('profitsetting/update', 'ProfitSettingControllers@update')->name('profitsetting.update'); //view create profitsetting
        Route::post('profitsetting/delete', 'ProfitSettingControllers@delete')->name('profitsetting.delete'); //view create profitsetting
    });
    // .DATA INDUK

    // keanggotaan 
    Route::get('keanggotaan', 'MembershipControllers@index')->name('keanggotaan'); // index keanggotaan
    Route::post('keanggotaan/datatable', 'MembershipControllers@datatable')->name('keanggotaan.datatable'); // index keanggotaan
    Route::get('keanggotaan/create', 'MembershipControllers@create')->name('keanggotaan.new'); //modal create keanggotaan
    Route::get('keanggotaan/persetujuan', 'MembershipControllers@persetujuan')->name('keanggotaan.persetujuan'); //modal persetujuan keanggotaan
    Route::post('keanggotaan/store', 'MembershipControllers@store')->name('keanggotaan.store'); //post save keanggotaan
    Route::post('keanggotaan/email_member', 'MembershipControllers@emailMember')->name('keanggotaan.email'); //post email persetujuan keanggotaan
    Route::get('keanggotaan/edit/{code}', 'MembershipControllers@edit')->name('keanggotaan.edit'); //page edit
    Route::post('keanggotaan/update/{code}', 'MembershipControllers@update')->name('keanggotaan.update'); //post update keanggotaan
    Route::post('keanggotaan/disable/', 'MembershipControllers@disable')->name('disableKeanggotaan'); //post disable 
    // .keanggotaan 

    // booking 
    Route::get('booking', 'BookingControllers@index')->name('booking'); // index booking
    Route::post('booking/datatable', 'BookingControllers@datatable')->name('booking.datatable'); // index booking
    Route::get('booking/create', 'BookingControllers@create')->name('booking.new'); //modal create booking
    Route::post('booking/store', 'BookingControllers@store')->name('booking.store'); //post save booking
    Route::get('booking/edit/{code}', 'BookingControllers@edit')->name('booking.edit'); //page edit
    Route::post('booking/update/{code}', 'BookingControllers@update')->name('booking.update'); //post update booking
    Route::post('booking/disable/', 'BookingControllers@disable')->name('disableKeanggotaan'); //post disable 
    Route::post('booking/getItem/', 'BookingControllers@getItem')->name('getItem'); //
    Route::post('booking/getMember/', 'BookingControllers@getMember')->name('getMember'); //
    Route::post('booking/getDataMember/', 'BookingControllers@getDataMember')->name('getDataMember'); //
    // .booking 

    // pos 
    Route::get('sales', 'POSControllers@index')->name('sales'); // index pos
    Route::post('sales/datatable', 'POSControllers@datatable')->name('sales.datatable'); // index pos
    Route::get('sales/create', 'POSControllers@create')->name('sales.new'); //modal create pos
    Route::post('sales/store/{action}', 'POSControllers@store')->name('sales.store'); //post save pos
    Route::get('sales/edit/{code}', 'POSControllers@edit')->name('sales.edit'); //page edit
    Route::post('sales/update/{code}/{action}', 'POSControllers@update')->name('sales.update'); //post update pos
    Route::post('sales/disable/', 'POSControllers@disable')->name('disableKeanggotaan'); //post disable 
    Route::post('sales/getItem/', 'POSControllers@getItem')->name('getItemSales'); //
    Route::post('sales/getDataMember/', 'POSControllers@getDataMember')->name('getDataMemberSales'); //
    Route::post('sales/removeCart/', 'POSControllers@removeCart')->name('removeCartSales'); //
    Route::post('sales/storeCart/', 'POSControllers@storeChart')->name('storeCart'); //
    Route::post('sales/getCart/', 'POSControllers@getCart')->name('removeGetCart'); //
    Route::post('sales/chart/tax_discount/', 'POSControllers@storeChartDiscount')->name('storeCartDiscount'); //
    Route::post('sales/editSales/{code}/{confirm}', 'POSControllers@editSales')->name('editSales'); //
    Route::get('sales/print/{code}/', 'POSControllers@printSales')->name('printSales'); //
    Route::get('sales/print/sementara/{table}/', 'POSControllers@printSementara')->name('printSalesSementara'); //
    Route::get('sales/order/{code}/', 'POSControllers@printOrder')->name('printOrder'); //
    Route::get('sales/payment_method/', 'POSControllers@paymentMethod')->name('sales.paymentMethod'); //
    Route::post('sales/payment_method/details', 'POSControllers@paymentMethodDetails')->name('sales.paymentMethod.details'); //
    // .pos 

    // purchase 
    // Route::get('purchase', 'PurchasesControllers@index')->name('purchase'); // index purchase
    // Route::post('purchase/datatable', 'PurchasesControllers@datatable')->name('purchase.datatable'); // index purchase
    // Route::get('purchase/create', 'PurchasesControllers@create')->name('purchase.new'); //modal create purchase
    // Route::post('purchase/store/{action}', 'PurchasesControllers@store')->name('purchase.store'); //post save purchase
    // Route::get('purchase/edit/{code}', 'PurchasesControllers@edit')->name('purchase.edit'); //page edit
    // Route::post('purchase/update/{code}/{action}', 'PurchasesControllers@update')->name('purchase.update'); //post update pos
    // Route::post('purchase/disable/', 'PurchasesControllers@disable')->name('disableKeanggotaan'); //post disable 
    // Route::post('purchase/getItem/', 'PurchasesControllers@getItem')->name('getItemPurchase'); //
    // Route::post('purchase/removeCart/', 'PurchasesControllers@removeCart')->name('removeCartPurchase'); //
    // Route::post('purchase/getDataSupplier/', 'PurchasesControllers@getDataSupplier')->name('getDataSupplier'); //
    // .purchase 

    // report sales
    Route::get('salesReport', 'ReportSalesControllers@index')->name('salesReport.index');
    Route::get('kasirReport', 'ReportSalesControllers@index_kasir')->name('salesReport.index');
    Route::post('salesReport', 'ReportSalesControllers@report')->name('salesReport.report');
    Route::get('salesReport/pendapatan', 'ReportSalesControllers@salesPendapatan')->name('salesReport.pendapatan.index');
    Route::post('salesReport/pendapatan', 'ReportSalesControllers@salesPendapatanFilter')->name('salesReport.pendapatan.filter');
    // ./report sales

    // report purchase
    Route::get('purchaseReport', 'ReportPurchasesControllers@index')->name('purchaseReport.index');
    Route::post('purchaseReport', 'ReportPurchasesControllers@report')->name('purchaseReport.report');
    // .report purchase

    // Cash Bank
    Route::get('cash/cash_bank', 'CashInController@indexCashBank')->name('cashBank.index');
    Route::get('cash/cash_bank/datatable', 'CashInController@datatableCashBank')->name('cashBank.datatable'); // index pos

    // CashIn
    Route::get('cash/in', 'CashInController@index')->name('cashIn.index');
    Route::get('cash/in/datatable', 'CashInController@datatable')->name('cashIn.datatable'); // index pos
    Route::get('cash/in/datatable_details', 'CashInController@datatableDetails'); // index detail pos
    Route::get('cash/in/create', 'CashInController@formCreate')->name('cashIn.create');
    Route::post('cash/in/store', 'CashInController@store')->name('cashIn.store');

    // CashOut
    Route::get('cash/out', 'CashOutController@index')->name('cashOut.index');
    Route::get('cash/out/datatable', 'CashOutController@datatable')->name('cashOut.datatable'); // index pos
    Route::get('cash/out/datatable_details', 'CashOutController@datatableDetails'); // index detail pos
    Route::get('cash/out/create', 'CashOutController@formCreate')->name('cashOut.create');
    Route::post('cash/out/store', 'CashOutController@store')->name('cashOut.store');
    Route::post('cash/out/remove', 'CashOutController@remove')->name('cashOutRemove');

    // Jurnal Umum
    Route::get('jurnal', 'JurnalUmumControllers@index');
    Route::get('jurnal/create', 'JurnalUmumControllers@formCreate');
    Route::get('jurnal/get_account', 'JurnalUmumControllers@getAccount');
    Route::post('jurnal/store', 'JurnalUmumControllers@store');
    Route::get('jurnal/detail/{code}', 'JurnalUmumControllers@detail');
    Route::get('jurnal/form_posting', 'JurnalUmumControllers@formPosting')->name('jurnal.form_posting');
    Route::post('jurnal/posting', 'JurnalUmumControllers@posting');
    Route::get('jurnal/buku_besar', 'JurnalUmumControllers@bukuBesar');

    // Pembelian
    Route::get('purchase', 'PembelianControllers@index');
    Route::post('purchase/datatable', 'PembelianControllers@datatable')->name('purchase.datatable');
    Route::get('purchase/create', 'PembelianControllers@create')->name('purchase.create');
    Route::post('purchase/store', 'PembelianControllers@store');
    Route::get('purchase/update/{id}', 'PembelianControllers@edit')->name('purchase.edit');
    Route::post('purchase/store/update', 'PembelianControllers@storeUpdate');
    Route::get('purchase/detail/{id}', 'PembelianControllers@showDetail')->name('purchase.detail');

});
// .Routing mcodedleware auth
