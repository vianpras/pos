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

class SalesCategoryControllers extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       //
       if (Helper::checkACL('master_sales_category', 'r')) {
          // render index
          $var = ['nav' => 'data-induk', 'subNav' => 'master_sales_category', 'title' => 'Kategori Penjualan'];
          return view('master.sales_category.index', $var);
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
       if (Helper::checkACL('master_sales_category', 'r')) {
          if ($request->ajax()) {
             // query Satuanata
             $salesCategries = DB::table('sales_categories')
                ->select(['id','mark_up', 'name', 'status'])
                ->orderBy('id','asc');
 
             return Datatables::of($salesCategries)
                ->addColumn('action', function ($salesCategory) {
                   // render column action
                   return view('master.sales_category.action', [
                      'edit_url' => '/',
                      'show_url' => '/',
                      'id' => $salesCategory->id,
                      'status' => $salesCategory->status,
                   ]);
                })
                ->editColumn('mark_up', function ($salesCategory) {
                    // render column buy_price
                    $buy_price = $salesCategory->mark_up.' <span class="text-teal">(%)</span>';
                    return $buy_price;
                })
                ->editColumn('status', function ($salesCategory) {
                   // render column status
                   $_status = $salesCategory->status == '1'
                      ? '<center><span class="right badge badge-success">Aktif</span></center>'
                      : '<center><span class="right badge badge-danger">Non-Aktif</span></center>';
                   return $_status;
                })
 
                ->filter(function ($query) use ($request) {
                   if ($request->has('salesCategory_name_filter')) {
                      // default column filter
                      $query->where('name', 'like', "%{$request->salesCategory_name_filter}%");
                   }
 
                   if ($request->has('salesCategory_mark_up_filter')) {
                      // default column filter
                      $query->where('mark_up', 'like', "%{$request->salesCategory_mark_up_filter}%");
                   }
 
                   if ($request->has('salesCategory_status_filter')) {
                      if (($request->salesCategory_status_filter) == '-1') {
                         // default column filter
                         $query->where('status', "<=", 3);
                      } else {
                         // filtered column
                         $query->where('status', 'like', "%" . $request->salesCategory_status_filter . "%");
                      }
                   }
                   if ($request->has('salesCategory_date_filter')) {
                      if (($request->salesCategory_date_filter) == null) {
                         // default column filter 1 bulan
                         $query->where([
                            ['created_at', '>=', Date('Y-m-d', strtotime("-6 months")) . ' 00:00:00'],
                            ['created_at', '<=',  Date('Y-m-d') . ' 59:59:59'],
                         ]);
                      } else {
                         // filtered column
                         $dateSeparator = explode(" - ", $request->salesCategory_date_filter);
                         $query->where([
                            ['created_at', '>=', $dateSeparator[0] . ' 00:00:00'],
                            ['created_at', '<=', $dateSeparator[1] . ' 59:59:59'],
                         ]);
                      }
                   }
                })
                ->rawColumns(['action', 'status','mark_up']) //render raw custom column 
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
       if (Helper::checkACL('master_sales_category', 'c')) {
          $var = ['nav' => 'data-induk', 'subNav' => 'master_sales_category', 'title' => 'Tambah Kategori'];
          return view('master.sales_category.create', $var);
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
       if (Helper::checkACL('master_sales_category', 'c')) {
          // Validation
          $vMessage = config('global.vMessage'); //get global validation messages
          $validator = Validator::make($request->all(), [
             'name' => 'required|string|min:3|max:255|unique:sales_categories,name',
             'mark_up' => 'required|string|min:1|max:255',
             'status' => 'required|boolean',
          ], $vMessage);
          // Valid?
          $valid = Helper::validationFail($validator);
          if (!is_null($valid)) {
             return response()->json($valid); //return if not valid
          }
          // Query creator
          try {
             DB::table('sales_categories')
                ->insert([
                   'name' => $request->name,
                   'mark_up' => $request->mark_up,
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
       if (Helper::checkACL('master_sales_category', 'r')) {
          $data = DB::table('sales_categories')
             ->select('id', 'name', 'mark_up', 'status')
             ->where('id', $id)->first();
          $var = ['nav' => 'data-induk', 'subNav' => 'master_sales_category', 'title' => 'Ubah Kategori '.$data->name, 'data' => $data];
          return view('master.sales_category.edit', $var);
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
       if (Helper::checkACL('master_sales_category', 'u')) {
          // Validation
          $vMessage = config('global.vMessage'); //get global validation messages
          $validator = Validator::make($request->all(), [
             'name' => 'required|string|min:3|max:255|unique:sales_categories,name,'.$id,
             'mark_up' => 'required|string|min:1|max:255',
             'status' => 'required|boolean',
          ], $vMessage);
          // Valid?
          $valid = Helper::validationFail($validator);
          if(!is_null($valid)){
             return response()->json($valid); //return if not valid
          }
          // Query Updater
          try {
             DB::table('sales_categories')
                ->where('id', $id)
                ->update([
                   'name' => $request->name,
                   'mark_up' => $request->mark_up,
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
       if (Helper::checkACL('master_sales_category', 'd')) {
          $id = $request->id;
          try {
             $user = DB::table('sales_categories')->where('id', $id);
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
 
