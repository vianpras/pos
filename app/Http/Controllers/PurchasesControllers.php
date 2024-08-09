<?php

namespace App\Http\Controllers;


use App\Helpers\Helper;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;


class PurchasesControllers extends Controller
{
    public function __construct()
    {
        // set middleware
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *purchases
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Helper::checkACL('purchase', 'r')) {
            // render index
            $membership = Helper::forSelect('memberships', 'code', 'nama', false, false);

            $var = ['nav' => 'purchases', 'subNav' => 'purchases', 'title' => 'Transaksi Pembelian', 'membership' => $membership];
            return view('purchases.index', $var);
        } else {
            // tidak memiliki otorisasi
            session()->flash('notifikasi', [
                "icon" => config('global.errors.E002.status'),
                "title" => config('global.errors.E002.code'),
                "message" =>  config('global.errors.E002.message'),
            ]);
            return redirect('dashboard');
        }
    }
    public function datatable(Request $request)
    {
        if (Helper::checkACL('purchase', 'r')) {
            if ($request->ajax()) {
                // query data
                $purchases = DB::table('purchases')
                    ->leftJoin('suppliers', 'purchases.supplier_code', '=', 'suppliers.code')

                    ->select([
                        'purchases.code as code',
                        'purchases.supplier as supplier',
                        'purchases.date_order as date_order',
                        'purchases.total as total',
                        'suppliers.nama as supplier_name',
                        'purchases.status as status',
                    ]);
                return Datatables::of($purchases)
                    ->addColumn('action', function ($purchases) {
                        // render column action
                        return view('purchases.action', [
                            'edit_url' => '/purchase/edit/' . $purchases->code,
                            'show_url' => '/',
                            'id' => $purchases->code,
                            'status' => $purchases->status,
                        ]);
                    })
                    ->editColumn('status', function ($purchases) {
                        // render column status
                        $_status = Helper::statusBadge($purchases->status);
                        return $_status;
                    })

                    ->filter(function ($query) use ($request) {

                        if ($request->has('purchases_code_filter')) {
                            // default column filter
                            $query->where('purchases.code', 'like', "%{$request->purchases_code_filter}%");
                        }
                        if ($request->has('purchases_status_filter')) {
                            if (($request->purchases_status_filter) == '-1') {
                                // default column filter
                                $query->where('purchases.status', "like", "%");
                            } else {
                                // filtered column
                                $query->where('purchases.status', 'like', "%" . $request->purchases_status_filter . "%");
                            }
                        }
                        if ($request->has('purchases_date_filter')) {
                            if (($request->purchases_date_filter) == null) {
                                // default column filter 1 bulan
                                $query->where([
                                    ['purchases.date_order', '>=', Date('Y-m-d', strtotime("-6 months")) . ' 00:00:00'],
                                    ['purchases.date_order', '<=',  Date('Y-m-d') . ' 59:59:59'],
                                ]);
                            } else {
                                // filtered column
                                $dateSeparator = explode(" - ", $request->purchases_date_filter);
                                $query->where([
                                    ['purchases.date_order', '>=', $dateSeparator[0] . ' 00:00:00'],
                                    ['purchases.date_order', '<=', $dateSeparator[1] . ' 59:59:59'],
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
                    "message" =>  config('global.errors.E002.message'),
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
        if (Helper::checkACL('purchase', 'c')) {
            $parentCategory = DB::table('categories')->select('id')->where('code', 'PO')->first();
            $category = DB::table('categories')->select('code', 'name')->where('parent', $parentCategory->id)->get();
            $items = DB::table('items')
                ->join('categories', 'categories.id', '=', 'items.category_id')
                ->select([
                    'items.id as id',
                    'items.code as code',
                    'items.name as name',
                    'items.buy_price as buy_price',
                    'items.category_id as category_id',
                    'categories.name as category_name'
                ])
                ->Where([
                    ['items.status', '1'],
                    ['categories.parent', $parentCategory->id],
                ])
                ->get();
            $var = [
                'nav' => 'purchasesCreate',
                'subNav' => 'purchases',
                'title' => 'Tambah Order Pembelian',
                'categories' => $category,
                'items' => $items,
            ];
            return view('purchases.create', $var);
        } else {
            session()->flash('notifikasi', [
                "icon" => config('global.errors.E002.status'),
                "title" => config('global.errors.E002.code'),
                "message" =>  config('global.errors.E002.message'),
            ]);
            return redirect('dashboard');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $action)
    {

        if (Helper::checkACL('purchase', 'c')) {
            // Validation
            // dd($request->all());
            $vMessage = config('global.vMessage'); //get global validation messages\
            $validator = Validator::make($request->all(), [
                // 'supplier_code' => 'exists:suppliers,code',
                'date_order' => 'required',
                'discount' => 'required',
                'tax' => 'required',
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query creator
            DB::beginTransaction();

            try {
                $code  = Helper::docPrefix('purchases');
                $supplier = DB::table('suppliers')->select('code', 'nama')->where('code', $request->supplier_code)->first();

                // simpan header
                // summary subtotal
                $purchases = DB::table('purchases')
                    ->insert([
                        'code' => $code,
                        'supplier_code' => is_null($supplier) ? null : $supplier->code,

                        'supplier' => $request->supplier,
                        'code' => $code,
                        'date_order' => $request->date_order,
                        'discount' => $request->discount,
                        'tax' => $request->tax,
                        'description' => $request->description,
                        'status' => $action == "simpan" ? "pending" : "close",
                        'created_at' => Carbon::now(),
                        'user_created' => Auth::id()
                    ]);
                $subGrandTotal = 0;
                $GrandTotal = 0;

                // simpan purchases details
                if ($request->item_id > 0) {
                    foreach ($request->item_id as $key => $value) {
                        if (!is_null($request->item_id[$key])) {
                            $item = DB::table('items')->select('buy_price', 'sell_price')->where('id', $request->item_id[$key])->first();
                            $subTotal = $request->quantity[$key] * $item->sell_price;
                            $purchasesDetails = DB::table('purchases_details')
                                ->insert([
                                    'purchases_id' => $code,
                                    'item_id' => $request->item_id[$key],
                                    'quantity' => $request->quantity[$key],
                                    'buy_price' => $item->buy_price,
                                    'sell_price' => $item->sell_price,
                                    'sub_total' => $subTotal,
                                    'description' => $request->catatan[$key],
                                    'created_at' => Carbon::now(),
                                ]);
                            $subGrandTotal = $subGrandTotal + $subTotal;
                        }
                    }
                }
                $sumDisc = $subGrandTotal - ($subGrandTotal * ($request->discount / 100));
                $GrandTotal = $sumDisc + ($sumDisc * ($request->tax / 100));

                //update subGrandTotal & Grand Total
                $purchasesSum = DB::table('purchases')
                    ->where('code', $code)
                    ->update([
                        'sub_total' => $subGrandTotal,
                        'total' => $GrandTotal,
                        'created_at' => Carbon::now()
                    ]);

                DB::commit();
                $result = config('global.success.S002');
            } catch (\Throwable $e) {
                DB::rollback();
                // $result = $e->getMessage();
                $result = config('global.errors.E010');
            }
            return response()->json($result);
        } else {
            // tidak memiliki otorisasi
            session()->flash('notifikasi', [
                "icon" => config('global.errors.E002.status'),
                "title" => config('global.errors.E002.code'),
                "message" =>  config('global.errors.E002.message'),
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
        if (Helper::checkACL('purchase', 'r')) {
            $parentCategory = DB::table('categories')->select('id')->where('code', 'PO')->first();
            $category = DB::table('categories')->select('code', 'name')->where('parent', $parentCategory->id)->get();
            $items = DB::table('items')
                ->join('categories', 'categories.id', '=', 'items.category_id')
                ->select([
                    'items.id as id',
                    'items.code as code',
                    'items.name as name',
                    'items.buy_price as buy_price',
                    'items.category_id as category_id',
                    'categories.name as category_name'
                ])
                ->Where([
                    ['items.status', '1'],
                    ['categories.parent', $parentCategory->id],
                ])
                ->get();
            $purchases = DB::table('purchases')
                ->where('code', $code)->first();
            $purchases_details = DB::table('purchases_details')
                ->join('items', 'items.id', '=', 'purchases_details.item_id')
                ->select([
                    'purchases_details.id as id',
                    'purchases_details.purchases_id as purchases_id',
                    'purchases_details.item_id as item_id',
                    'purchases_details.quantity as quantity',
                    'purchases_details.buy_price as buy_price',
                    'purchases_details.sub_total as sub_total',
                    'purchases_details.description as description',
                    'items.code as code',
                    'items.name as name'
                ])->orderBy('id', 'asc')
                ->where('purchases_details.purchases_id', $code)->get();
            $var = [
                'nav' => 'purchases',
                'subNav' => 'purchases',
                'title' => 'Ubah Order Pembelian',
                'items' => $items,
                'data' => $purchases,
                'purchases_details' => $purchases_details,
                'categories' => $category,
            ];
            if ($purchases->status == "close") {
                session()->flash('notifikasi', [
                    "icon" => 'warning',
                    "title" => config('global.errors.E014.code'),
                    "message" =>  config('global.errors.E014.message'),
                ]);
                return redirect('purchase');
            } else {
                return view('purchases.edit', $var);
            }
        } else {
            $result = config('global.errors.E002');
        }

        session()->flash('notifikasi', [
            "icon" => config('global.errors.E002.status'),
            "title" => config('global.errors.E002.code'),
            "message" =>  config('global.errors.E002.message'),
        ]);
        return redirect('dashboard');
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
        if (Helper::checkACL('purchase', 'u')) {
            $purchases = DB::table('purchases')->where('code', $code)->first();
            if (($purchases->status == 'cancel') || ($purchases->status == 'close') || ($purchases->status == 'confirm')) {
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E014.status'),
                    "title" => config('global.errors.E014.code'),
                    "message" =>  config('global.errors.E014.message') . '. Status : ' . $purchases->code . ' - ' . $purchases->status,
                ]);
                return redirect()->route('purchases');
            }
            // Validation
            // dd($request->all());
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                // 'supplier_code' => 'exists:suppliers,code',
                'date_order' => 'required',
                'discount' => 'required',
                'tax' => 'required',
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query creator
            DB::beginTransaction();


            try {
                // simpan header
                // summary subtotal
                $supplier = DB::table('suppliers')->select('code', 'nama')->where('code', $request->supplier_code)->first();
                $purchases = DB::table('purchases')
                    ->where('code', $code)
                    ->update([
                        'supplier_code' => is_null($supplier) ? null : $supplier->code,
                        'supplier' => $request->supplier,
                        'code' => $code,
                        'date_order' => $request->date_order,
                        'discount' => $request->discount,
                        'tax' => $request->tax,
                        'description' => $request->description,
                        'status' => $action == "simpan" ? "pending" : "close",
                        'updated_at' => Carbon::now(),
                        'user_updated' => Auth::id()
                    ]);
                $subGrandTotal = 0;
                $GrandTotal = 0;

                // simpan purchases details
                if ($request->item_id > 0) {
                    $purchasesDetails = DB::table('purchases_details')->where('purchases_id', $code)->get();
                    foreach ($request->item_id as $key => $value) {
                        $item = DB::table('items')->select('buy_price', 'sell_price')->where('id', $request->item_id[$key])->first();
                        $subTotal = $request->quantity[$key] * $item->sell_price;
                        if (!is_null($request->item_id[$key])) {
                            if (!is_null($request->purchases_detail_id_[$key])) {
                                // foreach ($purchasesDetails as $purchasesDetail) {
                                $purchasesDetail = DB::table('purchases_details')->where('id', $request->purchases_detail_id_[$key])->first();
                                if ($purchasesDetail->item_id == $request->item_id[$key]) {
                                    DB::table('purchases_details')
                                        ->where('id', $request->purchases_detail_id_[$key])
                                        ->update([
                                            // 'item_id' => $request->item_id[$key],
                                            'quantity' => $request->quantity[$key],
                                            'buy_price' => $purchasesDetail->buy_price,
                                            'sell_price' => $purchasesDetail->sell_price,
                                            'sub_total' => $purchasesDetail->sell_price * $request->quantity[$key],
                                            'updated_at' => Carbon::now(),
                                        ]);
                                } else {
                                    DB::table('purchases_details')
                                        ->where('id', $request->purchases_detail_id_[$key])
                                        ->update([
                                            'item_id' => $request->item_id[$key],
                                            'quantity' => $request->quantity[$key],
                                            'buy_price' => $item->buy_price,
                                            'sell_price' => $item->sell_price,
                                            'sub_total' => $subTotal,
                                            'created_at' => Carbon::now(),
                                        ]);
                                }
                            } else {
                                DB::table('purchases_details')
                                    ->insert([
                                        'purchases_id' => $code,
                                        'item_id' => $request->item_id[$key],
                                        'quantity' => $request->quantity[$key],
                                        'buy_price' => $item->buy_price,
                                        'sell_price' => $item->sell_price,
                                        'sub_total' => $subTotal,
                                        'updated_at' => Carbon::now(),
                                    ]);
                            }
                        }
                    }
                }

                $sumGrandTotal = DB::table('purchases_details')->where('purchases_id', $code)->sum('sub_total');
                $sumDisc = $sumGrandTotal - ($sumGrandTotal * ($request->discount / 100));
                $GrandTotal = $sumDisc + ($sumDisc * ($request->tax / 100));

                //update sumGrandTotal & Grand Total
                $purchasesSum = DB::table('purchases')
                    ->where('code', $code)
                    ->update([
                        'sub_total' => $sumGrandTotal,
                        'total' => $GrandTotal,
                        'updated_at' => Carbon::now()
                    ]);
                DB::commit();
                $result = config('global.success.S002');
            } catch (\Throwable $e) {
                // $result = $e->getMessage();
                DB::rollback();
                return $e->getMessage();
                // $result = config('global.errors.E010');
            }
            return response()->json($result);
        } else {
            // tidak memiliki otorisasi
            session()->flash('notifikasi', [
                "icon" => config('global.errors.E002.status'),
                "title" => config('global.errors.E002.code'),
                "message" =>  config('global.errors.E002.message'),
            ]);
            return redirect('dashboard');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {
        // disable data
        if (Helper::checkACL('purchase', 'd')) {
            $id = $request->id;
            DB::beginTransaction();
            try {
                $purchases = DB::table('purchases')->where('code', $id);
                $member_login = DB::table('memberships')->where('username', $id);
                $status = $purchases->first()->status;
                $statusM = $member_login->first()->status;
                $purchases->update(['status' => $status == 'active' ? 'suspend' : 'active']);
                $member_login->update(['status' => $statusM  ? false : true]);
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

    public function removeCart(Request $request){
        if (Helper::checkACL('purchase', 'u')) {
            DB::beginTransaction();
            try {
                $removeCart_id = $request->id;
                $removeCart = DB::table('purchases_details')->where('id', $removeCart_id)->delete();
                $result = config('global.success.S003');
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                
                $result = config('global.errors.E009');
            }
            return response()->json($result); //return json ke request ajax
        } else {
            // tidak memiliki otorisasi
            $result = config('global.errors.E002');
        }
        return response()->json($result); //return json ke request ajax
    }

    public function getItem(Request $request)
    {
        if (Helper::checkACL('purchase', 'r')) {
            $parentCategory = DB::table('categories')->select('id')->where('code', 'PO')->first();

            if ($request->search == 'favorit') {
                $items = DB::table('purchases_details')
                    ->join('items', 'items.id', '=', 'purchases_details.item_id')
                    ->join('categories', 'categories.id', '=', 'items.category_id')
                    ->select([
                        'items.id as id',
                        'items.code as code',
                        'items.name as name',
                        'items.buy_price as buy_price',
                        'items.category_id as category_id',
                        'categories.name as category_name'
                    ])
                    ->Where([
                        ['items.status', '1'],
                        ['categories.parent', $parentCategory->id],
                        // ['items.name', 'like', "%{$request->search}%"]
                    ])
                    ->groupBy('purchases_details.item_id')
                    ->limit(10)
                    ->get();
            } else {
                $items = DB::table('items')
                    ->join('categories', 'categories.id', '=', 'items.category_id')
                    ->select([
                        'items.id as id',
                        'items.code as code',
                        'items.name as name',
                        'items.buy_price as buy_price',
                        'items.category_id as category_id',
                        'categories.name as category_name'
                    ])
                    ->Where([
                        ['items.status', '1'],
                        ['categories.parent', $parentCategory->id],
                        ['items.name', 'like', "%{$request->search}%"]
                    ])
                    ->orWhere([
                        ['items.status', '1'],
                        ['categories.parent', $parentCategory->id],
                        ['items.code', 'like', "%{$request->search}%"]
                    ])
                    ->orWhere([
                        ['items.status', '1'],
                        ['categories.parent', $parentCategory->id],
                        ['categories.name', 'like', "%{$request->search}%"]
                    ])
                    ->get();
            }
            $list = array();
            if (count($items) > 0) {

                $result = response()->json($items);
            } else {
                return response()->json();
            }
            $result = response()->json($items);
        } else {
            // tidak memiliki otorisasi
            // $result = config('global.errors.E002',404);
            $result = response()->json(config('global.errors.E002'), 404);
        }
        return $result;
    }
    public function getDataSupplier(Request $request)
    {
        if (Helper::checkACL('purchase', 'r')) {
            $items = DB::table('suppliers')
                ->select('code', 'nama', 'mobile', 'address')
                ->where('code', $request->code)
                ->orWhere('nama', $request->code)
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
}
