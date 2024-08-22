<html>

<head>
    <title>{{$sales->code}}</title>
    <style media="print">
        * {
            font-size: 9px;
            font-family: Consolas, Menlo, Monaco, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, serif;
        }

        body {
            align-content: center;
            justify-content: center;
            justify-items: flex-start;
            display: grid;
        }

        .eightpx {
            font-size: 6pt;
            letter-spacing: -0.7px;

        }

        .ninepx {
            font-size: 7pt;
            letter-spacing: -0.5px;
        }

        .twentenpx {
            font-size: 12pt;
            letter-spacing: -1px;
        }

        hr {
            display: block;
            margin-top: 0.5em;
            margin-bottom: 0.5em;
            margin-left: 5mm;
            margin-right: 53mm;
            border-top: 1px dashed;
        }

        @page {
            size: 58mm {{$count <=5 ? 150 - (5 - $count) * 6: 150 + ($count - 5) * 6}}mm;
            margin: 3mm;
        }

        @media print {

            .hidden-print,
            .hidden-print * {
                display: none !important;
            }
        }

        table {
            width: 50mm;
            border-collapse: collapse;
            border: 0px
        }

        hr {
            margin: 0;
            border-top: 1px dashed;
        }
    </style>
</head>

<body>
    <button class="hidden-print" onclick="balikKasir()">Kembali</button>
    <button class="hidden-print" onclick="window.close()" autofocus>Tutup (Q)</button>
    <button class="hidden-print" onclick="window.print()">Print (P)</button>

    <div class="print">
        <table>
            <tbody>
                <tr>
                    <td colspan="4" align="center">
                        <img src="/img/configurations/1" height="50px"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" align="center" style="padding-top: 2mm;padding-bottom: 2mm;">
                        <b class="twentenpx">{{ $company->name }}</b></br></br>
                        {!! $company->address1 !!}</br>
                        {{ $company->mobile }} - {{ $company->email }}</br>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td class="eightpx" align="left">
                        Tanggal 
                    </td>
                    <td>:</td>
                    <td class="eightpx">
                        {{ date('d/m/Y', strtotime($sales->date_order)) }}
                    </td>
                    <td class="eightpx" align="left">
                        No.: {{$sales->code}}
                    </td>
                </tr>
                <tr>
                </tr>
                <tr>
                    <td class="eightpx" align="left">
                        Kasir
                    </td>
                    <td>:</td>
                    <td>
                        {{(Auth::user()->name)=='administrator'? 'admin' : Auth::user()->name}}
                    </td>
                    <td class="eightpx" align="left">
                        Pembayaran : {{ $sales->pMethod.' '.(($sales->pMethodDetail) ? $sales->pMethodDetail : "") }}
                    </td>
                    {{-- <td class="eightpx" align="left">
                        Pelanggan : {{$sales->customer}}
                    </td> --}}
                </tr>
                <tr>
                    <td class="eightpx" align="left">
                        Customer
                    </td>
                    <td>:</td>
                    <td>
                        {{ $sales->customer }}
                    </td>

                    {{-- <td class="eightpx" align="left">
                        Member ID : {{ is_null($sales->membership_code) ? '-': '#'.$sales->membership_code  }}
                    </td> --}}
                </tr>

            </tbody>
        </table>
        <table>
            {{-- <thead>
                <tr>
                    <td colspan="4">
                        <hr>
                        <hr>
                    </td>
                </tr>
                <tr>
                    <th class="eightpx" style="width:45%;" align="left">Item</th>
                    <th class="eightpx" style="width:5%;" align="center">Qty</th>
                    <th class="eightpx" style="width:20%;" align="center">Harga</th>
                    <th class="eightpx" style="width:30%;" align="center">Total</th>
                </tr>
            </thead> --}}
            <tbody>
                <tr>
                    <td colspan='4'>
                        <hr>
                        <hr>
                    </td>
                </tr>

                @foreach ($sales_details as $sales_detail)
                <tr style="">
                    <td class="eightpx" style="width:45%; padding-bottom: 2mm; padding-right: 1mm">
                        {{$sales_detail->item_name}}</td>
                    <td class="eightpx" style="width:5%; padding-bottom: 2mm; padding-right: 1mm" align="center">
                        {{Helper::formatNumber($sales_detail->quantity,'')}}</td>
                    <td class="eightpx" style="width:20%; padding-bottom: 2mm; padding-right: 1mm" align="right">
                        {{Helper::formatNumber($sales_detail->sell_price,'norp')}}</td>
                    <td class="eightpx" style="width:30%; padding-bottom: 2mm; padding-right: 1mm" align="right">
                        {{Helper::formatNumber($sales_detail->sub_total,'norp')}}</td>
                </tr style="padding-bottom: 2mm">
                @endforeach
                <tr>
                    <td colspan='4'>
                        <hr>
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' align="right" class="ninepx">
                        Jumlah Total :
                    </td>
                    <td colspan='2' class="ninepx" style='text-align:right; '>
                        {{Helper::formatNumber($sales->sub_total,'rupiah')}}</td>
                </tr>
                <tr>
                    <td colspan='2' align="right" class="ninepx">
                        Diskon :
                    </td>
                    <td colspan='2' class="ninepx" style='text-align:right;'>
                        {{Helper::formatNumber($discount,'rupiah')}}</td>
                </tr>
                <tr>
                    <td colspan='2' align="right" class="ninepx">
                        Pajak :
                    </td>
                    <td colspan='2' class="ninepx" style='text-align:right;'>{{Helper::formatNumber($tax,'rupiah')}}
                    </td>
                </tr>
                <tr>
                    <td colspan='2' align="right" class="ninepx">
                        Grand Total :
                    </td>
                    <td colspan='2' class="ninepx" style='text-align:right;'>
                        {{Helper::formatNumber($sales->total,'rupiah')}}</td>
                </tr>
                <tr>
                    <td colspan='2' align="right" class="ninepx">
                        Bayar :
                    </td>
                    <td colspan='2' class="ninepx" style='text-align:right;'>
                        {{-- {{ request()->has('pay') ? Helper::formatNumber(request()->get('pay'),'rupiah') : 'n/a' }}</br> --}}
                        {{  Helper::formatNumber($sales->payCash,'rupiah') }}</br>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' align="right" class="ninepx">
                        Kembali :
                    </td>
                    <td colspan='2' class="ninepx" style='text-align:right;'>
                        {{-- {{ request()->has('cashback') ? Helper::formatNumber(request()->get('cashback'),'rupiah') : 'n/a' }}</br> --}}
                        {{  Helper::formatNumber($sales->cashBack,'rupiah') }}</br>
                    </td>
                </tr>
            </tbody>
            <tfoot align='center'>
                <tr>
                    <td colspan="5">
                        <br><br>
                    </td>
                </tr>
                <tr>
                    {{-- <td colspan="5">{{Helper::setDate($sales->date_order,'fullDateId')}}</td> --}}
                </tr>
                <tr>
                    <td colspan="5" style="">{{ $configuration->print_footer1}}</td>
                </tr>
                <tr>
                    <td colspan="5" style="">{!! $configuration->print_footer2!!}</td>
                </tr>
                <tr>
                    <td colspan="5" style="">{!! $configuration->print_footer3!!}</td>
                </tr>
            </tfoot>

        </table>
    </div>
</body>
<script>
    function balikKasir() {
        location.replace('/sales/create');
        // alert('woke');
        // window.location = "{{url('/sales/create')}}";
    }


    document.addEventListener("keypress", function(event) {
    console.log(event.keyCode);
        if (event.keyCode == 113) {
            window.close()
        }
        if (event.keyCode == 112) {
            window.print()

        }
    });
    document.addEventListener("DOMContentLoaded", function(load) {

        // detek
        // detect()
        // cetak
        window.print();
        // ngapain sebelum print
        window.onbeforeprint = (event) => {
            console.log('counter_print_here');
        };
        // setelah print

        window.addEventListener("afterprint", function(event) {
            // balikKasir()

        });
        window.onafterprint = function(event) {
            // balikKasir()
        };
    });
</script>

</html>