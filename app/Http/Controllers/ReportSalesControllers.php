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

class ReportSalesControllers extends Controller
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
        if (Helper::checkACL('sales_report', 'r')) {
            $items = DB::table('items')
                ->join('categories', 'categories.id', '=', 'items.category_id')
                ->select([
                    'items.id as id',
                    'items.code as code',
                    'items.name as name',
                    'items.category_id as category_id',
                    'categories.name as category_name'
                ])
                ->Where([
                    ['items.status', '1'],
                    ['categories.parent', '2'],
                ])
                ->orderBy('items.name', 'asc')
                ->get();
            $var = [
                'nav' => 'report',
                'subNav' => 'sales_report',
                'title' => 'Laporan Penjualan',
                'items' => $items,
            ];
            return view('report.sales.index', $var);
        } else {
            session()->flash('notifikasi', [
                "icon" => config('global.errors.E002.status'),
                "title" => config('global.errors.E002.code'),
                "message" =>  config('global.errors.E002.message'),
            ]);
            return redirect('dashboard');
        }
    }

    public function index_kasir(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('sales');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('kasir', function($data){
                        $nama_kasir = DB::table('users')->where('id', $data->user_created)->first()->name;
                        return $nama_kasir;
                    })
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('range_date'))) {
                            $instance->where(function($w) use($request){
                               $rangeDate = $request->get('range_date');
                               $explode = explode(" - ", $rangeDate);

                               $w->whereDate('date_order','>=', $explode[0])
                                 ->whereDate('date_order','<=', $explode[1]);
                           });
                       }
                    })
                    ->rawColumns(['kasir'])
                    ->make(true);
        }

        $var = [
            'nav' => 'report_kasir',
            'subNav' => 'kasir_report',
            'title' => 'Laporan Penjualan Kasir',
        ];
        return view('report.sales.index_kasir', $var);
    }

    public function report(Request $request)
    {
        if (Helper::checkACL('sales_report', 'r')) {
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

                $listDates = Helper::generateListDate($dateSeparator[0], $dateSeparator[1]);
                if (count($listDates) > 31) {
                    $result = config('global.errors.E015');
                    return response()->json($result);
                }
                // jika jenis report global
                if ((is_null($typeReport)) || ($typeReport == '')) {
                    // query creator
                    $totalSales = DB::table('sales')
                        ->where([
                            ['status', 'close'],
                            ['date_order', '>=', $dateSeparator[0]],
                            ['date_order', '<=', $dateSeparator[1]],
                        ])->sum('total');
                    $countSales = DB::table('sales')
                        ->where([
                            ['status', 'close'],
                            ['date_order', '>=', $dateSeparator[0]],
                            ['date_order', '<=', $dateSeparator[1]],
                        ])->count();
                    $countItemSales = DB::table('sales_details')
                        ->leftJoin('sales', 'sales_details.sales_id', '=', 'sales.code')
                        ->where([
                            ['sales.status', 'close'],
                            ['sales.date_order', '>=', $dateSeparator[0]],
                            ['sales.date_order', '<=', $dateSeparator[1]],
                        ])
                        ->sum('quantity');

                    $report = DB::table('sales')->select(
                        'code',
                        'sub_total',
                        'tax',
                        // rumus pajak dari total : total-total(1+rate%)
                        // DB::raw('TRIM( SUM( (sub_total - ( sub_total*(discount/100)) ))+0 as tax'),
                        DB::raw('TRIM(SUM(sub_total*(discount/100)))+0 as discount'),
                        'total',
                    )
                        ->selectRaw($totalSales . ' as total_sales')
                        ->selectRaw($countSales . ' as count_sales')
                        ->orderBy('date_order')
                        ->groupBy('code')
                        ->where([
                            ['status', 'close'],
                            ['date_order', '>=', $dateSeparator[0]],
                            ['date_order', '<=', $dateSeparator[1]],
                        ])
                        ->get();

                    // jika pakai chart
                    if ($showChart == 1) {
                        $listDates = Helper::generateListDate($dateSeparator[0], $dateSeparator[1]);

                        foreach ($listDates as $keyDate => $listDate) {
                            $_dataChart = DB::table('sales')
                                ->select(
                                    'date_order',
                                    DB::raw('SUM(total) as total'),
                                )
                                ->groupBy('date_order')
                                ->where([
                                    ['status', 'close'],
                                    ['date_order', $listDate],
                                ])
                                ->first();
                            if (is_null($_dataChart)) {
                                $_label[] = $listDate;
                                $_data[] = 0;
                            } else {
                                $_label[] = $_dataChart->date_order;
                                $_data[] = $_dataChart->total;
                            }
                        }
                        $_datasets[] = json_encode([
                            'label' => "PENJUALAN",
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
                            'dateSalesChart' => 'Penjualan : ' . Helper::setDate($dateSeparator[0], 'fullDateId') . ' - ' . Helper::setDate($dateSeparator[1], 'fullDateId'),
                            'totalSalesChart' => $totalSales,
                            'transactionSalesChart' => $countSales,
                            'itemSalesChart' => $countItemSales,
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

                        $item_id = DB::table('items')
                            ->join('categories', 'categories.id', '=', 'items.category_id')
                            ->select([
                                'items.id as id',
                            ])
                            ->Where([
                                ['items.status', '1'],
                                ['categories.parent', '2'],
                            ])
                            ->orderBy('items.name', 'asc')
                            ->get();
                        foreach ($item_id as $keyItem => $item) {
                            # code...
                            $checkItemSales = DB::table('sales')
                                ->leftJoin('sales_details', 'sales_details.sales_id', '=', 'sales.code')
                                ->where([
                                    ['sales.status', 'close'],
                                    ['sales.date_order', '>=', $dateSeparator[0]],
                                    ['sales.date_order', '<=', $dateSeparator[1]],
                                    ['sales_details.item_id', $item->id],
                                ])
                                ->count();
                            $checkX[] = $checkItemSales;
                            // jika item ada
                            if ($checkItemSales >= 1) {

                                $getCountItemSales = (int) DB::table('sales_details')
                                    ->leftJoin('sales', 'sales_details.sales_id', '=', 'sales.code')
                                    ->where([
                                        ['sales.status', 'close'],
                                        ['sales.date_order', '>=', $dateSeparator[0]],
                                        ['sales.date_order', '<=', $dateSeparator[1]],
                                        ['sales_details.item_id', $item->id]
                                    ])
                                    ->sum('quantity');

                                $countItemSales[] = $getCountItemSales;

                                $getReport = DB::table('sales')
                                    ->leftJoin('sales_details', 'sales_details.sales_id', '=', 'sales.code')
                                    // ->leftJoin('items', 'items.id', '=', 'sales_details.item_id')
                                    ->leftJoin('items', 'sales_details.item_id', '=', 'items.id')
                                    ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                                    ->select([
                                        'items.id as id_item',
                                        'items.code as code_item',
                                        'items.name as name_item',
                                        'categories.name as name_category',
                                        DB::raw('SUM(sales_details.quantity) as total_quantity'),
                                    ])
                                    // ->orderBy('total_quantity', 'desc')
                                    ->selectRaw('1  as sales_true')
                                    ->groupBy('items.id')
                                    ->where([
                                        ['sales.status', 'close'],
                                        ['sales.date_order', '>=', $dateSeparator[0]],
                                        ['sales.date_order', '<=', $dateSeparator[1]],
                                        ['sales_details.item_id', $item->id],
                                    ])
                                    ->first();

                                // $report[] = $getReport;

                                $reports[] = !is_null($getReport) ? $getReport : null;
                            } else {

                                $countItemSales[] = 0;
                                $getReport = DB::table('items')
                                    ->leftJoin('sales_details', 'sales_details.item_id', '=', 'items.id')
                                    ->leftJoin('sales', 'sales_details.sales_id', '=', 'sales.code')
                                    ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                                    ->select([
                                        'items.id as id_item',
                                        'items.code as code_item',
                                        'items.name as name_item',
                                        'categories.name as name_category',
                                    ])
                                    ->selectRaw('IFNULL(SUM(sales_details.quantity), 0)  as total_quantity')
                                    ->selectRaw('0  as sales_true')
                                    // ->orderBy('total_quantity', 'desc')
                                    ->where([
                                        ['sales.status', 'close'],
                                        ['sales.date_order', '>=', $dateSeparator[0]],
                                        ['sales.date_order', '<=', $dateSeparator[1]],
                                        ['items.id', $item->id]

                                    ])
                                    // ->whereIn('items.id', [8])
                                    ->first();
                                // $report[] = $getReport;

                                // $report[] = !is_null($getReport)?$getReport:'di data item tidak ada';
                                $reports[] = !is_null($getReport) ? $getReport : null;
                            }
                        }

                        // dd(([$item_id, $report, $checkX]));
                        // check knp jika item id yang ada di sales_detail 1 data, tetapi tidka bisa di get

                        $countItemSales = array_sum($countItemSales);

                        if ($showChart == 1) {

                            foreach ($reports as $reportKey => $reportVal) {
                                foreach ($listDates as $keyDate => $listDate) {
                                    // jika di table sales ada
                                    if ($reportVal->sales_true == 1) {
                                        $_dataChart = DB::table('sales_details')
                                            ->leftJoin('sales', 'sales_details.sales_id', '=', 'sales.code')
                                            ->leftJoin('items', 'sales_details.item_id', '=', 'items.id')
                                            ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                                            ->select([
                                                'sales_details.item_id as item_id',
                                                'items.name as name_item',
                                                'sales.date_order as date_order',
                                                DB::raw('SUM(sales_details.quantity) as total_quantity'),
                                            ])
                                            ->groupBy('sales.date_order')
                                            ->where([
                                                ['sales.status', 'close'],
                                                ['sales.date_order', $listDate],
                                                ['sales_details.item_id', $reportVal->id_item]
                                            ])
                                            ->first();
                                    } else {
                                        // jika di table sales tidak ada
                                        $_dataChart = DB::table('items')
                                            ->select([
                                                'items.id as item_id',
                                                'items.name as name_item',
                                            ])
                                            ->selectRaw($listDate . ' as date_order')
                                            ->selectRaw('0 as total_quantity')
                                            ->where([
                                                ['items.id', $reportVal->id_item]

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
                            $checkItemSales = DB::table('sales_details')->where('item_id', $item)->count();
                            if ($checkItemSales > 0) {
                                $getCountItemSales = (int) DB::table('sales_details')
                                    ->leftJoin('sales', 'sales_details.sales_id', '=', 'sales.code')
                                    ->where([
                                        ['sales.status', 'close'],
                                        ['sales.date_order', '>=', $dateSeparator[0]],
                                        ['sales.date_order', '<=', $dateSeparator[1]],
                                        ['sales_details.item_id', $item]
                                    ])
                                    // ->whereIn('sales_details.item_id', $request->item_id)
                                    ->sum('quantity');
                                $countItemSales[] = $getCountItemSales;
                                $getReport = DB::table('sales')
                                    ->leftJoin('sales_details', 'sales_details.sales_id', '=', 'sales.code')
                                    ->leftJoin('items', 'sales_details.item_id', '=', 'items.id')
                                    ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                                    ->select([
                                        'sales_details.item_id as item_id',
                                        'items.code as code_item',
                                        'items.name as name_item',
                                        'categories.name as name_category',
                                        DB::raw('SUM(sales_details.quantity) as total_quantity'),
                                    ])
                                    ->orderBy('total_quantity', 'desc')
                                    ->groupBy('sales_details.item_id')
                                    ->where([
                                        ['sales.status', 'close'],
                                        ['sales.date_order', '>=', $dateSeparator[0]],
                                        ['sales.date_order', '<=', $dateSeparator[1]],
                                        ['sales_details.item_id', $item],
                                    ])
                                    // ->whereIn('sales_details.item_id', $request->item_id)
                                    ->first();
                                $reports[] = $getReport;
                            } else {

                                $countItemSales[] = 0;
                                $getReport = DB::table('items')
                                    ->leftJoin('sales_details', 'sales_details.item_id', '=', 'items.id')
                                    ->leftJoin('sales', 'sales_details.sales_id', '=', 'sales.code')
                                    ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                                    ->select([
                                        'items.id as item_id',
                                        'items.code as code_item',
                                        'items.name as name_item',
                                        'categories.name as name_category',
                                    ])
                                    ->selectRaw('IFNULL(SUM(sales_details.quantity), 0)  as total_quantity')
                                    ->orderBy('total_quantity', 'desc')
                                    ->where([
                                        ['sales.status', 'close'],
                                        ['sales.date_order', '>=', $dateSeparator[0]],
                                        ['sales.date_order', '<=', $dateSeparator[1]],
                                        ['sales_details.item_id', $item]

                                    ])
                                    // ->whereIn('items.id', [8])
                                    ->first();
                                $reports[] = $getReport;
                            }
                        }
                        $countItemSales = array_sum($countItemSales);

                        // jika pakai chart
                        if ($showChart == 1) {
                            foreach ($reports as $reportKey => $reportVal) {
                                foreach ($listDates as $keyDate => $listDate) {
                                    $_dataChart = DB::table('sales_details')
                                        ->leftJoin('sales', 'sales_details.sales_id', '=', 'sales.code')
                                        ->leftJoin('items', 'sales_details.item_id', '=', 'items.id')
                                        ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                                        ->select([
                                            'sales_details.item_id as item_id',
                                            'items.name as name_item',
                                            'sales.date_order as date_order',
                                            DB::raw('SUM(sales_details.quantity) as total_quantity'),
                                        ])
                                        ->groupBy('sales.date_order')
                                        ->where([
                                            ['sales.status', 'close'],
                                            ['sales.date_order', $listDate],
                                            ['sales_details.item_id', $reportVal->item_id]
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
                        'dateSalesChart' => 'Penjualan Item: ' . Helper::setDate($dateSeparator[0], 'fullDateId') . ' - ' . Helper::setDate($dateSeparator[1], 'fullDateId'),
                        'totalSalesChart' => 0,
                        'transactionSalesChart' => 0,
                        'itemSalesChart' => $countItemSales,
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
                // return response()->json($result, 404);
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

    public function salesPendapatan()
    {
        $var = [
            'nav' => 'report',
            'subNav' => 'pendapatan_sales_report',
            'title' => 'Laporan Pendapatan Penjualan'
        ];
        return view("report.sales.index_pendapatan", $var);
    }

    public function salesPendapatanFilter(Request $request)
    {
        $dateSeparator = explode(" - ", $request->range_date);
        $sales = DB::table('sales')
                    ->where([
                        ['sales.status', 'close'],
                        ['sales.date_order', '>=', $dateSeparator[0]],
                        ['sales.date_order', '<=', $dateSeparator[1]]
                    ])
                    ->get();
        $pembelian = DB::table('pembelian')
                    ->where([
                        ['tanggal', '>=', $dateSeparator[0]],
                        ['tanggal', '<=', $dateSeparator[1]]
                    ])
                    ->get();

        $var = [
            'nav' => 'report',
            'subNav' => 'pendapatan_sales_report',
            'title' => 'Laporan Pendapatan Penjualan',
            'sales' => $sales,
            'pembelian' => $pembelian
        ];
        return view("report.sales.index_pendapatan", $var);
    }
}
