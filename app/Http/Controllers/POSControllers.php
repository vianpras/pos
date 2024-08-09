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
            // render index
            $membership = Helper::forSelect('memberships', 'code', 'nama', false, false);

            $var = ['nav' => 'sales', 'subNav' => 'sales', 'title' => 'Transaksi Penjualan', 'membership' => $membership];
            return view('sales.index', $var);
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
    public function datatable(Request $request)
    {
        if (Helper::checkACL('sales', 'r')) {
            if ($request->ajax()) {
                // query data
                $sales = DB::table('sales')
                    ->leftJoin('memberships', 'sales.membership_code', '=', 'memberships.code')
                    ->select([
                        'sales.code as code',
                        'sales.customer as customer',
                        'sales.date_order as date_order',
                        'sales.table as table',
                        'sales.total as date_total',
                        'memberships.nama as member',
                        'sales.status as status',
                        'sales.pay as pay',
                        'sales.cashBack as cashBack',
                        'sales.pMethod as pMethod',
                    ]);
                return Datatables::of($sales)
                    ->addColumn('action', function ($sales) {
                        // render column action
                        return view('sales.action', [
                            'edit_url' => '/sales/edit/' . $sales->code,
                            'show_url' => '/',
                            'id' => $sales->code,
                            'status' => $sales->status,
                            'print_url' => '/sales/print/' . $sales->code.'?pay='.$sales->pay.'&cashback='.$sales->cashBack.'&pMethod='.$sales->pMethod,
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
    public function create()
    {
        if (Helper::checkACL('sales', 'c')) {
            $category = DB::table('categories')->select('code', 'name', 'id')->where('parent', '2')->get();
            $salesCategory = DB::table('sales_categories')->select('id', 'name')->orderBy('id','asc')->where('status', '1')->get();
            $getTotalCart = DB::table('configurations')->select('total_cart')->first();
            if (is_null($getTotalCart)) {
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E018.status'),
                    "title" => config('global.errors.E018.code'),
                    "message" => config('global.errors.E018.message'),
                ]);
                return redirect('/dataInduk/perusahaan');
            }else{
                $getTotalCart = $getTotalCart->total_cart;
            }
            $bahan_baku = DB::table('master_items')->where('tipe', 'Bahan Baku')->get();
            $parentCategory = DB::table('categories')->select('id')->where('code', 'SO')->first();
            $getSalesCategory = DB::table('sales_categories')->select('mark_up')->where('name','Umum')->first();
            is_null($getSalesCategory) ? $markUp = 0 : $markUp = $getSalesCategory->mark_up;
            
            // ambil cart kosong
            $checkCart = Helper::checkCart();
            // $items = DB::table('items')
            //     ->join('categories', 'categories.id', '=', 'items.category_id')
            //     ->select([
            //         'items.id as id',
            //         'items.code as code',
            //         'items.name as name',
            //         // 'items.sell_price as sell_price',
            //         'items.category_id as category_id',
            //         'categories.name as category_name',
            //     ])
            //     ->selectRaw('TRIM(items.sell_price + (items.sell_price * ('.$markUp.'/100)))+0 as sell_price')
            //     ->Where([
            //         // ['items.status', '-1'], //di matikan karna ada tambahan biaya untuk aktegori penjualan, bisa di request di AJAX pakai fungsi getITEM di bawah
            //         ['items.status', '1'], //di matikan karna ada tambahan biaya untuk aktegori penjualan, bisa di request di AJAX pakai fungsi getITEM di bawah
            //         ['categories.parent', $parentCategory->id],
            //     ])
            //     ->get();

            $items = DB::table('master_items')
                    ->select([
                        'master_items.id as id',
                        'master_items.kode_item as code',
                        'master_items.nama_item as name',
                        'master_items.sell_price as sell_price'
                    ])
                    ->where('master_items.tipe', 'Item Jadi')
                    ->where('master_items.status', '1')
                    ->get();
                    
            $var = [
                'nav' => 'salesCreate',
                'subNav' => 'sales',
                'title' => 'Tambah Order Penjualan',
                'categories' => $category,
                'salesCategories' => $salesCategory,
                'items' => $items,
                'bahan_baku' => $bahan_baku,
                'cart' => $checkCart,
                'getTotalCart' => $getTotalCart
            ];
            return view('sales.create1', $var);
        } else {
            $result = config('global.errors.E002');
        }

        session()->flash('notifikasi', [
            "icon" => config('global.errors.E002.status'),
            "title" => config('global.errors.E002.code'),
            "message" => config('global.errors.E002.message'),
        ]);
        return redirect('dashboard');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request, $action)
    {
        if (Helper::checkACL('sales', 'c')) {
            // Validation
            $vMessage = config('global.vMessage'); //get global validation messages\
            $validator = Validator::make($request->all(), [
                'table' => 'required',
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }

            // jika meja blm di pilih
            // Query creator
            DB::beginTransaction();

            try {
                $code = Helper::docPrefix('sales');
                // $membership_code = DB::table('memberships')->where('code', $request->membership_code)->first();
                // jika bayar
                if ($action == "bayar") {
                    $sales = DB::table('sales')->insert([
                                'code'          => $code,
                                'table'         => $request->table,
                                'date_order'    => Carbon::now(),
                                'discount'      => $request->discount,
                                'tax'           => $request->tax,
                                'pay'           => $request->pay,
                                'cashBack'      => $request->cashBack,
                                'pMethod'       => $request->pMethod,
                                'status'        => $action == "simpan" ? "pending" : "close",
                                'created_at'    => Carbon::now(),
                                'user_created'  => Auth::id(),
                            ]);

                    $subGrandTotal = 0;
                    $GrandTotal = 0;

                    // simpan sales details
                    if ($request->item_id > 0) {
                        foreach ($request->item_id as $key => $value) {
                            if (!is_null($request->item_id[$key]) || ($request->quantity[$key] > 0)) {
                                $item = DB::table('master_items')->select('id','buy_price', 'sell_price')->where('id', $request->item_id[$key])->first();
                                $profit_global = DB::table('profit_setting')->where('itemcode', 'Semua')->first();
                                $sellPrice = 0;
                                if($profit_global){
                                    if($profit_global->profit_type == 'persentase'){
                                        $sellPrice = $item->sell_price + ($item->sell_price * $profit_global->jumlah/100);
                                    } else {
                                        $sellPrice = $item->sell_price + $profit_global->jumlah;
                                    }
                                } else {
                                    $profit = DB::table('profit_setting')->where('itemcode', $item->id)->first();
                                    if($profit){
                                        if($profit->profit_type == 'persentase'){
                                            $sellPrice = $item->sell_price + ($item->sell_price * $profit->jumlah/100);
                                        } else {
                                            $sellPrice = $item->sell_price + $profit->jumlah;
                                        }
                                    } else {
                                        $sellPrice = $item->sell_price;
                                    }
                                }
                                $subTotal = abs($request->quantity[$key]) * $sellPrice;
                                $salesDetails = DB::table('sales_details')->insert([
                                                    'sales_id'      => $code,
                                                    'item_id'       => $request->item_id[$key],
                                                    'quantity'      => abs($request->quantity[$key]),
                                                    'buy_price'     => $item->buy_price,
                                                    'sell_price'    => $sellPrice,
                                                    'sub_total'     => $subTotal,
                                                    'description'   => $request->catatan[$key],
                                                    'created_at'    => Carbon::now(),
                                                ]);
                                $subGrandTotal = $subGrandTotal + $subTotal;
                            }
                        }
                    }

                    $sumDisc    = $subGrandTotal - ($subGrandTotal * ($request->discount / 100));
                    $GrandTotal = $sumDisc + ($sumDisc * ($request->tax / 100));

                    //update subGrandTotal & Grand Total
                    $salesSum = DB::table('sales')
                                ->where('code', $code)
                                ->update([
                                    'sub_total' => $subGrandTotal,
                                    'total' => $GrandTotal,
                                    'created_at' => Carbon::now(),
                                ]);
                    $cart = DB::table('carts')->where([
                                ['table', $request->table]
                            ]);
                    if ($cart->count() > 0) {
                        $deleteCartDetails = DB::table('cart_details')->where([
                            ['cart_id', $cart->first()->id],
                        ])->delete();
                        $deleteCart = $cart->delete();
                    }

                    $get_last_nomor_jurnal = DB::table('pos_jurnal_umum')->orderBy('id', 'desc')->first();

                    $new_number = '';
                    if($get_last_nomor_jurnal){
                        if($get_last_nomor_jurnal->no_jurnal_umum != ""){
                            $explode_nomor = explode("-", $get_last_nomor_jurnal->no_jurnal_umum);
                            $get_nomor = $explode_nomor[1];
                            $count_number = (int)$get_nomor + 1;
                            
                            $new_number = sprintf("%05d", $count_number);
                        } else {
                            $new_number = '00001';
                        }
                    } else {
                        $new_number = '00001';
                    }
                    $generate_nomor_jurnal = "NJU-".$new_number;
                    
                    $disc = $subGrandTotal * ($request->discount / 100);
                    $tax = $sumDisc * ($request->tax / 100);
                    $pendapatan = $subGrandTotal - $disc;
                    $kas = $pendapatan + $tax - $disc;

                    $akunKas = DB::table('chart_of_accounts')->where('code_account_default', '1.01.01.000.00')->first()->id;
                    $akunDiskon = DB::table('chart_of_accounts')->where('code_account_default', '4.05.00.000.00')->first()->id;
                    $akunPendapatan = DB::table('chart_of_accounts')->where('code_account_default', '4.00.00.000.00')->first()->id;
                    $akunPajak = DB::table('chart_of_accounts')->where('code_account_default', '2.01.03.001.00')->first()->id;
                    
                    DB::table('pos_jurnal_umum')->insert([
                            [
                                'no_jurnal_umum' => $generate_nomor_jurnal,
                                'tgl_transaksi' => Carbon::now(),
                                'no_transaksi' => $code,
                                'tipe' => 'penjualan',
                                'kode_akun' => $akunKas,
                                'debit' => $kas,
                                'kredit' => 0,
                                'sts_buku_besar' => 0,
                                'keterangan' => '-',
                                'sts_doc' => 0,
                                'created_at' => Carbon::now(),
                            ],[
                                'no_jurnal_umum' => $generate_nomor_jurnal,
                                'tgl_transaksi' => Carbon::now(),
                                'no_transaksi' => $code,
                                'tipe' => 'penjualan',
                                'kode_akun' => $akunDiskon,
                                'debit' => $disc,
                                'kredit' => 0,
                                'sts_buku_besar' => 0,
                                'keterangan' => '-',
                                'sts_doc' => 0,
                                'created_at' => Carbon::now(),
                            ],[
                                'no_jurnal_umum' => $generate_nomor_jurnal,
                                'tgl_transaksi' => Carbon::now(),
                                'no_transaksi' => $code,
                                'tipe' => 'penjualan',
                                'kode_akun' => $akunPendapatan,
                                'debit' => 0,
                                'kredit' => $pendapatan,
                                'sts_buku_besar' => 0,
                                'keterangan' => '-',
                                'sts_doc' => 0,
                                'created_at' => Carbon::now(),
                            ],[
                                'no_jurnal_umum' => $generate_nomor_jurnal,
                                'tgl_transaksi' => Carbon::now(),
                                'no_transaksi' => $code,
                                'tipe' => 'penjualan',
                                'kode_akun' => $akunPajak,
                                'debit' => 0,
                                'kredit' => $tax,
                                'sts_buku_besar' => 0,
                                'keterangan' => '-',
                                'sts_doc' => 0,
                                'created_at' => Carbon::now(),
                            ]
                        ]);

                    DB::commit();
                    $code = ['code_sales' => $code];

                    $result = array_merge($code, config('global.success.S002'));
                }
            } catch (\Throwable $e) {
                DB::rollback();
                // $result = config('global.errors.E010');
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
                $category = DB::table('categories')->select('code', 'name')->where('parent', '2')->get();
                $parentCategory = DB::table('categories')->select('id')->where('code', 'SO')->first();
                $checkCart = Helper::checkCart();
                $items = DB::table('master_items')
                    ->join('categories', 'categories.id', '=', 'master_items.category_id')
                    ->select([
                        'master_items.id as id',
                        'master_items.kode_item as code',
                        'master_items.nama_item as name',
                        'master_items.sell_price as sell_price',
                        'master_items.category_id as category_id',
                        'categories.name as category_name',
                    ])
                    ->Where([
                        ['master_items.status', '1'],
                        ['categories.parent', $parentCategory->id],
                    ])
                    ->get();
                $sales = DB::table('sales')
                    ->where('code', $code)->first();
                $sales_details = DB::table('sales_details')
                    ->join('master_items', 'master_items.id', '=', 'sales_details.item_id')
                    ->select([
                        'sales_details.id as id',
                        'sales_details.sales_id as sales_id',
                        'sales_details.item_id as item_id',
                        'sales_details.quantity as quantity',
                        'sales_details.sell_price as sell_price',
                        'sales_details.sub_total as sub_total',
                        'sales_details.description as description',
                        'master_items.kode_item as code',
                        'master_items.nama_item as name',
                    ])->orderBy('id', 'asc')
                    ->where('sales_details.sales_id', $code)->get();
                // $members = Helper::forSelect('memberships', 'code', DB::raw('CONCAT(code, "  -  " , nama) as member'), false, false);
                $var = [
                    'nav' => 'sales',
                    'subNav' => 'sales',
                    'title' => 'Ubah Order Penjualan',
                    'items' => $items,
                    'data' => $sales,
                    'sales_details' => $sales_details,
                    'categories' => $category,
                    'cart' => $checkCart,
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
            if ($sales->status == "closes") {
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
            // jika memakai status document
            // if (($sales->status == 'cancel') || ($sales->status == 'close') || ($sales->status == 'confirm')) {
            //     session()->flash('notifikasi', [
            //         "icon" => config('global.errors.E014.status'),
            //         "title" => config('global.errors.E014.code'),
            //         "message" =>  config('global.errors.E014.message') . '. Status : ' . $sales->code . ' - ' . $sales->status,
            //     ]);
            //     return redirect()->route('sales');
            // }

            // Validation
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                // 'table' => 'required',
                // 'customer' => 'required',
                'discount' => 'required',
                'tax' => 'required',
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query creator

            try {
                DB::beginTransaction();
                // simpan header
                $membership_code = DB::table('memberships')->where('code', $request->membership_code)->first();
                $sales = DB::table('sales')
                    ->where('code', $code)
                    ->update([
                        'membership_code' => is_null($membership_code) ? null : $request->membership_code,
                        'discount' => $request->discount,
                        'tax' => $request->tax,
                        'updated_at' => Carbon::now(),
                        'user_updated' => Auth::id(),
                    ]);
                $subGrandTotal = 0;
                $GrandTotal = 0;

                // simpan sales details
                if ($request->item_id > 0) {
                    $salesDetails = DB::table('sales_details')->where('sales_id', $code)->get();
                    foreach ($request->item_id as $key => $value) {
                        $item = DB::table('master_items')->select('buy_price', 'sell_price')->where('id', $request->item_id[$key])->first();
                        $subTotal = abs($request->quantity[$key]) * $item->sell_price;
                        if (($request->item_id[$key])) {
                            if (isset($request->sales_detail_id[$key])) {
                                $salesDetail = DB::table('sales_details')->where('id', $request->sales_detail_id[$key])->first();
                                if ($salesDetail->item_id == $request->item_id[$key]) {
                                    DB::table('sales_details')
                                        ->where('id', $request->sales_detail_id[$key])
                                        ->update([
                                            'quantity' => abs($request->quantity[$key]),
                                            'buy_price' => $salesDetail->buy_price,
                                            'sell_price' => $salesDetail->sell_price,
                                            'sub_total' => $salesDetail->sell_price * abs($request->quantity[$key]),
                                            'updated_at' => Carbon::now(),
                                        ]);
                                } else {
                                    DB::table('sales_details')
                                        ->where('id', $request->sales_detail_id[$key])
                                        ->update([
                                            'item_id' => $request->item_id[$key],
                                            'quantity' => abs($request->quantity[$key]),
                                            'buy_price' => $item->buy_price,
                                            'sell_price' => $item->sell_price,
                                            'sub_total' => $subTotal,
                                            'updated_at' => Carbon::now(),

                                        ]);
                                }
                            } else {
                                DB::table('sales_details')
                                    ->insert([
                                        'sales_id' => $code,
                                        'item_id' => $request->item_id[$key],
                                        'quantity' => abs($request->quantity[$key]),
                                        'buy_price' => $item->buy_price,
                                        'sell_price' => $item->sell_price,
                                        'sub_total' => $subTotal,
                                        'created_at' => Carbon::now(),

                                    ]);
                            }
                        }
                    }
                }

                $sumGrandTotal = DB::table('sales_details')->where('sales_id', $code)->sum('sub_total');
                $sumDisc = $sumGrandTotal - ($sumGrandTotal * ($request->discount / 100));
                $GrandTotal = $sumDisc + ($sumDisc * ($request->tax / 100));

                //update sumGrandTotal & Grand Total
                $salesSum = DB::table('sales')
                    ->where('code', $code)
                    ->update([
                        'sub_total' => $sumGrandTotal,
                        'total' => $GrandTotal,
                        'updated_at' => Carbon::now(),
                    ]);
                DB::commit();
                $result = config('global.success.S002');
            } catch (\Throwable $e) {
                // $result = $e->getMessage();
                DB::rollback();
                $result = config('global.errors.E010');
                return $e->getMessage();
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

    public function storeChart(Request $request){
        if (Helper::checkACL('sales', 'u')) {
            try {
                DB::beginTransaction();
                $masterCart = DB::table('carts')->where('table', $request->table)->first();
                if($masterCart){
                    DB::table('cart_details')->updateOrInsert(
                        ['cart_id' => $masterCart->id, 'item_id' => $request->item_id],
                        ['cart_id' => $masterCart->id, 'item_id' => $request->item_id, 'quantity' => $request->quantity, 'sell_price' => $request->sell_price, 'sub_total' => $request->sub_total, 'description' => $request->description]
                    );
                } else {
                    $id = DB::table('carts')->insertGetId([
                        'table' => $request->table,
                        'user_created' => Auth::id(),
                        'created_at' => Carbon::now()
                    ]);

                    DB::table('cart_details')->insert([
                        'cart_id' => $id, 
                        'item_id' => $request->item_id, 
                        'quantity' => $request->quantity, 
                        'sell_price' => $request->sell_price, 
                        'sub_total' => $request->sub_total,
                        'description' => $request->description
                    ]);
                }
                DB::commit();
                $result = config('global.success.S003');
            } catch (\Throwable $th) {
                DB::rollBack();
                $result = config('global.errors.E009');
            }
            return response()->json($result);
        } else {
            // tidak memiliki otorisasi
            $result = config('global.errors.E002');
        }
        return response()->json($result);
    }

    public function storeChartDiscount(Request $request)
    {
        if (Helper::checkACL('sales', 'u')) {
            try {
                DB::beginTransaction();

                DB::table('carts')->where('table', $request->table)->update([
                    'disc'      => $request->disc,
                    'tax'       => $request->tax,
                    'disc_rp'   => $request->disc_rp,
                    'tax_rp'    => $request->tax_rp
                ]);

                DB::commit();
                $result = config('global.success.S003');
            } catch (\Throwable $th) {
                DB::rollBack();
                $result = config('global.errors.E009');
            }
        } else {
            // tidak memiliki otorisasi
            $result = config('global.errors.E002');
        }
        return response()->json($result); //return json ke request ajax
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
                
                $masterCart = DB::table('carts')->where('table', $request->table)->first();
                
                DB::table('cart_details')->where('cart_id', $masterCart->id)->where('item_id', $request->item_id)->delete();

                $detailCount = DB::table('cart_details')->where('cart_id', $masterCart->id)->count();

                if($detailCount == 0){
                    DB::table('carts')->where('table', $request->table)->delete();
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

    public function getItemDatatable(Request $request)
    {
        
    }

    public function getCart(Request $request)
    {
        if (Helper::checkACL('sales', 'r')) {
            try {
                $table = $request->id;
                $cart = DB::table('carts')->where([
                    ['table', $table],
                ])->first();

                if (is_null($cart)) {
                    $result = config('global.errors.E011');
                    return response()->json($result);
                } else {
                    $cartDetail = DB::table('cart_details')
                        ->leftJoin('master_items', 'cart_details.item_id', '=', 'master_items.id')
                        ->select([
                            'cart_details.item_id as item_id',
                            'master_items.kode_item as item_code',
                            'master_items.nama_item as item_name',
                            'cart_details.sell_price as sell_price',
                            'cart_details.quantity as quantity',
                            DB::raw('(CASE WHEN cart_details.description IS NULL THEN "" ELSE cart_details.description END) AS description')
                        ])
                        ->where([
                            ['cart_details.cart_id', $cart->id],
                        ])->get();

                    $result = [
                        'cart' => $cart,
                        'cart_details' => $cartDetail,
                    ];

                    $result = array_merge($result, config('global.success.S000'));
                }
            } catch (\Throwable $th) {
                $result = config('global.errors.E011');
                $result = $th->getMessage();
            }
        } else {
            $result = config('global.errors.E002');
        }
        return response()->json($result);
    }
    public function editSales(Request $request, $code, $confirm)
    {
        if (Helper::checkACL('sales', 'r')) {
            // datanya
            try {
                $category = DB::table('categories')->select('code', 'name')->where('parent', '2')->get();

                $sales = DB::table('sales')
                    ->where('code', $code)->first();
                $sales_details = DB::table('sales_details')
                    ->join('master_items', 'master_items.id', '=', 'sales_details.item_id')
                    ->join('categories', 'categories.id', '=', 'master_items.category_id')
                    ->select([
                        'sales_details.id as id',
                        'sales_details.sales_id as sales_id',
                        'sales_details.item_id as item_id',
                        'sales_details.quantity as quantity',
                        'sales_details.sell_price as sell_price',
                        'sales_details.sub_total as sub_total',
                        'sales_details.description as description',
                        'master_items.kode_item as item_code',
                        'master_items.nama_item as item_name',
                        'categories.name as category_name',
                    ])->orderBy('id', 'asc')
                    ->where('sales_details.sales_id', $code)->get();
                // ./datanya
                if (is_null($sales)) {
                    $result = config('global.errors.E011');
                }
                $result = $confirm;
                if ($confirm == 'true') {
                    $result = [
                        'user' => $request->codeUser,
                        'pass' => $request->codePass,
                    ];
                    // validasi user/pass
                    $vMessage = config('global.vMessage'); //get global validation messages\
                    $validator = Validator::make($request->all(), [
                        // 'membership_code' => 'exists:memberships,code',
                        'codeUser' => 'required',
                        'codePass' => 'required',
                    ], $vMessage);
                    // Valid?
                    $valid = Helper::validationFail($validator);
                    if (!is_null($valid)) {
                        return response()->json($valid); //return if not valid
                    }
                    // check otorisasi
                    $getUser = DB::table('users')->where('username', $request->codeUser)->first();

                    if (!is_null($getUser)) {
                        // check hash pass
                        $hash_password = $getUser->password;
                        if (Hash::check($request->codePass, $hash_password)) {
                            // return data
                            $result = [
                                'sales' => $sales,
                                'sales_details' => $sales_details,
                                'categories' => $category,
                            ];
                            $result = array_merge($result, config('global.success.S000'));
                        } else {
                            $result = config('global.errors.E001');
                        }
                    } else {
                        $result = config('global.errors.E001');
                    }
                }
                if ($confirm == "false") {
                    // if ($sales->status == 'pending') {
                    // tambahkandi pengaturan program ada otorisasi edit atau tidak
                    // if ('a' == 'a') {
                    // return all result
                    $result = [
                        'sales' => $sales,
                        'sales_details' => $sales_details,
                        'categories' => $category,
                    ];
                    $result = array_merge($result, config('global.success.S000'));
                }
            } catch (\Throwable $th) {
                $result = config('global.errors.E011');
                // $result = $checkUser;
            }
        } else {
            $result = config('global.errors.E002');
        }
        return response()->json($result);
    }

    public function printSales($code)
    {
        try {
            $companies = DB::table('companies')->where('id', 1)->first();
            $configuration = DB::table('configurations')->where('id', 1)->first();
            $sales = DB::table('sales')->where('code', $code)->first();

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
            $companies = DB::table('companies')->where('id', 1)->first();
            $configuration = DB::table('configurations')->where('id', 1)->first();
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
            $companies = DB::table('companies')->where('id', 1)->first();
            $configuration = DB::table('configurations')->where('id', 1)->first();
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
}
