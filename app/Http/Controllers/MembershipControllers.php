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
            $category = Helper::forSelect('categories', 'id', 'name', false, false);

            $var = ['nav' => 'membership', 'subNav' => 'membership', 'title' => 'Keanggotaan', 'category' => $category];
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
                $memberships = DB::table('memberships')
                    ->select([
                        'memberships.code as code',
                        'memberships.nama as nama',
                        'memberships.mobile as mobile',
                        'memberships.status as status',
                    ]);
                return Datatables::of($memberships)
                    ->addColumn('action', function ($membership) {
                        // render column action
                        return view('membership.action', [
                            'edit_url' => '/',
                            'show_url' => '/',
                            'id' => $membership->code,
                            'status' => $membership->status,
                        ]);
                    })
                    ->editColumn('status', function ($membership) {
                        // render column status
                        $_status = Helper::statusBadge($membership->status);
                        return $_status;
                    })

                    ->filter(function ($query) use ($request) {
                        if ($request->membership_nama_filter) {
                            // default column filter
                            $query->where('memberships.nama', 'like', "%" . $request->membership_nama_filter . "%");
                        }

                        if ($request->has('membership_code_filter')) {
                            // default column filter
                            $query->where('memberships.code', 'like', "%{$request->membership_code_filter}%");
                        }
                        if ($request->has('membership_status_filter')) {
                            if (($request->membership_status_filter) == '-1') {
                                // default column filter
                                $query->where('memberships.status', "<=", 3);
                            } else {
                                // filtered column
                                $query->where('memberships.status', 'like', "%" . $request->membership_status_filter . "%");
                            }
                        }
                        if ($request->has('membership_date_filter')) {
                            if (($request->membership_date_filter) == null) {
                                // default column filter 1 bulan
                                $query->where([
                                    ['memberships.created_at', '>=', Date('Y-m-d', strtotime("-6 months")) . ' 00:00:00'],
                                    ['memberships.created_at', '<=',  Date('Y-m-d') . ' 59:59:59'],
                                ]);
                            } else {
                                // filtered column
                                $dateSeparator = explode(" - ", $request->membership_date_filter);
                                $query->where([
                                    ['memberships.created_at', '>=', $dateSeparator[0] . ' 00:00:00'],
                                    ['memberships.created_at', '<=', $dateSeparator[1] . ' 59:59:59'],
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
        if (Helper::checkACL('membership', 'c')) {

            $var = ['nav' => 'membership', 'subNav' => 'membership', 'title' => 'Tambah Keanggotaan',];
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
            // Validation
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|min:3|max:255|unique:memberships,nama',
                'place_birth' => 'required|min:3|max:255',
                'date_birth' => 'required|min:3|max:255',
                'kota' => 'required|min:3|max:255',
                'provinsi' => 'required|min:3|max:255',
                'email' => 'required|string|min:3|max:255|unique:memberships,email',
                'mobile' => 'required|string|min:3|max:255|unique:memberships,mobile',
                'nik' => 'required|string|min:16|max:17|unique:memberships,nik',
                'email' => 'required|email|max:255|unique:member_logins,email',
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query creator
            DB::beginTransaction();

            try {
                $code  = Helper::memberPrefix('memberships');
                $password = substr(md5(microtime()),26);
                $member = DB::table('member_logins')
                ->insertGetId([
                   'name' => $request->nama,
                   'username' => $code,
                   'email' => $request->email,
                   'mobile' => $request->mobile,
                   'password' => Hash::make($password),
                   'status' => 1,
                //    'users_acls_id' => nu,
                   'created_at' => Carbon::now(),
                ]);

                $membership = DB::table('memberships')
                    ->insert([
                        'code' => $code,
                        'nama' => $request->nama,
                        'nik' => $request->nik,
                        'mobile' => $request->mobile,
                        'gender' => $request->gender,
                        'kota' => $request->kota,
                        'provinsi' => $request->provinsi,
                        'email' => $request->email,
                        'place_birth' => $request->place_birth,
                        'date_birth' => $request->date_birth,
                        'status' => $request->status,
                        'address' => $request->address,
                        'created_at' => Carbon::now(),
                        'expired' => '2030-12-31',
                        'member_logins_id' => $member,
                        'user_created' => Auth::id(),
                    ]);
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
                $data = DB::table('memberships')
                ->where('code', $code)->first();
                if(is_null($data)){
                    dd($data);
                    $result = config('global.errors.E011');
                    return response()->json($result);
                } 
                $var = ['nav' => 'membership', 'subNav' => 'membership', 'title' => 'Edit Keanggotaan ' . $data->code, 'data' => $data];
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
    public function update(Request $request, $code)
    {

        if (Helper::checkACL('membership', 'u')) {
            // Validation
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                // 'email' => 'required|string|min:3|max:255|unique:memberships,email,'.$code,
                // 'mobile' => 'required|string|min:3|max:255|unique:memberships,mobile,'.$code,
                // 'nik' => 'required|string|min:16|max:17|unique:memberships,nik,'.$code,
                // 'email' => 'required|email|max:255|unique:users,email,'.$code,
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query creator
            DB::beginTransaction();

            try {

                DB::table('memberships')
                    ->where('code', $code)
                    ->update([
                        'nama' => $request->nama,
                        'nik' => $request->nik,
                        'mobile' => $request->mobile,
                        'gender' => $request->gender,
                        'kota' => $request->kota,
                        'provinsi' => $request->provinsi,
                        'email' => $request->email,
                        'place_birth' => $request->place_birth,
                        'date_birth' => $request->date_birth,
                        'status' => $request->status,
                        'address' => $request->address,
                        'expired' => '2030-12-31',
                        'member_logins_id' => 1,
                        'updated_at' => Carbon::now(),
                        'user_updated' => Auth::id(),
                    ]);
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
                $membership = DB::table('memberships')->where('code', $id);
                $member_login = DB::table('member_logins')->where('username', $id);
                $status = $membership->first()->status;
                $statusM = $member_login->first()->status;
                $membership->update(['status' => $status == 'active' ? 'suspend' : 'active']);
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
}
