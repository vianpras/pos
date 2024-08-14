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

// Controller Item untuk pembelian
class MasterItemController extends Controller
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
            $bahan_baku = DB::table('master_items')->where('tipe', 'Bahan Baku')->get();
            $var = [
                'nav'       => 'data-induk', 
                'subNav'    => 'item', 
                'title'     => 'Item', 
                'category'  => $category,
                'bahan_baku' => $bahan_baku
            ];

            return view('master.master_items.index', $var);
        } else {
            // tidak memiliki otorisasi
            session()->flash('notifikasi', [
                "icon"      => config('global.errors.E002.status'),
                "title"     => config('global.errors.E002.code'),
                "message"   =>  config('global.errors.E002.message'),
            ]);

            return redirect('dashboard');
        }        
    }
    public function datatable(Request $request)
    {
        if (Helper::checkACL('master_item', 'r')) {
            if ($request->ajax()) {
                // query data
                $items = DB::table('master_items')->leftJoin('units','master_items.satuan','=','units.id')->select('master_items.*', 'units.name AS nama_satuan');
                return Datatables::of($items)
                    ->addColumn('action', function ($items) {
                        // render column action
                        return view('master.master_items.action', [
                            'edit_url' => '/',
                            'show_url' => '/',
                            'id' => $items->id,
                            'status' => $items->status,
                        ]);
                    })
                    ->filter(function ($query) use ($request) {
                        if (!empty($request->get('kode_item_filter'))) {
                            $query->where('master_items.kode_item', $request->kode_item_filter);
                        }
                        if (!empty($request->get('nama_item_filter'))) {
                            $query->where('master_items.nama_item', 'like', "%{$request->nama_item_filter}%");
                        }
                        if (!empty($request->get('tipe_filter'))) {
                            $query->where('master_items.tipe', $request->tipe_filter);
                        }
                    })
                    ->rawColumns(['action'])
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
            $bahan_baku = DB::table('master_items')->where('tipe', 'Bahan Baku')->get();
            $category = DB::table('categories')->select('id', 'name', 'code')->where('parent', '!=', null)->get();
            $var = ['nav' => 'data-induk', 'subNav' => 'item', 'title' => 'Tambah Item', 'unit' => $unit, 'category' => $category, 'bahan_baku' => $bahan_baku];

            return view('master.master_items.create', $var);
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }
    public function store(Request $request)
    {
        if (Helper::checkACL('master_item', 'c')) {
            DB::beginTransaction();

            try {
                // Input Master Items
                $idMaster = DB::table('master_items')->insertGetId([
                    'kode_item' => $request->kode_item,
                    'nama_item' => $request->nama_item,
                    'satuan' => $request->satuan,
                    'category_id' => $request->category_id,
                    'tipe' => $request->tipe,
                    'buy_price' => str_replace(".", "", $request->buy_price),
                    'sell_price' => str_replace(".", "", $request->sell_price),
                    'description' => $request->description,
                    'status_bahan_baku' => (($request->status_bahan_baku) ? '1':'0'),
                    'user_created' => auth()->user()->id,
                    'created_at' => Carbon::now()
                ]);

                if($request->tipe == 'Item Jadi' && $request->status_bahan_baku == 1){
                    $details = $request->id_bahan_baku;
                    foreach($details AS $key => $dtl){
                        DB::table('master_items_bahan')->insert([
                            'id_item_jadi'      => $idMaster,
                            'id_item_bahan'     => $dtl,
                            'qty_bahan'         => $request->qty_bahan[$key],
                            'harga_beli_bahan'  => $request->harga_beli_bahan[$key]
                        ]);
                    }
                }

                DB::commit();
                $result = config('global.success.S002');
            } catch (\Throwable $e) {
                // $result = $e->getMessage();
                DB::rollback();
                $result = config('global.errors.E010');
            }
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    public function edit(Request $request, $id)
    {
        if (Helper::checkACL('master_item', 'r')) {
            try {
                $data = DB::table('master_items')->where('id', $id)->first();
                $unit = Helper::forSelect('units', 'id', 'name', false, false);
                $detail_bahan = DB::table('master_items_bahan')->leftJoin('master_items', 'master_items_bahan.id_item_jadi','=','master_items.id')->where('master_items.id', $id)->select('master_items.*', 'master_items_bahan.id_item_bahan', 'master_items_bahan.qty_bahan', 'master_items_bahan.harga_beli_bahan')->get();
                $bahan_baku = DB::table('master_items')->where('tipe', 'Bahan Baku')->get();
                $category = DB::table('categories')->select('id', 'name', 'code')->where('parent', '!=', null)->get();
                $var = ['nav' => 'data-induk', 'subNav' => 'barang', 'title' => 'Edit Item ' . $data->nama_item, 'data' => $data, 'unit' => $unit, 'category' => $category, 'bahan_baku' => $bahan_baku, 'detail_bahan' => $detail_bahan];
            } catch (\Throwable $e) {
                $result = config('global.errors.E011');
                return response()->json($result);
            }
            
            return view('master.master_items.edit', $var);
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }
    public function update(Request $request, $id)
    {
        if (Helper::checkACL('master_item', 'u')) {
            try {
                DB::table('master_items')->where('id', $id)->update([
                    'kode_item'     => $request->kode_item,
                    'nama_item'     => $request->nama_item,
                    'satuan'        => $request->satuan,
                    'category_id'   => $request->category_id,
                    'tipe'          => $request->tipe,
                    'buy_price'     => str_replace(".", "", $request->buy_price),
                    'sell_price'    => str_replace(".", "", $request->sell_price),
                    'description'   => $request->description,
                    'status_bahan_baku' => (($request->status_bahan_baku) ? '1':'0'),
                    'user_updated'  => auth()->user()->id,
                    'updated_at'    => Carbon::now()
                ]);
                DB::table('master_items_bahan')->where('id_item_jadi', $id)->delete();

                if($request->tipe == 'Item Jadi' && $request->status_bahan_baku == 1){

                    $details = $request->id_bahan_baku;
                    foreach($details AS $key => $dtl){
                        DB::table('master_items_bahan')->insert([
                            'id_item_jadi'      => $id,
                            'id_item_bahan'     => $dtl,
                            'qty_bahan'         => $request->qty_bahan[$key],
                            'harga_beli_bahan'  => $request->harga_beli_bahan[$key]
                        ]);
                    }
                }

                $result = config('global.success.S003');
            } catch (\Throwable $e) {
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
                $item = DB::table('master_items')->where('id', $id);
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

    function dataDetails(Request $request){
        $id = $request->id;
        $data = DB::table('master_items')->leftJoin('units','master_items.satuan','=','units.id')->where('master_items.id', $id)->select('master_items.*','units.name AS nama_satuan')->first();

        return response()->json($data);
    }
    function itemByTipe(Request $request){
        $tipe = $request->tipe;
        $data = DB::table('master_items')->leftJoin('units','master_items.satuan','=','units.id')->where('master_items.tipe', $tipe)->select('master_items.*','units.name AS nama_satuan')->get();

        return response()->json($data);
    }

    function listItem(){
        $items = DB::table('SAPOITM')->get();
        $var = [
            'nav'       => 'data-induk', 
            'subNav'    => 'item', 
            'title'     => 'Tambah Item', 
            'items'     => $items
        ];

        return view('sales.listItem', $var);
    }

    function itemPriceByCode(Request $request){
        $itemcode = $request->itemcode;
        $qty = $request->qty;
        $listnum = ($request->pricelist != "") ? $request->pricelist : 1;

        $pricelist = DB::table('SAPOPLN')->get();
        $itemprice = DB::table('SAPITM1')->where('itemcode', $itemcode)->where('pricelist', $listnum)->first();

        $var = [
            'pricelist' => $pricelist,
            'itemprice' => $itemprice
        ];
        return response()->json($var);
    }
}
