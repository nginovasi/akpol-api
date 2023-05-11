<?php

namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;

class WebMasterdata extends BaseController
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

    // controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------




    // ajax ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function birthday_place_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.*, a.kdkabkota as id, a.nama as text from m_lokasi_lahir a where a.is_deleted = 0";
        $where = ["a.nama"];

        parent::_loadSelect2($data, $query, $where);
    }

    function tingkat_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, a.tingkatan as text from m_tingkatan a where a.is_deleted = 0";
        $where = ["a.tingkatan"];

        parent::_loadSelect2($data, $query, $where);
    }

    function tingkatbybatalyon_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT a.*,concat(a.tingkatan, ' ',if(b.id is null , '(Belum Ada Mata Kuliah)' , '') ) as text, if(b.id is null , '0' , '1') as is_mapel from m_tingkatan a
                    left join ( select 
                        a.id
                    from m_tingkatan a
                    left join m_semester b on a.id=b.id_tingkat
                    join t_program_studi_mata_pelajaran c on b.id=c.id_semester and c.id_batalyon='" . $data['id_batalyon'] . "' 
                    group by a.id) b on a.id=b.id
                    where a.is_deleted = 0 and a.id in ('2', '3', '4','5')";
        $where = ["a.tingkatan"];

        parent::_loadSelect2($data, $query, $where);
    }

    function tingkatsatusampaiempat_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, a.tingkatan as text from m_tingkatan a where a.is_deleted = 0 and a.id in ('2','3','4','5') ";
        $where = ["a.tingkatan"];

        parent::_loadSelect2($data, $query, $where);
    }

    function tingkat_detail_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id as id, concat(b.semester, ' - ' ,a.jabatan) as text from m_tingkatan_detail a left join m_semester b on a.id_semester=b.id where a.is_deleted = 0 ";
        $where = ["a.jabatan"];

        parent::_loadSelect2($data, $query, $where);
    }

    function tingkat_detail_by_smt_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id as id, concat(b.semester, ' - ' ,a.jabatan) as text from m_tingkatan_detail a left join m_semester b on a.id_semester=b.id where a.is_deleted = 0 AND a.id != 1 AND a.id_semester is not null group by a.id_semester ";
        $where = ["a.jabatan", "a.kode_jabatan"];

        parent::_loadSelect2($data, $query, $where);
    }

    function batalyon_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT a.*, concat(a.batalyon , ' ( ', a.tahun_masuk, ' )') AS text
                    FROM m_sm_batalyon a
                    WHERE a.is_deleted = 0";
        $where = ["a.batalyon", "a.tahun_masuk"];
        $orderby = "ORDER BY a.angkatan DESC";

        parent::_loadSelect2orderby($data, $query, $where, $orderby);
    }

    function kompi_select_get()
    {
        $data = $this->request->getGet();
        $id_batalyon = $data["id_batalyon"];
        $query = "select a.*, a.kompi as text from m_sm_kompi a where a.is_deleted = 0 and a.id_batalyon = '" . $id_batalyon . "' ";
        $where = ["a.kompi"];

        parent::_loadSelect2($data, $query, $where);
    }

    function kompi_all_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT a.*, CONCAT(c.batalyon, ' (', a.kompi, ')') AS 'text'
                    FROM m_sm_kompi a
                    LEFT JOIN m_user_pendidik b ON a.id_user_pendidik = b.id_m_user
                    LEFT JOIN m_sm_batalyon c ON c.id = a.id_batalyon
                    WHERE a.is_deleted = 0 AND b.is_deleted = 0 AND c.is_deleted = 0";
        $where = ["a.kompi", "b.namagadik", "c.batalyon"];
        $orderby = "ORDER BY c.angkatan DESC, a.kompi ASC";

        parent::_loadSelect2orderby($data, $query, $where, $orderby);
    }

    function peleton_select_get()
    {
        $data = $this->request->getGet();
        $id_kompi = $data["id_kompi"];
        $query = "select a.*, a.peleton as text from m_sm_peleton a where a.is_deleted = 0 and a.id_kompi = '" . $id_kompi . "' ";
        $where = ["a.peleton"];

        parent::_loadSelect2($data, $query, $where);
    }

    function agama_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.*, a.agama as text from m_agama a where a.is_deleted = 0";
        $where = ["a.agama"];

        parent::_loadSelect2($data, $query, $where);
    }

    function suku_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.*, a.suku as text from m_suku a where a.is_deleted = 0";
        $where = ["a.suku"];

        parent::_loadSelect2($data, $query, $where);
    }

    function tingkat_pendidikan_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.*, a.pendidikan as text from m_tingkat_pendidikan_umum a where a.is_deleted = 0";
        $where = ["a.pendidikan"];

        parent::_loadSelect2($data, $query, $where);
    }

    function prov_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.*, a.kode as id, a.nama as text from m_lokasi_nik_ind a where a.is_deleted = 0 and length(a.kode) = 2";
        $where = ["a.nama"];

        parent::_loadSelect2($data, $query, $where);
    }


    function kab_select_get()
    {
        $data = $this->request->getGet();
        $id_prov = $data["id_prov"];
        $query = "select a.*, a.kode as id, a.nama as text from m_lokasi_nik_ind a where a.is_deleted = 0 and length(a.kode) = 5 and left(a.kode,2) = '" . $id_prov . "'";
        $where = ["a.nama"];

        parent::_loadSelect2($data, $query, $where);
    }

    function kec_select_get()
    {
        $data = $this->request->getGet();
        $id_kab = $data["id_kab"];
        $query = "select a.*, a.kode as id, a.nama as text from m_lokasi_nik_ind a where a.is_deleted = 0 and length(a.kode) = 8 and left(a.kode,5) = '" . $id_kab . "'";
        $where = ["a.nama"];

        parent::_loadSelect2($data, $query, $where);
    }

    function kel_select_get()
    {
        $data = $this->request->getGet();
        $id_kec = $data["id_kec"];
        $query = "select a.*, a.kode as id, a.nama as text from m_lokasi_nik_ind a where a.is_deleted = 0 and length(a.kode) = 13 and left(a.kode,8) = '" . $id_kec . "'";
        $where = ["a.nama"];

        parent::_loadSelect2($data, $query, $where);
    }

    function satker_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.*, a.satker as text from m_satker a where a.is_deleted = 0";
        $where = ["a.satker"];

        parent::_loadSelect2($data, $query, $where);
    }

    function pendidik_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT a.id_m_user AS id, a.namagadik AS text
                    FROM m_user_pendidik a
                    WHERE a.is_deleted = 0";
        $where = ["a.namagadik"];

        parent::_loadSelect2($data, $query, $where);
    }

    function kategori_pelanggaran_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.*, CONCAT (a.kategori,' (',a.min_poin,' - ',a.max_poin,')') as text from m_kategori_pelanggaran_karakter a where a.is_deleted = 0";
        $where = ["a.kategori"];

        parent::_loadSelect2($data, $query, $where);
    }

    function karakter_penilaian_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.*, a.karakter as text from m_karakter_penilaian a where a.is_deleted = 0";
        $where = ["a.karakter"];

        parent::_loadSelect2($data, $query, $where);
    }

    function aspek_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.*, a.aspek as text from m_aspek a where a.is_deleted = 0";
        $where = ["a.aspek"];

        parent::_loadSelect2($data, $query, $where);
    }

    function semester_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.*, a.semester as text from m_semester a where a.is_deleted = 0";
        $where = ["a.semester"];

        parent::_loadSelect2($data, $query, $where);
    }

    function taruna_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, concat(a.nik, ' | ' , a.namataruna) as text, a.nik, a.namataruna from m_user_taruna a where a.is_deleted = 0";
        $where = ["a.namataruna"];

        parent::_loadSelect2($data, $query, $where);
    }

    function kelompok_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, kelompok as text from m_kelompok a where a.is_deleted = 0";
        $where = ["a.kelompok"];

        parent::_loadSelect2($data, $query, $where);
    }

    function semester_select_where_get()
    {
        $data = $this->request->getGet();
        $id_prov = $data["id_prov"];
        $query = "select a.*, a.kode as id, a.nama as text from m_lokasi_nik_ind a where a.is_deleted = 0 and length(a.kode) = 5 and left(a.kode,2) = '" . $id_prov . "'";
        $where = ["a.nama"];

        parent::_loadSelect2($data, $query, $where);
    }

    function jabatan_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id as id, a.jabatan as text from m_tingkatan_detail a where a.is_deleted = 0 ";
        $where = ["a.jabatan"];

        parent::_loadSelect2($data, $query, $where);
    }

    function pujian_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT a.kode as id, concat(a.keterangan ,' - ',b.tingkatan) as text, a.id_tingkatan , a.id_kurikulum , b.tingkatan , c.tentang from m_pujian_karakter a left join m_tingkatan b on a.id_tingkatan=b.id left join m_kurikulum c on a.id_kurikulum=c.id where a.is_deleted = 0 ";
        $where = ["a.keterangan", "b.tingkatan"];
        $orderby = "order by a.id_tingkatan ASC , a.kode ASC";

        parent::_loadSelect2orderby($data, $query, $where, $orderby);
    }

    function matapelajaran_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, a.mata_pelajaran as text, a.kode_mk from m_mata_pelajaran a where a.is_deleted = 0";
        $where = ["a.mata_pelajaran"];

        parent::_loadSelect2($data, $query, $where);
    }

    function config_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, a.kode as text , parameter , nilai from m_config a where a.is_deleted = 0";
        $where = ["a.kode"];

        parent::_loadSelect2($data, $query, $where);
    }


    function gedung_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, concat(a.kode, ' | ' , a.nama) as text from m_gedung a where a.is_deleted = 0";
        $where = ["a.kode", "a.nama"];

        parent::_loadSelect2($data, $query, $where);
    }

    function variabelpujian_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT * FROM (SELECT b.id, concat(b.indikator , ' ( ' , c.tingkatan, ' )') as text, b.is_deleted  from m_pujian_karakter_ref1 b inner join m_tingkatan c on b.id_tingkatan=c.id where b.is_deleted='0' ) a where a.is_deleted='0' ";
        $where = ["a.text"];

        parent::_loadSelect2($data, $query, $where);
    }

    function namattd_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT * FROM ( select a.id, concat(a.jabatan, ' - ' , b.nama) as text, a.is_deleted from m_ttd_jabatan a left join m_ttd b on a.id=b.id_jabatan where a.is_deleted='0' and b.is_aktiv='1' ) a where a.is_deleted='0' ";
        $where = ["a.text"];

        parent::_loadSelect2($data, $query, $where);
    }

    function namamenu_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT url as id, name as text FROM m_menu a where a.is_deleted='0' ";
        $where = ["a.name"];

        parent::_loadSelect2($data, $query, $where);
    }

    function ttdjabatan_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT id, jabatan as text FROM m_ttd_jabatan a where a.is_deleted='0' ";
        $where = ["a.jabatan"];

        parent::_loadSelect2($data, $query, $where);
    }

    function indikatorpujian_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT * FROM (SELECT a.*, a.indikator as text from m_pujian_karakter_ref2 a
                    inner join m_pujian_karakter_ref1 b on a.id_pen_kar_ref1=b.id
                    where a.id_pen_kar_ref1='" . $data['id_pen_kar_ref1'] . "' and b.is_deleted='0' ) a where a.is_deleted='0' ";
        $where = ["a.text"];

        parent::_loadSelect2($data, $query, $where);
    }

    function itempujian_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT * FROM (SELECT a.*, a.item_nilai as text 
                    from m_pujian_karakter_ref3 a
                    inner join m_pujian_karakter_ref2 b on a.id_pen_kar_ref2=b.id
                    inner join m_pujian_karakter_ref1 c on b.id_pen_kar_ref1=c.id
                    where a.id_pen_kar_ref2='" . $data['id_pen_kar_ref2'] . "' and b.is_deleted='0' ) a where a.is_deleted='0' ";
        $where = ["a.text"];

        parent::_loadSelect2($data, $query, $where);
    }

    function ruangkelas_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, concat(a.kode, ' | ' , a.nama) as text from m_ruang_kelas a where a.is_deleted = 0";
        $where = ["a.kode", "a.nama"];

        parent::_loadSelect2($data, $query, $where);
    }

    function matapelajaran_list_get()
    {
        $data = $this->request->getGet();

        $id_semester = '1';
        $tahun_ajaran = '2021';
        $id_mata_pelajaran = $this->request->getGet('id_mata_pelajaran');
        // $id_mata_pelajaran = '3';

        // $query = "SELECT z.id_kelompok,z.kelompok, json_arrayagg(json_object(
        //             'id_mata_pelajaran', z.id_mata_pelajaran,
        //             'kode_mk', z.kode_mk,
        //             'mata_pelajaran', z.mata_pelajaran,
        //             'id_user_pendidik', z.id_user_pendidik,
        //             'nama_pendidik', z.namagadik,
        //             'id_bahan_ajar', z.id_bahan_ajar,
        //             'jumlah_pertemuan', z.jumlah_pertemuan,
        //             'pertemuan_ke', z.pertemuan_ke,
        //             'sisa_pertemuan', z.sisa_pertemuan)
        //         ) as detail
        //             from
        //             (select 
        //                 a.id_kelompok,
        //                 f.kelompok,
        //                 a.id_tingkat,
        //                 a.id_semester,
        //                 a.tahun_ajaran_awal as tahun_ajaran,
        //                 c.id as id_mata_pelajaran,
        //                 c.kode_mk,
        //                 c.mata_pelajaran,
        //                 b.id_user_pendidik,
        //                 e.namagadik,
        //                 b.id as id_bahan_ajar,
        //                 b.judul,
        //                 if(c.is_teori=1,c.jumlah_pertemuan*2,c.jumlah_pertemuan*1) as jumlah_pertemuan,
        //                 if(c.is_teori=1,b.pertemuan_ke*2,b.pertemuan_ke*1) as pertemuan_ke,
        //                 if(c.is_teori=1,c.jumlah_pertemuan*2,c.jumlah_pertemuan*1)-if(c.is_teori=1,b.pertemuan_ke*2,b.pertemuan_ke*1) as sisa_pertemuan
        //             from t_kelompok_taruna a 
        //             inner join t_bahan_ajar b
        //                 on a.tahun_ajaran_awal = b.tahun_ajaran
        //                 and a.id_semester = b.id_semester
        //             inner join m_mata_pelajaran c
        //                 on b.id_mata_pelajaran = c.id
        //             left join m_user_pendidik e
        //                 on e.id_m_user = b.id_user_pendidik
        //             left join t_jadwal d
        //                 on d.id_kelompok_taruna = a.id_kelompok
        //                 and d.id_bahan_ajar = b.id
        //             left join m_kelompok f on a.id_kelompok=f.id
        //             where a.tahun_ajaran_awal = '".$tahun_ajaran."'
        //                 and a.id_semester = '".$id_semester."'
        //                 and b.id_mata_pelajaran = '".$id_mata_pelajaran."'
        //                 and b.is_ujian = 0
        //                 and d.id is null
        //             group by a.id_kelompok, a.id_semester, a.tahun_ajaran_awal, b.id) z
        //             group by z.id_kelompok";


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
                                        'sisa_pertemuan', z.sisa_pertemuan)
                                    ) as detail
                    from
                    (select 
                        d.is_deleted,
                        a.id_kelompok,
                        a.id_tingkat,
                        a.id_semester,
                        a.tahun_ajaran_awal as tahun_ajaran,
                        c.id as id_mata_pelajaran,
                        c.kode_mk,
                        c.mata_pelajaran,
                        b.id_user_pendidik,
                        e.namagadik,
                        b.id as id_bahan_ajar,
                        b.judul,
                        if(c.is_teori=1,c.jumlah_pertemuan*2,c.jumlah_pertemuan*1) as jumlah_pertemuan,
                        if(c.is_teori=1,b.pertemuan_ke*2,b.pertemuan_ke*1) as pertemuan_ke,
                        if(c.is_teori=1,c.jumlah_pertemuan*2,c.jumlah_pertemuan*1)-if(c.is_teori=1,b.pertemuan_ke*2,b.pertemuan_ke*1) as sisa_pertemuan,
                        f.kelompok
                    from t_kelompok_taruna a 
                    inner join t_bahan_ajar b
                        on a.tahun_ajaran_awal = b.tahun_ajaran
                        and a.id_semester = b.id_semester
                        and a.is_deleted = 0
                    inner join m_mata_pelajaran c
                        on b.id_mata_pelajaran = c.id
                    left join m_user_pendidik e
                        on e.id_m_user = b.id_user_pendidik
                    left join t_jadwal d
                        on d.id_kelompok_taruna = a.id_kelompok
                        and d.id_bahan_ajar = b.id
                        and d.is_deleted = 0
                    left join m_kelompok f on a.id_kelompok=f.id
                    where a.tahun_ajaran_awal = '" . $tahun_ajaran . "'
                        and a.id_semester = '" . $id_semester . "'
                        and b.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
                        and b.is_ujian = 0
                        and b.is_deleted = 0
                        and d.id is null
                    group by a.id_kelompok, a.id_semester, a.tahun_ajaran_awal, b.id) z
                    group by id_kelompok";

        $data = $this->db->query($query)->getResult();

        $response = ['success' => true, 'data' => $data];

        echo json_encode($response);
    }

    function kurikulum_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT a.*, a.kurikulum AS text
                    FROM m_kurikulum a
                    WHERE a.is_deleted = 0";
        $where = ["a.kurikulum"];

        parent::_loadSelect2($data, $query, $where);
    }



    // action ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function datataruna_load()
    {
        $query = "select a.* from m_user_taruna a where a.is_deleted = 0";
        $where = ["a.nik", "a.namataruna"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function datataruna_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_user_taruna', $data, $userid);
    }

    function datataruna_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.*, a.id_tingkat as id_tingkat_detail, if(a.id_gender=1, 'Laki - Laki' , 'Perempuan') as nama_gender, concat(n.semester, ' - ' ,b.jabatan) as nama_tingkat_detail, c.batalyon as nama_batalyon,
        d.kompi as nama_kompi, e.peleton as nama_peleton, f.suku as nama_suku,
        g.agama as nama_agama, h.pendidikan as nama_tkpendid, i.nama as nama_kabkota_lhr,
        j.nama as nama_prov_ktp, k.nama as nama_kota_kab_ktp, l.nama as nama_kec_ktp, m.nama as nama_kel_ktp from m_user_taruna a 
        left join m_tingkatan_detail b on a.id_tingkat = b.id
        left join m_sm_batalyon c on a.id_batalyon = c.id
        left join m_sm_kompi d on a.id_kompi = d.id
        left join m_sm_peleton e on a.id_peleton = e.id
        left join m_suku f on a.id_suku = f.id
        left join m_agama g on a.id_agama = g.id
        left join m_tingkat_pendidikan_umum h on a.id_tkpendid = h.id
        left join m_lokasi_lahir i on a.id_kabkota_lhr = i.kdkabkota
        left join m_lokasi_nik_ind j on a.id_prov_ktp = j.kode
        left join m_lokasi_nik_ind k on a.id_kota_kab_ktp = k.kode
        left join m_lokasi_nik_ind l on a.id_kec_ktp = l.kode
        left join m_lokasi_nik_ind m on a.id_kel_ktp = m.kode
        left join m_semester n on b.id_semester = n.id
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_usr_taruna', $data, null, $query);
    }

    function datataruna_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_user_taruna', $data, $userid);
    }

    function datapendidik_load()
    {
        $query = "select a.* from m_user_pendidik a where a.is_deleted = 0";
        $where = ["a.nik", "a.namagadik"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function datapendidik_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $file_foto = $this->request->getFile('file_foto');

        if ($file_foto != null) {
            if ($file_foto->isValid() && !$file_foto->hasMoved()) {
                $newName = $file_foto->getRandomName();
                if (!is_dir('./public/photo/gdk/' . $data['nik'])) {
                    $file_foto->move('./public/photo/gdk/' . $data['nik'], $newName);
                }
                $image = \Config\Services::image()
                    ->withFile($file_foto)
                    ->resize(480, 640, true, 'height')
                    ->save('./public/photo/gdk/' . $data['nik'] . '/' . $newName, 80);
                $dir = base_url() . '/public/photo/gdk/' . $data['nik'] . '/' . $newName;
                $data['photopath'] = $dir;
                $response = [
                    'success' => true,
                    'data' => $dir
                ];
            }
        }
        parent::_insert('m_user_pendidik', $data, $userid);
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
        parent::_delete('m_user_pendidik', $data, $userid);
    }

    function datakelompok_load()
    {
        $query = "SELECT a.id,a.kelompok, a.max_kapasitas, a.id_batalyon , concat(b.batalyon , ' ( ', b.tahun_masuk,' )') as nama_batalyon from m_kelompok a  left join m_sm_batalyon b on a.id_batalyon=b.id where a.is_deleted = 0";
        $where = ["a.kelompok", "a.max_kapasitas", "a.batalyon"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function datakelompok_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_kelompok', $data, $userid);
    }

    function datakelompok_edit()
    {

        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT a.id,a.kelompok, a.max_kapasitas, a.id_batalyon , concat(b.batalyon , ' ( ', b.tahun_masuk,' )') as nama_batalyon from m_kelompok a  left join m_sm_batalyon b on a.id_batalyon=b.id
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_kelompok', $data, null, $query);


        // $data = json_decode($this->request->getPost('param'), true);
        // parent::_edit('m_kelompok', $data);
    }

    function datakelompok_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_kelompok', $data, $userid);
    }

    function datakelompoktaruna_load()
    {
        $query = "SELECT a.*, b.namataruna as nama_taruna,c.kelompok as nama_kelompok, d.tingkatan as nama_tingkatan , e.semester as nama_semester
                    from t_kelompok_taruna a
                    left join m_user_taruna b on a.id_taruna=b.id
                    left join m_kelompok c on a.id_kelompok=c.id
                    left join m_tingkatan d on a.id_tingkat=d.id
                    left join m_semester e on a.id_semester=e.id
                    where a.is_deleted = 0";
        $where = ["a.kelompok", "a.max_kapasitas"];
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
        parent::_edit('t_kelompok_taruna', $data);
    }

    function datakelompoktaruna_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('t_kelompok_taruna', $data, $userid);
    }

    function datasatker_load()
    {
        $query = "select a.* from m_satker a where a.is_deleted = 0";
        $where = ["a.satker"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function datasatker_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_satker', $data, $userid);
    }

    function datasatker_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        parent::_edit('m_satker', $data);
    }

    function datasatker_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_satker', $data, $userid);
    }

    function databatalyon_load()
    {
        $query = "SELECT a.*, b.namagadik, c.semester , d.kurikulum
                    FROM m_sm_batalyon a
                    LEFT JOIN m_user_pendidik b ON a.`id_user_pendidik` = b.id_m_user
                    LEFT JOIN m_semester c ON a.id_semester = c.id
                    LEFT JOIN m_kurikulum d ON a.id_kurikulum = d.id
                    WHERE a.is_deleted = 0 AND b.is_deleted = 0 AND c.is_deleted = 0 AND d.is_deleted = 0";
        $where = ["a.batalyon", "a.angkatan", "a.tahun_masuk", "b.namagadik", "c.semester"];
        $data = json_decode($this->request->getPost('param'), true);
        $orderby = "a.angkatan";
        $order = "DESC";

        parent::_loadDatatableorder($query, $where, $data, $orderby, $order);
    }

    function databatalyon_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        parent::_ldapAddOu($data, function ($result) use ($data, $userid) {
            if ($result["success"]) {
                $id_semester = $data['id_semester'];
                $id_batalyon = $data['id'];
                $id_user_pendidik = $data['id_user_pendidik'];

                $updateQuery = "UPDATE m_user_taruna set id_semester = '" . $id_semester . "' where id_batalyon = '" . $id_batalyon . "'";
                $this->db->query($updateQuery);

                $updateQueryuser = "UPDATE m_user set type_code_pgh = 'btl' where id = '" . $id_user_pendidik . "'";
                $this->db->query($updateQueryuser);

                parent::_insert('m_sm_batalyon', $data, $userid);
            } else {
                echo json_encode($result);
            }
        });
    }

    function databatalyon_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.*, b.namagadik as nama_user_pendidik, c.semester as nama_semester, d.kurikulum as nama_kurikulum from 
                        m_sm_batalyon a 
                    left join m_user_pendidik b on a.`id_user_pendidik`=b.id_m_user 
                    left join m_semester c on a.id_semester=c.id 
                    left join m_kurikulum d on a.id_kurikulum=d.id
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_sm_batalyon', $data, null, $query);
    }

    function databatalyon_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_sm_batalyon', $data, $userid);
    }

    function datakompi_load()
    {
        // $query = "select a.* , b.namagadik,c.batalyon from m_sm_kompi a  left join m_user_pendidik b on a.`id_user_pendidik`=b.id_m_user left join m_sm_batalyon c on a.`id_batalyon`=c.id where a.is_deleted = 0";
        $query = "SELECT a.*, b.namagadik, c.batalyon
                    FROM m_sm_kompi a
                    LEFT JOIN m_user_pendidik b ON a.id_user_pendidik = b.id_m_user
                    LEFT JOIN m_sm_batalyon c ON a.id_batalyon = c.id
                    WHERE a.is_deleted = 0 AND b.is_deleted = 0 AND c.is_deleted = 0";
        $where = ["a.kompi", "b.namagadik", "c.batalyon"];
        $data = json_decode($this->request->getPost('param'), true);
        $orderby = "a.kompi ASC, a.id_batalyon";

        parent::_loadDatatableorder($query, $where, $data, $orderby, "DESC");
    }

    function datakompi_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        $id_user_pendidik = $data['id_user_pendidik'];

        $updateQueryuser = "UPDATE m_user set type_code_pgh = 'kmp' where id = '" . $id_user_pendidik . "'";
        $this->db->query($updateQueryuser);

        parent::_insert('m_sm_kompi', $data, $userid);
    }

    function datakompi_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.* , b.namagadik as nama_user_pendidik,c.batalyon as nama_batalyon from m_sm_kompi a  left join m_user_pendidik b on a.`id_user_pendidik`=b.id left join m_sm_batalyon c on a.`id_batalyon`=c.id where a.id = '" . $data['id'] . "'";
        parent::_edit('m_sm_kompi', $data, null, $query);
    }

    function datakompi_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_sm_kompi', $data, $userid);
    }

    function datapeleton_load()
    {
        $query = "SELECT a.* , b.namagadik, c.kompi, d.batalyon
                    FROM m_sm_peleton a
                    LEFT JOIN m_user_pendidik b ON a.id_user_pendidik = b.id_m_user
                    LEFT JOIN m_sm_kompi c ON a.id_kompi = c.id
                    LEFT JOIN m_sm_batalyon d ON c.id_batalyon = d.id
                    WHERE a.is_deleted = 0 AND b.is_deleted = 0 AND c.is_deleted = 0 AND d.is_deleted = 0";
        $where = ["a.peleton", "b.namagadik", "c.kompi", "d.batalyon"];
        $data = json_decode($this->request->getPost('param'), true);
        $orderby = "a.id ASC, c.kompi ASC, c.id_batalyon";
        $order = "DESC";

        parent::_loadDatatableorder($query, $where, $data, $orderby, $order);
    }

    function datapeleton_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        $id_user_pendidik = $data['id_user_pendidik'];

        $updateQueryuser = "UPDATE m_user set type_code_pgh = 'ptn' where id = '" . $id_user_pendidik . "'";
        $this->db->query($updateQueryuser);

        parent::_insert('m_sm_peleton', $data, $userid);
    }

    function datapeleton_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.* , b.namagadik as nama_user_pendidik,c.kompi as nama_kompi from m_sm_peleton a  left join m_user_pendidik b on a.`id_user_pendidik`=b.id left join m_sm_kompi c on a.`id_kompi`=c.id
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_sm_peleton', $data, null, $query);
    }

    function datapeleton_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_sm_peleton', $data, $userid);
    }

    function dataaspek_load()
    {
        $query = "select a.* from m_aspek a where a.is_deleted = 0";
        $where = ["a.aspek", "a.min_nilai"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function dataaspek_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_aspek', $data, $userid);
    }

    function dataaspek_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.* from m_aspek a
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_aspek', $data, null, $query);
    }

    function dataaspek_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_aspek', $data, $userid);
    }

    function datakurikulum_load()
    {
        $query = "select a.* from m_kurikulum a where a.is_deleted = 0";
        $where = ["a.nomor", "a.tahun", "a.tentang"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function datakurikulum_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_kurikulum', $data, $userid);
    }

    function datakurikulum_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.* from m_kurikulum a
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_kurikulum', $data, null, $query);
    }

    function datakurikulum_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_kurikulum', $data, $userid);
    }

    function datakarakterpenilaian_load()
    {
        $query = "select a.* from m_karakter_penilaian a where a.is_deleted = 0";
        $where = ["a.karakter"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function datakarakterpenilaian_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_karakter_penilaian', $data, $userid);
    }

    function datakarakterpenilaian_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.* from m_karakter_penilaian a
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_karakter_penilaian', $data, null, $query);
    }

    function datakarakterpenilaian_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_karakter_penilaian', $data, $userid);
    }

    function datakategoripelanggaran_load()
    {
        $query = "select a.* from m_kategori_pelanggaran_karakter a where a.is_deleted = 0";
        $where = ["a.kategori", "a.min_poin", "a.max_poin"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function datakategoripelanggaran_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_kategori_pelanggaran_karakter', $data, $userid);
    }

    function datakategoripelanggaran_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.* from m_kategori_pelanggaran_karakter a
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_kategori_pelanggaran_karakter', $data, null, $query);
    }

    function datakategoripelanggaran_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_kategori_pelanggaran_karakter', $data, $userid);
    }

    function datapelanggaran_load()
    {
        $query = "select a.*, b.kategori, c.karakter from m_pelanggaran_karakter a left join m_kategori_pelanggaran_karakter b on a.id_kategori_pelanggaran=b.id left join m_karakter_penilaian c on a.id_karakter_penilaian=c.id where a.is_deleted = 0";
        $where = ["b.kategori", "c.karakter", "a.dasar_hukum", "a.deskripsi"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function datapelanggaran_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_pelanggaran_karakter', $data, $userid);
    }

    function datapelanggaran_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.*,a.id_kategori_pelanggaran, CONCAT (b.kategori,' (',b.min_poin,' - ',b.max_poin,')') as nama_kategori_pelanggaran  ,b.kategori, c.karakter as nama_karakter_penilaian  from m_pelanggaran_karakter a left join m_kategori_pelanggaran_karakter b on a.id_kategori_pelanggaran=b.id left join m_karakter_penilaian c on a.id_karakter_penilaian=c.id where a.is_deleted = 0 and a.id = '" . $data['id'] . "'";
        parent::_edit('m_pelanggaran_karakter', $data, null, $query);
    }

    function datapelanggaran_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_pelanggaran_karakter', $data, $userid);
    }


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

    function datajampertemuan_load()
    {
        $query = "select a.* from m_jam_pertemuan a where a.is_deleted = 0";
        $where = ["a.unit", "a.jam_mulai", "a.jam_selesai", "a.keterangan"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function datajampertemuan_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_jam_pertemuan', $data, $userid);
    }

    function datajampertemuan_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.* from m_jam_pertemuan a
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_jam_pertemuan', $data, null, $query);
    }

    function datajampertemuan_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_jam_pertemuan', $data, $userid);
    }

    function datamatapelajaran_load()
    {
        $query = "select a.*, b.aspek , d.tentang from m_mata_pelajaran a
                    left join m_aspek b on a.id_aspek=b.id
                    left join m_kurikulum d on a.id_kurikulum=d.id where a.is_deleted = 0";
        $where = ["a.kode_mk", "a.mata_pelajaran"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
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
        $query = "select a.*, b.aspek as nama_aspek ,  d.tentang as nama_kurikulum from m_mata_pelajaran a
                    left join m_aspek b on a.id_aspek=b.id
                    left join m_kurikulum d on a.id_kurikulum=d.id
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_mata_pelajaran', $data, null, $query);
    }

    function datamatapelajaran_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_mata_pelajaran', $data, $userid);
    }

    function datapujian_load()
    {
        $query = "SELECT a.*, b.tingkatan
                    from m_pujian_karakter a
                    left join m_tingkatan b on a.id_tingkatan=b.id
                    where a.is_deleted='0' ";
        $where = ["a.kode", "a.keterangan", "a.bobot", "b.tingkatan"];
        $data = json_decode($this->request->getPost('param'), true);
        $order = " a.id_tingkatan , a.kode ";

        // parent::_loadDatatable($query, $where, $data);
        parent::_loadDatatableorder($query, $where, $data, $order);
    }

    function datapujian_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_pujian_karakter', $data, $userid);
    }

    function datapujian_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "select a.*, b.aspek as nama_aspek , c.semester as nama_semester, d.tentang as nama_kurikulum from m_mata_pelajaran a
                    left join m_aspek b on a.id_aspek=b.id
                    left join m_semester c on a.id_semester=c.id
                    left join m_kurikulum d on a.id_kurikulum=d.id
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_pujian_karakter', $data, null, $query);
    }

    function datapujian_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_pujian_karakter', $data, $userid);
    }


    function configparameter_load()
    {
        $query = "SELECT * from m_config a 
                    where a.is_deleted='0' ";
        $where = ["a.kode", "a.parameter", "a.nilai"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function configparameter_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_config', $data, $userid);
    }

    function configparameter_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT * from m_config a
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_config', $data, null, $query);
    }

    function configparameter_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_config', $data, $userid);
    }


    function datagedung_load()
    {
        $query = "SELECT * from m_gedung a 
                    where a.is_deleted='0' ";
        $where = ["a.kode", "a.nama"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function datagedung_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_gedung', $data, $userid);
    }

    function datagedung_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT * from m_gedung a
        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_gedung', $data, null, $query);
    }

    function datagedung_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_gedung', $data, $userid);
    }

    function variabelpujian_load()
    {
        $query = "SELECT a.* , b.tingkatan from m_pujian_karakter_ref1 a inner join m_tingkatan b on a.id_tingkatan=b.id where a.is_deleted='0' ";
        $where = ["a.indikator", "a.nilai_awal", "b.tingkatan"];
        $data = json_decode($this->request->getPost('param'), true);
        $order = " a.id_tingkatan , a.kode ";

        // parent::_loadDatatable($query, $where, $data);
        parent::_loadDatatableorder($query, $where, $data, $order);
    }

    function variabelpujian_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_pujian_karakter_ref1', $data, $userid);
    }

    function variabelpujian_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.* , b.tingkatan as nama_tingkatan from m_pujian_karakter_ref1 a inner join m_tingkatan b on a.id_tingkatan=b.id where a.id = '" . $data['id'] . "'";
        parent::_edit('m_pujian_karakter_ref1', $data, null, $query);
    }

    function variabelpujian_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_pujian_karakter_ref1', $data, $userid);
    }

    function indikatorpujian_load()
    {
        $query = "SELECT * FROM (SELECT a.*, concat(b.indikator , ' ( ' , c.tingkatan, ' )') as variabel  from m_pujian_karakter_ref2 a
                    inner join m_pujian_karakter_ref1 b on a.id_pen_kar_ref1=b.id
                    inner join m_tingkatan c on b.id_tingkatan=c.id 
                    where a.is_deleted='0') a1

                    where a1.is_deleted='0' ";
        $where = ["a1.indikator", "a1.variabel"];
        $data = json_decode($this->request->getPost('param'), true);
        $order = " a1.id_pen_kar_ref1  ";

        parent::_loadDatatableorder($query, $where, $data, $order);
    }

    function indikatorpujian_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_pujian_karakter_ref2', $data, $userid);
    }

    function indikatorpujian_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.*, concat(b.indikator , ' ( ' , c.tingkatan, ' )') as nama_pen_kar_ref1  from m_pujian_karakter_ref2 a
                    inner join m_pujian_karakter_ref1 b on a.id_pen_kar_ref1=b.id
                    inner join m_tingkatan c on b.id_tingkatan=c.id 
                    where a.id = '" . $data['id'] . "'";
        parent::_edit('m_pujian_karakter_ref2', $data, null, $query);
    }

    function indikatorpujian_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_pujian_karakter_ref2', $data, $userid);
    }

    function itempujian_load()
    {
        $query = "SELECT * FROM (SELECT a.*, b.indikator as nama_pen_kar_ref2  from m_pujian_karakter_ref3 a
                    inner join m_pujian_karakter_ref2 b on a.id_pen_kar_ref2=b.id  
                    where a.is_deleted='0') a1

                    where a1.is_deleted='0' ";
        $where = ["a1.item_nilai", "a1.nama_pen_kar_ref2"];
        $data = json_decode($this->request->getPost('param'), true);
        $order = " a1.id_pen_kar_ref2 ";

        parent::_loadDatatableorder($query, $where, $data, $order);
    }

    function itempujian_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_pujian_karakter_ref3', $data, $userid);
    }

    function itempujian_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.*, b.id_pen_kar_ref1, concat(c.indikator , ' ( ' , d.tingkatan, ' )') as nama_pen_kar_ref1 , b.indikator as nama_pen_kar_ref2  from m_pujian_karakter_ref3 a
                    inner join m_pujian_karakter_ref2 b on a.id_pen_kar_ref2=b.id
                    inner join m_pujian_karakter_ref1 c on b.id_pen_kar_ref1=c.id
                    inner join m_tingkatan d on c.id_tingkatan=d.id 
                    where a.id = '" . $data['id'] . "'";
        parent::_edit('m_pujian_karakter_ref3', $data, null, $query);
    }

    function itempujian_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_pujian_karakter_ref3', $data, $userid);
    }

    function pointpujian_load()
    {
        $query = "SELECT * FROM (SELECT a.* , b.item_nilai from m_pujian_karakter_ref4 a
                    inner join m_pujian_karakter_ref3 b on a.id_pen_kar_ref3=b.id where a.is_deleted='0') a1

                    where a1.is_deleted='0' ";
        $where = ["a1.item_nilai", "a1.keterangan", "a1.bobot"];
        $data = json_decode($this->request->getPost('param'), true);
        $order = " a1.id_pen_kar_ref3 ";

        parent::_loadDatatableorder($query, $where, $data, $order);
    }

    function pointpujian_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_pujian_karakter_ref4', $data, $userid);
    }

    function pointpujian_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.*, c.id_pen_kar_ref1, concat(d.indikator , ' ( ' , e.tingkatan, ' )') as nama_pen_kar_ref1 ,b.id_pen_kar_ref2, c.indikator as nama_pen_kar_ref2 , b.item_nilai as nama_pen_kar_ref3  
                    from m_pujian_karakter_ref4 a
                    inner join m_pujian_karakter_ref3 b on a.id_pen_kar_ref3=b.id
                    inner join m_pujian_karakter_ref2 c on b.id_pen_kar_ref2=c.id
                    inner join m_pujian_karakter_ref1 d on c.id_pen_kar_ref1=d.id
                    inner join m_tingkatan e on d.id_tingkatan=e.id  
                    where a.id = '" . $data['id'] . "'";
        parent::_edit('m_pujian_karakter_ref4', $data, null, $query);
    }

    function pointpujian_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_pujian_karakter_ref4', $data, $userid);
    }



    // begin data Ruang Kelas done

    function kalenderakademik_load()
    {

        $userid = $this->request->getPost('userid');
        $usertype = $this->request->getPost('usertype');
        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT a.*
                    from m_kalender_akademik a
                    where a.is_deleted='0'";
        $where = ["a.nama", "a.tahun_ajaran"];
        $order = " a.created_at ";

        parent::_loadDatatableorder($query, $where, $data, $order, 'desc');
    }

    function kalenderakademik_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $userdetail = json_decode($this->request->getPost('userdetail'), true);
        $usertype = $this->request->getPost('usertype');
        $jenisfile = $data['jenisfile'];

        if ($jenisfile == '1') {
            $file_kalender_akademik = $this->request->getFile('file_kalender_akademik');
            $filetype = $this->request->getPost('filetype');
            if ($file_kalender_akademik != null) {
                if ($file_kalender_akademik->isValid() && !$file_kalender_akademik->hasMoved()) {
                    $newName = $file_kalender_akademik->getRandomName();
                    if ($usertype == "sad") {
                        $file_kalender_akademik->move('./public/file_penugasan/sad/' . $userid, $newName);
                        $dir = base_url() . '/public/file_penugasan/sad/' . $userid . '/' . $newName;
                    } else {
                        $file_kalender_akademik->move('./public/file_penugasan/' . $usertype . '/' . $userdetail['nik'], $newName);
                        $dir = base_url() . '/public/file_penugasan/' . $usertype . '/' . $userdetail['nik'] . '/' . $newName;
                    }



                    $id_tipe_file = $this->db->query("SELECT * FROM m_tipe_file
                    WHERE tipe_file = '" . $filetype . "'");
                    if ($id_tipe_file->getNumRows() > 0) {
                        $data['id_tipe_file'] = $id_tipe_file->getRow()->id;
                    }

                    $data['file_kalender_akademik'] = $dir;
                    $response = [
                        'success' => true,
                        'data' => $dir
                    ];
                }
            }
        } else {
            $data['file_kalender_akademik'] = $data['link_materi_tugas'];
        }
        $data['type_upload'] = $data['jenisfile'];

        unset($data['jenisfile']);
        unset($data['link_materi_tugas']);
        parent::_insert('m_kalender_akademik', $data, $userid);
    }

    function kalenderakademik_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.*
                    from m_kalender_akademik a
                    where a.id = '" . $data['id'] . "'";
        parent::_edit('m_kalender_akademik', $data, null, $query);
    }

    function kalenderakademik_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_kalender_akademik', $data, $userid);
    }


    function datattd_load()
    {
        $query = "SELECT b.id, a.jabatan, ifnull(b.nama, 'Belum Ada') as nama  , ifnull(b.nrp, 'Belum Ada') as nrp, b.tanggal_mulai, b.is_aktiv from m_ttd_jabatan a inner join m_ttd b on a.id=b.id_jabatan and b.is_deleted='0' where a.is_deleted='0'";
        $where = ["a.jabatan", "b.nama", "b.nrp", "b.tanggal_mulai"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function datattd_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_ttd', $data, $userid);
    }

    function datattd_saveaktiv()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $cekjabatan = $this->db->query("SELECT a.id_jabatan from m_ttd a where a.id='" . $data['id'] . "'")->getRow();
        $updatejabatan = $this->db->query("UPDATE m_ttd set is_aktiv='0' where id_jabatan='" . $cekjabatan->id_jabatan . "'");
        parent::_insert('m_ttd', $data, $userid);
    }

    function datattd_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.id as id_jabatan, a.jabatan as nama_jabatan, b.id, b.nama, b.nrp, b.url_ttd, b.tanggal_mulai, b.tanggal_selesai from m_ttd_jabatan a
                    left join m_ttd b on a.id=b.id_jabatan
        where b.id = '" . $data['id'] . "'";
        parent::_edit('m_ttd', $data, null, $query);
    }

    function datattd_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_ttd', $data, $userid);
    }

    function configurasittd_load()
    {
        // $query = "SELECT a.id, b.nama, b.id_jabatan, b.nrp, a.id_taruna, c.name FROM t_ttd a inner join m_ttd b on a.id_ttd=b.id and b.is_deleted='0' inner join m_menu c on a.id_menu=c.url and c.is_deleted='0' where a.is_deleted='0' ";
        $query = "SELECT a.id, c.nama, b.jabatan as jabatan, c.nrp, a.id_taruna, d.name 
FROM t_ttd a 
inner join m_ttd_jabatan b on a.id_ttd_jabatan=b.id and b.is_deleted='0' 
inner join m_ttd c on b.id=c.id_jabatan and c.is_aktiv='1'
inner join m_menu d on a.id_menu=d.url and d.is_deleted='0' 
where a.is_deleted='0' ";
        $where = ["c.name", "b.nama", "b.jabatan"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function configurasittd_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('t_ttd', $data, $userid);
    }

    function configurasittd_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        // $query = "SELECT a.*, concat(b.nama, ' - ' , b.jabatan) as nama_ttd, c.name as nama_menu , if(a.id_taruna=1, 'YA', 'TIDAK') as nama_taruna FROM t_ttd a inner join m_ttd b on a.id_ttd=b.id and b.is_deleted='0' inner join m_menu c on a.id_menu=c.url and c.is_deleted='0'
        // where a.id = '" . $data['id'] . "'";

        $query = "SELECT a.id, b.id as id_ttd_jabatan, concat(b.jabatan, ' - ' , c.nama) as nama_ttd_jabatan 
, a.id_menu , d.name as nama_menu , a.id_taruna
from t_ttd a
inner join m_ttd_jabatan b on a.id_ttd_jabatan=b.id
inner join m_ttd c on b.id=c.id_jabatan
inner join m_menu d on a.id_menu=d.url and d.is_deleted='0'
        where a.id = '" . $data['id'] . "'";
        parent::_edit('t_ttd', $data, null, $query);
    }

    function configurasittd_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('t_ttd', $data, $userid);
    }

    function jabatanttd_load()
    {
        $query = "SELECT * FROM m_ttd_jabatan a where a.is_deleted='0' ";
        $where = ["a.jabatan"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function jabatanttd_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_ttd_jabatan', $data, $userid);
    }

    function jabatanttd_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.* FROM m_ttd_jabatan a
                        where a.id = '" . $data['id'] . "'";
        parent::_edit('m_ttd_jabatan', $data, null, $query);
    }

    function jabatanttd_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_ttd_jabatan', $data, $userid);
    }
}
