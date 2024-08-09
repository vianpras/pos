<?php

namespace App\Http\Controllers;

use App\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ProfileControllers extends Controller
{
   public function __construct()
   {
      $this->middleware('auth');
   }
   public function update(Request $request)
   {
      $vMessage = config('global.vMessage'); //get global validation messages
      $validator = Validator::make($request->all(), [
         // 'name' => ['required', 'string', 'max:255'],

         // 'email' => 'required|unique:users,email,' . $request->user()->id,
         'email' => 'email|max:255|unique:users,email,'. $request->user()->id,
         'name' => 'required|string|min:3|max:255',
         'file' => 'mimes:jpeg,bmp,png|max:2048',

      ],$vMessage);
      // valid?
      $valid = Helper::validationFail($validator);
      if(!is_null($valid)){
         return redirect()->back()->with('status',$valid);
      }

      $user = User::findOrFail(Auth::id());
      $user->name = $request->get('name');
      if ($request->get('password') == null) {
         $user->save();
      } else {
         $validPass = Validator::make($request->all(), [
            'password' => 'confirmed|min:8',
         ]);
         $valid = Helper::validationFail($validator);
         if(!is_null($valid)){
            return redirect()->back()->with('status',$valid);
         }
         $user->password = Hash::make($request->get('password'));
         $user->save();
      }

      if ($request->hasFile('file')) {
         $file = $request->file('file');
         if ($request->file('file')->isValid()) {
            $file->storeAs('user', $request->user()->id,'private');
         }
      }
      // if ($validator->fails()) {
      //    Session::flash("notifikasi", [
      //       "icon" => "error",
      //       "message" => $validator->errors()
      //    ]);
      //    return redirect()->back()->with('status');
      // }
      Session::flash("notifikasi", [
         "icon" => "success",
         "message" => "Berhasil Merubah Data"
      ]);
      return redirect()->back()->with('status');
   }
}
