<?php

namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;

class WebAkademik extends BaseController
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

    function pelanggarankompi_list_get()
    {
        $data = $this->request->getGet();

        $query = "SELECT a.id , a.poin, a.is_approve, b.dasar_hukum, b.deskripsi, c.namataruna ,  d.semester, f.batalyon, g.namagadik, a.created_at, e.foto, 'Pelanggaran' as is_pelanggaran
                    from t_pelanggaran_karakter_taruna a
                    inner join m_pelanggaran_karakter b on a.id_pelanggaran_karakter=b.id and b.is_deleted='0'
                    inner join m_user_taruna c on a.id_taruna=c.id_m_user and c.is_deleted='0'
                    inner join m_semester d on a.id_semester=d.id
                    inner join t_bukti_pelanggaran_karakter_taruna e on a.id=e.id_pelanggaran_karakter
                    inner join m_sm_batalyon f on c.id_batalyon=f.id
                    inner join m_user_pendidik g on a.created_by=g.id_m_user
                    where a.is_deleted='0' and a.id='" . $data['id'] . "' ";

        $data = $this->db->query($query)->getResult();

        $response = ['success' => true, 'data' => $data];

        echo json_encode($response);
    }

    function pujiankompi_list_get()
    {
        $data = $this->request->getGet();

        $query = "SELECT  a.id , a.poin, a.is_approve, c.namataruna ,  d.semester, f.batalyon, g.namagadik, a.created_at, e.foto, b.keterangan  as ref4, h.item_nilai  as ref3, i.indikator as ref2 , j.indikator as ref1, 'Pujian' as is_pelanggaran
                    from t_pujian_karakter_taruna a
                    inner join m_pujian_karakter_ref4 b on a.id_pujian_karakter=b.id
                    inner join m_user_taruna c on a.id_taruna=c.id_m_user and c.is_deleted='0'
                    inner join m_semester d on a.id_semester=d.id
                    inner join t_bukti_pujian_karakter_taruna e on a.id=e.id_pujian_karakter
                    inner join m_sm_batalyon f on c.id_batalyon=f.id
                    inner join m_user_pendidik g on a.created_by=g.id_m_user
                    inner join m_pujian_karakter_ref3 h on b.id_pen_kar_ref3=h.id
                    inner join m_pujian_karakter_ref2 i on h.id_pen_kar_ref2=i.id
                    inner join m_pujian_karakter_ref1 j on i.id_pen_kar_ref1=j.id
                    where a.is_deleted='0' and a.id='" . $data['id'] . "' ";

        $data = $this->db->query($query)->getResult();

        $response = ['success' => true, 'data' => $data];

        echo json_encode($response);
    }

    function matapelajaran_list_get()
    {
        $data = $this->request->getGet();

        $id_batalyon = $this->request->getGet('id_batalyon');
        $id_mata_pelajaran = $this->request->getGet('id_mata_pelajaran');
        $is_ujian = $this->request->getGet('is_ujian');

        $query = "SELECT
                        z.id_kelompok,z.kelompok,
                        json_arrayagg(json_object(
                                        'id_mata_pelajaran', z.id_mata_pelajaran,
                                        'kode_mk', z.kode_mk,
                                        'mata_pelajaran', z.mata_pelajaran,
                                        'id_user_pendidik', z.id_user_pendidik,
                                        'nama_pendidik', z.namagadik,
                                        'id_bahan_ajar', z.id_bahan_ajar,
                                        'jumlah_pertemuan', z.jumlah_pertemuan,
                                        'pertemuan_ke', z.pertemuan_ke,
                                        'sisa_pertemuan', z.sisa_pertemuan,
                                        'is_ujian' , z.is_ujian,
                                        'id_jenis_ujian' , z.id_jenis_ujian,
                                        'satuan', z.satuan
                                                            )
                                    ) as detail , count(z.id_mata_pelajaran) as asdasda
                    from
                    (select 
                        d.is_deleted,
                        a.id_kelompok,
                        c.id as id_mata_pelajaran,
                        c.kode_mk,
                        c.mata_pelajaran,
                        b.id_user_pendidik,
                        e.namagadik,
                        b.id as id_bahan_ajar,
                        b.judul,
                        if(h.id_aspek=1,h.jumlah_pertemuan*2,h.jumlah_pertemuan*1) as jumlah_pertemuan,
                        if(h.id_aspek=1,b.pertemuan_ke*2,b.pertemuan_ke*1) as pertemuan_ke,
                        if(h.id_aspek=1,h.jumlah_pertemuan*2,h.jumlah_pertemuan*1)-if(h.id_aspek=1,b.pertemuan_ke*2,b.pertemuan_ke*1) as sisa_pertemuan,
                        f.kelompok,
                        b.is_ujian,
                        b.id_jenis_ujian,
                        h.satuan
                       
                    from m_user_taruna a 
                    inner join t_bahan_ajar b
                                    on a.id_batalyon=b.id_batalyon
                                                    and a.id_semester=b.id_semester
                    inner join m_mata_pelajaran c
                        on b.id_mata_pelajaran = c.id
                    left join m_user_pendidik e
                        on e.id_m_user = b.id_user_pendidik
                    left join t_jadwal d
                        on d.id_kelompok_taruna = a.id_kelompok
                        and d.id_bahan_ajar = b.id
                        and d.is_deleted = 0
                    left join m_kelompok f on a.id_kelompok=f.id
                     
                          left join m_sm_batalyon g on b.id_semester=g.id_semester and g.id=b.id_batalyon
                          
                          left join t_program_studi_mata_pelajaran h on h.id_semester=g.id_semester and g.id=h.id_batalyon and b.id_mata_pelajaran=h.id_mata_pelajaran
                    where a.id_batalyon = '" . $id_batalyon . "'
                        and b.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
                        and b.is_ujian='" . $is_ujian . "'
                        and b.is_deleted = 0
                        and d.id is null
                    group by a.id_kelompok, a.id_batalyon,  b.id) z
                    group by id_kelompok
                    ";

        $data = $this->db->query($query)->getResult();

        $response = ['success' => true, 'data' => $data];

        echo json_encode($response);
    }

    function datatarunadiajar_list_get()
    {
        $data = $this->request->getGet();

        $id_kelompok_taruna = $this->request->getGet('id_kelompok_taruna');
        $id_mata_pelajaran = $this->request->getGet('id_mata_pelajaran');

        $query = "SELECT c.id_m_user as id_taruna,c.namataruna,c.noaklong,c.photopath from t_jadwal a
            left join t_bahan_ajar b on a.id_bahan_ajar=b.id 
            left join m_user_taruna c on a.id_kelompok_taruna=c.id_kelompok and b.id_batalyon=c.id_batalyon and b.id_semester=c.id_semester
            where a.id_kelompok_taruna='" . $id_kelompok_taruna . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "'
            GROUP BY c.id_m_user";

        $data = $this->db->query($query)->getResult();

        $response = ['success' => true, 'data' => $data];

        echo json_encode($response);
    }

    function matapelajaran_select_get()
    {
        $data = $this->request->getGet();

        // echo json_encode($data);
        $query = "SELECT 
                            b.id,
                            b.kode_mk,
                            b.mata_pelajaran
                        from t_program_studi_mata_pelajaran a
                        inner join m_mata_pelajaran b
                            on a.id_mata_pelajaran = b.id
                        join m_sm_batalyon c
                            on a.id_semester=c.id_semester
                            AND a.id_batalyon=c.id
                        where a.is_deleted='0'
                            and a.id_batalyon='" . $data['id_batalyon'] . "'";
        $where = ["b.kode_mk", "b.mata_pelajaran"];

        parent::_loadSelect2($data, $query, $where);
    }

    function gedung_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, concat(a.kode, ' | ' , a.nama) as text from m_gedung a where a.is_deleted = 0";
        $where = ["a.kode", "a.nama"];

        parent::_loadSelect2($data, $query, $where);
    }

    function prodi_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, concat(a.nama, ' | ' , a.tahun_ajaran) as text, a.tahun_ajaran from m_program_studi a where a.is_deleted = 0";
        $where = ["a.tahun_ajaran", "a.nama"];

        parent::_loadSelect2($data, $query, $where);
    }

    function aspek_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.*, a.aspek as text from m_aspek a where a.is_deleted = 0";
        $where = ["a.aspek"];

        parent::_loadSelect2($data, $query, $where);
    }

    function kurikulum_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.*, a.kurikulum as text from m_kurikulum a where a.is_deleted = 0";
        $where = ["a.kurikulum"];

        parent::_loadSelect2($data, $query, $where);
    }

    function bidang_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT a.*, a.bidang as text from m_bidang a where a.is_deleted = 0";
        $where = ["a.bidang"];

        parent::_loadSelect2($data, $query, $where);
    }

    function semester_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT a.*, a.semester AS 'text', b.id_tingkatan  FROM m_semester a
LEFT JOIN (SELECT * FROM `m_tingkatan_detail` WHERE uts_uas='uas') b ON a.id=b.id_semester WHERE a.is_deleted = 0  AND a.id!='9'";
        $where = ["a.semester"];

        parent::_loadSelect2($data, $query, $where);
    }


    function semestertahunajaran_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT * from (
                        select b.id , concat( b.semester, ' - ' ,ROUND(tahun_masuk + ((b.id/2) + mod(b.id/2,1)-1)) ) as text , ROUND(tahun_masuk + ((b.id/2) + mod(b.id/2,1)-1)) as tahun_ajaran, b.is_deleted
                            from m_sm_batalyon a
                            join m_semester b
                            where a.id='" . $data['id_batalyon'] . "'
                        ) a1 WHERE a1.is_deleted = 0";
        $where = ["a1.text"];

        parent::_loadSelect2($data, $query, $where);
    }

    function mata_pelajaran_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT
                        a.id,
                        IF(c.id IS NULL, CONCAT(a.kode_mk, '|', a.mata_pelajaran, ' (belum ada ketua tim)'), CONCAT(a.kode_mk, '|', a.mata_pelajaran)) AS text,
                        IF(c.id IS NULL, 0, 1) AS active,
                        a.is_deleted, c.is_deleted 
                    FROM m_mata_pelajaran a
                    LEFT JOIN (
                        SELECT b.*
                        FROM t_pendidik_mata_pelajaran b
                        WHERE b.is_deleted = 0 AND b.is_ketua_tim = 1
                        GROUP BY b.id_batalyon, b.id_mata_pelajaran
                        ) c ON a.id = c.id_mata_pelajaran AND c.id_batalyon = '" . $data['id_batalyon'] . "'
                    WHERE a.is_deleted = 0 AND (c.is_deleted = 0 OR c.is_deleted IS NULL)";
        $where = ["a.kode_mk", "a.mata_pelajaran"];
        parent::_loadSelect2($data, $query, $where);
    }

    function datapaketmatakuliah_list_get()
    {
        $data = $this->request->getGet();

        $qr = $this->db->query("SELECT id_batalyon , id_semester from t_program_studi_mata_pelajaran a where a.id='" . $this->request->getGet('id') . "' ")->getRow();

        $query = "SELECT a.*, concat(b.kode_mk, ' | ' , b.mata_pelajaran) as nama_mata_pelajaran ,c.tahun_masuk , d.semester as nama_semester  , e.id_pendidik
                                from t_program_studi_mata_pelajaran a
                                left join m_mata_pelajaran b on a.id_mata_pelajaran=b.id
                                left join m_sm_batalyon c on a.id_batalyon=c.id
                                left join m_semester d on a.id_semester=d.id
                                left join `t_pendidik_mata_pelajaran` e 
                                    on a.id_mata_pelajaran = e.id_mata_pelajaran and e.is_ketua_tim = 1 and e.id_batalyon = '" . $qr->id_batalyon . "' and a.id_semester = '" . $qr->id_semester . "'
                                where a.is_deleted='0' and  a.id_batalyon='" . $qr->id_batalyon . "' and a.id_semester='" . $qr->id_semester . "' ";

        parent::_loadlist($data, $query);
    }

    function pendidik_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id_m_user as id, a.namagadik as text from m_user_pendidik a where a.is_deleted = 0";
        $where = ["a.namagadik"];

        parent::_loadSelect2($data, $query, $where);
    }

    function datajadwal_select_get()
    {

        $data = $this->request->getGet();
        $userid = $this->request->getGet('userid');
        $usertype = $this->request->getGet('usertype');
        if ($usertype == 'gdk') {
            // $where = " and c.id_user_pendidik='".$userid."' ";
            $where = " join t_pendidik_mata_pelajaran e on b.id_mata_pelajaran=e.id_mata_pelajaran and e.id_pendidik='" . $userid . "' ";
        } else {
            $where = " ";
        }

        $query = "SELECT a.id , concat( d.kelompok , ' | ' , c.mata_pelajaran, ' - ', b.pertemuan_ke )  as 'text'
                        from t_jadwal a
                        left join t_bahan_ajar b on a.id_bahan_ajar=b.id
                        left join m_mata_pelajaran c on b.id_mata_pelajaran=c.id
                        left join m_kelompok d on a.id_kelompok_taruna=d.id
                        $where
                        where a.is_deleted='0' ";
        $where = ["d.kelompok", "c.mata_pelajaran", "b.pertemuan_ke"];

        parent::_loadSelect2($data, $query, $where);
    }


    function batalyon_select_get()
    {
        $data = $this->request->getGet();

        if (isset($data['temp_id_batalyon'])) {
            $wherebatalyon = $data['temp_id_batalyon'] == '' ?  "" : " and a1.id='" . $data['temp_id_batalyon'] . "' ";
        } else {
            $wherebatalyon = " ";
        }

        $query = "SELECT  * from ( SELECT a.id , CONCAT(a.batalyon,'/' , a.angkatan ,'/' , b.semester ,'/' , c.tingkatan, '/' , a.tahun_masuk ) AS text , b.ganjil_genap, a.is_deleted, a.tahun_masuk , a.id_semester, c.id as id_tingkat FROM m_sm_batalyon a LEFT JOIN m_semester b ON a.id_semester=b.id LEFT JOIN m_tingkatan c ON b.id_tingkat=c.id WHERE a.is_deleted='0' AND b.id!='9' ) a1 where a1.is_deleted='0' $wherebatalyon ";
        $where = ["a1.text"];

        parent::_loadSelect2($data, $query, $where);
    }

    function kompi_select_get()
    {
        $data = $this->request->getGet();

        if (isset($data['temp_id_kompi'])) {
            $wherekompi = $data['temp_id_kompi'] == '' ?  "" : " and a.id='" . $data['temp_id_kompi'] . "' ";
        } else {
            $wherekompi = " ";
        }


        $id_batalyon = $data["id_batalyon"];
        $query = "SELECT a.*, a.kompi as text from m_sm_kompi a where a.is_deleted = 0 and a.id_batalyon = '" . $id_batalyon . "' $wherekompi";
        $where = ["a.kompi"];

        parent::_loadSelect2($data, $query, $where);
    }

    function peleton_select_get()
    {
        $data = $this->request->getGet();

        if (isset($data['temp_id_peleton'])) {
            $wherepeleton = $data['temp_id_peleton'] == '' ?  "" : " and a.id='" . $data['temp_id_peleton'] . "' ";
        } else {
            $wherepeleton = " ";
        }

        $id_kompi = $data["id_kompi"];
        $query = "SELECT a.*, a.peleton as text from m_sm_peleton a where a.is_deleted = 0 and a.id_kompi = '" . $id_kompi . "' $wherepeleton ";
        $where = ["a.peleton"];

        parent::_loadSelect2($data, $query, $where);
    }

    function bulan_select_get()
    {
        $data = $this->request->getGet();
        $ganjil_genap = $data["ganjil_genap"];
        $query = "SELECT * FROM (SELECT a.id, concat('BULAN - ', a.urut , ' ( ' , a.bulan, ' ) ') as text, a.is_deleted from m_bulan a where a.semester='" . $ganjil_genap . "' order by a.urut asc) a where a.is_deleted='0' ";
        $where = ["a.text"];

        parent::_loadSelect2($data, $query, $where);
    }

    function withbatkompel_kelas_select_get()
    {
        $data = $this->request->getGet();
        $id_batalyon = $data["id_batalyon"];

        $id_kompi = $data["id_kompi"] == '' ? '' : ' and a.id_kompi="' . $data["id_kompi"] . '" ';

        $id_peleton = $data["id_peleton"] == '' ? '' : ' and a.id_peleton="' . $data["id_peleton"] . '" ';

        $query = "SELECT * FROM (select b.id, b.kelompok as text , b.is_deleted from m_user_taruna a
                    inner join m_kelompok b on a.id_kelompok=b.id
                    where a.is_deleted='0' and a.id_batalyon='$id_batalyon'  $id_kompi  $id_peleton
                    group by b.id) a where a.is_deleted='0' ";
        $where = ["a.text"];

        parent::_loadSelect2($data, $query, $where);
    }

    function databatkomple()
    {
        $data = $this->request->getGet();

        if ($data['type_code'] == 'btl') {
            $query = "SELECT a.*, a.id as id_batalyon, b.ganjil_genap FROM m_sm_batalyon a LEFT JOIN m_semester b ON a.id_semester=b.id where id_user_pendidik='" . $data['id'] . "' ";
        } else if ($data['type_code'] == 'kmp') {
            $query = "SELECT b.*, b.id as id_kompi, c.batalyon, d.ganjil_genap from m_sm_kompi b inner join m_sm_batalyon c on b.id_batalyon=c.id 
            LEFT JOIN m_semester d ON c.id_semester=d.id where b.id_user_pendidik='" . $data['id'] . "' ";
        } else if ($data['type_code'] == 'ptn') {
            $query = "SELECT a.*,a.id as id_peleton, b.kompi, b.id_batalyon, c.batalyon, d.ganjil_genap from m_sm_peleton a inner join m_sm_kompi b on a.id_kompi=b.id inner join m_sm_batalyon c on b.id_batalyon=c.id LEFT JOIN m_semester d ON c.id_semester=d.id where a.id_user_pendidik='" . $data['id'] . "' ";
        }

        echo json_encode($this->db->query($query)->getRow());
    }



    function mata_pelajaran_batalyon_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT  * from ( SELECT
                        a.id_mata_pelajaran AS id,
                        b.kode_mk,
                        b.mata_pelajaran AS text,
                        d.semester,
                        a.is_deleted
                    FROM t_program_studi_mata_pelajaran a
                    LEFT JOIN m_mata_pelajaran b
                        ON b.id = a.id_mata_pelajaran
                    LEFT JOIN m_sm_batalyon c
                        ON a.id_semester = c.id_semester
                        AND c.id=a.id_batalyon
                    LEFT JOIN m_semester d
                        ON a.id_semester = d.id
                WHERE a.id_batalyon = '" . $data['id_batalyon'] . "'
                    AND a.is_deleted='0'
                    AND a.id_semester='" . $data['id_semester'] . "') a1 where a1.is_deleted='0' ";
        $where = ["a1.text"];

        parent::_loadSelect2($data, $query, $where);
    }

    function pendidik_batalandmapel_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT b.id_m_user AS id , namagadik AS `text` FROM t_pendidik_mata_pelajaran a
                    LEFT JOIN m_user_pendidik b ON a.id_pendidik=b.id_m_user
                    WHERE a.id_batalyon='" . $data['id_batalyon'] . "' AND a.id_mata_pelajaran='" . $data['id_mata_pelajaran'] . "' AND a.is_deleted='0' ";
        $where = ["b.namagadik"];

        parent::_loadSelect2($data, $query, $where);
    }




    // end ajax ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


    // controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------




    // begin data pendidik done

    function datapendidik_download()
    {
        $data = json_decode($this->request->getPost('param'), true);

        if ($data['search'] == '') {
            $search = '';
        } else {
            $search = " and ( a.nik like '%" . $data['search'] . "%' or a.namagadik like '%" . $data['search'] . "%' or a.nrp like '%" . $data['search'] . "%'  ) ";
        }

        if ($data['is_internal'] == '') {
            $is_internal = '';
        } else {
            $is_internal = " and a.is_internal='" . $data['is_internal'] . "' ";
        }


        $query = "SELECT a.*, if(a.id_gender=1, 'Laki - Laki' , 'Perempuan') as nama_gender,
                    f.suku as nama_suku, e.satker as nama_satker,
                    g.agama as nama_agama, i.nama as nama_kabkota_lhr,
                    j.nama as nama_prov_ktp, k.nama as nama_kota_kab_ktp, l.nama as nama_kec_ktp, m.nama as nama_kel_ktp from m_user_pendidik a 
                    left join m_satker e on a.id_satker = e.id
                    left join m_suku f on a.id_suku = f.id
                    left join m_agama g on a.id_agama = g.id
                    left join m_lokasi_lahir i on a.id_kabkota_lhr = i.kdkabkota
                    left join m_lokasi_nik_ind j on a.id_prov_ktp = j.kode
                    left join m_lokasi_nik_ind k on a.id_kota_kab_ktp = k.kode
                    left join m_lokasi_nik_ind l on a.id_kec_ktp = l.kode
                    left join m_lokasi_nik_ind m on a.id_kel_ktp = m.kode
                    where a.namagadik is not null $is_internal $search";

        $rs['data'] = $this->db->query($query)->getResult();

        $rs['nama_file'] = "Data Pendidik";

        echo json_encode($rs);
    }

    function datapendidik_load()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $is_internal = $data['is_internal'] == '' ? '' : " and a.is_internal = '" . $data['is_internal'] . "' ";
        $query = "SELECT * FROM (select a.*, a.is_internal as status_pendidik, if(a.is_internal=0, 'External', if(a.is_internal=1, 'Internal', if(a.is_internal=2, 'PNS' , '?' ) ) ) as sts_pendidik
         from m_user_pendidik a 
         where a.namagadik is not null $is_internal ) a where a.namagadik is not null  ";
        $where = ["a.nrp", "a.namagadik", "a.sts_pendidik"];


        parent::_loadDatatable($query, $where, $data);
    }

    function datapendidiklengkapi_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        // echo $userid;
        // echo json_encode($data['token_otp']);

        $cek_otp = $this->db->query("SELECT * FROM m_user where id='" . $userid . "' and uniq_code='" . $data['token_otp'] . "' ")->getRow();

        if ($cek_otp != null) {

            if ($cek_otp->expired_time >= date("Y-m-d H:i:s")) {

                unset($data['token_otp']);
                $file_foto = $this->request->getFile('file_foto');


                if ($data['id'] == '') {

                    // echo json_encode($data['email']);

                    $cekusername = $this->db->query("SELECT id FROM m_user a where username='" . $data['email'] . "' and a.is_deleted='0' ")->getRow();

                    if ($cekusername == null) {

                        $data['id_m_user'] = '';
                    } else {

                        $data['id_m_user'] = $cekusername->id;
                    }
                }




                if ($file_foto != null) {
                    if ($file_foto->isValid() && !$file_foto->hasMoved()) {
                        $newName = $file_foto->getRandomName();
                        $file_foto->move('./public/photo/gdk/' . $data['nik'], $newName);
                        // if (!is_dir('./public/photo/gdk/' . $data['nik'])) {
                        //     $file_foto->move('./public/photo/gdk/' . $data['nik'], $newName);
                        // }
                        // $image = \Config\Services::image()
                        //     ->withFile($file_foto)
                        //     ->resize(480, 640, true, 'height')
                        //     ->save('./public/photo/gdk/' . $data['nik'] . '/' . $newName, 80);
                        $dir = base_url() . '/public/photo/gdk/' . $data['nik'] . '/' . $newName;
                        $data['photopath'] = $dir;
                        $response = [
                            'success' => true,
                            'data' => $dir
                        ];
                    }
                }

                if ($data['id_m_user'] == '' and $data['id'] == '') {
                    echo json_encode(array('success' => false, 'message' => 'Data User Tidak Ditemukan'));
                } else {

                    parent::_insert('m_user_pendidik', $data, $userid);
                }
            } else {
                echo json_encode(array('success' => false, 'message' => 'Maaf Kode OTP Expired'));
            }
        } else {
            echo json_encode(array('success' => false, 'message' => 'Maaf Kode OTP Tidak Ditemukan'));
        }
    }

    function datapendidik_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $file_foto = $this->request->getFile('file_foto');


        if ($data['id'] == '') {

            // echo json_encode($data['email']);

            $cekusername = $this->db->query("SELECT id FROM m_user a where username='" . $data['nrp'] . "' and a.is_deleted='0' ")->getRow();

            if ($cekusername == null) {

                $data['id_m_user'] = '';
            } else {

                $data['id_m_user'] = $cekusername->id;
            }
        }




        if ($file_foto != null) {
            if ($file_foto->isValid() && !$file_foto->hasMoved()) {
                $newName = $file_foto->getRandomName();
                $file_foto->move('./public/photo/gdk/' . $data['nik'], $newName);
                // if (!is_dir('./public/photo/gdk/' . $data['nik'])) {
                //     $file_foto->move('./public/photo/gdk/' . $data['nik'], $newName);
                // }
                // $image = \Config\Services::image()
                //     ->withFile($file_foto)
                //     ->resize(480, 640, true, 'height')
                //     ->save('./public/photo/gdk/' . $data['nik'] . '/' . $newName, 80);
                $dir = base_url() . '/public/photo/gdk/' . $data['nik'] . '/' . $newName;
                $data['photopath'] = $dir;
                $response = [
                    'success' => true,
                    'data' => $dir
                ];
            }
        }

        if ($data['id_m_user'] == '' and $data['id'] == '') {
            echo json_encode(array('success' => false, 'message' => 'Data User Tidak Ditemukan'));
        } else {

            parent::_insert('m_user_pendidik', $data, $userid);
        }
    }

    function datapendidik_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $query = "select a.*, if(a.id_gender=1, 'Laki - Laki' , 'Perempuan') as nama_gender,
        f.suku as nama_suku, e.satker as nama_satker,
        g.agama as nama_agama, i.nama as nama_kabkota_lhr,
        j.nama as nama_prov_ktp, k.nama as nama_kota_kab_ktp, l.nama as nama_kec_ktp, m.nama as nama_kel_ktp from m_user_pendidik a 
        left join m_satker e on a.id_satker = e.id
        left join m_suku f on a.id_suku = f.id
        left join m_agama g on a.id_agama = g.id
        left join m_lokasi_lahir i on a.id_kabkota_lhr = i.kdkabkota
        left join m_lokasi_nik_ind j on a.id_prov_ktp = j.kode
        left join m_lokasi_nik_ind k on a.id_kota_kab_ktp = k.kode
        left join m_lokasi_nik_ind l on a.id_kec_ktp = l.kode
        left join m_lokasi_nik_ind m on a.id_kel_ktp = m.kode
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_usr_pendidik', $data, null, $query);

        // parent::_edit('m_user_pendidik', $data);
    }

    function datapendidik_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        // echo json_encode($data);
        parent::_delete('m_user_pendidik', $data, $userid);
    }

    function datapendidik_aktiv()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        // echo json_encode($data);
        parent::_insert('m_user_pendidik', $data, $userid);
    }

    // end  data pendidik done
    // begin data program studi done
    function dataprogramstudi_load()
    {
        $query = "select a.*  from m_program_studi a  where a.is_deleted = 0";
        $where = ["a.nama"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function dataprogramstudi_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_program_studi', $data, $userid);
    }

    function dataprogramstudi_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.*  from m_program_studi a
            where a.id = '" . $data['id'] . "'";
        parent::_edit('m_program_studi', $data, null, $query);
    }

    function dataprogramstudi_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_program_studi', $data, $userid);
    }

    // end data program studi done

    // begin data Ruang Kelas done

    function dataruangkelas_load()
    {
        $query = "SELECT a.*, b.nama as gedung from m_ruang_kelas a left join
                m_gedung b on a.id_gedung = b.id
                where a.is_deleted = 0";
        $where = ["a.nama", "a.kode"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function dataruangkelas_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_ruang_kelas', $data, $userid);
    }

    function dataruangkelas_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.*, concat(b.kode,' | ',b.nama) as nama_gedung from m_ruang_kelas a
            left join m_gedung b on a.id_gedung = b.id
            where a.id = '" . $data['id'] . "'";
        parent::_edit('m_ruang_kelas', $data, null, $query);
    }

    function dataruangkelas_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_ruang_kelas', $data, $userid);
    }
    // end data Ruang Kelas done

    // begin data kelompok kuliah done
    function datakelompoktaruna_download()
    {
        $data = json_decode($this->request->getPost('param'), true);

        if ($data['id_batalyon'] == 'null') {
            $batalyon = '';
            $rs['batalyon']  = '';
        } else {
            $batalyon = " and a.id = '" . $data['id_batalyon'] . "' ";
            $rs['batalyon'] = $this->db->query("SELECT a.batalyon from m_sm_batalyon a where a.id='" . $data['id_batalyon'] . "' ")->getRow()->batalyon;
        }

        if ($data['id_aspek'] == 'null') {
            $aspek = '';
            $rs['aspek']  = '';
        } else {
            $aspek = " and b.id_aspek = '" . $data['id_aspek'] . "' ";
            $rs['aspek'] = $this->db->query("SELECT a.aspek FROM m_aspek a WHERE a.id='" . $data['id_aspek'] . "'")->getRow()->aspek;
        }

        if ($data['id_semester'] == 'null') {
            $semester = '';
            $rs['semester']  = '';
        } else {
            $semester = " and b.id_semester = '" . $data['id_semester'] . "' ";
            $rs['semester'] = $this->db->query("SELECT a.semester from m_semester a where a.id='" . $data['id_semester'] . "' ")->getRow()->semester;
        }

        $query = "SELECT * FROM (
                                SELECT a.is_deleted ,d.id,concat(b.nilai ,' ', b.satuan) as sks, c.kode_mk , c.mata_pelajaran , d.semester ,e.aspek
                                    from m_sm_batalyon a
                                left join t_program_studi_mata_pelajaran b 
                                     on a.id = b.id_batalyon
                                left join m_mata_pelajaran c
                                     on b.id_mata_pelajaran = c.id
                                left join m_semester d 
                                    on b.id_semester=d.id
                                left join m_aspek e 
                                    on b.id_aspek=e.id
                                where a.is_deleted='0'
                                    $batalyon
                                    $aspek
                                    $semester
                                    and b.is_deleted = '0'
                                    and c.is_deleted = '0'
                                    and b.id_mata_pelajaran is not null
                                ) a1 where a1.is_deleted='0' ";

        $rs['data'] = $this->db->query($query)->getResult();

        $rs['nama_file'] = $rs['batalyon'] . ' ' . $rs['semester'] . ' ' . $rs['aspek'];

        echo json_encode($rs);
    }

    function datakelompoktaruna_load()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $batalyon = $data['batalyon'] == '' ? '' : " and a.id = '" . $data['batalyon'] . "' ";
        $aspek = $data['aspek'] == '' ? '' : " and b.id_aspek = '" . $data['aspek'] . "' ";
        $semester = $data['semester'] == '' ? '' : " and b.id_semester = '" . $data['semester'] . "' ";

        $query = "SELECT * FROM (
                                SELECT a.is_deleted ,d.id,concat(b.nilai ,' ', b.satuan) as sks, c.kode_mk , c.mata_pelajaran , d.semester ,e.aspek
                                    from m_sm_batalyon a
                                left join t_program_studi_mata_pelajaran b 
                                     on a.id = b.id_batalyon
                                left join m_mata_pelajaran c
                                     on b.id_mata_pelajaran = c.id
                                left join m_semester d 
                                    on b.id_semester=d.id
                                left join m_aspek e 
                                    on b.id_aspek=e.id
                                INNER JOIN t_pendidik_mata_pelajaran f ON c.id=f.id_mata_pelajaran AND f.id_batalyon=a.id AND f.is_ketua_tim='1'
                                where a.is_deleted='0'
                                    $batalyon
                                    $aspek
                                    $semester
                                    and b.is_deleted = '0'
                                    and c.is_deleted = '0'
                                    and b.id_mata_pelajaran is not null
                                ) a1 where a1.is_deleted='0' ";
        $where = ["a1.sks", "a1.kode_mk", "a1.mata_pelajaran", "a1.semester", "a1.aspek"];

        parent::_loadDatatable($query, $where, $data);
    }

    function datakelompoktaruna_edit()
    {

        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT c.* , d.semester from m_program_studi a 
                            left join t_program_studi_mata_pelajaran b 
                                on a.id = b.id_program_studi
                            left join m_mata_pelajaran c
                                on b.id_mata_pelajaran = c.id
                            left join m_semester d on b.id_semester=d.id
                            where a.id = '" . $data['id_prodi'] . "'
                                and b.is_deleted = '0'
                                and c.is_deleted = '0'
                                and id_mata_pelajaran is not null AND c.id_aspek='" . $data['id_aspek'] . "' ";
        $where = ["c.kode_mk", "c.mata_pelajaran", "d.semester"];


        $order = "b.id_semester ,c.mata_pelajaran ,c.is_teori ";

        parent::_loadDatatableorder($query, $where, $data, $order);
    }

    // end data kelompok kuliah done

    // begin data datamatapelajaran done
    function datamatapelajaran_load()
    {
        $query = "SELECT a.*, b.bidang, c.kurikulum
                    FROM m_mata_pelajaran a
                    LEFT JOIN m_bidang b ON a.id_bidang = b.id
                    LEFT JOIN m_kurikulum c ON a.id_kurikulum = c.id
                    WHERE a.is_deleted = 0 AND b.is_deleted = 0 AND c.is_deleted = 0";
        $where = ["a.kode_mk", "a.mata_pelajaran", "b.bidang", "c.kurikulum"];
        $data = json_decode($this->request->getPost('param'), true);
        $orderby = "c.id DESC, a.kode_mk";
        $order = "ASC";

        parent::_loadDatatableorder($query, $where, $data, $orderby, $order);
    }

    function datamatapelajaran_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_mata_pelajaran', $data, $userid);
    }

    function datamatapelajaran_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.*, b.bidang as nama_bidang , c.kurikulum as nama_kurikulum
                    from m_mata_pelajaran a
                    left join m_bidang b on a.id_bidang=b.id
                    left join m_kurikulum c on a.id_kurikulum=c.id
                    where a.id = '" . $data['id'] . "'";
        parent::_edit('m_mata_pelajaran', $data, null, $query);
    }

    function datamatapelajaran_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_mata_pelajaran', $data, $userid);
    }
    // end data datamatapelajaran done

    // begin data datapaketmatakuliah done
    function datapaketmatakuliah_load()
    {
        $query = "SELECT a.*
                    FROM (
                        SELECT
                            a.*,
                            min(a.is_verif) AS _is_verif,
                            b.mata_pelajaran,
                            c.batalyon AS nama_batalyon ,
                            c.tahun_masuk,
                            c.angkatan,
                            d.semester AS nama_semester ,
                            count(b.id) AS jml_mapel
                        FROM t_program_studi_mata_pelajaran a
                        LEFT JOIN m_mata_pelajaran b ON a.id_mata_pelajaran = b.id
                        LEFT JOIN m_sm_batalyon c ON a.id_batalyon = c.id
                        LEFT JOIN m_semester d ON a.id_semester = d.id
                        WHERE a.is_deleted = 0 AND b.is_deleted = 0 AND c.is_deleted = 0 AND d.is_deleted = 0
                        GROUP BY a.id_batalyon, a.id_semester
                        ORDER BY c.id DESC, d.id DESC
                        ) a
                    WHERE a.is_deleted = 0";

        $where = ["a.mata_pelajaran", "a.nama_batalyon", "a.tahun_masuk", "a.nama_semester", "c.angkatan"];
        $data = json_decode($this->request->getPost('param'), true);
        $order = " a.tahun_masuk, a.id_semester ";

        // parent::_loadDatatable($query, $where, $data);
        parent::_loadDatatableorder($query, $where, $data, $order);
    }

    function datapaketmatakuliah_otorisasi()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        $pertemuan = $this->db->query("SELECT a.*, b.mata_pelajaran as nama_mata_pelajaran ,c.tahun_masuk , d.semester as nama_semester , e.id_pendidik
                                from t_program_studi_mata_pelajaran a
                                left join m_mata_pelajaran b on a.id_mata_pelajaran=b.id
                                left join m_sm_batalyon c on a.id_batalyon=c.id
                                left join m_semester d on a.id_semester=d.id
                                left join `t_pendidik_mata_pelajaran` e 
                                    on a.id_mata_pelajaran = e.id_mata_pelajaran and e.is_ketua_tim = 1 and e.id_batalyon = '" . $data['id_batalyon'] . "' and a.id_semester = '" . $data['id_semester'] . "'
                                where a.is_deleted='0' and  a.id_batalyon='" . $data['id_batalyon'] . "' and a.id_semester='" . $data['id_semester'] . "' ")->getResult();

        $gadik = 1;
        foreach ($pertemuan as $item) {
            if ($item->id_pendidik == null) {
                $gadik = 0;
            }
        }

        if ($gadik == 1) {

            $where = ["id_semester"  => $data['id_semester'], "id_batalyon" => $data['id_batalyon']];

            parent::_otorisasi('t_program_studi_mata_pelajaran', $data, $userid, $where, true);

            $this->generateBahanajar($data['id_batalyon'], $data['id_semester']);
        } else {
            echo json_encode(array('success' => false, 'message' => 'Lengkapi data ketua tim dan tim pada paket mata kuliah ini !'));
        }
    }

    function datapaketmatakuliah_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $hasil = parent::_insertbatch('t_program_studi_mata_pelajaran', $data, $userid, null, false);
    }

    function generateBahanajar($id_batalyon, $id_semester)
    {
        $pertemuan = $this->db->query("SELECT 
                a.id_mata_pelajaran as id_mata_pelajaran,
                b.id_pendidik as id_user_pendidik,
                a.id_semester as id_semester,
                a.id_batalyon as id_batalyon,
                concat(c.mata_pelajaran,' - ',t.n) as judul,
                concat('Materi pembelajaran ',c.mata_pelajaran, ' Pertemuan ke - ',t.n) as deskripsi,
                t.n as pertemuan_ke,
                0 as is_ujian,
                NULL as id_jenis_ujian
            from 
                (
                select ((((((b7.0 << 1 | b6.0) << 1 | b5.0) << 1 | b4.0) << 1 | b3.0) << 1 | b2.0) << 1 | b1.0) << 1 | b0.0 as n
                from  
                    (select 0 union all select 1) as b0,
                    (select 0 union all select 1) as b1,
                    (select 0 union all select 1) as b2,
                    (select 0 union all select 1) as b3,
                    (select 0 union all select 1) as b4,
                    (select 0 union all select 1) as b5,
                    (select 0 union all select 1) as b6,
                    (select 0 union all select 1) as b7) t
            join t_program_studi_mata_pelajaran a 
            left join `t_pendidik_mata_pelajaran` b
                on a.id_mata_pelajaran = b.id_mata_pelajaran
                    and b.is_ketua_tim = 1
                    and b.id_batalyon = '" . $id_batalyon . "'
                    and a.id_semester = '" . $id_semester . "'
            left join m_mata_pelajaran c
                on a.id_mata_pelajaran = c.id
            where a.id_semester = '" . $id_semester . "'
            and a.is_deleted='0'
            and a.id_batalyon = '" . $id_batalyon . "'
            and (n > 0 and n <= a.jumlah_pertemuan)
            order by a.id_mata_pelajaran, pertemuan_ke")->getResult();

        $ujian = $this->db->query("SELECT 
                                    a.id_mata_pelajaran as id_mata_pelajaran,
                                    b.id_pendidik as id_user_pendidik,
                                    a.id_semester as id_semester,
                                    a.id_batalyon as id_batalyon,
                                    if (t.n = 1, concat(c.mata_pelajaran ,' - UTS'), concat(c.mata_pelajaran ,' - UAS')) as judul,
                                    concat(if (t.n = 1, 'UTS','UAS'), ' Materi pembelajaran ',c.mata_pelajaran, ' Semester - ',a.id_semester) as deskripsi,
                                    0 as pertemuan_ke,
                                    1 as is_ujian,
                                    if (t.n = 1, 1,2)  as id_jenis_ujian
                                from 
                                    (
                                    select ((((((b7.0 << 1 | b6.0) << 1 | b5.0) << 1 | b4.0) << 1 | b3.0) << 1 | b2.0) << 1 | b1.0) << 1 | b0.0 as n
                                    from  
                                        (select 0 union all select 1) as b0,
                                        (select 0 union all select 1) as b1,
                                        (select 0 union all select 1) as b2,
                                        (select 0 union all select 1) as b3,
                                        (select 0 union all select 1) as b4,
                                        (select 0 union all select 1) as b5,
                                        (select 0 union all select 1) as b6,
                                        (select 0 union all select 1) as b7) t
                                join t_program_studi_mata_pelajaran a 
                                left join `t_pendidik_mata_pelajaran` b
                                    on a.id_mata_pelajaran = b.id_mata_pelajaran
                                        and b.is_ketua_tim = 1
                                        and b.id_batalyon = '" . $id_batalyon . "'
                                        and a.id_semester = '" . $id_semester . "'
                                left join m_mata_pelajaran c
                                    on a.id_mata_pelajaran = c.id
                                where a.id_semester = '" . $id_semester . "'
                                and a.id_batalyon = '" . $id_batalyon . "'
                                and a.id_aspek in (1,2)
                                and (n > 0 and n <= 2)
                                order by a.id_mata_pelajaran, pertemuan_ke")->getResult();


        $values = [];
        foreach ($pertemuan as $item) {
            $values[] = " ( '" . $item->id_mata_pelajaran . "', '" . $item->id_user_pendidik . "', '" . $item->id_semester . "', '" . $item->id_batalyon . "', '" . $item->judul . "', '" . $item->deskripsi . "', '" . $item->pertemuan_ke . "', '" . $item->is_ujian . "', 0 )";
        }

        foreach ($ujian as $item) {
            $values[] = " ( '" . $item->id_mata_pelajaran . "', '" . $item->id_user_pendidik . "', '" . $item->id_semester . "', '" . $item->id_batalyon . "', '" . $item->judul . "', '" . $item->deskripsi . "', '" . $item->pertemuan_ke . "', '" . $item->is_ujian . "', '" . $item->id_jenis_ujian . "' )";
        }

        $values = implode(",", $values);
        $qrinsert = "INSERT IGNORE INTO `t_bahan_ajar` (`id_mata_pelajaran`, `id_user_pendidik`, `id_semester`, `id_batalyon`, `judul`, `deskripsi`, `pertemuan_ke`, `is_ujian`, `id_jenis_ujian` ) VALUES " . $values;

        $runqr = $this->db->query($qrinsert);
        $runqr = $this->db->query("ALTER TABLE t_bahan_ajar AUTO_INCREMENT=0");


        if ($runqr) {
            echo json_encode(array('success' => true, 'message' => 'Berhasil Insert Bahan Ajar'));
        } else {
            echo json_encode(array('success' => false, 'message' => $this->webModel->db->error()['message']));
        }
    }

    function datapaketmatakuliah_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        
        $qr = $this->db->query("SELECT id_batalyon , id_semester from t_program_studi_mata_pelajaran a where a.id='" . $data['id'] . "' ")->getRow();
        
        $query = "SELECT a.*, concat(b.kode_mk, ' | ' , b.mata_pelajaran) as nama_mata_pelajaran , concat(c.batalyon, ' ( ' , c.tahun_masuk , ' ) ') as nama_batalyon , c.tahun_masuk, d.semester as nama_semester , e.aspek as nama_aspek
                                from t_program_studi_mata_pelajaran a
                                left join m_mata_pelajaran b on a.id_mata_pelajaran=b.id
                                left join m_sm_batalyon c on a.id_batalyon=c.id
                                left join m_semester d on a.id_semester=d.id
                                             left join m_aspek e on a.id_aspek=e.id
                                where a.is_deleted='0' and  a.id_batalyon='" . $qr->id_batalyon . "' and a.id_semester='" . $qr->id_semester . "' ";

        parent::_editbatch('t_program_studi_mata_pelajaran', $data, null, $query);
    }

    function datapaketmatakuliah_loadedit()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $qr = $this->db->query("SELECT id_batalyon , id_semester from t_program_studi_mata_pelajaran a where a.id='" . $data['id'] . "' ")->getRow();

        $query = "SELECT a.*, concat(b.kode_mk, ' | ' , b.mata_pelajaran) as nama_mata_pelajaran , concat(c.batalyon, ' ( ' , c.tahun_masuk , ' ) ') as nama_batalyon , c.tahun_masuk, d.semester as nama_semester , e.aspek as nama_aspek
                                from t_program_studi_mata_pelajaran a
                                left join m_mata_pelajaran b on a.id_mata_pelajaran=b.id
                                left join m_sm_batalyon c on a.id_batalyon=c.id
                                left join m_semester d on a.id_semester=d.id
                                             left join m_aspek e on a.id_aspek=e.id
                                where a.is_deleted='0' and  a.id_batalyon='" . $qr->id_batalyon . "' and a.id_semester='" . $qr->id_semester . "' ";

        parent::_editbatch('t_program_studi_mata_pelajaran', $data, null, $query);
    }

    function datapaketmatakuliah_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        $qr = $this->db->query("SELECT a.id_mata_pelajaran, tahun_ajaran 
                                FROM t_program_studi_mata_pelajaran a
                                WHERE a.id ='" . $data['id'] . "'")->getRow();

        // $query = "UPDATE t_program_studi_mata_pelajaran a
        //             SET a.is_deleted = '1', a.is_ketua_tim = '0', last_edited_at = NOW(), last_edited_by = '" . $userid . "'
        //             WHERE a.id_mata_pelajaran = '" . $qr->id_mata_pelajaran . "' AND a.tahun_ajaran = '" . $qr->tahun_ajaran . "'";
                    
        $query = "UPDATE t_program_studi_mata_pelajaran a
                    SET a.is_deleted = '1', last_edited_at = NOW(), last_edited_by = '" . $userid . "'
                    WHERE a.id_mata_pelajaran = '" . $qr->id_mata_pelajaran . "' AND a.tahun_ajaran = '" . $qr->tahun_ajaran . "'";

        // print_r('<pre>');
        // print_r($this->db->getLastQuery());
        // print_r('</pre>');

        // echo json_encode($query);
        if ($this->db->query($query)) {
            echo json_encode(array('success' => true, 'message' => 'Berhasil Hapus Data'));
        } else {
            echo json_encode(array('success' => false, 'message' => $this->db->error()['message']));
        }
    }
    // end data datapaketmatakuliah done


    // begin datakurikulum done

    function datakurikulum_otorisasi()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $where = ["id_semester"  => $data['id_semester'], "id_batalyon" => $data['id_batalyon'], "id_mata_pelajaran" => $data['id_mata_pelajaran']];

        parent::_otorisasi('t_bahan_ajar', $data, $userid, $where);
    }

    function datakurikulum_load()
    {
        $query = "SELECT * FROM (
                    SELECT a.* , concat(b.batalyon , ' ( ' , b.tahun_masuk ,' )' ) as nama_batalyon , c.semester as nama_semester , concat(d.kode_mk , ' | ' , d.mata_pelajaran ) as nama_mata_pelajaran , e.namagadik as nama_user_pendidik , count(a.id) as jml
                        FROM t_bahan_ajar a
                        left join m_sm_batalyon b on a.id_batalyon=b.id
                        left join m_semester c on a.id_semester=c.id
                        left join m_mata_pelajaran d on a.id_mata_pelajaran=d.id 
                        left join m_user_pendidik e on a.id_user_pendidik=e.id_m_user
                        where a.is_deleted='0'
                        group by a.id_mata_pelajaran , a.id_user_pendidik , a.id_semester , a.id_batalyon
                    ) a1 where a1.is_deleted='0' ";
        $where = ["a1.nama_batalyon", "a1.nama_semester", "a1.nama_mata_pelajaran", "a1.nama_user_pendidik", "a1.jml"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function datakurikulum_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insertbatch('t_bahan_ajar', $data, $userid);
    }

    function datakurikulum_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $qr = $this->db->query("SELECT a.id_mata_pelajaran, a.id_user_pendidik  ,a.id_semester , a.id_batalyon  from t_bahan_ajar a where a.id='" . $data['id'] . "' ")->getRow();

        $query = "SELECT a.* , concat(b.batalyon , ' ( ' , b.tahun_masuk ,' )' ) as nama_batalyon , c.semester as nama_semester , concat(d.kode_mk , ' | ' , d.mata_pelajaran ) as nama_mata_pelajaran , e.namagadik as nama_user_pendidik
                    FROM t_bahan_ajar a
                    left join m_sm_batalyon b on a.id_batalyon=b.id
                    left join m_semester c on a.id_semester=c.id
                    left join m_mata_pelajaran d on a.id_mata_pelajaran=d.id 
                    left join m_user_pendidik e on a.id_user_pendidik=e.id_m_user
                    where a.is_deleted='0' 
                                    and a.id_mata_pelajaran='" . $qr->id_mata_pelajaran . "'
                                    and a.id_user_pendidik='" . $qr->id_user_pendidik . "'
                                    and a.id_semester='" . $qr->id_semester . "'
                                    and a.id_batalyon='" . $qr->id_batalyon . "'
                    ";

        parent::_editbatch('t_bahan_ajar', $data, null, $query);
    }

    function datakurikulum_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        $qr = $this->db->query("SELECT a.id_mata_pelajaran, a.id_user_pendidik  ,a.id_semester , a.id_batalyon  from t_bahan_ajar a where a.id='" . $data['id'] . "' ")->getRow();

        $query = "UPDATE t_bahan_ajar a set a.is_deleted='1' , last_edited_at='" . date('Y-m-d H:i:s') . "' , last_edited_by='" . $userid . "'
                    where a.is_deleted='0' 
                        and a.id_mata_pelajaran='" . $qr->id_mata_pelajaran . "'
                        and a.id_user_pendidik='" . $qr->id_user_pendidik . "'
                        and a.id_semester='" . $qr->id_semester . "'
                        and a.id_batalyon='" . $qr->id_batalyon . "' ";

        // echo json_encode($query);
        if ($this->db->query($query)) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, 'message' => $this->db->error()['message']));
        }
    }

    // end datakurikulum done

    // begin datajadwal done

    function datajadwal_otorisasi()
    {

        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        $tanggal_awal = date("Y-m-d", strtotime(str_replace("/", "-", $data['tanggal'])));

        $tanggal_akhir = date("Y-m-d", strtotime("$tanggal_awal +6 day"));

        // echo json_encode($tanggal_akhir);
        $response = array('success' => true, 'message' => $data);

        unset($data['tanggal']);
        for ($i = 0; $i <= 6; $i++) {

            $where = ["id_batalyon"  => $data['id_batalyon'],  "tanggal " =>  date("Y-m-d", strtotime("$tanggal_awal +$i day"))];
            $hasil = parent::_otorisasi('t_jadwal', $data, $userid, $where, true);

            if ($hasil['success'] == false) {
                $response = array('success' => false, 'message' => $hasil['message']);
            }
        }

        echo json_encode($response);


        // echo json_encode($where);

    }

    function datajadwal_download()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $id_batalyon = $data['id_batalyon'];

        $tanggal_awal = date("Y-m-d", strtotime(str_replace("/", "-", $data['tanggal'])));

        $tanggal_akhir = date("Y-m-d", strtotime("$tanggal_awal +4 day"));

        $header = $this->db->query("SELECT k.id_kelompok, k.nama_kelompok, if(l.id_ruangan is null,'',l.id_ruangan) as id_ruangan, if(l.nama_ruangan is null,'',l.nama_ruangan) as nama_ruangan
                from (
                    select  c.id as id_kelompok, c.kelompok as nama_kelompok from m_sm_batalyon a inner join m_user_taruna b  on a.id = b.id_batalyon and a.id_semester=b.id_semester left join m_kelompok c on b.id_kelompok = c.id where a.id = '" . $id_batalyon . "' and a.is_deleted = 0 and b.is_deleted = 0 and c.is_deleted = 0 group by c.id
                    ) k 
                left join (
                    select a.id_kelompok_taruna as id_kelompok, b.id as id_ruangan, b.nama as nama_ruangan  from t_jadwal a left join m_ruang_kelas b on a.id_ruang_kelas = b.id where a.id_batalyon = '" . $id_batalyon . "' and a.tanggal between '" . $tanggal_awal . "' and '" . $tanggal_akhir . "' group by a.id_kelompok_taruna, a.id_ruang_kelas
                    ) l on k.id_kelompok = l.id_kelompok ")->getResult();

        $query = "SELECT  
                a.selected_date as tanggal, DATE_FORMAT(a.selected_date, '%d %M %Y') as tglindo ,IF(a.selected_date<CURDATE() , '1' , '0') AS kunci,
                json_arrayagg( json_object('id_pertemuan' , b.id , 'unit_pertemuan' , b.unit , 'jam_mulai' , TIME_FORMAT(b.jam_mulai, '%H:%i') , 'jam_selesai' , TIME_FORMAT(b.jam_selesai, '%H:%i') ) ) as data_unit   
                from 
                    (select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) a
                join m_jam_pertemuan b where b.is_weekdays = 1 and b.is_deleted = 0
                and a.selected_date between '" . $tanggal_awal . "' and '" . $tanggal_akhir . "' 
                group by a.selected_date
                order by a.selected_date,b.jam_mulai asc";


        $data_unit = $this->db->query("SELECT
                                        a.id as id_jadwal,
                                        a.id_bahan_ajar,
                                        a.id_kelompok_taruna,
                                        a.id_ruang_kelas,
                                        a.id_jam_pertemuan,
                                        a.id_user_pendidik,
                                        f.namagadik as nama_pendidik,
                                        b.kelompok as nama_kelompok,
                                        g.kode_mk,
                                        g.mata_pelajaran,
                                        d.unit as unit_jam_pertemuan,
                                        c.kode as kode_ruang_kelas,
                                        c.nama as nama_ruang_kelas,
                                        e.id_mata_pelajaran,
                                        a.tanggal,
                        if(i.id_aspek=1,i.jumlah_pertemuan*2,i.jumlah_pertemuan*1) as jumlah_pertemuan,
                        if(i.id_aspek=1,e.pertemuan_ke*2,e.pertemuan_ke*1) as pertemuan_ke,
                        if(i.id_aspek=1,i.jumlah_pertemuan*2,i.jumlah_pertemuan*1)-if(i.id_aspek=1,e.pertemuan_ke*2,e.pertemuan_ke*1) as sisa_pertemuan,
                        e.is_ujian,
                        e.id_jenis_ujian, IF(a.tanggal<CURDATE() , '1' , '0') AS kunci, a.is_verif, i.satuan
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
                                    left join t_program_studi_mata_pelajaran i on h.id_semester=i.id_semester and h.id=i.id_batalyon and e.id_mata_pelajaran=i.id_mata_pelajaran
                                    where a.is_deleted = 0
                                        and b.is_deleted = 0
                                        and c.is_deleted = 0
                                        and d.is_deleted = 0
                                        and d.is_weekdays = 1
                                        and e.is_deleted = 0
                                        -- and e.is_ujian = 0
                                        and f.is_deleted = 0
                                        and g.is_deleted = 0
                                        and a.id_batalyon='" . $id_batalyon . "'
                                        and a.tanggal between '" . $tanggal_awal . "' and '" . $tanggal_akhir . "' ")->getResult();



        $result = $this->db->query($query)->getResult();

        $data = array(
            "header" => $header,
            "body" => $result,
            "content" => $data_unit
        );
        $judul = "SELECT a.* , concat('Kegiatan Taruna ' , c.tingkatan , '/ANGK. ',a.angkatan,' ', b.semester , ' TA. ',round(a.tahun_masuk + ((a.id_semester/2) + mod(a.id_semester/2,1)-1) )) as jadwal
                from m_sm_batalyon a
                left join m_semester b on a.id_semester=b.id
                left join m_tingkatan c on b.id_tingkat=c.id where a.id='" . $id_batalyon . "'";
        $katim = "SELECT 
                        d.kode_mk,
                        d.mata_pelajaran,
                        e.namagadik
                    from t_jadwal a
                    left join t_bahan_ajar b
                        on a.id_bahan_ajar = b.id
                    left join t_pendidik_mata_pelajaran c
                        on b.id_batalyon = c.id_batalyon
                        and c.id_mata_pelajaran = b.id_mata_pelajaran
                        and c.is_ketua_tim = 1
                    left join m_mata_pelajaran d
                        on c.id_mata_pelajaran = d.id
                    left join m_user_pendidik e
                        on e.id_m_user = c.id_pendidik
                    left join m_sm_batalyon f
                        on b.id_batalyon=f.id
                        and b.id_semester=f.id_semester
                    where (a.tanggal between '" . $tanggal_awal . "' and '" . $tanggal_akhir . "')
                        and b.id_batalyon = '" . $id_batalyon . "'
                    group by c.id_mata_pelajaran, c.id_pendidik";

        $rs['katim'] = $this->db->query($katim)->getResult();
        $rs['judul'] = $this->db->query($judul)->getRow();


        $rs['data'] = $data;
        $rs['nama_file'] = "Jadwal " . $rs['judul']->batalyon . "  " . $tanggal_awal . " - " . $tanggal_akhir;

        echo json_encode($rs);
    }

    function datajadwal_load()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        // $tanggal_awal = date("Y-m-d", strtotime($data['tanggal']));
        $tanggal_awal = date("Y-m-d", strtotime(str_replace("/", "-", $data['tanggal'])));

        $id_batalyon = $data['id_batalyon'];

        $tanggal_akhir = date("Y-m-d", strtotime("$tanggal_awal +6 day"));




        $header = $this->db->query("SELECT k.id_kelompok, k.nama_kelompok, if(l.id_ruangan is null,'',l.id_ruangan) as id_ruangan, if(l.nama_ruangan is null,'',l.nama_ruangan) as nama_ruangan
                from (
                    select  c.id as id_kelompok, c.kelompok as nama_kelompok from m_sm_batalyon a inner join m_user_taruna b  on a.id = b.id_batalyon and a.id_semester=b.id_semester left join m_kelompok c on b.id_kelompok = c.id where a.id = '" . $id_batalyon . "' and a.is_deleted = 0 and b.is_deleted = 0 and c.is_deleted = 0 group by c.id
                    ) k 
                left join (
                    select a.id_kelompok_taruna as id_kelompok, b.id as id_ruangan, b.nama as nama_ruangan  from t_jadwal a left join m_ruang_kelas b on a.id_ruang_kelas = b.id where a.id_batalyon = '" . $id_batalyon . "' and a.tanggal between '" . $tanggal_awal . "' and '" . $tanggal_akhir . "' group by a.id_kelompok_taruna, a.id_ruang_kelas
                    ) l on k.id_kelompok = l.id_kelompok ")->getResult();

        // select  c.id as id_kelompok, c.kelompok as nama_kelompok 
        // from m_sm_batalyon a
        // inner join t_kelompok_taruna_ b on a.id = b.id_batalyon
        // left join m_kelompok c on b.id_kelompok = c.id  
        // where a.id = '1' and a.is_deleted = 0 and b.is_deleted = 0 and c.is_deleted = 0 group by c.id


        $query = "SELECT  
                a.selected_date as tanggal, DATE_FORMAT(a.selected_date, '%d %M %Y') as tglindo ,IF(a.selected_date<CURDATE() , '1' , '0') AS kunci,
                json_arrayagg( json_object('id_pertemuan' , b.id , 'unit_pertemuan' , b.unit , 'jam_mulai' , TIME_FORMAT(b.jam_mulai, '%H:%i') , 'jam_selesai' , TIME_FORMAT(b.jam_selesai, '%H:%i') ) ) as data_unit   
                from 
                    (select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
                    (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) a
                join m_jam_pertemuan b where b.is_weekdays = 1 and b.is_deleted = 0
                and a.selected_date between '" . $tanggal_awal . "' and '" . $tanggal_akhir . "' 
                group by a.selected_date
                order by a.selected_date,b.jam_mulai asc";


        $data_unit = $this->db->query("SELECT
                                        a.id as id_jadwal,
                                        a.id_bahan_ajar,
                                        a.id_kelompok_taruna,
                                        a.id_ruang_kelas,
                                        a.id_jam_pertemuan,
                                        a.id_user_pendidik,
                                        f.namagadik as nama_pendidik,
                                        b.kelompok as nama_kelompok,
                                        g.kode_mk,
                                        g.mata_pelajaran,
                                        d.unit as unit_jam_pertemuan,
                                        c.kode as kode_ruang_kelas,
                                        c.nama as nama_ruang_kelas,
                                        e.id_mata_pelajaran,
                                        a.tanggal,
                        if(i.id_aspek=1,i.jumlah_pertemuan*2,i.jumlah_pertemuan*1) as jumlah_pertemuan,
                        if(i.id_aspek=1,e.pertemuan_ke*2,e.pertemuan_ke*1) as pertemuan_ke,
                        if(i.id_aspek=1,i.jumlah_pertemuan*2,i.jumlah_pertemuan*1)-if(i.id_aspek=1,e.pertemuan_ke*2,e.pertemuan_ke*1) as sisa_pertemuan,
                        e.is_ujian,
                        e.id_jenis_ujian, IF(a.tanggal<CURDATE() , '1' , '0') AS kunci, a.is_verif, i.satuan
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
                                    left join t_program_studi_mata_pelajaran i on h.id_semester=i.id_semester and h.id=i.id_batalyon and e.id_mata_pelajaran=i.id_mata_pelajaran
                                    where a.is_deleted = 0
                                        and b.is_deleted = 0
                                        and c.is_deleted = 0
                                        and d.is_deleted = 0
                                        and d.is_weekdays = 1
                                        and e.is_deleted = 0
                                        -- and e.is_ujian = 0
                                        and f.is_deleted = 0
                                        and g.is_deleted = 0
                                        and a.id_batalyon='" . $id_batalyon . "'
                                        and a.tanggal between '" . $tanggal_awal . "' and '" . $tanggal_akhir . "' ")->getResult();

        $result = $this->db->query($query)->getResult();




        $data = array(
            "header" => $header,
            "body" => $result,
            "content" => $data_unit
        );


        $response = ['success' => true, 'data' => $data];

        echo json_encode($response);
    }

    function datajadwal_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insertwithid('t_jadwal', $data, $userid);
    }

    function datajadwal_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.*  from m_pangkat_pendidik a
            where a.id = '" . $data['id'] . "'";
        parent::_edit('t_jadwal', $data, null, $query);
    }

    function datajadwal_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('t_jadwal', $data, $userid);
    }
    // end datajadwal done


    // begin data taruna

    function tarunaaktif_load()
    {
        $query = "SELECT a.* , b.batalyon AS nama_batalyon , c.semester AS nama_semester, IF(c.id<9, 'Taruna Aktif' , 'Alumni Taruna') AS status_taruna FROM m_user_taruna a
                LEFT JOIN `m_sm_batalyon` b ON a.`id_batalyon`=b.id
                LEFT JOIN m_semester c ON b.id_semester=c.id WHERE a.is_deleted = 0 AND a.is_verif=1 
                -- AND c.id!='9'
                ";
        $where = ["a.namataruna", "b.batalyon", "a.asal_pengiriman", "c.semester"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    // end data taruna


    // begin data Ruang Kelas done

    function datatugasmengajar_load()
    {

        $userid = $this->request->getPost('userid');
        $usertype = $this->request->getPost('usertype');
        $data = json_decode($this->request->getPost('param'), true);
        if ($usertype == 'gdk') {
            // $where = " and c.id_user_pendidik='".$userid."' ";
            $column = " , f.is_ketua_tim ";
            $where = " join t_pendidik_mata_pelajaran f on c.id_mata_pelajaran=f.id_mata_pelajaran and f.id_pendidik='" . $userid . "' ";
        } else {
            $column = " ";
            $where = " ";
        }

        $query = "SELECT a.id,
        a.id_jadwal,
        a.judul,
        a.deskripsi,
        a.waktu_pengumpulan,
        if(a.jenis_file_materi_tugas=1, concat('http://devel.nginovasi.id/akpol-api/', file_materi_tugas), file_materi_tugas) as file_materi_tugas,
        a.id_tipe_file,
        a.tipe_tugas,
        a.is_deleted,
        a.created_at,
        a.created_by,
        a.jenis_file_materi_tugas, d.id as id_mata_pelajaran, d.mata_pelajaran , e.kelompok  
                    $column
                    from t_jadwal_tugas a
                    left join t_jadwal b on a.id_jadwal=b.id
                    left join t_bahan_ajar c on b.id_bahan_ajar=c.id
                    left join m_mata_pelajaran d on c.id_mata_pelajaran=d.id
                    left join m_kelompok e on b.id_kelompok_taruna=e.id
                    $where
                    where a.is_deleted='0'";
        $where = ["a.judul", "a.waktu_pengumpulan", "d.mata_pelajaran", "e.kelompok"];
        $order = "a.waktu_pengumpulan ";

        parent::_loadDatatableorder($query, $where, $data, $order, 'desc');
    }

    function datatugasmengajar_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $userdetail = json_decode($this->request->getPost('userdetail'), true);
        $usertype = $this->request->getPost('usertype');
        $jenisfile = $data['jenisfile'];

        if ($jenisfile == '1') {
            $file_materi_tugas = $this->request->getFile('file_materi_tugas');
            $filetype = $this->request->getPost('filetype');
            if ($file_materi_tugas != null) {
                if ($file_materi_tugas->isValid() && !$file_materi_tugas->hasMoved()) {
                    $newName = $file_materi_tugas->getRandomName();
                    if ($usertype == "sad") {
                        $file_materi_tugas->move('./public/file_penugasan/sad/' . $userid, $newName);
                        // $dir = base_url() . '/public/file_penugasan/sad/' . $userid . '/' . $newName;
                        $dir =  '/public/file_penugasan/sad/' . $userid . '/' . $newName;
                    } else {
                        $file_materi_tugas->move('./public/file_penugasan/' . $usertype . '/' . $userdetail['nik'], $newName);
                        // $dir = base_url() . '/public/file_penugasan/' . $usertype . '/' . $userdetail['nik'] . '/' . $newName;
                        $dir =  '/public/file_penugasan/' . $usertype . '/' . $userdetail['nik'] . '/' . $newName;
                    }



                    $id_tipe_file = $this->db->query("SELECT * FROM m_tipe_file
                    WHERE tipe_file = '" . $filetype . "'");
                    if ($id_tipe_file->getNumRows() > 0) {
                        $data['id_tipe_file'] = $id_tipe_file->getRow()->id;
                    }

                    $data['file_materi_tugas'] = $dir;
                    $response = [
                        'success' => true,
                        'data' => $dir
                    ];
                }
            }
        } else {
            $data['file_materi_tugas'] = $data['link_materi_tugas'];
        }
        $data['jenis_file_materi_tugas'] = $data['jenisfile'];

        unset($data['jenisfile']);
        unset($data['link_materi_tugas']);
        parent::_insert('t_jadwal_tugas', $data, $userid);


        $infomapel      = $this->db->query("SELECT b.id_mata_pelajaran,a.id_kelompok_taruna from t_jadwal a
                                                LEFT JOIN t_bahan_ajar b on a.id_bahan_ajar=b.id
                                                where a.id='" . $data['id_jadwal'] . "'")->getRow();

        $id_mata_pelajaran = $infomapel->id_mata_pelajaran;
        $id_kelompok_taruna = $infomapel->id_kelompok_taruna;

        $tarunafcm = $this->db->query("SELECT e.id_m_user as id_taruna,c.mata_pelajaran,d.kelompok,g.fcm_token,f.namataruna,f.noaklong,f.photopath from t_jadwal a
                    left join t_bahan_ajar b on a.id_bahan_ajar=b.id
                    left join m_mata_pelajaran c on b.id_mata_pelajaran=c.id
                    left join m_kelompok d on a.id_kelompok_taruna=d.id
                    left join m_user_taruna e on a.id_kelompok_taruna=e.id_kelompok
                    left join m_user_taruna f on e.id_m_user=f.id_m_user
                    join m_user g on f.id_m_user=g.id and g.fcm_token is not null and g.fcm_token!=''
                    left join m_sm_batalyon h on e.id_batalyon=h.id
                    where a.id_user_pendidik='" . $userid . "' and a.id_kelompok_taruna = '" . $id_kelompok_taruna . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "'  group by e.id_m_user")->getResult();


        foreach ($tarunafcm as $obj) {
            $token = $obj->fcm_token;
            $mapel = $obj->mata_pelajaran;
            $kelompok = $obj->kelompok;
            $header = 'TUGAS - ' . $mapel . ' ' . $kelompok . '';
            $deskripsi = $data['judul'];

            $this->handlertoken($token, $header, $deskripsi);
        }
    }

    function datatugasmengajar_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.id,
        a.id_jadwal,
        a.judul,
        a.deskripsi,
        a.waktu_pengumpulan,
        if(a.jenis_file_materi_tugas=1, concat('http://devel.nginovasi.id/akpol-api/', file_materi_tugas), file_materi_tugas) as file_materi_tugas,
        a.id_tipe_file,
        a.tipe_tugas,
        a.is_deleted,
        a.created_at,
        a.created_by,
        a.jenis_file_materi_tugas, d.mata_pelajaran , e.kelompok , concat( e.kelompok , ' | ' , d.mata_pelajaran, ' - ', c.pertemuan_ke )  as nama_jadwal, replace(a.waktu_pengumpulan , ' ', 'T') as waktu_pengumpulannya
        ,if(a.jenis_file_materi_tugas=1, concat('http://devel.nginovasi.id/akpol-api/', file_materi_tugas), file_materi_tugas) as file_materi_tugas_
                    from t_jadwal_tugas a
                    left join t_jadwal b on a.id_jadwal=b.id
                    left join t_bahan_ajar c on b.id_bahan_ajar=c.id
                    left join m_mata_pelajaran d on c.id_mata_pelajaran=d.id
                    left join m_kelompok e on b.id_kelompok_taruna=e.id
            where a.id = '" . $data['id'] . "'";
        parent::_edit('t_jadwal_tugas', $data, null, $query);
    }

    function datatugasmengajar_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('t_jadwal_tugas', $data, $userid);
    }
    // end data Ruang Kelas done




    function golongankepangkatan_load()
    {
        $query = "select a.*  from m_pangkat_pendidik a  where a.is_deleted = 0";
        $where = ["a.nama"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function golongankepangkatan_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_pangkat_pendidik', $data, $userid);
    }

    function golongankepangkatan_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.*  from m_pangkat_pendidik a
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_pangkat_pendidik', $data, null, $query);
    }

    function golongankepangkatan_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_pangkat_pendidik', $data, $userid);
    }

    function rekapjammengajar_load()
    {

        $data = json_decode($this->request->getPost('param'), true);

        $tanggal = ' ';

        if ($data['tanggal'] != '') {

            $date = explode(" - ", $data['tanggal']);
            $dateawal = date("Y-m-d", strtotime(str_replace("/", "-", $date['0'])));
            $dateakhir = date("Y-m-d", strtotime(str_replace("/", "-", $date['1'])));
            $tanggal = " and a.tanggal between '" . $dateawal . "' and '" . $dateakhir . "' ";
        }

        $query = "SELECT * FROM (SELECT a.id , DATE_FORMAT(a.tanggal, '%d-%m-%Y') as tanggal , d.kelompok , e.nama ,  f.mata_pelajaran , c.judul , if(c.pertemuan_ke='0' , if(c.id_jenis_ujian=1, 'UTS', if(c.id_jenis_ujian='2', 'UAS' , '') ) , c.pertemuan_ke) as pertemuan_ke,
        if(a.is_absensi_pendidik=1, 'Hadir' , if(curdate()>a.tanggal, 'Tidak Hadir' , 'Sudah Terjadwal') ) as is_absensi, a.is_deleted, DATE_FORMAT(a.jam_mulai, '%H:%i') as jam_mulai, DATE_FORMAT(a.jam_selesai, '%H:%i') as jam_selesai
                    from t_jadwal a
                    left join m_user_pendidik b on a.id_user_pendidik=b.id_m_user
                    left join t_bahan_ajar c on a.id_bahan_ajar=c.id
                    left join m_kelompok d on a.id_kelompok_taruna=d.id
                    left join m_ruang_kelas e on a.id_ruang_kelas=e.id
                    left join m_mata_pelajaran f on c.id_mata_pelajaran=f.id
                    where a.is_deleted='0' and a.id_user_pendidik = '" . $data['pendidik'] . "' 
                        $tanggal  order by a.tanggal ASC
                    ) a1 where a1.is_deleted='0' ";
        $where = ["a1.tanggal", "a1.nama", "a1.kelompok", "a1.mata_pelajaran", "a1.judul", "a1.pertemuan_ke"];

        parent::_loadDatatable($query, $where, $data);
    }

    function rekapjammengajar_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('t_jadwal', $data, $userid);
    }

    function rekapjammengajar_edit()
    {

        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT a.id , a.tanggal , d.kelompok , e.nama ,  f.mata_pelajaran , c.judul , c.pertemuan_ke
                    from t_jadwal a
                    left join m_user_pendidik b on a.id_user_pendidik=b.id_m_user
                    left join t_bahan_ajar c on a.id_bahan_ajar=c.id
                    left join m_kelompok d on a.id_kelompok_taruna=d.id
                    left join m_ruang_kelas e on a.id_ruang_kelas=e.id
                    left join m_mata_pelajaran f on c.id_mata_pelajaran=f.id
                    where a.is_deleted='0'
                        and a.id_user_pendidik = '" . $data['id_user_pendidik'] . "'
                        and a.tanggal between '' and ''  ";
        $where = ["a.tanggal", "h.kelompok", "f.nama", "g.mata_pelajaran", "c.judul", "c.pertemuan_ke"];
        $data = json_decode($this->request->getPost('param'), true);
        $order = " a.tanggal, c.pertemuan_ke  ";

        parent::_loadDatatableorder($query, $where, $data, $order);
    }

    function rekapjammengajar_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('t_jadwal', $data, $userid);
    }


    //penilaian tugas
    function datatugasmengajar_load_nilai()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $id_jadwal_tugas = $data["id"];

        $query = "SELECT
        d.id,
        b.id_m_user as id_user_taruna,
        b.id_semester,
        b.namataruna,
        b.noaklong,
        c.kelompok,
        '" . $id_jadwal_tugas . "' as id_jadwal_tugas,
        d.catatan,
        d.file_tugas,
        d.id_tipe_file,
        d.upload_date,
        d.nilai,
        d.log_nilai
        from m_user_taruna b
        inner join m_kelompok c
            on c.id = b.id_kelompok
            left join t_jadwal e
            on b.id_kelompok = e.id_kelompok_taruna
        left join t_jadwal_tugas f
            on e.id = f.id_jadwal
        left join t_nilai_tugas d
            on b.id_m_user = d.id_user_taruna
            where d.id_jadwal_tugas = '" . $id_jadwal_tugas . "'
            and f.id = '" . $id_jadwal_tugas . "'";

        parent::_loadlist($data, $query);
    }

    function datatugasmengajar_save_nilai()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $data['log_nilai'] = date('Y-m-d H:i:s');
        // echo json_encode($data);
        parent::_insertReturn('t_nilai_tugas', $data, $userid);
    }


    function palajarandiajar_load()
    {

        $data = json_decode($this->request->getPost('param'), true);
        $userid = $this->request->getPost('userid');
        $usertype = $this->request->getPost('usertype');

        if ($usertype == 'gdk') {
            $where = " and f.id_pendidik = '" . $userid . "' ";
        } else {
            $where = " ";
        }


        $query = "SELECT a.* FROM (SELECT 
            a.id_user_pendidik,
              d.id_mata_pelajaran,
              e.deskripsi,
              g.namagadik AS ketua_tim,
              g.photopath,
              c.id AS id_batalyon,
            CONCAT(h.semester,' Batalyon ' ,c.batalyon) AS info_semester,
            b.kelompok,
            a.id_kelompok_taruna,
            c.id_semester,
            e.mata_pelajaran,
            a.is_deleted
        FROM t_jadwal a
        LEFT JOIN m_kelompok b
            ON a.id_kelompok_taruna = b.id
        LEFT JOIN m_user_taruna i
            ON i.id_kelompok=b.id
        LEFT JOIN m_sm_batalyon c
            ON c.id = i.id_batalyon
        LEFT JOIN t_bahan_ajar d
            ON d.id = a.id_bahan_ajar
        LEFT JOIN m_mata_pelajaran e
            ON e.id = d.id_mata_pelajaran
        LEFT JOIN t_pendidik_mata_pelajaran f
            ON d.id_mata_pelajaran=f.id_mata_pelajaran 
            AND d.id_batalyon=f.id_batalyon
        LEFT JOIN m_user_pendidik g
            ON f.id_pendidik=g.id_m_user 
        LEFT JOIN m_semester h
            ON c.id_semester=h.id
        WHERE 
            a.is_deleted = 0
            $where
            AND c.id_semester = d.id_semester
            AND d.id_batalyon = c.id
        GROUP BY f.id  ) a where a.is_deleted='0' ";
        $where = ["a.mata_pelajaran", "a.kelompok", "a.info_semester"];
        $data = json_decode($this->request->getPost('param'), true);
        $order = " a.kelompok ";

        parent::_loadDatatableorder($query, $where, $data, $order);
    }



    function handlertoken($token, $header, $deskripsi)
    {
        header('Content-Type: application/json');

        $url = "https://fcm.googleapis.com/fcm/send";
        $token = $token;
        // $token = "dcKM8A1eSca9b7j1LcJYg-:APA91bHC2779MS4oXIJ6_PBS-v9ruQiTwxKBkoLyZfSJSOdQYT6ObCC0e26CcNA82t8m_DlixPmGCvjNsa-bo99zO5l36swtCox8eHFMKv78JKaZvbdHm7BS6l9KJKbd1jDGuVjH7osd";
        // $token = "djPQP8rZAOc:APA91bG6mrEQlNn6cwC9t-ulUKnt5deY-4K1B1A3qrhRmKo1h_mjMUIjfdZxxx5R4SmXy8OU-folEeMs4C_F068rSxTtX49u_wzb51c6aL64Z_1rLsFnom0J9Kpkq7vqhOnNr3ydKg3I";
        $serverKey = 'AAAA4M5pR9Y:APA91bEI3mJRb3ANqZHdXELq9ywYw3wzzI-2lH7OQPjk5jxtvAlpcCvRqRATWLrTgCGG2DripXZE29oYXQp0rsJiQRZe-5Mdr7uOsi1qFrozgRptz9G6jwRa9ZNa4yMuo_mIHLpy1cS8';
        $title = $header;
        $body = "" . $deskripsi . "";
        $data = array(
            'header'        => $header,
            'deskripsi'     => $deskripsi
        );
        $notification = array('title' => $title, 'body' => $body, 'sound' => 'default');
        $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority' => 'high', 'data' => $data);
        $json = json_encode($arrayToSend);

        // print_r(expression)

        // echo $json;
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=' . $serverKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
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


    function jadwalpendidik_load()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        $tanggal = explode(" ", $data['tanggal']);
        $tanggal_awal = date("Y-m-d", strtotime(str_replace("/", "-", $tanggal[0])));

        $tanggal_akhir = date("Y-m-d", strtotime(str_replace("/", "-", $tanggal[2])));


        $id_pendidik = $data['id_pendidik'];

        // $tanggal_akhir = date("Y-m-d", strtotime("$tanggal_awal +6 day"));


        $data_unit = $this->db->query("SELECT g.mata_pelajaran, DATE_FORMAT(a.tanggal, '%d %M %Y') AS tanggal, f.namagadik AS nama_pendidik, c.nama AS nama_ruang_kelas, DATE_FORMAT(d.jam_mulai, '%H:%i') AS jam_mulai, DATE_FORMAT(d.jam_selesai, '%H:%i') AS jam_selesai
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
                                    -- left join t_program_studi_mata_pelajaran i on h.id_semester=i.id_semester and h.id=i.id_batalyon and e.id_mata_pelajaran=i.id_mata_pelajaran
                                    -- join m_user_taruna j on b.id=j.id_kelompok and h.id=j.id_batalyon
                                    where a.is_deleted = 0
                                        and b.is_deleted = 0
                                        and c.is_deleted = 0
                                        and d.is_deleted = 0
                                        and d.is_weekdays = 1
                                        and e.is_deleted = 0
                                        and f.is_deleted = 0
                                        and g.is_deleted = 0
                                        and (a.tanggal BETWEEN '" . $tanggal_awal . "' and '" . $tanggal_akhir . "')
                                        and a.id_user_pendidik='" . $id_pendidik . "' group by a.id ORDER BY a.tanggal ASC ")->getResult();
        $namagadik =  $this->db->query("SELECT namagadik FROM m_user_pendidik where id_m_user='" . $id_pendidik . "' ")->getRow();




        $data = array(
            "content" => $data_unit,
            "tgl_akhir" => date("d M Y", strtotime($tanggal_awal)) . ' sd ' . date("d M Y", strtotime($tanggal_akhir)),
            "namagadik" => $namagadik
        );


        $response = ['success' => true, 'data' => $data];

        echo json_encode($response);
    }


    // begin data pelanggarandanpujian done

    function pelanggarandanpujian_otorisasi()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $where = ["id"  => $data['id']];
        $stt_simpan = parent::_otorisasi('t_pelanggaran_karakter_taruna', $data, $userid, $where, true, 'approve');

        if ($stt_simpan['success']) {
            $cek_pelanggaranpujian = $this->db->query("SELECT '' as id, b.dasar_hukum as pelanggaranpujian, a.id_taruna, a.id_semester, a.id_tingkat, a.tahun_ajaran, a.poin, a.latitude, a.longitude, a.alamat, a.is_approve, a.approve_at, a.approve_by, '1' as is_pelanggaran, a.created_at as date_pelanggaranpujian  from t_pelanggaran_karakter_taruna a inner join m_pelanggaran_karakter b on a.id_pelanggaran_karakter=b.id where a.is_deleted='0' and a.id='" . $data['id'] . "' ")->getRow();
            $datane['id'] = '';
            $datane['pelanggaranpujian'] = $cek_pelanggaranpujian->pelanggaranpujian;
            $datane['id_taruna'] = $cek_pelanggaranpujian->id_taruna;
            $datane['id_semester'] = $cek_pelanggaranpujian->id_semester;
            $datane['id_tingkat'] = $cek_pelanggaranpujian->id_tingkat;
            $datane['tahun_ajaran'] = $cek_pelanggaranpujian->tahun_ajaran;
            $datane['poin'] = $cek_pelanggaranpujian->poin;
            $datane['latitude'] = $cek_pelanggaranpujian->latitude;
            $datane['longitude'] = $cek_pelanggaranpujian->longitude;
            $datane['alamat'] = $cek_pelanggaranpujian->alamat;
            $datane['is_approve'] = $cek_pelanggaranpujian->is_approve;
            $datane['approve_at'] = $cek_pelanggaranpujian->approve_at;
            $datane['approve_by'] = $cek_pelanggaranpujian->approve_by;
            $datane['is_pelanggaran'] = $cek_pelanggaranpujian->is_pelanggaran;
            $datane['date_pelanggaranpujian'] = $cek_pelanggaranpujian->date_pelanggaranpujian;


            parent::_insert('t_nsp', $datane, $userid);
        } else {
            echo json_encode($stt_simpan);
        }
    }

    function pelanggarandanpujian_otorisasipujian()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $where = ["id"  => $data['id']];
        $stt_simpan = parent::_otorisasi('t_pujian_karakter_taruna', $data, $userid, $where, true, 'approve');
        if ($stt_simpan['success']) {
            $cek_pelanggaranpujian = $this->db->query("SELECT '' as id, d.indikator as pelanggaranpujian, a.id_taruna, a.id_semester, a.id_tingkat, a.tahun_ajaran, a.poin, a.latitude, a.longitude, a.alamat, a.is_approve, a.approve_at, a.approve_by, '0' as is_pelanggaran, a.created_at as date_pelanggaranpujian from t_pujian_karakter_taruna a
                        inner join m_pujian_karakter_ref4 b on a.id_pujian_karakter=b.id
                        inner join m_pujian_karakter_ref3 c on b.id_pen_kar_ref3=c.id
                        inner join m_pujian_karakter_ref2 d on c.id_pen_kar_ref2=d.id
                        where a.is_deleted='0' and a.id='" . $data['id'] . "' ")->getRow();
            $datane['id'] = '';
            $datane['pelanggaranpujian'] = $cek_pelanggaranpujian->pelanggaranpujian;
            $datane['id_taruna'] = $cek_pelanggaranpujian->id_taruna;
            $datane['id_semester'] = $cek_pelanggaranpujian->id_semester;
            $datane['id_tingkat'] = $cek_pelanggaranpujian->id_tingkat;
            $datane['tahun_ajaran'] = $cek_pelanggaranpujian->tahun_ajaran;
            $datane['poin'] = $cek_pelanggaranpujian->poin;
            $datane['latitude'] = $cek_pelanggaranpujian->latitude;
            $datane['longitude'] = $cek_pelanggaranpujian->longitude;
            $datane['alamat'] = $cek_pelanggaranpujian->alamat;
            $datane['is_approve'] = $cek_pelanggaranpujian->is_approve;
            $datane['approve_at'] = $cek_pelanggaranpujian->approve_at;
            $datane['approve_by'] = $cek_pelanggaranpujian->approve_by;
            $datane['is_pelanggaran'] = $cek_pelanggaranpujian->is_pelanggaran;
            $datane['date_pelanggaranpujian'] = $cek_pelanggaranpujian->date_pelanggaranpujian;


            parent::_insert('t_nsp', $datane, $userid);
        } else {
            echo json_encode($stt_simpan);
        }
    }

    function save_nsp()
    {
        echo json_encode("test");
    }
    function pelanggaran_load()
    {
        // $query = "SELECT a.id , a.poin, a.is_approve, b.dasar_hukum, b.deskripsi, c.namataruna ,  d.semester
        //                     from t_pelanggaran_karakter_taruna a
        //                     inner join m_pelanggaran_karakter b on a.id_pelanggaran_karakter=b.id and b.is_deleted='0'
        //                     inner join m_user_taruna c on a.id_taruna=c.id_m_user and c.is_deleted='0'
        //                     inner join m_semester d on a.id_semester=d.id
        //                     where a.is_deleted='0' and a.is_approve='0' ";
        $data = json_decode($this->request->getPost('param'), true);

        $where = " ";
        if ($this->request->getPost('usertype') == 'btl') {
            $query = $this->db->query("SELECT a.id as id_batalyon FROM m_sm_batalyon a where id_user_pendidik='" . $this->request->getPost('userid') . "' ")->getRow();
            $where = " and a.id_batalyon='" . $query->id_batalyon . "' ";
        } else if ($this->request->getPost('usertype') == 'kmp') {
            $query = $this->db->query("SELECT b.*, b.id as id_kompi, c.batalyon, d.ganjil_genap from m_sm_kompi b inner join m_sm_batalyon c on b.id_batalyon=c.id 
            LEFT JOIN m_semester d ON c.id_semester=d.id where b.id_user_pendidik='" . $this->request->getPost('userid') . "' ")->getRow();
            $where = " and a.id_kompi='" . $query->id_kompi . "' ";
        } else if ($this->request->getPost('usertype') == 'ptn') {
            $query = $this->db->query("SELECT a.*,a.id as id_peleton, b.kompi, b.id_batalyon, c.batalyon, d.ganjil_genap from m_sm_peleton a inner join m_sm_kompi b on a.id_kompi=b.id inner join m_sm_batalyon c on b.id_batalyon=c.id LEFT JOIN m_semester d ON c.id_semester=d.id where a.id_user_pendidik='" . $this->request->getPost('userid') . "' ")->getRow();

            $where = " and a.id_peleton='" . $query->id_peleton . "' ";
        }

        $query = "SELECT * from (select * from (SELECT a.id , a.poin, a.is_approve, b.dasar_hukum, b.deskripsi, c.namataruna ,  d.semester, 'Pelanggaran' as is_pelanggaran, a.is_deleted, c.id_batalyon, c.id_kompi, c.id_peleton
                            from t_pelanggaran_karakter_taruna a
                            inner join m_pelanggaran_karakter b on a.id_pelanggaran_karakter=b.id and b.is_deleted='0'
                            inner join m_user_taruna c on a.id_taruna=c.id_m_user and c.is_deleted='0'
                            inner join m_semester d on a.id_semester=d.id
                            where a.is_deleted='0' and a.is_approve='0') a
                            union
			select * from (
			SELECT a.id, a.poin, a.is_approve, concat(d.indikator,'
			', c.item_nilai) as dasar_hukum, concat(c.item_nilai,'
			', b.keterangan) as deskripsi, e.namataruna, f.semester, 'Pujian' as is_pelanggaran, a.is_deleted, e.id_batalyon, e.id_kompi, e.id_peleton  from t_pujian_karakter_taruna a
                        inner join m_pujian_karakter_ref4 b on a.id_pujian_karakter=b.id
                        inner join m_pujian_karakter_ref3 c on b.id_pen_kar_ref3=c.id
                        inner join m_pujian_karakter_ref2 d on c.id_pen_kar_ref2=d.id
                        inner join m_user_taruna e on a.id_taruna=e.id_m_user
                        inner join m_semester f on a.id_semester=f.id
                        where a.is_deleted='0' and a.is_approve='0' ) b ) a where a.is_deleted='0' $where";
        $where = ["a.poin", "a.dasar_hukum", "a.deskripsi", "a.namataruna", "a.semester"];

        parent::_loadDatatable($query, $where, $data);
    }
    function pujian_load()
    {
        $query = "SELECT a.id,a.is_approve, e.namataruna, d.indikator, c.item_nilai, b.keterangan, f.semester, a.poin  from t_pujian_karakter_taruna a
                        inner join m_pujian_karakter_ref4 b on a.id_pujian_karakter=b.id
                        inner join m_pujian_karakter_ref3 c on b.id_pen_kar_ref3=c.id
                        inner join m_pujian_karakter_ref2 d on c.id_pen_kar_ref2=d.id
                        inner join m_user_taruna e on a.id_taruna=e.id_m_user
                        inner join m_semester f on a.id_semester=f.id
                        where a.is_deleted='0' and a.is_approve='0'";
        $where = ["e.namataruna", "d.indikator", "c.item_nilai", "b.keterangan", "f.semester", "a.poin"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function pelanggarandanpujian_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_mata_pelajaran', $data, $userid);
    }

    function pelanggarandanpujian_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.*
                        from m_mata_pelajaran a
                        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_mata_pelajaran', $data, null, $query);
    }

    function pelanggarandanpujian_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_mata_pelajaran', $data, $userid);
    }
    // end data datamatapelajaran done


    // begin data program studi done
    function rekapnsp_load()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $id_batalyon = $data['batalyon'] == '' ? '' : ' and a.id_batalyon ="' . $data['batalyon'] . '" ';
        $id_kompi = $data['kompi'] == '' ? '' : ' and a.id_kompi ="' . $data['kompi'] . '" ';
        $id_peleton = $data['peleton'] == '' ? '' : ' and a.id_peleton ="' . $data['peleton'] . '" ';
        $bulan = $data['bulan'] == '' ? '' : ' and month(a.date_pelanggaranpujian) ="' . $data['bulan'] . '" ';
        $id_kelas = $data['kelas'] == '' ? '' : ' and a.id_kelompok ="' . $data['kelas'] . '" ';

        $query = "SELECT a.id, a.id_m_user,a.namataruna, a.noaklong,c.batalyon, f.kelompok as kelas, a.id_batalyon, a.id_kompi, a.id_peleton, a.id_kelompok, b.bulan, '70' as na, ifnull(b.reward,0) as reward, ifnull(b.punisment,0) as punisment, ROUND(ifnull((b.na+b.reward)-punisment, 70), 2) as total, c.id_semester from 
	m_user_taruna a
	left join  (
                    select a.id,a.id_taruna, '70' as na, sum(if(a.is_pelanggaran=0, a.poin, 0)) as reward, sum(if(a.is_pelanggaran=1, a.poin, 0)) as punisment, a.date_pelanggaranpujian as tgl_pelanggaran, month(a.date_pelanggaranpujian) as bulan, a.is_deleted from t_nsp a
                    inner join m_user_taruna b on a.id_taruna=b.id_m_user
                    where a.is_deleted='0' and a.is_approve='1' $bulan
                    group by a.id_taruna) b on a.id_m_user=b.id_taruna
                    
                    inner join m_sm_batalyon c on a.id_batalyon=c.id
                    inner join m_sm_kompi d on a.id_kompi=d.id
                    inner join m_sm_peleton e on a.id_peleton=e.id
                    inner join m_kelompok f on a.id_kelompok=f.id
                     where a.is_deleted='0' $id_batalyon $id_kompi $id_peleton $id_kelas  ";
        // order by f.kelompok asc, a.namataruna asc
        $where = ["a.namataruna", "a.noaklong", "c.batalyon", "f.kelompok", "b.reward", "b.punisment", "(b.na+b.reward)-punisment", "'70'"];
        // parent::_loadDatatable($query, $where, $data);

        $order = "f.kelompok asc , a.namataruna ";
        parent::_loadDatatableorder($query, $where, $data, $order);
        // echo json_encode($this->db->getLastQuery());
    }

    function rekapnsp_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('t_nsp', $data, $userid);
    }

    function rekapnsp_posting()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        $cekuserkarakter = $this->db->query("SELECT * FROM t_penilaian_aspek_karakter a where id_user_taruna='" . $data['id_taruna'] . "' and id_semester='" . $data['id_semester'] . "' and id_batalyon='" . $data['id_batalyon'] . "' ")->getRow();
        if ($cekuserkarakter) {
            $datapost = array(
                'id' => $cekuserkarakter->id,
                'id_user_taruna' => $data['id_taruna'],
                'id_semester' => $data['id_semester'],
                'id_batalyon' => $data['id_batalyon'],
                'bulan_' . $data['bulan'] => $data['nilai'],
            );
        } else {
            $datapost = array(
                'id' => '',
                'id_user_taruna' => $data['id_taruna'],
                'id_semester' => $data['id_semester'],
                'id_batalyon' => $data['id_batalyon'],
                'bulan_' . $data['bulan'] => $data['nilai'],
            );
        }


        parent::_insert('t_penilaian_aspek_karakter', $datapost, $userid);
        echo json_encode($datapost);
    }

    function rekapnsptmp()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        // parent::_insert('t_nsp_tmp', $data, $userid);
        parent::_insertReturn('t_nsp_tmp', $data, $userid);
    }

    function rekapnsp_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $bulan = $data['bulan'] == '' ? '' : ' and month(a.date_pelanggaranpujian) ="' . $data['bulan'] . '" ';


        $query = "SELECT * FROM (SELECT a.id,a.id_taruna, '70' as na, date_format(a.date_pelanggaranpujian,'%d-%m-%Y %H-%i-%s') as tgl_pelanggaran, month(a.date_pelanggaranpujian) as bulan, a.is_deleted , if(a.is_pelanggaran='1', 'Pelanggaran' , 'Pujian' ) as is_pelanggaran, IFNULL(b.pelanggaranpujian, a.pelanggaranpujian) as deskripsi, IFNULL(b.poin, a.poin) as poin, b.id as id_edit
                    from t_nsp a
                    left join t_nsp_tmp b on a.id=b.id_nsp
                    inner join m_user_taruna c on a.id_taruna=c.id_m_user
                    where a.is_deleted='0' and a.is_approve='1'  $bulan and a.id_taruna='" . $data['id_taruna'] . "' ) a where a.is_deleted='0' ";
        // order by f.kelompok asc, a.namataruna asc
        $where = ["a.is_pelanggaran", "a.poin", "a.deskripsi", "a.deskripsi", "a.tgl_pelanggaran"];
        // parent::_loadDatatable($query, $where, $data);

        $order = "a.tgl_pelanggaran ";
        parent::_loadDatatableorder($query, $where, $data, $order);
    }
    function rekapnsp_edita()

    {
        $data = json_decode($this->request->getPost('param'), true);
        $id_jadwal_tugas = 1;

        $bulan = $data['bulan'] == 'null' ? '' : ' and month(a.date_pelanggaranpujian) ="' . $data['bulan'] . '" ';


        $query = "SELECT a.id,a.id_taruna, '70' as na, a.date_pelanggaranpujian as tgl_pelanggaran, month(a.date_pelanggaranpujian) as bulan, a.is_deleted , a.is_pelanggaran, IFNULL(b.pelanggaranpujian, a.pelanggaranpujian) as deskripsi, IFNULL(b.poin, a.poin) as poin, b.id as id_edit
                    from t_nsp a
                    left join t_nsp_tmp b on a.id=b.id_nsp
                    inner join m_user_taruna c on a.id_taruna=c.id_m_user
                    where a.is_deleted='0' and a.is_approve='1'  $bulan and a.id_taruna='" . $data['id_taruna'] . "' order by a.date_pelanggaranpujian ";

        parent::_loadlist($data, $query);
    }

    function rekapnsp_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        // echo json_encode($data);
        parent::_delete('t_nsp', $data, $userid);
    }


    function getdetailpendidik()
    {

        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        // echo json_encode($data['id']);
        $user_detail = $this->webModel->getUserDetail('gdk', $data['id']);
        $response = [
            "user_detail" => $user_detail
        ];
        echo json_encode($response);
    }


    function datansp_load()
    {
        $userid = $this->request->getPost('userid');
        $usertype = $this->request->getPost('usertype');

        $data = json_decode($this->request->getPost('param'), true);
        // echo json_encode($usertype);
        if ($usertype == 'btl') {
            $query = "SELECT * from (
                    SELECT a.id,a.id_taruna, '70' as na, a.date_pelanggaranpujian as tgl_pelanggaran, month(a.date_pelanggaranpujian) as bulan, a.is_deleted , a.is_pelanggaran,  a.pelanggaranpujian as deskripsi, a.poin as poin, b.id as id_edit , approvetrn_at, approvetrn_sts , approvebtl_at, approvebtl_sts
                    from t_nsp a
                    left join t_nsp_tmp b on a.id=b.id_nsp
                    inner join m_user_taruna c on a.id_taruna=c.id_m_user
                    where a.is_deleted='0' and a.is_approve='1' order by a.date_pelanggaranpujian ) a where a.is_deleted='0' ";
        } else if ($usertype == 'trn') {
            $query = "SELECT * from (
                    SELECT a.id,a.id_taruna, '70' as na, a.date_pelanggaranpujian as tgl_pelanggaran, month(a.date_pelanggaranpujian) as bulan, a.is_deleted , a.is_pelanggaran,  a.pelanggaranpujian as deskripsi, a.poin as poin, b.id as id_edit , approvetrn_at, approvetrn_sts , approvebtl_at, approvebtl_sts
                    from t_nsp a
                    left join t_nsp_tmp b on a.id=b.id_nsp
                    inner join m_user_taruna c on a.id_taruna=c.id_m_user
                    where a.is_deleted='0' and a.is_approve='1' and a.id_taruna='" . $userid . "' order by a.date_pelanggaranpujian ) a where a.is_deleted='0' ";
        } else {
            $query = "SELECT * from (
                    SELECT a.id,a.id_taruna, '70' as na, a.date_pelanggaranpujian as tgl_pelanggaran, month(a.date_pelanggaranpujian) as bulan, a.is_deleted , a.is_pelanggaran,  a.pelanggaranpujian as deskripsi, a.poin as poin, b.id as id_edit , approvetrn_at, approvetrn_sts , approvebtl_at, approvebtl_sts
                    from t_nsp a
                    left join t_nsp_tmp b on a.id=b.id_nsp
                    inner join m_user_taruna c on a.id_taruna=c.id_m_user
                    where a.is_deleted='0' and a.is_approve='1' order by a.date_pelanggaranpujian ) a where a.is_deleted='0' ";
        }
        $where = ["a.deskripsi", "a.poin"];

        parent::_loadDatatable($query, $where, $data);
    }

    function datansp_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('t_nsp', $data, $userid);
    }

    function datansp_edit()

    {
        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT * from (
                    SELECT a.id,a.id_taruna, '70' as na, a.date_pelanggaranpujian as tgl_pelanggaran, month(a.date_pelanggaranpujian) as bulan, a.is_deleted , a.is_pelanggaran,  a.pelanggaranpujian as deskripsi, a.poin as poin, b.id as id_edit , a.is_approve as sts_kompi, a.approve_at as date_kompi, a.approvetrn_sts as sts_trn, a.approvetrn_at as date_trn, a.approvebtl_sts as sts_btl, a.approvebtl_at as date_btl
                    from t_nsp a
                    left join t_nsp_tmp b on a.id=b.id_nsp
                    inner join m_user_taruna c on a.id_taruna=c.id_m_user
                    where a.is_deleted='0' and a.is_approve='1' and a.id='" . $data['id'] . "' order by a.date_pelanggaranpujian ) a where a.is_deleted='0' ";

        // parent::_loadlist($data, $query);
        parent::_edit('t_nsp', $data, null, $query);
    }

    function rekapnsp_updatensp()

    {
        $data = json_decode($this->request->getPost('param'), true);

        // echo json_encode($data);

        $query = " SELECT * FROM t_nsp a where a.is_deleted='0' and a.id='" . $data['id'] . "' ";

        parent::_edit('t_nsp', $data, null, $query);
    }



    function verifnsp_load()
    {
        $userid = $this->request->getPost('userid');
        $usertype = $this->request->getPost('usertype');

        $data = json_decode($this->request->getPost('param'), true);
        // echo json_encode($usertype);

        $query = "SELECT * from ( SELECT a.id,a.id_taruna, '70' as na, a.date_pelanggaranpujian as tgl_pelanggaran, month(a.date_pelanggaranpujian) as bulan, a.is_deleted , a.is_pelanggaran,  a.pelanggaranpujian as deskripsi, a.poin as poin, b.id as id_edit , approvetrn_at, approvetrn_sts , approvebtl_at, approvebtl_sts
                from t_nsp a
                left join t_nsp_tmp b on a.id=b.id_nsp
                inner join m_user_taruna c on a.id_taruna=c.id_m_user
                inner join m_sm_batalyon d on c.id_batalyon=d.id and d.id_user_pendidik='" . $userid . "'
                where a.is_deleted='0' and a.is_approve='1' and a.approvetrn_sts='1' and a.approvebtl_sts='0' order by a.date_pelanggaranpujian ) a where a.is_deleted='0'";
        $where = ["a.deskripsi", "a.poin"];

        parent::_loadDatatable($query, $where, $data);
    }


    function datanspa_edit()

    {
        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT * from (
                    SELECT a.id,a.id_taruna, '70' as na, a.date_pelanggaranpujian as tgl_pelanggaran, month(a.date_pelanggaranpujian) as bulan, a.is_deleted , a.is_pelanggaran,  a.pelanggaranpujian as deskripsi, a.poin as poin, b.id as id_edit , a.is_approve as sts_kompi, a.approve_at as date_kompi, a.approvetrn_sts as sts_trn, a.approvetrn_at as date_trn, a.approvebtl_sts as sts_btl, a.approvebtl_at as date_btl
                    from t_nsp a
                    left join t_nsp_tmp b on a.id=b.id_nsp
                    inner join m_user_taruna c on a.id_taruna=c.id_m_user
                    where a.is_deleted='0' and a.is_approve='1' and a.id='" . $data['id'] . "' order by a.date_pelanggaranpujian ) a where a.is_deleted='0' ";

        // parent::_loadlist($data, $query);
        parent::_edit('t_nsp', $data, null, $query);
    }

    // end data program studi done

    // end controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------





}
