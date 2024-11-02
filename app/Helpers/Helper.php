<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use LDAP\Result;
use Throwable;
use Carbon\CarbonPeriod;
use Carbon\Carbon;


class Helper
{
   public static function test($var)
   {
      return $var;
   }
   public static function LoginAction($id, $action)
   {
      if($action == 'success') {
         $pdo     = DB::getPdo();
         $query   = 'UPDATE users SET last_login = NOW(), failed_login = 0 WHERE id=:id';
         $stmt    = $pdo->prepare($query);

         $stmt->bindValue(':id', $id);
         $stmt->execute();
         $pdo = null;
         return;
      }elseif($action == 'failed') {
         // set failed_login = +1
         $pdo = DB::getPdo();
         $query = 'UPDATE users SET failed_login = failed_login+1 WHERE id=:id';
         $stmt = $pdo->prepare($query);
         $stmt->bindValue(':id', $id);
         $stmt->execute();
         $pdo = null;
         return;
      } else {
         $pdo = DB::getPdo();
         $query = 'SELECT failed_login FROM users WHERE id=:id';
         $stmt = $pdo->prepare($query);
         $stmt->bindValue(':id', $id);
         $stmt->execute();
         $data = $stmt->fetch($pdo::FETCH_ASSOC);
         $pdo = null;
         return $data['failed_login'];
      }
   }
   public static function Auth($user, $password, $type)
   {
      try {
         $pdo = DB::getPdo();
         if ($type == 'email') {
            $query = 'SELECT id, username, full_name, mobile, status_pos, password FROM users WHERE email=:email';
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':email', $user);
         }
         if ($type == "mobile") {
            $query = 'SELECT id, username, full_name, mobile, status_pos, password FROM users WHERE mobile=:mobile';
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':mobile', $user);
         }
         if ($type == "username") {
            $query = 'SELECT id, username, full_name, mobile, status_pos, password FROM users WHERE username=:username';
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':username', $user);
         }
         $stmt->execute();
         $data = $stmt->fetch($pdo::FETCH_ASSOC);
         // dd($data);
         $pdo = null;
         if ((is_array($data) >= 1)) {
            // dd($data);

            $id = $data['id'];
            $hash_password = $data['password'];
            $counterLogin = Helper::LoginAction($id, '');
            if ($counterLogin >= env('MAX_FAILED_LOGIN')) {
               Helper::LoginAction($id, 'failed');
               $result = config('global.errors.E007');
               return $result;
            } else {
               if (Hash::check($password, $hash_password)) { //password benar?
                  if ($data['status_pos'] == 0) { //user tidak aktif 0
                     $result = config('global.errors.E008');
                     return $result;
                  }
                  $result = config('global.success.S001');
                  return $result;
               } else { //password salah
                  Helper::LoginAction($id, 'failed');
                  $result = config('global.errors.E001');
                  return $result;
               }
            }
         }
         $result = config('global.errors.E001');
         return $result;
      } catch (\Throwable $th) {
         $result = config('global.errors.E999');
         $result = $th->getMessage();
         return $result;
      }
   }
   public static function title($title)
   {
      // $nameRoute = Str::title(Route::currentRouteName());
      $nameRoute = $title;
      $appName = config('app.name');
      $result = $nameRoute . ' • ' . $appName;
      if (empty($nameRoute)) {
         $result = $appName;
      }
      return $result;
   }
   public static function saveCurrency($value)
   {
      $delDot = str_replace('.', '', $value);
      $result = str_replace(',00', '', $delDot);
      return $result;
   }
   public static function setCurrency($value)
   {
      $result = number_format($value, 2, ",", ".");
      return $result;
   }

   public static function setDate($data, $type)
   {

      switch ($type) {
         case 'numMonth':
            $result = date('d-m-Y', strtotime($data));
            break;
         case 'strMonth':
            $result = date('d M Y', strtotime($data));
            break;
         case 'fullDate':
            $result = date('D, d M Y', strtotime($data));
            break;
         case 'bulan':
            $numMonth = intval(str_replace('0', '', $data));
            $month = array(
               1 => 'Januari',
               'Februari',
               'Maret',
               'April',
               'Mei',
               'Juni',
               'Juli',
               'Agustus',
               'September',
               'Oktober',
               'November',
               'Desember'
            );
            return $result = $month[$numMonth];
         case 'fullDateId':
            $month = array(
               1 =>   'Januari',
               'Februari',
               'Maret',
               'April',
               'Mei',
               'Juni',
               'Juli',
               'Agustus',
               'September',
               'Oktober',
               'November',
               'Desember'
            );
            $expld = explode('-', $data);

            // variabel expld 0 = tanggal
            // variabel expld 1 = bulan
            // variabel expld 2 = tahun

            return $expld[2] . ' ' . $month[(int)$expld[1]] . ' ' . $expld[0];
         default:
            $result = '';
            break;
      }
      return $result;
   }

   public static function checkACL($role, $permission)
   {
      $users_acl = Session::get('_users_acl');
      if (property_exists($users_acl, $role)) {
         $checkACL = strpos($users_acl->$role, $permission) !== false;
      } else {
         $checkACL = false;
      }

      return $checkACL;
   }
   public static function checkEditACL($role, $permission)
   {
      $checkACL = strpos($role, $permission) !== false;
      return $checkACL;
   }


   public static function forSelect($table, $columnID, $columnValue, $columnWhere, $columnWhereValue)
   {
      if (!$columnWhere) {
         $result =  DB::table($table)->select($columnID, $columnValue)->get();
      } else {
         $result =  DB::table($table)->select($columnID, $columnValue)->where($columnWhere, $columnWhereValue)->get();
      }

      return $result;
   }

   public static function trans($var)
   {
      if (array_key_exists($var, config('global.trans'))) {
         return config('global.trans.' . $var);
      }
      // else{
      //    return config('global.trans._valid');
      // }
   }

   public static function validationFail($validator)
   {
      if ($validator->fails()) {
         // dd( $validator);
         $messages = $validator->errors()->messages();
         $msg = "";
         foreach ($messages as $key => $message) {
            foreach ($message as $value) {
               $transKey = Helper::trans($key);
               $msg .= (count($messages) == 1) ?
                  $transKey . ": " . $value . "<br>" :
                  $transKey . ": " . $value . "<br>";
            }
         }
         $result = (object) [
            'http_code' => 200,
            'status' => 'error',
            'code' => 'E010',
            'message' => $msg,
         ];
         return $result;
      }
   }
   public static function formatNumber($value, $type)
   {
      switch ($type) {
         case 'rupiah':
            $result = "Rp " . number_format($value, 2, ',', '.');
            break;
         case 'norp':
            $result = number_format($value, 0, ',', '.');
            break;
         case 'number':
            $result = str_replace(array('.', ',00', 'Rp '), '', $value);
            break;
         default:
            $result = number_format($value, 0, '', '.');
            break;
      }
      return $result;
   }

   public static function statusBadge($value)
   {
      switch ($value) {
         case 'open':
            $result = '<center><span class="text-capitalize right badge badge-info">' . $value . '</span></center>';
            break;
         case 'clear':
            $result = '<center><span class="text-capitalize right badge badge-success">' . $value . '</span></center>';
            break;
         case 'cancel':
            $result = '<center><span class="text-capitalize right badge badge-danger">' . $value . '</span></center>';
            break;
         case 'draft':
            $result = '<center><span class="text-capitalize right badge badge-secondary">' . $value . '</span></center>';
            break;
         case 'close':
            $result = '<center><span class="text-capitalize right badge bg-dark">' . $value . '</span></center>';
            break;
         case '1':
            $result = '<center><span class="text-capitalize right badge badge-success">' . $value . '</span></center>';
            break;
         case '0':
            $result = '<center><span class="text-capitalize right badge badge-danger">' . $value . '</span></center>';
            break;
         case 'active':
            $result = '<center><span class="text-capitalize right badge badge-success">' . $value . '</span></center>';
            break;
         case 'suspend':
            $result = '<center><span class="text-capitalize right badge badge-danger">' . $value . '</span></center>';
            break;
         case 'pending':
            $result = '<center><span class="text-capitalize right badge bg-orange">' . $value . '</span></center>';
            break;
         case 'confirm':
            $result = '<center><span class="text-capitalize right badge bg-teal">' . $value . '</span></center>';
            break;
         default:
            $result = '<center><span class="text-capitalize right badge bg-orange">Unknown</span></center>';
            break;
      }
      return $result;
   }

   public static function zerofill($value, $length)
   {
      return str_pad($value, $length, '0', STR_PAD_LEFT);
   }

   public static function docPrefix($type)
   {
      try {
         //code...
         $countDoc = DB::table($type)->whereDate('created_at', Date('Y-m-d'))->count() + 1;
         $prefix = DB::table('_docPrefix')->select('prefix')->where('docType', $type)->first()->prefix;
         $result = $prefix  . Date('ymd') . '-' . Helper::zerofill($countDoc, 4);
      } catch (\Throwable $th) {
         return $th->getMessage();
      }

      // contoh : Helper::docPrefix('requistions'); --> PR-20220124-0001PR-20220124-0001
      return $result;
   }
   public static function memberPrefix($type)
   {
      try {
         //code...
         $countDoc = DB::table($type)->count() + 1;
         $prefix = DB::table('_docPrefix')->select('prefix')->where('docType', $type)->first()->prefix;
         $result = $prefix . Helper::zerofill($countDoc, 5);
      } catch (\Throwable $th) {
         return $th->getMessage();
      }

      // contoh : Helper::docPrefix('requistions'); --> PR-20220124-0001PR-20220124-0001
      return $result;
   }
   public static function randColor2()
   {
      return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
   }

   public static function randColor()
   {
      $color = [
         'primary',
         'info',
         'warning',
         'danger',
         'success',
         'indigo',
         'navy',
         'pink',
         'maroon',
         'teal',
         'lime',
         'purple',
         'olive',
      ];
      $count = count($color) - 1;
      $randColor = rand(0, $count);
      $result = $color[$randColor];
      return $result;
   }
   public static function counterData($table, $status)
   {
      try {
         $data = DB::table($table)->where('status', $status)->count();
         if ($data > 0) {
            $result = '<span class="badge badge-danger right">+' . $data . '</span>';
         } else {
            $result = null;
         }
      } catch (\Throwable $th) {
         $result =  $th->getMessage();
      }
      return $result;
   }

   public static function generateListDate($a, $b)
   {
      $period = CarbonPeriod::create($a, $b);

      // Iterate over the period
      foreach ($period as $date) {
         $result[] = $date->format('Y-m-d');
      }
      return $result;
      // Convert the period to an array of dates
      //  return $dates = $period->toArray();
   }

   public static function checkCart()
   {
      // total cart ex 20
      $getTotalCart = DB::table('configurations')->select('total_cart')->first()->total_cart;
      for ($i = 1; $i <= $getTotalCart; $i++) {
         $totalCart[] = $i;
      }
      // cart yang di pakai 

      try {
         $existCart = [];
         $_existCart = DB::table('carts')->select('table')->get();
         foreach ($_existCart as $key => $value) {
            $existCart[] = $value->table;
         }
      } catch (\Throwable $th) {
         dd($_existCart);
         //throw $th;
         return $th->getMessage();
      }

      // komparasi total cart dan table exist
      $check = array_diff($totalCart, $existCart);

      // ambil value table yang kosong
      $result = reset($check);

      return $result;
   }

   public static function loggingApp($ip, $idUser, $action)
   {
      try {
         $url = url()->current();
         $actionBundle = $action . ' access url: ' . $url;
         $saveLogs = DB::table('logs_apps')->insert([
            'user_id' => $idUser ?? null,
            'access_url' => $actionBundle ?? 'unkown action',
            'ip_address' => $ip ?? '999.999.999.999',
            'created_at' => Carbon::now(),
         ]);
         return;
      } catch (\Throwable $th) {
         session()->flash('notifikasi', [
            "icon" => config('global.errors.E501.status'),
            "title" => config('global.errors.E501.code'),
            "message" => config('global.errors.E501.message'),
        ]);
      }
   }
}
