<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class CashInController extends Controller
{
    public function indexCashBank(){
        if (Helper::checkACL('sales', 'r')) {
            $var = ['nav' => 'cashBank', 'subNav' => 'Cash Bank', 'title' => 'Kas & Bank'];
            return view('cash.index', $var);
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
    public function datatableCashBank(Request $request)
    {
        if ($request->ajax()) {
            // query data
            $akun = DB::table('chart_of_accounts')->where('code_parent', 'LIKE', '%1.01.01%');
            return Datatables::of($akun)
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
    public function index() 
    {
        if (Helper::checkACL('sales', 'r')) {
            $var = ['nav' => 'cashIn', 'subNav' => 'Cash In', 'title' => 'Kas Masuk'];
            return view('cash.cash_in.index', $var);
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
            $akunPendapatan = DB::table('chart_of_accounts')->select(['id', 'name'])->where('group_of_account', 'pendapatan')->get();
            $var = ['nav' => 'cash', 'subNav' => 'cash-in', 'title' => 'PEMASUKAN', 'akun' => $akun, 'akunPendapatan' => $akunPendapatan];
            return view('cash.cash_in.create', $var);
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    public function datatable(Request $request)
    {
        if ($request->ajax()) {
            // query data
            $cash_in = DB::table('cash_in');
            return Datatables::of($cash_in)
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
                ->addColumn('action', function ($cash_in) {
                    // render column action
                    return '<center><a data-toggle="modal" data-target="#modalDetails" class="btn btn-sm btn-info" data-toggle="tooltip" data-placement="bottom" title="Detail Transaksi" onclick="showDetails('.$cash_in->id.', \''.$cash_in->nomor_dokumen.'\')"><i class="fas fa-list"></i><a><a class="btn btn-sm btn-danger" id="btnRemove" data-toggle="tooltip" data-placement="bottom" title="Hapus Transaksi" data-id="'.$cash_in->id.'"><i class="fas fa-trash"></i><a></center>';
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
            $cash_out = DB::table('cash_in_details')->join('chart_of_accounts', 'cash_in_details.akun_pendapatan', '=', 'chart_of_accounts.id');
            return Datatables::of($cash_out)
                ->filter(function ($query) use ($request) {
                    $query->where('id_cash_in', $request->id);
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
                $cashInID = DB::table('cash_in')
                    ->insertGetId([
                        'nomor_dokumen' => $request->nomor_dokumen,
                        'akun_kasbank' => $request->akun_kasbank,
                        'tgl_transaksi' => $request->tgl_transaksi,
                        'total_nominal' => $request->total_nominal,
                        'terima_dari' => $request->terima_dari,
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

                // Input Jurnal Kas Masuk
                DB::table('pos_jurnal_umum')->insert([
                    'no_jurnal_umum' => $generate_nomor_jurnal,
                    'tgl_transaksi' => Carbon::now(),
                    'no_transaksi' => $request->nomor_dokumen,
                    'tipe' => 'kas masuk',
                    'kode_akun' => $request->akun_kasbank,
                    'debit' => $request->total_nominal,
                    'kredit' => 0,
                    'sts_buku_besar' => 0,
                    'keterangan' => '-',
                    'sts_doc' => 0,
                    'created_at' => Carbon::now()
                ]);
                    
                foreach ($details AS $dtls) {
                    DB::table('cash_in_details')->insert([
                        'id_cash_in' => $cashInID,
                        'akun_pendapatan' => $dtls['akun_pendapatan'],
                        'tgl_pelaksanaan' => $dtls['tgl_pelaksanaan'],
                        'nominal' => $dtls['nominal'],
                        'keterangan' => $dtls['keterangan'],
                        'created_at' => Carbon::now(),
                    ]);

                    // Input Jurnal
                    DB::table('pos_jurnal_umum')->insert([
                        'no_jurnal_umum' => $generate_nomor_jurnal,
                        'tgl_transaksi' => Carbon::now(),
                        'no_transaksi' => $request->nomor_dokumen,
                        'tipe' => 'kas masuk',
                        'kode_akun' => $dtls['akun_pendapatan'],
                        'debit' => 0,
                        'kredit' => $dtls['nominal'],
                        'sts_buku_besar' => 0,
                        'keterangan' => $dtls['keterangan'],
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
}
