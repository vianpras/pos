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

class PembelianControllers extends Controller
{
    public function __construct()
    {
        // set middleware
        $this->middleware('auth');
    }
    public function index()
    {
        if (Helper::checkACL('pembelian', 'r')) {
            // render index
            $category = Helper::forSelect('categories', 'id', 'name', false, false);
            $var = ['nav' => 'purchasesIndex', 'subNav' => 'pembelian', 'title' => 'Pembelian', 'category' => $category];

            return view('pembelian.index', $var);
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
        if (Helper::checkACL('pembelian', 'r')) {
            if ($request->ajax()) {
                // query data
                $items = DB::table('pembelian');
                return Datatables::of($items)
                    ->addColumn('action', function ($item) {
                        // render column action
                        return view('pembelian.action', [
                            'edit_url' => '/',
                            'show_url' => '/',
                            'id' => $item->id
                        ]);
                    })
                    ->filter(function ($query) use ($request) {
                        if (!empty($request->get('kode_pembelian_filter'))) {
                            $query->where('pembelian.kode_pembelian', $request->kode_pembelian_filter);
                        }
                        if (!empty($request->get('date'))) {
                            // filtered column
                            $dateSeparator = explode(" - ", $request->date);
                            $query->where([
                                ['pembelian.tanggal', '>=', $dateSeparator[0]],
                                ['pembelian.tanggal', '<=', $dateSeparator[1]],
                            ]);
                        }
                    })
                    ->rawColumns(['action']) //render raw custom column 
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
        if (Helper::checkACL('pembelian', 'c')) {
            $unit = Helper::forSelect('units', 'id', 'name', false, false);
            $item_jadi = DB::table('master_items')->where('tipe', '!=', 'Bahan Baku')->get();
            $bahan_baku = DB::table('master_items')->where('tipe', '!=', 'Item Jadi')->get();
            $var = ['nav' => 'purchasesIndex', 'subNav' => 'pembelian', 'title' => 'Buat Pembelian', 'unit' => $unit, 'item_jadi' => $item_jadi, 'bahan_baku' => $bahan_baku];

            return view('pembelian.create', $var);
        } else {
            // tidak memiliki otorisasi
            session()->flash('notifikasi', [
                "icon" => config('global.errors.E002.status'),
                "title" => config('global.errors.E002.code'),
                "message" =>  config('global.errors.E002.message'),
            ]);

            return redirect('purchase');
        }
    }
    public function store(Request $request)
    {
        $details = $request->detail;
        // dd($details);
        if (Helper::checkACL('pembelian', 'c')) {
            DB::beginTransaction();

            try {
                $pembelianID = DB::table('pembelian')
                    ->insertGetId([
                        'kode_pembelian'=> $request->kode_pembelian,
                        'tanggal'       => $request->tgl_transaksi,
                        'total'         => $request->total_biaya,
                        'created_by'    => auth()->user()->id,
                        'created_at'    => Carbon::now(),
                    ]);
                
                foreach ($details AS $dtls) {
                    if($dtls['tipe'] == 'Lain-lain'){
                        DB::table('pembelian_details')->insert([
                            'id_pembelian'  => $pembelianID,
                            'tipe'          => $dtls['tipe'],
                            'id_item'       => '-',
                            'nama'          => $dtls['nama_item'],
                            'unit'          => $dtls['satuan_item'],
                            'harga'         => $dtls['harga'],
                            'qty'           => $dtls['qty'],
                            'total'         => $dtls['subtotal']
                        ]);
                    } else {
                        $bahan_baku = DB::table('master_items')->where('kode_item', $dtls['kode_bahan_baku'])->first();
                        DB::table('pembelian_details')->insert([
                            'id_pembelian'  => $pembelianID,
                            'tipe'          => $dtls['tipe'],
                            'id_item'       => $bahan_baku->id,
                            'nama'          => $bahan_baku->nama_item,
                            'unit'          => $bahan_baku->satuan,
                            'harga'         => $dtls['harga'],
                            'qty'           => $dtls['qty'],
                            'total'         => $dtls['subtotal']
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
        if (Helper::checkACL('pembelian', 'u')) {
            try {
                $data = DB::table('pembelian')->where('id', $id)->first();
                $data_details = DB::table('pembelian_details')
                                    ->leftJoin('master_items', 'pembelian_details.id_item','=','master_items.id')
                                    ->leftJoin('units', 'pembelian_details.unit','=','units.id')
                                    ->where('pembelian_details.id_pembelian', $data->id)
                                    ->select('pembelian_details.*', 'master_items.kode_item', 'master_items.nama_item', 'units.id AS satuan_item', 'units.name AS nama_satuan')
                                    ->get();
                $unit = Helper::forSelect('units', 'id', 'name', false, false);
                $item_jadi = DB::table('master_items')->where('tipe', '!=', 'Bahan Baku')->get();
                $bahan_baku = DB::table('master_items')->where('tipe', '!=', 'Item Jadi')->get();

                $var = ['nav' => 'purchasesIndex', 'subNav' => 'pembelian', 'title' => 'Edit Pembelian', 'data' => $data, 'data_details' => $data_details, 'unit' => $unit, 'item_jadi' => $item_jadi, 'bahan_baku' => $bahan_baku];
            } catch (\Throwable $e) {
                $result = config('global.errors.E011');
                return response()->json($e->getMessage());
            }
            return view('pembelian.edit', $var);
        } else {
            // tidak memiliki otorisasi
            session()->flash('notifikasi', [
                "icon" => config('global.errors.E002.status'),
                "title" => config('global.errors.E002.code'),
                "message" =>  config('global.errors.E002.message'),
            ]);

            return redirect('purchase');
        }
    }

    public function storeUpdate(Request $request)
    {
        $id = $request->id;
        $details = $request->detail;

        if (Helper::checkACL('pembelian', 'e')) {
            DB::beginTransaction();

            try {
                DB::table('pembelian')->where('id', $id)->update([
                    'kode_pembelian'=> $request->kode_pembelian,
                    'tanggal'       => $request->tgl_transaksi,
                    'total'         => $request->total_biaya,
                    'updated_by'    => auth()->user()->id,
                    'updated_at'    => Carbon::now()
                ]);

                DB::table('pembelian_details')->where('id_pembelian', $id)->delete();
                
                foreach ($details AS $dtls) {
                    if($dtls['tipe'] == 'Lain-lain'){
                        DB::table('pembelian_details')->insert([
                            'id_pembelian'  => $id,
                            'tipe'          => $dtls['tipe'],
                            'id_item'       => '-',
                            'nama'          => $dtls['nama_item'],
                            'unit'          => $dtls['satuan_item'],
                            'harga'         => $dtls['harga'],
                            'qty'           => $dtls['qty'],
                            'total'         => $dtls['subtotal']
                        ]);
                    } else {
                        $bahan_baku = DB::table('master_items')->where('kode_item', $dtls['kode_bahan_baku'])->first();
                        DB::table('pembelian_details')->insert([
                            'id_pembelian' => $id,
                            'tipe' => $dtls['tipe'],
                            'id_item' => $bahan_baku->id,
                            'nama' => $bahan_baku->nama_item,
                            'unit' => $bahan_baku->satuan,
                            'harga' => $dtls['harga'],
                            'qty' => $dtls['qty'],
                            'total' => $dtls['subtotal']
                        ]);
                    }
                }

                DB::commit();
                $result = config('global.success.S002');
            } catch (\Throwable $e) {
                DB::rollback();
                $result = config('global.errors.E010');
            }
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    public function showDetail($id)
    {
        $data_details = DB::table('pembelian_details')
                            ->leftJoin('master_items', 'pembelian_details.id_item','=','master_items.id')
                            ->leftJoin('units', 'pembelian_details.unit','=','units.id')
                            ->where('pembelian_details.id_pembelian', $id)
                            ->select('pembelian_details.*', 'master_items.kode_item', 'master_items.nama_item', 'units.name AS satuan')
                            ->get();

        return response()->json($data_details);
    }
}
