<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use Session;

class POSControllers extends Controller
{
    public function __construct()
    {
        // set middleware
        $this->middleware(['auth']);
    }
    /**
     * Display a listing of the resource.
     *sales
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Helper::checkACL('sales', 'r')) {
            $var = ['nav' => 'sales', 'subNav' => 'sales', 'title' => 'Transaksi Penjualan'];

            return view('sales.index', $var);
        } else {
            // tidak memiliki otorisasi
            session()->flash('notifikasi', [
                "icon"      => config('global.errors.E002.status'),
                "title"     => config('global.errors.E002.code'),
                "message"   => config('global.errors.E002.message'),
            ]);

            return redirect('dashboard');
        }
    }
    public function datatable(Request $request)
    {
        if (Helper::checkACL('sales', 'r')) {
            if ($request->ajax()) {
                // query data
                $sales = DB::table('sales')
                    ->leftJoin('SAPOCRD', 'sales.customer', '=', 'SAPOCRD.cardcode')
                    ->leftJoin('users', 'sales.user_created', '=', 'users.id')
                    ->when(auth()->user()->site, function($query){
                        $query->where('users.site', auth()->user()->site);
                    })
                    ->select([
                        'sales.code as code',
                        'SAPOCRD.cardname as customer',
                        'sales.date_order as date_order',
                        'sales.total as date_total',
                        'sales.status as status',
                        'sales.cashBack as cashBack'
                    ]);

                return Datatables::of($sales)
                    ->addColumn('action', function ($sales) {
                        // render column action
                        return view('sales.action', [
                            'edit_url' => '/sales/edit/' . $sales->code,
                            'show_url' => '/',
                            'id' => $sales->code,
                            'status' => $sales->status,
                            'print_url' => '/sales/print/' . $sales->code,
                        ]);
                    })
                    ->editColumn('status', function ($sales) {
                        // render column status
                        $_status = Helper::statusBadge($sales->status);
                        return $_status;
                    })
                    ->filter(function ($query) use ($request) {
                        if ($request->has('sales_code_filter')) {
                            // default column filter
                            $query->where('sales.code', 'like', "%{$request->sales_code_filter}%");
                        }
                        if ($request->has('sales_status_filter')) {
                            if (($request->sales_status_filter) == '-1') {
                                // default column filter
                                $query->where('sales.status', "like", "%");
                            } else {
                                // filtered column
                                $query->where('sales.status', 'like', "%" . $request->sales_status_filter . "%");
                            }
                        }
                        if ($request->has('sales_date_filter')) {
                            if (($request->sales_date_filter) == null) {
                                // default column filter 1 bulan
                                $query->where([
                                    ['sales.date_order', '>=', Date('Y-m-d', strtotime("-6 months")) . ' 00:00:00'],
                                    ['sales.date_order', '<=', Date('Y-m-d') . ' 59:59:59'],
                                ]);
                            } else {
                                // filtered column
                                $dateSeparator = explode(" - ", $request->sales_date_filter);
                                $query->where([
                                    ['sales.date_order', '>=', $dateSeparator[0] . ' 00:00:00'],
                                    ['sales.date_order', '<=', $dateSeparator[1] . ' 59:59:59'],
                                ]);
                            }
                        }
                    })
                    ->rawColumns(['action', 'status']) //render raw custom column
                    ->make(true);
            } else {
                // tidak memiliki otorisasi
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E002.status'),
                    "title" => config('global.errors.E002.code'),
                    "message" => config('global.errors.E002.message'),
                ]);
                return redirect('dashboard');
            }
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (Helper::checkACL('sales', 'c')) {
            $items      = DB::table('SAPOITM')->get();

            $pricelist  = DB::table('SAPOPLN')->get();
            $customer   = DB::table('SAPOCRD')->select('SAPOCRD.*', DB::raw('RIGHT(SAPOCRD.phone, 4) AS phoneCode'))->get();

            // Session::forget('details_order');
            $session = Session::get('details_order');

            $cartCode = 0;
            $cart_details = [];
            $sales = [];
            if($request){
                $cartCode = $request->cartCode;
                $cart = DB::table('sales_cart')
                        ->leftJoin('users', 'sales_cart.salesid','=','users.id')
                        ->leftJoin('SAPOCRD', 'sales_cart.bussiness_partner','=','SAPOCRD.cardcode')
                        ->where('docnum', $cartCode)
                        ->where('stage', '1')
                        ->select('sales_cart.*', 'SAPOCRD.cardname', 'SAPOCRD.phone', 'users.full_name')
                        ->first();
                        
                if($cart){
                    $cart_details = DB::table('sales_cart_details')->where('cart_id', $cart->docnum)->get();
                    $sales = DB::table('users')->where('id', $cart->salesid)->first();
                }

            } 

            $var = [
                'nav'       => 'salesCreate',
                'subNav'    => 'sales',
                'title'     => 'Tambah Order Penjualan',
                'items'     => $items,
                'pricelist' => $pricelist,
                'customer'  => $customer,
                'docnum'    => $cartCode,
                'sales'     => ($request) ? $sales : false,
                'cart'      => ($request) ? $cart : false,
                'cart_details' => ($request) ? $cart_details : false
            ];

            return view('sales.create', $var);
        } else {
            $result = config('global.errors.E002');
            
            session()->flash('notifikasi', [
                "icon"      => config('global.errors.E002.status'),
                "title"     => config('global.errors.E002.code'),
                "message"   => config('global.errors.E002.message'),
            ]);
    
            return redirect('dashboard');
        }

    }

    public function paymentMethod()
    {
        $paymentMethod = DB::table('pas_master_payment')->groupBy('type_payment')->get();

        $var = [
            'paymentMethod' => $paymentMethod
        ];
        return response()->json($var);
    }

    public function paymentMethodDetails(Request $request)
    {
        $paymentMethodDetails = DB::table('pas_master_payment')->where('type_payment', $request->payCode)->get();

        $var = [
            'paymentMethodDetails' => $paymentMethodDetails
        ];
        return response()->json($var);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request, $action)
    {
        if(Helper::checkACL('sales', 'c')) {
            // Query creator
            DB::beginTransaction();
            try {
                $code = Helper::docPrefix('sales');
                $cart = DB::table('sales_cart')->where('docnum', $request->docnum)->first();
                $store = ((Auth::user()->site) ? Auth::user()->site : $request->store);
                // jika bayar
                
                DB::table('sales')->insert([
                    'docnum'        => $request->docnum,
                    'code'          => $code,
                    'customer'      => $request->custcode,
                    'customer_detail' => $request->custname,
                    'customer_number' => $request->custphone,
                    'date_order'    => Carbon::now(),
                    'discount'      => $request->discount,
                    'sub_total'     => $request->total,
                    'tax'           => $request->tax,
                    'total'         => $request->grandtotal,
                    'cashBack'      => $request->cashBack,
                    'status'        => $action == "simpan" ? "pending" : "close",
                    'salesid'       => $request->sales,
                    'user_created'  => Auth::id(),
                    'checkerid'     => $request->checker,
                    'created_at'    => Carbon::now(),
                    'site'          => $store,
                    'approved_by'   => ($cart) ? $cart->approved_by : 0
                ]);

                $payments = $request->payment_method_details;
                foreach($payments AS $key => $val){
                    DB::table('sales_payment_details')->insert([
                        'sales_code'        => $code,
                        'payment_method'    => $request->payment_method[$key],
                        'payment_method_detail' => $val,
                        'payment_charge'    => $request->payment_charge[$key],
                        'nominal'           => $request->detail_nominal[$key],
                        'created_at'        => Carbon::now()
                    ]);
                }

                // simpan sales details
                $itemcodes = $request->itemcode;
                foreach ($itemcodes as $key => $value) {
                    DB::table('sales_details')->insert([
                        'sales_id'      => $code,
                        'itemcode'      => $value,
                        'quantity'      => abs($request->qty[$key]),
                        'pricelist'     => $request->price_list[$key],
                        'sell_price'    => $request->price[$key],
                        'disc1'         => $request->disc1[$key],
                        'disc2'         => $request->disc2[$key],
                        'disc3'         => $request->disc3[$key],
                        'sub_total'     => $request->subtotal[$key],
                        'created_at'    => Carbon::now(),
                    ]);
                    
                    $lastStok = DB::table('pas_kartustok')->where('itemcode', $value)->where('whcode', auth()->user()->site)->orderBy('id', 'DESC')->first();
                    if($action != "simpan"){
                        // Mengurangi stok
                        $newStock = (($lastStok->saldo_akhir) ? $lastStok->saldo_akhir : 0) - $request->qty[$key];
                        DB::table('pas_kartustok')->insert([
                            'itemcode'  => $value,
                            'whcode'    => auth()->user()->site,
                            'tanggal'   => date('Y-m-d'),
                            'ref_code'  => $code,
                            'saldo_awal'=> $lastStok->saldo_akhir,
                            'type_mutasi'=> 'keluar',
                            'mutasi'    => $request->qty[$key],
                            'saldo_akhir'=> $newStock,
                            'type_doc'  => 'Kirim Item',
                            'keterangan'=> 'Pengurangan item penjualan POS',
                            'created_at'=> Carbon::now()
                        ]);
                    }
                }

                if($action != "simpan"){
                    // Membership
                    $member = DB::table('sales_membership')->where('nomor_handphone', $request->custphone)->where('member_category', 'retail')->where('status', 'active')->first();
                    if($member){
                        $settingMember = DB::table('sales_membership_setting')->where('member_category', 'retail')->first();

                        if($request->grandtotal >= $settingMember->min_transaksi){
                            $pembagian = $request->grandtotal / $settingMember->min_transaksi;
                            $point = floor($pembagian);

                            DB::table('sales_membership_transaction_points')->insert([
                                'member_id'         => $member->member_code,
                                'transaction_code'  => $code,
                                'transaction_amount'=> $request->grandtotal,
                                'type'              => 'masuk',
                                'point'             => $point,
                                'site'              => auth()->user()->site,
                                'created_at'        => Carbon::now()
                            ]);

                            $total_point = $member->total_point + $point;
                            DB::table('sales_membership')->where('member_code', $member->member_code)->update([
                                'total_point' => $total_point
                            ]);
                        }
                    }
                }

                DB::table('sales_cart')->where('docnum', $request->docnum)->update(['stage' => '2']);

                DB::commit();
                $code = ['code_sales' => $code];

                $result = array_merge($code, config('global.success.S002'));
                
            } catch (\Throwable $e) {
                DB::rollback();

                $result = $e->getMessage();
            }

            return response()->json($result);
        } else {
            //Tidak memiliki otorisasi
            session()->flash('notifikasi', [
                "icon" => config('global.errors.E002.status'),
                "title" => config('global.errors.E002.code'),
                "message" => config('global.errors.E002.message'),
            ]);
            return redirect('dashboard');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($code)
    {
        if (Helper::checkACL('sales', 'r')) {
            try {
                $items      = DB::table('SAPOITM')->get();

                $pricelist  = DB::table('SAPOPLN')->get();
                $customer   = DB::table('SAPOCRD')->select('SAPOCRD.*', DB::raw('RIGHT(SAPOCRD.phone, 4) AS phoneCode'))->get();

                $data = DB::table('sales')
                            ->leftJoin('users', 'sales.user_created','=','users.id')
                            ->leftJoin('SAPOCRD', 'sales.customer','=','SAPOCRD.cardcode')
                            ->where('sales.code', $code)
                            ->select('sales.*', 'SAPOCRD.cardname', 'SAPOCRD.phone')
                            ->first();

                DB::table('sales_cart')->where('docnum', $data->docnum)->update(['stage' => '1']);

                $sales = DB::table('sales_cart')
                        ->leftJoin('users', 'sales_cart.salesid','=','users.id')
                        ->leftJoin('SAPOCRD', 'sales_cart.bussiness_partner','=','SAPOCRD.cardcode')
                        ->where('docnum', $data->docnum)
                        ->where('stage', '1')
                        ->select('sales_cart.*', 'SAPOCRD.cardname', 'SAPOCRD.phone', 'users.full_name')
                        ->first();
                        
                $sales_details = DB::table('sales_cart_details')->where('cart_id', $data->docnum)->get();

                $var = [
                    'nav'       => 'sales',
                    'subNav'    => 'sales',
                    'title'     => 'Ubah Order Penjualan',
                    'items'     => $items,
                    'pricelist' => $pricelist,
                    'customer'  => $customer,
                    'data'      => $data,
                    'sales'     => $sales,
                    'sales_details' => $sales_details
                ];
                
            } catch (\Throwable $th) {
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E011.status'),
                    "title" => config('global.errors.E011.code'),
                    "message" => config('global.errors.E011.message'),
                ]);
                return redirect('sales');
            }

            if (is_null($sales)) {
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E011.status'),
                    "title" => config('global.errors.E011.code'),
                    "message" => config('global.errors.E011.message'),
                ]);

                return redirect('sales');
            }

            if ($data->status == "close") {
                session()->flash('notifikasi', [
                    "icon" => 'warning',
                    "title" => config('global.errors.E014.code'),
                    "message" => config('global.errors.E014.message'),
                ]);
                return redirect('sales');

            } else {
                return view('sales.edit', $var);
            }
        } else {
            session()->flash('notifikasi', [
                "icon" => config('global.errors.E002.status'),
                "title" => config('global.errors.E002.code'),
                "message" => config('global.errors.E002.message'),
            ]);
            return redirect('dashboard');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $code, $action)
    {
        if (Helper::checkACL('sales', 'u')) {
            $sales = DB::table('sales')->where('code', $code)->first();
            
            DB::beginTransaction();
            try {
                // simpan header
                DB::table('sales')->where('code', $code)->update([
                    'customer'      => $request->custcode,
                    'customer_detail' => $request->custname,
                    'customer_number' => $request->custphone,
                    'date_order'    => Carbon::now(),
                    'discount'      => $request->discount,
                    'sub_total'     => $request->total,
                    'tax'           => $request->tax,
                    'total'         => $request->grandtotal,
                    'cashBack'      => $request->cashBack,
                    'status'        => $action == "simpan" ? "pending" : "close",
                    'updated_at'    => Carbon::now(),
                    'user_updated'  => Auth::id(),
                ]);
                $subGrandTotal = 0;
                $GrandTotal = 0;

                // simpan sales details
                if ($request->itemcode > 0) {
                    DB::table('sales_details')->where('sales_id', $code)->delete();
                    $itemcodes = $request->itemcode;
                    foreach ($itemcodes as $key => $value) {
                        DB::table('sales_details')->insert([
                            'sales_id'      => $code,
                            'itemcode'      => $value,
                            'quantity'      => abs($request->qty[$key]),
                            'pricelist'     => $request->price_list[$key],
                            'sell_price'    => $request->price[$key],
                            'disc1'         => $request->disc1[$key],
                            'disc2'         => $request->disc2[$key],
                            'disc3'         => $request->disc3[$key],
                            'sub_total'     => $request->subtotal[$key],
                            'created_at'    => Carbon::now(),
                        ]);

                        $lastStok = DB::table('pas_kartustok')->where('itemcode', $value)->where('whcode', auth()->user()->site)->orderBy('id', 'DESC')->first();
                        if($action != "simpan"){
                            // Mengurangi stok
                            $newStock = (($lastStok->saldo_akhir) ? $lastStok->saldo_akhir : 0) - $request->qty[$key];
                            DB::table('pas_kartustok')->insert([
                                'itemcode'  => $value,
                                'whcode'    => auth()->user()->site,
                                'tanggal'   => date('Y-m-d'),
                                'ref_code'  => $code,
                                'saldo_awal'=> $lastStok->saldo_akhir,
                                'type_mutasi'=> 'keluar',
                                'mutasi'    => $request->qty[$key],
                                'saldo_akhir'=> $newStock,
                                'type_doc'  => 'Kirim Item',
                                'keterangan'=> 'Pengurangan item penjualan POS',
                                'created_at'=> Carbon::now()
                            ]);
                        }
                    }
                }

                $payments = $request->payment_method_details;
                DB::table('sales_payment_details')->where('sales_code', $code)->delete();
                foreach($payments AS $key => $val){
                    DB::table('sales_payment_details')->insert([
                        'sales_code'        => $code,
                        'payment_method'    => $request->payment_method[$key],
                        'payment_method_detail' => $val,
                        'payment_charge'    => $request->payment_charge[$key],
                        'nominal'           => $request->detail_nominal[$key],
                        'created_at'        => Carbon::now()
                    ]);
                }

                if($action != "simpan"){
                    // Membership
                    $member = DB::table('sales_membership')->where('nomor_handphone', $request->custphone)->where('member_category', 'retail')->where('status', 'active')->first();
                    if($member){
                        $settingMember = DB::table('sales_membership_setting')->where('member_category', 'retail')->first();

                        if($request->grandtotal >= $settingMember->min_transaksi){
                            $pembagian = $request->grandtotal / $settingMember->min_transaksi;
                            $point = floor($pembagian);

                            DB::table('sales_membership_transaction_points')->insert([
                                'member_id'         => $member->member_code,
                                'transaction_code'  => $code,
                                'transaction_amount'=> $request->grandtotal,
                                'type'              => 'masuk',
                                'point'             => $point,
                                'site'              => auth()->user()->site,
                                'created_at'        => Carbon::now()
                            ]);

                            $total_point = $member->total_point + $point;
                            DB::table('sales_membership')->where('member_code', $member->member_code)->update([
                                'total_point' => $total_point
                            ]);
                        }
                    }
                }

                DB::commit();

                $code = ['code_sales' => $code];

                $result = array_merge($code, config('global.success.S002'));

            } catch (\Throwable $e) {
                // $result = $e->getMessage();
                DB::rollback();

                $result = $e->getMessage();
            }

            return response()->json($result);
        } else {
            // tidak memiliki otorisasi
            session()->flash('notifikasi', [
                "icon" => config('global.errors.E002.status'),
                "title" => config('global.errors.E002.code'),
                "message" => config('global.errors.E002.message'),
            ]);

            return redirect('dashboard');
        }
    }

    // -- Cart atau Keranjang
    public function storeCart(Request $request){   
        if (Helper::checkACL('sales', 'c')) {
            $docnum = $request->docnum;
            $custcode = $request->custcode;
            $sales = $request->sales;
            $store = ((Auth::user()->site) ? Auth::user()->site : $request->store);

            DB::beginTransaction();
            try {
                if($docnum){
                    DB::table('sales_cart')->updateOrInsert(['docnum' => $docnum],[
                        'salesid'       => $sales,
                        'bussiness_partner'         => $custcode,
                        'bussiness_partner_detail'  => $request->custname,
                        'bussiness_partner_phone'   => $request->custphone,
                        'grandtotal'    => 0,
                        'cart_date'     => date('Y-m-d'),
                        'created_by'    => Auth::user()->id,
                        'stage'         => '1',
                        'created_at'    => date('Y-m-d H:i:s')
                    ]);

                    DB::table('sales_cart_details')->updateOrInsert(['cart_id' => $docnum, 'itemcode' => $request->itemcode],[
                        'cart_id'   => $docnum,
                        'salesid'   => $sales,
                        'bussiness_partner' => $custcode,
                        'itemcode'  => $request->itemcode,
                        'itemname'  => $request->itemname,
                        'qty'       => $request->qty,
                        'pricelist_id'  => $request->pricelist,
                        'price'     => $request->price,
                        'disc1'     => $request->disc1,
                        'disc2'     => $request->disc2,
                        'disc3'     => $request->disc3,
                        'subtotal'  => $request->subtotal
                    ]);
    
                    $grandtotal = DB::table('sales_cart_details')->where('cart_id', $docnum)->sum('subtotal');
                    DB::table('sales_cart')->where('docnum', $docnum)->update([
                        'grandtotal'  => $grandtotal
                    ]);
                } else {
                    $get_last_docnum = DB::table('sales_cart')->whereYear('cart_date', date('Y'))->whereMonth('cart_date', date('m'))->where('store_code', $store)->orderBy('created_at', 'DESC')->first();
        
                    if($get_last_docnum != ""){
                        $get_nomor    = substr($get_last_docnum->docnum, -4);
                        $count_number = $get_nomor + 1;
        
                        $new_number = sprintf("%04d", $count_number);
                    } else {
                        $new_number = '0001';
                    }
        
                    $docnum = $store.'-'.date('my').''.$new_number;
        
                    DB::table('sales_cart')->insert([
                        'docnum'        => $docnum,
                        'store_code'    => $store,
                        'salesid'       => $sales,
                        'bussiness_partner' => $custcode,
                        'bussiness_partner_detail' => $request->custname,
                        'bussiness_partner_phone' => $request->custphone,
                        'grandtotal'    => 0,
                        'cart_date'     => date('Y-m-d'),
                        'created_by'    => Auth::user()->id,
                        'stage'         => '1',
                        'created_at'    => date('Y-m-d H:i:s')
                    ]);
        
                    DB::table('sales_cart_details')->insert([
                        'cart_id'   => $docnum,
                        'salesid'   => $sales,
                        'bussiness_partner' => $custcode,
                        'itemcode'  => $request->itemcode,
                        'itemname'  => $request->itemname,
                        'qty'       => $request->qty,
                        'pricelist_id'  => $request->pricelist,
                        'price'     => $request->price,
                        'subtotal'  => $request->subtotal
                    ]);
        
                    $grandtotal = DB::table('sales_cart_details')->where('cart_id', $docnum)->sum('subtotal');
                    DB::table('sales_cart')->where('docnum', $docnum)->update([
                        'grandtotal'  => $grandtotal
                    ]);
                }
                DB::commit();
        
                $result = [
                    "status"    => "success",
                    "message"   => "Data berhasil disimpan",
                    "docnum"    => $docnum
                ];
            } catch (\Throwable $th) {
                DB::rollBack();
                $result = $th->getMessage();
            }
            return response()->json($result);
        } else {
            // tidak memiliki otorisasi
            $result = config('global.errors.E002');
        }
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeCart(Request $request)
    {
        if (Helper::checkACL('sales', 'u')) {
            try {
                DB::beginTransaction();
                
                DB::table('sales_cart_details')->where('cart_id', $request->docnum)->where('itemcode', $request->itemcode)->delete();

                $details = DB::table('sales_cart_details')->where('cart_id', $request->docnum);
                
                $grandTotal = $details->sum('subtotal');
                DB::table('sales_cart')->where('docnum', $request->docnum)->update([
                    'grandtotal' => $grandTotal
                ]);
                
                $detailCount = $details->count();
                if($detailCount == 0){
                    DB::table('sales_cart')->where('docnum', $request->docnum)->delete();
                }

                $result = config('global.success.S003');
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();

                $result = config('global.errors.E009');
                // $result = $th->getMessage();
            }
            return response()->json($result); //return json ke request ajax
        } else {
            // tidak memiliki otorisasi
            $result = config('global.errors.E002');
        }

        return response()->json($result); //return json ke request ajax
    }

    public function approveCart(Request $request)
    {
        $user = DB::table('users')->where('email', $request->email_approval)->where('status', '1')->first();

        $validCredentials = Hash::check($request->password_approval, $user->password);

        $status = 'false';
        $approved = 0;
        if($validCredentials == true){
            $checkAccess = DB::table('users')->leftJoin('role_permissions', 'users.role', '=', 'role_permissions.id')->where('users.id', $user->id)->where('role_permissions.approval_pricelist', 'c')->first();
            if($checkAccess){
                DB::table('sales_cart')->where('docnum', $request->docnum)->update([
                    'approved_by' => $user->id,
                    'approved_at' => Carbon::now()
                ]);
                $approved = $user->id;
                $status = 'true';
            } else {
                $approved = 0;
                $status = 'false';
            }
        }

        $return = [
            'status' => $status,
            'approved' => $approved
        ];

        return response()->json($return);
    }
    // Cart atau Keranjang --

    public function disable(Request $request)
    {
        // disable data
        if (Helper::checkACL('sales', 'd')) {
            $id = $request->id;
            DB::beginTransaction();

            try {
                $sales = DB::table('sales')->where('code', $id);
                $member_login = DB::table('memberships')->where('username', $id);
                $status = $sales->first()->status;
                $statusM = $member_login->first()->status;
                $sales->update(['status' => $status == 'active' ? 'suspend' : 'active']);
                $member_login->update(['status' => $statusM ? false : true]);
                $result = config('global.success.S003');
                DB::commit();
            } catch (QueryException $e) {
                DB::rollBack();
                $result = config('global.errors.E009');
                $result = $e->getMessage();
            } catch (\Throwable $e) {
                DB::rollBack();
                $result = config('global.errors.E009');
                $result = $e->getMessage();
            }
        } else {
            // tidak memiliki otorisasi
            $result = config('global.errors.E002');
        }

        return response()->json($result); //return json ke request ajax
    }

    public function getDataMember(Request $request)
    {
        if (Helper::checkACL('sales', 'r')) {
            $items = DB::table('memberships')
                ->select('code', 'nik', 'nama', 'mobile', 'address')
                ->where('code', $request->code)
                ->where('status', 'active')
                ->limit(1)
                ->get();
            $list = array();
            if (count($items) > 0) {
                foreach ($items as $key => $item) {
                    $list['id'] = $item->code;
                    $list['name'] = $item->nama;
                }
                $result = response()->json($list);
            } else {
                return response()->json();
            }
            $result = response()->json($list);
        } else {
            // tidak memiliki otorisasi
            // $result = config('global.errors.E002',404);
            $result = response()->json(config('global.errors.E002'), 404);
        }
        return $result;
    }

    public function getItem(Request $request)
    {
        if (Helper::checkACL('sales', 'r')) {
            $parentCategory = DB::table('categories')->select('id')->where('code', 'SO')->first();
            $salesCategory = DB::table('sales_categories')->select('mark_up')->where('id',$request->sales_category)->first();
            is_null($salesCategory) ? $markUp = 0 : $markUp = $salesCategory->mark_up;
            
            if ($request->search == 'favorit') {
                $items = DB::table('sales_details')
                    ->join('master_items', 'master_items.id', '=', 'sales_details.item_id')
                    ->join('categories', 'categories.id', '=', 'master_items.category_id')
                    ->select([
                        'master_items.id as id',
                        'master_items.kode_item as code',
                        'master_items.nama_item as name',
                        // 'items.sell_price as sell_price',
                        'master_items.category_id as category_id',
                        'categories.name as category_name',
                    ])
                    ->selectRaw('TRIM(master_items.sell_price + (master_items.sell_price * ('.$markUp.'/100)))+0 as sell_price')
                    ->Where([
                        ['master_items.status', '1'],
                        ['master_items.tipe', 'Item Jadi'],
                        ['categories.parent', $parentCategory->id]
                    ])
                    ->groupBy('sales_details.item_id')
                    ->limit(10)
                    ->get();
            }elseif($request->search == 'semua_item'){
                $items = DB::table('master_items')
                    ->join('categories', 'categories.id', '=', 'master_items.category_id')
                    ->select([
                        'master_items.id as id',
                        'master_items.kode_item as code',
                        'master_items.nama_item as name',
                        // 'items.sell_price as sell_price',
                        'master_items.category_id as category_id',
                        'categories.name as category_name',
                    ])
                    ->selectRaw('TRIM(master_items.sell_price + (master_items.sell_price * ('.$markUp.'/100)))+0 as sell_price')
                    ->Where([
                        ['master_items.status', '1'],
                        ['master_items.tipe', 'Item Jadi'],
                        ['categories.parent', $parentCategory->id]
                    ])
                    ->get();                
            }else {
                $items = DB::table('master_items')
                    ->join('categories', 'categories.id', '=', 'master_items.category_id')
                    ->select([
                        'master_items.id as id',
                        'master_items.kode_item as code',
                        'master_items.nama_item as name',
                        // 'items.sell_price as sell_price',
                        'master_items.category_id as category_id',
                        'categories.name as category_name',
                    ])
                    ->selectRaw('TRIM(master_items.sell_price + (master_items.sell_price * ('.$markUp.'/100)))+0 as sell_price')
                    ->Where([
                        ['master_items.status', '1'],
                        ['master_items.tipe', 'Item Jadi'],
                        ['categories.parent', $parentCategory->id],
                        ['master_items.category_id', $request->search],
                    ])
                    ->orWhere([
                        ['master_items.status', '1'],
                        ['master_items.tipe', 'Item Jadi'],
                        ['categories.parent', $parentCategory->id],
                        ['master_items.category_id', $request->search],
                    ])
                    ->orWhere([
                        ['master_items.status', '1'],
                        ['master_items.tipe', 'Item Jadi'],
                        ['categories.parent', $parentCategory->id],
                        ['categories.id', $request->search],
                    ])
                    ->get();
            }
            $list = array();
            if (count($items) > 0) {
                foreach ($items as $key => $value) {
                    $profit_global = DB::table('profit_setting')->where('itemcode', 'Semua')->first();
                    if($profit_global){
                        if($profit_global->profit_type == 'persentase'){
                            $list[] = [
                                'id'        => $value->id,
                                'code'      => $value->code,
                                'item_name' => $value->name,
                                'sell_price'=> $value->sell_price + ($value->sell_price * $profit_global->jumlah/100),
                            ];
                        } else {
                            $list[] = [
                                'id'        => $value->id,
                                'code'      => $value->code,
                                'item_name' => $value->name,
                                'sell_price'=> $value->sell_price + $profit_global->jumlah,
                            ];
                        }
                    } else {
                        $profit = DB::table('profit_setting')->where('itemcode', $value->id)->first();
                        if($profit){
                            if($profit->profit_type == 'persentase'){
                                $list[] = [
                                    'id'        => $value->id,
                                    'code'      => $value->code,
                                    'item_name' => $value->name,
                                    'sell_price'=> $value->sell_price + ($value->sell_price * $profit->jumlah/100),
                                ];
                            } else {
                                $list[] = [
                                    'id'        => $value->id,
                                    'code'      => $value->code,
                                    'item_name' => $value->name,
                                    'sell_price'=> $value->sell_price + $profit->jumlah,
                                ];
                            }
                        } else {
                            $list[] = [
                                'id'        => $value->id,
                                'code'      => $value->code,
                                'item_name' => $value->name,
                                'sell_price'=> $value->sell_price,
                            ];
                        }
                    }
                }

                $result = response()->json($items);
            } else {
                return response()->json();
            }
            $result = response()->json($list);
        } else {
            // tidak memiliki otorisasi
            // $result = config('global.errors.E002',404);
            $result = response()->json(config('global.errors.E002'), 404);
        }

        return $result;
    }

    public function printSales($code)
    {
        try {
            $companies = DB::table('companies')->where('site_code', auth()->user()->site)->first();
            $configuration = DB::table('configurations')->where('site_code', auth()->user()->site)->first();
            $sales = DB::table('sales')
                    ->leftJoin('SAPOCRD', 'sales.customer','=','SAPOCRD.cardcode')
                    ->select('sales.*', 'SAPOCRD.cardname')
                    ->where('code', $code)->first();

            $totPayment = DB::table('sales_payment_details')->where('sales_code', $code)->sum('nominal');

            $sales->payment = $totPayment;
            

            // data sales tidak ada
            if (is_null($sales)) {
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E011.status'),
                    "title" => config('global.errors.E011.code'),
                    "message" => config('global.errors.E011.message'),
                ]);
                return redirect('/sales/create');
            }
            $sales_details = DB::table('sales_details')
                ->leftJoin('SAPOITM', 'sales_details.itemcode', '=', 'SAPOITM.itemcode')
                ->select([
                    'SAPOITM.UDF_ItemCode as itemcode_short',
                    'SAPOITM.itemname as item_name',
                    'sales_details.sell_price as sell_price',
                    'sales_details.quantity as quantity',
                    'sales_details.sub_total as sub_total'
                ])
                ->where('sales_details.sales_id', $code)->get();
            $discount = ($sales->sub_total * ($sales->discount / 100));
            $tax = (($sales->sub_total - $discount) * ($sales->tax / 100));

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage());
        }
        $var = [
            'sales' => $sales,
            'sales_details' => $sales_details,
            'discount' => $discount,
            'tax' => $tax,
            'count' => count($sales_details),
            'company' => $companies,
            'configuration' => $configuration,

        ];
        return view('sales.print', $var);
    }

    public function printSementara($table_id){
        try {
            $companies = DB::table('companies')->where('site_code', auth()->user()->site)->first();
            $configuration = DB::table('configurations')->where('site_code', auth()->user()->site)->first();
            $sales = DB::table('carts')->where('table', $table_id)->first();
            $sales_details = DB::table('cart_details')->leftJoin('master_items', 'cart_details.item_id','=','master_items.id')->where('cart_id', $sales->id)->get();

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage());
        }
        $var = [
            'sales' => $sales,
            'sales_details' => $sales_details,
            'discount' => $sales->disc_rp,
            'tax' => $sales->tax_rp,
            'subtotal' => $sales->tax_rp,
            'grandtotal' => $sales->tax_rp,
            'count' => count($sales_details),
            'company' => $companies,
            'configuration' => $configuration,
        ];

        return view('sales.print_sementara', $var);
    }   

    public function printOrder($code)
    {
        try {
            $companies = DB::table('companies')->where('site_code', auth()->user()->site)->first();
            $configuration = DB::table('configurations')->where('site_code', auth()->user()->site)->first();
            // ambil juga data yang ada di cart
            $sales = DB::table('sales')->where('code', $code)->first();
            if (is_null($sales)) {
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E011.status'),
                    "title" => config('global.errors.E011.code'),
                    "message" => config('global.errors.E011.message'),
                ]);
                return redirect('/sales/create');
            }
            $sales_details = DB::table('sales_details')
                ->leftJoin('master_items', 'sales_details.item_id', '=', 'master_items.id')
                ->select([
                    'master_items.nama_item as item_name',
                    'sales_details.sell_price as sell_price',
                    'sales_details.quantity as quantity',
                    'sales_details.sub_total as sub_total'
                ])
                ->where('sales_details.sales_id', $code)->get();
            $discount = ($sales->sub_total * ($sales->discount / 100));
            $tax = (($sales->sub_total - $discount) * ($sales->tax / 100));

            // data sales tidak ada
            
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage());
        }
        $var = [
            'sales' => $sales,
            'sales_details' => $sales_details,
            'discount' => $discount,
            'tax' => $tax,
            'count' => count($sales_details),
            'company' => $companies,
            'configuration' => $configuration,

        ];
        return view('sales.orderPrint', $var);
    }

    public function refund($code, $action)
    {
        $header = DB::table('sales')->where('code', $code)->first();
        $detail = DB::table('sales_details')->where('sales_id', $code)->get();

        DB::table('sales')->where('code', $code)->update(['status' => 'return']);

        foreach($detail AS $dt){
            $lastStok = DB::table('pas_kartustok')->where('itemcode', $dt->itemcode)->where('whcode', auth()->user()->site)->orderBy('id', 'DESC')->first();

            if($action != "simpan"){
                $newStock = $lastStok->saldo_akhir + $dt->quantity;
                DB::table('pas_kartustok')->insert([
                    'itemcode'      => $dt->itemcode,
                    'whcode'        => auth()->user()->site,
                    'tanggal'       => date('Y-m-d'),
                    'ref_code'      => $code,
                    'saldo_awal'    => $lastStok->saldo_akhir,
                    'type_mutasi'   => 'masuk',
                    'mutasi'        => $dt->quantity,
                    'saldo_akhir'   => $newStock,
                    'type_doc'      => 'Terima Item',
                    'keterangan'    => 'Penambahan item refund POS',
                    'created_at'    => Carbon::now()
                ]);
            }
        }

        if($action != "simpan"){
            // Membership
            $member = DB::table('sales_membership')->where('nomor_handphone', $header->customer_number)->where('member_category', 'retail')->where('status', 'active')->first();
            if($member){
                $settingMember = DB::table('sales_membership_setting')->where('member_category', 'retail')->first();

                if($header->total >= $settingMember->min_transaksi){
                    $getPoin = DB::table('sales_membership_transaction_points')->where('member_id', $member->member_code)->where('transaction_code', $header->code)->first();

                    DB::table('sales_membership_transaction_points')->insert([
                        'member_id'         => $member->member_code,
                        'transaction_code'  => $code,
                        'transaction_amount'=> $getPoin->transaction_amount,
                        'type'              => 'refund',
                        'point'             => $getPoin->point,
                        'site'              => auth()->user()->site,
                        'created_at'        => Carbon::now()
                    ]);

                    $total_point = $member->total_point - $getPoin->point;
                    DB::table('sales_membership')->where('member_code', $member->member_code)->update([
                        'total_point' => $total_point
                    ]);
                }
            }
        }

        $result = [
            'status'    => 'success',
            'code'      => $code
        ];

        return response()->json($result);

    }
    
    public function refundCreate($code)
    {
        $header = DB::table('sales')->leftJoin('SAPOCRD', 'sales.customer', '=', 'SAPOCRD.cardcode')->where('sales.code', $code)->first();
        $detail = DB::table('sales_details')
                    ->leftJoin('SAPOITM', 'sales_details.itemcode', '=', 'SAPOITM.itemcode')
                    ->where('sales_id', $code)->get();
        $items      = DB::table('SAPOITM')->get();

        $pricelist  = DB::table('SAPOPLN')->get();
        $customer   = DB::table('SAPOCRD')->select('SAPOCRD.*', DB::raw('RIGHT(SAPOCRD.phone, 4) AS phoneCode'))->get();

        $var = [
            'nav'       => 'refund',
            'subNav'    => 'refund',
            'title'     => 'Refund Sales',
            'items'     => $items,
            'pricelist' => $pricelist,
            'customer'  => $customer,
            'header'    => $header,
            'detail'    => $detail
        ];
        
        return view('sales.refund', $var);
    }
}
