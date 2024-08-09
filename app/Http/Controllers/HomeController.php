<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $var = [
            'nav' => 'dashboard',
            'subNav' => 'dashboard',
            'title' => 'Dashboard',
        ];

        if (Helper::checkACL('dashboard', 'r')) {
          
            return view('home.dashboard', $var);
        } else {
            return view('home.index', $var);
        }
    }
    public function dashboardAjax(Request $request)
    {
        if (Helper::checkACL('dashboard', 'r')) {
            if ($request->ajax()) {
                try {
                    // pendapatan harian(rp)
                    $dailySales = DB::table('sales')
                        ->where([
                            ['status', 'close'],
                            ['date_order', '>=', Date('Y-m-d 00:00:00')],
                        ])
                        ->sum('total');

                    // item terjual harian
                    $countDailyItemSales = DB::table('sales_details')
                        ->leftJoin('sales', 'sales_details.sales_id', '=', 'sales.code')
                        ->where([
                            ['sales.status', 'close'],
                            ['date_order', '=', Date('Y-m-d')],
                        ])
                        ->sum('quantity');

                    // total nota order harian
                    $countDailySales = DB::table('sales')
                        ->where([
                            ['status', 'close'],
                            ['date_order', '=', Date('Y-m-d')],
                        ])->count();

                    // pendaftaran member baru harian
                    $dailyMember = DB::table('memberships')
                        ->where([
                            ['status', 'active'],
                            ['created_at', '>=', Date('Y-m-d 00:00:00')],
                        ])->count();
                    // graph line penjualan vs pembelian
                    // graph pie top ten item

                    // total penjualan bulan lalu
                    $monthlySales = DB::table('sales')
                        ->where([
                            ['status', 'close'],
                        ])
                        ->whereMonth('date_order', Date('m'))
                        ->sum('total');

                    // total pembelian bulan ini
                    $monthlyPurchases = DB::table('purchases')
                        ->where([
                            ['status', 'close'],
                        ])
                        ->whereMonth('date_order', Date('m'))
                        ->sum('total');

                    // total penjualan item bulan ini

                    $countMonthlyItemSales = DB::table('sales_details')
                        ->leftJoin('sales', 'sales_details.sales_id', '=', 'sales.code')
                        ->where([
                            ['sales.status', 'close'],
                        ])
                        ->whereMonth('date_order', Date('m'))
                        ->sum('quantity');

                    // total member bulan ini
                    $monthlyMember = DB::table('memberships')
                        ->where([
                            ['status', 'active'],
                        ])
                        ->whereMonth('created_at', Date('m'))
                        ->count();

                    // ===============   chart Line
                    // ambil tanggal
                    $dateSeparator = [
                        date("Y-n-j", strtotime("first day of previous month")),
                        date("Y-n-j", strtotime("last day of previous month")),
                    ];
                    $dateSeparatorNow = [
                        date('d.m.Y', strtotime('first day of this month')),
                        date('d.m.Y', strtotime('last day of this month')),
                    ];
                    $listDates = Helper::generateListDate($dateSeparator[0], $dateSeparator[1]);
                    $listDatesNow = Helper::generateListDate($dateSeparatorNow[0], $dateSeparatorNow[1]);
                    // generate object chartline
                    foreach ($listDates as $keyDate => $listDate) {
                        // penjualan bulan lalu
                        $_dataChartSales = DB::table('sales')
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
                        if (is_null($_dataChartSales)) {
                            $_dataSales[] = 0;
                        } else {
                            $_dataSales[] = $_dataChartSales->total;
                        }
                        // pembelian  bulan lalu
                        $_dataChartPurchase = DB::table('purchases')
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
                        if (is_null($_dataChartPurchase)) {
                            $_dataPurchase[] = 0;
                        } else {
                            $_dataPurchase[] = $_dataChartPurchase->total;
                        }
                    }
                    foreach ($listDatesNow as $listDateNow) {
                        // penjualan bulan ini
                        $_dataChartSalesNow = DB::table('sales')
                            ->select(
                                'date_order',
                                DB::raw('SUM(total) as total'),
                            )
                            ->groupBy('date_order')
                            ->where([
                                ['status', 'close'],
                                ['date_order', $listDateNow],
                            ])
                            ->first();
                        if (is_null($_dataChartSalesNow)) {
                            $_dataSalesNow[] = 0;
                        } else {
                            $_dataSalesNow[] = $_dataChartSalesNow->total;
                        }
                        // pembelian  bulan ini
                        $_dataChartPurchaseNow = DB::table('purchases')
                            ->select(
                                'date_order',
                                DB::raw('SUM(total) as total'),
                            )
                            ->groupBy('date_order')
                            ->where([
                                ['status', 'close'],
                                ['date_order', $listDateNow],
                            ])
                            ->first();
                        if (is_null($_dataChartPurchaseNow)) {
                            $_dataPurchaseNow[] = 0;
                        } else {
                            $_dataPurchaseNow[] = $_dataChartPurchaseNow->total;
                        }
                    }

                    $_datasets[] = json_encode(
                        [
                            'label' => "Penj. " . Helper::setDate(date('m') - 1, 'bulan'),
                            'backgroundColor' => "rgba(0,188,140,0.3)",
                            'borderColor' => "rgba(0,188,140,0.8)",
                            'data' => $_dataSales,
                            'pointStyle' => "circle",
                            'pointRadius' => 3,
                            'pointHoverRadius' => 7,
                            'pointColor' => "#efefef",
                            'pointBackgroundColor' => "#efefef",
                        ]
                    );
                    $_datasets[] = json_encode([

                        'label' => "Pemb. " . Helper::setDate(date('m') - 1, 'bulan'),
                        'backgroundColor' => "rgba(220,53,69,0.3)",
                        'borderColor' => "rgba(220,53,69,0.8)",
                        'data' => $_dataPurchase,
                        'pointStyle' => "circle",
                        'pointRadius' => 3,
                        'pointHoverRadius' => 7,
                        'pointColor' => "#efefef",
                        'pointBackgroundColor' => "#efefef",
                    ]);

                    $_datasets[] = json_encode(
                        [
                            'label' => "Penj. " . Helper::setDate(date('m'), 'bulan'),
                            'backgroundColor' => "rgba(27,152,172,0.3)",
                            'borderColor' => "rgba(27,152,172,0.8)",
                            'data' => $_dataSalesNow,
                            'pointStyle' => "circle",
                            'pointRadius' => 3,
                            'pointHoverRadius' => 7,
                            'pointColor' => "#efefef",
                            'pointBackgroundColor' => "#efefef",
                        ]
                    );
                    $_datasets[] = json_encode([

                        'label' => "Pemb. " . Helper::setDate(date('m'), 'bulan'),
                        'backgroundColor' => "rgba(255,102,1,0.3)",
                        'borderColor' => "rgba(255,102,1,0.8)",
                        'data' => $_dataPurchaseNow,
                        'pointStyle' => "circle",
                        'pointRadius' => 3,
                        'pointHoverRadius' => 7,
                        'pointColor' => "#efefef",
                        'pointBackgroundColor' => "#efefef",
                    ]);
                    for ($i = 0; $i < 31; $i++) {
                        $counter = 1 + $i;
                        $listLabel[] = 'Hari-' . $counter;
                    }
                    // dd($listLabel);
                    $chartLine = [
                        'label' => $listLabel,
                        'datasets' => $_datasets,
                    ];

                    // ===============   end chart Line
                    // chart Donut
                    // ambil data top 10
                    $itemTopTens = DB::table('sales_details')
                        ->leftJoin('sales', 'sales_details.sales_id', '=', 'sales.code')
                        ->leftJoin('items', 'sales_details.item_id', '=', 'items.id')
                        ->select([
                            'items.name as name_item',
                            'sales_details.item_id',
                            DB::raw('SUM(quantity) as quantity_sum'),

                        ])
                        ->where([
                            ['sales.status', 'close'],
                        ])
                        // ->selectRaw('SUM(quantity) as quantity_sumx')
                        ->whereMonth('sales.date_order', Date('m') - 1)
                        ->orderBy('date_order')
                        ->groupBy('sales_details.item_id')
                        ->limit(10)
                        ->get();
                    // if (is_null($itemTopTens)) {
                    //     foreach ($itemTopTens as $itemTopTen) {
                    //         $randColor = Helper::randColor2();
                    //         $_backgroundColorDonut[] = $randColor . 'A0';
                    //         $_borderColorDonut[] = $randColor . '66';
                    //         $_labelDonut[] = $itemTopTen->name_item;
                    //         $_dataDonut[] = $itemTopTen->quantity_sum;
                    //     }

                    //     $_datasetDonut[] = json_encode([
                    //         'backgroundColor' => $_backgroundColorDonut,
                    //         'borderColor' => $_borderColorDonut,
                    //         'data' => $_dataDonut,
                    //         'pointStyle' => "circle",
                    //         'pointRadius' => 3,
                    //         'pointHoverRadius' => 7,
                    //         'pointColor' => "#efefef",
                    //         'pointBackgroundColor' => "#efefef",
                    //     ]);
                    //     $chartDonut = [
                    //         'label' => $_labelDonut,
                    //         'datasets' => $_datasetDonut,
                    //     ];
                    // } else {
                    //     $chartDonut = [
                    //         'label' => [],
                    //         'datasets' => [],
                    //     ];
                    // }
                    $_backgroundColorDonut = [];
                    $_borderColorDonut = [];
                    $_labelDonut = [];
                    $_dataDonut = [];
                    foreach ($itemTopTens as $itemTopTen) {
                        $randColor = Helper::randColor2();
                        $_backgroundColorDonut[] = $randColor . 'A0';
                        $_borderColorDonut[] = $randColor . '66';
                        $_labelDonut[] = $itemTopTen->name_item;
                        $_dataDonut[] = $itemTopTen->quantity_sum;
                    }

                    $_datasetDonut[] = json_encode([
                        'backgroundColor' => $_backgroundColorDonut,
                        'borderColor' => $_borderColorDonut,
                        'data' => $_dataDonut,
                        'pointStyle' => "circle",
                        'pointRadius' => 3,
                        'pointHoverRadius' => 7,
                        'pointColor' => "#efefef",
                        'pointBackgroundColor' => "#efefef",
                    ]);
                    $chartDonut = [
                        'label' => $_labelDonut,
                        'datasets' => $_datasetDonut,
                    ];
                    $sectionOne = [
                        'dailySales' => $dailySales,
                        'countDailyItemSales' => $countDailyItemSales,
                        'countDailySales' => $countDailySales,
                        'dailyMember' => $dailyMember,
                    ];

                    $sectionTwo = [
                        'chartLine' => $chartLine,
                        'chartDonut' => $chartDonut,
                        'monthlySales' => $monthlySales,
                        'monthlyPurchases' => $monthlyPurchases,
                        'countMonthlyItemSales' => $countMonthlyItemSales,
                        'monthlyMember' => $monthlyMember,
                    ];
                    $result = [
                        'sectionOne' => $sectionOne,
                        'sectionTwo' => $sectionTwo,
                    ];

                    $data = array_merge(
                        config('global.success.S000'),
                        $result,

                    );
                } catch (\Throwable $th) {
                    $data = config('global.errors.E011');
                    // $data = $th->getMessage();
                }
                return response()->json($data);
            }
        }
    }
    public function iframe()
    {
        $nav = ['nav' => 'iframe', 'subNav' => 'iframe', 'title' => 'Tools IFrame'];
        return view('home.iframe', $nav);
    }
    public function gambar($type, $id)
    {
        switch ($type) {
            case 'user':
                if (Auth::check()) {
                    $image = Storage::path('user/' . Auth::id());
                    if (!file_exists($image)) {
                        $image = Storage::path('noImage.png');
                    }
                    $mime = mime_content_type($image);
                    $headers = array(
                        'Content-Type:' . $mime,
                    );
                }
                break;

            default:
                # code...
                $image = Storage::path('noImage.png');
                $mime = mime_content_type($image);

                break;
        }
        $headers = array(
            'Content-Type:' . $mime,
        );
        return response()->file($image, $headers);
    }
}
