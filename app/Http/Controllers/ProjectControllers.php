<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ProjectControllers extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Helper::checkACL('project', 'r')) {
            // render index
            $var = ['nav' => 'proyek', 'subNav' => 'proyek', 'title' => 'Managemen Proyek'];
            return view('project.index', $var);
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
        if (Helper::checkACL('project', 'r')) {
            if ($request->ajax()) {
                // query data
                $projects = DB::table('projects')
                    ->leftJoin('users', 'users.id', '=', 'projects.user_created')
                    ->select([
                        'projects.code',
                        'projects.start_project',
                        'projects.end_project',
                        'users.name',
                        'projects.status',
                    ]);

                return Datatables::of($projects)
                    ->addColumn('action', function ($project) {
                        // render column action
                        return view('project.action', [
                            'edit_url' => '/',
                            'show_url' => '/',
                            'code' => $project->code,
                            'status' => $project->status,
                        ]);
                    })
                    ->editColumn('status', function ($project) {
                        // render column status
                        $_status = Helper::statusBadge($project->status);
                        return $_status;
                    })

                    // ->filter(function ($query) use ($request) {
                    //     if ($request->has('item_name_filter')) {
                    //         // default column filter
                    //         $query->where('items.name', 'like', "%{$request->item_name_filter}%");
                    //     }

                    //     if ($request->has('item_code_filter')) {
                    //         // default column filter
                    //         $query->where('items.code', 'like', "%{$request->item_code_filter}%");
                    //     }
                    //     // dd($request->item_category_filter);
                    //     if ($request->has('item_category_filter')) {
                    //         // default column filter
                    //         if (($request->item_category_filter) == '-1') {
                    //             // default column filter
                    //             $query->where('items.category_id', ">=", 0);
                    //         } else {
                    //             // filtered column
                    //             $query->where('items.category_id', '=', $request->item_category_filter);
                    //         }
                    //     }
                    //     if ($request->has('item_status_filter')) {
                    //         if (($request->item_status_filter) == '-1') {
                    //             // default column filter
                    //             $query->where('items.status', "<=", 3);
                    //         } else {
                    //             // filtered column
                    //             $query->where('items.status', 'like', "%" . $request->item_status_filter . "%");
                    //         }
                    //     }
                    //     if ($request->has('item_date_filter')) {
                    //         if (($request->item_date_filter) == null) {
                    //             // default column filter 1 bulan
                    //             $query->where([
                    //                 ['items.created_at', '>=', Date('Y-m-d', strtotime("-6 months")) . ' 00:00:00'],
                    //                 ['items.created_at', '<=',  Date('Y-m-d') . ' 59:59:59'],
                    //             ]);
                    //         } else {
                    //             // filtered column
                    //             $dateSeparator = explode(" - ", $request->item_date_filter);
                    //             $query->where([
                    //                 ['items.created_at', '>=', $dateSeparator[0] . ' 00:00:00'],
                    //                 ['items.created_at', '<=', $dateSeparator[1] . ' 59:59:59'],
                    //             ]);
                    //         }
                    //     }
                    // })
                    ->rawColumns(['action', 'status',]) //render raw custom column 
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
        if (Helper::checkACL('project', 'c')) {
            if (is_null(Helper::docPrefix('projects'))) {
                $result = config('global.errors.E012');
                return response()->json($result);
            }
            $var = ['nav' => 'proyek', 'subNav' => 'proyek', 'title' => 'Tambah Proyek'];
            return view('project.create', $var);
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
        if (Helper::checkACL('project', 'c')) {
            // Validation
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|min:1|max:255|unique:units,code',
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query creator
            DB::beginTransaction();
            try {
                DB::table('projects')
                    ->insert([
                        'code' =>  Helper::docPrefix('projects'),
                        'description' => $request->description,
                        'status' => 'open',
                        'start_project' => $request->start_project,
                        'end_project' => $request->end_project,
                        'user_created' => Auth::id(),
                        'created_at' => Carbon::now(),
                    ]);
                $result = config('global.success.S002');
            } catch (\Throwable $e) {
                DB::rollback();
                $result = config('global.errors.E010');
            }
        } else {
            $result = config('global.errors.E002');
        }
        DB::commit();
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
        //
        if (Helper::checkACL('project', 'r')) {
            try {
                $data = DB::table('projects')
                    ->select('code', 'status', 'start_project', 'end_project', 'description')
                    ->where([
                        ['code', $code],
                        // ['status', 'open']
                    ])
                    ->first();
                // if (is_null($data)) {
                //     $result = config('global.errors.E009');
                //     return response()->json($result,404);
                // }
                $var = ['nav' => 'proyek', 'subNav' => 'proyek', 'title' => 'Ubah Proyek ' . $data->code, 'data' => $data];
            } catch (\Throwable $th) {
                $result = config('global.errors.E999');
                return response()->json($result, 500);
            }
            return view('project.edit', $var);
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
        if (Helper::checkACL('project', 'u')) {
            // Validation
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|min:1|max:255|unique:units,code,' . $code,
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query Updater
            DB::beginTransaction();
            try {
                $data = DB::table('projects')
                    ->where([
                        ['code', $code],
                        ['status', 'open'],
                    ])
                    ->update([
                        'description' => $request->description,
                        'status' => $request->status,
                        'start_project' => $request->start_project,
                        'end_project' => $request->end_project,
                        'user_updated' => Auth::id(),
                        'updated_at' => Carbon::now(),
                    ]);

                if (!$data) {
                    DB::rollback();
                    $result = config('global.errors.E009');
                    return response()->json($result, 404);
                }
                $var = ['nav' => 'proyek', 'subNav' => 'proyek', 'data' => $data];
                $result = config('global.success.S003');
            } catch (\Throwable $th) {
                DB::rollback();
                $result = config('global.errors.E999');
                return response()->json($result, 500);
            }
        } else {
            $result = config('global.errors.E002');
        }
        DB::commit();
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
