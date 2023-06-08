<?php

namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;
use CodeIgniter\Session\Session;

class WebAkademikkhs extends BaseController
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
        $query = "select a.id, kelompok as text from m_kelompok a where a.is_deleted = 0";
        $where = ["a.kelompok"];

        parent::_loadSelect2($data, $query, $where);
    }

    function kelompokbybatalandmatkul_select_get()
    {
        $data = $this->request->getGet();
        $id_batalyon = $data["id_batalyon"];
        $id_mata_pelajaran = $data["id_mata_pelajaran"];

        if (isset($data['type_code'])) {

            if ($data['type_code'] == 'gdk') {
                $id_pendidik = $data["id_pendidik"];
                $where = " and a.id_batalyon='" . $id_batalyon . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "'  and a.id_user_pendidik='" . $id_pendidik . "'  ";

                // $where = " and a.id_batalyon='".$id_batalyon."' and b.id_mata_pelajaran='".$id_mata_pelajaran."'  and a.id_user_pendidik='".$id_pendidik."' and a.id_ruang_kelas='1' ";
            } else {
                $where = " and a.id_batalyon='" . $id_batalyon . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "' ";
            }
        } else {
            $where = " and a.id_batalyon='" . $id_batalyon . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "' ";
        }

        $query = "SELECT c.id, c.kelompok as text from t_jadwal a 
                    left join t_bahan_ajar b on a.id_bahan_ajar=b.id
                    join m_kelompok c on a.id_kelompok_taruna=c.id
                    where a.is_deleted='0' $where
                    ";

        $where = ["c.kelompok"];
        $orderby = "group by a.id_ruang_kelas";

        parent::_loadSelect2orderby($data, $query, $where, $orderby);
    }

    function kelompokwhere_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT * FROM (SELECT 
                        c.id,
                        c.kelompok AS text,
                        c.kelompok,
                        a.is_deleted
                    FROM m_user_taruna a
                    LEFT JOIN m_sm_batalyon b ON a.id_batalyon=b.id
                    LEFT JOIN m_kelompok c
                        ON c.id = a.id_kelompok
                    WHERE a.is_deleted = 0
                        AND a.is_deleted = 0
                        AND a.id_batalyon='" . $data['id_batalyon'] . "'
                    GROUP BY a.id_kelompok ) a WHERE a.is_deleted=0 ";
        $where = ["a.kelompok", "a.id"];

        parent::_loadSelect2($data, $query, $where);
    }

    function semester_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.*, a.semester as text from m_semester a where a.is_deleted = 0 AND ganjil_genap is not null";
        $where = ["a.semester"];

        parent::_loadSelect2($data, $query, $where);
    }

    function semesterbybatalyon_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT a.*, concat(a.semester, ' ',if(b.id is null , '(Belum Ada Mata Kuliah)' , '') )  as text , if(b.id is null , '0' , '1') as is_mapel from m_semester a
                left join ( select 
                    a.*
                from m_semester a 
                left join t_program_studi_mata_pelajaran b on a.id=b.id_semester
                where b.id_batalyon='" . $data['id_batalyon'] . "' group by a.id) b on a.id=b.id
                where a.is_deleted = 0 AND a.ganjil_genap is not null ";
        $where = ["a.semester"];

        parent::_loadSelect2($data, $query, $where);
    }

    function matapelajaran_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, a.mata_pelajaran as text, a.kode_mk from m_mata_pelajaran a where a.is_deleted = 0";
        $where = ["a.mata_pelajaran"];

        parent::_loadSelect2($data, $query, $where);
    }

    function matapelajaranwhere_select_get()
    {
        $data = $this->request->getGet();

        $query = "SELECT 
                    d.id,
                    d.kode_mk,
                    d.mata_pelajaran as text
                from t_program_studi_mata_pelajaran a
                left join m_program_studi b
                    on a.id_program_studi = b.id
                left join m_config c
                    on c.id = 1
                left join m_mata_pelajaran d
                    on d.id = a.id_mata_pelajaran
                where a.is_deleted = 0
                    and c.nilai = b.tahun_ajaran
                    and a.id_semester ='" . $data['id_semester'] . "' ";
        $where = ["d.mata_pelajaran"];

        parent::_loadSelect2($data, $query, $where);
    }

    function matapelajaranbysemester_select_get()
    {
        $data = $this->request->getGet();
        $id_semester = $data["id_semester"];
        $id_aspek = $data["id_aspek"];
        $query = "select 
            a.id_mata_pelajaran as id,
            b.kode_mk,
            b.mata_pelajaran as text
        from t_program_studi_mata_pelajaran a
        inner join m_mata_pelajaran b
            on a.id_mata_pelajaran = b.id
        left join m_program_studi c on a.id_program_studi=c.id
        left join m_config d on d.id=1";
        $where = ["b.mata_pelajaran"];
        $orderby = "where a.id_semester = '" . $id_semester . "'
        and b.id_aspek = '" . $id_aspek . "' and c.tahun_ajaran=d.nilai and a.is_deleted=0;";

        parent::_loadSelect2orderby($data, $query, $where, $orderby);
    }

    function matapelajaranbybatalyon_select_get()
    {
        $data = $this->request->getGet();
        $id_batalyon = $data["id_batalyon"];
        $id_aspek = $data["id_aspek"];
        $id_pendidik = $data["id_pendidik"];

        // if (isset($data['type_code'])) {
        //     if ($data['type_code'] == 'gdk') {
        //         $join = "JOIN (
        //             SELECT
        //                 *
        //             FROM
        //                 t_pendidik_mata_pelajaran a1
        //             WHERE
        //                 a1.id_pendidik = '$id_pendidik'
        //             GROUP BY
        //                 a1.id_batalyon,
        //                 a1.id_mata_pelajaran
        //         ) g ON a.id_batalyon = g.id_batalyon AND a.id_mata_pelajaran = g.id_mata_pelajaran";
        //     } else {
        //         $join = "";
        //     }
        // } else {
        //     $join = "";
        // }

        //     $query = "SELECT
        //     a.id_mata_pelajaran as id,
        //     b.kode_mk,
        //     b.mata_pelajaran as text,
        //     d.semester,
        //     f.tahun_ajaran
        // from t_program_studi_mata_pelajaran a
        // left join m_mata_pelajaran b
        //     on b.id = a.id_mata_pelajaran
        // join m_sm_batalyon c
        //     on a.id_semester = c.id_semester
        //     and c.id = a.id_batalyon
        // left join m_semester d

        //     on a.id_semester = d.id
        // left join t_program_studi_mata_pelajaran f 
        //     on f.id_mata_pelajaran = a.id_mata_pelajaran
        //     and f.id_semester = a.id_semester
        //     and f.id_batalyon = a.id_batalyon
        //     $join
        // where a.id_batalyon = '" . $id_batalyon . "'
        //     and a.is_deleted='0'
        //     and a.id_aspek = '" . $id_aspek . "'
        //     ";

        // $query = "SELECT DISTINCT a.id_mata_pelajaran AS id, b.kode_mk,
        // CONCAT(b.kode_mk, ' | ', IF(b.id_aspek IS NULL, CONCAT(b.mata_pelajaran, ' (Belum ada aspek)'), CONCAT(b.mata_pelajaran, ' (', e.aspek,')'))) AS text,
        // d.semester, f.tahun_ajaran
        // FROM t_program_studi_mata_pelajaran a
        // LEFT JOIN m_mata_pelajaran b ON b.id = a.id_mata_pelajaran
        // LEFT JOIN m_sm_batalyon c ON a.id_semester = c.id_semester AND c.id = a.id_batalyon
        // LEFT JOIN m_semester d ON a.id_semester = d.id
        // LEFT JOIN t_program_studi_mata_pelajaran f ON f.id_mata_pelajaran = a.id_mata_pelajaran AND f.id_semester = a.id_semester AND f.id_batalyon = a.id_batalyon
        // LEFT JOIN m_aspek e ON e.id = b.id_aspek
        // $join
        // WHERE a.id_batalyon = '$id_batalyon' AND a.is_deleted = '0' AND a.id_aspek = '$id_aspek'
        // ";

        $query = "SELECT
        a.id_mata_pelajaran AS id,
        b.kode_mk,
        CONCAT(
            b.kode_mk,
            ' | ',
            IF(
                b.id_aspek IS NULL,
                CONCAT(b.mata_pelajaran, ' (Belum ada aspek)'),
                CONCAT(b.mata_pelajaran, ' (', a.id_aspek, ')')
            )
        ) AS text,
        d.semester,
        a.tahun_ajaran
    FROM
        t_program_studi_mata_pelajaran a
        LEFT JOIN m_mata_pelajaran b ON b.id = a.id_mata_pelajaran
        JOIN m_sm_batalyon c ON a.id_semester = c.id_semester AND c.id = $id_batalyon
        LEFT JOIN m_semester d ON a.id_semester = d.id
        JOIN (
            SELECT
                nilai
            FROM
                m_config
            WHERE
                kode = 'TA'
        ) e ON e.nilai = a.tahun_ajaran
        JOIN (
            SELECT
                *
            FROM
                t_pendidik_mata_pelajaran a1
            WHERE
                a1.id_pendidik = '$id_pendidik'
            GROUP BY
                a1.id_batalyon,
                a1.id_mata_pelajaran
        ) g ON a.id_batalyon = g.id_batalyon AND a.id_mata_pelajaran = g.id_mata_pelajaran
        LEFT JOIN t_program_studi_mata_pelajaran f ON f.id_mata_pelajaran = a.id_mata_pelajaran AND f.id_semester = a.id_semester AND f.id_batalyon = a.id_batalyon
    WHERE
        a.id_batalyon = '$id_batalyon'
        AND a.is_deleted = '0'
        AND a.id_aspek = '$id_aspek'";

        $where = ["b.mata_pelajaran"];
        $orderby = "";

        parent::_loadSelect2orderby($data, $query, $where, $orderby);
        // var_dump($this->db->getLastQuery());
    }

    function matapelajaranbybatalyonnoaspek_select_get()
    {
        $data = $this->request->getGet();
        $id_batalyon = $data["id_batalyon"];


        if (isset($data['type_code']) == 0) {
            $query = "SELECT
		        a.id_mata_pelajaran as id,
		        b.kode_mk,
		        b.mata_pelajaran as text,
		        d.semester
		    from t_program_studi_mata_pelajaran a
		    inner join m_mata_pelajaran b
		        on b.id = a.id_mata_pelajaran
		    inner join m_sm_batalyon c
		        on a.id_semester = c.id_semester
		        and c.id = a.id_batalyon
		    inner join m_semester d
		        on a.id_semester = d.id
		        where a.id_batalyon = '" . $id_batalyon . "'
		        and a.is_deleted='0' ";
        } else {
            if ($data['type_code'] == 'gdk') {
                $id_pendidik = $data["id_pendidik"];
                $query = "SELECT
				        a.id_mata_pelajaran as id,
				        b.kode_mk,
				        b.mata_pelajaran as text,
				        d.semester
				    from t_program_studi_mata_pelajaran a
				    inner join m_mata_pelajaran b
				        on b.id = a.id_mata_pelajaran
				    inner join m_sm_batalyon c
				        on a.id_semester = c.id_semester
				        and c.id = a.id_batalyon
				    inner join m_semester d
				        on a.id_semester = d.id
				    join (SELECT * FROM t_pendidik_mata_pelajaran a1 where a1.id_pendidik='" . $id_pendidik . "' group by a1.id_batalyon, a1.id_mata_pelajaran) e on a.id_batalyon=e.id_batalyon and a.id_mata_pelajaran=e.id_mata_pelajaran
				    where a.id_batalyon = '" . $id_batalyon . "'
				        and a.is_deleted='0' ";
            } else {
                $query = "SELECT
				        a.id_mata_pelajaran as id,
				        b.kode_mk,
				        b.mata_pelajaran as text,
				        d.semester
				    from t_program_studi_mata_pelajaran a
				    inner join m_mata_pelajaran b
				        on b.id = a.id_mata_pelajaran
				    inner join m_sm_batalyon c
				        on a.id_semester = c.id_semester
				        and c.id = a.id_batalyon
				    inner join m_semester d
				        on a.id_semester = d.id
				        where a.id_batalyon = '" . $id_batalyon . "'
				        and a.is_deleted='0' ";
            }
        }
        $where = ["b.mata_pelajaran"];
        $orderby = "";

        // echo json_encode($data);
        parent::_loadSelect2orderby($data, $query, $where, $orderby);
    }

    function matapelajaranbyidsmt_select_get()
    {
        $data = $this->request->getGet();
        $id_semester = $data["id_semester"];
        $query = "select 
            a.id_mata_pelajaran as id,
            b.kode_mk,
            b.mata_pelajaran as text
        from t_program_studi_mata_pelajaran a
        inner join m_mata_pelajaran b
            on a.id_mata_pelajaran = b.id
        left join m_program_studi c on a.id_program_studi=c.id
        left join m_config d on d.id=1";
        $where = ["b.mata_pelajaran"];
        $orderby = "where a.id_semester = '" . $id_semester . "'
        and c.tahun_ajaran=d.nilai and a.is_deleted=0;";

        parent::_loadSelect2orderby($data, $query, $where, $orderby);
    }

    function kompibybatalyon_select_get()
    {
        $data = $this->request->getGet();
        $id_batalyon = $data["id_batalyon"];
        $query = " select a.id, a.kompi as text, a.id_batalyon, a.id_user_pendidik  from m_sm_kompi a where a.is_deleted = 0";
        $where = ["a.kompi"];
        $orderby = "and a.id_batalyon = '" . $id_batalyon . "'";
        parent::_loadSelect2orderby($data, $query, $where, $orderby);
    }

    function peletonbykompi_select_get()
    {
        $data = $this->request->getGet();
        $id_kompi = $data["id_kompi"];
        $query = " select a.id, a.peleton as text, a.id_kompi, a.id_user_pendidik  from m_sm_peleton a where a.is_deleted = 0";
        $where = ["a.peleton"];
        $orderby = "and a.id_kompi = '" . $id_kompi . "'";
        parent::_loadSelect2orderby($data, $query, $where, $orderby);
    }

    function pertemuan_select_get()
    {
        $data = $this->request->getGet();
        $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_batalyon = $data["id_batalyon"];
        $query = "select a.id, a.judul as text from t_bahan_ajar a
        left join m_sm_batalyon b on a.id_batalyon = b.id  
        where a.id_mata_pelajaran = '" . $id_mata_pelajaran . "' and a.id_batalyon = '" . $id_batalyon . "' and a.is_deleted = '0'";
        $where = ["judul"];

        parent::_loadSelect2($data, $query, $where);
    }

    function pertemuanbykelas_select_get()
    {
        $data = $this->request->getGet();
        $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_batalyon = $data["id_batalyon"];
        $id_kelompok = $data["id_kelompok"];

        if (isset($data['type_code'])) {

            if ($data['type_code'] == 'gdk') {
                $id_pendidik = $data["id_pendidik"];
                $where = " and a.id_batalyon='" . $id_batalyon . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "'  and a.id_user_pendidik='" . $id_pendidik . "' and a.id_kelompok_taruna='" . $id_kelompok . "' ";
            } else {
                $where = " and a.id_batalyon='" . $id_batalyon . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "' and a.id_kelompok_taruna='" . $id_kelompok . "' ";
            }
        } else {
            $where = " and a.id_batalyon='" . $id_batalyon . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "' and a.id_kelompok_taruna='" . $id_kelompok . "' ";
        }


        $query = "SELECT b.id, b.judul as text from t_jadwal a 
                    join t_bahan_ajar b on a.id_bahan_ajar=b.id
                    join m_kelompok c on a.id_kelompok_taruna=c.id
                    where a.is_deleted='0' $where
                    ";
        $where = ["judul"];

        parent::_loadSelect2($data, $query, $where);
    }

    function alasantidakhadir_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.alasan as text, a.alasan as id, a.id as ori_id from m_alasan_tidak_hadir a where a.is_deleted = 0";
        $where = ["a.alasan"];

        parent::_loadSelect2($data, $query, $where);
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
                                        'id_jenis_ujian' , z.id_jenis_ujian
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
                        b.id_jenis_ujian
                       
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
                        and b.is_ujian='1'
                        and b.is_deleted = 0
                        and d.id is null
                    group by a.id_kelompok, a.id_batalyon,  b.id) z
                    group by id_kelompok
                    ";

        $data = $this->db->query($query)->getResult();

        $response = ['success' => true, 'data' => $data];

        echo json_encode($response);
    }

    public function matapelajaranbysemester_get($id_semester)
    {
        $query = "select 
        a.id_mata_pelajaran as id,
        b.kode_mk,
        b.mata_pelajaran,
        a.nilai as sks,
        a.satuan,
                a.id_aspek,
                e.aspek
    from t_program_studi_mata_pelajaran a
    inner join m_mata_pelajaran b
        on a.id_mata_pelajaran = b.id
    left join m_config d on d.id=1 
            left join m_aspek e on a.id_aspek = e.id
    where a.id_semester = '" . $id_semester . "'
    and a.tahun_ajaran=d.nilai and a.is_deleted=0 order by e.aspek ASC";


        // parent::_loadlist($data, $query);
        $rs = $this->webModel->base_load_list($query);
        return $rs;
    }

    public function tarunabysemester_get($id_semester)
    {
        $query = "        select
        b.id_m_user as id_user_taruna,
        '" . $id_semester . "' as id_semester,
        b.namataruna,
        b.noaklong,
        c.kelompok
        from m_user_taruna b
           inner join m_kelompok c
        on c.id = b.id_kelompok
        left join m_sm_batalyon d on b.id_batalyon = d.id       
        where d.id_semester = '" . $id_semester . "'";

        // parent::_loadlist($data, $query);
        $rs = $this->webModel->base_load_list($query);
        return $rs;
    }

    function syaratnilaijasmani_get()
    {
        $query = "SELECT a.id as _id, a.syarat as label FROM
        m_syarat_penilaian_jasmani a where a.is_deleted = 0;";

        $rs = $this->webModel->base_load_list($query);
        $response = ['success' => true, 'data' => $rs];

        echo json_encode($response);
    }

    function batalyonsmt_select_get()
    {
        $userid = $this->request->getPost('id');
        // var_dump($userid);
        $data = $this->request->getGet();
        // print_r($data);
        // die;

        if (isset($data['type_code']) == 1 && $data['type_code'] == 'gdk') {
            $query = "SELECT  * from ( 
				SELECT a.id , concat(a.batalyon, ' ( ' , a.tahun_masuk , ' ) - ', b.semester) as text , a.is_deleted ,c.id_batalyon, d.id_pendidik
				from m_sm_batalyon a 
				left join m_semester b on a.id_semester = b.id 
                LEFT JOIN t_pendidik_mata_pelajaran d ON d.id = a.id
				join (SELECT * FROM t_pendidik_mata_pelajaran a1 where a1.id_pendidik='" . $data['id_pendidik'] . "' group by a1.id_batalyon) c on a.id=c.id_batalyon
				where a.is_deleted='0' and b.id < 9 ) a1 
	        where a1.is_deleted='0' ";
        } else {
            $query = "SELECT  * from ( 
				SELECT a.id , concat(a.batalyon, ' ( ' , a.tahun_masuk , ' ) - ', b.semester) as text , a.is_deleted ,c.id_batalyon, d.id_pendidik
				from m_sm_batalyon a 
				left join m_semester b on a.id_semester = b.id 
                LEFT JOIN t_pendidik_mata_pelajaran d ON d.id = a.id
				join (SELECT * FROM t_pendidik_mata_pelajaran a1 group by a1.id_batalyon) c on a.id=c.id_batalyon
				where a.is_deleted='0' and b.id < 9 ) a1 
	        where a1.is_deleted='0' ";
        }
        $where = ["a1.text"];

        parent::_loadSelect2($data, $query, $where);

        // echo json_encode($data);
    }

    function taruna_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT * from (select a.id_m_user as id, concat(a.nik, ' | ' , a.namataruna) as text, a.nik, a.namataruna, a.id_batalyon, a.is_deleted from m_user_taruna a where a.is_deleted = 0) a where a.is_deleted='0' ";
        $where = ["a.text"];

        parent::_loadSelect2($data, $query, $where);
    }

    function datataruna_list_get()
    {
        $data = $this->request->getGet();

        $query = "SELECT 
                    b.judul,
                    b.pertemuan_ke,
                    CASE
                        WHEN d.is_absen=1 THEN 'Hadir'
                        WHEN d.is_absen=0 THEN 'Tidak Hadir'
                        WHEN d.is_absen is null THEN 'Belum Tersedia'
                        ELSE 'n/a'
                    END as is_absen,
                    ifnull(d.keterangan, '') as keterangan
                from m_user_taruna a 
                left join (select * from t_bahan_ajar where is_deleted = 0) b
                    on a.id_semester = b.id_semester
                    and a.id_batalyon = b.id_batalyon
                left join (select * from t_jadwal where is_deleted = 0) c
                    on b.id = c.id_bahan_ajar
                    and a.id_kelompok = c.id_kelompok_taruna
                left join t_absensi d
                    on d.id_taruna = a.id_m_user
                    and d.id_jadwal = c.id
                where a.id_m_user = '" . $data['id_taruna'] . "'
                    and b.id_mata_pelajaran = '" . $data['id'] . "' ";

        $query = "SELECT b.judul,
                    -- b.pertemuan_ke,
                    if(b.pertemuan_ke='0' , if(b.id_jenis_ujian=1, 'UTS', if(b.id_jenis_ujian='2', 'UAS' , '') ) , b.pertemuan_ke) as pertemuan_ke,
                    CASE
                        WHEN d.is_absen=1 THEN 'Hadir'
                        WHEN d.is_absen=0 THEN 'Tidak Hadir'
                        WHEN d.is_absen is null THEN 'Belum Tersedia'
                        ELSE 'n/a'
                    END as is_absen,
                    ifnull(d.keterangan, '') as keterangan, e.batalyon, f.semester,CONCAT(g.`kode_mk`,' | ', g.mata_pelajaran) AS mata_pelajaran, i.namagadik, ifnull(DATE_FORMAT(c.absen_at, '%d/%m/%Y %H:%i'), '') as absen_at
                FROM m_user_taruna a 
                LEFT JOIN (SELECT * FROM t_bahan_ajar WHERE is_deleted = 0) b
                    ON a.id_semester = b.id_semester
                    AND a.id_batalyon = b.id_batalyon
                LEFT JOIN (SELECT * FROM t_jadwal WHERE is_deleted = 0) c
                    ON b.id = c.id_bahan_ajar
                    AND a.id_kelompok = c.id_kelompok_taruna
                LEFT JOIN t_absensi d
                    ON d.id_taruna = a.id_m_user
                    AND d.id_jadwal = c.id
                LEFT JOIN m_sm_batalyon e ON a.id_batalyon=e.id
                LEFT JOIN m_semester f ON e.id_semester=f.id
                LEFT JOIN m_mata_pelajaran g ON b.id_mata_pelajaran=g.id
                LEFT JOIN (SELECT * FROM `t_pendidik_mata_pelajaran` WHERE is_ketua_tim='1' AND is_deleted='0' ) h ON b.id_mata_pelajaran=h.id_mata_pelajaran AND e.id=h.id_batalyon
                LEFT JOIN m_user_pendidik i ON h.id_pendidik=i.id
                WHERE a.id_m_user = '" . $data['id_taruna'] . "'
                    and b.id_mata_pelajaran = '" . $data['id'] . "' ";
        // GROUP BY b.id_mata_pelajaran ";

        // parent::_loadlist($data, $query);
        parent::_editbatch('t_bahan_ajar', $data, null, $query);



        // $rs['data'] = $this->db->query($query)->getResult();

        // $rs['semester'] = $this->db->query("SELECT a.semester , b.batalyon from m_semester a
        //                                     left join m_sm_batalyon b on a.id=b.id_semester 
        //                                     where b.id='" . $id_batalyon . "' ")->getRow()->semester;
        // $rs['batalyon'] = $this->db->query("SELECT a.semester , b.batalyon from m_semester a
        //                                     left join m_sm_batalyon b on a.id=b.id_semester 
        //                                     where b.id='" . $id_batalyon . "' ")->getRow()->batalyon;
        // $rs['matapelajaran'] = $this->db->query("SELECT a.mata_pelajaran from m_mata_pelajaran a where a.id='" . $id_mata_pelajaran . "' ")->getRow()->mata_pelajaran;
        // $rs['kelompok'] = $this->db->query("SELECT kelompok FROM `m_kelompok` a WHERE a.id='" . $id_kelompok . "' ")->getRow()->kelompok;

        // $rs['nama_file'] = "Nilai Jasmani ".$rs['batalyon'].'-'.$rs['semester'].'-'.$rs['matapelajaran'];

        // echo json_encode($rs);
    }

    function nilaipelajaran_list_get()
    {
        $data = $this->request->getGet();

        if ($data['aspek'] == 1) {
            $query = "SELECT 
                        e.batalyon, f.semester,CONCAT(g.`kode_mk`,' | ', g.mata_pelajaran) AS mata_pelajaran,
                        b.uts_ljk, b.uts_esai, b.uas_ljk, b.uas_esai, b.her_ljk, b.her_esai, b.proses_ajar, b.tugas , b.her
                    FROM m_user_taruna a 
                    LEFT JOIN t_penilaian_aspek_pengetahuan b ON a.id_m_user=b.id_user_taruna AND a.id_semester=b.id_semester AND a.id_batalyon=b.id_batalyon
                    
                    LEFT JOIN m_sm_batalyon e ON a.id_batalyon=e.id
                    LEFT JOIN m_semester f ON e.id_semester=f.id
                    LEFT JOIN m_mata_pelajaran g ON b.id_mata_pelajaran=g.id
                    WHERE a.id_m_user = '" . $data['id_taruna'] . "'
                        and b.id_mata_pelajaran = '" . $data['id'] . "' ";
        } else if ($data['aspek'] == 2) {
            $query = "SELECT 
                        e.batalyon, f.semester,CONCAT(g.`kode_mk`,' | ', g.mata_pelajaran) AS mata_pelajaran,
                        b.uts, b.uas, b.her, b.proses_pelatihan, b.produk_pelatihan, b.nilai_unjuk_kerja
                        
                    FROM m_user_taruna a 
                    LEFT JOIN t_penilaian_aspek_keterampilan b ON a.id_m_user=b.id_user_taruna AND a.id_semester=b.id_semester AND a.id_batalyon=b.id_batalyon
                    
                    LEFT JOIN m_sm_batalyon e ON a.id_batalyon=e.id
                    LEFT JOIN m_semester f ON e.id_semester=f.id
                    LEFT JOIN m_mata_pelajaran g ON b.id_mata_pelajaran=g.id
                    WHERE a.id_m_user = '" . $data['id_taruna'] . "'
                        and b.id_mata_pelajaran = '" . $data['id'] . "' ";
        }



        parent::_editbatch('t_bahan_ajar', $data, null, $query);
    }

    function nilaipelajarantugas_list_get()
    {
        $data = $this->request->getGet();

        $query = "SELECT t.nilai, t.log_nilai,  a.judul, a.deskripsi, a.waktu_pengumpulan, DATE_FORMAT(a.waktu_pengumpulan, '%d/%m/%y %H:%i') AS waktu, 
                    d.id AS id_mata_pelajaran 
                    FROM t_nilai_tugas t
                    LEFT JOIN t_jadwal_tugas a ON t.id_jadwal_tugas = a.id
                    LEFT JOIN t_jadwal b ON a.id_jadwal=b.id
                    LEFT JOIN t_bahan_ajar c ON b.id_bahan_ajar=c.id
                    LEFT JOIN m_mata_pelajaran d ON c.id_mata_pelajaran=d.id
                    WHERE a.is_deleted='0' AND t.id_user_taruna = '" . $data['id_taruna'] . "' AND d.id='" . $data['id'] . "'
                     ";

        parent::_editbatch('t_bahan_ajar', $data, null, $query);
    }

    function materibymapel_list_get()
    {
        $data = $this->request->getGet();

        $cekusertaruna = $this->db->query("SELECT id_batalyon FROM m_user_taruna where id_m_user='" . $data['id_taruna'] . "' ")->getRow();

        $query = "SELECT c.mata_pelajaran,
                                        a.pertemuan_ke,
                                        CONCAT('http://devel.nginovasi.id/akpol-api/',a.lokasi_file) as lokasi_file,
                                        b.tipe_file,
                                        b.icon_file,
                                        CONCAT('File at Session ',a.pertemuan_ke,' - ',c.mata_pelajaran, ' ~ ', 'Upload by ',e.namagadik) as info_file,
                                        CONCAT(sf_formatdate_ID(a.created_at),' • ',DATE_FORMAT(a.created_at,'%H:%i'), ' WIB') as tanggal_detail,
                                        a.created_at as date_insert
                                from t_file_materi a
                                LEFT JOIN m_tipe_file b on a.id_tipe_file=b.id
                                LEFT JOIN m_mata_pelajaran c on a.id_mata_pelajaran=c.id
                                LEFT JOIN m_sm_batalyon d on a.id_batalyon=d.id
                                LEFT JOIN m_user_pendidik e on a.id_user_pendidik=e.id_m_user
                                where a.id_mata_pelajaran='" . $data['id'] . "' and a.id_batalyon='" . $cekusertaruna->id_batalyon . "' and a.is_deleted=0 order by a.pertemuan_ke ";

        parent::_editbatch('t_file_materi', $data, null, $query);
    }



    // end ajax ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


    // controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    // function cetakkhs_download2()
    // {
    //     $data = json_decode($this->request->getPost('param'), true);
    //     $id_m_user = $data['id_taruna'];
    //     $id_semester = $data['id_semester'];

    //     // $rs['nilai_karakter'] = $this->db->query("SELECT nilai_akhir FROM t_penilaian_aspek_karakter where id_user_taruna='".$id_m_user."' and id_semester='".$id_semester."' ")->getRow();
    //     // $rs['nilai_jasmani'] = $this->db->query("SELECT nilai_akhir FROM t_penilaian_aspek_jasmani where id_user_taruna='".$id_m_user."' and id_semester='".$id_semester."' ")->getRow();
    //     // $rs['nilai_kesehatan'] = $this->db->query("SELECT nilai_akhir FROM t_penilaian_aspek_kesehatan where id_user_taruna='".$id_m_user."' and id_semester='".$id_semester."' ")->getRow();

    //     $query = "SELECT a.namataruna, a.noaklong, max(a.semester) as semester,
    //             group_concat(distinct if(a.aspek='Pengetahuan',a.nilai,null)) as pengetahuan,
    //             group_concat(distinct if(a.aspek='Keterampilan',a.nilai,null)) as keterampilan,
    //             group_concat(distinct if(a.aspek='Karakter',a.nilai,null)) as karakter,
    //             group_concat(distinct if(a.aspek='Kesehatan',a.nilai,null)) as kesehatan,
    //             group_concat(distinct if(a.aspek='Jasmani',a.nilai,null)) as jasmani,
    //             sum(if(a.aspek='Pengetahuan',a.jml,0))/sum(if(a.aspek='Pengetahuan',a.pembagi,0)) as rata_pengetahuan,
    //             sum(if(a.aspek='Keterampilan',a.jml,0))/sum(if(a.aspek='Keterampilan',a.pembagi,0)) as rata_keterampilan,
    //             sum(if(a.aspek='Karakter',a.jml,0))/sum(if(a.aspek='Karakter',a.pembagi,0)) as rata_karakter,
    //             sum(if(a.aspek='Kesehatan',a.jml,0))/sum(if(a.aspek='Kesehatan',a.pembagi,0)) as rata_kesehatan,
    //             sum(if(a.aspek='Jasmani',a.jml,0))/sum(if(a.aspek='Jasmani',a.pembagi,0)) as rata_jasmani
    //             from (select rangkuman.namataruna, rangkuman.noaklong, rangkuman.aspek, 
    //             json_arrayagg(json_object('matkul',rangkuman.mata_pelajaran,'nilai',rangkuman.nilai_akhir,'bobot',rangkuman.bobot,'klasifikasi',rangkuman.klasifikasi,'sks',concat(rangkuman.nilai,' ',rangkuman.satuan), 'rata', rata_rata)) as nilai,
    //             sum(rangkuman.nilai_akhir) as jml, count(1) as pembagi, group_concat(distinct rangkuman.semester separator '/') as semester, rangkuman.id_semester
    //             from (select c.namataruna, c.noaklong, a.mata_pelajaran, x.semester, b.id_semester, b.satuan, b.nilai, y.rata_rata,
    //             CASE
    //                 WHEN b.id_aspek = '1' THEN ifnull(d.nilai_akhir,0)
    //                 WHEN b.id_aspek = '2' THEN ifnull(e.nilai_akhir,0)


    //             END as nilai_akhir,
    //             CASE
    //                 WHEN b.id_aspek = '1' THEN ifnull(d.klasifikasi,'')
    //                 WHEN b.id_aspek = '2' THEN ifnull(e.klasifikasi,'')
    //             END as klasifikasi,
    //             CASE
    //                 WHEN b.id_aspek = '1' THEN ifnull(d.bobot,0)
    //                 WHEN b.id_aspek = '2' THEN ifnull(e.bobot,0)
    //             END as bobot,
    //             z.aspek from m_mata_pelajaran a
    //             left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
    //             left join m_user_taruna c on c.id_batalyon = b.id_batalyon
    //             left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
    //             left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0


    //             left join m_aspek z on z.id = b.id_aspek
    //             left join m_semester x on x.id = b.id_semester
    //             left join (
    //                 select rangkuman.semester, rangkuman.id as id_mata_pelajaran, mata_pelajaran, ifnull(avg(nilai_akhir),0) as rata_rata, id_batalyon from (
    //                 select a.id, c.namataruna, c.noaklong, a.mata_pelajaran, x.semester, b.id_semester, b.id_batalyon,
    //                 CASE
    //                     WHEN b.id_aspek = '1' THEN ifnull(d.nilai_akhir,0)
    //                     WHEN b.id_aspek = '2' THEN ifnull(e.nilai_akhir,0)
    //                 END as nilai_akhir from m_mata_pelajaran a
    //                 left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
    //                 left join m_user_taruna c on c.id_batalyon = b.id_batalyon
    //                 left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
    //                 left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
    //                 left join m_semester x on x.id = b.id_semester
    //                 where a.is_deleted = 0
    //                 and b.id_semester = '" . $id_semester . "' and b.id_aspek in ('1', '2')
    //                 group by a.id, c.noaklong, b.id_batalyon) rangkuman
    //                 group by rangkuman.id, rangkuman.id_batalyon
    //             ) y on a.id = y.id_mata_pelajaran and y.id_batalyon = c.id_batalyon
    //             where a.is_deleted = 0
    //             and c.id_m_user = '" . $id_m_user . "'
    //             and b.id_semester = '" . $id_semester . "'
    //             group by a.id, c.noaklong, b.id_semester) rangkuman
    //             group by rangkuman.aspek, rangkuman.id_semester) a
    //             group by a.id_semester";


    //     $datataruna = "SELECT a.noaklong, a.namataruna , CONCAT(b.batalyon , ' ( ' ,b.`tahun_masuk` ,' )') AS batalyon , c.semester , e.kelompok, g.tingkatan , b.angkatan, f.semester , ROUND(b.tahun_masuk + ((b.id_semester/2) + MOD(b.id_semester/2,1)-1))  AS tahun_ajaran
    //                     FROM m_user_taruna a 
    //                     LEFT JOIN m_sm_batalyon b ON a.id_batalyon=b.id
    //                     LEFT JOIN m_semester c ON b.`id_semester`=c.id
    //                     LEFT JOIN m_kelompok e ON a.`id_kelompok`=e.id
    //                     LEFT JOIN m_semester f ON b.id_semester=f.id
    //                     LEFT JOIN m_tingkatan g ON f.id_tingkat=g.id
    //                     where a.id_m_user='" . $id_m_user . "'";
    //     $rs['datataruna'] = $this->db->query($datataruna)->getRow();

    //     $datasemester = "SELECT
    //                                 a.semester,
    //                                 b.tingkatan
    //                             FROM m_semester a 
    //                             LEFT JOIN m_tingkatan b
    //                                 ON a.id_tingkat = b.id
    //                             WHERE a.id ='" . $id_semester . "' ";
    //     $rs['datasemester'] = $this->db->query($datasemester)->getRow();

    //     $rs['data'] = $this->db->query($query)->getResult();

    //     $rs['nama_file'] = $rs['datataruna']->namataruna . " - KHS";

    //     echo json_encode($rs);
    // }

    function cetakkhs_download()
    {
        $data = json_decode($this->request->getGetPost('param'), true);

        $id_m_user = $data['id_taruna'];
        $id_semester = $data['id_semester'];

        $rs['nilai_karakter'] = $this->db->query("SELECT nilai_akhir FROM t_penilaian_aspek_karakter where id_user_taruna='" . $id_m_user . "' and id_semester='" . $id_semester . "' ")->getRow();
        $rs['nilai_jasmani'] = $this->db->query("SELECT nilai_akhir FROM t_penilaian_aspek_jasmani where id_user_taruna='" . $id_m_user . "' and id_semester='" . $id_semester . "' ")->getRow();
        $rs['nilai_kesehatan'] = $this->db->query("SELECT nilai_akhir FROM t_penilaian_aspek_kesehatan where id_user_taruna='" . $id_m_user . "' and id_semester='" . $id_semester . "' ")->getRow();

        $setsession = $this->db->query('SET SESSION group_concat_max_len = 99999');
        $query = "SELECT a.namataruna, a.noaklong, max(a.semester) as semester,
        group_concat(distinct if(a.aspek = 'Pengetahuan', a.nilai, null)) as pengetahuan,
        group_concat(distinct if(a.aspek = 'Keterampilan', a.nilai, null)) as keterampilan,
        group_concat(distinct if(a.aspek = 'Karakter', a.nilai, null)) as karakter,
        group_concat(distinct if(a.aspek = 'Kesehatan', a.nilai, null)) as kesehatan,
        group_concat(distinct if(a.aspek = 'Jasmani', a.nilai, null)) as jasmani,
        sum(if(a.aspek = 'Pengetahuan', a.jml, 0)) / sum(if(a.aspek = 'Pengetahuan', a.pembagi, 0)) as rata_pengetahuan,
        sum(if(a.aspek = 'Keterampilan', a.jml, 0)) / sum(if(a.aspek = 'Keterampilan', a.pembagi, 0)) as rata_keterampilan,
        sum(if(a.aspek = 'Karakter', a.jml, 0)) / sum(if(a.aspek = 'Karakter', a.pembagi, 0)) as rata_karakter,
        sum(if(a.aspek = 'Kesehatan', a.jml, 0)) / sum(if(a.aspek = 'Kesehatan', a.pembagi, 0)) as rata_kesehatan,
        sum(if(a.aspek = 'Jasmani', a.jml, 0)) / sum(if(a.aspek = 'Jasmani', a.pembagi, 0)) as rata_jasmani
    from
        (
            select rangkuman.namataruna, rangkuman.noaklong, rangkuman.aspek,
                json_arrayagg(
                    json_object(
                        'matkul', rangkuman.mata_pelajaran,
                        'nilai', rangkuman.nilai_akhir,
                        'bobot', rangkuman.bobot,
                        'klasifikasi', rangkuman.klasifikasi,
                        'sks', concat(rangkuman.nilai, ' ', rangkuman.satuan),
                        'rata', rata_rata
                    )
                ) as nilai,
                sum(rangkuman.nilai_akhir) as jml,
                count(1) as pembagi,
                group_concat(distinct rangkuman.semester separator '/') as semester,
                rangkuman.id_semester
            from
                (
                    select c.namataruna, c.noaklong, a.mata_pelajaran, x.semester, b.id_semester, b.satuan, b.nilai, y.rata_rata,
                        CASE
                            WHEN b.id_aspek = '1' THEN ifnull(d.nilai_akhir, 0)
                            WHEN b.id_aspek = '2' THEN ifnull(e.nilai_akhir, 0)
                        END as nilai_akhir,
                        CASE
                            WHEN b.id_aspek = '1' THEN ifnull(d.klasifikasi, '')
                            WHEN b.id_aspek = '2' THEN ifnull(e.klasifikasi, '')
                        END as klasifikasi,
                        CASE
                            WHEN b.id_aspek = '1' THEN ifnull(d.bobot, 0)
                            WHEN b.id_aspek = '2' THEN ifnull(e.bobot, 0)
                        END as bobot, z.aspek
                    FROM m_mata_pelajaran a
                        left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
                        left join m_user_taruna c on c.id_batalyon = b.id_batalyon
                        left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
                        left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
                        left join m_aspek z on z.id = b.id_aspek
                        left join m_semester x on x.id = b.id_semester
                        left join (
                            select rangkuman.semester, rangkuman.id as id_mata_pelajaran, mata_pelajaran, ifnull(avg(nilai_akhir), 0) as rata_rata, id_batalyon
                            from
                                (
                                    select a.id, c.namataruna, c.noaklong, a.mata_pelajaran, x.semester, b.id_semester, b.id_batalyon,
                                        CASE
                                            WHEN b.id_aspek = '1' THEN ifnull(d.nilai_akhir, 0)
                                            WHEN b.id_aspek = '2' THEN ifnull(e.nilai_akhir, 0)
                                        END as nilai_akhir
                                    from m_mata_pelajaran a
                                    left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
                                    left join m_user_taruna c on c.id_batalyon = b.id_batalyon
                                    left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
                                    left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
                                    left join m_semester x on x.id = b.id_semester
                                    where a.is_deleted = 0 and b.id_semester = '" . $id_semester . "' and b.id_aspek in ('1', '2')
                                    group by a.id, c.noaklong, b.id_batalyon
                                ) rangkuman
                            group by rangkuman.id, rangkuman.id_batalyon
                        ) y on a.id = y.id_mata_pelajaran and y.id_batalyon = c.id_batalyon
                    where a.is_deleted = 0 and c.id_m_user = '" . $id_m_user . "' and b.id_semester = '" . $id_semester . "'
                    group by a.id, c.noaklong, b.id_semester
                ) rangkuman
            group by rangkuman.aspek, rangkuman.id_semester
        ) a
    group by a.id_semester";


        $datataruna = "SELECT a.noaklong, a.namataruna , CONCAT(b.batalyon , ' ( ' ,b.`tahun_masuk` ,' )') AS batalyon , c.semester , e.kelompok, g.tingkatan , b.angkatan, f.semester , ROUND(b.tahun_masuk + ((b.id_semester/2) + MOD(b.id_semester/2,1)-1))  AS tahun_ajaran
                        FROM m_user_taruna a 
                        LEFT JOIN m_sm_batalyon b ON a.id_batalyon=b.id
                        LEFT JOIN m_semester c ON b.`id_semester`=c.id
                        LEFT JOIN m_kelompok e ON a.`id_kelompok`=e.id
                        LEFT JOIN m_semester f ON b.id_semester=f.id
                        LEFT JOIN m_tingkatan g ON f.id_tingkat=g.id
                        where a.id_m_user='" . $id_m_user . "'";
        $rs['datataruna'] = $this->db->query($datataruna)->getRow();

        $datasemester = "SELECT
                                    a.semester,
                                    b.tingkatan
                                FROM m_semester a 
                                LEFT JOIN m_tingkatan b
                                    ON a.id_tingkat = b.id
                                WHERE a.id ='" . $id_semester . "' ";
        $rs['datasemester'] = $this->db->query($datasemester)->getRow();

        $rs['data'] = $this->db->query($query)->getResult();

        $rs['nama_file'] = $rs['datataruna']->namataruna . " - KHS";

        // $rs['query'] = $query;

        echo json_encode($rs);
    }

    function cetakkrs_download()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $id_m_user = $data['id_taruna'];
        $id_semester = $data['id_semester'];
        $query = "SELECT e.kode_mk ,d.satuan, e.mata_pelajaran , d.nilai , d.satuan, g.namagadik, h.aspek, f.is_deleted=''
                    FROM m_user_taruna a
                    INNER JOIN m_sm_batalyon c ON a.`id_batalyon`=c.`id` and c.is_deleted='0'
                    INNER JOIN t_program_studi_mata_pelajaran d ON d.id_batalyon=c.id and d.is_deleted='0'
                    
                    INNER JOIN m_mata_pelajaran e ON e.id=d.id_mata_pelajaran and e.is_deleted='0'
                    
                    INNER JOIN t_pendidik_mata_pelajaran f ON e.id=f.id_mata_pelajaran and f.id_batalyon=a.id_batalyon AND f.is_ketua_tim='1' and f.is_deleted='0'
                    INNER JOIN m_user_pendidik g ON f.id_pendidik=g.id_m_user and g.is_deleted='0'
                    INNER JOIN m_aspek h ON d.id_aspek=h.id and h.is_deleted='0'
                    where a.id_m_user='" . $id_m_user . "'
                    AND d.id_semester='" . $id_semester . "'
                    AND a.is_deleted='0'
                     ";


        $datataruna = "SELECT a.noaklong, a.namataruna , CONCAT(b.batalyon , ' ( ' ,b.`tahun_masuk` ,' )') AS batalyon , c.semester , e.kelompok, g.tingkatan , b.angkatan, f.semester , ROUND(b.tahun_masuk + ((b.id_semester/2) + MOD(b.id_semester/2,1)-1))  AS tahun_ajaran
                        FROM m_user_taruna a 
                        LEFT JOIN m_sm_batalyon b ON a.id_batalyon=b.id
                        LEFT JOIN m_semester c ON b.`id_semester`=c.id
                        LEFT JOIN m_kelompok e ON a.`id_kelompok`=e.id
                        LEFT JOIN m_semester f ON b.id_semester=f.id
                        LEFT JOIN m_tingkatan g ON f.id_tingkat=g.id
                        where a.id_m_user='" . $id_m_user . "'";

        $datasemester = "SELECT
                            a.semester,
                            b.tingkatan
                        FROM m_semester a 
                        LEFT JOIN m_tingkatan b
                            ON a.id_tingkat = b.id
                        WHERE a.id ='" . $id_semester . "' ";

        $rs['datataruna'] = $this->db->query($datataruna)->getRow();
        $rs['datasemester'] = $this->db->query($datasemester)->getRow();

        $rs['data'] = $this->db->query($query)->getResult();
        $rs['nama_file'] = $rs['datataruna']->namataruna . " - KRS";

        echo json_encode($rs);
    }

    function cetaktranskrip_download()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $id_m_user = $data['id_taruna'];
        $id_tingkat = $data['id_tingkat'];

        $semester = "SELECT * FROM m_semester WHERE id_tingkat = '" . $id_tingkat . "' ";

        $query = "SELECT aspek, sum(if(ganjil_genap='ganjil',rata,0)) as ganjil, sum(if(ganjil_genap='genap',rata,0)) as genap,
            sum(rata)/2 as rata_rata from (
            select namataruna, noaklong, sum(nilai_akhir)/count(1) as rata, id_semester, aspek, ganjil_genap from (
            (select c.namataruna, c.noaklong, a.mata_pelajaran, x.semester, b.id_semester,
            CASE
                WHEN b.id_aspek = '1' THEN ifnull(d.nilai_akhir,0)
                WHEN b.id_aspek = '2' THEN ifnull(e.nilai_akhir,0)               
            END as nilai_akhir, x.ganjil_genap,
            z.aspek from m_mata_pelajaran a
            left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
            left join m_user_taruna c on c.id_batalyon = b.id_batalyon
            left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
            left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
            inner join m_aspek z on z.id = b.id_aspek AND z.aspek!='Karakter' AND z.aspek!='Jasmani' AND z.aspek!='Kesehatan'  AND z.aspek!='Kokurikuler'
            left join m_semester x on x.id = b.id_semester
            where a.is_deleted = 0
            and x.id_tingkat = '" . $id_tingkat . "'
            and c.id_m_user = '" . $id_m_user . "'
            group by a.id, c.noaklong, b.id_semester)
            UNION ALL (
                SELECT 'tar' AS namataruna, 'asd' AS noaklong, '' AS mata_pelajaran, b.semester, b.id AS id_semester,IFNULL(a.nilai_akhir,0) AS nilai_akhir, b.ganjil_genap,'Jasmani' AS aspek 
                FROM t_penilaian_aspek_jasmani a 
                LEFT JOIN m_semester b ON a.id_semester=b.id AND b.`is_deleted`='0'
                WHERE a.`is_deleted`='0'
                AND b.id_tingkat = '" . $id_tingkat . "'
                AND a.id_user_taruna = '" . $id_m_user . "')
            
            UNION ALL (
                SELECT 'tar' AS namataruna, 'asd' AS noaklong, '' AS mata_pelajaran, b.semester, b.id AS id_semester,IFNULL(a.nilai_akhir,0) AS nilai_akhir, b.ganjil_genap,'Kesehatan' AS aspek 
                FROM t_penilaian_aspek_kesehatan a 
                LEFT JOIN m_semester b ON a.id_semester=b.id AND b.`is_deleted`='0'
                WHERE a.`is_deleted`='0'
                AND b.id_tingkat = '" . $id_tingkat . "'
                AND a.id_user_taruna = '" . $id_m_user . "')
             
            UNION ALL (
                SELECT 'tar' AS namataruna, 'asd' AS noaklong, '' AS mata_pelajaran, b.semester, b.id AS id_semester,IFNULL(a.nilai_akhir,0) AS nilai_akhir, b.ganjil_genap,'Karakter' AS aspek 
                FROM t_penilaian_aspek_karakter a 
                LEFT JOIN m_semester b ON a.id_semester=b.id AND b.`is_deleted`='0'
                WHERE a.`is_deleted`='0'
                AND b.id_tingkat = '" . $id_tingkat . "'
                AND a.id_user_taruna = '" . $id_m_user . "')
                
            ) rapor
            group by rapor.aspek, rapor.id_semester) rangkuman
            group by aspek";

        $datataruna = "SELECT a.id_m_user, a.id_batalyon, a.noaklong, a.namataruna , CONCAT(b.batalyon , ' ( ' ,b.`tahun_masuk` ,' )') AS batalyon , c.semester , e.kelompok, g.tingkatan , b.angkatan, f.semester , ROUND(b.tahun_masuk + ((b.id_semester/2) + MOD(b.id_semester/2,1)-1))  AS tahun_ajaran,h.tingkatan as next_tingkat
                        FROM m_user_taruna a 
                        LEFT JOIN m_sm_batalyon b ON a.id_batalyon=b.id
                        LEFT JOIN m_semester c ON b.`id_semester`=c.id
                        LEFT JOIN m_kelompok e ON a.`id_kelompok`=e.id
                        
                        LEFT JOIN m_semester f ON b.id_semester=f.id
            LEFT JOIN m_tingkatan g ON '" . $id_tingkat . "'=g.id
            LEFT JOIN m_tingkatan h ON g.id+1=h .id
                        where a.id_m_user='" . $id_m_user . "'";
        $rs['datataruna'] = $this->db->query($datataruna)->getRow();

        $ranking = "SELECT ranking from (select x.*, @rank := @rank + 1 as ranking from (select 
                        ranking.namataruna, ranking.noaklong,
                        round(sum(((2.5*rata_pengetahuan)+(2.5*rata_keterampilan)+(2.5*rata_karakter)+rata_kesehatan+(1.5*rata_jasmani))/10)/2,2) as rank_aspek
                        from (select a.namataruna, a.noaklong, max(a.semester) as semester,
                        sum(if(a.aspek='Pengetahuan',a.jml,0))/sum(if(a.aspek='Pengetahuan',a.pembagi,0)) as rata_pengetahuan,
                        sum(if(a.aspek='Keterampilan',a.jml,0))/sum(if(a.aspek='Keterampilan',a.pembagi,0)) as rata_keterampilan,
                        sum(if(a.aspek='Karakter',a.jml,0))/sum(if(a.aspek='Karakter',a.pembagi,0)) as rata_karakter,
                        sum(if(a.aspek='Kesehatan',a.jml,0))/sum(if(a.aspek='Kesehatan',a.pembagi,0)) as rata_kesehatan,
                        sum(if(a.aspek='Jasmani',a.jml,0))/sum(if(a.aspek='Jasmani',a.pembagi,0)) as rata_jasmani
                        from (select rangkuman.namataruna, rangkuman.noaklong, rangkuman.aspek, 
                        json_arrayagg(json_object(rangkuman.mata_pelajaran,ifnull(rangkuman.nilai_akhir,0))) as nilai,
                        sum(rangkuman.nilai_akhir) as jml, count(1) as pembagi, group_concat(distinct rangkuman.semester separator '/') as semester, rangkuman.id_semester
                        from (select c.namataruna, c.noaklong, a.mata_pelajaran, x.semester, b.id_semester,
                        CASE
                            WHEN b.id_aspek = '1' THEN d.nilai_akhir
                            WHEN b.id_aspek = '2' THEN e.nilai_akhir
                            WHEN b.id_aspek = '3' THEN f.nilai_akhir
                            WHEN b.id_aspek = '4' THEN g.nilai_akhir
                            WHEN b.id_aspek = '5' THEN h.nilai_akhir                    
                        END as nilai_akhir,
                        z.aspek from m_mata_pelajaran a
                        left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
                        left join m_user_taruna c on c.id_batalyon = b.id_batalyon
                        left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
                        left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
                        left join t_penilaian_aspek_karakter f on f.id_semester = b.id_semester and f.id_user_taruna = c.id_m_user and f.is_deleted = 0
                        left join t_penilaian_aspek_kesehatan g on g.id_semester = b.id_semester and g.id_user_taruna = c.id_m_user and g.is_deleted = 0
                        left join t_penilaian_aspek_jasmani h on h.id_semester = b.id_semester and h.id_user_taruna = c.id_m_user and h.is_deleted = 0
                        left join m_aspek z on z.id = b.id_aspek
                        left join m_semester x on x.id = b.id_semester
                        where a.is_deleted = 0
                        and b.id_batalyon = '" . $rs['datataruna']->id_batalyon . "'
                        and x.id_tingkat = '" . $id_tingkat . "'
                        group by a.id, c.noaklong, b.id_semester) rangkuman
                        group by rangkuman.noaklong, rangkuman.aspek, rangkuman.id_semester) a
                        group by a.noaklong, a.id_semester) ranking
                        group by ranking.noaklong) x, (select @rank := 0) r
                        order by x.rank_aspek desc) a 
                        left join m_user_taruna b on a.noaklong=b.noaklong
                        where b.id_m_user='" . $rs['datataruna']->id_m_user . "'";




        $rs['semester'] = $this->db->query($semester)->getResult();

        $rs['data'] = $this->db->query($query)->getResult();
        $rs['ranking'] = $this->db->query($ranking)->getRow()->ranking;
        $rs['nama_file'] = $rs['datataruna']->namataruna . " - Transkrip";

        echo json_encode($rs);
    }

    function cetaktranskrip_download_old()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $id_m_user = $data['id_taruna'];
        $id_tingkat = $data['id_tingkat'];

        $semester = "SELECT * FROM m_semester WHERE id_tingkat = '" . $id_tingkat . "' ";

        $query = "SELECT z.aspek, SUM(IF(y.ganjil_genap = 'ganjil', IFNULL(z.nilai_akhir,0), 0)) AS ganjil,
                    SUM(IF(y.ganjil_genap = 'genap', IFNULL(z.nilai_akhir,0), 0)) AS genap, SUM(z.nilai_akhir)/2 AS rerata FROM (
                        SELECT 
                            a.nilai_akhir, a.id_semester, a.id_user_taruna,
                            'Karakter' AS aspek
                        FROM t_penilaian_aspek_karakter a 
                        LEFT JOIN m_mata_pelajaran b
                            ON a.id_mata_pelajaran = b.id       
                        WHERE a.is_deleted = 0
                            AND b.is_deleted = 0
                        UNION
                        SELECT 
                            ROUND(SUM(a.nilai_akhir)/COUNT(*),2) AS nilai_akhir,
                            a.id_semester, a.id_user_taruna,            
                            'Pengetahuan' AS aspek
                        FROM t_penilaian_aspek_pengetahuan a 
                        LEFT JOIN m_mata_pelajaran b
                            ON a.id_mata_pelajaran = b.id       
                        WHERE a.is_deleted = 0
                            AND b.is_deleted = 0
                           		group by a.id_user_taruna
                        UNION
                        SELECT 
                            ROUND(SUM(a.nilai_akhir)/COUNT(*),2) AS nilai_akhir,
                             a.id_semester, a.id_user_taruna,
                            'Keterampilan' AS aspek
                        FROM t_penilaian_aspek_keterampilan a 
                        LEFT JOIN m_mata_pelajaran b
                            ON a.id_mata_pelajaran = b.id       
                        WHERE a.is_deleted = 0
                            AND b.is_deleted = 0
                           		group by a.id_user_taruna
                        UNION
                        SELECT 
                            a.nilai_akhir, a.id_semester, a.id_user_taruna,
                            'Jasmani' AS aspek
                        FROM t_penilaian_aspek_jasmani a 
                        LEFT JOIN m_mata_pelajaran b
                            ON a.id_mata_pelajaran = b.id
                        WHERE a.is_deleted = 0
                            AND b.is_deleted = 0
                        UNION
                        SELECT 
                            a.nilai_akhir, a.id_semester, a.id_user_taruna,
                            'Kesehatan' AS aspek
                        FROM t_penilaian_aspek_kesehatan a 
                        LEFT JOIN m_mata_pelajaran b
                            ON a.id_mata_pelajaran = b.id
                        WHERE a.is_deleted = 0
                            AND b.is_deleted = 0) z 
                        LEFT JOIN m_semester y ON y.id = z.id_semester
                            WHERE z.id_user_taruna = '" . $id_m_user . "' AND y.id_tingkat = '" . $id_tingkat . "'
                            GROUP BY z.aspek ";


        $datataruna = "SELECT a.noaklong, a.namataruna , CONCAT(b.batalyon , ' ( ' ,b.`tahun_masuk` ,' )') AS batalyon , c.semester , e.kelompok, g.tingkatan , b.angkatan, f.semester , ROUND(b.tahun_masuk + ((b.id_semester/2) + MOD(b.id_semester/2,1)-1))  AS tahun_ajaran,h.tingkatan as next_tingkat
                        FROM m_user_taruna a 
                        LEFT JOIN m_sm_batalyon b ON a.id_batalyon=b.id
                        LEFT JOIN m_semester c ON b.`id_semester`=c.id
                        LEFT JOIN m_kelompok e ON a.`id_kelompok`=e.id
                        
                        LEFT JOIN m_semester f ON b.id_semester=f.id
            LEFT JOIN m_tingkatan g ON '" . $id_tingkat . "'=g.id
            LEFT JOIN m_tingkatan h ON g.id+1=h .id
                        where a.id_m_user='" . $id_m_user . "'";

        $rs['semester'] = $this->db->query($semester)->getResult();
        $rs['datataruna'] = $this->db->query($datataruna)->getRow();

        $rs['data'] = $this->db->query($query)->getResult();
        $rs['nama_file'] = $rs['datataruna']->namataruna . " - Transkrip";

        echo json_encode($rs);
    }

    function cetakkartuujian_download()
    {

        $id_m_user = '6';
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.id_m_user,a.noaklong, a.namataruna , a.photopath, CONCAT(b.batalyon , ' ( ' ,b.`tahun_masuk` ,' )') AS batalyon , c.semester, d.kelompok
                        FROM m_user_taruna a 
                        LEFT JOIN m_sm_batalyon b ON a.id_batalyon=b.id
                        LEFT JOIN m_semester c ON b.id_semester=c.id
                        left join m_kelompok d on a.id_kelompok=d.id
                        where a.id_m_user='" . $data['id_taruna'] . "'";

        $querymatapelajaran = "SELECT a.tanggal , a.jam_mulai , a.jam_selesai,a.id_kelompok_taruna , c.kelompok , d.mata_pelajaran , e.nama as nama_ruangan 
                        from t_jadwal a 
                        left join t_bahan_ajar b on a.id_bahan_ajar=b.id
                        left join m_kelompok c on a.id_kelompok_taruna = c.id
                        left join m_mata_pelajaran d on b.id_mata_pelajaran=d.id
                        left join m_ruang_kelas e on a.id_ruang_kelas=e.id
                        left join m_user_taruna f on f.id_kelompok=c.id
                        where a.is_deleted='0' 
                            and b.is_ujian='1'
                            and b.id_jenis_ujian='" . $data['id_jenis_ujian'] . "'
                            and f.id_m_user='" . $data['id_taruna'] . "' ";


        $rs['datataruna'] = $this->db->query($query)->getRow();
        $rs['querymatapelajaran'] = $this->db->query($querymatapelajaran)->getResult();
        $rs['nama_file'] = $rs['datataruna']->namataruna . " - KRS ";

        echo json_encode($rs);
    }

    function cetakabsensiperkuliahan_download()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $id_batalyon = $data['id_batalyon'];
        $id_mata_pelajaran = $data['id_mata_pelajaran'];
        $id_kelompok_taruna = $data['id_kelompok_taruna'];
        $query = " 
            SELECT
                        a.id_mata_pelajaran,
                        e.id_batalyon,
                        e.id_semester,
                        e.id_m_user AS id_user_taruna,
                        e.namataruna AS nama_taruna,
                        e.noaklong,
                        g.tidak_hadir,
                                    g.sakit,
                                    g.dinas,
                                    g.ijin,
                                    g.deputasi,
                        g.hadir,
                        g.jumlah_pertemuan,
                        g.prosentase_kehadiran,
                                    g.prosentase_sakit
                        FROM 
                            t_program_studi_mata_pelajaran a
                       LEFT JOIN m_user_taruna e
                                        on e.id_batalyon = a.id_batalyon
                                        and e.id_semester = a.id_semester
                                    LEFT JOIN m_sm_batalyon b
                            ON a.id_batalyon = b.id 
                                        and a.id_semester = b.id_semester 
                        LEFT JOIN m_kelompok c
                            ON c.id=e.id_kelompok
                        left join (
                                select m.id_mata_pelajaran,
                                    m.id_semester,
                                    l.id_batalyon,
                                    k.id_taruna,
                                    n.namataruna as nama_taruna,
                                    n.noaklong,
                                    sum(if(k.is_absen=0,1,0)) as tidak_hadir,
                                                sum(if(k.keterangan='Sakit',1,0)) as sakit,
                                    sum(if(k.keterangan='Dinas',1,0)) as dinas,
                                                sum(if(k.keterangan='Ijin Khusus',1,0)) as ijin,
                                                sum(if(k.keterangan='Deputasi',1,0)) as deputasi,
                                    sum(if(k.is_absen=0,0,1)) as hadir,
                                    count(*) as jumlah_pertemuan,
                                    sum(k.is_absen)/count(*)*100 as prosentase_kehadiran,
                                    sum(if(k.keterangan='Sakit',1,0))/count(*)*100 as prosentase_sakit
                                from t_absensi k
                                left join t_jadwal l
                                    on k.id_jadwal = l.id
                                left join t_bahan_ajar m
                                    on l.id_bahan_ajar = m.id
                                left join m_user_taruna n
                                    on k.id_taruna = n.id_m_user
                                where m.is_ujian = 0
                                    and k.is_deleted = 0
                                group by  m.id_semester, k.id_taruna, m.id_mata_pelajaran
                        ) g
                        on g.id_mata_pelajaran = a.id_mata_pelajaran
                            and g.id_semester = b.id_semester
                            and g.id_taruna = e.id_m_user
                            and g.id_batalyon = a.id_batalyon
                        where c.id = '" . $id_kelompok_taruna . "'
                            and a.is_deleted = 0
                            and a.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
                            and a.id_batalyon = '" . $id_batalyon . "'
        ";

        $rs['semester'] = $this->db->query("SELECT a.semester from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester where b.id='" . $id_batalyon . "' ")->getRow()->semester;
        $rs['matapelajaran'] = $this->db->query("SELECT a.mata_pelajaran from m_mata_pelajaran a where a.id='" . $id_mata_pelajaran . "' ")->getRow()->mata_pelajaran;
        $rs['kelompoktaruna'] = $this->db->query("SELECT a.kelompok from m_kelompok a where a.id='" . $id_kelompok_taruna . "'")->getRow()->kelompok;
        $rs['data'] = $this->db->query($query)->getResult();
        $rs['datasakit'] = $this->db->query($query . "having g.prosentase_sakit>'25'")->getResult();
        $rs['nama_file'] = "Absensi Perkuliahan";

        echo json_encode($rs);
    }

    function cetakabsensiujian_download()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $id_batalyon = $data['id_batalyon'];
        $id_mata_pelajaran = $data['id_mata_pelajaran'];
        $id_kelompok_taruna = $data['id_kelompok_taruna'];
        $jenis_ujian = $data['jenis_ujian'];
        $query = " 
        SELECT * from t_filter_taruna_ujian a

        left join m_user_taruna b on a.id_user_taruna=b.id_m_user

        where a.is_deleted='0'
        ";

        $rs['semester'] = $this->db->query("SELECT a.semester from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester where b.id='" . $id_batalyon . "' ")->getRow()->semester;
        $rs['matapelajaran'] = $this->db->query("SELECT a.mata_pelajaran from m_mata_pelajaran a where a.id='" . $id_mata_pelajaran . "' ")->getRow()->mata_pelajaran;
        $rs['kelompoktaruna'] = $this->db->query("SELECT a.kelompok from m_kelompok a where a.id='" . $id_kelompok_taruna . "'")->getRow()->kelompok;

        $rs['jenis_ujian'] = $this->db->query("SELECT a.keterangan from m_jenis_ujian a where a.id='" . $jenis_ujian . "'")->getRow()->keterangan;
        $rs['data'] = $this->db->query($query)->getResult();
        $rs['nama_file'] = "Absensi Ujian";

        echo json_encode($rs);
    }


    // begin jadwaltaruna done
    function jadwaltaruna_load()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        $tanggal = explode(" ", $data['tanggal']);
        $tanggal_awal = date("Y-m-d", strtotime(str_replace("/", "-", $tanggal[0])));

        $tanggal_akhir = date("Y-m-d", strtotime(str_replace("/", "-", $tanggal[2])));

        // $tanggal_wal
        // echo json_encode($data['tanggal']);
        // echo json_encode(explode(" ",$data['tanggal']));

        $id_taruna = $data['id_taruna'];

        // $tanggal_akhir = date("Y-m-d", strtotime("$tanggal_awal +6 day"));




        $header = $this->db->query("SELECT k.id_kelompok, k.nama_kelompok, if(l.id_ruangan is null,'',l.id_ruangan) as id_ruangan, if(l.nama_ruangan is null,'',l.nama_ruangan) as nama_ruangan
                from (
                    select  c.id as id_kelompok, c.kelompok as nama_kelompok from m_sm_batalyon a inner join m_user_taruna b  on a.id = b.id_batalyon and a.id_semester=b.id_semester left join m_kelompok c on b.id_kelompok = c.id where b.id_m_user='" . $id_taruna . "' and a.is_deleted = 0 and b.is_deleted = 0 and c.is_deleted = 0 group by c.id
                    ) k 
                left join (
                    select a.id_kelompok_taruna as id_kelompok, b.id as id_ruangan, b.nama as nama_ruangan  from t_jadwal a left join m_ruang_kelas b on a.id_ruang_kelas = b.id where a.tanggal between '" . $tanggal_awal . "' and '" . $tanggal_akhir . "' group by a.id_kelompok_taruna, a.id_ruang_kelas
                    ) l on k.id_kelompok = l.id_kelompok ")->getResult();

        $query = "SELECT  
                a.selected_date as tanggal,IF(a.selected_date<CURDATE() , '1' , '0') AS kunci,
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
                                        and (a.tanggal BETWEEN '" . $tanggal_awal . "' and '" . $tanggal_akhir . "')
                                        and j.id_m_user='" . $id_taruna . "' group by a.id ORDER BY a.tanggal ASC ")->getResult();

        $result = $this->db->query($query)->getResult();




        $data = array(
            "header" => $header,
            "body" => $result,
            "content" => $data_unit,
            "tgl_akhir" => date("d M Y", strtotime($tanggal_awal)) . ' sd ' . date("d M Y", strtotime($tanggal_akhir))
        );


        $response = ['success' => true, 'data' => $data];

        echo json_encode($response);
    }


    function cetakkhs_load()
    {
        $query = "select a.*  from m_program_studi a  where a.is_deleted = 0";
        $where = ["a.nama"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function cetakkhs_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_program_studi', $data, $userid);
    }

    function cetakkhs_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.*  from m_program_studi a
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_program_studi', $data, null, $query);
    }

    function cetakkhs_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_program_studi', $data, $userid);
    }

    function absensiperkuliahan_load()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_kelompok = $data["id_kelompok"];
        $pertemuan_ke = $data["pertemuan_ke"];
        $id_batalyon = $data["id_batalyon"];

        $query = "SELECT * from (SELECT  d.id_m_user, d.namataruna, d.noaklong, d.photopath, c.pertemuan_ke, f.*
        from m_mata_pelajaran a
		left join t_program_studi_mata_pelajaran z on z.id_batalyon = z.id_batalyon and a.id = z.id_mata_pelajaran
        left join t_bahan_ajar c on a.id=c.id_mata_pelajaran and z.id_batalyon=c.id_batalyon
        left join m_user_taruna d on z.id_batalyon = d.id_batalyon
        inner join t_jadwal e on c.id=e.id_bahan_ajar
        inner join t_absensi f 
            on e.id=f.id_jadwal and d.id_m_user=f.id_taruna
        where c.id_mata_pelajaran= '" . $id_mata_pelajaran . "' and d.id_kelompok='" . $id_kelompok . "' and d.id_batalyon = '" . $id_batalyon . "' and c.id='" . $pertemuan_ke . "' and e.is_deleted='0' group by d.id_m_user) a1 where a1.is_deleted='0' ";

        // echo $query;
        parent::_loadlist($data, $query);
    }

    function absensiperkuliahan_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insertbatch('t_absensi', $data, $userid);
    }

    function inputnilai_load()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $userid = json_decode($this->request->getPost('userid'), true);
        $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_batalyon = $data["id_batalyon"];
        // var_dump($data['type_code']);
        if ($data['type_code'] !== 'vds') {
            $query = "SELECT
                    d.id,
                    b.id_m_user AS id_user_taruna,
                    '" . $id_mata_pelajaran . "' AS id_mata_pelajaran,
                    b.id_semester,
                    '" . $id_batalyon . "' AS id_batalyon,
                    b.namataruna,
                    b.noaklong,
                    c.kelompok,
                    d.uts_ljk,
                    d.uts_esai,
                    d.uas_ljk,
                    d.uas_esai,
                    d.her_ljk,
                    d.her_esai,
                    d.proses_ajar,
                    d.tugas,
                    d.her,
                    d.nilai_akhir,
                    d.kategori,
                    d.bobot,
                    d.klasifikasi,
                    e.tahun_ajaran,
                    f.esai AS limit_esai,
                    f.ljk AS limit_ljk,
                    j.nilai AS batas_her
                FROM m_user_taruna b
                    INNER JOIN m_kelompok c ON c.id = b.id_kelompok
                    LEFT JOIN t_penilaian_aspek_pengetahuan d ON d.id_user_taruna = b.id_m_user AND d.id_semester = b.id_semester AND d.id_batalyon = b.id_batalyon AND d.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
                    LEFT JOIN t_program_studi_mata_pelajaran e ON d.id_mata_pelajaran = e.id_mata_pelajaran AND b.id_batalyon = e.id_batalyon AND b.id_semester = e.id_semester
                    LEFT JOIN m_konversi_nilai_pengetahuan f ON b.id_semester = f.id_semester
                    JOIN t_pendidik_mata_pelajaran k ON k.id_batalyon = '" . $id_batalyon . "' AND k.id_mata_pelajaran = '" . $id_mata_pelajaran . "' AND k.id_pendidik = '" . $userid . "'
                    JOIN m_user h ON h.id = '" . $userid . "'
                    LEFT JOIN m_tingkatan_detail i ON i.id_semester = b.id_semester AND i.uts_uas = 'UAS'
                    LEFT JOIN m_konversi_nilai_batas_lulus j ON j.id_semester = b.id_semester AND j.id_tingkat = i.id_tingkatan AND j.id_aspek = '1'
                WHERE
                    b.id_batalyon = '" . $id_batalyon . "'
                    AND b.is_deleted = '0'
                    AND b.is_verif = '1'
                ORDER BY c.kelompok, b.namataruna";
        } else {
            $query = "SELECT
                    d.id,
                    b.id_m_user AS id_user_taruna,
                    '" . $id_mata_pelajaran . "' AS id_mata_pelajaran,
                    b.id_semester,
                    '" . $id_batalyon . "' AS id_batalyon,
                    b.namataruna,
                    b.noaklong,
                    c.kelompok,
                    d.uts_ljk,
                    d.uts_esai,
                    d.uas_ljk,
                    d.uas_esai,
                    d.her_ljk,
                    d.her_esai,
                    d.proses_ajar,
                    d.tugas,
                    d.her,
                    d.nilai_akhir,
                    d.kategori,
                    d.bobot,
                    d.klasifikasi,
                    e.tahun_ajaran,
                    f.esai AS limit_esai,
                    f.ljk AS limit_ljk,
                    j.nilai AS batas_her
                FROM m_user_taruna b
                    INNER JOIN m_kelompok c ON c.id = b.id_kelompok
                    LEFT JOIN t_penilaian_aspek_pengetahuan d ON d.id_user_taruna = b.id_m_user AND d.id_semester = b.id_semester AND d.id_batalyon = b.id_batalyon AND d.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
                    LEFT JOIN t_program_studi_mata_pelajaran e ON d.id_mata_pelajaran = e.id_mata_pelajaran AND b.id_batalyon = e.id_batalyon AND b.id_semester = e.id_semester
                    LEFT JOIN m_konversi_nilai_pengetahuan f ON b.id_semester = f.id_semester
                    LEFT JOIN t_pendidik_mata_pelajaran k ON k.id_batalyon = '" . $id_batalyon . "' AND k.id_mata_pelajaran = '" . $id_mata_pelajaran . "' AND k.id_pendidik = '" . $userid . "'
                    LEFT JOIN m_user h ON h.id = '" . $userid . "'
                    LEFT JOIN m_tingkatan_detail i ON i.id_semester = b.id_semester AND i.uts_uas = 'UAS'
                    LEFT JOIN m_konversi_nilai_batas_lulus j ON j.id_semester = b.id_semester AND j.id_tingkat = i.id_tingkatan AND j.id_aspek = '1'
                WHERE
                    b.id_batalyon = '" . $id_batalyon . "'
                    AND b.is_deleted = '0'
                    AND b.is_verif = '1'
                ORDER BY c.kelompok, b.namataruna";
        }
        parent::_loadlist($data, $query);
    }


    function inputnilai_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $existingId = $this->db->query("SELECT * FROM t_penilaian_aspek_pengetahuan
                                       WHERE id_user_taruna = '" . $data['id_user_taruna'] . "' AND
                                       id_mata_pelajaran = '" . $data['id_mata_pelajaran'] . "' AND
                                       id_semester = '" . $data['id_semester'] . "' AND
                                       id_batalyon = '" . $data['id_batalyon'] . "'");
        if ($existingId->getNumRows() > 0) {
            $data['id'] = $existingId->getRow()->id;
        }
        // var_dump($this->db->getLastQuery());

        // echo json_encode($data);
        parent::_insertReturn('t_penilaian_aspek_pengetahuan', $data, $userid);
    }

    function inputnilaiketerampilan_load()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $userid = json_decode($this->request->getPost('userid'), true);
        $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_batalyon = $data["id_batalyon"];

        // $where = ($data['type_code'] == 'pgh' ? "and h.id = '" . $userid . "'" : "");
        // $query = "SELECT
        //     d.id,
        //     b.id_m_user as id_user_taruna,
        //     '" . $id_mata_pelajaran . "' as id_mata_pelajaran,
        //     b.id_semester,
        //     '" . $id_batalyon . "' as id_batalyon,
        //     i.tahun_ajaran,
        //     b.namataruna,
        //     b.noaklong,
        //     c.kelompok,
        //     d.proses_pelatihan,
        //     d.produk_pelatihan,
        //     d.her,
        //     d.nilai_akhir,
        //     d.kategori,
        //     d.bobot,
        //     d.klasifikasi,
        //     f.esai as limit_esai,
        //     f.ljk as limit_ljk
        // from m_user_taruna b
        // inner join m_kelompok c
        //     on c.id = b.id_kelompok
        // left join t_penilaian_aspek_keterampilan d
        //     on d.id_user_taruna = b.id_m_user
        //     and d.id_semester = b.id_semester
        //     and d.id_batalyon = b.id_batalyon
        //     and d.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
        // left join t_program_studi_mata_pelajaran i
        //     on d.id_mata_pelajaran = i.id_mata_pelajaran
        // left join m_konversi_nilai_keterampilan f
        //     on b.id_semester = f.id_semester
        // left join m_sm_peleton g 
        //     on b.id_peleton = g.id
        // left join m_user h 
        //     on h.id = g.id_user_pendidik
        // where b.id_batalyon = '" . $id_batalyon . "' " . $where;

        if ($data['type_code'] !== 'vds' || $data['type_code'] !== 'pgs' || $data['type_code'] !== 'jmn' || $data['type_code'] !== 'bnt' || $data['type_code'] !== 'pfk' || $data['type_code'] !== 'bkm' || $data['type_code'] !== 'ftr' || $data['type_code'] !== 'gdk') {
            $where = ($data['type_code'] == 'vds' || $data['type_code'] == 'pgs' || $data['type_code'] == 'jmn' || $data['type_code'] == 'bnt' || $data['type_code'] == 'pfk' || $data['type_code'] == 'bkm' || $data['type_code'] == 'ftr' || $data['type_code'] == 'gdk' ? "and h.id = '" . $userid . "'" : "");
            $query = "SELECT
                            d.id,
                            b.id_m_user as id_user_taruna,
                            '" . $id_mata_pelajaran . "' as id_mata_pelajaran,
                            b.id_semester,
                            '" . $id_batalyon . "' as id_batalyon,
                            i.tahun_ajaran,
                            b.namataruna,
                            b.noaklong,
                            c.kelompok,
                            d.proses_pelatihan,
                            d.produk_pelatihan,
                            d.her,
                            d.nilai_akhir,
                            d.kategori,
                            d.bobot,
                            d.klasifikasi,
                            f.esai as limit_esai,
                            f.ljk as limit_ljk
                        from
                            m_user_taruna b
                            inner join m_kelompok c on c.id = b.id_kelompok
                            left join t_penilaian_aspek_keterampilan d on d.id_user_taruna = b.id_m_user
                            and d.id_semester = b.id_semester
                            and d.id_batalyon = b.id_batalyon
                            and d.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
                            JOIN t_pendidik_mata_pelajaran e ON e.id_batalyon = '" . $id_batalyon . "' AND e.id_mata_pelajaran = '" . $id_mata_pelajaran . "' AND e.id_pendidik = '". $userid ."'
                            left join t_program_studi_mata_pelajaran i on d.id_mata_pelajaran = i.id_mata_pelajaran
                            left join m_konversi_nilai_keterampilan f on b.id_semester = f.id_semester
                            join m_user h on h.id = '". $userid ."'
                        where
                            b.id_batalyon = '" . $id_batalyon . "' " . $where;
        } else {
            $query = "SELECT
                            d.id,
                            b.id_m_user as id_user_taruna,
                            '" . $id_mata_pelajaran . "' as id_mata_pelajaran,
                            b.id_semester,
                            '" . $id_batalyon . "' as id_batalyon,
                            i.tahun_ajaran,
                            b.namataruna,
                            b.noaklong,
                            c.kelompok,
                            d.proses_pelatihan,
                            d.produk_pelatihan,
                            d.her,
                            d.nilai_akhir,
                            d.kategori,
                            d.bobot,
                            d.klasifikasi,
                            f.esai as limit_esai,
                            f.ljk as limit_ljk
                        from m_user_taruna b
                            inner join m_kelompok c on c.id = b.id_kelompok
                            left join t_penilaian_aspek_keterampilan d on d.id_user_taruna = b.id_m_user
                            and d.id_semester = b.id_semester
                            and d.id_batalyon = b.id_batalyon
                            and d.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
                            JOIN t_pendidik_mata_pelajaran e ON e.id_batalyon = '" . $id_batalyon . "' AND e.id_mata_pelajaran = '" . $id_mata_pelajaran . "' AND e.id_pendidik = '". $userid ."'
                            left join t_program_studi_mata_pelajaran i on d.id_mata_pelajaran = i.id_mata_pelajaran
                            left join m_konversi_nilai_keterampilan f on b.id_semester = f.id_semester
                            join m_user h on h.id = '". $userid ."'
                        where b.id_batalyon = '" . $id_batalyon . "'";
        }

        parent::_loadlist($data, $query);
    }

    function inputnilaiketerampilan_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $existingId = $this->db->query("SELECT * FROM t_penilaian_aspek_keterampilan
                                       WHERE id_user_taruna = '" . $data['id_user_taruna'] . "' AND
                                       id_mata_pelajaran = '" . $data['id_mata_pelajaran'] . "' AND
                                       id_semester = '" . $data['id_semester'] . "' AND
                                       id_batalyon = '" . $data['id_batalyon'] . "'");
        if ($existingId->getNumRows() > 0) {
            $data['id'] = $existingId->getRow()->id;
        }

        // echo json_encode($data);
        parent::_insertReturn('t_penilaian_aspek_keterampilan', $data, $userid);
    }

    function inputnilaikesehatan_load()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $userid = json_decode($this->request->getPost('userid'), true);
        // $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_batalyon = $data["id_batalyon"];

        $where = ($data['type_code'] == 'pgh' ? "and f.id = '" . $userid . "'" : "");
        $query = "SELECT
            d.id,
            b.id_m_user as id_user_taruna,
            b.id_semester,
            '" . $id_batalyon . "' as id_batalyon,
            b.namataruna,
            b.noaklong,
            c.kelompok,
            d.rikesla,
            d.insidentil,
            d.rikes,
            d.keswa,
            d.her,
            d.nilai_akhir,
            d.kategori,
            d.bobot,
            d.klasifikasi,
            j.nilai as batas_her
        from m_user_taruna b
        inner join m_kelompok c
            on c.id = b.id_kelompok
        left join t_penilaian_aspek_kesehatan d
            on d.id_user_taruna = b.id_m_user
            and d.id_semester = b.id_semester
            and d.id_batalyon = b.id_batalyon
        left join m_sm_peleton e 
            on b.id_peleton = e.id
        left join m_user f 
            on f.id = e.id_user_pendidik
        left join m_tingkatan_detail i
            on i.id_semester = b.id_semester
            and i.uts_uas = 'UAS'
        left join m_konversi_nilai_batas_lulus j
            on j.id_semester = b.id_semester
            and j.id_tingkat = i.id_tingkatan
            and j.id_aspek = '4'
        where b.id_batalyon = '" . $id_batalyon . "' " . $where . " order by c.kelompok, b.namataruna";


        parent::_loadlist($data, $query);
    }

    function inputnilaikesehatan_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $existingId = $this->db->query("SELECT * FROM t_penilaian_aspek_kesehatan
                                       WHERE id_user_taruna = '" . $data['id_user_taruna'] . "' AND
                                       id_semester = '" . $data['id_semester'] . "' AND
                                       id_batalyon = '" . $data['id_batalyon'] . "'");
        if ($existingId->getNumRows() > 0) {
            $data['id'] = $existingId->getRow()->id;
        }

        // echo json_encode($data);
        parent::_insertReturn('t_penilaian_aspek_kesehatan', $data, $userid);
    }

    function inputnilaijasmani_load()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $userid = json_decode($this->request->getPost('userid'), true);
        // $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_batalyon = $data["id_batalyon"];

        $where = ($data['type_code'] == 'pgh' ? "and f.id = '" . $userid . "'" : "");
        $query = "SELECT
            d.id,
            b.id_m_user as id_user_taruna,
            b.id_semester,
            '" . $id_batalyon . "' as id_batalyon,
            b.namataruna,
            IF(b.id_gender = 1, 'Laki-laki', 'Perempuan') as gender,
            b.noaklong,
            c.kelompok,
            d.putaran_jumlah/400 as putaran,
            d.putaran_jumlah,
            d.tambahan_jumlah,
            d.putaran_jumlah+d.tambahan_jumlah as lari_jumlah,
            d.pull_up_jumlah,
            d.pull_up_nilai,
            d.sit_up_jumlah,
            d.sit_up_nilai,
            d.push_up_jumlah,
            d.push_up_nilai,
            d.run_jumlah,
            d.run_nilai,
            d.samapta_a,
            d.samapta_b,
            d.id_syarat_penilaian,
            d.nilai_akhir,
            d.kategori,
            d.bobot,
            d.klasifikasi,
            j.nilai as batas_her,
            k.lari_nilai as batas_lari,
            k.pull_up_nilai as batas_pull_up,
            k.sit_up_nilai as batas_sit_up,
            k.push_up_nilai as batas_push_up,
            k.shuttle_run_nilai as batas_run,
            k.nilai_akhir as batas_nilai_akhir
        from m_user_taruna b
        inner join m_kelompok c
            on c.id = b.id_kelompok
        left join t_penilaian_aspek_jasmani d
            on d.id_user_taruna = b.id_m_user
            and d.id_semester = b.id_semester
            and d.id_batalyon = b.id_batalyon
        left join m_sm_peleton e 
            on b.id_peleton = e.id
        left join m_user f 
            on f.id = e.id_user_pendidik
        left join m_tingkatan_detail i
            on i.id_semester = b.id_semester
            and i.uts_uas = 'UAS'
        left join m_konversi_nilai_batas_lulus j
            on j.id_semester = b.id_semester
            and j.id_tingkat = i.id_tingkatan
            and j.id_aspek = '5'
        left join m_nilai_batas_jasmani k
        	on b.id_gender = k.id_jenis	
        	and i.id_tingkatan = k.id_tingkatan
        where b.id_batalyon = '" . $id_batalyon . "' " . $where . " order by c.kelompok, b.namataruna";


        parent::_loadlist($data, $query);
    }

    function inputnilaijasmani_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $existingId = $this->db->query("SELECT * FROM t_penilaian_aspek_jasmani
                                       WHERE id_user_taruna = '" . $data['id_user_taruna'] . "' AND
                                       id_semester = '" . $data['id_semester'] . "' AND
                                       id_batalyon = '" . $data['id_batalyon'] . "'");
        if ($existingId->getNumRows() > 0) {
            $data['id'] = $existingId->getRow()->id;
        }

        // echo json_encode($data);
        parent::_insertReturn('t_penilaian_aspek_jasmani', $data, $userid);
    }

    function inputnilaikarakter_load()
    {

        $data = json_decode($this->request->getPost('param'), true);
        $id_batalyon_selec = $data["id_batalyon"];
        // $user_detail = $this->webModel->getUserDetail('gdk', $data['id']);
        $userid = json_decode($this->request->getPost('userid'), true);
        $id_batalyons = "SELECT id from m_sm_batalyon where id_user_pendidik = '" . $userid . "' and id = '" . $id_batalyon_selec . "' and is_deleted = 0";
        $id_kompis = "SELECT id from m_sm_kompi where id_user_pendidik = '" . $userid . "' and id_batalyon = '" . $id_batalyon_selec . "' and is_deleted = 0";
        $id_peletons = "SELECT a.id FROM m_sm_peleton a LEFT JOIN m_sm_kompi b ON b.id = a.id_kompi WHERE a.id_user_pendidik = '" . $userid . "' and b.id_batalyon = '" . $id_batalyon_selec . "' AND a.is_deleted = 0 AND b.is_deleted = 0";
        $id_batalyon = $this->db->query($id_batalyons)->getRow();
        $id_kompi = $this->db->query($id_kompis)->getRow();
        $id_peleton = $this->db->query($id_peletons)->getRow();
        if (isset($id_batalyon)) {
            $where = "b.id_batalyon = '" . $id_batalyon->id . "'";
        } elseif (isset($id_kompi)) {
            $where = "b.id_kompi = '" . $id_kompi->id . "'";
        } elseif (isset($id_peleton)) {
            $where = "b.id_peleton = '" . $id_peleton->id . "'";
        } else {
            $where = "b.id_batalyon = '0'";
        }

        // print_r('<pre>');
        // print_r($where);
        // print_r('</pre>');
        // die;


        // $id_mata_pelajaran = $data["id_mata_pelajaran"];

        $where2 = ($data['type_code'] == 'pgh' ? "and f.id = '" . $id_batalyon_selec . "'" : "");
        $query = "SELECT
            d.id,
            b.id_m_user as id_user_taruna,
            b.id_semester,
            b.id_batalyon as id_batalyon,
            b.namataruna,
            b.noaklong,
            c.kelompok,
            d.bulan_1,
            d.bulan_2,
            d.bulan_3,
            d.bulan_4,
            d.bulan_5,
            d.bulan_6,
            d.nilai_akhir,
            d.kategori,
            d.bobot,
            d.klasifikasi,
            j.nilai as batas_her
        from m_user_taruna b
        inner join m_kelompok c
            on c.id = b.id_kelompok
        left join t_penilaian_aspek_karakter d
            on d.id_user_taruna = b.id_m_user
            and d.id_semester = b.id_semester
            and d.id_batalyon = b.id_batalyon
        left join m_sm_peleton e 
            on b.id_peleton = e.id
        left join m_user f 
            on f.id = e.id_user_pendidik
        left join m_tingkatan_detail i
            on i.id_semester = b.id_semester
            and i.uts_uas = 'UAS'
        left join m_konversi_nilai_batas_lulus j
            on j.id_semester = b.id_semester
            and j.id_tingkat = i.id_tingkatan
            and j.id_aspek = '3'
        where " . $where  . $where2 . " and b.is_verif = '1' order by c.kelompok, b.namataruna";

        parent::_loadlist($data, $query);
        // var_dump($this->db->getLastQuery());
    }


    function inputnilaikarakter_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $existingId = $this->db->query("SELECT * FROM t_penilaian_aspek_karakter
                                       WHERE id_user_taruna = '" . $data['id_user_taruna'] . "' AND
                                       id_semester = '" . $data['id_semester'] . "' AND
                                       id_batalyon = '" . $data['id_batalyon'] . "'");
        if ($existingId->getNumRows() > 0) {
            $data['id'] = $existingId->getRow()->id;
        }

        // echo json_encode($data);
        parent::_insertReturn('t_penilaian_aspek_karakter', $data, $userid);
    }

    function rekapnilaikarakterbypeleton_load()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $userid = json_decode($this->request->getPost('userid'), true);

        $id_batalyon = $data["id_batalyon"];
        $id_kompi = $data["id_kompi"];
        $id_peleton = $data["id_peleton"];

        $where = ($data['type_code'] == 'pgh' ? "and f.id = '" . $userid . "'" : "");
        $query = "select a.id, b.id, c.id, d.id, f.id, a.namataruna, a.noaklong, b.batalyon, c.kompi, d.peleton, '70' as nilai_awal, sum(ifnull(f.poin,0)) as pelanggaran, (70-ifnull(f.poin,0)) as total 
        from m_user_taruna a
        left join m_sm_batalyon b on a.id_batalyon = b.id
        left join m_sm_kompi c on a.id_kompi = c.id
        left join m_sm_peleton d on a.id_peleton = d.id 
        left join t_pelanggaran_karakter_taruna f on a.id_m_user = f.id_taruna
        group by f.id_taruna
        where a.id_batalyon = '" . $id_batalyon . "' and a.id_kompi = '" . $id_kompi . "' and a.id_peleton = '" . $id_peleton . "' ";

        parent::_loadlist($data, $query);
    }

    function datapesertaujian_load()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_kelompok = $data["id_kelompok"];
        $id_batalyon = $data["id_batalyon"];

        $query = "select
		f.id,
        a.id_mata_pelajaran,
        a.id as id_program_studi,
        a.id_batalyon,
      	a.tahun_ajaran,
        e.id_m_user as id_user_taruna,
        e.namataruna as nama_taruna,
        e.noaklong,
        g.kehadiran,
        f.is_uas,
        f.is_uts,
        f.keterangan
        from 
            t_program_studi_mata_pelajaran a
        left join m_user_taruna e
            on a.id_batalyon = a.id_batalyon
        left join t_filter_taruna_ujian f
            on f.id_user_taruna = e.id
            and f.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
            and f.id_batalyon = '" . $id_batalyon . "'
        left join (
                select m.id_mata_pelajaran,
                    m.id_batalyon,
                    k.id_taruna,
                    sum(k.is_absen)/count(*)*100 as kehadiran
                from t_absensi k
                left join t_jadwal l
                    on k.id_jadwal = l.id
                left join t_bahan_ajar m
                    on l.id_bahan_ajar = m.id
                where m.is_ujian = 0
                    and k.is_deleted = 0
                    and m.id_batalyon = '" . $id_batalyon . "'
                group by m.id_batalyon, k.id_taruna
        ) g
        on g.id_mata_pelajaran = a.id_mata_pelajaran
            and g.id_batalyon = a.id_batalyon
            and g.id_taruna = e.id_m_user 
        where a.is_deleted = 0
        and a.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
            and e.id_kelompok ='" . $id_kelompok . "'";

        parent::_loadlist($data, $query);
    }


    function datapesertaujian_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insertbatch('t_filter_taruna_ujian', $data, $userid);
    }

    function nilaiakhirsemester_load()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $id_semester = $data["id_semester"];
        $id_batalyon = $data["id_batalyon"];

        $mata_pelajaran = $this->matapelajaranbysemester_get($id_semester);
        $taruna = $this->tarunabysemester_get($id_semester);
        $set_session = 'SET SESSION group_concat_max_len = 60000;';
        $this->db->query($set_session);

        $query = " SELECT rapor.*, 
        ROUND(((2.5*rapor.rata_pengetahuan)+(2.5*rapor.rata_keterampilan)+(2.5*rapor.rata_karakter)+rata_kesehatan+(1.5*rata_jasmani))/10,2) as nap
        FROM
         (SELECT a.namataruna, a.noaklong, a.kelompok, max(a.semester) as semester,
        group_concat(distinct if(a.aspek='Pengetahuan',a.nilai,null)) as pengetahuan,
        group_concat(distinct if(a.aspek='Keterampilan',a.nilai,null)) as keterampilan,
        group_concat(distinct if(a.aspek='Karakter',a.nilai,null)) as karakter,
        group_concat(distinct if(a.aspek='Kesehatan',a.nilai,null)) as kesehatan,
        group_concat(distinct if(a.aspek='Jasmani',a.nilai,null)) as jasmani,
        sum(if(a.aspek='Pengetahuan',a.jml,0))/sum(if(a.aspek='Pengetahuan',a.pembagi,0)) as rata_pengetahuan,
        sum(if(a.aspek='Keterampilan',a.jml,0))/sum(if(a.aspek='Keterampilan',a.pembagi,0)) as rata_keterampilan,
        sum(if(a.aspek='Karakter',a.jml,0))/sum(if(a.aspek='Karakter',a.pembagi,0)) as rata_karakter,
        sum(if(a.aspek='Kesehatan',a.jml,0))/sum(if(a.aspek='Kesehatan',a.pembagi,0)) as rata_kesehatan,
        sum(if(a.aspek='Jasmani',a.jml,0))/sum(if(a.aspek='Jasmani',a.pembagi,0)) as rata_jasmani
        from (select rangkuman.namataruna, rangkuman.noaklong, rangkuman.kelompok, rangkuman.aspek, 
        json_arrayagg(
            json_object(
                rangkuman.mata_pelajaran,
        
                        json_object(
                            'nilai_awal',
                            ifnull(rangkuman.nilai_akhir,0),
                        'her',
                            ifnull(rangkuman.her,0),
                        'bobot',
                            ifnull(rangkuman.bobot,0),
                        'nilai_akhir',
                            if(rangkuman.her > 0, rangkuman.her, ifnull(rangkuman.nilai_akhir,0))
                        )	
            )
        ) as nilai,
        sum(rangkuman.nilai_akhir) as jml, count(1) as pembagi, group_concat(distinct rangkuman.semester separator '/') as semester
        from (select c.namataruna, c.noaklong, a.mata_pelajaran, j.kelompok, x.semester,
        CASE
            WHEN b.id_aspek = '1' THEN d.nilai_akhir
            WHEN b.id_aspek = '2' THEN e.nilai_akhir
            WHEN b.id_aspek = '3' THEN f.nilai_akhir
            WHEN b.id_aspek = '4' THEN g.nilai_akhir
            WHEN b.id_aspek = '5' THEN h.nilai_akhir                    
        END as nilai_akhir,
        CASE
            WHEN b.id_aspek = '1' THEN d.her
            WHEN b.id_aspek = '2' THEN e.her
            WHEN b.id_aspek = '3' THEN 0
            WHEN b.id_aspek = '4' THEN g.her
            WHEN b.id_aspek = '5' THEN h.her                    
        END as her,
        CASE
            WHEN b.id_aspek = '1' THEN d.bobot
            WHEN b.id_aspek = '2' THEN e.bobot
            WHEN b.id_aspek = '3' THEN f.bobot
            WHEN b.id_aspek = '4' THEN g.bobot
            WHEN b.id_aspek = '5' THEN h.bobot                    
        END as bobot,
        z.aspek from m_mata_pelajaran a
        left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
        left join m_user_taruna c on c.id_batalyon = b.id_batalyon
        left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
        left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
        left join t_penilaian_aspek_karakter f on f.id_semester = b.id_semester and f.id_user_taruna = c.id_m_user and f.is_deleted = 0
        left join t_penilaian_aspek_kesehatan g on g.id_semester = b.id_semester and g.id_user_taruna = c.id_m_user and g.is_deleted = 0
        left join t_penilaian_aspek_jasmani h on h.id_semester = b.id_semester and h.id_user_taruna = c.id_m_user and h.is_deleted = 0
        left join m_kelompok j on c.id_kelompok = j.id
        left join m_aspek z on z.id = b.id_aspek
        left join m_semester x on x.id = b.id_semester
        where a.is_deleted = 0
        and b.id_batalyon = $id_batalyon
        and b.id_semester = $id_semester
        group by a.id, c.noaklong) rangkuman
        group by rangkuman.noaklong, rangkuman.aspek) a
        group by a.noaklong) rapor;";

        $nilai = $this->db->query($query)->getResultArray();

        echo json_encode(array('success' => true, 'data_taruna' => $taruna, 'data_mata_pelajaran' => $mata_pelajaran, 'nilai' => $nilai));
    }

    function tugastaruna_load()
    {

        $userid = $this->request->getPost('userid');
        $usertype = $this->request->getPost('usertype');
        $data = json_decode($this->request->getPost('param'), true);
        if ($usertype == 'gdk') {
            // $where = " and c.id_user_pendidik='".$userid."' ";
            $column = " f.is_ketua_tim ";
            $where = " join t_pendidik_mata_pelajaran f on c.id_mata_pelajaran=f.id_mata_pelajaran and f.id_pendidik='" . $userid . "' ";
        } else {
            $column = " ";
            $where = " ";
        };
        $query = "SELECT t.*, a.judul, a.deskripsi, a.waktu_pengumpulan, date_format(a.waktu_pengumpulan, '%d/%m/%y %H:%i') as waktu, 
                a.file_materi_tugas, a.tipe_tugas, d.id as id_mata_pelajaran, d.mata_pelajaran , e.kelompok
                from t_nilai_tugas t
                left join t_jadwal_tugas a on t.id_jadwal_tugas = a.id
                left join t_jadwal b on a.id_jadwal=b.id
                left join t_bahan_ajar c on b.id_bahan_ajar=c.id
                left join m_mata_pelajaran d on c.id_mata_pelajaran=d.id
                left join m_kelompok e on b.id_kelompok_taruna=e.id
                where a.is_deleted='0' and t.id_user_taruna = '" . $userid . "'";

        $where = ["a.nama", "a.kode"];
        $order = "a.waktu_pengumpulan ";

        parent::_loadDatatableorder($query, $where, $data, $order, 'desc');
    }

    function tugastaruna_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $userdetail = json_decode($this->request->getPost('userdetail'), true);
        $usertype = $this->request->getPost('usertype');
        $jenisfile = $data['jenisfile'];

        if ($jenisfile == '1') {
            $file_materi_tugas = $this->request->getFile('file_tugas');
            $filetype = $this->request->getPost('filetype');
            if ($file_materi_tugas != null) {
                if ($file_materi_tugas->isValid() && !$file_materi_tugas->hasMoved()) {
                    $newName = $file_materi_tugas->getRandomName();
                    if ($usertype == "sad") {
                        $file_materi_tugas->move('./public/file_penugasan/sad/' . $userid, $newName);
                        $dir = base_url() . '/public/file_penugasan/sad/' . $userid . '/' . $newName;
                    } else {
                        $file_materi_tugas->move('./public/file_penugasan/' . $usertype . '/' . $userdetail['nik'], $newName);
                        $dir = base_url() . '/public/file_penugasan/' . $usertype . '/' . $userdetail['nik'] . '/' . $newName;
                    }



                    $id_tipe_file = $this->db->query("SELECT * FROM m_tipe_file
                    WHERE tipe_file = '" . $filetype . "'");
                    if ($id_tipe_file->getNumRows() > 0) {
                        $data['id_tipe_file'] = $id_tipe_file->getRow()->id;
                    }

                    $data['id_user_taruna'] = $userid;
                    $data['file_tugas'] = $dir;
                    $data['upload_date'] = date("Y-m-d H:m:s", time());
                    // $data['is_done'] = "1";
                }
            }
        } else {
            $data['file_tugas'] = $data['link_materi_tugas'];
        }
        $data['jenis_file_tugas'] = $data['jenisfile'];

        unset($data['jenisfile']);
        unset($data['link_materi_tugas']);

        parent::_insert('t_nilai_tugas', $data, $userid);
    }

    // begin datajadwal done
    function penjadwalanujian_load()
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
                    select a.id_kelompok_taruna as id_kelompok, b.id as id_ruangan, b.nama as nama_ruangan  from t_jadwal a left join m_ruang_kelas b on a.id_ruang_kelas = b.id where a.tanggal between '" . $tanggal_awal . "' and '" . $tanggal_akhir . "' group by a.id_kelompok_taruna, a.id_ruang_kelas
                    ) l on k.id_kelompok = l.id_kelompok ")->getResult();

        // select  c.id as id_kelompok, c.kelompok as nama_kelompok 
        // from m_sm_batalyon a
        // inner join t_kelompok_taruna_ b on a.id = b.id_batalyon
        // left join m_kelompok c on b.id_kelompok = c.id  
        // where a.id = '1' and a.is_deleted = 0 and b.is_deleted = 0 and c.is_deleted = 0 group by c.id


        $query = "SELECT  
                a.selected_date as tanggal,IF(a.selected_date<CURDATE() , '1' , '0') AS kunci,
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
                        e.id_jenis_ujian, IF(a.tanggal<CURDATE() , '1' , '0') AS kunci
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

    function penjadwalanujian_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('t_jadwal', $data, $userid);
    }

    function penjadwalanujian_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.*  from m_pangkat_pendidik a
            where a.id = '" . $data['id'] . "'";
        parent::_edit('t_jadwal', $data, null, $query);
    }

    function penjadwalanujian_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('t_jadwal', $data, $userid);
    }
    // end datajadwal done



    // begin penjadwalanujian done
    function pencapaianperkuliahan_load()
    {

        $query = "SELECT a.*, b.kelompok, c.judul, c.pertemuan_ke, d.mata_pelajaran from t_jadwal a
                    left join m_kelompok b on a.id_kelompok_taruna=b.id
                    left join t_bahan_ajar c on a.id_bahan_ajar=c.id
                    left join m_mata_pelajaran d on c.id_mata_pelajaran=d.id
                    where a.is_deleted='0' ";
        $where = ["a.judul_pertemuan", "b.kelompok", "c.pertemuan_ke", "d.mata_pelajaran"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function pencapaianperkuliahan_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('t_jadwal', $data, $userid);
    }

    function pencapaianperkuliahan_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.*, b.kelompok, c.judul, c.pertemuan_ke, d.mata_pelajaran from t_jadwal a
                    left join m_kelompok b on a.id_kelompok_taruna=b.id
                    left join t_bahan_ajar c on a.id_bahan_ajar=c.id
                    left join m_mata_pelajaran d on c.id_mata_pelajaran=d.id
            where a.id = '" . $data['id'] . "'";
        parent::_edit('t_jadwal', $data, null, $query);
    }

    function pencapaianperkuliahan_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('t_jadwal_copy', $data, $userid);
    }
    // end penjadwalanujian done


    function matapelajarantaruna_load()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT 
                        c.id,
                        c.kode_mk,
                        c.mata_pelajaran ,
                        ifnull(d.jml, 0) as jml
                    from m_user_taruna a 
                    left join t_program_studi_mata_pelajaran b
                        on a.id_semester = b.id_semester
                        and a.id_batalyon = b.id_batalyon
                    left join m_mata_pelajaran c
                        on b.id_mata_pelajaran = c.id
                    left join (select a.id_mata_pelajaran, a.id_batalyon, a.id_tingkatan_detail, count(*) as jml, b.id_semester from t_file_materi a inner join m_tingkatan_detail b on a.id_tingkatan_detail=b.id  group by a.id_mata_pelajaran, a.id_tingkatan_detail, a.id_batalyon) d on c.id=d.id_mata_pelajaran and a.id_batalyon=d.id_batalyon 
                    -- and a.id_semester=d.id_semester
                    where 
                        a.id_m_user = '" . $data['id_taruna'] . "' and
                        b.is_deleted = 0
                        ";

        $where = ["c.mata_pelajaran ", "c.kode_mk"];
        $data = json_decode($this->request->getPost('param'), true);
        $order = " c.mata_pelajaran ";

        parent::_loadDatatableorder($query, $where, $data, $order);
    }

    function nilaipelajaran_load()
    {
        $data = json_decode($this->request->getPost('param'), true);

        // $query = "SELECT 
        //                 c.id,
        //                 c.kode_mk,
        //                 c.mata_pelajaran ,
        //                 a.id_m_user,
        //                 a.id_semester,
        //                 a.id_batalyon
        //             FROM m_user_taruna a 
        //             LEFT JOIN t_program_studi_mata_pelajaran b
        //                 ON a.id_semester = b.id_semester
        //                 AND a.id_batalyon = b.id_batalyon
        //             LEFT JOIN m_mata_pelajaran c
        //                 ON b.id_mata_pelajaran = c.id
        //             WHERE 
        //                 a.id_m_user = '" . $data['id_taruna'] . "' and
        //                 b.is_deleted = 0
        //                 ";

        $query = "SELECT 
                        c.id,
                        c.kode_mk,
                        c.mata_pelajaran ,
                        b.id_aspek,
                        a.id_m_user,
                        a.id_semester,
                        a.id_batalyon,
                        
                        ifnull(d.id_mata_pelajaran, 0) as tugas,
                        ifnull(e.id_mata_pelajaran, 0) as pengetahuan,
                        ifnull(f.id_mata_pelajaran, 0) as keterampilan
                    FROM m_user_taruna a 
                    LEFT JOIN t_program_studi_mata_pelajaran b
                        ON a.id_semester = b.id_semester
                        AND a.id_batalyon = b.id_batalyon
                    LEFT JOIN m_mata_pelajaran c
                        ON b.id_mata_pelajaran = c.id
                    LEFT JOIN (SELECT t.nilai, t.log_nilai,  a.judul, a.deskripsi, a.waktu_pengumpulan, DATE_FORMAT(a.waktu_pengumpulan, '%d/%m/%y %H:%i') AS waktu, 
                        c.id_mata_pelajaran AS id_mata_pelajaran 
                        FROM t_nilai_tugas t
                        LEFT JOIN t_jadwal_tugas a ON t.id_jadwal_tugas = a.id and a.is_deleted='0'
                        LEFT JOIN t_jadwal b ON a.id_jadwal=b.id and b.is_deleted='0'
                        LEFT JOIN t_bahan_ajar c ON b.id_bahan_ajar=c.id and c.is_deleted='0'
                        WHERE a.is_deleted='0' AND t.id_user_taruna = '" . $data['id_taruna'] . "' group by c.id_mata_pelajaran) d on d.id_mata_pelajaran=b.id_mata_pelajaran
                    left join (SELECT * FROM t_penilaian_aspek_pengetahuan a where a.id_user_taruna='" . $data['id_taruna'] . "' ) e on e.id_mata_pelajaran=b.id_mata_pelajaran and e.id_semester=a.id_semester and e.id_batalyon=a.id_batalyon
                    left join (SELECT * FROM t_penilaian_aspek_keterampilan a where a.id_user_taruna='" . $data['id_taruna'] . "' ) f on f.id_mata_pelajaran=b.id_mata_pelajaran and f.id_semester=a.id_semester and f.id_batalyon=a.id_batalyon
                    
                    WHERE 
                        a.id_m_user = '" . $data['id_taruna'] . "' and
                        b.is_deleted = 0";

        $where = ["c.mata_pelajaran ", "c.kode_mk"];
        $data = json_decode($this->request->getPost('param'), true);
        $order = " c.mata_pelajaran ";

        parent::_loadDatatableorder($query, $where, $data, $order);
    }

    // end controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // Download -------------

    function absensiperkuliahan_download()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_kelompok = $data["id_kelompok"];
        $pertemuan_ke = $data["pertemuan_ke"];
        $id_batalyon = $data["id_batalyon"];

        $query = "SELECT  d.id_m_user, d.namataruna, d.noaklong, f.id_jadwal, d.photopath, c.pertemuan_ke, f.*
        from m_mata_pelajaran a
        left join t_program_studi_mata_pelajaran z on z.id_batalyon = z.id_batalyon and a.id = z.id_mata_pelajaran
        left join t_bahan_ajar c on a.id=c.id_mata_pelajaran and z.id_batalyon=c.id_batalyon
        left join m_user_taruna d on z.id_batalyon = d.id_batalyon
        inner join t_jadwal e on c.id=e.id_bahan_ajar
        inner join t_absensi f 
            on e.id=f.id_jadwal and d.id_m_user=f.id_taruna
        where c.id_mata_pelajaran= '" . $id_mata_pelajaran . "' and d.id_kelompok='" . $id_kelompok . "' and d.id_batalyon = '" . $id_batalyon . "' and c.id='" . $pertemuan_ke . "' and e.is_deleted='0'";



        $rs['semester'] = $this->db->query("SELECT a.semester from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester where b.id='" . $id_batalyon . "' ")->getRow()->semester;
        $rs['matapelajaran'] = $this->db->query("SELECT a.mata_pelajaran from m_mata_pelajaran a where a.id='" . $id_mata_pelajaran . "' ")->getRow()->mata_pelajaran;
        $rs['kelompoktaruna'] = $this->db->query("SELECT a.kelompok from m_kelompok a where a.id='" . $id_kelompok . "'")->getRow()->kelompok;
        $rs['data'] = $this->db->query($query)->getResult();

        $rs['nama_file'] = "Absensi Perkuliahan";

        echo json_encode($rs);
    }

    function pencapaianperkuliahan_download()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT a.*, b.kelompok, c.judul, c.pertemuan_ke, d.mata_pelajaran from t_jadwal a
                    left join m_kelompok b on a.id_kelompok_taruna=b.id
                    left join t_bahan_ajar c on a.id_bahan_ajar=c.id
                    left join m_mata_pelajaran d on c.id_mata_pelajaran=d.id
                    where a.is_deleted='0' ";


        $rs['data'] = $this->db->query($query)->getResult();

        $rs['nama_file'] = "Pencapaian Perkuliahan";

        echo json_encode($rs);
    }

    function inputnilai_download()
    {

        $data = json_decode($this->request->getPost('param'), true);
        $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_batalyon = $data["id_batalyon"];

        $where = ($data['type_code'] == 'pgh' ? "and h.id = '" . $userid . "'" : "");
        $query = "SELECT
            d.id,
            b.id_m_user as id_user_taruna,
            '" . $id_mata_pelajaran . "' as id_mata_pelajaran,
            b.id_semester,
            '" . $id_batalyon . "' as id_batalyon,
            b.namataruna,
            b.noaklong,
            c.kelompok,
            d.uts_ljk,
            d.uts_esai,
            d.uas_ljk,
            d.uas_esai,
            d.her_ljk,
            d.her_esai,
            d.proses_ajar,
            d.tugas,
            d.her,
            d.nilai_akhir,
            d.kategori,
            d.bobot,
            d.klasifikasi,
            f.esai as limit_esai,
            f.ljk as limit_ljk
        from m_user_taruna b
        inner join m_kelompok c
            on c.id = b.id_kelompok
        left join t_penilaian_aspek_pengetahuan d
            on d.id_user_taruna = b.id_m_user
            and d.id_semester = b.id_semester
            and d.id_batalyon = b.id_batalyon
            and d.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
        left join m_konversi_nilai_pengetahuan f
            on b.id_semester = f.id_semester
        left join m_sm_peleton g 
            on b.id_peleton = g.id
        left join m_user h 
            on h.id = g.id_user_pendidik
        where b.id_batalyon = '" . $id_batalyon . "' $where ";


        $rs['data'] = $this->db->query($query)->getResult();

        $rs['semester'] = $this->db->query("SELECT a.semester , b.batalyon from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester 
                                            where b.id='" . $id_batalyon . "' ")->getRow()->semester;
        $rs['batalyon'] = $this->db->query("SELECT a.semester , b.batalyon from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester 
                                            where b.id='" . $id_batalyon . "' ")->getRow()->batalyon;
        $rs['matapelajaran'] = $this->db->query("SELECT a.mata_pelajaran from m_mata_pelajaran a where a.id='" . $id_mata_pelajaran . "' ")->getRow()->mata_pelajaran;


        $rs['nama_file'] = "Nilai Pengetahuan " . $rs['batalyon'] . '-' . $rs['semester'] . '-' . $rs['matapelajaran'];

        echo json_encode($rs);
    }


    function penjadwalanujian_download()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        // echo json_encode($_POST);
        $id_batalyon = $data['id_batalyon'];

        $tanggal_awal = date("Y-m-d", strtotime(str_replace("/", "-", $data['tanggal'])));

        $tanggal_akhir = date("Y-m-d", strtotime("$tanggal_awal +4 day"));

        $header = $this->db->query("SELECT k.id_kelompok, k.nama_kelompok, if(l.id_ruangan is null,'',l.id_ruangan) as id_ruangan, if(l.nama_ruangan is null,'',l.nama_ruangan) as nama_ruangan
                from (
                    select  c.id as id_kelompok, c.kelompok as nama_kelompok from m_sm_batalyon a inner join m_user_taruna b  on a.id = b.id_batalyon and a.id_semester=b.id_semester left join m_kelompok c on b.id_kelompok = c.id where a.id = '" . $id_batalyon . "' and a.is_deleted = 0 and b.is_deleted = 0 and c.is_deleted = 0 group by c.id
                    ) k 
                left join (
                    select a.id_kelompok_taruna as id_kelompok, b.id as id_ruangan, b.nama as nama_ruangan  from t_jadwal a left join m_ruang_kelas b on a.id_ruang_kelas = b.id where a.tanggal between '" . $tanggal_awal . "' and '" . $tanggal_akhir . "' group by a.id_kelompok_taruna, a.id_ruang_kelas
                    ) l on k.id_kelompok = l.id_kelompok ")->getResult();

        $query = "SELECT  
                a.selected_date as tanggal,IF(a.selected_date<CURDATE() , '1' , '0') AS kunci,
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
                        e.id_jenis_ujian, IF(a.tanggal<CURDATE() , '1' , '0') AS kunci
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

    function inputnilaiketerampilan_download()
    {

        $data = json_decode($this->request->getPost('param'), true);
        $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_batalyon = $data["id_batalyon"];

        $where = ($data['type_code'] == 'pgh' ? "and h.id = '" . $userid . "'" : "");
        $query = "SELECT
            d.id,
            b.id_m_user as id_user_taruna,
            '" . $id_mata_pelajaran . "' as id_mata_pelajaran,
            b.id_semester,
            '" . $id_batalyon . "' as id_batalyon,
            b.namataruna,
            b.noaklong,
            c.kelompok,
            d.uts_ljk,
            d.uts_esai,
            d.uas_ljk,
            d.uas_esai,
            d.her_ljk,
            d.her_esai,
            d.praktek,
            d.proses_pelatihan,
            d.produk_pelatihan,
            d.her,
            d.nilai_akhir,
            d.kategori,
            d.bobot,
            d.klasifikasi,
            f.esai as limit_esai,
            f.ljk as limit_ljk
        from m_user_taruna b
        inner join m_kelompok c
            on c.id = b.id_kelompok
        left join t_penilaian_aspek_keterampilan d
            on d.id_user_taruna = b.id_m_user
            and d.id_semester = b.id_semester
            and d.id_batalyon = b.id_batalyon
            and d.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
        left join m_konversi_nilai_keterampilan f
            on b.id_semester = f.id_semester
        left join m_sm_peleton g 
            on b.id_peleton = g.id
        left join m_user h 
            on h.id = g.id_user_pendidik
        where b.id_batalyon = '" . $id_batalyon . "' " . $where;

        $rs['data'] = $this->db->query($query)->getResult();

        $rs['semester'] = $this->db->query("SELECT a.semester , b.batalyon from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester 
                                            where b.id='" . $id_batalyon . "' ")->getRow()->semester;
        $rs['batalyon'] = $this->db->query("SELECT a.semester , b.batalyon from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester 
                                            where b.id='" . $id_batalyon . "' ")->getRow()->batalyon;
        $rs['matapelajaran'] = $this->db->query("SELECT a.mata_pelajaran from m_mata_pelajaran a where a.id='" . $id_mata_pelajaran . "' ")->getRow()->mata_pelajaran;

        $rs['nama_file'] = "Nilai Keterampilan " . $rs['batalyon'] . '-' . $rs['semester'] . '-' . $rs['matapelajaran'];

        echo json_encode($rs);
    }

    function inputnilaikesehatan_download()
    {

        $data = json_decode($this->request->getPost('param'), true);
        $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_batalyon = $data["id_batalyon"];

        $where = ($data['type_code'] == 'pgh' ? "and h.id = '" . $userid . "'" : "");
        $query = "SELECT
            d.id,
            b.id_m_user as id_user_taruna,
            '" . $id_mata_pelajaran . "' as id_mata_pelajaran,
            b.id_semester,
            '" . $id_batalyon . "' as id_batalyon,
            b.namataruna,
            b.noaklong,
            c.kelompok,
            d.rikesla,
            d.insidentil,
            d.rikes,
            d.keswa,
            d.her,
            d.nilai_akhir,
            d.kategori,
            d.bobot,
            d.klasifikasi
        from m_user_taruna b
        inner join m_kelompok c
            on c.id = b.id_kelompok
        left join t_penilaian_aspek_kesehatan d
            on d.id_user_taruna = b.id_m_user
            and d.id_semester = b.id_semester
            and d.id_batalyon = b.id_batalyon
            and d.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
        left join m_sm_peleton e 
            on b.id_peleton = e.id
        left join m_user f 
            on f.id = e.id_user_pendidik
        where b.id_batalyon = '" . $id_batalyon . "' " . $where;

        $rs['data'] = $this->db->query($query)->getResult();

        $rs['semester'] = $this->db->query("SELECT a.semester , b.batalyon from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester 
                                            where b.id='" . $id_batalyon . "' ")->getRow()->semester;
        $rs['batalyon'] = $this->db->query("SELECT a.semester , b.batalyon from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester 
                                            where b.id='" . $id_batalyon . "' ")->getRow()->batalyon;
        $rs['matapelajaran'] = $this->db->query("SELECT a.mata_pelajaran from m_mata_pelajaran a where a.id='" . $id_mata_pelajaran . "' ")->getRow()->mata_pelajaran;

        $rs['nama_file'] = "Nilai Kesehatan " . $rs['batalyon'] . '-' . $rs['semester'] . '-' . $rs['matapelajaran'];

        echo json_encode($rs);
    }

    function inputnilaikarakter_download()
    {

        $data = json_decode($this->request->getPost('param'), true);
        $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_batalyon = $data["id_batalyon"];

        $where = ($data['type_code'] == 'pgh' ? "and h.id = '" . $userid . "'" : "");
        $query = "SELECT
            d.id,
            b.id_m_user as id_user_taruna,
            '" . $id_mata_pelajaran . "' as id_mata_pelajaran,
            b.id_semester,
            '" . $id_batalyon . "' as id_batalyon,
            b.namataruna,
            b.noaklong,
            c.kelompok,
            d.bulan_1,
            d.bulan_2,
            d.bulan_3,
            d.bulan_4,
            d.bulan_5,
            d.bulan_6,
            d.her,
            d.nilai_akhir,
            d.kategori,
            d.bobot,
            d.klasifikasi
        from m_user_taruna b
        inner join m_kelompok c
            on c.id = b.id_kelompok
        left join t_penilaian_aspek_karakter d
            on d.id_user_taruna = b.id_m_user
            and d.id_semester = b.id_semester
            and d.id_batalyon = b.id_batalyon
            and d.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
        left join m_sm_peleton e 
            on b.id_peleton = e.id
        left join m_user f 
            on f.id = e.id_user_pendidik
        where b.id_batalyon = '" . $id_batalyon . "' " . $where;

        $rs['data'] = $this->db->query($query)->getResult();

        $rs['semester'] = $this->db->query("SELECT a.semester , b.batalyon from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester 
                                            where b.id='" . $id_batalyon . "' ")->getRow()->semester;
        $rs['batalyon'] = $this->db->query("SELECT a.semester , b.batalyon from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester 
                                            where b.id='" . $id_batalyon . "' ")->getRow()->batalyon;
        $rs['matapelajaran'] = $this->db->query("SELECT a.mata_pelajaran from m_mata_pelajaran a where a.id='" . $id_mata_pelajaran . "' ")->getRow()->mata_pelajaran;

        $rs['nama_file'] = "Nilai Karakter " . $rs['batalyon'] . '-' . $rs['semester'] . '-' . $rs['matapelajaran'];

        echo json_encode($rs);
    }

    function inputnilaijasmani_download()
    {

        $data = json_decode($this->request->getPost('param'), true);
        $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_batalyon = $data["id_batalyon"];

        $where = ($data['type_code'] == 'pgh' ? "and h.id = '" . $userid . "'" : "");
        $query = "SELECT
            d.id,
            b.id_m_user as id_user_taruna,
            '" . $id_mata_pelajaran . "' as id_mata_pelajaran,
            b.id_semester,
            '" . $id_batalyon . "' as id_batalyon,
            b.namataruna,
            IF(b.id_gender = 1, 'Laki-laki', 'Perempuan') as gender,
            b.noaklong,
            c.kelompok,
            d.lari_jumlah,
            d.lari_nilai,
            d.pull_up_jumlah,
            d.pull_up_nilai,
            d.sit_up_jumlah,
            d.sit_up_nilai,
            d.push_up_jumlah,
            d.push_up_nilai,
            d.run_jumlah,
            d.run_nilai,
            d.samapta_a,
            d.samapta_b,
            d.her,
            d.her_2,
            d.id_syarat_penilaian,
            d.nilai_akhir,
            d.kategori,
            d.bobot,
            d.klasifikasi
        from m_user_taruna b
        inner join m_kelompok c
            on c.id = b.id_kelompok
        left join t_penilaian_aspek_jasmani d
            on d.id_user_taruna = b.id_m_user
            and d.id_semester = b.id_semester
            and d.id_batalyon = b.id_batalyon
            and d.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
        left join m_sm_peleton e 
            on b.id_peleton = e.id
        left join m_user f 
            on f.id = e.id_user_pendidik
        where b.id_batalyon = '" . $id_batalyon . "' " . $where;

        $rs['data'] = $this->db->query($query)->getResult();

        $rs['semester'] = $this->db->query("SELECT a.semester , b.batalyon from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester 
                                            where b.id='" . $id_batalyon . "' ")->getRow()->semester;
        $rs['batalyon'] = $this->db->query("SELECT a.semester , b.batalyon from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester 
                                            where b.id='" . $id_batalyon . "' ")->getRow()->batalyon;
        $rs['matapelajaran'] = $this->db->query("SELECT a.mata_pelajaran from m_mata_pelajaran a where a.id='" . $id_mata_pelajaran . "' ")->getRow()->mata_pelajaran;

        $rs['nama_file'] = "Nilai Jasmani " . $rs['batalyon'] . '-' . $rs['semester'] . '-' . $rs['matapelajaran'];

        echo json_encode($rs);
    }


    function datapesertaujian_download()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $id_kelompok = $data["id_kelompok"];
        $id_batalyon = $data["id_batalyon"];

        $query = "select
        f.id,
        a.id_mata_pelajaran,
        a.id as id_program_studi,
        a.id_batalyon,
        a.tahun_ajaran,
        e.id_m_user as id_user_taruna,
        e.namataruna as nama_taruna,
        e.noaklong,
        g.kehadiran,
        f.is_uas,
        f.is_uts,
        f.keterangan
        from 
            t_program_studi_mata_pelajaran a
        left join m_user_taruna e
            on a.id_batalyon = a.id_batalyon
        left join t_filter_taruna_ujian f
            on f.id_user_taruna = e.id
            and f.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
            and f.id_batalyon = '" . $id_batalyon . "'
        left join (
                select m.id_mata_pelajaran,
                    m.id_batalyon,
                    k.id_taruna,
                    sum(k.is_absen)/count(*)*100 as kehadiran
                from t_absensi k
                left join t_jadwal l
                    on k.id_jadwal = l.id
                left join t_bahan_ajar m
                    on l.id_bahan_ajar = m.id
                where m.is_ujian = 0
                    and k.is_deleted = 0
                    and m.id_batalyon = '" . $id_batalyon . "'
                group by m.id_batalyon, k.id_taruna
        ) g
        on g.id_mata_pelajaran = a.id_mata_pelajaran
            and g.id_batalyon = a.id_batalyon
            and g.id_taruna = e.id_m_user 
        where a.is_deleted = 0
        and a.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
            and e.id_kelompok ='" . $id_kelompok . "'";

        $rs['data'] = $this->db->query($query)->getResult();

        $rs['semester'] = $this->db->query("SELECT a.semester , b.batalyon from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester 
                                            where b.id='" . $id_batalyon . "' ")->getRow()->semester;
        $rs['batalyon'] = $this->db->query("SELECT a.semester , b.batalyon from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester 
                                            where b.id='" . $id_batalyon . "' ")->getRow()->batalyon;
        $rs['matapelajaran'] = $this->db->query("SELECT a.mata_pelajaran from m_mata_pelajaran a where a.id='" . $id_mata_pelajaran . "' ")->getRow()->mata_pelajaran;
        $rs['kelompok'] = $this->db->query("SELECT kelompok FROM `m_kelompok` a WHERE a.id='" . $id_kelompok . "' ")->getRow()->kelompok;

        $rs['nama_file'] = "Nilai Jasmani " . $rs['batalyon'] . '-' . $rs['semester'] . '-' . $rs['matapelajaran'];

        echo json_encode($rs);
    }

    function rekapnilaitugas_load()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $id_pendidik = json_decode($this->request->getPost('userid'), true);

        $query = "select a.* from (select 
        f.*,
        a.id_user_pendidik,
        e.deskripsi,
        g.namagadik as pendidik,
        CONCAT(h.semester,'-' ,c.batalyon) as info_semester,
        b.kelompok,
        a.id_kelompok_taruna,
        c.id_semester,
        e.mata_pelajaran
    from t_jadwal_tugas z
    left join t_jadwal a
    on a.id = z.id_jadwal
    left join m_kelompok b
                on a.id_kelompok_taruna = b.id
            left join m_user_taruna i
                on i.id_kelompok=b.id
            left join m_sm_batalyon c
                on c.id = i.id_batalyon
            left join t_bahan_ajar d
                on d.id = a.id_bahan_ajar
            left join m_mata_pelajaran e
                on e.id = d.id_mata_pelajaran
            left join t_pendidik_mata_pelajaran f
                on d.id_mata_pelajaran=f.id_mata_pelajaran 
                and d.id_batalyon=f.id_batalyon
            left join m_user_pendidik g
                on f.id_pendidik=g.id_m_user 
            left join m_semester h
                on c.id_semester=h.id
            where 
                a.is_deleted = 0
                and z.is_deleted = 0
                and f.id_pendidik = '" . $id_pendidik . "'
                and c.id_semester = d.id_semester
                and d.id_batalyon = c.id
            group by f.id, a.id_kelompok_taruna) a";

        // echo $query;

        $where = ["a.mata_pelajaran"];

        parent::_loadDatatable($query, $where, $data);
    }

    function rekapnilaitugas_load_kelas()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $id_mata_pelajaran = $data['id_mata_pelajaran'];
        $id_batalyon = $data['id_batalyon'];
        $id_semester = $data['id_semester'];
        $id_kelompok = $data['id_kelompok'];

        // $query = "SET @sql = NULL;
        //             ;
        //             SET @sql = CONCAT('select rangkuman.namataruna, rangkuman.noaklong,',@sql,'
        //             from (select d.namataruna, d.noaklong, c.pertemuan_ke, g.nilai from m_mata_pelajaran a
        //             left join t_bahan_ajar c on a.id = c.id_mata_pelajaran and c.is_deleted = 0
        //             left join m_user_taruna d on c.id_batalyon = d.id_batalyon
        //             inner join t_jadwal e on c.id = e.id_bahan_ajar and e.is_deleted = 0
        //             inner join t_jadwal_tugas f on e.id = f.id_jadwal and f.is_deleted = 0
        //             left join t_nilai_tugas g on f.id = g.id_jadwal_tugas and g.id_user_taruna = d.id_m_user and g.is_deleted = 0
        //             where d.id_kelompok = '" . $id_kelompok . "'
        //             and c.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
        //             and c.id_batalyon = '" . $id_batalyon . "'
        //             and c.id_semester = '" . $id_semester . "'
        //             ) rangkuman
        //             group by rangkuman.noaklong order by rangkuman.namataruna');
        //             PREPARE stmt FROM @sql;
        //             EXECUTE stmt;
        //             ";

        $field = $this->db->query("SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(IF(rangkuman.pertemuan_ke = \"', a.pertemuan_ke, '\", rangkuman.nilai, 0)) AS ', concat('pertemuan_ke_',a.pertemuan_ke) )) as q
                    from t_bahan_ajar a
                    inner join t_jadwal b on a.id = b.id_bahan_ajar and b.is_deleted = 0
                    inner join t_jadwal_tugas c on b.id = c.id_jadwal and c.is_deleted = 0
                    where a.is_deleted = 0 
                    and a.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
                    and a.id_batalyon = '" . $id_batalyon . "'
                    and a.id_semester = '" . $id_semester . "'
                    and b.id_kelompok_taruna = '" . $id_kelompok . "' ")->getRow();

        $query = "select rangkuman.id,rangkuman.id_m_user , rangkuman.id_mata_pelajaran , rangkuman.id_semester , rangkuman.id_batalyon ,rangkuman.namataruna, rangkuman.noaklong " . (!is_null($field->q) ? "," . $field->q : "") . " ,round( ifnull(sum(rangkuman.nilai)/count(rangkuman.pertemuan_ke),0) ,2) as rata
                    from (select h.id, c.id_mata_pelajaran,c.id_batalyon,c.id_semester, d.id_m_user,d.namataruna, d.noaklong, c.pertemuan_ke, g.nilai from m_mata_pelajaran a
                    left join t_bahan_ajar c on a.id = c.id_mata_pelajaran and c.is_deleted = 0
                    left join m_user_taruna d on c.id_batalyon = d.id_batalyon
                    inner join t_jadwal e on c.id = e.id_bahan_ajar and e.is_deleted = 0
                    inner join t_jadwal_tugas f on e.id = f.id_jadwal and f.is_deleted = 0
                    inner join t_nilai_tugas g on f.id = g.id_jadwal_tugas and g.id_user_taruna = d.id_m_user and g.is_deleted = 0
                    left join t_penilaian_aspek_pengetahuan h on d.id_m_user=h.id_user_taruna and h.id_mata_pelajaran=c.id_mata_pelajaran and h.id_semester=c.id_semester and h.id_batalyon=c.id_batalyon
                    where d.id_kelompok = '" . $id_kelompok . "'
                    and c.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
                    and c.id_batalyon = '" . $id_batalyon . "'
                    and c.id_semester = '" . $id_semester . "'
                    ) rangkuman
                    group by rangkuman.noaklong order by rangkuman.namataruna";
        // echo json_encode($query);



        // echo $query;

        // $rs['data'] = $this->db->query($query)->getResult();
        // echo json_encode($query);

        parent::_loadlist($data, $query);
    }

    function rekapnilaitugas_load_proses()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $id_mata_pelajaran = $data['id_mata_pelajaran'];
        $id_batalyon = $data['id_batalyon'];
        $id_semester = $data['id_semester'];
        $id_kelompok = $data['id_kelompok'];

        $field = $this->db->query("SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(IF(rangkuman.pertemuan_ke = \"', a.pertemuan_ke, '\", rangkuman.poin, 0)) AS ', concat('pertemuan_ke_',a.pertemuan_ke) )) as q
                    from t_bahan_ajar a
                    inner join t_jadwal b on a.id = b.id_bahan_ajar and b.is_deleted = 0
                    left join t_penilaian_aktifitas_pembelajaran c on b.id = c.id_jadwal and c.is_deleted = 0
                    where a.is_deleted = 0 
                    and a.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
                    and a.id_batalyon = '" . $id_batalyon . "'
                    and a.id_semester = '" . $id_semester . "'
                    and b.id_kelompok_taruna = '" . $id_kelompok . "' ")->getRow();

        $query = "SELECT rangkuman.id,rangkuman.id_m_user , rangkuman.id_mata_pelajaran , rangkuman.id_semester , rangkuman.id_batalyon ,rangkuman.namataruna, rangkuman.na_proses , rangkuman.noaklong " . (!is_null($field->q) ? "," . $field->q : "") . " ,(rangkuman.na_proses+round( ifnull(sum(rangkuman.poin),0) ,2)) as rata
                    from (select h.id, c.id_mata_pelajaran,c.id_batalyon,c.id_semester, d.id_m_user,d.namataruna, d.noaklong, c.pertemuan_ke, g.poin, k.na_proses from m_mata_pelajaran a
                    left join t_bahan_ajar c on a.id = c.id_mata_pelajaran and c.is_deleted = 0
                    left join m_user_taruna d on c.id_batalyon = d.id_batalyon
                    inner join t_jadwal e on c.id = e.id_bahan_ajar and e.is_deleted = 0
                    left join t_penilaian_aktifitas_pembelajaran f on e.id=f.id_jadwal and d.id_m_user=f.id_user_taruna and f.is_deleted = 0 
                    left join m_penilaian_aktivitas_pembelajaran g on f.id_aktivitas=g.id
                    left join t_penilaian_aspek_pengetahuan h on d.id_m_user=h.id_user_taruna and h.id_mata_pelajaran=c.id_mata_pelajaran and h.id_semester=c.id_semester and h.id_batalyon=c.id_batalyon
                    inner join m_sm_batalyon i on d.id_batalyon=i.id
                    inner join m_tingkatan_detail j on i.id_semester=j.id
                    inner join m_tingkatan k on j.id_tingkatan=k.id
                    where d.id_kelompok = '" . $id_kelompok . "'
                    and c.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
                    and c.id_batalyon = '" . $id_batalyon . "'
                    and c.id_semester = '" . $id_semester . "'
                    ) rangkuman
                    group by rangkuman.noaklong order by rangkuman.namataruna";

        // echo $query;
        parent::_loadlist($data, $query);
    }

    function rekapnilaitugas_load_kelas_old()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $id_mata_pelajaran = $data['id_mata_pelajaran'];
        $id_batalyon = $data['id_batalyon'];
        $id_semester = $data['id_semester'];
        $id_kelompok = $data['id_kelompok'];

        $query = "SELECT 
            a.id as id_tugas,
            b.kelompok,
            CONCAT(h.semester,' - ' ,c.batalyon) as info_semester,
            e.mata_pelajaran,
            d.pertemuan_ke
        from 
        t_jadwal_tugas z
        left join t_jadwal a
        on a.id = z.id_jadwal
        left join m_kelompok b
                    on a.id_kelompok_taruna = b.id
        left join m_user_taruna i
                    on i.id_kelompok=b.id
                left join m_sm_batalyon c
                    on c.id = i.id_batalyon
                left join t_bahan_ajar d
                    on d.id = a.id_bahan_ajar
                left join m_mata_pelajaran e
                    on e.id = d.id_mata_pelajaran
                left join t_pendidik_mata_pelajaran f
                    on d.id_mata_pelajaran=f.id_mata_pelajaran 
                    and d.id_batalyon=f.id_batalyon
                left join m_user_pendidik g
                    on f.id_pendidik=g.id_m_user 
                left join m_semester h
                    on c.id_semester=h.id
                where 
                    a.is_deleted = 0
                    and z.is_deleted = 0
                    and a.id_kelompok_taruna = '" . $id_kelompok . "'
                    and e.id = '" . $id_mata_pelajaran . "'
                    and c.id = '" . $id_batalyon . "'
                    and h.id = '" . $id_semester . "'
                group by z.id";

        // echo $query;

        // $rs['data'] = $this->db->query($query)->getResult();
        // echo json_encode($rs);

        parent::_loadlist($data, $query);
    }

    function rekapnilaitugas_save_savenilai()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        parent::_insertbatch('t_penilaian_aspek_pengetahuan', $data, $userid);
    }


    function rankingtaruna_download()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT rapor.id_m_user as id, rapor.namataruna, rapor.nama_gender, rapor.noaklong, rapor.batalyon, rapor.kelompok, rapor.photopath, rapor.semester, 
        ROUND(rapor.rata_pengetahuan, 2) as rata_pengetahuan,
        ROUND(rapor.rata_keterampilan, 2) as rata_keterampilan,
        ROUND(rapor.rata_karakter, 2) as rata_karakter,
        ROUND(rapor.rata_kesehatan, 2) as rata_kesehatan,
        ROUND(rapor.rata_jasmani, 2) as rata_jasmani,
        CAST(ROUND((rapor.rata_karakter + rapor.rata_pengetahuan + 
        	   rapor.rata_keterampilan + rapor.rata_jasmani + 
        	   rapor.rata_kesehatan)/5,2) AS DECIMAL(10,2)) as rerata,
        RANK() OVER(ORDER BY rapor.rata_pengetahuan DESC) pengetahuan_rank,   
        RANK() OVER(ORDER BY rapor.rata_keterampilan DESC) keterampilan_rank,
        RANK() OVER(ORDER BY rapor.rata_kesehatan DESC) kesehatan_rank,
        RANK() OVER(ORDER BY rapor.rata_karakter DESC) karakter_rank,
        RANK() OVER(ORDER BY rapor.rata_jasmani DESC) jasmani_rank,
        RANK() OVER(ORDER BY ROUND((rapor.rata_karakter + rapor.rata_pengetahuan + 
        	   rapor.rata_keterampilan + rapor.rata_jasmani + 
        	   rapor.rata_kesehatan)/5,2) DESC) rerata_rank
        from (SELECT a.id_m_user, a.namataruna, a.nama_gender, a.noaklong, a.batalyon, a.kelompok, a.photopath, max(a.semester) as semester,
        group_concat(distinct if(a.aspek='Pengetahuan',a.nilai,null)) as pengetahuan,
        group_concat(distinct if(a.aspek='Keterampilan',a.nilai,null)) as keterampilan,
        group_concat(distinct if(a.aspek='Karakter',a.nilai,null)) as karakter,
        group_concat(distinct if(a.aspek='Kesehatan',a.nilai,null)) as kesehatan,
        group_concat(distinct if(a.aspek='Jasmani',a.nilai,null)) as jasmani,
        sum(if(a.aspek='Pengetahuan',a.jml,0))/sum(if(a.aspek='Pengetahuan',a.pembagi,0)) as rata_pengetahuan,
        sum(if(a.aspek='Keterampilan',a.jml,0))/sum(if(a.aspek='Keterampilan',a.pembagi,0)) as rata_keterampilan,
        sum(if(a.aspek='Karakter',a.jml,0))/sum(if(a.aspek='Karakter',a.pembagi,0)) as rata_karakter,
        sum(if(a.aspek='Kesehatan',a.jml,0))/sum(if(a.aspek='Kesehatan',a.pembagi,0)) as rata_kesehatan,
        sum(if(a.aspek='Jasmani',a.jml,0))/sum(if(a.aspek='Jasmani',a.pembagi,0)) as rata_jasmani
        from (select rangkuman.id_m_user, rangkuman.namataruna, rangkuman.noaklong, rangkuman.nama_gender, rangkuman.batalyon, rangkuman.kelompok, rangkuman.photopath, rangkuman.aspek, 
        json_arrayagg(json_object(rangkuman.mata_pelajaran,ifnull(rangkuman.nilai_akhir,0))) as nilai,
        sum(rangkuman.nilai_akhir) as jml, count(1) as pembagi, group_concat(distinct rangkuman.semester separator '/') as semester
        from (select c.id_m_user, c.namataruna, c.noaklong, if(c.id_gender=1, 'Taruna' , 'Taruni') as nama_gender,
              i.batalyon, j.kelompok, c.photopath, a.mata_pelajaran, x.semester,
        CASE
            WHEN b.id_aspek = '1' THEN d.nilai_akhir
            WHEN b.id_aspek = '2' THEN e.nilai_akhir
            WHEN b.id_aspek = '3' THEN f.nilai_akhir
            WHEN b.id_aspek = '4' THEN g.nilai_akhir
            WHEN b.id_aspek = '5' THEN h.nilai_akhir                    
        END as nilai_akhir,
        z.aspek from m_mata_pelajaran a
        left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
        left join m_user_taruna c on c.id_batalyon = b.id_batalyon
        left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
        left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
        left join t_penilaian_aspek_karakter f on f.id_semester = b.id_semester and f.id_user_taruna = c.id_m_user and f.is_deleted = 0
        left join t_penilaian_aspek_kesehatan g on g.id_semester = b.id_semester and g.id_user_taruna = c.id_m_user and g.is_deleted = 0
        left join t_penilaian_aspek_jasmani h on h.id_semester = b.id_semester and h.id_user_taruna = c.id_m_user and h.is_deleted = 0
        left join m_sm_batalyon i on c.id_batalyon = i.id
        left join m_kelompok j on c.id_kelompok = j.id
        left join m_aspek z on z.id = b.id_aspek
        left join m_semester x on x.id = b.id_semester
        where a.is_deleted = 0
        and b.id_batalyon = '" . $data['id_batalyon'] . "'
        and b.id_semester = '" . $data['id_semester'] . "'
        group by a.id, c.noaklong) rangkuman
        group by rangkuman.noaklong, rangkuman.aspek) a
        group by a.noaklong) rapor";



        // $datasemester = "SELECT
        //                     a.semester,
        //                     b.tingkatan
        //                 FROM m_semester a 
        //                 LEFT JOIN m_tingkatan b
        //                     ON a.id_tingkat = b.id
        //                 WHERE a.id ='" . $id_semester . "' ";

        // $rs['datasemester'] = $this->db->query($datasemester)->getRow();

        $rs['data'] = $this->db->query($query)->getResult();
        $rs['nama_file'] = "RANKING TARUNA";

        echo json_encode($rs);
    }

    function rankingpersemester_download()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT rapor.id_m_user as id, rapor.namataruna, rapor.nama_gender, rapor.noaklong, rapor.batalyon, rapor.kelompok, rapor.photopath, rapor.semester, 
        ROUND(((2.5*rapor.rata_pengetahuan)+(2.5*rapor.rata_keterampilan)+(2.5*rapor.rata_karakter)+rata_kesehatan+(1.5*rata_jasmani))/10,2) as nap,
        ROUND((rata_pengetahuan+rata_keterampilan)/2,2) as npk,
        ROUND(rata_karakter,2) as nsp,
        ROUND(((6*rapor.rata_jasmani)+(4*rapor.rata_kesehatan))/10,2) as njk,
        RANK() OVER(ORDER BY ROUND(((2.5*rapor.rata_pengetahuan)+(2.5*rapor.rata_keterampilan)+(2.5*rapor.rata_karakter)+rata_kesehatan+(1.5*rata_jasmani))/10,2) DESC) nap_rank,   
        RANK() OVER(ORDER BY ROUND((rata_pengetahuan+rata_keterampilan)/2,2) DESC) npk_rank,
        RANK() OVER(ORDER BY ROUND(rata_karakter,2) DESC) nsp_rank,
        RANK() OVER(ORDER BY ROUND(((6*rapor.rata_jasmani)+(4*rapor.rata_kesehatan))/10,2)  DESC) njk_rank
        from (select a.id_m_user, a.namataruna, a.nama_gender, a.noaklong, a.batalyon, a.kelompok, a.photopath, max(a.semester) as semester,
        group_concat(distinct if(a.aspek='Pengetahuan',a.nilai,null)) as pengetahuan,
        group_concat(distinct if(a.aspek='Keterampilan',a.nilai,null)) as keterampilan,
        group_concat(distinct if(a.aspek='Karakter',a.nilai,null)) as karakter,
        group_concat(distinct if(a.aspek='Kesehatan',a.nilai,null)) as kesehatan,
        group_concat(distinct if(a.aspek='Jasmani',a.nilai,null)) as jasmani,
        sum(if(a.aspek='Pengetahuan',a.jml,0))/sum(if(a.aspek='Pengetahuan',a.pembagi,0)) as rata_pengetahuan,
        sum(if(a.aspek='Keterampilan',a.jml,0))/sum(if(a.aspek='Keterampilan',a.pembagi,0)) as rata_keterampilan,
        sum(if(a.aspek='Karakter',a.jml,0))/sum(if(a.aspek='Karakter',a.pembagi,0)) as rata_karakter,
        sum(if(a.aspek='Kesehatan',a.jml,0))/sum(if(a.aspek='Kesehatan',a.pembagi,0)) as rata_kesehatan,
        sum(if(a.aspek='Jasmani',a.jml,0))/sum(if(a.aspek='Jasmani',a.pembagi,0)) as rata_jasmani
        from (select rangkuman.id_m_user, rangkuman.namataruna, rangkuman.nama_gender, rangkuman.noaklong, rangkuman.batalyon, rangkuman.kelompok, rangkuman.photopath, rangkuman.aspek, 
        json_arrayagg(json_object(rangkuman.mata_pelajaran,ifnull(rangkuman.nilai_akhir,0))) as nilai,
        sum(rangkuman.nilai_akhir) as jml, count(1) as pembagi, group_concat(distinct rangkuman.semester separator '/') as semester
        from (select c.id_m_user, c.namataruna, c.noaklong, if(c.id_gender=1, 'Taruna' , 'Taruni') as nama_gender, 
              i.batalyon, j.kelompok, c.photopath, a.mata_pelajaran, x.semester,
        CASE
            WHEN b.id_aspek = '1' THEN d.nilai_akhir
            WHEN b.id_aspek = '2' THEN e.nilai_akhir
            WHEN b.id_aspek = '3' THEN f.nilai_akhir
            WHEN b.id_aspek = '4' THEN g.nilai_akhir
            WHEN b.id_aspek = '5' THEN h.nilai_akhir                    
        END as nilai_akhir,
        z.aspek from m_mata_pelajaran a
        left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
        left join m_user_taruna c on c.id_batalyon = b.id_batalyon
        left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
        left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
        left join t_penilaian_aspek_karakter f on f.id_semester = b.id_semester and f.id_user_taruna = c.id_m_user and f.is_deleted = 0
        left join t_penilaian_aspek_kesehatan g on g.id_semester = b.id_semester and g.id_user_taruna = c.id_m_user and g.is_deleted = 0
        left join t_penilaian_aspek_jasmani h on h.id_semester = b.id_semester and h.id_user_taruna = c.id_m_user and h.is_deleted = 0
        left join m_sm_batalyon i on c.id_batalyon = i.id
        left join m_kelompok j on c.id_kelompok = j.id
        left join m_aspek z on z.id = b.id_aspek
        left join m_semester x on x.id = b.id_semester
        where a.is_deleted = 0
        and b.id_batalyon = '" . $data['id_batalyon'] . "'
        and b.id_semester = '" . $data['id_semester'] . "'
        group by a.id, c.noaklong) rangkuman
        group by rangkuman.noaklong, rangkuman.aspek) a
        group by a.noaklong) rapor";

        // $datasemester = "SELECT
        //                     a.semester,
        //                     b.tingkatan
        //                 FROM m_semester a 
        //                 LEFT JOIN m_tingkatan b
        //                     ON a.id_tingkat = b.id
        //                 WHERE a.id ='" . $id_semester . "' ";

        // $rs['datasemester'] = $this->db->query($datasemester)->getRow();

        $rs['data'] = $this->db->query($query)->getResult();
        $rs['nama_file'] = "RANKING TARUNA";

        echo json_encode($rs);
    }

    function rankingpertingkat_download()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT rapor.*, 
        RANK() OVER(ORDER BY rapor.rank_aspek DESC) rank_aspek_rank,   
        RANK() OVER(ORDER BY rapor.rank_peng_ket DESC) rank_peng_ket_rank,
        RANK() OVER(ORDER BY rapor.rank_karakter DESC) rank_karakter_rank,
        RANK() OVER(ORDER BY rapor.rank_jas_kes DESC) rank_jas_kes_rank
        from (select ranking.id_m_user as id, ranking.namataruna, ranking.noaklong, ranking.nama_gender, ranking.batalyon, ranking.kelompok, ranking.photopath, ranking.semester,
        round(sum(((2.5*rata_pengetahuan)+(2.5*rata_keterampilan)+(2.5*rata_karakter)+rata_kesehatan+(1.5*rata_jasmani))/10)/2,2) as rank_aspek, 
        round(((sum(rata_pengetahuan)/2) + (sum(rata_keterampilan)/2))/2,2) as rank_peng_ket,
        round(sum(rata_karakter)/2,2) as rank_karakter, round(((sum(rata_kesehatan)/2) + (sum(rata_jasmani)/2))/2,2) as rank_jas_kes
        from (select a.id_m_user, a.namataruna, a.nama_gender, a.noaklong, a.batalyon, a.kelompok, a.photopath,  max(a.semester) as semester,
        sum(if(a.aspek='Pengetahuan',a.jml,0))/sum(if(a.aspek='Pengetahuan',a.pembagi,0)) as rata_pengetahuan,
        sum(if(a.aspek='Keterampilan',a.jml,0))/sum(if(a.aspek='Keterampilan',a.pembagi,0)) as rata_keterampilan,
        sum(if(a.aspek='Karakter',a.jml,0))/sum(if(a.aspek='Karakter',a.pembagi,0)) as rata_karakter,
        sum(if(a.aspek='Kesehatan',a.jml,0))/sum(if(a.aspek='Kesehatan',a.pembagi,0)) as rata_kesehatan,
        sum(if(a.aspek='Jasmani',a.jml,0))/sum(if(a.aspek='Jasmani',a.pembagi,0)) as rata_jasmani
        from (select rangkuman.id_m_user, rangkuman.namataruna, rangkuman.noaklong, rangkuman.nama_gender, rangkuman.batalyon, rangkuman.kelompok, rangkuman.photopath, rangkuman.aspek, 
        json_arrayagg(json_object(rangkuman.mata_pelajaran,ifnull(rangkuman.nilai_akhir,0))) as nilai,
        sum(rangkuman.nilai_akhir) as jml, count(1) as pembagi, group_concat(distinct rangkuman.semester separator '/') as semester, rangkuman.id_semester
        from (select c.id_m_user, c.namataruna, c.noaklong, if(c.id_gender=1, 'Taruna' , 'Taruni') as nama_gender,
              i.batalyon, j.kelompok, c.photopath, a.mata_pelajaran, x.semester, b.id_semester,
        CASE
            WHEN b.id_aspek = '1' THEN d.nilai_akhir
            WHEN b.id_aspek = '2' THEN e.nilai_akhir
            WHEN b.id_aspek = '3' THEN f.nilai_akhir
            WHEN b.id_aspek = '4' THEN g.nilai_akhir
            WHEN b.id_aspek = '5' THEN h.nilai_akhir                    
        END as nilai_akhir,
        z.aspek from m_mata_pelajaran a
        left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
        left join m_user_taruna c on c.id_batalyon = b.id_batalyon
        left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
        left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
        left join t_penilaian_aspek_karakter f on f.id_mata_pelajaran = a.id and f.id_semester = b.id_semester and f.id_user_taruna = c.id_m_user 
        left join t_penilaian_aspek_kesehatan g on g.id_mata_pelajaran = a.id and g.id_semester = b.id_semester and g.id_user_taruna = c.id_m_user 
        left join t_penilaian_aspek_jasmani h on h.id_mata_pelajaran = a.id and h.id_semester = b.id_semester and h.id_user_taruna = c.id_m_user 
            left join m_sm_batalyon i on c.id_batalyon = i.id
        left join m_kelompok j on c.id_kelompok = j.id
        left join m_aspek z on z.id = b.id_aspek
        left join m_semester x on x.id = b.id_semester
        where a.is_deleted = 0
        and b.id_batalyon = '" . $data['id_batalyon'] . "'
        and x.id_tingkat = '" . $data['id_tingkat'] . "'
        group by a.id, c.noaklong, b.id_semester) rangkuman
        group by rangkuman.noaklong, rangkuman.aspek) a
        group by a.noaklong, a.id_semester) ranking
        group by ranking.noaklong) rapor";

        // $datasemester = "SELECT
        //                     a.semester,
        //                     b.tingkatan
        //                 FROM m_semester a 
        //                 LEFT JOIN m_tingkatan b
        //                     ON a.id_tingkat = b.id
        //                 WHERE a.id ='" . $id_semester . "' ";

        // $rs['datasemester'] = $this->db->query($datasemester)->getRow();

        $rs['data'] = $this->db->query($query)->getResult();
        $rs['nama_file'] = "RANKING TARUNA";

        echo json_encode($rs);
    }

    function rankingtaruna_load()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $batalyon = $data['batalyon'] == '' ? '' : " and a.id_batalyon = '" . $data['batalyon'] . "' ";
        $gender = $data['gender'] == '' ? '' : " and a.id_gender = '" . $data['gender'] . "' ";

        $query = "SELECT * FROM ( SELECT * FROM (          
            SELECT a.*,  @curRank := @curRank + 1 as ranking from     	 
            (SELECT a.id_m_user as id, a.noaklong, a.namataruna, a.photopath, a.batalyon, if(a.id_gender=1, 'Taruna' , 'Taruni') as nama_gender,
                CAST(b.karakter AS DECIMAL(10,2)) as karakter, 
                CAST(c.pengetahuan AS DECIMAL(10,2)) as pengetahuan , 
                CAST(d.keterampilan AS DECIMAL(10,2)) as keterampilan, 
                CAST(e.jasmani AS DECIMAL(10,2)) as jasmani, 
                CAST(f.kesehatan AS DECIMAL(10,2)) as kesehatan,
                CAST(ROUND((b.karakter + c.pengetahuan + d.keterampilan + e.jasmani + f.kesehatan)/5,2) AS DECIMAL(10,2)) as rerata               				  
                from (SELECT a.*, b.batalyon FROM m_user_taruna a 
                      LEFT JOIN m_sm_batalyon b on a.id_batalyon=b.id
                      WHERE a.is_deleted = 0
                      $batalyon
                      $gender) a 
                        left join
                        (SELECT 
                    a.nilai_akhir as 'karakter', a.id_semester, a.id_user_taruna
                FROM t_penilaian_aspek_karakter a 
                LEFT JOIN m_mata_pelajaran b
                    ON a.id_mata_pelajaran = b.id       
                WHERE a.is_deleted = 0
                    AND b.is_deleted = 0) b 
                            on a.id_m_user = b.id_user_taruna
                            left join
                            (SELECT 
                    ROUND(SUM(a.nilai_akhir)/COUNT(*),2) as 'pengetahuan',
                    a.id_semester, a.id_user_taruna
                FROM t_penilaian_aspek_pengetahuan a 
                LEFT JOIN m_mata_pelajaran b
                    ON a.id_mata_pelajaran = b.id       
                WHERE a.is_deleted = 0
                    AND b.is_deleted = 0
                    group by a.id_user_taruna) c        		
                    on a.id_m_user = c.id_user_taruna
                left join
            (SELECT 
                    ROUND(SUM(a.nilai_akhir)/COUNT(*),2) as 'keterampilan',
                    a.id_semester, a.id_user_taruna
                FROM t_penilaian_aspek_keterampilan a 
                LEFT JOIN m_mata_pelajaran b
                    ON a.id_mata_pelajaran = b.id       
                WHERE a.is_deleted = 0
                    AND b.is_deleted = 0
                    group by a.id_user_taruna) d
                            on a.id_m_user = d.id_user_taruna
                            left join
                            (SELECT 
                    a.nilai_akhir as 'jasmani', a.id_semester, a.id_user_taruna
                FROM t_penilaian_aspek_jasmani a 
                LEFT JOIN m_mata_pelajaran b
                    ON a.id_mata_pelajaran = b.id
                WHERE a.is_deleted = 0
                    AND b.is_deleted = 0) e
                on a.id_m_user = e.id_user_taruna
                left join
                            (SELECT 
                    a.nilai_akhir as 'kesehatan', a.id_semester, a.id_user_taruna
                FROM t_penilaian_aspek_kesehatan a 
                LEFT JOIN m_mata_pelajaran b
                    ON a.id_mata_pelajaran = b.id
                WHERE a.is_deleted = 0
                    AND b.is_deleted = 0) f
                            on a.id_m_user = f.id_user_taruna
                                order by rerata desc) a, (SELECT @curRank := 0) r) a ) z where z.id!='' ";

        // echo $query;

        $where = ["z.namataruna", "z.noaklong"];

        $order = "ranking";

        parent::_loadDatatableorder($query, $where, $data, $order, 'asc');
        // parent::_loadDatatable($query, $where, $data);

    }

    function rankingtaruna_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $semester = $data['id_semester'] ? " and a.id_semester = '" . $data['id_semester'] . "' " : '';
        $semester_absen = $data['id_semester'] ? " and b.id_semester = '" . $data['id_semester'] . "' " : '';
        $query = "SELECT a.noaklong, a.namataruna, a.nik, a.telp, a.email, a.photopath, a.batalyon, a.kompi, a.peleton, a.kelompok, if(a.id_gender=1, 'Laki-laki' , 'Perempuan') as nama_gender,
        CAST(b.karakter AS DECIMAL(10,2)) as karakter, 
        CAST(c.pengetahuan AS DECIMAL(10,2)) as pengetahuan , 
        CAST(d.keterampilan AS DECIMAL(10,2)) as keterampilan, 
        CAST(e.jasmani AS DECIMAL(10,2)) as jasmani, 
        CAST(f.kesehatan AS DECIMAL(10,2)) as kesehatan,
        CAST(ROUND((b.karakter + c.pengetahuan + d.keterampilan + e.jasmani + f.kesehatan)/5,2) AS DECIMAL(10,2)) as rerata               				  
        from (SELECT a.*, b.batalyon, c.kompi, d.peleton, e.kelompok FROM m_user_taruna a 
              LEFT JOIN m_sm_batalyon b on a.id_batalyon=b.id
                        LEFT JOIN m_sm_kompi c on a.id_kompi = c.id
                        LEFT JOIN m_sm_peleton d on a.id_peleton = d.id
                        LEFT JOIN m_kelompok e on a.id_kelompok=e.id
              WHERE a.is_deleted = 0
              and a.id_m_user = '" . $data['id'] . "') a 
                left join
        (SELECT 
            a.nilai_akhir as 'karakter', a.id_semester, a.id_user_taruna
        FROM t_penilaian_aspek_karakter a 
        LEFT JOIN m_mata_pelajaran b
            ON a.id_mata_pelajaran = b.id       
        WHERE a.is_deleted = 0
            AND b.is_deleted = 0
            $semester) b 
                    on a.id_m_user = b.id_user_taruna
                    left join
        (SELECT 
            ROUND(SUM(a.nilai_akhir)/COUNT(*),2) as 'pengetahuan',
            a.id_semester, a.id_user_taruna
        FROM t_penilaian_aspek_pengetahuan a 
        LEFT JOIN m_mata_pelajaran b
            ON a.id_mata_pelajaran = b.id       
        WHERE a.is_deleted = 0
            AND b.is_deleted = 0
            $semester
            group by a.id_user_taruna) c        		
            on a.id_m_user = c.id_user_taruna
        left join
        (SELECT 
            ROUND(SUM(a.nilai_akhir)/COUNT(*),2) as 'keterampilan',
            a.id_semester, a.id_user_taruna
        FROM t_penilaian_aspek_keterampilan a 
        LEFT JOIN m_mata_pelajaran b
            ON a.id_mata_pelajaran = b.id       
        WHERE a.is_deleted = 0
            AND b.is_deleted = 0 
            $semester
            group by a.id_user_taruna) d
                    on a.id_m_user = d.id_user_taruna
                    left join
        (SELECT 
            a.nilai_akhir as 'jasmani', a.id_semester, a.id_user_taruna
        FROM t_penilaian_aspek_jasmani a 
        LEFT JOIN m_mata_pelajaran b
            ON a.id_mata_pelajaran = b.id
        WHERE a.is_deleted = 0
            AND b.is_deleted = 0
            $semester) e
        on a.id_m_user = e.id_user_taruna
        left join
        (SELECT 
            a.nilai_akhir as 'kesehatan', a.id_semester, a.id_user_taruna
        FROM t_penilaian_aspek_kesehatan a 
        LEFT JOIN m_mata_pelajaran b
            ON a.id_mata_pelajaran = b.id
        WHERE a.is_deleted = 0
            AND b.is_deleted = 0
            $semester) f
                    on a.id_m_user = f.id_user_taruna";

        $taruna = $this->db->query($query)->getRowArray();

        $absen = "SELECT ifnull(ceil((sum(if(is_absen=1 , total , '0'))*100)/sum(total)) ,0) as total from (
            SELECT d.is_absen , count(d.is_absen) as total
            from m_user_taruna a 
            left join (select * from t_bahan_ajar where is_deleted = 0) b
                on a.id_semester = b.id_semester
                and a.id_batalyon = b.id_batalyon
                $semester_absen
            left join (select * from t_jadwal where is_deleted = 0) c
                on b.id = c.id_bahan_ajar
                and a.id_kelompok = c.id_kelompok_taruna
            join t_absensi d
                on d.id_taruna = a.id_m_user
                and d.id_jadwal = c.id
                
            where a.id_m_user = '" . $data['id'] . "' and c.is_deleted='0' and d.is_deleted='0'
            group by is_absen ) a";
        $absenResult = $this->db->query($absen)->getRow()->total;

        echo json_encode(array('success' => true, 'data' => $taruna, 'absen' => $absenResult));
    }

    function rankingtaruna_detail_nilai()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $table = 't_penilaian_aspek_pengetahuan';
        if ($data['aspek']) {
            $table = $data['aspek'];
        }
        $query = "select c.namataruna, c.noaklong, if(c.id_gender=1, 'Taruna' , 'Taruni') as nama_gender, 
        i.batalyon, j.kelompok, c.photopath, a.mata_pelajaran, x.semester,
        d.*,
        z.aspek from m_mata_pelajaran a
        left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
        left join m_user_taruna c on c.id_batalyon = b.id_batalyon
        left join $table d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
        left join m_sm_batalyon i on c.id_batalyon = i.id
        left join m_kelompok j on c.id_kelompok = j.id
        left join m_aspek z on z.id = b.id_aspek
        left join m_semester x on x.id = b.id_semester
        where a.is_deleted = 0
        and b.id_batalyon = '" . $data['id_batalyon'] . "'
        and b.id_semester = '" . $data['id_semester'] . "'
        and c.id_m_user = '" . $data['id_m_user'] . "'
        and b.id_aspek = '" . $data['id_aspek'] . "'
        group by a.id, c.noaklong";

        $result = $this->db->query($query)->getResult();

        $response = ['success' => true, 'data' => $result];

        echo json_encode($response);
    }

    function rankingpersemester_load()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT * FROM (SELECT rapor.id_m_user as id, rapor.namataruna, rapor.nama_gender, rapor.noaklong, rapor.batalyon, rapor.kelompok, rapor.photopath, rapor.semester, 
        ROUND(((2.5*rapor.rata_pengetahuan)+(2.5*rapor.rata_keterampilan)+(2.5*rapor.rata_karakter)+rata_kesehatan+(1.5*rata_jasmani))/10,2) as nap,
        ROUND((rata_pengetahuan+rata_keterampilan)/2,2) as npk,
        ROUND(rata_karakter,2) as nsp,
        ROUND(((6*rapor.rata_jasmani)+(4*rapor.rata_kesehatan))/10,2) as njk,
        RANK() OVER(ORDER BY ROUND(((2.5*rapor.rata_pengetahuan)+(2.5*rapor.rata_keterampilan)+(2.5*rapor.rata_karakter)+rata_kesehatan+(1.5*rata_jasmani))/10,2) DESC) nap_rank,   
        RANK() OVER(ORDER BY ROUND((rata_pengetahuan+rata_keterampilan)/2,2) DESC) npk_rank,
        RANK() OVER(ORDER BY ROUND(rata_karakter,2) DESC) nsp_rank,
        RANK() OVER(ORDER BY ROUND(((6*rapor.rata_jasmani)+(4*rapor.rata_kesehatan))/10,2)  DESC) njk_rank
        from (select a.id_m_user, a.namataruna, a.nama_gender, a.noaklong, a.batalyon, a.kelompok, a.photopath, max(a.semester) as semester,
        group_concat(distinct if(a.aspek='Pengetahuan',a.nilai,null)) as pengetahuan,
        group_concat(distinct if(a.aspek='Keterampilan',a.nilai,null)) as keterampilan,
        group_concat(distinct if(a.aspek='Karakter',a.nilai,null)) as karakter,
        group_concat(distinct if(a.aspek='Kesehatan',a.nilai,null)) as kesehatan,
        group_concat(distinct if(a.aspek='Jasmani',a.nilai,null)) as jasmani,
        sum(if(a.aspek='Pengetahuan',a.jml,0))/sum(if(a.aspek='Pengetahuan',a.pembagi,0)) as rata_pengetahuan,
        sum(if(a.aspek='Keterampilan',a.jml,0))/sum(if(a.aspek='Keterampilan',a.pembagi,0)) as rata_keterampilan,
        sum(if(a.aspek='Karakter',a.jml,0))/sum(if(a.aspek='Karakter',a.pembagi,0)) as rata_karakter,
        sum(if(a.aspek='Kesehatan',a.jml,0))/sum(if(a.aspek='Kesehatan',a.pembagi,0)) as rata_kesehatan,
        sum(if(a.aspek='Jasmani',a.jml,0))/sum(if(a.aspek='Jasmani',a.pembagi,0)) as rata_jasmani
        from (select rangkuman.id_m_user, rangkuman.namataruna, rangkuman.nama_gender, rangkuman.noaklong, rangkuman.batalyon, rangkuman.kelompok, rangkuman.photopath, rangkuman.aspek, 
        json_arrayagg(json_object(rangkuman.mata_pelajaran,ifnull(rangkuman.nilai_akhir,0))) as nilai,
        sum(rangkuman.nilai_akhir) as jml, count(1) as pembagi, group_concat(distinct rangkuman.semester separator '/') as semester
        from (select c.id_m_user, c.namataruna, c.noaklong, if(c.id_gender=1, 'Taruna' , 'Taruni') as nama_gender, 
              i.batalyon, j.kelompok, c.photopath, a.mata_pelajaran, x.semester,
        CASE
            WHEN b.id_aspek = '1' THEN d.nilai_akhir
            WHEN b.id_aspek = '2' THEN e.nilai_akhir
            WHEN b.id_aspek = '3' THEN f.nilai_akhir
            WHEN b.id_aspek = '4' THEN g.nilai_akhir
            WHEN b.id_aspek = '5' THEN h.nilai_akhir                    
        END as nilai_akhir,
        z.aspek from m_mata_pelajaran a
        left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
        left join m_user_taruna c on c.id_batalyon = b.id_batalyon
        left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
        left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
        left join t_penilaian_aspek_karakter f on f.id_semester = b.id_semester and f.id_user_taruna = c.id_m_user and f.is_deleted = 0
        left join t_penilaian_aspek_kesehatan g on g.id_semester = b.id_semester and g.id_user_taruna = c.id_m_user and g.is_deleted = 0
        left join t_penilaian_aspek_jasmani h on h.id_semester = b.id_semester and h.id_user_taruna = c.id_m_user and h.is_deleted = 0
        left join m_sm_batalyon i on c.id_batalyon = i.id
        left join m_kelompok j on c.id_kelompok = j.id
        left join m_aspek z on z.id = b.id_aspek
        left join m_semester x on x.id = b.id_semester
        where a.is_deleted = 0
        and b.id_batalyon = '" . $data['id_batalyon'] . "'
        and b.id_semester = '" . $data['id_semester'] . "'
        group by a.id, c.noaklong) rangkuman
        group by rangkuman.noaklong, rangkuman.aspek) a
        group by a.noaklong) rapor) a where a.id!='' ";


        // echo $query;

        $where = ["a.namataruna", "a.noaklong"];

        $order = "nap";

        parent::_loadDatatableorder($query, $where, $data, $order, 'desc');
        // parent::_loadDatatable($query, $where, $data);

    }

    function rankingaspekpersemester_load()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT * FROM (SELECT rapor.id_m_user as id, rapor.namataruna, rapor.nama_gender, rapor.noaklong, rapor.batalyon, rapor.kelompok, rapor.photopath, rapor.semester, 
        ROUND(rapor.rata_pengetahuan, 2) as rata_pengetahuan,
        ROUND(rapor.rata_keterampilan, 2) as rata_keterampilan,
        ROUND(rapor.rata_karakter, 2) as rata_karakter,
        ROUND(rapor.rata_kesehatan, 2) as rata_kesehatan,
        ROUND(rapor.rata_jasmani, 2) as rata_jasmani,
        CAST(ROUND((rapor.rata_karakter + rapor.rata_pengetahuan + 
        	   rapor.rata_keterampilan + rapor.rata_jasmani + 
        	   rapor.rata_kesehatan)/5,2) AS DECIMAL(10,2)) as rerata,
        RANK() OVER(ORDER BY rapor.rata_pengetahuan DESC) pengetahuan_rank,   
        RANK() OVER(ORDER BY rapor.rata_keterampilan DESC) keterampilan_rank,
        RANK() OVER(ORDER BY rapor.rata_kesehatan DESC) kesehatan_rank,
        RANK() OVER(ORDER BY rapor.rata_karakter DESC) karakter_rank,
        RANK() OVER(ORDER BY rapor.rata_jasmani DESC) jasmani_rank,
        RANK() OVER(ORDER BY ROUND((rapor.rata_karakter + rapor.rata_pengetahuan + 
        	   rapor.rata_keterampilan + rapor.rata_jasmani + 
        	   rapor.rata_kesehatan)/5,2) DESC) rerata_rank
        from (SELECT a.id_m_user, a.namataruna, a.nama_gender, a.noaklong, a.batalyon, a.kelompok, a.photopath, max(a.semester) as semester,
        group_concat(distinct if(a.aspek='Pengetahuan',a.nilai,null)) as pengetahuan,
        group_concat(distinct if(a.aspek='Keterampilan',a.nilai,null)) as keterampilan,
        group_concat(distinct if(a.aspek='Karakter',a.nilai,null)) as karakter,
        group_concat(distinct if(a.aspek='Kesehatan',a.nilai,null)) as kesehatan,
        group_concat(distinct if(a.aspek='Jasmani',a.nilai,null)) as jasmani,
        sum(if(a.aspek='Pengetahuan',a.jml,0))/sum(if(a.aspek='Pengetahuan',a.pembagi,0)) as rata_pengetahuan,
        sum(if(a.aspek='Keterampilan',a.jml,0))/sum(if(a.aspek='Keterampilan',a.pembagi,0)) as rata_keterampilan,
        sum(if(a.aspek='Karakter',a.jml,0))/sum(if(a.aspek='Karakter',a.pembagi,0)) as rata_karakter,
        sum(if(a.aspek='Kesehatan',a.jml,0))/sum(if(a.aspek='Kesehatan',a.pembagi,0)) as rata_kesehatan,
        sum(if(a.aspek='Jasmani',a.jml,0))/sum(if(a.aspek='Jasmani',a.pembagi,0)) as rata_jasmani
        from (select rangkuman.id_m_user, rangkuman.namataruna, rangkuman.noaklong, rangkuman.nama_gender, rangkuman.batalyon, rangkuman.kelompok, rangkuman.photopath, rangkuman.aspek, 
        json_arrayagg(json_object(rangkuman.mata_pelajaran,ifnull(rangkuman.nilai_akhir,0))) as nilai,
        sum(rangkuman.nilai_akhir) as jml, count(1) as pembagi, group_concat(distinct rangkuman.semester separator '/') as semester
        from (select c.id_m_user, c.namataruna, c.noaklong, if(c.id_gender=1, 'Taruna' , 'Taruni') as nama_gender,
              i.batalyon, j.kelompok, c.photopath, a.mata_pelajaran, x.semester,
        CASE
            WHEN b.id_aspek = '1' THEN d.nilai_akhir
            WHEN b.id_aspek = '2' THEN e.nilai_akhir
            WHEN b.id_aspek = '3' THEN f.nilai_akhir
            WHEN b.id_aspek = '4' THEN g.nilai_akhir
            WHEN b.id_aspek = '5' THEN h.nilai_akhir                    
        END as nilai_akhir,
        z.aspek from m_mata_pelajaran a
        left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
        left join m_user_taruna c on c.id_batalyon = b.id_batalyon
        left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
        left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
        left join t_penilaian_aspek_karakter f on f.id_semester = b.id_semester and f.id_user_taruna = c.id_m_user and f.is_deleted = 0
        left join t_penilaian_aspek_kesehatan g on g.id_semester = b.id_semester and g.id_user_taruna = c.id_m_user and g.is_deleted = 0
        left join t_penilaian_aspek_jasmani h on h.id_semester = b.id_semester and h.id_user_taruna = c.id_m_user and h.is_deleted = 0
        left join m_sm_batalyon i on c.id_batalyon = i.id
        left join m_kelompok j on c.id_kelompok = j.id
        left join m_aspek z on z.id = b.id_aspek
        left join m_semester x on x.id = b.id_semester
        where a.is_deleted = 0
        and b.id_batalyon = '" . $data['id_batalyon'] . "'
        and b.id_semester = '" . $data['id_semester'] . "'
        group by a.id, c.noaklong) rangkuman
        group by rangkuman.noaklong, rangkuman.aspek) a
        group by a.noaklong) rapor) a where a.id!='' ";

        // echo $query;

        $where = ["a.namataruna", "a.noaklong"];

        $order = "rata_pengetahuan";

        parent::_loadDatatableorder($query, $where, $data, $order, 'desc');
        // parent::_loadDatatable($query, $where, $data);

    }

    function rankingaspekpersemester_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $semester_absen = $data['id_semester'] ? " and b.id_semester = '" . $data['id_semester'] . "' " : '';
        $query = "SELECT rapor.id_m_user as id, rapor.namataruna, rapor.nama_gender, rapor.noaklong, rapor.batalyon, rapor.kelompok, rapor.photopath, rapor.semester, 
        ROUND(rapor.rata_pengetahuan, 2) as pengetahuan,
        ROUND(rapor.rata_keterampilan, 2) as keterampilan,
        ROUND(rapor.rata_karakter, 2) as karakter,
        ROUND(rapor.rata_kesehatan, 2) as kesehatan,
        ROUND(rapor.rata_jasmani, 2) as jasmani,
        CAST(ROUND((rapor.rata_karakter + rapor.rata_pengetahuan + 
            rapor.rata_keterampilan + rapor.rata_jasmani + 
            rapor.rata_kesehatan)/5,2) AS DECIMAL(10,2)) as rerata     
        from (SELECT a.id_m_user, a.namataruna, a.nama_gender, a.noaklong, a.batalyon, a.kelompok, a.photopath, max(a.semester) as semester,
        group_concat(distinct if(a.aspek='Pengetahuan',a.nilai,null)) as pengetahuan,
        group_concat(distinct if(a.aspek='Keterampilan',a.nilai,null)) as keterampilan,
        group_concat(distinct if(a.aspek='Karakter',a.nilai,null)) as karakter,
        group_concat(distinct if(a.aspek='Kesehatan',a.nilai,null)) as kesehatan,
        group_concat(distinct if(a.aspek='Jasmani',a.nilai,null)) as jasmani,
        sum(if(a.aspek='Pengetahuan',a.jml,0))/sum(if(a.aspek='Pengetahuan',a.pembagi,0)) as rata_pengetahuan,
        sum(if(a.aspek='Keterampilan',a.jml,0))/sum(if(a.aspek='Keterampilan',a.pembagi,0)) as rata_keterampilan,
        sum(if(a.aspek='Karakter',a.jml,0))/sum(if(a.aspek='Karakter',a.pembagi,0)) as rata_karakter,
        sum(if(a.aspek='Kesehatan',a.jml,0))/sum(if(a.aspek='Kesehatan',a.pembagi,0)) as rata_kesehatan,
        sum(if(a.aspek='Jasmani',a.jml,0))/sum(if(a.aspek='Jasmani',a.pembagi,0)) as rata_jasmani
        from (select rangkuman.id_m_user, rangkuman.namataruna, rangkuman.noaklong, rangkuman.nama_gender, rangkuman.batalyon, rangkuman.kelompok, rangkuman.photopath, rangkuman.aspek, 
        json_arrayagg(json_object(rangkuman.mata_pelajaran,ifnull(rangkuman.nilai_akhir,0))) as nilai,
        sum(rangkuman.nilai_akhir) as jml, count(1) as pembagi, group_concat(distinct rangkuman.semester separator '/') as semester
        from (select c.id_m_user, c.namataruna, c.noaklong, if(c.id_gender=1, 'Taruna' , 'Taruni') as nama_gender,
              i.batalyon, j.kelompok, c.photopath, a.mata_pelajaran, x.semester,
        CASE
            WHEN b.id_aspek = '1' THEN d.nilai_akhir
            WHEN b.id_aspek = '2' THEN e.nilai_akhir
            WHEN b.id_aspek = '3' THEN f.nilai_akhir
            WHEN b.id_aspek = '4' THEN g.nilai_akhir
            WHEN b.id_aspek = '5' THEN h.nilai_akhir                    
        END as nilai_akhir,
        z.aspek from m_mata_pelajaran a
        left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
        left join m_user_taruna c on c.id_batalyon = b.id_batalyon
        left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
        left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
        left join t_penilaian_aspek_karakter f on f.id_semester = b.id_semester and f.id_user_taruna = c.id_m_user and f.is_deleted = 0
        left join t_penilaian_aspek_kesehatan g on g.id_semester = b.id_semester and g.id_user_taruna = c.id_m_user and g.is_deleted = 0
        left join t_penilaian_aspek_jasmani h on h.id_semester = b.id_semester and h.id_user_taruna = c.id_m_user and h.is_deleted = 0
        left join m_sm_batalyon i on c.id_batalyon = i.id
        left join m_kelompok j on c.id_kelompok = j.id
        left join m_aspek z on z.id = b.id_aspek
        left join m_semester x on x.id = b.id_semester
        where a.is_deleted = 0
        and b.id_batalyon = '" . $data['id_batalyon'] . "'
        and b.id_semester = '" . $data['id_semester'] . "'
        and c.id_m_user = '" . $data['id'] . "'
        group by a.id, c.noaklong) rangkuman
        group by rangkuman.noaklong, rangkuman.aspek) a
        group by a.noaklong) rapor";

        $taruna = $this->db->query($query)->getRowArray();

        $absen = "SELECT ifnull(ceil((sum(if(is_absen=1 , total , '0'))*100)/sum(total)) ,0) as total from (
            SELECT d.is_absen , count(d.is_absen) as total
            from m_user_taruna a 
            left join (select * from t_bahan_ajar where is_deleted = 0) b
                on a.id_semester = b.id_semester
                and a.id_batalyon = b.id_batalyon
                $semester_absen
            left join (select * from t_jadwal where is_deleted = 0) c
                on b.id = c.id_bahan_ajar
                and a.id_kelompok = c.id_kelompok_taruna
            join t_absensi d
                on d.id_taruna = a.id_m_user
                and d.id_jadwal = c.id
                
            where a.id_m_user = '" . $data['id'] . "' and c.is_deleted='0' and d.is_deleted='0'
            group by is_absen ) a";
        $absenResult = $this->db->query($absen)->getRow()->total;

        echo json_encode(array('success' => true, 'data' => $taruna, 'absen' => $absenResult));
    }

    function rankingpertingkat_load()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT * FROM (SELECT rapor.*, 
        RANK() OVER(ORDER BY rapor.rank_aspek DESC) rank_aspek_rank,   
        RANK() OVER(ORDER BY rapor.rank_peng_ket DESC) rank_peng_ket_rank,
        RANK() OVER(ORDER BY rapor.rank_karakter DESC) rank_karakter_rank,
        RANK() OVER(ORDER BY rapor.rank_jas_kes DESC) rank_jas_kes_rank
        from (select ranking.id_m_user as id, ranking.namataruna, ranking.noaklong, ranking.nama_gender, ranking.batalyon, ranking.kelompok, ranking.photopath, ranking.semester,
        round(sum(((2.5*rata_pengetahuan)+(2.5*rata_keterampilan)+(2.5*rata_karakter)+rata_kesehatan+(1.5*rata_jasmani))/10)/2,2) as rank_aspek, 
        round(((sum(rata_pengetahuan)/2) + (sum(rata_keterampilan)/2))/2,2) as rank_peng_ket,
        round(sum(rata_karakter)/2,2) as rank_karakter, round(((sum(rata_kesehatan)/2) + (sum(rata_jasmani)/2))/2,2) as rank_jas_kes
        from (select a.id_m_user, a.namataruna, a.nama_gender, a.noaklong, a.batalyon, a.kelompok, a.photopath,  max(a.semester) as semester,
        sum(if(a.aspek='Pengetahuan',a.jml,0))/sum(if(a.aspek='Pengetahuan',a.pembagi,0)) as rata_pengetahuan,
        sum(if(a.aspek='Keterampilan',a.jml,0))/sum(if(a.aspek='Keterampilan',a.pembagi,0)) as rata_keterampilan,
        sum(if(a.aspek='Karakter',a.jml,0))/sum(if(a.aspek='Karakter',a.pembagi,0)) as rata_karakter,
        sum(if(a.aspek='Kesehatan',a.jml,0))/sum(if(a.aspek='Kesehatan',a.pembagi,0)) as rata_kesehatan,
        sum(if(a.aspek='Jasmani',a.jml,0))/sum(if(a.aspek='Jasmani',a.pembagi,0)) as rata_jasmani
        from (select rangkuman.id_m_user, rangkuman.namataruna, rangkuman.noaklong, rangkuman.nama_gender, rangkuman.batalyon, rangkuman.kelompok, rangkuman.photopath, rangkuman.aspek, 
        json_arrayagg(json_object(rangkuman.mata_pelajaran,ifnull(rangkuman.nilai_akhir,0))) as nilai,
        sum(rangkuman.nilai_akhir) as jml, count(1) as pembagi, group_concat(distinct rangkuman.semester separator '/') as semester, rangkuman.id_semester
        from (select c.id_m_user, c.namataruna, c.noaklong, if(c.id_gender=1, 'Taruna' , 'Taruni') as nama_gender,
              i.batalyon, j.kelompok, c.photopath, a.mata_pelajaran, x.semester, b.id_semester,
        CASE
            WHEN b.id_aspek = '1' THEN d.nilai_akhir
            WHEN b.id_aspek = '2' THEN e.nilai_akhir
            WHEN b.id_aspek = '3' THEN f.nilai_akhir
            WHEN b.id_aspek = '4' THEN g.nilai_akhir
            WHEN b.id_aspek = '5' THEN h.nilai_akhir                    
        END as nilai_akhir,
        z.aspek from m_mata_pelajaran a
        left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
        left join m_user_taruna c on c.id_batalyon = b.id_batalyon
        left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
        left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
        left join t_penilaian_aspek_karakter f on f.id_mata_pelajaran = a.id and f.id_semester = b.id_semester and f.id_user_taruna = c.id_m_user
        left join t_penilaian_aspek_kesehatan g on g.id_mata_pelajaran = a.id and g.id_semester = b.id_semester and g.id_user_taruna = c.id_m_user
        left join t_penilaian_aspek_jasmani h on h.id_mata_pelajaran = a.id and h.id_semester = b.id_semester and h.id_user_taruna = c.id_m_user
            left join m_sm_batalyon i on c.id_batalyon = i.id
        left join m_kelompok j on c.id_kelompok = j.id
        left join m_aspek z on z.id = b.id_aspek
        left join m_semester x on x.id = b.id_semester
        where a.is_deleted = 0
        and b.id_batalyon = '" . $data['id_batalyon'] . "'
        and x.id_tingkat = '" . $data['id_tingkat'] . "'
        group by a.id, c.noaklong, b.id_semester) rangkuman
        group by rangkuman.noaklong, rangkuman.aspek) a
        group by a.noaklong, a.id_semester) ranking
        group by ranking.noaklong) rapor) a where a.id!='' ";

        // echo $query;

        $where = ["a.namataruna", "a.noaklong"];

        $order = "rank_aspek";

        parent::_loadDatatableorder($query, $where, $data, $order, 'desc');
        // parent::_loadDatatable($query, $where, $data);

    }

    function nilaiproses_load()
    {
        $data = json_decode($this->request->getPost('param'), true);
    }

    function rankingaspekpertingkat_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $tingkat_absen = $data['id_tingkat'] ? " and b.id_tingkat = '" . $data['id_tingkat'] . "' " : '';
        $query = "SELECT rapor.id_m_user as id, rapor.namataruna, rapor.nama_gender, rapor.noaklong, rapor.batalyon, rapor.kelompok, rapor.photopath, rapor.semester, 
        ROUND(rapor.rata_pengetahuan, 2) as pengetahuan,
        ROUND(rapor.rata_keterampilan, 2) as keterampilan,
        ROUND(rapor.rata_karakter, 2) as karakter,
        ROUND(rapor.rata_kesehatan, 2) as kesehatan,
        ROUND(rapor.rata_jasmani, 2) as jasmani,
        CAST(ROUND((rapor.rata_karakter + rapor.rata_pengetahuan + 
            rapor.rata_keterampilan + rapor.rata_jasmani + 
            rapor.rata_kesehatan)/5,2) AS DECIMAL(10,2)) as rerata     
        from (SELECT a.id_m_user, a.namataruna, a.nama_gender, a.noaklong, a.batalyon, a.kelompok, a.photopath, max(a.semester) as semester,
        group_concat(distinct if(a.aspek='Pengetahuan',a.nilai,null)) as pengetahuan,
        group_concat(distinct if(a.aspek='Keterampilan',a.nilai,null)) as keterampilan,
        group_concat(distinct if(a.aspek='Karakter',a.nilai,null)) as karakter,
        group_concat(distinct if(a.aspek='Kesehatan',a.nilai,null)) as kesehatan,
        group_concat(distinct if(a.aspek='Jasmani',a.nilai,null)) as jasmani,
        sum(if(a.aspek='Pengetahuan',a.jml,0))/sum(if(a.aspek='Pengetahuan',a.pembagi,0)) as rata_pengetahuan,
        sum(if(a.aspek='Keterampilan',a.jml,0))/sum(if(a.aspek='Keterampilan',a.pembagi,0)) as rata_keterampilan,
        sum(if(a.aspek='Karakter',a.jml,0))/sum(if(a.aspek='Karakter',a.pembagi,0)) as rata_karakter,
        sum(if(a.aspek='Kesehatan',a.jml,0))/sum(if(a.aspek='Kesehatan',a.pembagi,0)) as rata_kesehatan,
        sum(if(a.aspek='Jasmani',a.jml,0))/sum(if(a.aspek='Jasmani',a.pembagi,0)) as rata_jasmani
        from (select rangkuman.id_m_user, rangkuman.namataruna, rangkuman.noaklong, rangkuman.nama_gender, rangkuman.batalyon, rangkuman.kelompok, rangkuman.photopath, rangkuman.aspek, 
        json_arrayagg(json_object(rangkuman.mata_pelajaran,ifnull(rangkuman.nilai_akhir,0))) as nilai,
        sum(rangkuman.nilai_akhir) as jml, count(1) as pembagi, group_concat(distinct rangkuman.semester separator '/') as semester
        from (select c.id_m_user, c.namataruna, c.noaklong, if(c.id_gender=1, 'Taruna' , 'Taruni') as nama_gender,
              i.batalyon, j.kelompok, c.photopath, a.mata_pelajaran, x.semester,
        CASE
            WHEN b.id_aspek = '1' THEN d.nilai_akhir
            WHEN b.id_aspek = '2' THEN e.nilai_akhir
            WHEN b.id_aspek = '3' THEN f.nilai_akhir
            WHEN b.id_aspek = '4' THEN g.nilai_akhir
            WHEN b.id_aspek = '5' THEN h.nilai_akhir                    
        END as nilai_akhir,
        z.aspek from m_mata_pelajaran a
        left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
        left join m_user_taruna c on c.id_batalyon = b.id_batalyon
        left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
        left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
        left join t_penilaian_aspek_karakter f on f.id_mata_pelajaran = a.id and f.id_semester = b.id_semester and f.id_user_taruna = c.id_m_user 
        left join t_penilaian_aspek_kesehatan g on g.id_mata_pelajaran = a.id and g.id_semester = b.id_semester and g.id_user_taruna = c.id_m_user 
        left join t_penilaian_aspek_jasmani h on h.id_mata_pelajaran = a.id and h.id_semester = b.id_semester and h.id_user_taruna = c.id_m_user 
        left join m_sm_batalyon i on c.id_batalyon = i.id
        left join m_kelompok j on c.id_kelompok = j.id
        left join m_aspek z on z.id = b.id_aspek
        left join m_semester x on x.id = b.id_semester
        where a.is_deleted = 0
        and b.id_batalyon = '" . $data['id_batalyon'] . "'
        and x.id_tingkat = '" . $data['id_tingkat'] . "'
        and c.id_m_user = '" . $data['id'] . "'
        group by a.id, c.noaklong) rangkuman
        group by rangkuman.noaklong, rangkuman.aspek) a
        group by a.noaklong) rapor";

        $taruna = $this->db->query($query)->getRowArray();

        $absen = "SELECT ifnull(ceil((sum(if(is_absen=1 , total , '0'))*100)/sum(total)) ,0) as total from (
            SELECT d.is_absen , count(d.is_absen) as total
            from m_user_taruna a 
            left join (select a.*, b.id_tingkat from t_bahan_ajar a left join m_semester b on a.id_semester = b.id where a.is_deleted = 0) b
                on a.id_semester = b.id_semester
                and a.id_batalyon = b.id_batalyon
                $tingkat_absen
            left join (select * from t_jadwal where is_deleted = 0) c
                on b.id = c.id_bahan_ajar
                and a.id_kelompok = c.id_kelompok_taruna
            join t_absensi d
                on d.id_taruna = a.id_m_user
                and d.id_jadwal = c.id
                
            where a.id_m_user = '" . $data['id'] . "' and c.is_deleted='0' and d.is_deleted='0'
            group by is_absen ) a";
        $absenResult = $this->db->query($absen)->getRow()->total;

        echo json_encode(array('success' => true, 'data' => $taruna, 'absen' => $absenResult));
    }

    function rankingpertingkat_detail_nilai()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $table = 't_penilaian_aspek_pengetahuan';
        if ($data['aspek']) {
            $table = $data['aspek'];
        }
        $query = "select c.namataruna, c.noaklong, if(c.id_gender=1, 'Taruna' , 'Taruni') as nama_gender, 
        i.batalyon, j.kelompok, c.photopath, a.mata_pelajaran, x.semester,
        d.*,
        z.aspek from m_mata_pelajaran a
        left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
        left join m_user_taruna c on c.id_batalyon = b.id_batalyon
        left join $table d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
        left join m_sm_batalyon i on c.id_batalyon = i.id
        left join m_kelompok j on c.id_kelompok = j.id
        left join m_aspek z on z.id = b.id_aspek
        left join m_semester x on x.id = b.id_semester
        where a.is_deleted = 0
        and b.id_batalyon = '" . $data['id_batalyon'] . "'
        and x.id_tingkat = '" . $data['id_tingkat'] . "'
        and c.id_m_user = '" . $data['id_m_user'] . "'
        and b.id_aspek = '" . $data['id_aspek'] . "'
        group by a.id, c.noaklong";

        $result = $this->db->query($query)->getResult();

        $response = ['success' => true, 'data' => $result];

        echo json_encode($response);
    }


    function log_log()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $datalog =  [
            'created_by' => $data['userid'],
            'ip' =>  $data['ip'],
            'id_menu' => $data['data_ttd']['nama_menu'],
            'noaklong' => $data['data_content']['datataruna']['noaklong'],
            'data_content'  => json_encode($data['data_content']),
            'data_ttd'  => json_encode($data['data_ttd'])
        ];

        $this->db->table('log_ttd_req')->insert($datalog);
    }
}
