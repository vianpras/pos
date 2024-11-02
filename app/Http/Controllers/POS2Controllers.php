<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class POS2Controllers extends Controller
{
    public function __construct()
    {
        // set middleware
        $this->middleware(['auth']);
    }

    public function index()
    {
        if (Helper::checkACL('sales', 'r')) {
            // render index
            $membership = Helper::forSelect('memberships', 'code', 'nama', false, false);

            $var = ['nav' => 'sales', 'subNav' => 'sales', 'title' => 'Transaksi Penjualan', 'membership' => $membership];
            return view('sales.index', $var);
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

    public function create(Request $request)
    {
        if (Helper::checkACL('sales', 'c')) {
            $items      = DB::table('SAPOITM')->get();
            $items->map(function($value, $key){
                $items = $value;
                $hetPrice = DB::table('SAPOPLN')->where('listname', 'LIKE', '%Eceran Tertinggi%')->first()->listnum;
                $itemPrice = DB::table('SAPITM1')->where('itemcode', $items->itemcode)->where('pricelist', $hetPrice)->first();
                if($itemPrice){
                    $itemPrice = $itemPrice->price;
                } else {
                    $itemPrice = 0;
                }
                $items->price = $itemPrice;
            });

            $pricelist  = DB::table('SAPOPLN')->get();
            $customer   = DB::table('SAPOCRD')->select('SAPOCRD.*', DB::raw('RIGHT(SAPOCRD.phone, 4) AS phoneCode'))->get();
            
            $cartCode = 0;
            if($request){
                $cartCode = $request->cartCode;
                $cart = DB::table('sales_cart')
                        ->leftJoin('users', 'sales_cart.salesid','=','users.id')
                        ->leftJoin('SAPOCRD', 'sales_cart.bussiness_partner','=','SAPOCRD.cardcode')
                        ->where('docnum', $cartCode)
                        ->select('sales_cart.*', 'SAPOCRD.cardname', 'SAPOCRD.phone', 'users.full_name')
                        ->first();
                $cart_details = DB::table('sales_cart_details')->where('cart_id', $cartCode)->get();
            }
        
            $var = [
                'nav'       => 'salesCreate',
                'subNav'    => 'sales',
                'title'     => 'Tambah Order Penjualan',
                'items'     => $items,
                'pricelist' => $pricelist,
                'customer'  => $customer,
                'cart'      => ($request) ? $cart : false,
                'cart_details' => ($request) ? $cart_details : false
            ];

            return view('sales.create2', $var);
        } else {
            $result = config('global.errors.E002');
            
            session()->flash('notifikasi', [
                "icon"      => config('global.errors.E002.status'),
                "title"     => config('global.errors.E002.code'),
                "message"   => config('global.errors.E002.message'),
            ]);
    
            return redirect('dashboard');
        }

    }

    public function storeChart(Request $request){
        if (Helper::checkACL('sales', 'c')) {
            try {
                DB::beginTransaction();
                $masterCart = DB::table('carts')->where('table', $request->table)->first();
                if($masterCart){
                    DB::table('cart_details')->updateOrInsert(
                        ['cart_id' => $masterCart->id, 'item_id' => $request->item_id],
                        ['cart_id' => $masterCart->id, 'item_id' => $request->item_id, 'quantity' => $request->quantity, 'sell_price' => $request->sell_price, 'sub_total' => $request->sub_total, 'description' => $request->description]
                    );
                } else {
                    $id = DB::table('carts')->insertGetId([
                        'table' => $request->table,
                        'user_created' => Auth::id(),
                        'created_at' => Carbon::now()
                    ]);

                    DB::table('cart_details')->insert([
                        'cart_id' => $id, 
                        'item_id' => $request->item_id, 
                        'quantity' => $request->quantity, 
                        'sell_price' => $request->sell_price, 
                        'sub_total' => $request->sub_total,
                        'description' => $request->description
                    ]);
                }
                DB::commit();
                $result = config('global.success.S003');
            } catch (\Throwable $th) {
                DB::rollBack();
                $result = config('global.errors.E009');
            }
            return response()->json($result);
        } else {
            // tidak memiliki otorisasi
            $result = config('global.errors.E002');
        }
        return response()->json($result);
    }

    public function storeChartDiscount(Request $request)
    {
        if (Helper::checkACL('sales', 'u')) {
            try {
                DB::beginTransaction();

                DB::table('carts')->where('table', $request->table)->update([
                    'disc'      => $request->disc,
                    'tax'       => $request->tax,
                    'disc_rp'   => $request->disc_rp,
                    'tax_rp'    => $request->tax_rp
                ]);

                DB::commit();
                $result = config('global.success.S003');
            } catch (\Throwable $th) {
                DB::rollBack();
                $result = config('global.errors.E009');
            }
        } else {
            // tidak memiliki otorisasi
            $result = config('global.errors.E002');
        }
        return response()->json($result); //return json ke request ajax
    }
}
