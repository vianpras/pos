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
            $kategoriHarga = Helper::forSelect('SAPOPLN', 'listnum', 'listname', false, false);
            $var = ['nav' => 'data-induk', 'subNav' => 'barang', 'title' => 'Barang', 'category' => $kategoriHarga];

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
                $items = DB::table('SAPITM1')
                        ->leftJoin('SAPOITM', 'SAPITM1.itemcode','=','SAPOITM.itemcode')
                        ->leftJoin('SAPOPLN', 'SAPITM1.pricelist','=','SAPOPLN.listnum')
                        ->select(
                            'SAPOITM.itemcode',
                            'SAPOITM.itemname',
                            'SAPITM1.price',
                            'SAPOPLN.listnum',
                            'SAPOPLN.listname'
                        );
                return Datatables::of($items)
                    ->addColumn('action', function ($item) {
                        return view('master.item.action', [
                            'edit_url' => '/',
                            'show_url' => '/',
                            'id'       => $item->itemcode
                        ]);
                    })
                    ->addColumn('pricing', function ($item) {
                        // render column buy_price
                        $buy_price = 'Rp. ' . Helper::formatNumber($item->price, '');
                        return $buy_price;
                    })
                    ->filter(function ($query) use ($request) {
                        if (!empty($request->get('itemcode_filter'))) {
                            $query->where('SAPOITM.itemcode', $request->itemcode_filter);
                        }
                        if (!empty($request->get('itemname_filter'))) {
                            $query->where('SAPOITM.itemname', $request->itemname_filter);
                        }
                        if (!empty($request->get('pricelist_filter'))) {
                            $query->where('SAPOPLN.listnum', $request->pricelist_filter);
                        }
                    })
                    ->rawColumns(['action','pricing']) 
                    ->make(true);
            } else {
                session()->flash('notifikasi', [
                    "icon"    => config('global.errors.E002.status'),
                    "title"   => config('global.errors.E002.code'),
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
