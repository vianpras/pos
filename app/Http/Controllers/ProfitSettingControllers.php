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

class ProfitSettingControllers extends Controller
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
            $datas = DB::table('profit_setting')->leftJoin('master_items','profit_setting.itemcode','=','master_items.id')
                        ->select('profit_setting.*', 'master_items.nama_item', 'master_items.kode_item')
                        ->get();

            $var = ['nav' => 'data-induk', 'subNav' => 'profit-setting', 'title' => 'Setting Profit', 'data' => $datas];

            return view('master.profit_setting.index', $var);
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
    public function create()
    {
        if (Helper::checkACL('master_item', 'c')) {
            $items = DB::table('master_items')
                            ->leftJoin('pembelian_details', 'master_items.id','=','pembelian_details.id_item')
                            ->where('master_items.tipe', 'Item Jadi')
                            ->select('master_items.*', 'pembelian_details.total AS total_pembelian')
                            ->get();
            $var = ['nav' => 'data-induk', 'subNav' => 'profit-setting', 'title' => 'Setting Profit', 'items' => $items];

            return view('master.profit_setting.create', $var);
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    public function store(Request $request)
    {
        // Query creator
        try {
            $duplicate = DB::table('profit_setting')->where('itemcode', $request->kode_item)->first();

            if($duplicate){
                $result = config('global.errors.E010');
            } else {
                DB::table('profit_setting')->insert([
                    'itemcode' => $request->kode_item,
                    'profit_type' => $request->tipe_profit,
                    'jumlah' => $request->jumlah,
                    'created_at' => Carbon::now()
                ]);
                $result = config('global.success.S002');
            }
        } catch (\Throwable $e) {
            // $result = $e->getMessage();
            $result = config('global.errors.E010');
        }

        return response()->json($result);
    }

    public function edit($id)
    {
        if (Helper::checkACL('master_item', 'c')) {
            $items = DB::table('master_items')
                        ->leftJoin('pembelian_details', 'master_items.id','=','pembelian.item_id')
                        ->where('master_items.tipe', 'Item Jadi')
                        ->select('master_items.*', 'pembelian.total AS total_pembelian')
                        ->get();
                            
            $data = DB::table('profit_setting')->where('id', $id)->first();
            
            $var = ['nav' => 'data-induk', 'subNav' => 'profit-setting', 'title' => 'Setting Profit Edit', 'items' => $items, 'data' => $data];

            return view('master.profit_setting.edit', $var);
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    public function update(Request $request)
    {
        try {
            $id = $request->id;

            DB::table('profit_setting')->where('id', $id)->update([
                'itemcode' => $request->kode_item,
                'profit_type' => $request->tipe_profit,
                'jumlah' => $request->jumlah,
                'created_at' => Carbon::now()
            ]);
            $result = config('global.success.S002');

        } catch (\Throwable $e) {
            // $result = $e->getMessage();
            $result = config('global.errors.E010');
        }

        return response()->json($result);
    }

    public function delete(Request $request)
    {
        // Query creator
        DB::beginTransaction();

        try {
            DB::table('profit_setting')->where('id', $request->id)->delete();

            DB::commit();
            $result = config('global.success.S004');
        } catch (\Throwable $e) {
            // $result = $e->getMessage();
            DB::rollback();
            $result = config('global.errors.E019');
        }  
        
        return response()->json($result);
    }
}
