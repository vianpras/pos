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

// Controller item menu
class ItemControllers extends Controller
{
    public function __construct()
    {
        // set middleware
        $this->middleware('auth');
    }
    public function index()
    {
        if (Helper::checkACL('master_item', 'r')) {
            // render index
            $category = Helper::forSelect('categories', 'id', 'name', false, false);


            $var = ['nav' => 'data-induk', 'subNav' => 'barang', 'title' => 'Barang', 'category' => $category];
            // dump($var);
            return view('master.item.index', $var);
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
        if (Helper::checkACL('master_item', 'r')) {
            if ($request->ajax()) {
                // query data
                $items = DB::table('items')
                    ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                    ->leftJoin('units as big_unit', 'items.big_unit_id', '=', 'big_unit.id')
                    ->leftJoin('units as small_unit', 'items.small_unit_id', '=', 'small_unit.id')
                    ->select([
                        'items.id as id',
                        'items.name as name',
                        'items.code as code',
                        'items.buy_price as buy_price',
                        'items.sell_price as sell_price',
                        'items.big_quantity as big_quantity',
                        'items.small_quantity as small_quantity',
                        'items.status as status',
                        'big_unit.code as big_unit',
                        'small_unit.code as small_unit',
                        'categories.name as category',
                    ]);
                return Datatables::of($items)
                    ->addColumn('action', function ($item) {
                        // render column action
                        return view('master.item.action', [
                            'edit_url' => '/',
                            'show_url' => '/',
                            'id' => $item->id,
                            'status' => $item->status,
                        ]);
                    })
                    ->editColumn('picture', function ($item) {
                        // render column picture
                        $html_code = '<img style="max-width:50px; width:50px; height:50px; margin-right:20px;" id="'.$item->code.'"  class="profile-user-img  img-circle text-center img-fluid" src="/img/items/' . $item->id . '" />';
                        return $html_code;
                    })
                    ->addColumn('small_quantity', function ($item) {
                        // render column small_quantity
                        $small_quantity = Helper::formatNumber($item->small_quantity, '') . ' ' . $item->small_unit;
                        return $small_quantity;
                    })

                    ->addColumn('buy_price', function ($item) {
                        // render column buy_price
                        $buy_price = 'Rp. ' . Helper::formatNumber($item->buy_price, '');
                        return $buy_price;
                    })

                    ->addColumn('sell_price', function ($item) {
                        // render column sell_price
                        $sell_price = 'Rp. ' . Helper::formatNumber($item->sell_price, '');
                        return $sell_price;
                    })
                    ->addColumn('big_quantity', function ($item) {
                        // render column big_quantity
                        $big_quantity = Helper::formatNumber($item->big_quantity, '') . ' ' . $item->big_unit;
                        return $big_quantity;
                    })
                    // ->addColumn('cost_price', function ($item) {
                    //     return Helper::formatNumber($item->cost_price,'rupiah');
                    // })
                    // ->addColumn('sell_price', function ($item) {
                    //     return Helper::formatNumber($item->sell_price,'rupiah');
                    // })
                    ->editColumn('status', function ($item) {
                        // render column status
                        $_status = $item->status == '1'
                            ? '<center><span class="right badge badge-success">Aktif</span></center>'
                            : '<center><span class="right badge badge-danger">Non-Aktif</span></center>';
                        return $_status;
                    })

                    ->filter(function ($query) use ($request) {
                        if ($request->has('item_name_filter')) {
                            // default column filter
                            $query->where('items.name', 'like', "%{$request->item_name_filter}%");
                        }

                        if ($request->has('item_code_filter')) {
                            // default column filter
                            $query->where('items.code', 'like', "%{$request->item_code_filter}%");
                        }
                        // dd($request->item_category_filter);
                        if ($request->has('item_category_filter')) {
                            // default column filter
                            if (($request->item_category_filter) == '-1') {
                                // default column filter
                                $query->where('items.category_id', ">=", 0);
                            } else {
                                // filtered column
                                $query->where('items.category_id', '=', $request->item_category_filter);
                            }
                        }
                        if ($request->has('item_status_filter')) {
                            if (($request->item_status_filter) == '-1') {
                                // default column filter
                                $query->where('items.status', "<=", 3);
                            } else {
                                // filtered column
                                $query->where('items.status', 'like', "%" . $request->item_status_filter . "%");
                            }
                        }
                        if ($request->has('item_date_filter')) {
                            if (($request->item_date_filter) == null) {
                                // default column filter 1 bulan
                                $query->where([
                                    ['items.created_at', '>=', Date('Y-m-d', strtotime("-6 months")) . ' 00:00:00'],
                                    ['items.created_at', '<=',  Date('Y-m-d') . ' 59:59:59'],
                                ]);
                            } else {
                                // filtered column
                                $dateSeparator = explode(" - ", $request->item_date_filter);
                                $query->where([
                                    ['items.created_at', '>=', $dateSeparator[0] . ' 00:00:00'],
                                    ['items.created_at', '<=', $dateSeparator[1] . ' 59:59:59'],
                                ]);
                            }
                        }
                    })
                    ->rawColumns(['action', 'picture', 'status', 'big_quantity', 'small_quantity']) //render raw custom column 
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
    public function create()
    {
        if (Helper::checkACL('master_item', 'c')) {

            $unit = Helper::forSelect('units', 'id', 'name', false, false);
            $category = DB::table('categories')->select('id', 'name', 'code')->where('parent', '!=', null)->get();
            $var = ['nav' => 'data-induk', 'subNav' => 'barang', 'title' => 'Tambah Barang', 'unit' => $unit, 'category' => $category];
            return view('master.item.create', $var);
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    public function edit(Request $request, $id)
    {
        if (Helper::checkACL('master_item', 'r')) {
            try {
                $data = DB::table('items')->where('id', $id)->first();
                $unit = Helper::forSelect('units', 'id', 'name', false, false);
                $category = Helper::forSelect('categories', 'id', 'name', false, false);
                $var = ['nav' => 'data-induk', 'subNav' => 'barang', 'title' => 'Edit Barang ' . $data->name, 'data' => $data, 'unit' => $unit, 'category' => $category];
            } catch (\Throwable $e) {
                $result = config('global.errors.E011');
                return response()->json($result);
            }
            return view('master.item.edit', $var);
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }


    public function store(Request $request)
    {

        if (Helper::checkACL('master_item', 'c')) {
            // Validation
            $buy_price = Helper::saveCurrency($request->buy_price);
            $sell_price = Helper::saveCurrency($request->sell_price);

            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:255',
                'code' => 'required|string|min:3|max:255|unique:items,code',
                // 'big_unit_id' => 'required|exists:units,id',
                'small_unit_id' => 'required|exists:units,id',
                'category_id' => 'required|exists:categories,id',
                // 'big_quantity' => 'required|min:1',
                'small_quantity' => 'required|min:1',
                'status' => 'required|boolean',
                'image' => ['image', 'mimes:jpeg,bmp,png', 'max:2048'],

            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if ($buy_price > $sell_price) {
                return response()->json(config('global.errors.E013'));
            }
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query creator
            try {
                $item = DB::table('items')
                    ->insertGetId([
                        'name' => $request->name,
                        'code' => $request->code,
                        'buy_price' => Helper::saveCurrency($request->buy_price),
                        'sell_price' => Helper::saveCurrency($request->sell_price),
                        'big_unit_id' => $request->big_unit_id,
                        'small_unit_id' => $request->small_unit_id,
                        'category_id' => $request->category_id,
                        'big_quantity' => $request->big_quantity,
                        'small_quantity' => $request->small_quantity,
                        'status' => $request->status,
                        'description' => $request->description,
                        'created_at' => Carbon::now(),
                        'user_created' => Auth::id(),
                    ]);
                if ($request->hasFile('image')) {
                    $gambar = $request->file('image');
                    // dd($gambar);
                    if ($request->file('image')->isValid()) {
                        $gambar->storeAs('/items', $item, 'private');
                    }
                }
                $result = config('global.success.S002');
            } catch (\Throwable $e) {
                $result = config('global.errors.E010');
            }
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    public function update(Request $request, $id)
    {

        if (Helper::checkACL('master_item', 'u')) {
            // Validation
            $buy_price = Helper::saveCurrency($request->buy_price);
            $sell_price = Helper::saveCurrency($request->sell_price);

            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:255',
                'code' => 'required|string|min:3|max:255|unique:items,code,' . $id,
                // 'big_unit_id' => 'required|exists:units,id',
                'small_unit_id' => 'required|exists:units,id',
                'category_id' => 'required|exists:categories,id',
                // 'big_quantity' => 'required|min:1',
                'small_quantity' => 'required|min:1',
                'status' => 'required|boolean',
                'image' => ['image', 'mimes:jpeg,bmp,png', 'max:2048'],

            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if ($buy_price > $sell_price) {
                return response()->json(config('global.errors.E013'));
            }
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query Updater
            try {
                DB::table('items')
                    ->where('id', $id)
                    ->update([
                        'name' => $request->name,
                        'code' => $request->code,
                        'buy_price' => $buy_price,
                        'sell_price' => $sell_price,
                        'big_unit_id' => $request->big_unit_id,
                        'small_unit_id' => $request->small_unit_id,
                        'category_id' => $request->category_id,
                        'big_quantity' => $request->big_quantity,
                        'small_quantity' => $request->small_quantity,
                        'status' => $request->status,
                        'description' => $request->description,
                        'updated_at' => Carbon::now(),
                        'user_updated' => Auth::id(),
                    ]);
                if ($request->hasFile('image')) {
                    $gambar = $request->file('image');
                    if ($request->file('image')->isValid()) {
                        $gambar->storeAs('/items', $id, 'private');
                    }
                }
                $result = config('global.success.S003');
            } catch (\Throwable $e) {
                // dd();
                $result = config('global.errors.E009');
            }
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    public function disable(Request $request)
    {
        // disable data
        if (Helper::checkACL('master_item', 'd')) {
            $id = $request->id;
            try {
                $item = DB::table('items')->where('id', $id);
                $status = $item->first()->status;
                $item->update(['status' => $status ? false : true]);
                $result = config('global.success.S003');
            } catch (QueryException $e) {
                $result = config('global.errors.E009');
            } catch (\Throwable $e) {
                $result = config('global.errors.E009');
            }
        } else {
            // tidak memiliki otorisasi
            $result = config('global.errors.E002');
        }

        return response()->json($result); //return json ke request ajax
    }
}
