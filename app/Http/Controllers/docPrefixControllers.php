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

class docPrefixControllers extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Helper::checkACL('master_docPrefix', 'r')) {
            // render index
            $var = ['nav' => 'data-induk', 'subNav' => 'docPrefix', 'title' => 'Dokumen Prefix'];
            return view('master.docPrefix.index', $var);
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
        //
        if (Helper::checkACL('master_docPrefix', 'r')) {
            if ($request->ajax()) {
                // query Satuanata
                $docPrefixs = DB::table('_docPrefix')
                    ->select(['docType', 'prefix']);

                return Datatables::of($docPrefixs)
                    ->addColumn('action', function ($docPrefix) {
                        // render column action
                        return view('master.docPrefix.action', [
                            'edit_url' => '/',
                            'show_url' => '/',
                            'prefix' => $docPrefix->prefix,
                        ]);
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        {
            if (Helper::checkACL('master_docPrefix', 'c')) {
                $var = ['nav' => 'data-induk', 'subNav' => 'docPrefix', 'title' => 'Tambah Dokumen Prefix'];
                return view('master.docPrefix.create', $var);
            } else {
                $result = config('global.errors.E002');
            }

            return response()->json($result);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Helper::checkACL('master_docPrefix', 'c')) {
            // Validation
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                'docType' => 'required|string|min:1|max:255|unique:_docPrefix,docType',
                'prefix' => 'required|string|min:1|max:255|unique:_docPrefix,prefix',
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query creator
            try {
                DB::table('_docPrefix')
                    ->insert([
                        'docType' => $request->docType,
                        'prefix' => $request->prefix,
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
    public function edit($prefix)
    {
        //
        if (Helper::checkACL('master_docPrefix', 'r')) {
            $data = DB::table('_docPrefix')
                ->select('prefix', 'docType')
                ->where('prefix', $prefix)->first();
            $var = ['nav' => 'data-induk', 'subNav' => 'unit', 'title' => 'Ubah Dokumen Prefix ' . $data->docType, 'data' => $data];
            return view('master.docPrefix.edit', $var);
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
    public function update(Request $request, $prefix)
    {
        //
        if (Helper::checkACL('master_docPrefix', 'u')) {
            // Validation
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                'docType' => 'required|string|min:3|max:255|unique:_docPrefix,docType,' . $prefix . ',prefix',
                'prefix' => 'required|string|min:1|max:255|unique:_docPrefix,prefix,' . $prefix . ',prefix',
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query Updater
            try {
                DB::table('_docPrefix')
                    ->where('prefix', $prefix)
                    ->update([
                        'docType' => $request->docType,
                        'prefix' => $request->prefix,
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
    public function destroy($id)
    {
        //
    }
}
