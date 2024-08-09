<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class UnitControllers extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      //
      if (Helper::checkACL('master_unit', 'r')) {
         // render index
         $var = ['nav' => 'data-induk', 'subNav' => 'unit', 'title' => 'Satuan'];
         return view('master.unit.index', $var);
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

   /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function datatable(Request $request)
   {
      //
      if (Helper::checkACL('master_unit', 'r')) {
         if ($request->ajax()) {
            // query Satuanata
            $units = DB::table('units')
               ->select(['id','code', 'name', 'status']);

            return Datatables::of($units)
               ->addColumn('action', function ($unit) {
                  // render column action
                  return view('master.unit.action', [
                     'edit_url' => '/',
                     'show_url' => '/',
                     'id' => $unit->id,
                     'status' => $unit->status,
                  ]);
               })
               ->editColumn('status', function ($unit) {
                  // render column status
                  $_status = $unit->status == '1'
                     ? '<center><span class="right badge badge-success">Aktif</span></center>'
                     : '<center><span class="right badge badge-danger">Non-Aktif</span></center>';
                  return $_status;
               })

               ->filter(function ($query) use ($request) {
                  if ($request->has('unit_name_filter')) {
                     // default column filter
                     $query->where('name', 'like', "%{$request->unit_name_filter}%");
                  }

                  if ($request->has('unit_code_filter')) {
                     // default column filter
                     $query->where('code', 'like', "%{$request->unit_code_filter}%");
                  }

                  if ($request->has('unit_status_filter')) {
                     if (($request->unit_status_filter) == '-1') {
                        // default column filter
                        $query->where('status', "<=", 3);
                     } else {
                        // filtered column
                        $query->where('status', 'like', "%" . $request->unit_status_filter . "%");
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
      if (Helper::checkACL('master_unit', 'c')) {
         $var = ['nav' => 'data-induk', 'subNav' => 'unit', 'title' => 'Tambah Satuan'];
         return view('master.unit.create', $var);
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
      if (Helper::checkACL('master_unit', 'c')) {
         // Validation
         $vMessage = config('global.vMessage'); //get global validation messages
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255|unique:units,name',
            'code' => 'required|string|min:1|max:255|unique:units,code',
            'status' => 'required|boolean',
         ], $vMessage);
         // Valid?
         $valid = Helper::validationFail($validator);
         if (!is_null($valid)) {
            return response()->json($valid); //return if not valid
         }
         // Query creator
         try {
            DB::table('units')
               ->insert([
                  'name' => $request->name,
                  'code' => $request->code,
                  'status' => $request->status,
                  'created_at' => Carbon::now(),
                  'user_created' => Auth::id(),
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


   /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function edit($id)
   {
      if (Helper::checkACL('master_unit', 'r')) {
         $data = DB::table('units')
            ->select('id', 'name', 'code', 'status')
            ->where('id', $id)->first();
         $var = ['nav' => 'data-induk', 'subNav' => 'unit', 'title' => 'Ubah Satuan '.$data->name, 'data' => $data];
         return view('master.unit.edit', $var);
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
   public function update(Request $request, $id)
   {
      if (Helper::checkACL('master_unit', 'u')) {
         // Validation
         $vMessage = config('global.vMessage'); //get global validation messages
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255|unique:units,name,'.$id,
            'code' => 'required|string|min:1|max:255|unique:units,code,'.$id,
            'status' => 'required|boolean',
         ], $vMessage);
         // Valid?
         $valid = Helper::validationFail($validator);
         if(!is_null($valid)){
            return response()->json($valid); //return if not valid
         }
         // Query Updater
         try {
            DB::table('units')
               ->where('id', $id)
               ->update([
                  'name' => $request->name,
                  'code' => $request->code,
                  'status' => $request->status,
                  'updated_at' => Carbon::now(),
                  'user_updated' => Auth::id(),
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

   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function disable(Request $request)
   {
      if (Helper::checkACL('master_unit', 'd')) {
         $id = $request->id;
         try {
            $user = DB::table('units')->where('id', $id);
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
