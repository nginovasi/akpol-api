<?php

namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;

class WebMain extends BaseController
{
    private $webModel;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->webModel = new WebModel();
    }

    public function index()
    {
        return redirect()->to(base_url());
    }

    // ajax ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


    function data_main()
    {
        $aktiv = "  SELECT IFNULL(COUNT(a.id), 0) AS jml_aktiv 
                    FROM m_user_taruna a 
                    LEFT JOIN m_sm_batalyon b ON a.id_batalyon = b.id AND a.id_semester = b.id_semester 
                    WHERE b.id_semester < '9' AND a.is_verif = '1' AND a.is_deleted = '0' AND b.angkatan IN (54,55,56,57);
        "; // aktiv

        $alumni = "SELECT IFNULL(COUNT(a.id), 0) AS jml_alumni FROM m_user_taruna a LEFT JOIN m_sm_batalyon b ON a.id_batalyon=b.id AND a.id_semester=b.id_semester WHERE b.id_semester='9'  and a.id_batalyon!='6' AND a.is_verif = '1' AND a.is_deleted = '0' "; // alumni

        $tingkat = "    SELECT b.angkatan, 
                                SUM(CASE WHEN a.is_verif = 1 THEN 1 ELSE 0 END) AS jml_verif,
                                SUM(CASE WHEN a.is_verif = 0 THEN 1 ELSE 0 END) AS jml_unverif
                        FROM m_user_taruna a
                        JOIN m_sm_batalyon b ON a.id_batalyon = b.id
                        WHERE b.angkatan IN (54,55,56,57) AND a.is_deleted = 0
                        GROUP BY b.angkatan
                    ";
        $dalam = "SELECT ifnull(count(id), 0) as pendidik_dalam from m_user_pendidik where is_internal='1'";
        $luar = "SELECT ifnull(count(id), 0) as pendidik_luar from m_user_pendidik where is_internal='0'";
        $pelanggaran_taruna = "SELECT 
                        b.id_m_user,
                        b.namataruna,
                        b.noaklong,
                        b.photopath,
                        c.dasar_hukum,
                        a.is_approve
                    from t_pelanggaran_karakter_taruna a
                     join m_user_taruna b
                        on a.id_taruna = b.id_m_user
                    left join m_pelanggaran_karakter c
                        on a.id_pelanggaran_karakter = c.id
                    where date_format(a.created_at,'%Y%m') = date_format(curdate(),'%Y%m')
                    order by a.created_at desc";

        $absenterbaru = "SELECT 
                            c.kode_mk,
                            c.mata_pelajaran,
                            d.namagadik as gadik,
                            d.photopath as photo_gadik,
                            e.namagadik as ketua_tim,
                            e.photopath as photo_gadik,
                            date_format(a.absen_at,'%d %M %Y %h:%i:%s') as absen,
                            a.lat_absen,
                            a.long_absen,
                            f.kelompok
                            
                        from t_jadwal a
                        left join t_bahan_ajar b
                            on a.id_bahan_ajar = b.id
                        left join m_mata_pelajaran c
                            on c.id = b.id_mata_pelajaran
                        left join m_user_pendidik d
                            on d.id_m_user = a.id_user_pendidik
                        left join m_user_pendidik e
                            on e.id_m_user = b.id_user_pendidik
                                    left join `m_kelompok` f 
                                        on f.`id`=a.`id_kelompok_taruna`
                        where date(a.absen_at) = curdate()
                        and a.is_deleted = 0
                        and a.is_absensi_pendidik = 1";

        $kalenderakademik = " SELECT a.* from m_kalender_akademik a where a.is_deleted='0' ";


        $result['kalenderakademik'] = $this->db->query($kalenderakademik)->getResult();
        $result['aktiv'] = $this->db->query($aktiv)->getRow();
        $result['alumni'] = $this->db->query($alumni)->getRow();
        $result['tingkat'] = $this->db->query($tingkat)->getResult();
        $result['dalam'] = $this->db->query($dalam)->getRow();
        $result['luar'] = $this->db->query($luar)->getRow();
        $result['pelanggaran_taruna'] = $this->db->query($pelanggaran_taruna)->getResult();
        $result['absenterbaru'] = $this->db->query($absenterbaru)->getResult();

        echo json_encode(array('success' => true, 'data' => $result));
    }


    function datataruna_load()
    {

        $id_taruna = $this->request->getPost('id_taruna');

        $tugastaruna = "SELECT  d.mata_pelajaran, if(a.tipe_tugas=0, 'Kumpulkan Di Kelas' , 'Kumpulkan Online') as pengumpulan, DATE_FORMAT(a.waktu_pengumpulan, '%d/%m/%Y') as waktu_pengumpulan , DATE_FORMAT(a.waktu_pengumpulan, '%h:%i') as jam_pengumpulan , a.judul, t.file_tugas, t.nilai
                from t_nilai_tugas t
                left join t_jadwal_tugas a on t.id_jadwal_tugas = a.id
                left join t_jadwal b on a.id_jadwal=b.id
                left join t_bahan_ajar c on b.id_bahan_ajar=c.id
                left join m_mata_pelajaran d on c.id_mata_pelajaran=d.id
                left join m_kelompok e on b.id_kelompok_taruna=e.id
                where a.is_deleted='0' and t.id_user_taruna = '" . $id_taruna . "'";

        $jmlmatkul = "SELECT count(a.id) as jml
                    from m_user_taruna a 
                    left join t_program_studi_mata_pelajaran b
                        on a.id_semester = b.id_semester
                        and a.id_batalyon = b.id_batalyon
                    where 
                        a.id_m_user =  '" . $id_taruna . "' and
                        b.is_deleted = 0";

        $tingkat = "SELECT c.`tingkatan`, b.semester FROM m_user_taruna a 
                        LEFT JOIN m_semester b ON a.id_semester=b.id
                        LEFT JOIN m_tingkatan c ON b.id_tingkat=c.id
                        WHERE a.id_m_user = '" . $id_taruna . "' ";

        $absen = "SELECT ifnull(ceil((sum(if(is_absen=1 , total , '0'))*100)/sum(total)) ,0) as total from (
                            SELECT d.is_absen , count(d.is_absen) as total
                            from m_user_taruna a 
                            left join (select * from t_bahan_ajar where is_deleted = 0) b
                                on a.id_semester = b.id_semester
                                and a.id_batalyon = b.id_batalyon
                            left join (select * from t_jadwal where is_deleted = 0) c
                                on b.id = c.id_bahan_ajar
                                and a.id_kelompok = c.id_kelompok_taruna
                            join t_absensi d
                                on d.id_taruna = a.id_m_user
                                and d.id_jadwal = c.id
                            where a.id_m_user = '" . $id_taruna . "' and c.is_deleted='0' and d.is_deleted='0'
                group by is_absen ) a";

        $jadwal = "SELECT g.mata_pelajaran, DATE_FORMAT(a.tanggal, '%d/%m/%Y') AS tanggal, f.namagadik AS nama_pendidik, c.nama AS nama_ruang_kelas, DATE_FORMAT(d.jam_mulai, '%H:%i') AS jam_mulai, DATE_FORMAT(d.jam_selesai, '%H:%i') AS jam_selesai
                                    from t_jadwal a
                                    left join m_kelompok b
                                        on a.id_kelompok_taruna = b.id
                                    left join m_ruang_kelas c
                                        on a.id_ruang_kelas = c.id
                                    left join m_jam_pertemuan d
                                        on a.id_jam_pertemuan = d.id
                                    left join t_bahan_ajar e
                                        on a.id_bahan_ajar = e.id
                                    left join m_user_pendidik f
                                        on a.id_user_pendidik = f.id_m_user
                                    left join m_mata_pelajaran g
                                        on e.id_mata_pelajaran = g.id
                                    left join m_sm_batalyon h on e.id_semester=h.id_semester and h.id=e.id_batalyon
                                    left join t_program_studi_mata_pelajaran i on e.id_semester=i.id_semester and e.id=i.id_batalyon and e.id_mata_pelajaran=i.id_mata_pelajaran
                                    join m_user_taruna j on b.id=j.id_kelompok and h.id=j.id_batalyon
                                    where a.is_deleted = 0
                                        and b.is_deleted = 0
                                        and c.is_deleted = 0
                                        and d.is_deleted = 0
                                        and d.is_weekdays = 1
                                        and e.is_deleted = 0
                                        and f.is_deleted = 0
                                        and g.is_deleted = 0
                                        and (a.tanggal BETWEEN CURDATE() and DATE_ADD(CURDATE(), INTERVAL 7 DAY) )
                                        and j.id_m_user='" . $id_taruna . "' group by a.id ORDER BY a.tanggal ASC ";


        $pengumuman = "SELECT a.*, b.name from 
        (select id, judul, deskripsi, 'internal' as jenis, created_by, DATE_FORMAT(created_at, '%d-%m-%Y') as date_short, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i:%s') as created_at from t_informasi_pendidik where is_deleted='0'
        union
        select id, judul, deskripsi, 'umum' as jenis, created_by, DATE_FORMAT(created_at, '%d-%m-%Y') as date_short, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i:%s') as created_at from m_pengumuman where is_deleted='0') a
        left join m_user b on a.created_by = b.id order by a.created_at DESC;";

        $pelanggaran = "SELECT 
                        a.poin,
                        DATE_FORMAT(a.created_at,'%d-%m-%Y') as tgl,
                        b.id_m_user,
                        b.namataruna,
                        b.noaklong,
                        b.photopath,
                        c.dasar_hukum,
                        c.deskripsi,
                        a.is_approve
                    from t_pelanggaran_karakter_taruna a
                     join m_user_taruna b
                        on a.id_taruna = b.id_m_user
                    left join m_pelanggaran_karakter c
                        on a.id_pelanggaran_karakter = c.id
                    where a.id_taruna='" . $id_taruna . "'
                    order by a.created_at desc ";

        $kalenderakademik = " SELECT a.* from m_kalender_akademik a where a.is_deleted='0' ";


        $result['kalenderakademik'] = $this->db->query($kalenderakademik)->getResult();

        $result['tugastaruna'] = $this->db->query($tugastaruna)->getResult();
        $result['jmlmatkul'] = $this->db->query($jmlmatkul)->getRow()->jml;
        $result['semester'] = $this->db->query($tingkat)->getRow()->semester;
        $result['tingkat'] = $this->db->query($tingkat)->getRow()->tingkatan;
        $result['absen'] = $this->db->query($absen)->getRow()->total;
        $result['jadwal'] = $this->db->query($jadwal)->getResult();
        $result['pengumuman'] = $this->db->query($pengumuman)->getResult();
        $result['pelanggaran'] = $this->db->query($pelanggaran)->getResult();

        echo json_encode(array('success' => true, 'data' => $result));
    }

    function datagubernur_load()
    {
        $aktiv = "SELECT IFNULL(COUNT(a.id), 0) AS jml_aktiv FROM m_user_taruna a LEFT JOIN m_sm_batalyon b ON a.id_batalyon=b.id AND a.id_semester=b.id_semester WHERE b.id_semester<'9' and a.is_deleted='0'"; // aktiv
        $alumni = "SELECT IFNULL(COUNT(a.id), 0) AS jml_alumni FROM m_user_taruna a LEFT JOIN m_sm_batalyon b ON a.id_batalyon=b.id AND a.id_semester=b.id_semester WHERE b.id_semester='9' and a.is_deleted='0'"; // alumni
        // $tingkat = "SELECT a1.id, a1.tingkatan , count(b1.id) as jml from m_tingkatan a1 left join (select a.* , b.id_tingkatan from m_user_taruna a left join m_tingkatan_detail b on a.id_tingkat_=b.id where a.is_deleted='0') b1 on b1.id_tingkatan=a1.id group by a1.tingkatan";
        $tingkat = "SELECT a1.id, a1.tingkatan , count(b1.id) as jml from m_tingkatan a1 left join 
                        (select a.* , c.id_tingkatan from m_user_taruna a 
                        left join m_sm_batalyon b on a.id_batalyon=b.id
                        left join m_tingkatan_detail c on b.id_semester=c.id
                        where a.is_deleted='0') b1 
                    on b1.id_tingkatan=a1.id group by a1.tingkatan";
        $pendidik = "SELECT ifnull(count(id), 0) as jml_pendidik from m_user_pendidik where is_deleted='0'";
        $dalam = "SELECT ifnull(count(id), 0) as pendidik_dalam from m_user_pendidik where is_internal='1' and is_deleted='0'";
        $luar = "SELECT ifnull(count(id), 0) as pendidik_luar from m_user_pendidik where is_internal='0' and is_deleted='0'";
        $pelanggaran_taruna = "SELECT 
                        b.id_m_user,
                        b.namataruna,
                        b.noaklong,
                        b.photopath,
                        c.dasar_hukum,
                        a.is_approve
                    from t_pelanggaran_karakter_taruna a
                     join m_user_taruna b
                        on a.id_taruna = b.id_m_user
                    left join m_pelanggaran_karakter c
                        on a.id_pelanggaran_karakter = c.id
                    where date_format(a.created_at,'%Y%m') = date_format(curdate(),'%Y%m')
                    order by a.created_at desc";

        $absenterbaru = "SELECT 
                            c.kode_mk,
                            c.mata_pelajaran,
                            d.namagadik as gadik,
                            d.photopath as photo_gadik,
                            e.namagadik as ketua_tim,
                            e.photopath as photo_gadik,
                            date_format(a.absen_at,'%d %M %Y %h:%i:%s') as absen,
                            a.lat_absen,
                            a.long_absen,
                            f.kelompok
                            
                        from t_jadwal a
                        left join t_bahan_ajar b
                            on a.id_bahan_ajar = b.id
                        left join m_mata_pelajaran c
                            on c.id = b.id_mata_pelajaran
                        left join m_user_pendidik d
                            on d.id_m_user = a.id_user_pendidik
                        left join m_user_pendidik e
                            on e.id_m_user = b.id_user_pendidik
                                    left join `m_kelompok` f 
                                        on f.`id`=a.`id_kelompok_taruna`
                        where date(a.absen_at) = curdate()
                        and a.is_deleted = 0
                        and a.is_absensi_pendidik = 1";

        $mapelpersmt = "SELECT a.id, a.semester, a.jabatan, e.batalyon, b.mata_pelajaran, count(b.id) as tot_mata_pelajaran
        from m_semester a 
        left join t_program_studi_mata_pelajaran c on c.id_semester = a.id
        left join m_mata_pelajaran b on b.id = c.id_mata_pelajaran and b.is_deleted = '0'
        left join m_aspek d on c.id_aspek = d.id
        left join m_sm_batalyon e on c.id_batalyon = e.id
        where a.is_deleted = '0' and a.id < 9
        group by a.id;";

        $pengumuman = "SELECT a.*, b.name from 
        (select id, judul, deskripsi, 'internal' as jenis, created_by, DATE_FORMAT(created_at, '%d-%m-%Y') as date_short, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i:%s') as created_at from t_informasi_pendidik where is_deleted='0'
        union
        select id, judul, deskripsi, 'umum' as jenis, created_by, DATE_FORMAT(created_at, '%d-%m-%Y') as date_short, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i:%s') as created_at from m_pengumuman where is_deleted='0') a
        left join m_user b on a.created_by = b.id order by a.created_at DESC;";

        $chart_taruna = "SELECT 
        a.semester, 
        a.jabatan, 
        count(case when b.id_gender = 1 then 1 end) as tot_taruna,
        count(case when b.id_gender= 2 then 1 end) as tot_taruni,
        count(b.id) as total
        from m_semester a
        left join m_user_taruna b on b.id_semester = a.id and b.is_deleted='0'
        where a.is_deleted = '0' and a.id < 9
        group by a.id;";

        $chart_batalyon = "SELECT 
        a.tahun_masuk, 
        a.batalyon, 
        a.angkatan,
        c.namagadik,
        a.id_user_pendidik,
        count(case when b.id_gender = 1 then 1 end) as tot_taruna,
        count(case when b.id_gender= 2 then 1 end) as tot_taruni,
        count(b.id) as total
        from m_sm_batalyon a
        left join m_user_taruna b on b.id_batalyon = a.id and b.is_deleted='0'
        left join m_user_pendidik c on c.id_m_user = a.id_user_pendidik
        where a.is_deleted = '0'
        group by a.id;";

        $kalenderakademik = " SELECT a.* from m_kalender_akademik a where a.is_deleted='0' ";


        $result['kalenderakademik'] = $this->db->query($kalenderakademik)->getResult();

        $result['aktiv'] = $this->db->query($aktiv)->getRow();
        $result['pendidik'] = $this->db->query($pendidik)->getRow();
        $result['alumni'] = $this->db->query($alumni)->getRow();
        $result['tingkat'] = $this->db->query($tingkat)->getResult();
        $result['dalam'] = $this->db->query($dalam)->getRow();
        $result['luar'] = $this->db->query($luar)->getRow();
        $result['pelanggaran_taruna'] = $this->db->query($pelanggaran_taruna)->getResult();
        $result['absenterbaru'] = $this->db->query($absenterbaru)->getResult();
        $result['mapelpersmt'] = $this->db->query($mapelpersmt)->getResult();
        $result['pengumuman'] = $this->db->query($pengumuman)->getResult();
        $result['chart_taruna'] = $this->db->query($chart_taruna)->getResult();
        $result['chart_batalyon'] = $this->db->query($chart_batalyon)->getResult();

        echo json_encode(array('success' => true, 'data' => $result));
    }

    // end ajax ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


    // controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function pengumuman_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $file_banner = $this->request->getFile('file_banner');
        if ($file_banner != null) {
            if ($file_banner->isValid() && !$file_banner->hasMoved()) {
                $newName = $file_banner->getRandomName();
                $file_banner->move('./public/file_banner/pengumuman/' . $userid, $newName);
                $dir = base_url() . '/public/file_banner/pengumuman/' . $userid . '/' . $newName;
                $data['banner_path'] = $dir;
            }
        }

        parent::_insert('m_pengumuman', $data, $userid);

        $getuser = $this->db->query("SELECT * from m_user a where a.fcm_token is not null and a.fcm_token!=''")->getResult();

        foreach ($getuser as $obj) {
            $token = $obj->fcm_token;
            $header = 'PENGUMUMAN';
            $deskripsi = $data['judul'];
        
            $this->handlertoken($token,$header,$deskripsi);
        }
    }

    function handlertoken($token,$header,$deskripsi){
        header('Content-Type: application/json');

        $url = "https://fcm.googleapis.com/fcm/send";
        $token = $token;
        // $token = "dcKM8A1eSca9b7j1LcJYg-:APA91bHC2779MS4oXIJ6_PBS-v9ruQiTwxKBkoLyZfSJSOdQYT6ObCC0e26CcNA82t8m_DlixPmGCvjNsa-bo99zO5l36swtCox8eHFMKv78JKaZvbdHm7BS6l9KJKbd1jDGuVjH7osd";
        // $token = "djPQP8rZAOc:APA91bG6mrEQlNn6cwC9t-ulUKnt5deY-4K1B1A3qrhRmKo1h_mjMUIjfdZxxx5R4SmXy8OU-folEeMs4C_F068rSxTtX49u_wzb51c6aL64Z_1rLsFnom0J9Kpkq7vqhOnNr3ydKg3I";
        $serverKey = 'AAAA4M5pR9Y:APA91bEI3mJRb3ANqZHdXELq9ywYw3wzzI-2lH7OQPjk5jxtvAlpcCvRqRATWLrTgCGG2DripXZE29oYXQp0rsJiQRZe-5Mdr7uOsi1qFrozgRptz9G6jwRa9ZNa4yMuo_mIHLpy1cS8';
        $title = $header;
        $body = "".$deskripsi."";
        $data = array(
            'header'        => $header,
            'deskripsi'     => $deskripsi
        );
        $notification = array('title' =>$title , 'body' => $body, 'sound' => 'default');
        $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high', 'data'=>$data);
        $json = json_encode($arrayToSend);

        // print_r(expression)

        // echo $json;
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //Send the request
        $response = curl_exec($ch);
        //Close request
        if ($response === FALSE) {
        /*
        $info = curl_getinfo($ch);
        $datapost['res'] = curl_errno($ch);
        $datapost['datapost'] = json_encode($data);
        $datapost['ip'] = $this->input->ip_address();
        $datapost['origin'] = 'ngi';
        $this->db->insert("log_post2",$datapost);
        */
            die('FCM Send Error: ' . curl_error($ch));
        }
        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            // $datapost['res'] = json_encode($info);
            // $datapost['datapost'] = json_encode($data);
            // $datapost['ip'] = $this->input->ip_address();
            // $datapost['origin'] = 'ngi';
            // $this->db->insert("log_post2",$datapost);
        }
        curl_close($ch);
    }

    // end controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

}
