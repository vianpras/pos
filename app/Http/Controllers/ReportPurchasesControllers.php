<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportPurchasesControllers extends Controller
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
    public function index(Request $request)
    {
        if (Helper::checkACL('purchase_report', 'r')) {
            try {
                $parentCategory = DB::table('categories')->select('id')->where('code', 'PO')->first();
                $items = DB::table('master_items')
                    ->join('categories', 'categories.id', '=', 'master_items.category_id')
                    ->select([
                        'master_items.id as id',
                        'master_items.kode_item as code',
                        'master_items.nama_item as name',
                        'master_items.buy_price as buy_price',
                        'master_items.category_id as category_id',
                        'categories.name as category_name'
                    ])
                    ->Where([
                        ['master_items.status', '1'],
                        ['categories.parent', $parentCategory->id],
                    ])
                    ->get();
                $var = [
                    'nav'   => 'report',
                    'subNav'=> 'purchase_report',
                    'title' => 'Laporan Pembelian',
                    'items' => $items,
                ];
                return view('report.purchases.index', $var);
            } catch (\Throwable $th) {
                return $th->getMessage();
                session()->flash('notifikasi', [
                    "icon" => config('global.errors.E999.status'),
                    "title" => config('global.errors.E999.code'),
                    "message" =>  config('global.errors.E999.message'),
                ]);
                return redirect('dashboard');
            }
           
        } else {
            session()->flash('notifikasi', [
                "icon" => config('global.errors.E002.status'),
                "title" => config('global.errors.E002.code'),
                "message" =>  config('global.errors.E002.message'),
            ]);
            return redirect('dashboard');
        }
    }

    public function report(Request $request)
    {
        if (Helper::checkACL('purchase_report', 'r')) {
            $vMessage = config('global.vMessage'); //get global validation messages\
            $validator = Validator::make($request->all(), [
                'range_date' => 'required',
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if (!is_null($valid)) {
                // return response()->json($valid); //return if not valid
                $result = config('global.errors.E010');
                return response()->json($result, 404);
            }

            try {
                $dateSeparator = explode(" - ", $request->range_date);
                $typeReport = $request->typeReport;
                $showChart = $request->showChart;
                $item_id = $request->item_id;
                $report = [];
                $chart = [];
                $_datasets = array();
                $parentCategory = DB::table('categories')->select('id')->where('code', 'PO')->first();


                $listDates = Helper::generateListDate($dateSeparator[0], $dateSeparator[1]);
                if (count($listDates) > 31) {
                    $result = config('global.errors.E015');
                    return response()->json($result);
                }
                // jika jenis report global
                if ((is_null($typeReport)) || ($typeReport == '')) {
                    // query creator
                    $totalPurchase = DB::table('pembelian')
                        ->where([
                            ['tanggal', '>=', $dateSeparator[0]],
                            ['tanggal', '<=', $dateSeparator[1]],
                        ])->sum('total');
                    $countPurchase = DB::table('pembelian')
                        ->where([
                            ['tanggal', '>=', $dateSeparator[0]],
                            ['tanggal', '<=', $dateSeparator[1]],
                        ])->count();
                    $countItemPurchase = DB::table('pembelian_details')
                        ->leftJoin('pembelian', 'pembelian_details.id_pembelian', '=', 'pembelian.id')
                        ->where([
                            ['pembelian.tanggal', '>=', $dateSeparator[0]],
                            ['pembelian.tanggal', '<=', $dateSeparator[1]],
                        ])
                        ->sum('qty');

                    $report = DB::table('pembelian')->select(
                        'kode_pembelian',
                        'tanggal',
                        'total',
                    )
                        ->selectRaw($totalPurchase . ' as total_purchases')
                        ->selectRaw($countPurchase . ' as count_purchases')
                        ->orderBy('tanggal')
                        ->groupBy('kode_pembelian')
                        ->where([
                            ['tanggal', '>=', $dateSeparator[0]],
                            ['tanggal', '<=', $dateSeparator[1]],
                        ])
                        ->get();

                    // jika pakai chart
                    if ($showChart == 1) {
                        $listDates = Helper::generateListDate($dateSeparator[0], $dateSeparator[1]);

                        foreach ($listDates as $keyDate => $listDate) {
                            $_dataChart = DB::table('pembelian')
                                ->select(
                                    'tanggal',
                                    DB::raw('SUM(total) as total'),
                                )
                                ->groupBy('tanggal')
                                ->where([
                                    ['tanggal', $listDate],
                                ])
                                ->first();
                            if (is_null($_dataChart)) {
                                $_label[] = $listDate;
                                $_data[] = 0;
                            } else {
                                $_label[] = $_dataChart->tanggal;
                                $_data[] = $_dataChart->total;
                            }
                        }
                        $_datasets[] = json_encode([
                            'label' => "PEMBELIAN",
                            'backgroundColor' => "rgba(60,141,188,0.5)",
                            'borderColor' => "rgba(60,141,188,0.8)",
                            'data' => $_data,
                            'pointStyle' => "circle",
                            'pointRadius' => 3,
                            'pointHoverRadius' => 7,
                            'pointColor' => "#efefef",
                            'pointBackgroundColor' => "#efefef",
                        ]);

                        $chart = [
                            'datePurchaseChart' => 'Pembelian : ' . Helper::setDate($dateSeparator[0], 'fullDateId') . ' - ' . Helper::setDate($dateSeparator[1], 'fullDateId'),
                            'totalPurchaseChart' => $totalPurchase,
                            'transactionPurchaseChart' => $countPurchase,
                            'itemPurchaseChart' => $countItemPurchase,
                            'label' => $_label,
                            'datasets' => $_datasets,
                        ];
                    }
                }
                // jika jenis report item
                if ((!is_null($typeReport)) && ($typeReport == 1)) {
                    // query creator
                    // jika semua item
                    // dd($request->item_id);
                    $checkItemId = is_array($request->item_id) ? (in_array("all", $request->item_id)) : ($request->item_id == 'all');
                    if ($checkItemId || is_null($request->item_id)) {

                        $item_id = DB::table('master_items')
                            ->join('categories', 'categories.id', '=', 'master_items.category_id')
                            ->select([
                                'master_items.id as id',
                            ])
                            ->Where([
                                ['categories.parent', $parentCategory->id],
                            ])
                            ->orderBy('master_items.nama_item', 'asc')
                            ->get();
                        foreach ($item_id as $keyItem => $item) {
                            # code...
                            $checkItemPurchase = DB::table('pembelian')
                                ->leftJoin('pembelian_details', 'pembelian_details.id_pembelian', '=', 'pembelian.id')
                                ->where([
                                    ['pembelian.tanggal', '>=', $dateSeparator[0]],
                                    ['pembelian.tanggal', '<=', $dateSeparator[1]],
                                    ['pembelian_details.id_item', $item->id],
                                ])
                                ->count();
                            $checkX[] = $checkItemPurchase;
                            // jika item ada
                            if ($checkItemPurchase >= 1) {

                                $getCountItemPurchase = (int) DB::table('pembelian_details')
                                    ->leftJoin('pembelian', 'pembelian_details.id_pembelian', '=', 'pembelian.id')
                                    ->where([
                                        ['pembelian.tanggal', '>=', $dateSeparator[0]],
                                        ['pembelian.tanggal', '<=', $dateSeparator[1]],
                                        ['pembelian_details.id_item', $item->id]
                                    ])
                                    ->sum('qty');

                                $countItemPurchase[] = $getCountItemPurchase;

                                $getReport = DB::table('pembelian')
                                    ->leftJoin('pembelian_details', 'pembelian_details.id_pembelian', '=', 'pembelian.id')
                                    // ->leftJoin('items', 'items.id', '=', 'purchases_details.item_id')
                                    ->leftJoin('master_items', 'pembelian_details.id_item', '=', 'master_items.id')
                                    ->leftJoin('categories', 'master_items.category_id', '=', 'categories.id')
                                    ->select([
                                        'master_items.id as id_item',
                                        'master_items.kode_item as code_item',
                                        'master_items.nama_item as name_item',
                                        'categories.name as name_category',
                                        DB::raw('SUM(pembelian_details.qty) as total_quantity'),
                                    ])
                                    // ->orderBy('total_quantity', 'desc')
                                    ->selectRaw('1  as purchases_true')
                                    ->groupBy('master_items.id')
                                    ->where([
                                        ['pembelian.tanggal', '>=', $dateSeparator[0]],
                                        ['pembelian.tanggal', '<=', $dateSeparator[1]],
                                        ['pembelian_details.id_item', $item->id],
                                    ])
                                    ->first();

                                // $report[] = $getReport;

                                $reports[] = !is_null($getReport) ? $getReport : null;
                            } else {

                                $countItemPurchase[] = 0;
                                $getReport = DB::table('master_items')
                                    ->leftJoin('pembelian_details', 'pembelian_details.id_item', '=', 'master_items.id')
                                    ->leftJoin('pembelian', 'pembelian_details.id_pembelian', '=', 'pembelian.id')
                                    ->leftJoin('categories', 'master_items.category_id', '=', 'categories.id')
                                    ->select([
                                        'master_items.id as id_item',
                                        'master_items.kode_item as code_item',
                                        'master_items.nama_item as name_item',
                                        'categories.name as name_category',
                                    ])
                                    ->selectRaw('IFNULL(SUM(pembelian_details.qty), 0)  as total_quantity')
                                    ->selectRaw('0  as purchases_true')
                                    // ->orderBy('total_quantity', 'desc')
                                    ->where([
                                        ['pembelian.tanggal', '>=', $dateSeparator[0]],
                                        ['pembelian.tanggal', '<=', $dateSeparator[1]],
                                        ['master_items.id', $item->id]

                                    ])
                                    // ->whereIn('items.id', [8])
                                    ->first();
                                // $report[] = $getReport;

                                // $report[] = !is_null($getReport)?$getReport:'di data item tidak ada';
                                $reports[] = !is_null($getReport) ? $getReport : null;
                            }
                        }

                        // dd(([$item_id, $report, $checkX]));
                        // check knp jika item id yang ada di purchases_detail 1 data, tetapi tidka bisa di get

                        $countItemPurchase = array_sum($countItemPurchase);

                        if ($showChart == 1) {

                            foreach ($reports as $reportKey => $reportVal) {
                                foreach ($listDates as $keyDate => $listDate) {
                                    // jika di table purchases ada
                                    if ($reportVal->purchases_true == 1) {
                                        $_dataChart = DB::table('pembelian_details')
                                            ->leftJoin('pembelian', 'pembelian_details.id_pembelian', '=', 'pembelian.id')
                                            ->leftJoin('master_items', 'pembelian_details.id_item', '=', 'master_items.id')
                                            ->leftJoin('categories', 'master_items.category_id', '=', 'categories.id')
                                            ->select([
                                                'pembelian_details.id_item as item_id',
                                                'master_items.nama_item as name_item',
                                                'pembelian.tanggal as date_order',
                                                DB::raw('SUM(pembelian_details.qty) as total_quantity'),
                                            ])
                                            ->groupBy('pembelian.tanggal')
                                            ->where([
                                                ['pembelian.tanggal', $listDate],
                                                ['pembelian_details.id_item', $reportVal->id_item]
                                            ])
                                            ->first();
                                    } else {
                                        // jika di table purchases tidak ada
                                        $_dataChart = DB::table('master_items')
                                            ->select([
                                                'master_items.id as item_id',
                                                'master_items.nama_item as name_item',
                                            ])
                                            ->selectRaw($listDate . ' as date_order')
                                            ->selectRaw('0 as total_quantity')
                                            ->where([
                                                ['master_items.id', $reportVal->id_item]

                                            ])
                                            // ->whereIn('items.id', [8])
                                            ->first();
                                    }

                                    if (is_null($_dataChart)) {
                                        $_data[$reportKey][$keyDate] = 0;
                                    } else {
                                        $_data[$reportKey][$keyDate] = $_dataChart->total_quantity;
                                    }
                                    $_xxx[] = $_dataChart;
                                }

                                $randColor = Helper::randColor2();
                                $_datasets[] = json_encode([
                                    'legendText' => 'text',
                                    'label' => $reportVal->name_item,
                                    'backgroundColor' => $randColor . 'A0',
                                    'borderColor' => $randColor . '66',
                                    'data' => $_data[$reportKey],
                                    'pointStyle' => "circle",
                                    'pointRadius' => 3,
                                    'pointHoverRadius' => 7,
                                    'pointColor' => "#efefef",
                                    'pointBackgroundColor' => $randColor . 'A0',
                                ]);
                            }
                            // dd($_datasets);
                        }
                    } else {
                        // jika item_id kosong
                        // if(is_null($request->item_id)){
                        //     $result = config('global.errors.E010');
                        //     return response()->json($result, 404);
                        // }
                        foreach ($item_id as $keyItem => $item) {
                            # code...
                            $checkItemPurchase = DB::table('pembelian_details')->where('id_item', $item)->count();
                            if ($checkItemPurchase > 0) {
                                $getCountItemPurchase = (int) DB::table('pembelian_details')
                                    ->leftJoin('pembelian', 'pembelian_details.id_pembelian', '=', 'pembelian.id')
                                    ->where([
                                        ['pembelian.tanggal', '>=', $dateSeparator[0]],
                                        ['pembelian.tanggal', '<=', $dateSeparator[1]],
                                        ['pembelian_details.id_item', $item]
                                    ])
                                    // ->whereIn('purchases_details.item_id', $request->item_id)
                                    ->sum('qty');
                                $countItemPurchase[] = $getCountItemPurchase;
                                $getReport = DB::table('pembelian')
                                    ->leftJoin('pembelian_details', 'pembelian_details.id_pembelian', '=', 'pembelian.id')
                                    ->leftJoin('master_items', 'pembelian_details.id_item', '=', 'master_items.id')
                                    ->leftJoin('categories', 'master_items.category_id', '=', 'categories.id')
                                    ->select([
                                        'pembelian_details.id_item as item_id',
                                        'master_items.kode_item as code_item',
                                        'master_items.nama_item as name_item',
                                        'categories.name as name_category',
                                        DB::raw('SUM(pembelian_details.qty) as total_quantity'),
                                    ])
                                    ->orderBy('total_quantity', 'desc')
                                    ->groupBy('pembelian_details.id_item')
                                    ->where([
                                        ['pembelian.tanggal', '>=', $dateSeparator[0]],
                                        ['pembelian.tanggal', '<=', $dateSeparator[1]],
                                        ['pembelian_details.id_item', $item],
                                    ])
                                    // ->whereIn('purchases_details.item_id', $request->item_id)
                                    ->first();
                                $reports[] = $getReport;
                            } else {

                                $countItemPurchase[] = 0;
                                $getReport = DB::table('master_items')
                                    ->leftJoin('pembelian_details', 'pembelian_details.id_item', '=', 'master_items.id')
                                    ->leftJoin('pembelian', 'pembelian_details.id_pembelian', '=', 'pembelian.id')
                                    ->leftJoin('categories', 'master_items.category_id', '=', 'categories.id')
                                    ->select([
                                        'master_items.id as item_id',
                                        'master_items.kode_item as code_item',
                                        'master_items.nama_item as name_item',
                                        'categories.name as name_category',
                                    ])
                                    ->selectRaw('IFNULL(SUM(pembelian_details.qty), 0)  as total_quantity')
                                    ->orderBy('total_quantity', 'desc')
                                    ->where([
                                        ['pembelian.tanggal', '>=', $dateSeparator[0]],
                                        ['pembelian.tanggal', '<=', $dateSeparator[1]],
                                        ['pembelian_details.id_item', $item]

                                    ])
                                    // ->whereIn('items.id', [8])
                                    ->first();
                                $reports[] = $getReport;
                            }
                        }
                        $countItemPurchase = array_sum($countItemPurchase);

                        // jika pakai chart
                        if ($showChart == 1) {
                            foreach ($reports as $reportKey => $reportVal) {
                                foreach ($listDates as $keyDate => $listDate) {
                                    $_dataChart = DB::table('pembelian_details')
                                        ->leftJoin('pembelian', 'pembelian_details.id_pembelian', '=', 'pembelian.id')
                                        ->leftJoin('master_items', 'pembelian_details.id_item', '=', 'master_items.id')
                                        ->leftJoin('categories', 'master_items.category_id', '=', 'categories.id')
                                        ->select([
                                            'pembelian_details.id_item as item_id',
                                            'master_items.nama_item as name_item',
                                            'pembelian.tanggal as date_order',
                                            DB::raw('SUM(pembelian_details.qty) as total_quantity'),
                                        ])
                                        ->groupBy('pembelian.tanggal')
                                        ->where([
                                            ['pembelian.tanggal', $listDate],
                                            ['pembelian_details.id_item', $reportVal->item_id]
                                        ])
                                        ->first();
                                    if (is_null($_dataChart)) {
                                        $_data[$reportKey][$keyDate] = 0;
                                    } else {
                                        $_data[$reportKey][$keyDate] = $_dataChart->total_quantity;
                                    }
                                    $_xxx[] = $_dataChart;
                                }
                                $randColor = Helper::randColor2();
                                $_datasets[] = json_encode([
                                    'legendText' => 'text',
                                    'label' => strtoupper($reportVal->name_item),
                                    'backgroundColor' => $randColor . 'A0',
                                    'borderColor' => $randColor . '66',
                                    'data' => $_data[$reportKey],
                                    'pointStyle' => "circle",
                                    'pointRadius' => 3,
                                    'pointHoverRadius' => 7,
                                    'pointColor' => "#efefef",
                                    'pointBackgroundColor' => $randColor . 'A0',
                                ]);
                            }
                        }
                    }
                    $chart = [
                        'datePurchaseChart' => 'Pembelian Item: ' . Helper::setDate($dateSeparator[0], 'fullDateId') . ' - ' . Helper::setDate($dateSeparator[1], 'fullDateId'),
                        'totalPurchaseChart' => 0,
                        'transactionPurchaseChart' => 0,
                        'itemPurchaseChart' => $countItemPurchase,
                        'label' => $listDates,
                        'datasets' => $_datasets,

                    ];
                    // sort Report desc total_quantity
                    foreach ($reports as $key => $reportVal) {
                        $report[] = (array) $reportVal;
                    }
                    usort($report, function ($x, $y) {
                        return $y['total_quantity'] <=> $x['total_quantity'];
                    });
                }
 

                $result = [
                    'report' => $report,
                    'chart' => $chart,
                ];
                $result = array_merge($result, config('global.success.S000'));
            } catch (\Throwable $th) {
                //throw $th;
                $result = config('global.errors.E010');
                $result = $th->getMessage();
            }

            return response()->json($result);
        } else {
            session()->flash('notifikasi', [
                "icon" => config('global.errors.E002.status'),
                "title" => config('global.errors.E002.code'),
                "message" =>  config('global.errors.E002.message'),
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