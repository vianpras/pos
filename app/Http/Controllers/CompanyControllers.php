<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class CompanyControllers extends Controller
{
    public function __construct()
    {
        // set middleware
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Helper::checkACL('company', 'r')) {
            // render index
            try {
                //code...
                $companies = DB::table('companies')->where('id', 1)->first();
                is_null($companies) &&
                $companies = (object)[
                    'owner' => '',
                    'name' => '',
                    'address1' => '',
                    'address2' => '',
                    'address3' => '',
                    'phone' => '',
                    'mobile' => '',
                    'email' => '',
                    'whatsapp' => '',
                    'facebook' => '',
                    'instagram' => '',
                    'twitter' => '',
                    'website' => '',
                ];
                $config = DB::table('configurations')->where('id', 1)->first();
                is_null($config) &&
                    $config = (object) [
                        'id' => '',
                        'total_cart' => '',
                        'change_authorization' => '',
                        'set_inventory' => '',
                        'set_edit_authorization' => '',
                        'print_footer1' => '',
                        'print_footer2' => '',
                        'print_footer3' => '',
                    ];
            } catch (\Throwable $th) {
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E999.status'),
                    "title" => config('global.errors.E999.code'),
                    "message" => config('global.errors.E999.message'),
                ]);
            }
            // dump();

            $var = [
                'nav' => 'data-induk',
                'subNav' => 'company',
                'title' => 'Pengaturan Perusahaan & Aplikasi',
                'company' => $companies,
                'config' => $config
            ];
            return view('master.company.index', $var);
        } else {
            // tidak memiliki otorisasi
            session()->flash('notifikasi', [
                "icon" => config('global.errors.E002.status'),
                "title" => config('global.errors.E002.code'),
                "message" => config('global.errors.E002.message'),
            ]);
            return redirect('dashboard');
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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ((Helper::checkACL('company', 'c')) || (Helper::checkACL('company', 'u'))) {
            // dd($request->change_authorization);
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                'image' => ['image', 'mimes:jpeg,bmp,png', 'max:2048', 'required'],

            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            try {
                // companies
                $checkCompanies = DB::table('companies')->where('id', 1)->first();
                if (is_null($checkCompanies)) {
                    $insertCompanies = DB::table('companies')
                        ->insertGetId([
                            'id' => 1,
                            'owner' => $request->owner,
                            'name' => $request->name,
                            'address1' => $request->address1,
                            'address2' => $request->address2,
                            'address3' => $request->address3,
                            'phone' => $request->phone,
                            'mobile' => $request->mobile,
                            'email' => $request->email,
                            'whatsapp' => $request->whatsapp,
                            'facebook' => $request->facebook,
                            'instagram' => $request->instagram,
                            'twitter' => $request->twitter,
                            'website' => $request->website,
                             'created_at' => Carbon::now(),
                             'user_created' => Auth::id(),
                        ]);
                } else {

                    $updateCompanies = DB::table('companies')
                        ->where('id', 1)
                        ->update([
                            'owner' => $request->owner,
                            'name' => $request->name,
                            'address1' => $request->address1,
                            'address2' => $request->address2,
                            'address3' => $request->address3,
                            'phone' => $request->phone,
                            'mobile' => $request->mobile,
                            'email' => $request->email,
                            'whatsapp' => $request->whatsapp,
                            'facebook' => $request->facebook,
                            'instagram' => $request->instagram,
                            'twitter' => $request->twitter,
                            'website' => $request->website,
                            'updated_at' => Carbon::now(),
                             'user_updated' => Auth::id(),
                        ]);
                }

                // configurations
                $checkConfigurations = DB::table('configurations')->where('id', 1)->first();
                if (is_null($checkConfigurations)) {
                    $insertConfigurations = DB::table('configurations')
                        ->insertGetId([
                            'id' => 1,
                            'total_cart' => $request->total_cart,
                            'change_authorization' => is_null($request->change_authorization) ? 0 : 1,
                            'set_inventory' => is_null($request->set_inventory) ? 0 : 1,
                            'set_edit_authorization' => is_null($request->set_edit_authorization) ? 0 : 1,
                            'print_footer1' => $request->print_footer1,
                            'print_footer2' => $request->print_footer2,
                            'print_footer3' => $request->print_footer3,
                            'created_at' => Carbon::now(),
                            'user_created' => Auth::id(),
                        ]);
                } else {
                    $updateConfigurations = DB::table('configurations')
                        ->where('id', 1)
                        ->update([
                            'id' => 1,
                            'total_cart' => $request->total_cart,
                            'change_authorization' => is_null($request->change_authorization) ? 0 : 1,
                            'set_inventory' => is_null($request->set_inventory) ? 0 : 1,
                            'set_edit_authorization' => is_null($request->set_edit_authorization) ? 0 : 1,
                            'print_footer1' => $request->print_footer1,
                            'print_footer2' => $request->print_footer2,
                            'print_footer3' => $request->print_footer3,
                            'updated_at' => Carbon::now(),
                            'user_updated' => Auth::id(),
                        ]);
                }

                if ($request->hasFile('image')) {
                    $gambar = $request->file('image');
                    if ($request->file('image')->isValid()) {
                        $gambar->storeAs('/configurations', 1, 'private');
                    }
                }
                $result = config('global.success.S000');
            } catch (\Throwable $th) {
                // $result = config('global.errors.E999');
                $result = $th->getMessage();
            }
        } else {
            // tidak memiliki otorisasi
            $result = config('global.errors.E002');
        }
        return response()->json($result);

        //
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
    public function edit($id)
    {
        //
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
        //
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
