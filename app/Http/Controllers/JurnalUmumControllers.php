<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class JurnalUmumControllers extends Controller
{
    public function index(Request $request){
        if (Helper::checkACL('jurnal_umum', 'r')) {
            if ($request->ajax()) {
                // query data
                $data = DB::table('pos_jurnal_umum')->select('no_jurnal_umum', 'no_transaksi', 'tgl_transaksi', 'tipe', DB::raw('SUM(debit) AS debit'))->groupBy('no_jurnal_umum');

                return Datatables::of($data)
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('nomor_jurnal'))) {
                            $instance->where(function($w) use($request){
                                $no_jurnal = $request->get('nomor_jurnal');
                                $w->where('no_jurnal_umum', $no_jurnal);
                            });
                        }
                        if (!empty($request->get('nomor_transaksi'))) {
                            $instance->where(function($w) use($request){
                                $no_transaksi = $request->get('nomor_transaksi');
                                $w->where('no_transaksi', $no_transaksi);
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
                    ->addColumn('link_jurnal', function($data){
                        return '<a href="'.url('jurnal/detail/').'/'.$data->no_jurnal_umum.'">'.$data->no_jurnal_umum.'</a>';
                    })
                    ->rawColumns(['link_jurnal'])
                    ->order(function ($query) {
                        if (request()->has('id')) {
                            $query->orderBy('id', 'desc');
                        }
                    })
                    ->make(true);
            }

            $var = [
                'nav' => 'jurnal_umum',
                'subNav' => 'jurnal_umum',
                'title' => 'Jurnal Umum',
            ];

            return view('jurnal_umum.index', $var);  
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

    public function formCreate(){
        if (Helper::checkACL('jurnal_umum', 'c')) {
            // Generate ticket kode
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

            $var = [
                'nav' => 'create_jurnal',
                'subNav' => 'create_jurnal',
                'title' => 'Jurnal Umum',
                'nomor_jurnal' => $generate_nomor_jurnal
            ];

            return view('jurnal_umum.create', $var);
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

    public function getAccount(){
        $items = DB::table('chart_of_accounts')->select('id', 'name', 'code_account_default AS code_account')->get();

        return json_encode($items);
    }

    public function store(Request $request){
        $details = $request->detail;
        
        foreach ($details AS $dtls) {
            DB::table('pos_jurnal_umum')->insert([
                    'no_jurnal_umum' => $request->nomor_jurnal,
                    'no_transaksi' => $request->nomor_transaksi,
                    'tgl_transaksi' => $request->tanggal_transaksi,
                    'tipe' => 'jurnal entry',
                    'sts_buku_besar' => 0,
                    'sts_doc' => 0,
                    'kode_akun' => $dtls['akun'],
                    'debit' => $dtls['debit'],
                    'kredit' => $dtls['kredit'],
                    'keterangan' => $dtls['keterangan'],
                    'created_at' => Carbon::now(),
                ]);
        }
        $result = config('global.success.S002');
        
        return response()->json($result);
    }

    public function detail($no_jurnal){

        $master = DB::table('pos_jurnal_umum')->where('no_jurnal_umum', $no_jurnal)->groupBy('no_jurnal_umum')->first();
        $details = DB::table('pos_jurnal_umum')
                        ->join('chart_of_accounts', 'chart_of_accounts.id','=','pos_jurnal_umum.kode_akun')
                        ->where('pos_jurnal_umum.no_jurnal_umum', $no_jurnal)->get();

        $var = [
            'nav' => 'create_jurnal',
            'subNav' => 'create_jurnal',
            'title' => 'Jurnal Umum',
            'master' => $master,
            'details' => $details
        ];

        return view('jurnal_umum.details', $var);
    }

    public function formPosting(){
        if (Helper::checkACL('jurnal_umum', 'p')) {
            $var = ['nav' => 'jurnal_umum', 'subNav' => 'jurnal_umum', 'title' => 'Posting Jurnal'];
            return view('jurnal_umum.posting_jurnal', $var);
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    public function posting(Request $request){
        $tgl_awal = $request->start_date;
        $tgl_akhir = $request->end_date;

        $akun = DB::table('chart_of_accounts')->orderBy('id', 'ASC')->get();
        foreach($akun AS $master_akun){
            $jurnal = DB::table('pos_jurnal_umum')->where('tgl_transaksi', '>=', $tgl_awal)->where('tgl_transaksi', '<=', $tgl_akhir)->where('kode_akun', $master_akun->id)->where('sts_buku_besar', '0')->get();

            if($jurnal->count() > 0){
                foreach($jurnal as $jurnal_akun){           
                    DB::table('pos_buku_besar')->insert([
                        'no_buku_besar' => '001',
                        'no_jurnal_umum'=> $jurnal_akun->no_jurnal_umum,
                        'tgl_transaksi' => $jurnal_akun->tgl_transaksi,
                        'no_transaksi'  => $jurnal_akun->no_transaksi,
                        'tipe'          => $jurnal_akun->tipe,
                        'kode_akun'     => $jurnal_akun->kode_akun,
                        'debit'         => $jurnal_akun->debit,
                        'kredit'        => $jurnal_akun->kredit,
                        'keterangan'    => $jurnal_akun->keterangan,
                        'sts_doc'       => $jurnal_akun->sts_doc
                    ]);

                    DB::table('pos_jurnal_umum')->where('id', $jurnal_akun->id)->update([
                        'sts_buku_besar'    => '1'
                    ]);
                }
            }
        }
        $result = config('global.success.S003');
        return response()->json($result);
    }

    public function bukuBesar(Request $request){

        if ($request->ajax()) {

            $data = DB::table(DB::raw("(SELECT no_jurnal_umum,tgl_transaksi,no_transaksi,tipe,kode_akun,debit,kredit FROM pos_jurnal_umum ORDER BY kode_akun ASC) AS j"))
                        ->leftJoin(DB::raw("(SELECT * FROM chart_of_accounts) AS coa"), "coa.id", "=", "j.kode_akun")
                        ->select("j.no_jurnal_umum", "j.tgl_transaksi", "j.no_transaksi", "j.tipe", "j.kode_akun", "coa.name", "j.debit", "j.kredit")
                        ->get();
            
            
            foreach($data AS $dt){
                
                $rekap = DB::table(DB::raw("(SELECT no_jurnal_umum,tgl_transaksi,no_transaksi,tipe,kode_akun,debit,kredit FROM pos_jurnal_umum ORDER BY kode_akun ASC) AS j"))
                            ->leftJoin(DB::raw("(SELECT * FROM chart_of_accounts) AS coa"), "coa.id", "=", "j.kode_akun")
                            ->select("j.no_jurnal_umum", "j.tgl_transaksi", "j.no_transaksi", "j.tipe", "j.kode_akun", "coa.name", "j.debit", "j.kredit");

                $kode_akun = "";
                $saldo = 0;
                return Datatables::of($rekap)
                        ->addColumn('saldo', function($data) use ($kode_akun, $saldo){
                            if($kode_akun != $data->kode_akun || $kode_akun == ""){
                                DB::statement(DB::raw('set @variable=0'));
                                $saldo = 1;
                            } else {
                                $saldo = 2;
                            }

                            return $saldo;
                            $kode_akun = $data->kode_akun;
                        })
                        ->rawColumns(['saldo'])
                        ->order(function ($query) { 
                            $query->orderBy('kode_akun', 'asc');
                        })
                        ->make(true);
            }
                        
        }

        $var = [
            'nav' => 'buku besar',
            'subNav' => 'buku besar',
            'title' => 'Buku Besar',
        ];

        return view('jurnal_umum.buku_besar', $var);
    }
}
