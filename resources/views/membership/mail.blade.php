<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <style>
            #btnSetuju {
                border: 0;
                text-align: center;
                display: inline-block;
                padding: 10px;
                width: 150px;
                margin: 7px;
                color: #ffffff;
                background-color: #00b46f;
                border-radius: 6px;
                font-family: "proxima-nova-soft", sans-serif;
                font-weight: 600;
                font-size: 15px;
                text-decoration: none;
                transition: box-shadow 200ms ease-out;
            }
            #btnTolak {
                border: 0;
                text-align: center;
                display: inline-block;
                padding: 10px;
                width: 150px;
                margin: 7px;
                color: #ffffff;
                background-color: #eb3c36;
                border-radius: 6px;
                font-family: "proxima-nova-soft", sans-serif;
                font-weight: 600;
                font-size: 15px;
                text-decoration: none;
                transition: box-shadow 200ms ease-out;
            }
        </style>
    </head>
	
	<body style="background-color:aliceblue; padding-top:50px; padding-bottom: 2px;">
        <table border='0' style="width:80%; height:10%; padding:4em 4em;background-color:white;margin-left: auto;margin-right: auto;margin-top:auto;margin-bottom:70px">
            <tr>
                <td colspan="3" style="padding-bottom: 15px;">
                    <p>Yang bertanda tangan berikut ini dengan detail :</p>
                </td>
            </tr>
            <tr>
                <td style="width: 50px;"></td>
                <td style="width: 100px;">Nama</td>
                <td>: {{ $nama }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Jenis Kelamin</td>
                <td>: {{ (($gender == 'l') ? 'Laki-laki' : (($gender == 'p') ? 'Perempuan' : '')) }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Tgl. Lahir</td>
                <td>: {{ date('d-m-Y', strtotime($tgl_lahir)) }}</td>
            </tr>
            <tr>
                <td></td>
                <td>No. Hp</td>
                <td>: {{ $phone_number }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Alamat</td>
                <td>: {{ $alamat }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Email</td>
                <td>: {{ $email }}</td>
            </tr>
            <tr>
                <td colspan="3" style="padding-top: 15px;">
                    <p>Menyatakan bahwa menyetujui menjadi member dari OLA FIT CLUB mulai per tanggal {{ date('d-m-Y') }}, dengan memilih tombol menyetujui dan 
                       selanjutnya kami akan mengirim email balasan mengenai konfirmasi. Detail penjelasan kontrak penjanjian dapat Anda baca di lampiran
                       yang kami kirimkan dalam email ini.</p>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center; padding-top: 25px;">
                    <a href="{{ url('membership_approve').'/'.$code }}" id="btnSetuju">Menyetujui</a>
                    <a href="{{ url('membership_reject').'/'.$code }}" id="btnTolak">Tolak</a>
                </td>
            </tr>
        </table>
    </body>
</html>