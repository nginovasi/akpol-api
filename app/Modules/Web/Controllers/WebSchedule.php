<?php

namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;

class WebSchedule extends BaseController
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

    function kelompok_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, kelompok as text, a.max_kapasitas from m_kelompok a where a.is_deleted = 0";
        $where = ["a.kelompok"];

        parent::_loadSelect2($data, $query, $where);
    }

    function tingkat_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, a.tingkatan as text from m_tingkatan a where a.is_deleted = 0";
        $where = ["a.tingkatan"];

        parent::_loadSelect2($data, $query, $where);
    }

    function semester_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.*, a.semester as text from m_semester a where a.is_deleted = 0";
        $where = ["a.semester"];

        parent::_loadSelect2($data, $query, $where);
    }

    function jabatan_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id as id, a.jabatan as text from m_tingkatan_detail a where a.is_deleted = 0 ";
        $where = ["a.jabatan"];

        parent::_loadSelect2($data, $query, $where);
    }

    function taruna_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, concat(a.nik, ' | ' , a.namataruna) as text, a.nik, a.namataruna from m_user_taruna a where a.is_deleted = 0";
        $where = ["a.namataruna"];

        parent::_loadSelect2($data, $query, $where);
    }

    function kelompoktaruna_list_get()
    {
        $data = $this->request->getGet();

        $qr = $this->db->query("SELECT a.id_kelompok, a.id_tingkat ,a.id_jabatan ,a.id_semester, a.tahun_ajaran_awal  from t_kelompok_taruna a where a.id='" . $this->request->getGet('id') . "' ")->getRow();

        $query = "SELECT b.namataruna,b.noakshort, b.noaklong 
                    from t_kelompok_taruna a 
                    left join m_user_taruna b on a.id_taruna=b.id
                    where a.id_kelompok='" . $qr->id_kelompok . "' AND a.id_tingkat='" . $qr->id_tingkat . "' AND a.id_jabatan='" . $qr->id_jabatan . "' AND a.id_semester='" . $qr->id_semester . "' AND a.tahun_ajaran_awal='" . $qr->tahun_ajaran_awal . "' and a.is_deleted='0' ";

        parent::_loadlist($data, $query);
    }


    function mata_pelajaran_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT a.id, concat(a.kode_mk, ' | ' , IF(a.id_aspek IS NULL, CONCAT(a.mata_pelajaran, ' (Belum ada aspek)'), CONCAT(a.mata_pelajaran, ' (', b.aspek, ')'))) AS text, deskripsi 
                    FROM m_mata_pelajaran a 
                    LEFT JOIN m_aspek b ON a.id_aspek = b.id
                    WHERE a.is_deleted = 0 AND a.id_kurikulum='".$data['id_kurikulum']."' ";
        $where = ["a.kode_mk", "a.mata_pelajaran", "b.aspek"];

        parent::_loadSelect2($data, $query, $where);
        
    }

    function pendidik_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id_m_user as id, concat(a.nik, ' | ' , a.namagadik) as text from m_user_pendidik a where a.is_deleted = 0";
        $where = ["a.nik", "a.namagadik"];

        parent::_loadSelect2($data, $query, $where);
    }

    function kelompok_taruna_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id_m_user as id, concat(a.nik, ' | ' , a.namagadik) as text from m_user_pendidik a where a.is_deleted = 0";
        $where = ["a.nik", "a.namagadik"];

        parent::_loadSelect2($data, $query, $where);
    }

    function ruang_kelas_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, concat(a.kode, ' | ' , a.nama) as text from m_ruang_kelas a where a.is_deleted = 0";
        $where = ["a.kode", "a.nama"];

        parent::_loadSelect2($data, $query, $where);
    }

    function jam_pertemuan_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id_m_user as id, concat(a.nik, ' | ' , a.namagadik) as text from m_user_pendidik a where a.is_deleted = 0";
        $where = ["a.nik", "a.namagadik"];

        parent::_loadSelect2($data, $query, $where);
    }

    function bahan_ajar_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id_m_user as id, concat(a.nik, ' | ' , a.namagadik) as text from m_user_pendidik a where a.is_deleted = 0";
        $where = ["a.nik", "a.namagadik"];

        parent::_loadSelect2pagging($data, $query, $where);
    }

    function batalyon_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT  * from ( SELECT a.id , concat(a.batalyon, ' ( ' , a.tahun_masuk , ' ) ') as text  , a.id_kurikulum, a.is_deleted from m_sm_batalyon a where a.is_deleted='0' ) a1 where a1.is_deleted='0' ";
        $where = ["a1.text"];

        parent::_loadSelect2($data, $query, $where);
    }

    function tahun_ajaran_get()
    {
        $query = "select nilai as tahun_ajaran from m_config where kode = 'TA'";
        $result = $this->db->query($query)->getResult();

        echo json_encode($result);
    }

    function jam_pertemuan_get()
    {
        $query = "select * from m_jam_pertemuan where is_weekdays = 1 and is_deleted = 0 order by jam_mulai asc";
        $result = $this->db->query($query)->getResult();

        echo json_encode($result);
    }

    function kelompok_taruna_get()
    {
        $tahun_ajaran = $this->request->getPost('tahun_ajaran');
        $id_tingkat = $this->request->getPost('id_tingkat');
        $id_semester = $this->request->getPost('id_semester');
        // if ((isset($tahun_ajaran)||$tahun_ajaran.isemty())&&(isset($id_tingkat)||$id_tingkat.isemty())&&(isset($id_semester)||$id_semester.isemty())) {
        $query = "select  
                    c.id as id_kelompok,
                    c.kelompok as nama_kelompok,
                    b.tahun_ajaran_awal as tahun_ajaran,
                    b.id_tingkat,
                    b.id_semester
                from m_config a
                inner join t_kelompok_taruna b
                    on a.nilai = b.tahun_ajaran_awal
                left join m_kelompok c
                    on b.id_kelompok = c.id
                where a.kode = 'TA'
                    and b.tahun_ajaran_awal = '" . $tahun_ajaran . "'
                    and b.id_tingkat = " . $id_tingkat . "
                    and b.id_semester = " . $id_semester . "
                group by c.id";
        $result = $this->db->query($query)->getResult();

        echo json_encode($result);
        // } else {
        //     $response['data'] = new stdclass();
        //     echo json_encode($response);
        // }
    }

    // end ajax ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


    // controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


    function datakelompoktaruna_load()
    {
        $query = "SELECT a.* , b.kelompok as nama_kelompok, concat(count(*),' dari ',b.max_kapasitas) as max_kelompok, c.tingkatan as nama_tingkatan, d.jabatan as nama_jabatan from t_kelompok_taruna a 
            left join m_kelompok b on a.id_kelompok=b.id
            left join m_tingkatan c on a.id_tingkat=c.id
            left join m_tingkatan_detail d on a.id_tingkat=d.id
            left join m_semester e on a.id_semester=e.id
            where a.is_deleted='0'
            group by a.id_kelompok, a.id_tingkat ,a.id_jabatan ,a.id_semester, a.tahun_ajaran_awal";
        $where = ["b.kelompok", "b.max_kapasitas"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function datakelompoktaruna_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insertbatch('t_kelompok_taruna', $data, $userid);
    }

    function datakelompoktaruna_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $qr = $this->db->query("SELECT a.id_kelompok, a.id_tingkat ,a.id_jabatan ,a.id_semester, a.tahun_ajaran_awal  from t_kelompok_taruna a where a.id='" . $data['id'] . "' ")->getRow();

        $query = "SELECT a.*, concat(b.nik, ' | ' , b.namataruna) as nama_taruna,b.nik, c.kelompok as nama_kelompok , c.max_kapasitas as max_kapasitas , d.tingkatan as nama_tingkat , e.jabatan as nama_jabatan, f.semester as nama_semester
                    from t_kelompok_taruna a 
                    left join m_user_taruna b on a.id_taruna=b.id
                    left join m_kelompok c on a.id_kelompok=c.id
                    left join m_tingkatan d on a.id_tingkat=d.id
                    left join m_tingkatan_detail e on a.id_jabatan=e.id
                    left join m_semester f on a.id_semester=f.id
                    where a.id_kelompok='" . $qr->id_kelompok . "' AND a.id_tingkat='" . $qr->id_tingkat . "' AND a.id_jabatan='" . $qr->id_jabatan . "' AND a.id_semester='" . $qr->id_semester . "' AND a.tahun_ajaran_awal='" . $qr->tahun_ajaran_awal . "' and a.is_deleted='0' ";

        parent::_editbatch('t_kelompok_taruna', $data, null, $query);
    }

    function datakelompoktaruna_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        $qr = $this->db->query("SELECT a.id_kelompok, a.id_tingkat ,a.id_jabatan ,a.id_semester, a.tahun_ajaran_awal  from t_kelompok_taruna a where a.id='" . $data['id'] . "' ")->getRow();

        $query = "UPDATE t_kelompok_taruna a set a.is_deleted='1' , last_edited_at='" . date('Y-m-d H:i:s') . "' , last_edited_by='" . $userid . "'
                    where a.id_kelompok='" . $qr->id_kelompok . "' AND a.id_tingkat='" . $qr->id_tingkat . "' AND a.id_jabatan='" . $qr->id_jabatan . "' AND a.id_semester='" . $qr->id_semester . "' AND a.tahun_ajaran_awal='" . $qr->tahun_ajaran_awal . "' ";

        // echo json_encode($query);
        if ($this->db->query($query)) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, 'message' => $this->db->error()['message']));
        }
    }

    function databahanajar_load()
    {
        $query = "SELECT a.* , b.mata_pelajaran , c.name as namagadik, d.tingkatan , e.semester, f.id AS id_prodi, CONCAT(f.nama, ' | ' , f.`tahun_ajaran`) AS nama_prodi
                from t_bahan_ajar a
                left join m_mata_pelajaran b on a.id_mata_pelajaran=b.id 
                left join m_user c on a.id_user_pendidik=c.id
                left join m_tingkatan d on a.id_tingkat=d.id
                left join m_semester e on a.id_semester=e.id
                LEFT JOIN m_program_studi f ON a.tahun_ajaran=f.tahun_ajaran
                where a.is_deleted='0'
                group by a.id_mata_pelajaran , a.id_user_pendidik , a.id_tingkat , a.id_semester ,a.tahun_ajaran";
        $where = ["b.kelompok", "b.max_kapasitas"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function databahanajar_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insertbatch('t_bahan_ajar', $data, $userid);
    }

    function databahanajar_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $qr = $this->db->query("SELECT a.id_mata_pelajaran, a.id_user_pendidik ,a.id_tingkat ,a.id_semester , a.tahun_ajaran  from t_bahan_ajar a where a.id='" . $data['id'] . "' ")->getRow();

        $query = "SELECT a.* , b.mata_pelajaran as nama_mata_pelajaran , c.namagadik as nama_user_pendidik, d.tingkatan as nama_tingkat, e.semester as nama_semester, a.tahun_ajaran, f.id AS id_prodi, CONCAT(f.nama, ' | ' , f.`tahun_ajaran`) AS nama_prodi 
                from t_bahan_ajar a
                left join m_mata_pelajaran b on a.id_mata_pelajaran=b.id 
                left join m_user_pendidik c on a.id_user_pendidik=c.id_m_user
                left join m_tingkatan d on a.id_tingkat=d.id
                left join m_semester e on a.id_semester=e.id
                LEFT JOIN m_program_studi f ON a.tahun_ajaran=f.tahun_ajaran
                where a.is_deleted='0' and a.id_mata_pelajaran='" . $qr->id_mata_pelajaran . "' and a.id_user_pendidik='" . $qr->id_user_pendidik . "' and a.id_tingkat='" . $qr->id_tingkat . "' and a.id_semester='" . $qr->id_semester . "' and a.tahun_ajaran='" . $qr->tahun_ajaran . "' ";

        parent::_editbatch('t_bahan_ajar', $data, null, $query);
    }

    function databahanajar_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        $qr = $this->db->query("SELECT a.id_mata_pelajaran, a.id_user_pendidik ,a.id_tingkat ,a.id_semester , a.tahun_ajaran  from t_bahan_ajar a where a.id='" . $data['id'] . "' ")->getRow();

        $query = "UPDATE t_bahan_ajar a set a.is_deleted='1' , last_edited_at='" . date('Y-m-d H:i:s') . "' , last_edited_by='" . $userid . "'
                    where a.is_deleted='0' and a.id_mata_pelajaran='" . $qr->id_mata_pelajaran . "' and a.id_user_pendidik='" . $qr->id_user_pendidik . "' and a.id_tingkat='" . $qr->id_tingkat . "' and a.id_semester='" . $qr->id_semester . "' and a.tahun_ajaran='" . $qr->tahun_ajaran . "' ";

        // echo json_encode($query);
        if ($this->db->query($query)) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, 'message' => $this->db->error()['message']));
        }
    }


    function datapengampuhanjar_load()
    {

        $query = "SELECT
                        *
                    FROM
                        (
                            SELECT
                                a.*,
                                concat(b.kode_mk, ' | ', IF(b.id_aspek IS NULL, CONCAT(b.mata_pelajaran, '(Belum ada aspek)'), CONCAT(b.mata_pelajaran, '(',f.aspek, ')'))) AS mata_pelajaran,
                                json_arrayagg(
                                    json_object('nama', c.namagadik, 'photopath', c.photopath)
                                ) AS pendidik,
                                d.batalyon,
                                d.tahun_masuk,
                                e.kurikulum,
                                f.aspek
                            FROM
                                t_pendidik_mata_pelajaran a
                                LEFT JOIN m_mata_pelajaran b ON b.id = a.id_mata_pelajaran
                                INNER JOIN m_user_pendidik c ON c.id_m_user = a.id_pendidik AND c.is_deleted = '0'
                                LEFT JOIN m_sm_batalyon d ON a.id_batalyon = d.id
                                LEFT JOIN m_kurikulum e ON d.id_kurikulum = e.id
                                        LEFT JOIN m_aspek f ON b.id_aspek = f.id
                            WHERE
                                a.is_deleted = '0'
                            GROUP BY
                                a.id_mata_pelajaran,
                                a.id_batalyon
                        ) a
                    WHERE
                        a.is_deleted = '0'";

        $where = ["a.mata_pelajaran", "a.pendidik", "a.batalyon" , "a.pendidik"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function datapengampuhanjar_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insertbatch('t_pendidik_mata_pelajaran', $data, $userid);
    }

    function datapengampuhanjar_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $qr = $this->db->query("SELECT a.id_mata_pelajaran, a.id_batalyon from t_pendidik_mata_pelajaran a where a.id='" . $data['id'] . "' ")->getRow();

        $query = "SELECT a.id_mata_pelajaran ,  concat(b.kode_mk, ' | ', IF(b.id_aspek IS NULL, concat(b.mata_pelajaran, ' (Belum ada aspek)'), concat(b.mata_pelajaran, ' (',e.aspek,')')) ) AS nama_mata_pelajaran, a.id as 'id', a.id_pendidik as 'id_pendidik' , concat(c.nik, ' | ' , c.namagadik) as 'nama_pendidik' , a.is_ketua_tim as 'is_ketua_tim' , a.id_batalyon, concat(d.batalyon, ' ( ' , d.tahun_masuk , ' ) ') as nama_batalyon
                from t_pendidik_mata_pelajaran a
                left join m_mata_pelajaran b on b.id=a.id_mata_pelajaran
                inner join m_user_pendidik c on c.id_m_user=a.id_pendidik and c.is_deleted='0'
                left join m_sm_batalyon d on a.id_batalyon=d.id
                LEFT JOIN m_aspek e ON b.id_aspek = e.id
                where a.is_deleted='0' and a.id_mata_pelajaran='" . $qr->id_mata_pelajaran . "' and a.id_batalyon='" . $qr->id_batalyon . "'
                ORDER BY a.is_ketua_tim DESC";

        parent::_editbatch('t_pendidik_mata_pelajaran', $data, null, $query);
    }

    function datapengampuhanjar_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        $qr = $this->db->query("SELECT a.id_mata_pelajaran, a.id_pendidik , a.id_batalyon  from t_pendidik_mata_pelajaran a where a.id='" . $data['id'] . "' ")->getRow();


        $query = "UPDATE t_pendidik_mata_pelajaran a set a.is_deleted='1' , a.is_ketua_tim='0' , last_edited_at='" . date('Y-m-d H:i:s') . "' , last_edited_by='" . $userid . "'
                    where a.id_mata_pelajaran='" . $qr->id_mata_pelajaran . "' AND a.id_batalyon='" . $qr->id_batalyon . "' ";

        // echo json_encode($query);
        if ($this->db->query($query)) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, 'message' => $this->db->error()['message']));
        }
    }

    function datajadwal_load()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
    
        // $tahun_ajaran = $this->request->getPost('tahun_ajaran');
        $tanggal_awal = $data['tanggal'];
        $id_semester = $data['id_semester'];

        // $tanggal_awal = '2021-11-08';
        // $tanggal_akhir = '2021-11-14';
        $tanggal_akhir = date( "Y-m-d", strtotime( "$tanggal_awal +7 day" ) );
        // $id_tingkat = '2';
        // $id_semester = '1';


        $header = $this->db->query("SELECT k.id_kelompok, k.nama_kelompok, if(l.id_ruangan is null,'',l.id_ruangan) as id_ruangan, if(l.nama_ruangan is null,'',l.nama_ruangan) as nama_ruangan, k.tahun_ajaran, k.id_tingkat, k.id_semester 
            from (
                select c.id as id_kelompok, c.kelompok as nama_kelompok, b.tahun_ajaran_awal as tahun_ajaran, b.id_tingkat, b.id_semester from m_config a inner join t_kelompok_taruna b on a.nilai = b.tahun_ajaran_awal left join m_kelompok c on b.id_kelompok = c.id where a.kode = 'TA' and b.tahun_ajaran_awal = a.nilai and b.id_tingkat = 2 and b.id_semester = 1 and a.is_deleted = 0 and b.is_deleted = 0 and c.is_deleted = 0 group by c.id
                ) k 
            left join (
                select a.id_kelompok_taruna as id_kelompok, b.id as id_ruangan, b.nama as nama_ruangan  from t_jadwal a left join m_ruang_kelas b on a.id_ruang_kelas = b.id where a.tanggal between '".$tanggal_awal."' and '".$tanggal_akhir."' group by a.id_kelompok_taruna, a.id_ruang_kelas
                ) l on k.id_kelompok = l.id_kelompok
            where  k.id_semester = '".$id_semester."' ")->getResult();
        
        $query = "SELECT  
            a.selected_date as tanggal,
            json_arrayagg( json_object('id_pertemuan' , b.id , 'unit_pertemuan' , b.unit , 'jam_mulai' , TIME_FORMAT(b.jam_mulai, '%H:%i') , 'jam_selesai' , TIME_FORMAT(b.jam_selesai, '%H:%i') ) ) as data_unit	
            from 
                (select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
                (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
                (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
                (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
                (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
                (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) a
            join m_jam_pertemuan b where b.is_weekdays = 1 and b.is_deleted = 0
            and a.selected_date between '".$tanggal_awal."' and '".$tanggal_akhir."' 
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
                                    a.tanggal,
                                    if(g.is_teori=1,g.jumlah_pertemuan*2,g.jumlah_pertemuan*1) as jumlah_pertemuan,
                                    if(g.is_teori=1,e.pertemuan_ke*2,e.pertemuan_ke*1) as pertemuan_ke,
                                    if(g.is_teori=1,g.jumlah_pertemuan*2,g.jumlah_pertemuan*1)-if(g.is_teori=1,e.pertemuan_ke*2,e.pertemuan_ke*1) as sisa_pertemuan
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
                                    on e.id_user_pendidik = f.id_m_user
                                left join m_mata_pelajaran g
                                    on e.id_mata_pelajaran = g.id
                                where a.is_deleted = 0
                                    and b.is_deleted = 0
                                    and c.is_deleted = 0
                                    and d.is_deleted = 0
                                    and d.is_weekdays = 1
                                    and e.is_deleted = 0
                                    and f.is_deleted = 0
                                    and g.is_deleted = 0
                                    and a.tanggal between '".$tanggal_awal."' and '".$tanggal_akhir."' ")->getResult();

        $result = $this->db->query($query)->getResult();


        // $data = array(
        //             "Header" => $header);

        $data = array(
            "header" => $header,
            "body" => $result,
            "content" => $data_unit
        );


        $response = [ 'success' => true , 'data' => $data ];

        echo json_encode($response);
    }

    function kelompokruangkelas_load()
    {
        $query = "SELECT a.*, b.kelompok, c.kode as kode_ruang, c.nama as ruang_kelas, d.semester, e.tingkatan as tingkat from t_kelompok_ruang_kelas a left join
        m_kelompok b on a.id_kelompok = b.id left join
        m_ruang_kelas c on a.id_ruang_kelas = c.id left join
        m_semester d on a.id_semester = d.id left join
        m_tingkatan e on a.id_tingkat = e.id where a.is_deleted = 0";
        $where = ["b.kelompok", "c.nama", "d.semester", "e.tingkatan", "a.tahun_ajaran"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function kelompokruangkelas_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('t_kelompok_ruang_kelas', $data, $userid);
    }

    function kelompokruangkelas_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.*, b.kelompok, c.kode as kode_ruang, c.nama as ruang_kelas, d.semester, e.tingkatan as tingkat from t_kelompok_ruang_kelas a left join
        m_kelompok b on a.id_kelompok = b.id left join
        m_ruang_kelas c on a.id_ruang_kelas = c.id left join
        m_semester d on a.id_semester = d.id left join
        m_tingkatan e on a.id_tingkat = e.id where a.is_deleted = 0
        and a.id = '" . $data['id'] . "'";
        parent::_edit('m_ruang_kelas', $data, null, $query);
    }
    
    function kelompokruangkelas_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('t_kelompok_ruang_kelas', $data, $userid);
    }
    // end controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

}
