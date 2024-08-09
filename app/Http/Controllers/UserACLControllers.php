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

class UserACLControllers extends Controller
{
   public function __construct()
   {
      // set middleware
      $this->middleware('auth');
   }
   public function index(Request $request)
   {
      if (Helper::checkACL('master_acl', 'r')) {
         // render index
         $var = ['nav' => 'data-induk', 'subNav' => 'hakakses', 'title' => 'Hak Akses'];
         return view('master.acl.index', $var);
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
      if (Helper::checkACL('master_acl', 'c')) {
         if ($request->ajax()) {
            // query data
            $acls = DB::table('users_acls')
               ->select(['id', 'name']);

            return Datatables::of($acls)
               ->addColumn('action', function ($acl) {
                  // render column action
                  return view('master.acl.action', [
                     'edit_url' => '/',
                     'show_url' => '/',
                     'id' => $acl->id,
                  ]);
               })

               ->filter(function ($query) use ($request) {
                  $query->where([
                     ['id', '!=', Auth::user()->users_acls_id], //tidak menampilkan acl yg dia pakai
                     ['id', '!=', 0] //tidak menampilkan sudo acl
                  ]);
                  if ($request->has('acl_name_filter')) {
                     // default column filter
                     $query->where('name', 'like', "%{$request->acl_name_filter}%");
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
      if (Helper::checkACL('master_acl', 'c')) {
         $data = DB::table('users_acls')->first();
         
         foreach ($data as $key => $_data) {
            $_columns[] = $key;
         }
         $columns = array_splice($_columns, 2);
         $var = ['nav' => 'data-induk', 'subNav' => 'hakakses', 'title' => 'Tambah Hak Akses', 'columns' => $columns];
         return view('master.acl.create', $var);
      } else {
         $result = config('global.errors.E002');
      }

      return response()->json($result);
   }

   public function edit(Request $request, $id)
   {
      if (Helper::checkACL('master_acl', 'r')) {
         $data = DB::table('users_acls')
            ->where('id', $id)->first();
         foreach ($data as $key => $_data) {
            $_columns[] = $key;
         }
         $permissions = ['c', 'r', 'u', 'd', 'i', 'e',];
         $columns = array_splice($_columns, 2);

         $var = ['nav' => 'data-induk', 'subNav' => 'hakakses', 'title' => 'Edit Hak Akses '.$data->name, 'data' => $data, 'columns' => $columns];
         return view('master.acl.edit', $var);
      } else {
         $result = config('global.errors.E002');
      }

      return response()->json($result);
   }


   public function store(Request $request)
   {

      if (Helper::checkACL('master_acl', 'c')) {
         $vMessage = config('global.vMessage'); //get global validation messages

         // generate & grouping validation
         $data = DB::table('users_acls')->first();
         $permissions = ['c', 'r', 'u', 'd', 'i', 'e',];
         foreach ($data as $key => $_data) {
            $_columns[] = $key;
         }
         $columns = array_splice($_columns, 2);
         foreach ($columns as $key => $_column) {
            $_request[$_column] = "";

            foreach ($permissions as $permission) {
               $__request = $_column . '_' . $permission;
               $_valueColumn = $request->input($__request) ? substr($__request, -1) : "";
               $column[] = $_column . '_' . $permission;
               $columnValidation[$__request] = 'boolean';
               $columnValidationM[$__request . '.boolean'] = 'Inputan tidak Sesuai';
               $_request[$_column] .= $_valueColumn;
            }
         }
         // make validation
         $validator = Validator::make($request->only($column), $columnValidation, $columnValidationM);
         // Valid?
         $valid = Helper::validationFail($validator);
         if (!is_null($valid)) {
            return response()->json($valid); //return if not valid
         }
         // Query creator
         try {
            $data = array_merge($_request, ['name' => $request->_name]);
            DB::table('users_acls')
               ->insert($data);
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

      if (Helper::checkACL('master_acl', 'u')) {
         $vMessage = config('global.vMessage'); //get global validation messages

         // generate & grouping validation
         $data = DB::table('users_acls')->first();
         $permissions = ['c', 'r', 'u', 'd', 'i', 'e',];
         foreach ($data as $key => $_data) {
            $_columns[] = $key;
         }
         $columns = array_splice($_columns, 2);
         foreach ($columns as $key => $_column) {
            $_request[$_column] = "";

            foreach ($permissions as $permission) {
               $__request = $_column . '_' . $permission;
               $_valueColumn = $request->input($__request) ? substr($__request, -1) : "";
               $column[] = $_column . '_' . $permission;
               $columnValidation[$__request] = 'boolean';
               $columnValidationM[$__request . '.boolean'] = 'Inputan tidak Sesuai';
               $_request[$_column] .= $_valueColumn;
            }
         }
         // make validation
         $validator = Validator::make($request->only($column), $columnValidation, $columnValidationM);
         // Valid?
         $valid = Helper::validationFail($validator);
         if (!is_null($valid)) {
            return response()->json($valid); //return if not valid
         }
         // Query creator
         try {
            $data = array_merge($_request, ['name' => $request->_name]);
            DB::table('users_acls')
               ->where('id',$id)
               ->update($data);
            $result = config('global.success.S002');
         } catch (\Throwable $e) {
            dd($e->getMessage());
            $result = config('global.errors.E010');
         }
      } else {
         $result = config('global.errors.E002');
      }

      return response()->json($result);
   }

   public function disable(Request $request)
   {
      // disable data
      if (Helper::checkACL('master_acl', 'd')) {
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
