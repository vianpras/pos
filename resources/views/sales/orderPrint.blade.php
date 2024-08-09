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
            size: 58mm {{$count <=5 ? 75 - (5 - $count) * 6: 75 + ($count - 5) * 6}}mm;
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
    {{-- <button class="hidden-print" onclick="balikKasir()">Kembali</button> --}}
    <button class="hidden-print" onclick="window.close()" autofocus>Tutup (Q)</button>
    <button class="hidden-print" onclick="window.print()">Print (P)</button>

    <div class="print">
        <table>
            <tbody>
                <tr>
                    <td class="eightpx" align="left">
                        No.: {{$sales->code}}
                    </td>
                    <td class="eightpx" align="left">
                        Tgl : {{Helper::setDate($sales->date_order,'fullDateId')}}
                    </td>

                </tr>
                <tr>
                </tr>
                <tr>
                    <td class="eightpx" align="left">
                        Kasir : {{(Auth::user()->name)=='administrator'? 'admin' : Auth::user()->name}}
                    </td>
                    <td class="eightpx" align="left">
                        Pelanggan : {{$sales->customer}}
                    </td>
                </tr>
                <tr>
                    <td class="eightpx" align="left">
                        meja : {{ $sales->table }}
                    </td>
                    <td class="eightpx" align="left">
                        Member ID : {{ is_null($sales->membership_code) ? '-': '#'.$sales->membership_code  }}
                    </td>
                </tr>

            </tbody>
        </table>
        <table>
            <thead>
                <tr>
                    <td colspan="4">
                        <hr>
                        <hr>
                    </td>
                </tr>
                <tr>
                    <th class="eightpx" style="width:45%;" align="left">Item</th>
                    <th class="eightpx" style="width:5%;" align="center">Qty</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan='2'>
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
                    
                </tr style="padding-bottom: 2mm">
                @endforeach
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