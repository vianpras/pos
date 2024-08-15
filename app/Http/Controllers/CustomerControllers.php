<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerControllers extends Controller
{
    function custByCode(Request $request){
        $cardcode = $request->cust_code;
        $customer_details  = DB::table('SAPOCRD')->where('cardcode', $cardcode)->select('SAPOCRD.*', DB::raw('RIGHT(SAPOCRD.phone, 4) AS phoneCode'))->first();

        $var = [
            'cust_details' => $customer_details
        ];

        return response()->json($var);
    }
}
