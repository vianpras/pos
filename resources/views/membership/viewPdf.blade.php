<!DOCTYPE html>
<html>
<head>
    <title>How To Generate PDF From HTML View In Laravel - techsolutionstuff.com</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <style>
        .container{
            font-size: 13px;
            font-family: "Times New Roman", Times, serif;
            padding-left: 4%;
            padding-right: 4%;
        }

        .container h3{
            text-align: center;
        }

        ol li{
            margin-bottom:10px;
        }

        #watermark
        {
            width: 250px;
            opacity: 0.4;
            color: rgb(209, 209, 209);
            font-size: 35px;
            display: flex;
            justify-content: center;
            /* border: 1px  solid rgb(116, 116, 116); */
            /* transform: rotate(-10deg); */
        }
    </style>
</head>
<body>
    <div class="container" style="">
        <h3><b>KETENTUAN DAN PERNYATAAN PERSETUJUAN ANGGOTA</b></h3>
        <ol>
            <li>Anda wajib memenuhi batas usia ( 17 Tahun atau lebih) ketidak menyetujui perjanjian ini; Jika dibawah 17 Tahun, maka Anda harus menyatakan bahwa Anda telah memperoleh persetujuan dari wali (orang tua) sebelum menyetujui perjanjian ini.</li>
            <li>Jika anda dibawah usia 16 tahun, maka Anda diwajibkan menggunakan jasa Personal Trainer untuk pemakaian fasilitas gym.</li>
            <li>Wajib menggunakan <b style="color: red;"><i>pakaian renang ( bukan kaos )</i></b> saat masuk ke dalam kolam renang.</li>
            <li>Wajib menggunakan sepatu dan pakaian olah raga selama berada di area GYM dan kelas.</li>
            <li>Anda menyetujui OLA FIT CLUB untuk mengambil dan menggunakan data dan informasi pribadi Anda untuk kelengkapan data keanggotaan, marketing, operasional, dan tujuan identifikasi.</li>
            <li>Anda menyetujui bahwa kami dapat memberikan informasi Anda kepada penasehat profesional kami jika diperlukan oleh hukum atau penyelesaian masalah keluhan.</li>
            <li>Anda diminta untuk memberikan data pribadi yang berlaku, seperti: nomor telepon, alamat, alamat email, dan nomor telepon darurat yang bisa di hubungi. OLA FIT CLUB dapat mengirimkan pemberitahuan kepada anda dari waktu ke waktu tentang status keanggotaan, promosi, dan event. Dalam hal anggota mengalami perubahan data, maka anggota wajib untuk memberitahukan perubahan data tersebut.</li>
            <li>Keanggotaan bersifat pribadi terhadap anggota dan tidak bisa dialihkan, tidak bisa dipindahtangankan  dan tidak bisa dikembalikan. Seorang anggota tidak bisa meminjamkan kartu keanggotaan untuk dipakai oleh pihak ketiga. Perusahaan boleh mengalihkan manfaat dari semua perjanjian kepada perorangan, usaha atau perusahaan apapun di setiap waktu tanpa pemberitahuan kepada anggota.</li>
            <li>Anda harus menunjukkan kartu IDENTITAS/keanggotaan anda untuk masuk ke area OLA FIT CLUB. Jika kartu anda hilang atau dicuri, anda wajib menghubungi club untuk menerbitkannya kembali kartu pengganti. Anda bertanggung jawab atas biaya penggantian kartu.</li>
            <li>Segala biaya pembayaran apapun yang telah dibayarkan <b style="color: red;"><i>tidak dapat dikembalikan (NON REFUNDABLE).</i></b></li>
            <li>Anggota dapat membekukan (cuti) keangotaannya minimal 1 bulan dan maksimal 2 bulan.</li>
            <li>Pengajuan cuti keangotaan akan dikenakan biaya sebesar Rp 75.000 (tujuh puluh lima ribu rupiah) per bulan.</li>
            <li>Cuti keangotaan hanya akan disetujui jika alasan ketidakmampuan menggunakan fasilitas club dianggap tepat. Anggota tidak dapat memasuki area club saat cuti keanggotaan.</li>
            <li>Untuk aktivasi kartu member dilakukan maksimal 2 (dua) minggu setelah tanggal transaksi. Jika member berhalangan hadir maka,akan diaktifkan otomatis oleh sistem.</li>
            <li>Untuk alasan keamanan dan melindungi para anggota dan karyawan dalam lokasi kami diawasi oleh kamera pengawas (terkecuali ruang ganti). Anda tidak disarankan untuk meninggalkan barang berharga anda diruang ganti.</li>
            <li>Tanpa adanya pemberitahuan atau persetujuan terlebih dahulu dari OLA FIT CLUB, kamera atau alat perekam foto dan video lainnya termasuk telepon genggam yang memiliki alat perekam seperti yang dimaksud tersebut, dilarang digunakan untuk pengambilan gambar atau rekaman dalam lokasi terutama di area tempat ruang ganti.</li>
            <li>Anggota wajib menjaga barang bawaan yang berharga seperti perhiasan, HP, dan lain-lain. Dikarenakan segala bentuk kehilangan kerusakan barang pribadi anggota bukan menjadi tanggung jawab OLA FIT CLUB.</li>
            <li>OLA FIT CLUB memiliki loker pengamanan yang terletak di ruang ganti dan tidak diawasi oleh kamera pengawas. OLA FIT CLUB tidak bertanggung jawab atas kehilangan barang diseluruh area club.</li>
            <li>Anggota dengan ini menyadari, menyatakan dan menjamin bahwa selama latihan, setiap anggota berhak menggunakan loker harian untuk menyimpan barang-barangnya. Loker harian harus dikosongkan setelah anggota selesai latihan. Setiap loker harian akan diperiksa dan dikosongkan disetiap akhir jam operasional. OLA FIT CLUB sangat menyarankan agar tidak membawa barang-barang berharga ke club. Dengan menandatangani perjanjian ini, anda setuju bahwa OLA FIT CLUB tidak bertanggung jawab atas barang-barang yang hilang dengan alasan apapun. Anda bertanggung jawab untuk menjaga barang-barang pribadi anda sendiri.</li>
            <li>Kelas - kelas khusus dan pelatihan pribadi seperti personal training akan dikenakan biaya tambahan dan tidak termasuk biaya keanggotaan.</li>
            <li>Kewajiban lain yang tidak tertera dalam perjanjian ini yang kemungkinan akan timbul, akan mengikuti peraturan dan Undang-Undang yang berlaku di Indonesia.</li>
            <li>Semua perselisihan yang timbul dari atau sehubungan dengan perjanjian ini akan diupayakan secara kekeluargaan. Dalam hal ini penyelesaian  secara kekeluargaan  tidak dapat tercapai, maka masing-masing pihak sepakat menunjuk Badan Arbitrase Nasional (BAN) guna penyelesaian hukum atas perselisihan.</li>
            <li>Anggota dengan ini menyadari, menyatakan  dan menjamin bahwa  penggunaan  fasilitas di  OLA FIT CLUB tentunya mengandung resiko kecelakaan bagi setiap anggota sendiri, atau anggota lainnya, atau orang disekitarnya, baik disebabkan oleh  anda  sendiri  maupun  orang  lain.  Anggota  memahami  dan  secara  sukarela  menerima  resiko  tersebut.  Anggota menjamin dan menyatakan bahwa anggota telah berkonsultasi dengan dokter sebelum memulai setiap latihan, sehingga dari dan oleh karenanya, anggota dengan ini setuju dan membebaskan  tanggung jawab OLA FIT CLUB atas segala jenis kecelakaan, termasuk tidak terbatas pada cedera pribadi, cedera fisik, cedera mental, kerugian ekonomi, atau kerugian lain anggota, atau keluarganya sebagai dari tindakan seseorang yang menggunakan fasilitas ataupun tindakan dari pegawai atau agen OLA FIT CLUB.</li>
            <li>Anggota setuju bertanggung jawab sepenuhnya atas semua tanggung jawab dan kerugian yang timbul sebagai akibat dari setiap kecelakaan, termasuk namun tidak terbatas pada cedera tubuh atau cedera mental, kerugian ekonomi, atau setiap kerugian bagi anggota lain disebabkan oleh tindakan kesengajaan atau kelalaian anggota sendiri. Jika ada tuntutan dari siapapun juga yang dikarenakan oleh cedera apapun, kehilangan atau kerusakan lainnya yang berkaitan dengan anda atau tamu anda maka anda menyetujui untuk (I) membela OLA FIT CLUB atas segala tuntutan tersebut dan membayar OLA FIT CLUB atas segala pengeluaran termasuk biaya hukum berkaitan dengan tuntutan tersebut. (II) melindungi OLA FIT CLUB dari segala tanggung jawab kepada anda, suami/istri anda, anak dalam kandungan, keluarga atau siapapun juga sebagai akibat yang terkait dari tuntutan tersebut dan (III) membebaskan OLA FIT CLUB dari segala bentuk tuntutan, gugatan dan ganti kerugian dalam bentuk apapun dan dari siapapun tanpa terkecuali.</li>
            <li>Dengan ini anggota menyadari bahwa OLA FIT CLUB tidak pernah menjanjikan apapun <b>selain apa yang disampaikan secara tertulis</b> oleh OLA FIT CLUB dan atau sebagaimana yang tertulis pada perjanjian ini, dalam hal ini anggota mendapatkan janji-janji diluar perjanjian ini, misal oleh staff OLA FIT CLUB (oknum) maka anggota dapat melaporkan atau menyampaikan kepada pihak OLA FIT CLUB, namun TETAPI janji-janji tersebut tidak berlaku dan tidak mengikat kedua belah pihak dan anggota dengan ini membebaskan OLA FIT CLUB dari segala bentuk tuntutan, gugatan, dan mengganti kerugian dalam bentuk apapun dan dari siapapun tanpa terkecuali.</li>
            <li>Anggota dengan ini menyatakan dan menjamin bahwa isi dan perjanjian mencakup segala komunikasi dan atau pernyataan diantara anggota dan OLA FIT CLUB. Sehubungan dengan hal-hal yang berkaitan dengan perjanjian ini, sehingga segala bentuk komunikasi dan atau pernyataan diantara anggota dan OLA FIT CLUB yang tidak diatur dalam perjanjian ini menjadi tidak berlaku dan tidak mengikat kedua belah pihak, dan anggota dengan ini membebaskan OLA FIT CLUB dari segala bentuk tuntutan, gugatan, dan kerugian dalam bentuk apapun dan dari siapapun tanpa terkecuali.</li>
            <li>Untuk melindungi kepentingan bisnisnya dan demi keselamatan dan pertimbangan para anggota lainnya, OLA FIT CLUB <b>berhak memutuskan keanggotaan</b> setiap anggota dengan atau tanpa pemberitahuan terlebih dahulu. Alasan pemutusan dapat berupa perilaku yang dianggap tidak pantas ataupun tindakan yang menimbulkan kegaduhan atau menjadikan suatu keadaan menjadi tidak kondusif, termasuk tetapi tidak terbatas juga terhadap setiap tindakan kekerasan terhadap anggota lainnya atau staff OLA FIT CLUB, perbuatan melawan hukum serta dugaan tindak pidana, atau melakukan pelayanan seperti kepelatihan pribadi (melatih jasa personal trainer). Penjualan barang-barang tanpa izin, perolehan finansial dan atau perilaku yang dianggap melanggar syarat dan ketentuan yang ditetapkan dalam perjanjian ini atau setiap peraturan dan ketentuan yang tertera di seluruh club. Semua uang yang telah dibayarkan tidak dapat dikembalikan. Sebagai tujuan proses pemutusan perjanjian keanggotaan ini, kedua belah pihak setuju untuk mengabaikan semua biaya yang terjadi selama keputusan untuk mengakhiri perjanjian ini. Anggota setuju dan sepakat mengesampingkan ketetapan ketentuan pasal 1266 dan 1267 kitab Undang-Undang Hukum Perdata Republik Indonesia sehubungan dengan pengakhiran perjanjian keanggotaan.</li>
            <li>Dengan menandatangani surat perjanjian ini, Anda menyetujui setiap dan seluruh peraturan dan pedoman keanggotaan yang disampaikan melalui club dan perjanjian ini, OLA FIT CLUB berhak mengubah setiap dan seluruh peraturan dari waktu ke waktu sesuai kebijaksanaan kami terkait kemungkinan adanya perubahan dan atau penyesuaian kebijakan- kebijakan pemerintah setempat dan pertimbangan bisnis, setelah sekali didaftarkan, maka Anda setuju bahwa semua peraturan ini berlaku pada Anda.</li>
            <p style="margin-top: 30px;">({{ date('d/m/Y', strtotime($now)) }})</p>
            <p style="margin-bottom: 20px;">Tanda tangan Anggota (Member) Jika Menyetujui perjanjian ini :</p>
            <div id="watermark">
                <b>Menyetujui</b>
            </div>
            <h4>({{ $check->nama }})</h4>
        </ol>  
    </div>
</body>
</html>