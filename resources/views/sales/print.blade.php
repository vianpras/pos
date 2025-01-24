<html>

<head>
    <title>{{$sales->code}}</title>
    <style media="print">
        * {
            font-size: 13px;
            font-family: Consolas, Menlo, Monaco, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, serif;
        }

        body {
            align-content: flex-start;
            /* align-content: center;
            justify-content: center;
            justify-items: flex-start;
            display: grid; */
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
            size: 58mm 297mm;
            margin: 1;
        }
        
        @media print {

            .hidden-print,
            .hidden-print * {
                display: none !important;
            }
        }

        table {
            /* width: 50mm; */
            border-collapse: collapse;
            border: 0px
        }

        hr {
            margin: 0;
            border-top: 1px solid;
        }

        table tr#table-info td {
            text-align: left;
            vertical-align: top;
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
                    <td colspan="6" align="center">
                        <b>{{ ($company) ? $company->name : "" }}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center">
                        <b>{!! ($company) ? $company->address1 : "" !!}</b>
                        <b>{!! ($company) ? $company->mobile : "" !!}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="6"><hr></td>
                </tr>
                <tr id="table-info">
                    <td>Kode</td>
                    <td>:</td>
                    <td>{!! $sales->customer !!}</td>
                    <td>No</td>
                    <td>:</td>
                    <td>{!! $sales->code !!}</td>
                </tr>
                <tr id="table-info">
                    <td colspan="3">{!! ($sales->customer_detail) ? $sales->customer_detail : $sales->cardname !!}</td>
                    <td>Tgl</td>
                    <td>:</td>
                    <td>{!! date('d/M/Y', strtotime($sales->date_order)) !!}</td>
                </tr>
                <tr id="table-info">
                    <td colspan="3"></td>
                    <td>Jam</td>
                    <td>:</td>
                    <td>{!! date('H:i', strtotime($sales->created_at)) !!}</td>
                </tr>
                <tr id="table-info">
                    <td>Sales</td>
                    <td>:</td>
                    <td colspan="4"></td>
                </tr>
                <tr id="table-info">
                    <td>Kasir</td>
                    <td>:</td>
                    <td colspan="4">{{ (Auth::user()->name)=='administrator'? 'admin' : Auth::user()->full_name }}</td>
                </tr>
                <tr id="table-info">
                    <td>Checker</td>
                    <td>:</td>
                    <td colspan="4"></td>
                </tr>
                {{-- <tr>
                    <td colspan="3" align="center">
                        <img src="{{ asset('dist/img/AdminLTELogo.png') }}" height="50px"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" align="center" style="padding-top: 2mm;padding-bottom: 2mm;">
                        <b class="twentenpx">{{ $company->name }}</b></br></br>
                        {!! $company->address1 !!}</br>
                        {{ $company->mobile }} - {{ $company->email }}</br>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td class="eightpx" align="left" style="width: 20px;">
                        No. 
                    </td>
                    <td style="width: 2px;">:</td>
                    <td class="eightpx">
                        {{$sales->code}}
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
                </tr>
                <tr>
                    <td class="eightpx" align="left">
                        Customer
                    </td>
                    <td>:</td>
                    <td>
                        {{ $sales->customer }}
                    </td>
                </tr>
                <tr>
                    <td class="eightpx" align="left">
                        Pembayaran
                    </td>
                    <td>:</td>
                    <td>
                    </td>
                </tr> --}}
            </tbody>
        </table>
        <table>
            <tbody>
                <tr>
                    <td colspan='6'>
                        <hr>
                    </td>
                </tr>

                @foreach ($sales_details as $sales_detail)
                <tr style="">
                    <td colspan="6" style="vertical-align: top;">
                        {{ $sales_detail->itemcode_short }}
                    </td>
                </tr>
                <tr>
                    <td colspan="6" style="vertical-align: top;">
                        {{-- <p style="white-space: nowrap; text-overflow: clip; overflow: hidden; width: 120px;">{{ $sales_detail->item_name }}</p> --}}
                        <p>{{ $sales_detail->item_name }}</p>
                    </td>
                    {{-- <td class="eightpx" style="width:5%; padding-bottom: 2mm; padding-right: 1mm" align="center">
                        {{Helper::formatNumber($sales_detail->quantity,'')}}
                    </td>
                    <td class="eightpx" style="width:20%; padding-bottom: 2mm; padding-right: 1mm" align="right">
                        {{Helper::formatNumber($sales_detail->sell_price,'norp')}}
                    </td>
                    <td class="eightpx" style="width:30%; padding-bottom: 2mm; padding-right: 1mm" align="right">
                        {{Helper::formatNumber($sales_detail->sub_total,'norp')}}
                    </td> --}}
                </tr>
                <tr>
                    <td colspan="1" align="center" width="60">{{ Helper::formatNumber($sales_detail->quantity,'') }}</td>
                    <td colspan="2" style="min-width: 100px">{{ Helper::formatNumber($sales_detail->sell_price,'rupiah') }}</td>
                    <td colspan="3" style="min-width: 100px">{{ Helper::formatNumber($sales_detail->sub_total,'rupiah') }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan='6'>
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' align="right">
                        Total :
                    </td>
                    <td colspan='2' style='text-align:right; '>
                        {{Helper::formatNumber($sales->sub_total,'rupiah')}}
                    </td>
                </tr>
                <tr>
                    <td colspan='2' align="right">
                        Bayar :
                    </td>
                    <td colspan='2' style='text-align:right;'>
                        {{  Helper::formatNumber($sales->payment,'rupiah') }}</br>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' align="right">
                        Kembali :
                    </td>
                    <td colspan='2' style='text-align:right;'>
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
                    <td colspan="5" style="">{{ ($configuration) ? $configuration->print_footer1 : ""}}</td>
                </tr>
                <tr>
                    <td colspan="5" style="">{!! ($configuration) ? $configuration->print_footer2 : "" !!}</td>
                </tr>
                <tr>
                    <td colspan="5" style="">{!! ($configuration) ? $configuration->print_footer3 : "" !!}</td>
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