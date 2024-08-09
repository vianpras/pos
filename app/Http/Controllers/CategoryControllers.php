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


class CategoryControllers extends Controller
{
    public function index()
    {
        //
        if (Helper::checkACL('master_category', 'r')) {
            // render index
            $var = ['nav' => 'data-induk', 'subNav' => 'category', 'title' => 'Kategori'];
            return view('master.category.index', $var);
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
        // dd($request->category_date_filter);
        if (Helper::checkACL('master_category', 'r')) {
            if ($request->ajax()) {
                // query Kategori
                $categories = DB::table('categories')
                    ->select(['id', 'code', 'name']);
                return Datatables::of($categories)
                    ->addColumn('action', function ($category) {
                        // render column action
                        return view('master.category.action', [
                            'edit_url' => '/',
                            'show_url' => '/',
                            'id' => $category->id,
                        ]);
                    })
                    ->filter(function ($query) use ($request) {
                        if ($request->has('category_name_filter') || $request->category_name_filter != '') {
                            // default column filter
                            $query->where('name', 'like', "%{$request->category_name_filter}%");
                        }

                        if ($request->has('category_code_filter') || $request->category_code_filter != '') {
                            // default column filter
                            $query->where('code', 'like', "%{$request->category_code_filter}%");
                        }

                        if ($request->category_date_filter != '' OR $request->category_date_filter != null) {

                            $dateSeparator = explode(" - ", $request->category_date_filter);
                            $query->where([
                                ['created_at', '>=', $dateSeparator[0] . ' 00:00:00'],
                                ['created_at', '<=', $dateSeparator[1] . ' 59:59:59'],
                            ]);
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
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Helper::checkACL('master_category', 'c')) {
            $categories = DB::table('categories')->select(['id', 'name'])->whereNull('parent')->get();
            $var = ['nav' => 'data-induk', 'subNav' => 'category', 'title' => 'Tambah Kategori', 'categories' => $categories];
            return view('master.category.create', $var);
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
        if (Helper::checkACL('master_category', 'c')) {
            // Validation
            // dd($request->all());
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:255',
                'code' => 'required|string|min:1|max:255|unique:categories,code',
                'parent' => 'required',
                // 'image' => ['image', 'mimes:jpeg,bmp,png', 'max:2048', 'required'],

            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query creator
            try {
                $category = DB::table('categories')
                    ->where('id', 1)
                    ->insertGetId([
                        'code' => $request->code,
                        'name' => $request->name,
                        'description' => $request->description,
                        'parent' => $request->parent == 'null' ? null : $request->parent,
                        'user_created' => Auth::id(),
                        'user_updated' => null,
                        'created_at' => Carbon::now(),
                    ]);
                // if ($request->hasFile('image')) {
                //     $gambar = $request->file('image');
                //     if ($request->file('image')->isValid()) {
                //         $gambar->storeAs('/categories', $category, 'private');
                //     }
                // }
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


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Helper::checkACL('master_category', 'r')) {
            $categories = DB::table('categories')->select(['id', 'name', 'code'])->where('id', '!=', $id)->get();
            $data = DB::table('categories')
                ->select('id', 'name', 'code', 'description', 'parent', 'as_parent')
                ->where('id', $id)->first();
            $var = ['nav' => 'data-induk', 'subNav' => 'category', 'title' => 'Ubah Kategori ' . $data->name, 'data' => $data, 'categories' => $categories];
            return view('master.category.edit', $var);
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

        if (Helper::checkACL('master_category', 'u')) {
            // Validation
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:255',
                'code' => 'required|string|min:1|max:255|unique:categories,code,' . $id,
                // 'image' => ['image', 'mimes:jpeg,bmp,png', 'max:2048', 'required'],

            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                return response()->json($valid); //return if not valid
            }
            // Query Updater
            try {
                DB::table('categories')
                    ->where('id', $id)
                    ->update([
                        'code' => $request->code,
                        'name' => $request->name,
                        'description' => $request->description,
                        'parent' => $request->parent == 'null' ? null : $request->parent,
                        'user_updated' => Auth::id(),
                        'updated_at' => Carbon::now(),
                    ]);
                // if ($request->hasFile('image')) {
                //     $gambar = $request->file('image');
                //     if ($request->file('image')->isValid()) {
                //         $gambar->storeAs('/categories', $id, 'private');
                //     }
                // }
                $result = config('global.success.S003');
            } catch (\Throwable $e) {
                $result = config('global.errors.E009');
                $result = $e->getMessage();
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
        if (Helper::checkACL('master_category', 'd')) {
            $id = $request->id;
            try {
                $user = DB::table('categories')->where('id', $id);
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
