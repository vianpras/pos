<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class RequistionControllers extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Helper::checkACL('procurement_requistion', 'r')) {
            // render index
            $var = ['nav' => 'pengadaan', 'subNav' => 'permintaan', 'title' => 'Permintaan Pembelian'];
            return view('procurement.requistion.index', $var);
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
        if (Helper::checkACL('procurement_requistion', 'r')) {
            if ($request->ajax()) {
                // query data
                $requistions = DB::table('requistions')
                    ->leftJoin('projects', 'projects.code', '=', 'requistions.project_id')
                    // ->leftJoin('users as user_created', 'requistions.user_created', '=', 'user_created.id')
                    ->leftJoin('users', 'requistions.user_created', '=', 'users.id')
                    ->select([
                        'requistions.code',
                        'requistions.date_request',
                        'requistions.date_need',
                        'projects.code as project_code',
                        'requistions.status',
                        // 'user_created.name',
                        'users.name'
                    ]);

                return Datatables::of($requistions)
                    ->addColumn('action', function ($requistion) {
                        // render column action
                        return view('procurement.requistion.action', [
                            'edit_url' => route('requistion.edit', $requistion->code),
                            'show_url' => '/',
                            'id' => $requistion->code,
                            'status' => $requistion->status,
                        ]);
                    })
                    ->editColumn('status', function ($requistion) {
                        // render column status
                        $_status = Helper::statusBadge($requistion->status);
                        return $_status;
                    })

                    ->filter(function ($query) use ($request) {
                        if ($request->has('requistion_code_filter')) {
                            // default column filter
                            $query->where('requistions.code', 'like', "%{$request->requistion_code_filter}%");
                        }

                        // if ($request->has('requistion_code_project_filter')) {
                        //     // default column filter
                        //     $query->where('projects.code', 'like', "%{$request->requistion_code_project_filter}%");
                        // }

                        if ($request->has('requistion_status_filter')) {
                            if (($request->requistion_status_filter) == '-1') {
                                // default column filter
                                // $query->where('requistions.status', "like", 3);
                            } else {
                                // filtered column
                                $query->where('requistions.status', 'like', "%" . $request->requistion_status_filter . "%");
                            }
                        }
                        if ($request->has('requistion_date_filter')) {
                            if (($request->requistion_date_filter) == null) {
                                // default column filter 1 bulan
                                $query->where([
                                    ['requistions.created_at', '>=', Date('Y-m-d', strtotime("-6 months")) . ' 00:00:00'],
                                    ['requistions.created_at', '<=',  Date('Y-m-d') . ' 59:59:59'],
                                ]);
                            } else {
                                // filtered column
                                $dateSeparator = explode(" - ", $request->requistion_date_filter);
                                $query->where([
                                    ['requistions.created_at', '>=', $dateSeparator[0] . ' 00:00:00'],
                                    ['requistions.created_at', '<=', $dateSeparator[1] . ' 59:59:59'],
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
        //
        if (Helper::checkACL('procurement_requistion', 'c')) {
            // render index
            // dump(is_null(Helper::docPrefix('requistion')));
            if (is_null(Helper::docPrefix('requistions'))) {
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E012.status'),
                    "title" => config('global.errors.E012.code'),
                    "message" =>  config('global.errors.E012.message'),
                ]);
                return redirect()->back();
            }
            $project = Helper::forSelect('projects', 'code', 'code', false, false);
            $var = ['nav' => 'pengadaan', 'subNav' => 'permintaan', 'title' => 'Permintaan Pembelian Baru', 'project' => $project];
            return view('procurement.requistion.create', $var);
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());

        if (Helper::checkACL('procurement_requistion', 'c')) {
            // Validation
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|min:3|max:255|unique:requistions,code',
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query creator
            DB::beginTransaction();
            try {
                $code = Helper::docPrefix('requistions');
                $requistion = DB::table('requistions')
                    ->insertGetId([
                        'code' => $code,
                        'date_request' => Carbon::now(),
                        'date_need' => $request->date_need,
                        'project_id' => $request->project_id == 0 ? NULL : $request->project_id,
                        'status' => 'draft',
                        'description' => $request->note,
                        'created_at' => Carbon::now(),
                        'user_created' => Auth::id(),
                    ]);
                $requistionCode = DB::table('requistions')->where('code', $code)->sharedLock()->first()->code;
                foreach ($request->nama as $key => $value) {
                    $requistion_detail = DB::table('requistion_details')
                        ->insert([
                            'requistion_id' => $requistionCode,
                            'item_id' => $request->nama[$key],
                            'quantity' => $request->quantity[$key],
                            'description' => $request->description[$key],
                            'created_at' => Carbon::now(),
                        ]);
                }
                session()->flash('notifikasi', [
                    "icon" => config('global.success.S002.status'),
                    "title" => config('global.success.S002.code'),
                    "message" =>  config('global.success.S002.message'),
                ]);
                DB::commit();
                return redirect('/pengadaan/permintaan');
            } catch (\Throwable $e) {
                DB::rollback();
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E011.status'),
                    "title" => config('global.errors.E011.code'),
                    "message" =>  config('global.errors.E011.message') . ' ' . $e->getMessage(),
                ]);
                return redirect()->back();
            }
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

        if (Helper::checkACL('procurement_requistion', 'c')) {
            $project = Helper::forSelect('projects', 'code', 'code', false, false);
            try {
                $requistion = DB::table('requistions')
                    ->leftJoin('projects', 'projects.code', '=', 'requistions.project_id')
                    // ->leftJoin('users as user_created', 'requistions.user_created', '=', 'user_created.id')
                    ->select([
                        'requistions.code',
                        'requistions.date_request',
                        'requistions.date_need',
                        'requistions.project_id',
                        'projects.code as project_code',
                        'requistions.status',
                        'requistions.description as note',
                    ])
                    ->where('requistions.code', $code)
                    ->first();
                $requistion_detail = DB::table('requistion_details')
                    ->join('items', 'requistion_details.item_id', '=', 'items.id')
                    ->select([
                        'requistion_details.id',
                        'requistion_details.item_id',
                        'requistion_details.requistion_id',
                        'items.name as item_name',
                        'items.code as item_code',
                        'requistion_details.quantity',
                        'requistion_details.description',
                    ])
                    ->where('requistion_id', $requistion->code)
                    ->get();
                $var = [
                    'nav' => 'pengadaan',
                    'subNav' => 'permintaan',
                    'title' => 'Ubah Permintaan Pembelian ' . $requistion->code,
                    'project' => $project,
                    'requistion' => $requistion,
                    'requistion_detail' => $requistion_detail,
                ];
            } catch (\Throwable $e) {
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E011.status'),
                    "title" => config('global.errors.E011.code'),
                    "message" =>  config('global.errors.E011.message'),
                ]);
                return redirect('/pengadaan/permintaan');
            }
            return view('procurement.requistion.edit', $var);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $code)
    {
        if (Helper::checkACL('procurement_requistion', 'u')) {
            // Validation
            $vMessage = config('global.vMessage'); //get global validation messages
            $id = $code;
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|min:3|max:255|unique:requistions,code,' . $code . ',code'
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                session()->flash('notifikasi', [
                    "icon" => $valid->status,
                    "title" => $valid->code,
                    "message" => config('global.errors.E009.message'),
                ]);
                return redirect()->back();
            }
            // Query Updater
            DB::beginTransaction();
            try {
                $requistion = DB::table('requistions')
                    ->where([
                        ['requistions.code', $code],
                        // ['status','draft']
                    ])
                    ->update([
                        'date_need' => $request->date_need,
                        'project_id' => $request->project_id == 0 ? NULL : $request->project_id,
                        'description' => $request->note,
                        'updated_at' => Carbon::now(),
                    ]);
                if ($request->counting > 0) {
                    DB::table('requistion_details')->where('requistion_id', $code)->delete();
                    foreach ($request->nama as $key => $value) {
                        $requistion_detail = DB::table('requistion_details')
                            ->insert([
                                'requistion_id' =>  $code,
                                'item_id' => $request->nama[$key],
                                'quantity' => $request->quantity[$key],
                                'description' => $request->description[$key],
                                'updated_at' => Carbon::now(),
                            ]);
                    }
                }
                session()->flash('notifikasi', [
                    "icon" => config('global.success.S003.status'),
                    "title" => config('global.success.S003.code'),
                    "message" =>  config('global.success.S003.message'),
                ]);
                DB::commit();
                return redirect('/pengadaan/permintaan');
            } catch (\Throwable $e) {
                DB::rollback();
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E009.status'),
                    "title" => config('global.errors.E009.code'),
                    "message" =>  config('global.errors.E009.message') . '<br>' . $e->getMessage(),
                ]);
                return redirect()->back();
            }
        } else {
            // tidak memiliki otorisasi
            session()->flash('notifikasi', [
                "icon" => config('global.errors.E002.status'),
                "title" => config('global.errors.E002.code'),
                "message" =>  config('global.errors.E002.message'),
            ]);
            return redirect('/pengadaan/permintaan');
        }
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
    public function getItem(Request $request)
    {
        $items = DB::table('items')
            ->select('id', 'name', 'code')
            ->where('name', 'like', '%' . $request->data . '%')
            ->orWhere('code', 'like', '%' . $request->data . '%')
            ->limit(5)
            ->get();
        $list = array();
        if (count($items) > 0) {
            foreach ($items as $key => $item) {
                $list[$key]['id'] = $item->id;
                $list[$key]['text'] = $item->code . "  |  " . $item->name;
                $list[$key]['name'] = $item->name;
                $list[$key]['quantity'] = 1;
            }
        } else {
            $list[0]['id'] = 0;
            $list[0]['text'] = 'Item Tidak Ditemukan';
        }
        $result = response()->json($list);
        return $result;
    }
}
