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
            size: 58mm {{ $count <= 5 ? 110 - (5 - $count) * 6 : 100 + ($count - 5) * 6 }}mm;
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

<body onload="coba()">
    <button class="hidden-print" onclick="balikKasir()">Kembali</button>
    {{-- <a href="/sales/create">Kembali</a> --}}

    <div class="print">
        <table>
            <tbody>
                <tr>
                    <td colspan="2" align="center" style="padding-top: 2mm;padding-bottom: 2mm;">
                        <b class="twentenpx">TB. MAJU JAYA SEKALI</b></br></br>
                        Komplek Rumah Orang Kaya</br>
                        Jl. Maju Terus Pantang Mundur No. 13, Kel. Mana, Kec. Saja, Kota Surabaya, Indonesia 61151</br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td class="ninepx" colspan="2">
                        No.: {{$sales->code}}
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
                    <th class="eightpx" style="width:20%;" align="center">Harga</th>
                    <th class="eightpx" style="width:30%;" align="center">Total</th>
                </tr>
            </thead>
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
                {{-- <tr>
                    <td colspan='2' align="right" class="ninepx">
                        Tunai :
                    </td>
                    <td colspan='2' class="ninepx" style='text-align:right;'>Rp. 1.000.000</td>
                </tr>
                <tr>
                    <td colspan='2' align="right" class="ninepx">
                        Sisa Tunai :
                    </td>
                    <td colspan='2' class="ninepx" style='text-align:right;'>Rp. 0</td>
                </tr> --}}
            </tbody>
            <tfoot align='center'>
                <tr>
                    <td colspan="5">
                        <br><br>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">{{Helper::setDate($sales->date_order,'fullDateId')}}</td>
                </tr>
                <tr>
                    <td colspan="5" style="">****** TERIMA KASIH ******</td>
                </tr>
            </tfoot>

        </table>
    </div>
</body>
<script>
    function coba(){
        window.print()
        window.close()
    }
    function balikKasir() {
        location.replace('/sales/create');
        // alert('woke');
        // window.location = "{{url('/sales/create')}}";
    }

    document.addEventListener("DOMContentLoaded", function(load) {
        // detek
        // detect()
        // cetak
        // window.print();
        // ngapain sebelum print
        // window.onbeforeprint = (event) => {
        //     console.log('counter_print_here');
        // };
        // // setelah print

        // window.addEventListener("afterprint", function(event) {
        //     // balikKasir()
        //     alert('woke');

            

        // });
        // window.onafterprint = function(event) {
        //     // balikKasir()
        // };
    });
</script>
</html>