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

class COAControllers extends Controller
{
    function index() {
        if (Helper::checkACL('master_coa', 'c')) {
            $categories = DB::table('categories')->select(['id', 'name'])->whereNull('parent')->get();
            $var = ['nav' => 'data-induk', 'subNav' => 'coa', 'title' => 'Chart Of Account(COA)'];
            return view('master.coa.index', $var);
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }

    public function datatable(Request $request)
    {
        //
        if (Helper::checkACL('master_coa', 'r')) {
            if ($request->ajax()) {
                // query COA
                $coas = DB::table('chart_of_accounts')
                    ->select(['id', 'code_account_default', 'name', 'group_of_account'])
                    ->where('type_of_account', 'header');

                return Datatables::of($coas)
                    ->addColumn('action', function ($coas) {
                        // render column action
                        return view('master.coa.action', [
                            'edit_url' => '/',
                            'show_url' => '/',
                            'id' => $coas->id,
                        ]);
                    })
                    ->filter(function ($query) use ($request) {
                        if ($request->has('coa_code_filter')) {
                            // default column filter
                            $query->where('code_account_default', 'like', "%{$request->coa_code_filter}%");
                        }

                        if ($request->has('name')) {
                            // default column filter
                            $query->where('name', 'like', "%{$request->coa_name_filter}%");
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
        if (Helper::checkACL('master_coa', 'c')) {
            $parentCode = DB::table('chart_of_accounts')->select(['id', 'code_account_default', 'name'])->where('type_of_account', 'header')->get();
            $var = ['nav' => 'data-induk', 'subNav' => 'coa', 'title' => 'Tambah COA', 'parentCode' => $parentCode];
            return view('master.coa.create', $var);
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }
    
    public function store(Request $request)
    {
        if (Helper::checkACL('master_coa', 'c')) {
            // Validation
            // dd($request->all());
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                'code_account_default'  => 'required',
                'name'                  => 'required',
                'group_of_account'      => 'required',
                'type_of_account'       => 'required',
                'type_of_business'      => 'required',

            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query creator
            try {
                $parent_id = null;
                $parent_code = null;
                $account_code = null;
                if($request->code_parent != "null"){
                    $get = DB::table('chart_of_accounts')->where('code_account_default', $request->code_parent)->first();
                    $parent_id = $get->id;
                    $parent_code = $request->code_parent;
                    
                    $explodeCode = explode(".",$parent_code);
                    $account_code = $explodeCode[0].".".$request->code_account_default;
                }
                $coa = DB::table('chart_of_accounts')
                    ->insert([
                        'chart_of_account_id' => $parent_id,
                        'code_account_default' => $account_code,
                        'code_parent' => $parent_code,
                        'code_account_alias' => $account_code,
                        'is_coa_alias' => 0,
                        'name' => $request->name,
                        'group_of_account' => $request->group_of_account,
                        'type_of_account' => $request->type_of_account,
                        'type_of_business' => $request->type_of_business,
                        'description' => $request->description,
                        'user_created' => Auth::id(),
                        'user_updated' => null,
                        'created_at' => Carbon::now(),
                    ]);
                $result = config('global.success.S002');
            } catch (\Throwable $e) {
                // $result = $e->getMessage();
                $result = config('global.errors.E010');
            }
        } else {
            $result = config('global.errors.E002');
        }

        return response()->json($result);
    }    
}
