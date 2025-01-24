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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\persetujuanMail;

class MembershipControllers extends Controller
{
    public function __construct()
    {
        // set middleware
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *membership
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Helper::checkACL('membership', 'r')) {
            // render index

            $var = ['nav' => 'membership', 'subNav' => 'membership', 'title' => 'Keanggotaan'];
            return view('membership.index', $var);
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
        if (Helper::checkACL('membership', 'r')) {
            if ($request->ajax()) {
                // query data
                $memberships = DB::table('sales_membership');

                return Datatables::of($memberships)
                    ->addColumn('action', function ($membership) {
                        // render column action
                        return view('membership.action', [
                            'edit_url' => '/',
                            'show_url' => '/',
                            'id' => $membership->id,
                            'status' => $membership->status,
                        ]);
                    })
                    ->editColumn('status', function ($membership) {
                        // render column status
                        $_status = Helper::statusBadge($membership->status);
                        return $_status;
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
        if (Helper::checkACL('membership', 'c')) {

            $var = ['nav' => 'membership', 'subNav' => 'membership', 'title' => 'Tambah Membership',];
            return view('membership.create', $var);
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (Helper::checkACL('membership', 'c')) {
            // Query creator
            DB::beginTransaction();
            try {
                $code  = Helper::memberPrefix('memberships');

                DB::table('sales_membership')->updateOrInsert(['nomor_handphone' => $request->mobile, 'member_category' => $request->member_category],[
                    'member_category'   => $request->member_category,
                    'member_code'       => $code,
                    'full_name'         => $request->nama,
                    'nomor_handphone'   => $request->mobile,
                    'jenis_kelamin'     => $request->gender,
                    'kota'              => $request->kota,
                    'provinsi'          => $request->provinsi,
                    'tempat_lahir'      => $request->place_birth,
                    'tgl_lahir'         => $request->date_birth,
                    'email'             => $request->email,
                    'status'            => $request->status,
                    'alamat'            => $request->address,
                    'user_created'      => Auth::id(),
                    'created_at'        => Carbon::now()
                ]);

                DB::commit();
                $result = config('global.success.S002');
            } catch (\Throwable $e) {
                $result = $e->getMessage();
                DB::rollback();
                // $result = config('global.errors.E010');
            }
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
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
        if (Helper::checkACL('membership', 'r')) {
            try {
                $data = DB::table('sales_membership')->where('id', $code)->first();
                if(is_null($data)){
                    $result = config('global.errors.E011');
                    return response()->json($result);
                } 
                $var = ['nav' => 'membership', 'subNav' => 'membership', 'title' => 'Edit Keanggotaan ' . $data->member_code, 'data' => $data];
            } catch (\Throwable $e) {
                $result = config('global.errors.E011');
                return response()->json($e->getMessage());
            }
            return view('membership.edit', $var);
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        if (Helper::checkACL('membership', 'u')) {
            $code = $request->member_code;

            // Query creator
            DB::beginTransaction();
            try {

                DB::table('sales_membership')->where('member_code', $code)
                    ->update([
                        'member_category'   => $request->member_category,
                        'full_name'         => $request->nama,
                        'nomor_handphone'   => $request->mobile,
                        'jenis_kelamin'     => $request->gender,
                        'kota'              => $request->kota,
                        'provinsi'          => $request->provinsi,
                        'tempat_lahir'      => $request->place_birth,
                        'tgl_lahir'         => $request->date_birth,
                        'email'             => $request->email,
                        'status'            => $request->status,
                        'alamat'            => $request->address,
                        'user_updated'      => Auth::id(),
                        'updated_at'        => Carbon::now()
                    ]);
                DB::commit();

                $result = [
                    'status' => 'success',
                    'message' => 'Berhasil update member'
                ];
            } catch (\Throwable $e) {
                DB::rollback();

                $result = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
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
        if (Helper::checkACL('membership', 'd')) {
            $id = $request->id;

            DB::beginTransaction();
            try {
                $membership = DB::table('sales_membership')->where('id', $id);
                $status = $membership->first()->status;
                $membership->update(['status' => $status == 'active' ? 'suspend' : 'active']);

                $result = config('global.success.S003');
                DB::commit();
            } catch (QueryException $e) {
                DB::rollBack();

                $result = $e->getMessage();
            } catch (\Throwable $e) {
                DB::rollBack();

                $result = $e->getMessage();
            }
        } else {
            // tidak memiliki otorisasi
            $result = config('global.errors.E002');
        }

        return response()->json($result); //return json ke request ajax
    }

    public function persetujuan()
    {
        if (Helper::checkACL('membership', 'c')) {
            $member = DB::table('memberships')->where('agreement', '0')->get();

            $var = ['nav' => 'membership', 'subNav' => 'membership', 'title' => 'Persetujuan Keanggotaan', 'members' => $member];
            return view('membership.persetujuan', $var);
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    public function emailMember(Request $request)
    {

        if (Helper::checkACL('membership', 'c')) {
            // Validation
            $code_member = $request->member;
            $member = DB::table('memberships')->where('code', $code_member)->first();

            $path = public_path()."/persetujuan/perjanjian-keanggotaan.pdf";
            $data = array('nama' => $member->nama, 'code' => $code_member, 'gender' => $member->gender, 'tgl_lahir' => $member->date_birth, 'phone_number' => $member->mobile, 'alamat' => $member->address, 'email' => $member->email,'attach' => $path);

            Mail::to($member->email)->send(new persetujuanMail($data));
            
            $result = config('global.success.S003');
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    public function settingMember()
    {
        $data_retail = DB::table('sales_membership_setting')->where('member_category', 'retail')->first();
        $data_grosir = DB::table('sales_membership_setting')->where('member_category', 'grosir')->first();

        $var = ['nav' => 'membership.setting', 'subNav' => 'membership.setting', 'title' => 'Setting Keanggotaan', 'data_retail' => $data_retail, 'data_grosir' => $data_grosir];

        return view('membership.setting', $var);
    }
    public function settingMemberStore(Request $request)
    {
        DB::beginTransaction();
        try {

            foreach($request->member_category AS $key => $val){
                DB::table('sales_membership_setting')->updateOrInsert(["member_category" => $val], [
                    "member_category"           => $val,
                    "min_transaksi"             => $request->min_payment[$key],
                    "poin_per_min_transaksi"    => $request->poin_transaction[$key],
                    "konversi_per_poin"         => $request->konversi_poin[$key],
                    "date_exp"                  => $request->date_exp[$key],
                    "month_exp"                 => $request->month_exp[$key],
                    "created_by"                => Auth::id(),
                    "created_at"                => Carbon::now()
                ]);
            }

            DB::commit();

            $result = [
                'status' => 'success'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            $result = [
                'status' => 'error'
            ];
        }

        return response()->json($result);
    }

    public function history()
    {
        $var = ['nav' => 'membership_points', 'subNav' => 'membership.history_point', 'title' => 'History Poin Keanggotaan'];

        return view('membership.history', $var);
    }

    public function historyDatatable(Request $request)
    {
        if ($request->ajax()) {
            // query data
            $history_point = DB::table('sales_membership_transaction_points')->leftJoin('sales_membership', 'sales_membership_transaction_points.member_id', '=', 'sales_membership.member_code')
                                ->select('sales_membership_transaction_points.*', 'sales_membership.full_name', 'sales_membership.nomor_handphone');

            return Datatables::of($history_point)
                ->filter(function ($query) use ($request) {
                    if($request->membership_name) {
                        // default column filter
                        $query->where('sales_membership.full_name', 'LIKE', "%{$request->membership_name}%");
                    }
                    if($request->membership_phone) {
                        // default column filter
                        $query->where('sales_membership.nomor_handphone', "{$request->membership_phone}");
                    }
                })
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

    public function check(Request $request)
    {
        $data_member = DB::table('sales_membership')->where('nomor_handphone', $request->phone)->first();
        $sumPointPlus = DB::table('sales_membership_transaction_points')->where('type', 'masuk')->where('member_id', $data_member->member_code)->sum('point');
        $sumPointMinus = DB::table('sales_membership_transaction_points')->where('type', 'keluar')->where('member_id', $data_member->member_code)->sum('point');

        $pointPosition = $sumPointPlus - $sumPointMinus;

        $data_member->posisi_point = $pointPosition;

        $result = [
            'status' => ($data_member) ? 'success' : 'error',
            'data'  => $data_member
        ];

        return response()->json($result);
    }
}
