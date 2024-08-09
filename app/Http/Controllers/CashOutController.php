<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class CashOutController extends Controller
{
    public function index() 
    {
        if (Helper::checkACL('sales', 'r')) {
            $var = ['nav' => 'cashOut', 'subNav' => 'Cash Out', 'title' => 'Kas Keluar'];
            return view('cash.cash_out.index', $var);
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

    public function formCreate() 
    {
        if (Helper::checkACL('master_coa', 'c')) {
            $akun = DB::table('chart_of_accounts')->select(['id', 'name'])->where('code_parent', 'LIKE', '%1.01.01%')->get();
            $akunBiaya = DB::table('chart_of_accounts')->select(['id', 'name'])->where('name', 'LIKE', '%biaya%')->get();
            $var = ['nav' => 'cash', 'subNav' => 'cash-out', 'title' => 'PEMBAYARAN', 'akun' => $akun, 'akunBiaya' => $akunBiaya];
            return view('cash.cash_out.create', $var);
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    public function datatable(Request $request)
    {
        if ($request->ajax()) {
            // query data
            $cash_out = DB::table('cash_out');

            return Datatables::of($cash_out)
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('nomor_dokumen'))) {
                        $instance->where(function($w) use($request){
                            $no_dokumen = $request->get('nomor_dokumen');
                            $w->where('nomor_dokumen', $no_dokumen);
                        });
                    }
                    if (!empty($request->get('date'))) {
                        $instance->where(function($w) use($request){
                            $date = $request->get('date');
                            $explode = explode(' - ', $date);

                            $w->whereDate('tgl_transaksi', '>=', $explode[0])
                              ->whereDate('tgl_transaksi', '<=', $explode[1]);
                        });
                    }
                })
                ->addColumn('action', function ($cash_out) {
                    // render column action
                    return '<center><a data-toggle="modal" data-target="#modalDetails" class="btn btn-sm btn-info" data-toggle="tooltip" data-placement="bottom" title="Detail Transaksi" onclick="showDetails('.$cash_out->id.', \''.$cash_out->nomor_dokumen.'\')"><i class="fas fa-list"></i><a><a class="btn btn-sm btn-danger" id="btnRemove" data-toggle="tooltip" data-placement="bottom" title="Hapus Transaksi" data-id="'.$cash_out->id.'"><i class="fas fa-trash"></i><a></center>';
                })
                ->rawColumns(['action']) //render raw custom column
                ->order(function ($query) {
                    if (request()->has('id')) {
                        $query->orderBy('id', 'desc');
                    }
                })
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
    public function datatableDetails(Request $request)
    {
        if ($request->ajax()) {
            // query data
            $cash_out = DB::table('cash_out_details')->join('chart_of_accounts', 'cash_out_details.akun_biaya', '=', 'chart_of_accounts.id');
            return Datatables::of($cash_out)
                ->filter(function ($query) use ($request) {
                    $query->where('id_cash_out', $request->id);
                })
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

    public function store(Request $request)
    {
        $details = $request->detail;

        if (Helper::checkACL('membership', 'c')) {
            // Query creator
            DB::beginTransaction();

            try {
                $cashOutID = DB::table('cash_out')
                    ->insertGetId([
                        'nomor_dokumen' => $request->nomor_dokumen,
                        'akun_kasbank' => $request->akun_kasbank,
                        'tgl_transaksi' => $request->tgl_transaksi,
                        'total_nominal' => $request->total_nominal,
                        'bayar_kepada' => $request->bayar_kepada,
                        'total_pembayaran' => $request->total_pembayaran,
                        'total_biaya' => $request->total_biaya,
                        'balance' => $request->balance,
                        'created_at' => Carbon::now(),
                    ]);

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
    
                    // Input Jurnal Kas Keluar
                    DB::table('pos_jurnal_umum')->insert([
                        'no_jurnal_umum' => $generate_nomor_jurnal,
                        'tgl_transaksi' => Carbon::now(),
                        'no_transaksi' => $request->nomor_dokumen,
                        'tipe' => 'kas keluar',
                        'kode_akun' => $request->akun_kasbank,
                        'debit' => $request->total_nominal,
                        'kredit' => 0,
                        'sts_buku_besar' => 0,
                        'keterangan' => '-',
                        'sts_doc' => 0,
                        'created_at' => Carbon::now()
                    ]);
                    
                foreach ($details AS $dtls) {
                    DB::table('cash_out_details')->insert([
                        'id_cash_out' => $cashOutID,
                        'akun_biaya' => $dtls['akun_biaya'],
                        'tgl_pelaksanaan' => $dtls['tgl_pelaksanaan'],
                        'nominal' => $dtls['nominal'],
                        'keperluan' => $dtls['keperluan'],
                        'created_at' => Carbon::now(),
                    ]);

                    // Input Jurnal
                    DB::table('pos_jurnal_umum')->insert([
                        'no_jurnal_umum' => $generate_nomor_jurnal,
                        'tgl_transaksi' => Carbon::now(),
                        'no_transaksi' => $request->nomor_dokumen,
                        'tipe' => 'kas keluar',
                        'kode_akun' => $dtls['akun_biaya'],
                        'debit' => 0,
                        'kredit' => $dtls['nominal'],
                        'sts_buku_besar' => 0,
                        'keterangan' => $dtls['keperluan'],
                        'sts_doc' => 0,
                        'created_at' => Carbon::now()
                    ]);
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
    
    public function remove(Request $request){
        if (Helper::checkACL('membership', 'c')) {
            // Query creator
            DB::beginTransaction();

            try {
                DB::table('cash_out')->where('id', $request->id)->delete();
                // Delete detail
                DB::table('cash_out_details')->where('id_cash_out', $request->id)->delete();

                DB::commit();
                $result = config('global.success.S004');
            } catch (\Throwable $e) {
                // $result = $e->getMessage();
                DB::rollback();
                $result = config('global.errors.E019');
            }
        } else {
            $result = config('global.errors.E002');
        }    
        
        return response()->json($result);
    }
}
