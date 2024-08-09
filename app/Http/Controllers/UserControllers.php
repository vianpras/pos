<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserControllers extends Controller
{
   public function __construct()
   {
      // set middleware
      $this->middleware('auth');
   }
   public function index()
   {
      if (Helper::checkACL('master_user', 'r')) {
         // render index
         $var = ['nav' => 'data-induk', 'subNav' => 'user', 'title' => 'Pengguna'];
         return view('master.user.index', $var);
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
      if (Helper::checkACL('master_user', 'r')) {
         if ($request->ajax()) {
            // query data
            $users = DB::table('users')
               ->leftJoin('users_acls', 'users.users_acls_id', '=', 'users_acls.id')
               ->select(['users.id', 'users.name', 'users.username', 'users.email', 'users.status', 'users_acls.name as hakakses']);

            return Datatables::of($users)
               ->addColumn('action', function ($user) {
                  // render column action
                  return view('master.user.action', [
                     'edit_url' => '/',
                     'show_url' => '/',
                     'id' => $user->id,
                     'status' => $user->status,
                  ]);
               })
               ->editColumn('status', function ($user) {
                  // render column status
                  $_status = $user->status == '1'
                     ? '<center><span class="right badge badge-success">Aktif</span></center>'
                     : '<center><span class="right badge badge-danger">Non-Aktif</span></center>';
                  return $_status;
               })

               ->filter(function ($query) use ($request) {
                  $query->where('users.id', '!=', Auth::id());
                  if ($request->has('user_name_filter')) {
                     // default column filter
                     $query->where('users.name', 'like', "%{$request->user_name_filter}%");
                  }

                  if ($request->has('user_mail_filter')) {
                     // default column filter
                     $query->where('users.email', 'like', "%{$request->user_mail_filter}%");
                  }

                  if ($request->has('user_status_filter')) {
                     if (($request->user_status_filter) == '-1') {
                        // default column filter
                        $query->where('users.status', "<=", 3);
                     } else {
                        // filtered column
                        $query->where('users.status', 'like', "%" . $request->user_status_filter . "%");
                     }
                  }
                  if ($request->has('user_date_filter')) {
                     if (($request->user_date_filter) == null) {
                        // default column filter 1 bulan
                        $query->where([
                           ['created_at', '>=', Date('Y-m-d', strtotime("-6 months")) . ' 00:00:00'],
                           ['created_at', '<=',  Date('Y-m-d') . ' 59:59:59'],
                        ]);
                     } else {
                        // filtered column
                        $dateSeparator = explode(" - ", $request->user_date_filter);
                        $query->where([
                           ['created_at', '>=', $dateSeparator[0] . ' 00:00:00'],
                           ['created_at', '<=', $dateSeparator[1] . ' 59:59:59'],
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
   public function create()
   {
      if (Helper::checkACL('master_user', 'c')) {
         $user_acl = Helper::forSelect('users_acls', 'id', 'name', false, false);
         $var = ['nav' => 'data-induk', 'subNav' => 'user', 'title' => 'Tambah Pengguna', 'user_acl' => $user_acl];
         return view('master.user.create', $var);
      } else {
         $result = config('global.errors.E002');
      }

      return response()->json($result);
   }

   public function edit(Request $request, $id)
   {
      if (Helper::checkACL('master_user', 'r')) {
         $data = DB::table('users')
            ->select('id', 'name', 'username', 'email', 'mobile', 'status', 'last_login', 'users_acls_id', 'sudo', 'created_at')
            ->where('id', $id)->first();
            $user_acl = Helper::forSelect('users_acls', 'id', 'name', false, false);
         $var = ['nav' => 'data-induk', 'subNav' => 'user', 'title' => 'Edit Pengguna '.$data->name, 'data' => $data, 'user_acl' => $user_acl];
         return view('master.user.edit', $var);
      } else {
         $result = config('global.errors.E002');
      }

      return response()->json($result);
   }


   public function store(Request $request)
   {

      if (Helper::checkACL('master_user', 'c')) {
         // Validation
         $vMessage = config('global.vMessage'); //get global validation messages
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'username' => 'required|string|min:3|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'mobile' => 'required|digits_between:9,13',
            'password' => 'required|confirmed|min:8',
            'status' => 'required|boolean',
            'users_acls_id' => 'required|exists:users_acls,id',
            'sudo' => 'required|boolean',
         ], $vMessage);
         // Valid?
         $valid = Helper::validationFail($validator);
         if(!is_null($valid)){
            return response()->json($valid); //return if not valid
         }
         // Query creator
         try {
            DB::table('users')
               ->insert([
                  'name' => $request->name,
                  'username' => $request->username,
                  'email' => $request->email,
                  'mobile' => $request->mobile,
                  'password' => Hash::make($request->password),
                  'status' => $request->status,
                  'users_acls_id' => $request->users_acls_id,
                  'sudo' => $request->sudo,
                  'created_at' => Carbon::now(),
               ]);
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

      if (Helper::checkACL('master_user', 'u')) {
         // Validation
         $vMessage = config('global.vMessage'); //get global validation messages
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'username' => 'required|string|min:3|max:255|unique:users,username,'.$id,
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'mobile' => 'required|digits_between:9,13',
            'status' => 'required|boolean',
            'users_acls_id' => 'required|exists:users_acls,id',
            'sudo' => 'required|boolean',
         ], $vMessage);
         // Valid?
         $valid = Helper::validationFail($validator);
         if(!is_null($valid)){
            return response()->json($valid); //return if not valid
         }
         // Query Updater
         try {
            DB::table('users')
               ->where('id', $id)
               ->update([
                  'name' => $request->name,
                  'username' => $request->username,
                  'email' => $request->email,
                  'mobile' => $request->mobile,
                  'status' => $request->status,
                  'users_acls_id' => $request->users_acls_id,
                  'sudo' => $request->sudo,
                  'updated_at' => Carbon::now(),
               ]);
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
      if (Helper::checkACL('master_user', 'd')) {
         $id = $request->id;
         try {
            $user = DB::table('users')->where('id', $id);
            $status = $user->first()->status;
            $user->update(['status' => $status ? false : true]);
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
