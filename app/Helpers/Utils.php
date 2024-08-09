<?php

namespace App;
use PDO;
use DB;
use Session;
use DateTime;
use Request;
use Auth;

class Utils 
{
    private static $iv = 'f664bd0ad5f0abcf';
    private static $key = '550d51dcf8ca4871';

    public static function id2Folder($id) {
        return strval(floor($id / 1000)+1);
    }

    public static function encrypt($filename)
    {
        $data = file_get_contents($filename);
        $cypher = 'AES-256-CBC';
        $ivSize  = openssl_cipher_iv_length($cypher);
        $ivData  = openssl_random_pseudo_bytes($ivSize);

        $encripted = openssl_encrypt($data,
            $cypher,
            self::$key,
            OPENSSL_RAW_DATA,
            $ivData);

        return base64_encode($ivData  . $encripted);
    }

    public static function decrypt($filename)
    {
        $data = file_get_contents($filename);
        $cypher = 'AES-256-CBC';
        $ivSize  = openssl_cipher_iv_length($cypher);
        $data = base64_decode($data);
        $ivData   = substr($data, 0, $ivSize);
        $encData = substr($data, $ivSize);

        $result = openssl_decrypt($encData,
            $cypher,
            self::$key,
            OPENSSL_RAW_DATA,
            $ivData);
            
        return $result;
    }

    public static function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir.'/'.$object) == 'dir')
                        self::rrmdir($dir.'/'.$object);
                    else
                        unlink   ($dir.'/'.$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function saveUploadImage($file_dari_upload, $file_save, $flag_resize = true) {
        //*** siapkan thumbnail, penamaan adalah ditambahkan suffix: _thumb
        //hapus thumb yang lama (jika ada)
        if (file_exists($file_save)) {
            unlink($file_save);
        }

        $file_temp = $file_save.'~temp';
        //hapus temporary file jika ada
        if (file_exists($file_temp)) {
            unlink($file_temp);
        }

        if($file_dari_upload->getMimeType() == 'image/jpeg') {
            $img = imagecreatefromjpeg($file_dari_upload);
        }else if($file_dari_upload->getMimeType() == 'image/png'){
            $img = imagecreatefrompng($file_dari_upload);
        }else if($file_dari_upload->getMimeType() == 'image/gif'){
            $img = imagecreatefrombmp($file_dari_upload);
        }
        //$img = imagecreatefromjpeg($file_dari_upload);

        $width = imagesx($img);
        $height = imagesy($img);

        if($flag_resize==true && $width> 640){
            //ukuran gambar maksimal
            $desired_width = 640;
            $desired_height = floor($height * ($desired_width / $width));

            $temp_img = imagecreatetruecolor($desired_width, $desired_height);

            imagecopyresized( $temp_img, $img, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height );
            if($file_dari_upload->getMimeType() == 'image/jpeg') {
                imagejpeg($temp_img, $file_temp);
            }else if($file_dari_upload->getMimeType() == 'image/png'){
                imagepng($temp_img, $file_temp);
            }else{
                imagejpeg($temp_img, $file_temp);
            }

            file_put_contents($file_save, self::encrypt($file_temp));

            //hapus temporary file jika ada
            if (file_exists($file_temp)) {
                unlink($file_temp);
            }
        } else {
            file_put_contents($file_save, self::encrypt($file_dari_upload));
        }
    }

    public static function makeThumbnail($file_dari_upload, $file_thumbnail, $encrypt= 'true', $thumb = '_thumb', $width_expected = 256) {
        //*** siapkan thumbnail, penamaan adalah ditambahkan suffix: _thumb
        //hapus thumb yang lama (jika ada)
        if (file_exists($file_thumbnail)) {
            unlink($file_thumbnail);
        }

        if($encrypt == 'true'){
            $file_temp = $file_thumbnail.'~temp';
        }else{
            $file_temp = $file_thumbnail.'.thumb';
        }
        //hapus temporary file jika ada
        if (file_exists($file_temp)) {
            unlink($file_temp);
        }

        $img = '';
        if($file_dari_upload->getMimeType() == 'image/jpeg') {
            $img = imagecreatefromjpeg($file_dari_upload);
        }else if($file_dari_upload->getMimeType() == 'image/png'){
            $img = imagecreatefrompng($file_dari_upload);
        }else if($file_dari_upload->getMimeType() == 'image/gif'){
            $img = imagecreatefrombmp($file_dari_upload);
        }
        //$img = imagecreatefromjpeg($file_dari_upload);
        if($img != '') {
            $width = imagesx($img);
            $height = imagesy($img);

            $desired_width = $width < $width_expected ? $width : $width_expected;
            $desired_height = floor($height * ($desired_width / $width));

            $temp_img = imagecreatetruecolor($desired_width, $desired_height);

            if($file_dari_upload->getMimeType() == 'image/png'){
                imagecolortransparent($temp_img, imagecolorallocatealpha($temp_img, 255, 255, 255, 127));
                imagealphablending($temp_img, false);
                imagesavealpha($temp_img, true);
            }

            imagecopyresampled($temp_img, $img, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
            if($file_dari_upload->getMimeType() == 'image/jpeg') {
                imagejpeg($temp_img, $file_temp, 90);
            }else if($file_dari_upload->getMimeType() == 'image/png'){
                imagepng($temp_img, $file_temp);
            }else{
                imagejpeg($temp_img, $file_temp, 90);
            }

            //kembalikan ke format enkripsi
            if($encrypt == 'true'){
                file_put_contents($file_thumbnail . $thumb, self::encrypt($file_temp));
                //hapus temporary file jika ada
                if (file_exists($file_temp)) {
                    unlink($file_temp);
                }
            }
        }
    }

    public static function CheckOtpRegex($pesan) {
        $pdo = DB::getPdo();
        $sql = 'SELECT regex FROM otp_regex WHERE digunakan=\'y\'';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $match = null;
            $regex = $row['regex'];
            preg_match($regex, $pesan, $match);
            if (count($match) > 0) {
                return true;
            }
        }
        return false;
    }

    public static function getTextColor($warna) {
        //atur warna teks
        $r = hexdec(substr($warna,0,1)) / 255.0;
        if ($r <= 0.03928) {
            $r = $r/12.92;
        }
        else {
            $r = pow(($r+0.055)/1.055, 2.4);
        }
        $g = hexdec(substr($warna,2,2)) / 255.0;
        if ($g <= 0.03928) {
            $g = $g/12.92;
        }
        else {
            $g = pow(($g+0.055)/1.055, 2.4);
        }
        $b = hexdec(substr($warna,4,2)) / 255.0;
        if ($b <= 0.03928) {
            $b = $b/12.92;
        }
        else {
            $b = pow(($b+0.055)/1.055, 2.4);
        }

        $l = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;

        if ($l > 0.279) {
            return '#000000';
        }
        else {
            return '#ffffff';
        }
    }

    public static function hapusChecksumImg($menu,$id){
        $pdo = DB::getPdo();
        $sql = '';
        if($menu == 'chat_admin' || $menu == 'informasi_kategori'){
            $sql = 'UPDATE '.$menu.' SET imgchecksum = "", imgfiletype = NULL WHERE id = :id';
        }elseif($menu == 'member_datapribadi_konfirmasi'){
            $sql = 'UPDATE '.$menu.' SET selfie_checksum = NULL, selfie_filetype = NULL, ktp_checksum = NULL, ktp_filetype = NULL WHERE idmember = :id';
        }elseif(strpos($menu, 'konten_') !== false){
            $menu == 'konten_satuan' ? 
            $sql = 'UPDATE '.$menu.' SET cover_checksum = "" WHERE idkonten = :id':
            $sql = 'UPDATE '.$menu.' SET cover_checksum = "" WHERE id = :id';
        }else{
            $sql = 'UPDATE '.$menu.' SET imgchecksum = NULL, imgfiletype = NULL WHERE id = :id';
        }
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }

    public static function hapusGambar($menu,$id){
        $path = '';
        if($menu != 'user' && $menu != 'pengguna' && $menu != 'member_datapribadi_konfirmasi'){
            if(strpos($menu, 'konten_') !== false){
                $menu == 'konten_satuan' ? 
                    $imgchecksum = Utils::getData($menu,'cover_checksum','idkonten='.$id):
                    $imgchecksum = Utils::getData($menu,'cover_checksum','id='.$id);
            }elseif($menu == 'paydonk_lampiran'){
                $imgchecksum = Utils::getData('paydonk','lampiran_checksum','id='.$id);
            }elseif($menu == 'edc_lampiran'){
                $imgchecksum = Utils::getData('edc','lampiran_checksum','id='.$id);
            }else{
                $imgchecksum = Utils::getData($menu,'imgchecksum','id='.$id);
            }
        }elseif($menu == 'buktitransfer'){
            $data = Utils::getDataMultipleObj('transfer_transaksi_riwayat', 'buktitransfer, updated', 'id = '.$id);
            $tanggal = date("Ymd", strtotime($data[0]->updated));
            $buktitransfer = $data[0]->buktitransfer;
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG_BUKTITRANFER').'/'.$tanggal.'/'.$buktitransfer;
        }
        
        if($menu == 'tingkatan'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/tingkatan/indikator/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'tingkatan_img_infolevel'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/tingkatan/infolevel/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'tingkatan_img_upgrade'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/tingkatan/upgrade/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'kodenegara'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/kodenegara/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'informasikurs'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/informasikurs/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'air_pascabayar_produk'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/air/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'emoney'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/emoney/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'telepon_operator'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/telepon/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'topup_bank'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/topup_bank/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'transferbank'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/transferbank/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'transferbankva'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/transferbankva/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'brankas_bank'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/brankas_bank/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'member'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/member/'.Utils::id2Folder($id).'/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'chat_admin'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/chat/admin/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'chat_group'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/chat/group/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'user' || $menu == 'pengguna'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/user/profil/'.$id;
        }elseif($menu == 'informasi_kategori'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/informasi_kategori/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'informasi'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/informasi/'.Utils::id2Folder($id).'/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'agent'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/agent/'.Utils::id2Folder($id).'/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'owner'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/owner/'.Utils::id2Folder($id).'/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'welcomescreen_item'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/welcomescreen/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'konten_satuan'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/konten/satuan/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'konten_horizontal'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/konten/horizontal/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'konten_slideshow'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/konten/slideshow/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'paydonk_kategori'){
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/paydonk/'.$id.'_'.$imgchecksum;
        }elseif($menu == 'paydonk_lampiran'){
            $idmember = Utils::getData('paydonk', 'idmember', 'id = '.$id);
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/paydonk/'.Utils::id2Folder($idmember).'/'.$idmember.'/'.$idmember.'_lampiran_'.$imgchecksum;
        }elseif($menu == 'paydonk_lampiran'){
            $idmember = Utils::getData('paydonk', 'idmember', 'id = '.$id);
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/edc/'.Utils::id2Folder($idmember).'/'.$idmember.'/'.$idmember.'_lampiran_'.$imgchecksum;
        }elseif('member_datapribadi_konfirmasi'){
            $folder = Utils::getData('member_datapribadi_konfirmasi','folder','idmember ='.$id);
            $selfie_imgchecksum = $imgchecksum = Utils::getData($menu,'selfie_checksum','idmember ='.$id);
            $ktp_imgchecksum = $imgchecksum = Utils::getData($menu,'ktp_checksum','idmember ='.$id);
            $path_ktp = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/datapribadi/'.Utils::id2Folder($id).'/'.$id.'/'.$folder.'/konfirmasi_'.$ktp_imgchecksum;
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.config('consts.FOLDER_IMG').'/datapribadi/'.Utils::id2Folder($id).'/'.$id.'/'.$folder.'/konfirmasi_'.$selfie_imgchecksum;

            if(file_exists($path_ktp)){ unlink($path_ktp); }
            if(file_exists($path_ktp.'_thumb')){ unlink($path_ktp.'_thumb'); }
            if(file_exists($path_ktp.'.thumb')){ unlink($path_ktp.'.thumb'); }
        }
        
        if(file_exists($path)){ unlink($path); }
        if(file_exists($path.'_thumb')){ unlink($path.'_thumb'); }
        if(file_exists($path.'.thumb')){ unlink($path.'.thumb'); }

        if($menu != 'user' && $menu != 'pengguna' && $menu != 'paydonk_lampiran' && $menu != 'edc_lampiran'){
            self::hapusChecksumImg($menu,$id);
        }
    }

    public static function buttonManipulasi($url, $jenis, $jenisclass='', $icon = "edit", $title ='Modifikasi Lanjutan'){
        if($jenis == 'edit'){
            $jenisclass = $jenisclass == '' ? 'btnEdit' : $jenisclass;
            return '<a data-title="Ubah Data" href="'.$url.'"><i style="color: #7465eb" class="btn-action icon icon-pencil pointer"></i><a>';
        }else if($jenis == 'mdledit'){
            $jenisclass = $jenisclass == '' ? 'btnEdit' : $jenisclass;
            return '<a data-title="Ubah Data"><i style="color: #7465eb" class="btn-action icon icon-pencil '.$jenisclass.' pointer" data-edit="'.$url.'"></i></a>';
        }else if($jenis == 'hapus'){
            $jenisclass = $jenisclass == '' ? 'btnDelete' : $jenisclass;
            return '<a data-title="Hapus Data"><i style="color:red" class="btn-action icon icon-trash '.$jenisclass.' pointer" data-remove="'.$url.'"></i></a>';
        }else if($jenis == 'gagalkan'){
            $jenisclass = $jenisclass == '' ? 'btnGagal' : $jenisclass;
            return '<i data-title="Gagalkan Proses" style="cursor:pointer" class="btn-action icon icon-times-circle '.$jenisclass.'" data-gagal="'.url($url).'"></i>';
        }else if($jenis == 'sukseskan'){
            $jenisclass = $jenisclass == '' ? 'btnSukses' : $jenisclass;
            return '<i data-title="Sukseskan Proses" style="cursor:pointer" class="btn-action icon icon-check-circle '.$jenisclass.'" data-sukses="'.url($url).'"></i>';
        }else if($jenis == 'ulangi'){
            $jenisclass = $jenisclass == '' ? 'btnUlang' : $jenisclass;
            return '<i data-title="Ulangi Proses" style="cursor:pointer" class="btn-action icon icon-history '.$jenisclass.'" data-ulang="'.url($url).'"></i>';
        }else if($jenis == 'detail'){
            return '<a data-title="'.$title.'" href="'.$url.'"><i style="cursor:pointer" class="btn-action icon icon-'.$icon.'"></i><a>';
        }
    }


    public static function formCreateImg($type = 'empty'){
        return '<div style="width:500px; margin-bottom:25px; text-align:center;">
                    <div style="width:160px; margin:auto;">
                        <div class="form-group" style="width:120px; margin:auto;">
                            <label class="control-label">Gambar</label>
                            <img id="imgInp" width=120 style="border-radius: 10%;" height=120>
                        </div>
                        <p class="help-block"><small><i><b>* Ekstensi</b> : JPG/JPEG, PNG dan GIF < 2MB</i></small></p>
                        <div class="form-group" style="width:120px; margin:auto;">
                            <input type="file" style="display:none" name="gambar" id="gambar" class="filestyle"  data-badge="false" data-input="false">
                            <button style="margin-left:15px;" type="button" onclick="browseImg()" class="btn btn-primary"><i class="icon icon-folder-open"></i></button>&nbsp;&nbsp;
                            <button type="button" onclick="delImg(\''.$type.'\')" class="btn btn-danger"><i class="icon icon-trash"></i></button>
                        </div>
                    </div>
                </div>';
    }

    public static function formUpdateImg($isImgExist){
        return '<div style="width:500px; margin-bottom:25px; text-align:center;">
                    <div style="width:160px; margin:auto;">
                        <div class="form-group" style="width:120px; margin:auto;">
                            <label class="control-label">Gambar</label>
                            <img id="imgInp" width=120 style="border-radius:10%;" height=120 adagambar="'.$isImgExist.'">
                        </div>
                        <p class="help-block"><small><i><b>* Ekstensi</b> : JPG/JPEG, PNG dan GIF < 2MB</i></small></p>
                        <div class="form-group" style="width:120px; margin:auto;">
                            <input type="file" style="display:none" name="gambar" id="gambar" class="filestyle"  data-badge="false" data-input="false">
                            <button style="margin-left:15px;" type="button" onclick="browseImg()" class="btn btn-primary"><i class="icon icon-folder-open"></i></button>&nbsp;&nbsp;
                            <button type="button" id="delImg" class="btn btn-danger"><i class="icon icon-trash"></i></button>
                        </div>
                    </div>
                </div>';
    }

    public static function modalDetail($event,$icon='info-circle', $title = 'Info Detail'){
            return '<a data-title="'.$title.'"><span class="icon icon-'.$icon.' pointer" onclick="'.$event.'"></span></a>&nbsp;&nbsp;';
    }

    public static function maskingSecretNumber($data, $mask = '*'){
        $result = '';
        $char = str_split($data);
        for($i=0; $i<count($char); $i++){
            if(is_numeric($char[$i])){
                $result .= $mask;
            }else{
                $result .= $char[$i];
            }
        }
        return $result;
    }

    public static function getStatus($status, $par = ''){
        if($par == 'aktif'){
            if($status == 'a'){
                return '<label class="label label-outline-success">Aktif</label>';
            }elseif($status == 'aktif'){
                return '<label class="label label-outline-success">Aktif</label>';
            }elseif($status == 'terima'){
                return '<label class="label label-outline-success">Terima</label>';
            }elseif($status == 't'){
                return '<label class="label label-outline-danger">Tidak Aktif</label>';
            }elseif($status == 'tidak-aktif'){
                return '<label class="label label-outline-danger">Tidak Aktif</label>';
            }elseif($status == 'b'){
                return '<label class="label label-outline-warning">Blokir</label>';
            }elseif($status == 'k' || $status == 'konfirmasi'){
                return '<label class="label label-outline-primary">Konfirmasi</label>';
            }elseif($status == 'konfirmasi'){
                return '<label class="label label-outline-primary">Konfirmasi</label>';
            }elseif($status == 'valid'){
                return '<label class="label label-outline-success">Valid</label>';
            }elseif($status == 'butuhperbaikan'){
                return '<label class="label label-outline-danger">Butuh Perbaikan</label>';
            }elseif($status == 'tolak'){
                return '<label class="label label-outline-danger">Ditolak</label>';
            }elseif($status == 'tersedia'){
                return '<label class="label label-outline-primary">Tersedia</label>';
            }elseif($status == 'tidak'){
                return '<label class="label label-outline-danger">Tidak</label>';
            }elseif($status == 'tersedia'){
                return '<label class="label label-outline-primary">Tersedia</label>';
            }elseif($status == 'trial'){
                return '<label class="label label-outline-warning">Trial</label>';
            }
        }elseif($par == 'gunakan'){
            if($status == 'y'){
                return '<label class="label label-outline-success">Digunakan</label>';
            }elseif($status == 't'){
                return '<label class="label label-outline-danger">Tidak Digunakan</label>';
            }
        }
        if($status == 'tunggu' || $status == 'menunggu' || $status == 'timeout' || $status == 'deprecated' || $status == 'konfirmasi' || $status == 'menunggu_diambil' || $status == 'menunggu_checkin'){
            return '<label class="label label-outline-warning">'.ucwords(str_replace('_', ' ', $status)).'</label>';
        }elseif($status == 'proses' || $status == 'waiting' || $status == 'penambahan' || $status == 'published' || $status == 'baru' || $status == 'dikirim' || $status == 'withdraw'){
            return '<label class="label label-outline-primary">'.ucfirst($status).'</label>';
        }elseif($status == 'sukses' || $status == 'terkirim' || $status == 'valid' || $status == 'update' || $status == 'selesai'){
            return '<label class="label label-outline-success">'.ucfirst($status).'</label>';
        }elseif($status == 'gagal' || $status == 'expired' || $status == 'error' || $status == 'canceled' || $status == 'pengurangan' || $status == 'removed' || $status == 'dibatalkan' || $status == 'unwithdraw'){
            return '<label class="label label-outline-danger">'.ucfirst($status).'</label>';
        }elseif($status == 'terblokir'){
            return '<label class="label label-outline-warning">Akun Terblokir</label>';
        }elseif($status == 'y'){
            return '<label class="label label-outline-primary">Ya</label>';
        }elseif($status == 't'){
            return '<label class="label label-outline-danger">Tidak</label>';
        }elseif($status == 'approved'){
            return '<label class="label label-outline-success">'.ucfirst($status).'</label>';
        }elseif($status == 'declined' || $status == 'dikemas'){
            return '<label class="label label-outline-default">'.ucfirst($status).'</label>';
        }
    }

    public static function logUser($keterangan){
        $pdo = DB::getPdo();
        $sql = 'INSERT INTO loguser VALUES(NULL, NOW(), :iduser, :keterangan)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':iduser', Session::get('iduser'));
        $stmt->bindValue(':keterangan', $keterangan);
        $stmt->execute();
    }

    public static function checkExist($table, $field, $where=''){
        $pdo = DB::getPdo();
        $sqlWhere = '';
        if($where != ''){
            $sqlWhere = ' WHERE '.$where;
        }
        $sql = 'SELECT '.$field.' FROM '.$table.$sqlWhere;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        if($stmt->rowCount() > 0){
            return 1;
        }else{
            return 0;
        }
    }

    public static function getAllData($table, $where = '', $jenis='OBJ'){
        $pdo = DB::getPdo();
        $sqlwhere = '';
        if($where != ''){
            $sqlwhere = ' WHERE '.$where; 
        }
        $sql = 'SELECT * FROM '.$table.$sqlwhere;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $hasil = array();
        if($stmt->rowCount() > 0){
            if($jenis == 'OBJ'){
                $hasil = $stmt->fetchAll(PDO::FETCH_OBJ);
            }else{
                $hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        return $hasil;
    }

    public static function getDataAll($table, $where = '', $jenis='OBJ'){
        $pdo = DB::getPdo();
        $sqlwhere = '';
        if($where != ''){
            $sqlwhere = ' WHERE '.$where; 
        }
        $sql = 'SELECT * FROM '.$table.$sqlwhere;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $hasil = array();
        if($stmt->rowCount() > 0){
            if($jenis == 'OBJ'){
                $hasil = $stmt->fetch(PDO::FETCH_OBJ);
            }else{
                $hasil = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
        return $hasil;
    }


    public static function getData($table, $field, $where=''){
        $pdo = DB::getPdo();
        $sqlWhere = '';
        if($where != ''){
            $sqlWhere = ' WHERE '.$where;
        }
        $sql = 'SELECT '.$field.' FROM '.$table.$sqlWhere;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $hasil = '';

        if($stmt->rowCount() > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hasil = $row[$field];
        }
        return $hasil;
    }

    public static function getCountDatatable($table, $bind, $where='', $search, $count = "COUNT(*)"){
        $pdo = DB::getPdo();
        $sqlWhere = '';
        if($where != ''){
            $sqlWhere = ' WHERE '.$where;
        }
        $sql = 'SELECT '.$count.' as jml FROM '.$table.$sqlWhere;
        $stmt = $pdo->prepare($sql);
        if(count($bind) > 0){
            for($i=0; $i < count($bind); $i++){
                $stmt->bindValue(':'.$bind[$i], $search, PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hasil = $row['jml'];
        }
        return $hasil;
    }

    public static function getHariFromAngka($angka){
        $hari = '';
        if($angka == 1){
            $hari = 'Minggu';
        }elseif($angka == 2){
            $hari = 'Senin';
        }elseif($angka == 3){
            $hari = 'Selasa';
        }elseif($angka == 4){
            $hari = 'Rabu';
        }elseif($angka == 5){
            $hari = 'Kamis';
        }elseif($angka == 6){
            $hari = "Jum'at";
        }elseif($angka == 7){
            $hari = 'Sabtu';
        }
        return $hari;
    }

    public static function formatAngka($angka,$koma=0){
        return number_format($angka,$koma,".",",");
    }

    public static function getHari($hari,$singkat='')
    {
        if($singkat == 'singkat') {
            $arrhari = array("", "Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab");
        }else{
            $arrhari = array("", "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
        }
        return $arrhari[$hari];
    }

    public static function getBulan($bulan, $singkat = '')
    {
        $bulan = strval(intval($bulan));
        if ($singkat == 'singkat') {
            $arrbulan = array("", "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");
        }
        else {
            $arrbulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        }
        return $arrbulan[$bulan];
    }

    public static function tanggalCantik($tanggal,$jenis="singkat")
    {
        $hasil = '';
        $jam = '';
        if($tanggal != '' && $tanggal != '0000-00-00'){
            if(strlen($tanggal) == 19){
                //format yy-mm-dd hh:mm:ss
                $split = explode(' ', $tanggal);
                $splittgl = explode('-', $split[0]);
                $tgl = $splittgl[2] + 0;
                $bln = $splittgl[1] + 0;
                $tahun = $splittgl[0];
                $jam = $split[1];
            } else {
                if (strpos($tanggal, '-') !== false) {
                    //format yy-mm-dd
                    $split = explode('-', $tanggal);
                    $tgl = $split[2] + 0;
                    $bln = $split[1] + 0;
                    $tahun = $split[0];
                } else {
                    //format yymmdd
                    $tgl = substr($tanggal, -2) + 0;
                    $tgl1 = substr($tanggal, -2);
                    $bln = substr($tanggal, 4, -2) + 0;
                    $bln1 = substr($tanggal, 4, -2);
                    $tahun = substr($tanggal, 0, -4);
                    if (strlen($tanggal) == 7) {
                        //format yymmd
                        $tgl = substr($tanggal, -1) + 0;
                        $tgl1 = '0' . substr($tanggal, -1);
                        $bln = substr($tanggal, 4, -1) + 0;
                        $bln1 = substr($tanggal, 4, -1);
                        $tahun = substr($tanggal, 0, -3);
                    }
                    $tanggal = $tahun . $bln1 . $tgl1;
                }
            }
            $pdo = DB::getPdo();

            //cari hari
            if(strlen($tanggal) == 19){
                $sql = 'SELECT DAYOFWEEK(DATE_FORMAT(:tanggal,"%Y-%m-%d %T")) as tgl';
            }else if (strpos($tanggal, '-') !== false) {
                $sql = 'SELECT DAYOFWEEK(:tanggal) as tgl';
            }else{
                $sql = 'SELECT DAYOFWEEK(DATE_FORMAT(:tanggal,"%Y%m%d")) as tgl';
            }
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':tanggal', $tanggal);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hari = $row['tgl'];

            $hasil = self::getHari($hari,$jenis) . ', ' . $tgl . ' ' . self::getBulan($bln,$jenis) . ' ' . $tahun.' '.$jam;
        }
        return $hasil;
    }

    public static function tanggalLahir($tanggal, $jenis='full')
    {
        $hasil = '';
        $jam = '';
        if($tanggal != '' && $tanggal != '0000-00-00'){
            if(strlen($tanggal) == 19){
                //format yy-mm-dd hh:mm:ss
                $split = explode(' ', $tanggal);
                $splittgl = explode('-', $split[0]);
                $tgl = $splittgl[2] + 0;
                $bln = $splittgl[1] + 0;
                $tahun = $splittgl[0];
                $jam = $split[1];
            } else {
                if (strpos($tanggal, '-') !== false) {
                    //format yy-mm-dd
                    $split = explode('-', $tanggal);
                    $tgl = $split[2] + 0;
                    $bln = $split[1] + 0;
                    $tahun = $split[0];
                } else {
                    //format yymmdd
                    $tgl = substr($tanggal, -2) + 0;
                    $tgl1 = substr($tanggal, -2);
                    $bln = substr($tanggal, 4, -2) + 0;
                    $bln1 = substr($tanggal, 4, -2);
                    $tahun = substr($tanggal, 0, -4);
                    if (strlen($tanggal) == 7) {
                        //format yymmd
                        $tgl = substr($tanggal, -1) + 0;
                        $tgl1 = '0' . substr($tanggal, -1);
                        $bln = substr($tanggal, 4, -1) + 0;
                        $bln1 = substr($tanggal, 4, -1);
                        $tahun = substr($tanggal, 0, -3);
                    }
                    $tanggal = $tahun . $bln1 . $tgl1;
                }
            }
            $pdo = DB::getPdo();

            //cari hari
            if(strlen($tanggal) == 19){
                $sql = 'SELECT DAYOFWEEK(DATE_FORMAT(:tanggal,"%Y-%m-%d %T")) as tgl';
            }else if (strpos($tanggal, '-') !== false) {
                $sql = 'SELECT DAYOFWEEK(:tanggal) as tgl';
            }else{
                $sql = 'SELECT DAYOFWEEK(DATE_FORMAT(:tanggal,"%Y%m%d")) as tgl';
            }
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':tanggal', $tanggal);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hari = $row['tgl'];

            $hasil = $tgl . ' ' . self::getBulan($bln,$jenis) . ' ' . $tahun.' '.$jam;
        }
        return $hasil;
    }

    public static function tanggalCantikDariSampai($tanggaldari,$tanggalsampai)
    {
        //formattanggal harus yyyy-mm-dd
        $hasil = '';
        if($tanggaldari != '' and $tanggalsampai != '' and $tanggaldari != '0000-00-00' and $tanggalsampai != '0000-00-00'){
            $pdo = DB::getPdo();

            //tanggal dari
            $split = explode('-', $tanggaldari);
            $tgldari = $split[2]+0;
            $tahundari = $split[0];

            //cari bulan tanggal dari
            $sql = 'SELECT MONTH(:tanggal) as bln';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':tanggal', $tanggaldari);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $bulandari = $row['bln'];

            //tanggal sampai
            $split = explode('-', $tanggalsampai);
            $tglsampai = $split[2]+0;
            $tahunsampai = $split[0];

            //cari bulan tanggal sampai
            $sql = 'SELECT MONTH(:tanggal) as bln';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':tanggal', $tanggalsampai);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $bulansampai = $row['bln'];

            if($tahundari != $tahunsampai){
                $hasil = $tgldari.' '.self::getBulan($bulandari, 'singkat').' '.$tahundari.' - '.$tglsampai.' '.self::getBulan($bulansampai, 'singkat').' '.$tahunsampai;
            }else{
                if($bulandari != $bulansampai){
                    $hasil = $tgldari.' '.self::getBulan($bulandari, 'singkat').' - '.$tglsampai.' '.self::getBulan($bulansampai, 'singkat').' '.$tahunsampai;
                }else{
                    if($tgldari != $tglsampai){
                        $hasil = $tgldari.' - '.$tglsampai.' '.self::getBulan($bulansampai, 'singkat').' '.$tahunsampai;
                    }else{
                        $hasil = $tgldari.' '.self::getBulan($bulandari, 'singkat').' '.$tahunsampai;
                    }
                }
            }
        }
        return $hasil;
    }

    //Format SQL ke format indonesia
    public static function convertYmd2Dmy($tanggal, $sparator='/'){
        $sparator == '/' ? $spr = "%d/%m/%Y" : $spr = "%d-%m-%Y";
        $pdo = DB::getPdo();
        $sql = 'SELECT DATE_FORMAT("'.$tanggal.'", "'.$spr.'") as tanggal';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['tanggal'];
    }

    //Format indonesia ke format SQL
    public static function convertDmy2Ymd($tanggal){
        $pdo = DB::getPdo();
        $sql = 'SELECT STR_TO_DATE("'.$tanggal.'","%d/%m/%Y") as tanggal';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['tanggal'];
    }

    public static function StartsWith($string, $startString)
    {
        $len = strlen($startString);
        return (strtolower(substr($string, 0, $len)) === strtolower($startString));
    }

    public static function getTimeMillis(){
        return round(microtime(true) * 1000);
    }

    public static function filterTanggal($tanggalawal,$tanggalakhir, $url, $autorefresh){
        $checked = "";
        $autorefresh == "" ?  $checked = "" : $checked = $autorefresh;
        return '<div class="form-group col-lg-2 col-md-4 col-sm-4 col-xs-3" style="padding-left:0px;width:22%;">
                    <label for="tanggalawal" class="control-label">Tanggal Awal</label>
                    <input style="cursor:pointer;" id="tanggalawal" class="form-control masktanggal" type="text" name="tanggalawal" value="'.$tanggalawal.'" data-inputmask="\'alias\': \'dd/mm/yyyy\'" spellcheck="false" autocomplete="off" data-provide="datepicker" data-date-today-highlight="true" data-date-today-btn="linked">
                </div>
                <div>
                    <label style="margin-top: 30px; float:left;">-</label>
                </div>
                <div class="form-group col-lg-2 col-md-4 col-sm-4 col-xs-3" style="width:24%">
                    <label for="tanggalakhir" class="control-label">Tanggal Akhir</label>
                    <input style="cursor:pointer;" id="tanggalakhir" class="form-control masktanggal" type="text" name="tanggalakhir" value="'.$tanggalakhir.'" data-inputmask="\'alias\': \'dd/mm/yyyy\'" spellcheck="false" autocomplete="off" data-provide="datepicker" data-date-today-highlight="true" data-date-today-btn="linked">
                </div>
                <div class="form-inline">
                    <div class="form-group pull-left">   
                        <button type="submit" class="btn btn-info" style="margin-top: 25px;"><i class="icon icon-search"></i> Cari</button>
                    </div>
                    <div class="form-group pull-left" style="margin-left:10px;">   
                        <a href="'.$url.'"><button type="button" class="btn btn-warning" style="margin-top: 25px;"><i class="icon icon-refresh"></i> Reset</button></a>
                    </div>
                    <div class="form-group" style="margin-left:30px; margin-top:35px">
                        <input type="checkbox" '.$checked.' id="autorefresh">&nbsp;&nbsp;
                    </div>
                    <div class="form-group">
                        <span onclick="spanClick(\'autorefresh\')" style="position: absolute; top:35px; cursor:pointer;">Auto Refresh</span>
                    </div>
                    
                </div>';
    }

    public static function getTanggal($menu){
        $tanggal = array();
        $tanggal['tglawal'] = "01".date('/m/Y');
        $tanggal['tglakhir'] = date("t/m/Y", strtotime(date("Y-m-d")));
        if(Session::has($menu.'_tglawal')){
            $tanggal['tglawal'] = Session::get($menu.'_tglawal');
        }else{
            Session::put($menu.'_tglawal', $tanggal['tglawal']);
        }
        if(Session::has($menu.'_tglakhir')){
            $tanggal['tglakhir'] = Session::get($menu.'_tglakhir');
        }else{
            Session::put($menu.'_tglakhir', $tanggal['tglakhir']);
        }
        return $tanggal;
    }

    public static function refreshStuff(){
        return '<div class="form-group" style="margin-top:30px;">
                    <input type="checkbox" id="autorefresh" checked style="margin-right:5px;">&nbsp;&nbsp;
                    <span onclick="spanClick(\'autorefresh\')" style="position:absolute;padding-bottom:10px;cursor:pointer;">Auto Refresh</span>
                </div>';
    }

    public static function getDataMultipleArray($tabel,$field,$where='',$type='all'){
        $pdo = DB::getPdo();
        $sqlwhere = '';
        if($where != ''){
            $sqlwhere = ' WHERE '.$where; 
        }
        $sql = 'SELECT '.$field.' FROM '.$tabel.$sqlwhere;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $hasil = array();
        if($stmt->rowCount() > 0){
            if($type == 'all'){
                $hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }else{
                $hasil = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
        return $hasil;
    }

    public static function getDataMultipleObj($tabel,$field,$where='',$type='all'){
        $pdo = DB::getPdo();
        $sqlwhere = '';
        if($where != ''){
            $sqlwhere = ' WHERE '.$where; 
        }
        $sql = 'SELECT '.$field.' FROM '.$tabel.$sqlwhere;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $hasil = array();
        if($stmt->rowCount() > 0){
            if($type == 'all'){
                $hasil = $stmt->fetchAll(PDO::FETCH_OBJ);
            }else{
                $hasil = $stmt->fetch(PDO::FETCH_OBJ);
            }
        }
        return $hasil;
    }

    public static function deleteData($table,$id, $par_id = 'id'){
        $pdo = DB::getPdo();
        $sql = 'DELETE FROM '.$table.' WHERE '.$par_id.' = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id',$id);
        $stmt->execute();
    }

    public static function deleteDataWhere($table, $where){
        $pdo = DB::getPdo();
        $sql = 'DELETE FROM '.$table.' WHERE '.$where;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    public static function strpos_arr($array, $find) {
        if(!is_array($array)) $array = array($array);
        foreach($array as $what) {
            if(($pos = strpos($find, $what))!==false) return 'ada';
        }
        return 'tidak';
    }

    public static function replaceAttrUang($nominal, $minus = 't'){
        if($minus == 't'){
            $result = str_replace('Rp ', '', str_replace('Rp. ','',str_replace(',','',str_replace('-','',$nominal))));
        }else{
            $result = str_replace('Rp ', '', str_replace('Rp. ','', str_replace(',','',$nominal)));
        }
        return $result;
    }

    public static function whereFilterSQL($field,$value,$type="like"){
        $resp = '';
        if($type == 'like'){
            $resp = $value != '' ? ' AND '.$field.' LIKE \'%'.$value.'%\'' : '';
        }else if($type == 'date'){
            $resp = $value != '' ? ' AND DATE('.$field.') = STR_TO_DATE(\''.$value.'\', \'%d/%m/%Y\')' : '';
        }else if($type == 'is'){
            $val = $value;
            if(!is_int($value)){
                $val = '\''.$value.'\'';
            }
            $resp = $value != '' ? ' AND '.$field.' = '.$val : '';
        }
        return $resp;
    }

    public static function mainFilter($sessionName, $idx, $where){
        if(Session::has($sessionName.'_filter') && Session::get($sessionName.'_filter')['client'] != '-'){
            $where .= ' AND idclient = '.Session::get($sessionName.'_filter')['client'];
        }
        if(Session::has($sessionName.'_filter') && Session::get($sessionName.'_filter')['produk'] != '-'){
            $where .= ' AND idproduk = '.Session::get($sessionName.'_filter')['produk'];
        }
        if(Session::has($sessionName.'_filter') && Session::get($sessionName.'_filter')['supplier'] != '-'){
            $where .= ' AND idsupplier = '.Session::get($sessionName.'_filter')['supplier'];
        }
        if(Session::has($sessionName.'_filter') && Session::get($sessionName.'_filter')['status'] != '-'){
            $where .= ' AND '.$idx.'.`status` = \''.Session::get($sessionName.'_filter')['status'].'\'';
        };

        return $where;
    }

    public static function reverseCurrencyFormat($currency){
        $result = str_replace('Rp ','',str_replace('Rp. ','',str_replace(',','',str_replace('-','',$currency))));
        return $result;
    }

    public static function filterEachItem($sessionName, $field, $where, $type = 'select', $key="", $alias=""){
        $filterDef = '';
        $filter = ' AND '.$field.' = '.Session::get($sessionName.'_filter')[$field];
        if($type == 'text' && $key == 'like'){
            $filter = ' AND '.$field.' LIKE \'%'.Session::get($sessionName.'_filter')[$field].'%\'';
        }elseif($type == 'date'){
            $filter =' AND DATE('.$field.') = STR_TO_DATE(\''.Session::get($sessionName.'_filter')[$field].'\', \'%d/%m/%Y\')';
        }elseif($type == 'currency'){
            $filter = ' AND '.$field.' = '.Utils::reverseCurrencyFormat(Session::get($sessionName.'_filter')[$field]);
        }elseif(($type == 'text' || $type == 'select') && $key=='char'){
            $filter = ' AND '.$field.' = \''.Session::get($sessionName.'_filter')[$field].'\'';
        }

        if($alias != '' && $key == 'char'){
            $filter = ' AND '.$alias.'.'.$field.' = \''.Session::get($sessionName.'_filter')[$field].'\'';
        }elseif($alias != '' && $key== ''){
            $filter = ' AND '.$alias.'.'.$field.' = '.Session::get($sessionName.'_filter')[$field];
        }

        if($type == 'select'){
            $filterDef = Session::get($sessionName.'_filter')[$field] != '-';
        }elseif($type == 'text' || $type == 'date' || $type == 'currency'){
            $filterDef = Session::get($sessionName.'_filter')[$field] != '';
        }

        if(Session::has($sessionName.'_filter') &&  $filterDef){
            $where .= $filter;
        }
        return $where;
        
    }

    public static function GetTimeJadwalBeroperasi($id, $hari){
        $pdo = DB::getPdo();
        $sql = 'SELECT GROUP_CONCAT(waktuawal, " - ", waktuakhir SEPARATOR " , ") as waktu FROM jadwalberoperasi_waktu WHERE idjadwalberoperasi = :id AND hari = :hari';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':hari', $hari);
        $stmt->execute();
        $hasil = '';

        if($stmt->rowCount() > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hasil .= $row['waktu'];
        }
        return $hasil;

    }

    public static function periodeCantik($yymm) {
        $hasil = '';
        if($yymm != ''){
            $tahun = '20'.substr($yymm, 0, 2);
            $bln = intval(substr($yymm, 2));
    
            $arrbulan = array("","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
            $hasil = $arrbulan[$bln].' '.$tahun;
        }
        return $hasil;
    }
    
    public static function dateOfYesterday($format = "d/m/Y", $jml_hari = '1'){
        $date = date($format,mktime(0, 0, 0, date("m"), date("d")-$jml_hari,date("Y")));
        return $date;
    }

    public static function getTotalData($tabel,$where=''){
        $pdo = DB::getPdo();
        $sqlwhere = '';
        if($where != ''){
            $sqlwhere .= ' AND '.$where;
        }
        $sql = 'SELECT COUNT(*) as total FROM '.$tabel.' WHERE 1=1 '.$sqlwhere;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row =  $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    static function generateRandomString2($length = 10, $abc = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") {
        return substr(str_shuffle($abc), 0, $length);
    }

    //excel export utils
    public static function setHeaderStyleExcel($objPHPExcel,$arrWidth,$styleArray=array())
    {
        for ($j = 0; $j < count($arrWidth); $j++) {
            $huruf = Utils::angkaToHuruf($j+1);
            $objPHPExcel->getActiveSheet()->getStyle($huruf . '1')->getFont()->setBold(true);
            if(count($styleArray) > 0){
                $objPHPExcel->getActiveSheet()->getStyle($huruf . '1')->applyFromArray($styleArray);
            }
            $objPHPExcel->getActiveSheet()->getColumnDimension($huruf)->setWidth($arrWidth[$j]);
        }
    }
    
    public static function setPropertiesExcel($objPHPExcel,$menu='')
    {
        $objPHPExcel->getProperties()->setCreator('Pakaidonk')
                    ->setLastModifiedBy('Pakaidonk')
                    ->setTitle($menu == '' ? 'Office 2007 XLSX Document' : $menu)
                    ->setSubject($menu == '' ? 'Office 2007 XLSX Document' : $menu);
    }

    public static function passwordExcel($objPHPExcel,$password)
    {
        $objPHPExcel->getSecurity()->setLockWindows(true);
        $objPHPExcel->getSecurity()->setLockStructure(true);
        $objPHPExcel->getSecurity()->setWorkbookPassword($password);
        $objPHPExcel->getActiveSheet()->getProtection()->setPassword($password);
        $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
        $objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
        $objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
        $objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
    }

    public static function setFileNameExcel($name, $date = '')
    {
        $header = '';
        $date == '' ? $header = date('dmY') : $header = $date;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' .  $header . '_' . $name . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
    }

    public static function angkaToHuruf($num) {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return self::angkaToHuruf($num2) . $letter;
        } else {
            return $letter;
        }
    }

    public static function GetDateDif($tanggal_awal, $tanggal_akhir){
        $pdo = DB::getPdo();
        $sql = "SELECT DATEDIFF(:tanggal_akhir, :tanggal_awal) as marginDate";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':tanggal_awal', self::convertDmy2Ymd($tanggal_awal));
        $stmt->bindValue(':tanggal_akhir', self::convertDmy2Ymd($tanggal_akhir));
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data->marginDate;
    }

    public static function tanggalPeriode($date){
        if(strlen($date) == 8){
            $d = substr($date, 0, 2);
            $m = substr($date, 2, 2);
            $y = substr($date, 4,4);
            $newDate = $y.'-'.$m.'-'.$d;
            return date('d M Y', strtotime($newDate));
        }
    }

    public static function jsoncURL($url,$headers,$type,$postdata=''){
        $ch = curl_init();
        if($type == 'GET') {
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => $headers
            ));
        }else{
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $type,
                CURLOPT_POSTFIELDS => json_encode($postdata),
                CURLOPT_HTTPHEADER => $headers
            ));
        }
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        $response = array();
        $response['result'] = $result;
        $response['httpcode'] = $httpcode;
        return $response;
    }

    public static function filecURL($url, $header, $payload) {
        $parameter = json_decode($payload, true);
        $src_file = $parameter['src_file']; //contoh: /var/www/213892173_1371234564_100000_1552912312.png
        $src_md5 = md5_file($src_file);
        $dest_path = $parameter['dest_path']; //contoh: /buktitransfer/20190101
        $dest_filename = $parameter['dest_filename']; //contoh: 213892173_1371234564_100000_1552912312.png

        $curl_src_file = curl_file_create($src_file);

        $postfields["file"] = $curl_src_file;
        $postfields["checksum"] = $src_md5;
        $postfields["dest_path"] = $dest_path;
        $postfields["dest_filename"] = $dest_filename;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($header==null) {
            $header = array();
        }

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(
                array("Content-Type" => "multipart/form-data"),
                $header
            )
        );
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        $response = array();
        $response['result'] = $result;
        $response['httpcode'] = $httpcode;
        return $response;
    }

    public static function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }

    public static function KirimFCM($registration_ids, $id, $cmd, $msg, $time_to_live=300) {
        try {
            $fields = array();
            $fields['data'] = array();
            $fields['data']['id'] = $id;
            $fields['data']['cmd'] = $cmd;
            $fields['data']['msg'] = $msg;
            $fields['priority'] = 'high';
            if (count($registration_ids)==1) {
                $fields['to'] = $registration_ids[0];
            } else {
                $fields['registration_ids'] = $registration_ids;
            }
            $fields['time_to_live'] = $time_to_live;

            $headers = array(
                'Authorization: key='.config('consts.FCM_KEY'),
                'Content-Type: application/json'
            );

            // Open connection
            $ch = curl_init();

            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, config('consts.FCM_URL'));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

            curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {

        }
    }

    public static function KirimMemberFCM($idmember, $id, $cmd, $msg, $time_to_live=300) {
        $pdo = DB::getPdo();
        $sql = 'SELECT distinct(fcmid) as fcmid FROM member_fcm WHERE idmember=:idmember';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idmember', $idmember);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $registration_ids = array();
        for ($i=0;$i<count($row);$i++){
            $fcmid = $row[$i]['fcmid'];

            $sql = 'INSERT INTO log_fcm VALUES(NULL, \'member\', :idmember, :fcmid, :payload_cmd, :payload_msg, NOW())';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':idmember', $idmember);
            $stmt->bindValue(':fcmid', $fcmid);
            $stmt->bindValue(':payload_cmd', $cmd);
            $stmt->bindValue(':payload_msg', $msg);
            $stmt->execute();

            $registration_ids[$i] = $fcmid;

        }
        if (count($registration_ids)>0) {
            self::KirimFCM($registration_ids, $id, $cmd, $msg, $time_to_live);
        }
    }

    public static function prettyJson($json_data, $id='pre_json'){
        // $json_data = preg_replace('/\s+/', '', $json_data);
        $hasil = '';
        //Initialize variable for adding space
        $space = 0;
        $flag = false;
        //Using <pre> tag to format alignment and font
        $hasil .= "<pre id='".$id."'>";
        //loop for iterating the full json data
        for($counter=0; $counter<strlen($json_data); $counter++)
        {
            //Checking ending second and third brackets
            if ( $json_data[$counter] == '}' || $json_data[$counter] == ']' )
            {
                $space--;
                $hasil .="\n";
                $hasil .=str_repeat(' ', ($space*2));
            }

            //Checking for double quote(“) and comma (,)
            if ( $json_data[$counter] == '"' && ($json_data[$counter-1] == ',' ||
                    $json_data[$counter-2] == ',') )
            {
                $hasil .="\n";
                $hasil .=str_repeat(' ', ($space*2));
            }
            if ( $json_data[$counter] == '"' && !$flag )
            {
                if ( $json_data[$counter-1] == ':' || $json_data[$counter-2] == ':' )

                    //Add formatting for question and answer
                    $hasil .='<span style="color:blue;font-weight:bold">';
                else

                    //Add formatting for answer options
                    $hasil .='<span style="color:red;">';
            }
            $hasil .=$json_data[$counter];
            //Checking conditions for adding closing span tag
            if ( $json_data[$counter] == '"' && $flag )
                $hasil .='</span>';
            if ( $json_data[$counter] == '"' )
                $flag = !$flag;

            //Checking starting second and third brackets
            if ( $json_data[$counter] == '{' || $json_data[$counter] == '[' )
            {
                $space++;
                $hasil .="\n";
                $hasil .=str_repeat(' ', ($space*2));
            }
        }
        $hasil .= "</pre>";
        return $hasil;
    }

    public static function getBirthdateFromKTP($ktp){
        $tgl = substr($ktp, 6, 2);
        $bln = substr($ktp, 8, 2);
        $th = DateTime::createFromFormat('y', substr($ktp, 10, 2))->format('Y');
        $date = date('d/m/Y',strtotime($th.'-'.$bln.'-'.$tgl));
        return $date;
    }

    public static function insert_login_log_ip($kategori){
        $ipaddress = Request::ip();
        $pdo =DB::getPdo();
        $stmt = $pdo->prepare('INSERT INTO login_log_ip VALUES(:ipaddress, :kategori, NOW())');
        $stmt->bindValue(':ipaddress', $ipaddress);
        $stmt->bindValue(':kategori', $kategori);
        $stmt->execute();

        $maks_bruteforce = Config('consts.LIMIT_JML_BRUTEFORCE');

        $jml_failed = self::getData('login_log_ip', 'COUNT(*)', 'ipaddress = "'.$ipaddress.'" AND inserted >= ADDDATE(NOW(), INTERVAL - '.Config('consts.LIMIT_WAKTU_BRUTEFORCE').' MINUTE) AND inserted <= NOW()');
        if($jml_failed >= $maks_bruteforce){
            $cek_exists = self::checkExist('login_blokir_ip', 'ipaddress', 'ipaddress = "'.$ipaddress.'"');
            if($cek_exists == 0){
                $stmt = $pdo->prepare('INSERT INTO login_blokir_ip VALUES(NULL, :ipaddress, :keterangan, NOW())');
                $stmt->bindValue(':ipaddress', $ipaddress);
                $stmt->bindValue(':keterangan', 'Diblokir karena sistem menilai ada serangan brute force melalui IP : '.$ipaddress.' tersebut');
                $stmt->execute();
            }
        }
    }

    public static function checkPrivilege($akses){
        if($akses == '' || strpos($akses, 'l') === false){
            return abort(404);
        }
    }


    public static function checkAuth(){
        if (!Auth::check()) {
            return abort('401');
        }
    }

}