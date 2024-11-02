<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class CartControllers extends Controller
{
    public function index()
    {
        $var = ['nav' => 'data-cart', 'subNav' => 'cart', 'title' => 'Cart Transaksi'];
        return view('cart.index', $var);
    }

    public function datatable(Request $request){
        if($request->ajax()){
            $carts = DB::table('sales_cart')
                    ->leftJoin('users', 'sales_cart.salesid','=','users.id')
                    ->leftJoin('SAPOCRD', 'sales_cart.bussiness_partner','=','SAPOCRD.cardcode')
                    ->select('sales_cart.*', 'SAPOCRD.cardname', 'users.full_name');
            return Datatables::of($carts)
                ->addColumn('action', function ($cart) {
                    // render column action
                    return view('cart.action', [
                        'edit_url' => '/',
                        'show_url' => '/',
                        'docnum' => $cart->docnum,
                        'stage' => $cart->stage,
                    ]);
                })
                ->rawColumns(['action']) //render raw custom column 
                ->make(true);
        }
    }

    public function create()
    {
        $pricelist  = DB::table('SAPOPLN')->get();
        $customer   = DB::table('SAPOCRD')->select('SAPOCRD.*', DB::raw('RIGHT(SAPOCRD.phone, 4) AS phoneCode'))->get();
        $cart       = DB::table('sales_cart')->where('salesid', Auth::user()->id)->where('store_code', Auth::user()->site)->where('stage', '0')->first();
        if($cart){
            $cartDetails = DB::table('sales_cart_details')
                            ->leftJoin('SAPOPLN','sales_cart_details.pricelist_id','=','SAPOPLN.listnum')
                            ->where('sales_cart_details.cart_id', $cart->docnum)->get();
        }

        $var = [
            'nav'       => 'salesCart',
            'subNav'    => 'cart',
            'title'     => 'Tambah Data',
            'pricelist' => $pricelist,
            'customer'  => $customer,
            'cart'      => $cart,
            'cart_details' => ($cart) ? $cartDetails : ""
        ];

        return view('cart.create', $var);
    }

    public function store(Request $request){
        $docnum = $request->docnum;
        $sales = $request->sales;
        $custcode = $request->custcode;
        $store = Auth::user()->site;

        DB::beginTransaction();
        try {
            if($docnum != 0){
                $dataCartSales =  DB::table('sales_cart')->where('docnum', $docnum)->where('stage', '1')->first();
                if($dataCartSales){
                    DB::table('sales_cart_details')->insert([
                        'cart_id'   => $docnum,
                        'itemcode'  => $request->itemcode,
                        'itemname'  => $request->itemname,
                        'qty'       => $request->qty,
                        'pricelist_id'  => $request->pricelist,
                        'price'     => $request->price,
                        'subtotal'  => $request->subtotal
                    ]);
    
                    $grandtotal = DB::table('sales_cart_details')->where('cart_id', $docnum)->sum('subtotal');
                    DB::table('sales_cart')->where('docnum', $docnum)->update([
                        'grandtotal'  => $grandtotal
                    ]);
                } else {
                    $dataStage0 = DB::table('sales_cart')->where('store_code', $store)->where('salesid', $sales)->where('bussiness_partner', $custcode)->where('stage', '0')->first();
                    if($dataStage0){
                        DB::table('sales_cart_details')->insert([
                            'cart_id'   => $docnum,
                            'itemcode'  => $request->itemcode,
                            'itemname'  => $request->itemname,
                            'qty'       => $request->qty,
                            'pricelist_id'  => $request->pricelist,
                            'price'     => $request->price,
                            'subtotal'  => $request->subtotal
                        ]);
        
                        $grandtotal = DB::table('sales_cart_details')->where('cart_id', $docnum)->sum('subtotal');
                        DB::table('sales_cart')->where('docnum', $docnum)->update([
                            'grandtotal'  => $grandtotal
                        ]);
                    } else {
                        $get_last_docnum = DB::table('sales_cart')->whereYear('cart_date', date('Y'))->whereMonth('cart_date', date('m'))->where('store_code', Auth::user()->site)->orderBy('created_at', 'DESC')->first();
        
                        if($get_last_docnum != ""){
                            $get_nomor    = substr($get_last_docnum->docnum, -4);
                            $count_number = $get_nomor + 1;
        
                            $new_number = sprintf("%04d", $count_number);
                        } else {
                            $new_number = '0001';
                        }
        
                        $docnum = Auth::user()->site.'-'.date('my').''.$new_number;
        
                        DB::table('sales_cart')->insert([
                            'docnum'        => $docnum,
                            'store_code'    => $store,
                            'salesid'       => $sales,
                            'bussiness_pertner' => $custcode,
                            'grandtotal'    => 0,
                            'cart_date'     => date('Y-m-d'),
                            'created_by'    => Auth::user()->id,
                            'stage'         => $request->stage,
                            'created_at'    => date('Y-m-d H:i:s')
                        ]);
        
                        DB::table('sales_cart_details')->insert([
                            'cart_id'   => $docnum,
                            'itemcode'  => $request->itemcode,
                            'itemname'  => $request->itemname,
                            'qty'       => $request->qty,
                            'pricelist_id'  => $request->pricelist,
                            'price'     => $request->price,
                            'subtotal'  => $request->subtotal
                        ]);
        
                        $grandtotal = DB::table('sales_cart_details')->where('cart_id', $docnum)->sum('subtotal');
                        DB::table('sales_cart')->where('docnum', $docnum)->update([
                            'grandtotal'  => $grandtotal
                        ]);
                    }
                }
            } else {
                $get_last_docnum = DB::table('sales_cart')->whereYear('cart_date', date('Y'))->whereMonth('cart_date', date('m'))->where('store_code', Auth::user()->site)->orderBy('created_at', 'DESC')->first();
    
                if($get_last_docnum != ""){
                    $get_nomor    = substr($get_last_docnum->docnum, -4);
                    $count_number = $get_nomor + 1;
    
                    $new_number = sprintf("%04d", $count_number);
                } else {
                    $new_number = '0001';
                }
    
                $docnum = Auth::user()->site.'-'.date('my').''.$new_number;
    
                DB::table('sales_cart')->insert([
                    'docnum'        => $docnum,
                    'store_code'    => $store,
                    'salesid'       => $sales,
                    'bussiness_partner' => $custcode,
                    'grandtotal'    => 0,
                    'cart_date'     => date('Y-m-d'),
                    'created_by'    => Auth::user()->id,
                    'stage'         => $request->stage,
                    'created_at'    => date('Y-m-d H:i:s')
                ]);
    
                DB::table('sales_cart_details')->insert([
                    'cart_id'   => $docnum,
                    'itemcode'  => $request->itemcode,
                    'itemname'  => $request->itemname,
                    'qty'       => $request->qty,
                    'pricelist_id'  => $request->pricelist,
                    'price'     => $request->price,
                    'subtotal'  => $request->subtotal
                ]);
    
                $grandtotal = DB::table('sales_cart_details')->where('cart_id', $docnum)->sum('subtotal');
                DB::table('sales_cart')->where('docnum', $docnum)->update([
                    'grandtotal'  => $grandtotal
                ]);
            }
            DB::commit();
    
            $response = [
                "status"    => "success",
                "message"   => "Data berhasil disimpan",
                "docnum"    => $docnum
            ];            
        } catch (\Throwable $th) {
            DB::rollBack();

            $response = [
                "status"    => "gagal",
                "message"   => "Data gagal disimpan ".$th->getMessage(),
                "docnum"    => 0
            ];
        }

        return response()->json($response);
    }

    public function edit($docnum){
        $pricelist  = DB::table('SAPOPLN')->get();
        $customer   = DB::table('SAPOCRD')->select('SAPOCRD.*', DB::raw('RIGHT(SAPOCRD.phone, 4) AS phoneCode'))->get();
        $cart       = DB::table('sales_cart')->where('docnum', $docnum)->first();
        if($cart){
            $cartDetails = DB::table('sales_cart_details')
                            ->leftJoin('SAPOPLN','sales_cart_details.pricelist_id','=','SAPOPLN.listnum')
                            ->where('sales_cart_details.cart_id', $docnum)->get();
        }

        $var = [
            'nav'       => 'salesCart',
            'subNav'    => 'cart',
            'title'     => 'Edit Data',
            'pricelist' => $pricelist,
            'customer'  => $customer,
            'cart'      => $cart,
            'cart_details' => ($cart) ? $cartDetails : ""
        ];

        return view('cart.edit', $var);
    }

    public function update(Request $request){
        $docnum = $request->docnum;
        $sales = $request->sales;
        $custcode = $request->custcode;
        $store = Auth::user()->site;

        DB::beginTransaction();
        try {
            DB::table('sales_cart_details')->insert([
                'cart_id'   => $docnum,
                'itemcode'  => $request->itemcode,
                'itemname'  => $request->itemname,
                'qty'       => $request->qty,
                'pricelist_id'  => $request->pricelist,
                'price'     => $request->price,
                'subtotal'  => $request->subtotal
            ]);

            $grandtotal = DB::table('sales_cart_details')->where('cart_id', $docnum)->sum('subtotal');
            DB::table('sales_cart')->where('docnum', $docnum)->update([
                'grandtotal'  => $grandtotal
            ]);

            DB::commit();
    
            $response = [
                "status"    => "success",
                "message"   => "Data berhasil diupdate",
                "docnum"    => $docnum
            ];            
        } catch (\Throwable $th) {
            DB::rollBack();

            $response = [
                "status"    => "gagal",
                "message"   => "Data gagal diupdate ".$th->getMessage(),
                "docnum"    => 0
            ];
        }

        return response()->json($response);        
    }

    public function delete(Request $request){
        $docnum = $request->docnum;
        $itemcode = $request->itemcode;

        DB::beginTransaction();
        try {
            DB::table('sales_cart_details')->where('cart_id', $docnum)->where('itemcode', $itemcode)->delete();
            
            DB::commit();
            $response = [
                "status"    => "success",
                "message"   => "Data berhasil dihapus"
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = [
                "status"    => "gagal",
                "message"   => "Data gagal dihapus ".$th->getMessage()
            ];
        }

        return response()->json($response);
    }

    public function commit(Request $request){
        DB::table('sales_cart')->where('docnum', $request->docnum)->update([
            'bussiness_partner' => $request->custcode,
            'stage' => $request->stage
        ]);

        $response = [
            "status"    => "success",
            "message"   => "Berhasil commit cart"
        ];

        return response()->json($response);
    }

    public function detailPricelist(Request $request){
        $pricelist  = DB::table('SAPITM1')->where('itemcode', $request->itemcode)->where('pricelist', $request->listnum)->first();
        $response = [];

        if($pricelist){
            $response = [
                "status"    => "success",
                "message"   => "Data berhasil ditemukan",
                "data"      => $pricelist
            ];	
        } else {
            $response = [
                "status"    => "success",
                "message"   => "Data tidak ditemukan"
            ];
        }
        
        return response()->json($response);
    }
}
