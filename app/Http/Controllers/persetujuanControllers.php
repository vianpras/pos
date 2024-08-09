<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Illuminate\Support\Facades\Mail;
use App\Mail\approveMail;
use Carbon\Carbon;

class persetujuanControllers extends Controller
{
    public function approve($code_member){
        $check = DB::table('memberships')->where('code', $code_member)->first();

        if($check->agreement == '0'){
            DB::table('memberships')->where('code', $code_member)->update([
                'agreement' => '1',
                'agreement_time' => Carbon::now()
            ]);

            $now = Carbon::now();

            $pdf = PDF::loadView('membership.viewPdf',compact('check','now'))->setOptions(['defaultFont' => 'sans-serif']);
            $path = public_path().'/persetujuan/';
            $fileName = 'member-'.$code_member.'.pdf';
            $pdf->save($path  . $fileName);

            $path2 = public_path()."/persetujuan/".$fileName;
            $data = array('nama' => $check->nama ,'attach' => $path2);

            Mail::to($check->email)->send(new approveMail($data));

            return view('alerts.success');
        } else {
            return view('alerts.error');
        }
    }

    public function reject($code_member){
        $check = DB::table('memberships')->where('code', $code_member)->first();
        if($check->agreement == '0'){
            DB::table('memberships')->where('code', $code_member)->update([
                'agreement' => '2',
                'agreement_time' => Carbon::now()
            ]);

            return view('alerts.success');
        } else {
            return view('alerts.error');
        }
    }
}
