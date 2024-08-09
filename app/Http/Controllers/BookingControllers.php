<?php

namespace App\Http\Controllers;


use App\Helpers\Helper;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;


class BookingControllers extends Controller
{
    public function __construct()
    {
        // set middleware
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *booking
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Helper::checkACL('booking', 'r')) {
            // render index
            $category = Helper::forSelect('categories', 'id', 'name', false, false);

            $var = ['nav' => 'booking', 'subNav' => 'booking', 'title' => 'Pemesanan', 'category' => $category];
            return view('booking.index', $var);
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
        if (Helper::checkACL('booking', 'r')) {
            if ($request->ajax()) {
                // query data
                $bookings = DB::table('bookings')
                    ->leftJoin('memberships', 'bookings.membership_code', '=', 'memberships.code')
                    ->select([
                        'bookings.code as code',
                        'bookings.name as name',
                        'memberships.nama as member',
                        'bookings.date_booking as date_booking',
                        'bookings.mobile as mobile',
                        'bookings.status as status',
                    ]);
                return Datatables::of($bookings)
                    ->addColumn('action', function ($booking) {
                        // render column action
                        return view('booking.action', [
                            'edit_url' => '/booking/edit/' . $booking->code,
                            'show_url' => '/',
                            'id' => $booking->code,
                            'status' => $booking->status,
                        ]);
                    })
                    ->editColumn('status', function ($booking) {
                        // render column status
                        $_status = Helper::statusBadge($booking->status);
                        return $_status;
                    })

                    ->filter(function ($query) use ($request) {
                        if ($request->booking_name_filter) {
                            // default column filter
                            $query->where('bookings.name', 'like', "%" . $request->booking_name_filter . "%");
                        }

                        if ($request->has('booking_code_filter')) {
                            // default column filter
                            $query->where('bookings.code', 'like', "%{$request->booking_code_filter}%");
                        }
                        if ($request->has('booking_status_filter')) {
                            if (($request->booking_status_filter) == '-1') {
                                // default column filter
                                $query->where('bookings.status', "like", "%");
                            } else {
                                // filtered column
                                $query->where('bookings.status', 'like', "%" . $request->booking_status_filter . "%");
                            }
                        }
                        if ($request->has('booking_date_filter')) {
                            if (($request->booking_date_filter) == null) {
                                // default column filter 1 bulan
                                $query->where([
                                    ['bookings.date_booking', '>=', Date('Y-m-d', strtotime("-6 months")) . ' 00:00:00'],
                                    ['bookings.date_booking', '<=',  Date('Y-m-d') . ' 59:59:59'],
                                ]);
                            } else {
                                // filtered column
                                $dateSeparator = explode(" - ", $request->booking_date_filter);
                                $query->where([
                                    ['bookings.date_booking', '>=', $dateSeparator[0] . ' 00:00:00'],
                                    ['bookings.date_booking', '<=', $dateSeparator[1] . ' 59:59:59'],
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
        if (Helper::checkACL('booking', 'c')) {
            // $members = Helper::forSelect('memberships', 'code', DB::raw('CONCAT(code, "  -  " , nama) as member'), false, false);
            $var = [
                'nav' => 'booking',
                'subNav' => 'booking',
                'title' => 'Tambah Pemesanan',
                // 'members' => $members,
            ];
            return view('booking.create', $var);
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

        if (Helper::checkACL('booking', 'c')) {
            // Validation
            $vMessage = config('global.vMessage'); //get global validation messages\
            $validator = Validator::make($request->all(), [
                'membership_code' => 'required',
                'member_id' => 'required|string|min:4|max:6|exists:memberships,code',
                'date_need' => 'required|date|after:1 days ago',
                'nik' => 'required',
                'name' => 'required',
                'mobile' => 'required',
                'address' => 'required',
                'necessary' => 'required',
                'discount' => 'required',
                'tax' => 'required',
                'total' => 'required',
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                // dd($valid);
                session()->flash('notifikasi', [
                    "icon" => $valid->status,
                    "title" => $valid->code,
                    "message" =>  $valid->message
                ]);
                return redirect()->route('booking.new');
            }
            // Query creator
            DB::beginTransaction();

            try {
                $code  = Helper::docPrefix('bookings');
                // simpan header
                // summary subtotal
                $booking = DB::table('bookings')
                    ->insert([
                        'code' => $code,
                        'membership_code' => strtoupper($request->member_id),
                        'date_booking' => $request->date_need,
                        'nik' => $request->nik,
                        'name' => $request->name,
                        'mobile' => $request->mobile,
                        'address' => $request->address,
                        'necessary' => $request->necessary,
                        'discount' => $request->discount,
                        'tax' => $request->tax,
                        'status' => 'pending',
                        'created_at' => Carbon::now(),
                        'user_created' => Auth::id()
                    ]);
                $subGrandTotal = 0;
                $GrandTotal = 0;

                // simpan booking details
                if ($request->counting > 0) {
                    foreach ($request->nama as $key => $value) {
                        if (!is_null($request->nama[$key])) {
                            $item = DB::table('items')->select('buy_price', 'sell_price')->where('id', $request->nama[$key])->first();
                            $subTotal = $request->quantity[$key] * $item->sell_price;
                            $bookingDetails = DB::table('booking_details')
                                ->insert([
                                    'booking_id' => $code,
                                    'item_id' => $request->nama[$key],
                                    'quantity' => $request->quantity[$key],
                                    'buy_price' => $item->buy_price,
                                    'sell_price' => $item->sell_price,
                                    'sub_total' => $subTotal,
                                    'description' => '',
                                    'created_at' => Carbon::now(),
                                ]);
                            $subGrandTotal = $subGrandTotal + $subTotal;
                        }
                    }
                }
                $sumDisc = $subGrandTotal - ($subGrandTotal * ($request->discount / 100));
                $GrandTotal = $sumDisc + ($sumDisc * ($request->tax / 100));

                //update subGrandTotal & Grand Total
                $bookingSum = DB::table('bookings')
                    ->where('code', $code)
                    ->update([
                        'sub_total' => $subGrandTotal,
                        'total' => $GrandTotal,
                        'created_at' => Carbon::now()
                    ]);

                DB::commit();
                session()->flash('notifikasi', [
                    "icon" => config('global.success.S002.status'),
                    "title" => config('global.success.S002.code'),
                    "message" =>  config('global.success.S002.message'),
                ]);

                return redirect('/booking');
            } catch (\Throwable $e) {
                // $result = $e->getMessage();
                DB::rollback();
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E011.status'),
                    "title" => config('global.errors.E011.code'),
                    "message" =>  config('global.errors.E011.message') . ' ' . $e->getMessage(),
                ]);
                return redirect()->route('booking.new');
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
        if (Helper::checkACL('booking', 'r')) {
            $booking = DB::table('bookings')
                ->where('code', $code)->first();
            $booking_details = DB::table('booking_details')
                ->join('items', 'items.id', '=', 'booking_details.item_id')
                ->select([
                    'booking_details.id as id',
                    'booking_details.booking_id as booking_id',
                    'booking_details.item_id as item_id',
                    'booking_details.quantity as quantity',
                    'booking_details.sell_price as sell_price',
                    'booking_details.sub_total as sub_total',
                    'items.code as code',
                    'items.name as name'
                ])->orderBy('id', 'asc')
                ->where('booking_details.booking_id', $code)->get();
            // $members = Helper::forSelect('memberships', 'code', DB::raw('CONCAT(code, "  -  " , nama) as member'), false, false);
            $var = [
                'nav' => 'booking',
                'subNav' => 'booking',
                'title' => 'Ubah Pemesanan',
                'data' => $booking,
                'booking_details' => $booking_details,
            ];
            return view('booking.edit', $var);
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
        if (Helper::checkACL('booking', 'u')) {
            $booking = DB::table('bookings')->where('code', $code)->first();
            if (($booking->status == 'cancel') || ($booking->status == 'close') || ($booking->status == 'confirm')) {
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E014.status'),
                    "title" => config('global.errors.E014.code'),
                    "message" =>  config('global.errors.E014.message').'. Status : '.$booking->code.' - '.$booking->status,
                ]);
                return redirect()->route('booking');

            }
            // Validation
            // dd($request->all());
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                'membership_code' => 'required',
                'member_id' => 'required|string|min:4|max:6|exists:memberships,code',
                'date_need' => 'required|date|after:1 days ago',
                'nik' => 'required',
                'name' => 'required',
                'mobile' => 'required',
                'address' => 'required',
                'necessary' => 'required',
                'discount' => 'required',
                'tax' => 'required',
                'total' => 'required',
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                // dd($valid);
                session()->flash('notifikasi', [
                    "icon" => $valid->status,
                    "title" => $valid->code,
                    "message" =>  $valid->message
                ]);
                return redirect()->route('booking.edit', $code);
            }
            // Query creator
            DB::beginTransaction();

            try {
                // simpan header
                // summary subtotal
                $booking = DB::table('bookings')
                    ->where('code', $code)
                    ->update([
                        'membership_code' => strtoupper($request->member_id),
                        'date_booking' => $request->date_need,
                        'nik' => $request->nik,
                        'name' => $request->name,
                        'mobile' => $request->mobile,
                        'address' => $request->address,
                        'necessary' => $request->necessary,
                        'discount' => $request->discount,
                        'tax' => $request->tax,
                        'status' => $request->status,
                        'updated_at' => Carbon::now(),
                        'user_updated' => Auth::id()
                    ]);
                $subGrandTotal = 0;
                $GrandTotal = 0;

                // simpan booking details
                if ($request->counting > 0) {

                    // DB::table('booking_details')->where('booking_id', $code)->delete();
                    $bookingDetails = DB::table('booking_details')->where('booking_id', $code)->get();

                    foreach ($request->nama as $key => $value) {
                        $item = DB::table('items')->select('buy_price', 'sell_price')->where('id', $request->nama[$key])->first();
                        $subTotal = $request->quantity[$key] * $item->sell_price;
                        if (!is_null($request->nama[$key])) {
                            if (!is_null($request->booking_detail_id[$key])) {
                                // foreach ($bookingDetails as $bookingDetail) {
                                $bookingDetail = DB::table('booking_details')->where('id', $request->booking_detail_id[$key])->first();
                                if ($bookingDetail->item_id == $request->nama[$key]) {
                                    DB::table('booking_details')
                                        ->where('id', $request->booking_detail_id[$key])
                                        ->update([
                                            // 'item_id' => $request->nama[$key],
                                            'quantity' => $request->quantity[$key],
                                            'buy_price' => $bookingDetail->buy_price,
                                            'sell_price' => $bookingDetail->sell_price,
                                            'sub_total' => $bookingDetail->sell_price * $request->quantity[$key],
                                            'updated_at' => Carbon::now(),
                                        ]);
                                } else {
                                    DB::table('booking_details')
                                        ->where('id', $request->booking_detail_id[$key])
                                        ->update([
                                            'item_id' => $request->nama[$key],
                                            'quantity' => $request->quantity[$key],
                                            'buy_price' => $item->buy_price,
                                            'sell_price' => $item->sell_price,
                                            'sub_total' => $subTotal,
                                            'updated_at' => Carbon::now(),
                                        ]);
                                }
                                // }

                                // }
                            } else {
                                DB::table('booking_details')
                                    ->insert([
                                        'booking_id' => $code,
                                        'item_id' => $request->nama[$key],
                                        'quantity' => $request->quantity[$key],
                                        'buy_price' => $item->buy_price,
                                        'sell_price' => $item->sell_price,
                                        'sub_total' => $subTotal,
                                        'updated_at' => Carbon::now(),
                                    ]);
                            }
                        }
                    }
                }

                $sumGrandTotal = DB::table('booking_details')->where('booking_id', $code)->sum('sub_total');
                $sumDisc = $sumGrandTotal - ($sumGrandTotal * ($request->discount / 100));
                $GrandTotal = $sumDisc + ($sumDisc * ($request->tax / 100));

                //update sumGrandTotal & Grand Total
                $bookingSum = DB::table('bookings')
                    ->where('code', $code)
                    ->update([
                        'sub_total' => $sumGrandTotal,
                        'total' => $GrandTotal,
                        'updated_at' => Carbon::now()
                    ]);
                DB::commit();
                session()->flash('notifikasi', [
                    "icon" => config('global.success.S003.status'),
                    "title" => config('global.success.S003.code'),
                    "message" =>  config('global.success.S003.message'),
                ]);
                // return redirect()->route('booking.edit', $code);
                return redirect()->route('booking');
            } catch (\Throwable $e) {
                // $result = $e->getMessage();
                DB::rollback();
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E011.status'),
                    "title" => config('global.errors.E011.code'),
                    "message" =>  config('global.errors.E011.message') . ' ' . $e->getMessage(),
                ]);
                // return $e->getMessage();
                return redirect()->route('booking.edit', $code);
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {
        // disable data
        if (Helper::checkACL('booking', 'd')) {
            $id = $request->id;
            DB::beginTransaction();

            try {
                $booking = DB::table('bookings')->where('code', $id);
                $member_login = DB::table('memberships')->where('username', $id);
                $status = $booking->first()->status;
                $statusM = $member_login->first()->status;
                $booking->update(['status' => $status == 'active' ? 'suspend' : 'active']);
                $member_login->update(['status' => $statusM  ? false : true]);
                $result = config('global.success.S003');
                DB::commit();
            } catch (QueryException $e) {
                DB::rollBack();
                $result = config('global.errors.E009');
                $result = $e->getMessage();
            } catch (\Throwable $e) {
                DB::rollBack();
                $result = config('global.errors.E009');
                $result = $e->getMessage();
            }
        } else {
            // tidak memiliki otorisasi
            $result = config('global.errors.E002');
        }

        return response()->json($result); //return json ke request ajax
    }
    public function getItem(Request $request)
    {
        if (Helper::checkACL('booking', 'r')) {
            $id = $request->id;
            DB::beginTransaction();

            $items = DB::table('items')
                ->select('id', 'name', 'code', 'sell_price')
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
                    $list[$key]['sell_price'] = Helper::formatNumber($item->sell_price, '');
                }
            } else {
                $list[0]['id'] = 0;
                $list[0]['text'] = 'Item Tidak Ditemukan';
            }
            $result = response()->json($list);
        } else {
            // tidak memiliki otorisasi
            $result = config('global.errors.E002');
        }
        return $result;
    }
    public function getMember(Request $request)
    {
        if (Helper::checkACL('booking', 'r')) {
            $items = DB::table('memberships')
                ->select('code', 'nik', 'nama', 'mobile', 'address')
                ->where('nama', 'like', '%' . $request->data . '%')
                ->orWhere('mobile', 'like', '%' . $request->data . '%')
                ->limit(5)
                ->get();
            $list = array();
            if (count($items) > 0) {
                foreach ($items as $key => $item) {
                    $list[$key]['id'] = $item->code;
                    $list[$key]['text'] = $item->nama . "  |  " . $item->mobile;
                    $list[$key]['nik'] = $item->nik;
                    $list[$key]['name'] = $item->nama;
                    $list[$key]['mobile'] = $item->mobile;
                    $list[$key]['address'] = $item->address;
                }
            } else {
                $list[0]['id'] = null;
                $list[0]['text'] = 'Member Tidak Ditemukan';
            }
            $result = response()->json($list);
        } else {
            // tidak memiliki otorisasi
            $result = config('global.errors.E002');
        }
        return $result;
    }
    public function getDataMember(Request $request)
    {
        if (Helper::checkACL('booking', 'r')) {
            $items = DB::table('memberships')
                ->select('code', 'nik', 'nama', 'mobile', 'address')
                ->where('code', $request->code)
                ->where('status', 'active')
                ->limit(1)
                ->get();
            $list = array();
            if (count($items) > 0) {
                foreach ($items as $key => $item) {
                    $list['id'] = $item->code;
                    $list['text'] = $item->nama . "  |  " . $item->mobile;
                    $list['nik'] = $item->nik;
                    $list['name'] = $item->nama;
                    $list['mobile'] = $item->mobile;
                    $list['address'] = $item->address;
                }
                $result = response()->json($list);
            } else {
                return response()->json();
            }
            $result = response()->json($list);
        } else {
            // tidak memiliki otorisasi
            // $result = config('global.errors.E002',404);
            $result = response()->json(config('global.errors.E002'), 404);
        }
        return $result;
    }
}
