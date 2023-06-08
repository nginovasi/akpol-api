<?php

namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;

class WebPortal extends BaseController
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


    function buktipendaftaran_download()
    {
        $data = json_decode($this->request->getPost('param'), true);
        // $query = "SELECT c.kode_mk ,c.is_sks, c.mata_pelajaran , c.nilai , c.satuan, e.namagadik, f.aspek
        //             from m_user_taruna a
        //             left join t_kelompok_taruna b on a.id_m_user=b.id_taruna
        //             left join m_mata_pelajaran c on b.id_semester=c.id_semester_
        //             left join t_pendidik_mata_pelajaran d on c.id=d.id_mata_pelajaran and d.is_ketua_tim='1'
        //             left join m_user_pendidik e on d.id_pendidik=e.id_m_user
        //             left join m_aspek f on c.id_aspek=f.id
        //             where a.id_m_user='2769'";


        // $rs['data'] = $this->db->query($query)->getResult();
        $rs['nama_file'] = "KRS";

        echo json_encode($rs);
    }


    function khs_download()
    {
        $data = json_decode($this->request->getGetPost('param'), true);
        $id_m_user = $data['id_user'];
        $id_semester = $data['id_semester'];
        // $id_m_user = '4235';
        // $id_semester = '3';
        // $this->db->table('log_logan')->insert(['datanya' => $this->request->getGetPost('param')]);

        $rs['nilai_karakter'] = $this->db->query("SELECT nilai_akhir FROM t_penilaian_aspek_karakter where id_user_taruna='" . $id_m_user . "' and id_semester='" . $id_semester . "' ")->getRow();
        $rs['nilai_jasmani'] = $this->db->query("SELECT nilai_akhir FROM t_penilaian_aspek_jasmani where id_user_taruna='" . $id_m_user . "' and id_semester='" . $id_semester . "' ")->getRow();
        $rs['nilai_kesehatan'] = $this->db->query("SELECT nilai_akhir FROM t_penilaian_aspek_kesehatan where id_user_taruna='" . $id_m_user . "' and id_semester='" . $id_semester . "' ")->getRow();

        $setsession = $this->db->query('SET SESSION group_concat_max_len = 99999');
        $query = "SELECT a.namataruna, a.noaklong, max(a.semester) as semester,
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
                from (select rangkuman.namataruna, rangkuman.noaklong, rangkuman.aspek, 
                json_arrayagg(json_object('matkul',rangkuman.mata_pelajaran,'nilai',rangkuman.nilai_akhir,'bobot',rangkuman.bobot,'klasifikasi',rangkuman.klasifikasi,'sks',concat(rangkuman.nilai,' ',rangkuman.satuan), 'rata', rata_rata)) as nilai,
                sum(rangkuman.nilai_akhir) as jml, count(1) as pembagi, group_concat(distinct rangkuman.semester separator '/') as semester, rangkuman.id_semester
                from (select c.namataruna, c.noaklong, a.mata_pelajaran, x.semester, b.id_semester, b.satuan, b.nilai, y.rata_rata,
                CASE
                    WHEN b.id_aspek = '1' THEN ifnull(d.nilai_akhir,0)
                    WHEN b.id_aspek = '2' THEN ifnull(e.nilai_akhir,0)     
                END as nilai_akhir,
                CASE
                    WHEN b.id_aspek = '1' THEN ifnull(d.klasifikasi,'')
                    WHEN b.id_aspek = '2' THEN ifnull(e.klasifikasi,'')
                END as klasifikasi,
                CASE
                    WHEN b.id_aspek = '1' THEN ifnull(d.bobot,0)
                    WHEN b.id_aspek = '2' THEN ifnull(e.bobot,0)
                END as bobot, z.aspek
                FROM m_mata_pelajaran a
                left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
                left join m_user_taruna c on c.id_batalyon = b.id_batalyon
                left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
                left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
                left join m_aspek z on z.id = b.id_aspek
                left join m_semester x on x.id = b.id_semester
                left join (
                    select rangkuman.semester, rangkuman.id as id_mata_pelajaran, mata_pelajaran, ifnull(avg(nilai_akhir),0) as rata_rata, id_batalyon from (
                    select a.id, c.namataruna, c.noaklong, a.mata_pelajaran, x.semester, b.id_semester, b.id_batalyon,
                    CASE
                        WHEN b.id_aspek = '1' THEN ifnull(d.nilai_akhir,0)
                        WHEN b.id_aspek = '2' THEN ifnull(e.nilai_akhir,0)
                    END as nilai_akhir from m_mata_pelajaran a
                    left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
                    left join m_user_taruna c on c.id_batalyon = b.id_batalyon
                    left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
                    left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
                    left join m_semester x on x.id = b.id_semester
                    where a.is_deleted = 0
                    and b.id_semester = '" . $id_semester . "' and b.id_aspek in ('1', '2')
                    group by a.id, c.noaklong, b.id_batalyon) rangkuman
                    group by rangkuman.id, rangkuman.id_batalyon
                ) y on a.id = y.id_mata_pelajaran and y.id_batalyon = c.id_batalyon
                where a.is_deleted = 0
                and c.id_m_user = '" . $id_m_user . "'
                and b.id_semester = '" . $id_semester . "'
                group by a.id, c.noaklong, b.id_semester) rangkuman
                group by rangkuman.aspek, rangkuman.id_semester) a
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

        echo json_encode($rs);
    }

    function khs_download_new()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $id_m_user = $data['id_taruna'];
        $id_semester = $data['id_semester'];

        $rs['nilai_karakter'] = $this->db->query("SELECT nilai_akhir FROM t_penilaian_aspek_karakter where id_user_taruna='" . $id_m_user . "' and id_semester='" . $id_semester . "' ")->getRow();
        $rs['nilai_jasmani'] = $this->db->query("SELECT nilai_akhir FROM t_penilaian_aspek_jasmani where id_user_taruna='" . $id_m_user . "' and id_semester='" . $id_semester . "' ")->getRow();
        $rs['nilai_kesehatan'] = $this->db->query("SELECT nilai_akhir FROM t_penilaian_aspek_kesehatan where id_user_taruna='" . $id_m_user . "' and id_semester='" . $id_semester . "' ")->getRow();

        $setsession = $this->db->query('SET SESSION group_concat_max_len = 99999');
        $query = "SELECT a.namataruna, a.noaklong, max(a.semester) as semester,
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
                from (select rangkuman.namataruna, rangkuman.noaklong, rangkuman.aspek, 
                json_arrayagg(json_object('matkul',rangkuman.mata_pelajaran,'nilai',rangkuman.nilai_akhir,'bobot',rangkuman.bobot,'klasifikasi',rangkuman.klasifikasi,'sks',concat(rangkuman.nilai,' ',rangkuman.satuan), 'rata', rata_rata)) as nilai,
                sum(rangkuman.nilai_akhir) as jml, count(1) as pembagi, group_concat(distinct rangkuman.semester separator '/') as semester, rangkuman.id_semester
                from (select c.namataruna, c.noaklong, a.mata_pelajaran, x.semester, b.id_semester, b.satuan, b.nilai, y.rata_rata,
                CASE
                    WHEN b.id_aspek = '1' THEN ifnull(d.nilai_akhir,0)
                    WHEN b.id_aspek = '2' THEN ifnull(e.nilai_akhir,0)
                    
                                       
                END as nilai_akhir,
                CASE
                    WHEN b.id_aspek = '1' THEN ifnull(d.klasifikasi,'')
                    WHEN b.id_aspek = '2' THEN ifnull(e.klasifikasi,'')
                END as klasifikasi,
                CASE
                    WHEN b.id_aspek = '1' THEN ifnull(d.bobot,0)
                    WHEN b.id_aspek = '2' THEN ifnull(e.bobot,0)
                END as bobot,
                z.aspek from m_mata_pelajaran a
                left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
                left join m_user_taruna c on c.id_batalyon = b.id_batalyon
                left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
                left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
                
                
                left join m_aspek z on z.id = b.id_aspek
                left join m_semester x on x.id = b.id_semester
                left join (
                    select rangkuman.semester, rangkuman.id as id_mata_pelajaran, mata_pelajaran, ifnull(avg(nilai_akhir),0) as rata_rata, id_batalyon from (
                    select a.id, c.namataruna, c.noaklong, a.mata_pelajaran, x.semester, b.id_semester, b.id_batalyon,
                    CASE
                        WHEN b.id_aspek = '1' THEN ifnull(d.nilai_akhir,0)
                        WHEN b.id_aspek = '2' THEN ifnull(e.nilai_akhir,0)
                    END as nilai_akhir from m_mata_pelajaran a
                    left join t_program_studi_mata_pelajaran b on b.id_mata_pelajaran = a.id and b.is_deleted = 0
                    left join m_user_taruna c on c.id_batalyon = b.id_batalyon
                    left join t_penilaian_aspek_pengetahuan d on d.id_mata_pelajaran = a.id and d.id_semester = b.id_semester and d.id_user_taruna = c.id_m_user and d.is_deleted = 0
                    left join t_penilaian_aspek_keterampilan e on e.id_mata_pelajaran = a.id and e.id_semester = b.id_semester and e.id_user_taruna = c.id_m_user and e.is_deleted = 0
                    left join m_semester x on x.id = b.id_semester
                    where a.is_deleted = 0
                    and b.id_semester = '" . $id_semester . "' and b.id_aspek in ('1', '2')
                    group by a.id, c.noaklong, b.id_batalyon) rangkuman
                    group by rangkuman.id, rangkuman.id_batalyon
                ) y on a.id = y.id_mata_pelajaran and y.id_batalyon = c.id_batalyon
                where a.is_deleted = 0
                and c.id_m_user = '" . $id_m_user . "'
                and b.id_semester = '" . $id_semester . "'
                group by a.id, c.noaklong, b.id_semester) rangkuman
                group by rangkuman.aspek, rangkuman.id_semester) a
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

        echo json_encode($rs);
    }

    function krs_download()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT c.kode_mk ,c.is_sks, c.mata_pelajaran , c.nilai , c.satuan, e.namagadik, f.aspek
                    from m_user_taruna a
                    left join t_kelompok_taruna b on a.id_m_user=b.id_taruna
                    left join m_mata_pelajaran c on b.id_semester=c.id_semester_
                    left join t_pendidik_mata_pelajaran d on c.id=d.id_mata_pelajaran and d.is_ketua_tim='1'
                    left join m_user_pendidik e on d.id_pendidik=e.id_m_user
                    left join m_aspek f on c.id_aspek=f.id
                    where a.id_m_user='2769'";

        $querytaruna = "";

        $rs['data'] = $this->db->query($query)->getResult();
        $rs['nama_file'] = "KRS";

        echo json_encode($rs);
    }

    function kumulatif_download()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $id_pendidik = $data['id_user'];
        $year = date("Y", strtotime($data['date']));
        $month = date("m", strtotime($data['date']));


        $query = "SELECT a.id , a.tanggal , d.kelompok , e.nama ,  f.mata_pelajaran , c.judul , c.pertemuan_ke
                    from t_jadwal a
                    left join m_user_pendidik b on a.id_user_pendidik=b.id_m_user
                    left join t_bahan_ajar c on a.id_bahan_ajar=c.id
                    left join m_kelompok d on a.id_kelompok_taruna=d.id
                    left join m_ruang_kelas e on a.id_ruang_kelas=e.id
                    left join m_mata_pelajaran f on c.id_mata_pelajaran=f.id
                    where a.is_deleted='0'
                        and a.id_user_pendidik = '" . $id_pendidik . "'
                        and month(a.tanggal)='" . $month . "'
                        and year(a.tanggal)='" . $year . "'
                        ";

        $pendidik = "SELECT a.nrp, a.namagadik
                        FROM m_user_pendidik a 
                        where a.id_m_user='" . $id_pendidik . "'";
        $rs['datapendidik'] = $this->db->query($pendidik)->getRow();

        $rs['data'] = $this->db->query($query)->getResult();
        $rs['nama_file'] = $rs['datapendidik']->namagadik . " - KUMULATIF";
        $rs['tahunbulan'] = date("M / Y", strtotime($data['date']));

        echo json_encode($rs);
    }

    function datataruna_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $photo_taruna = $this->request->getFile('photo_taruna');
        $dataarray = array();
        $dir_kerabat = array();

        $this->db->transStart();

        if ($data['id'] == '') {


            $cekusername = $this->db->query("SELECT id FROM m_user a where username='" . $data['noaklong'] . "' and a.is_deleted='0' ")->getRow();

            if ($cekusername == null) {

                $data['id_m_user'] = '';
            } else {

                $data['id_m_user'] = $cekusername->id;
            }
        }

        if ($photo_taruna != null) {
            if ($photo_taruna->isValid() && !$photo_taruna->hasMoved()) {
                $newName = $photo_taruna->getRandomName();
                if (!is_dir('./public/photo/trn/' . $data['nik'])) {
                    mkdir('./public/photo/trn/' . $data['nik']);
                }
                $photo_taruna->move('./public/photo/trn/' . $data['nik'], $newName);
                // $image = \Config\Services::image()
                //     ->withFile($photo_taruna)
                //     ->resize(480, 640, true, 'height')
                //     ->save('./public/photo/trn/' . $data['nik'] . '/' . $newName, 80);
                $dir = base_url() . '/public/photo/trn/' . $data['nik'] . '/' . $newName;
                $data['photopath'] = $dir;
            }
        }

        if ($this->request->getFileMultiple('photo_kerabat_array')) {
            $kerabatfile = $this->request->getFiles()['photo_kerabat_array'];
            foreach ($kerabatfile as $key => $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $newName = $img->getRandomName();
                    if (!is_dir('./public/photo/trn/' . $data['nik'])) {
                        mkdir('./public/photo/trn/' . $data['nik']);
                    }

                    $img->move('./public/photo/trn/' . $data['nik'] . '/saudara/', $newName);
                    $dir_kerabat[$key] = base_url() . '/public/photo/trn/' . $data['nik'] . '/saudara/' . $newName;
                }
            }
        }

        // echo json_encode($dir_kerabat);


        if (isset($data['nama'])) {
            foreach ($data['nama'] as $key => $photo_kerabat) {

                $elm = array(
                    'id' => $data['id_saudara'][$key],
                    'id_m_user' => $data['id_m_user'],
                    'nama' => $data['nama'][$key],
                    'tanggal_lahir' => $data['tanggal_lahir'][$key],
                    'jenis_kelamin' => $data['jenis_kelamin'][$key],
                    'alamat' => $data['alamat'][$key],
                    'pekerjaan' => $data['pekerjaan'][$key],
                    'hubungan' => $data['hubungan'][$key],
                    'status' => $data['status'][$key]
                );
                if (isset($dir_kerabat[$key])) {
                    $elm['photopath'] = $dir_kerabat[$key];
                } else {
                    $elm['photopath'] = null;
                }
                array_push($dataarray, $elm);
            }
            $hasil = parent::_insertbatch('m_saudara_taruna', $dataarray, $userid, null, true);
        }

        if ($data['id_m_user'] == '' and $data['id'] == '') {
            echo json_encode(array('success' => false, 'message' => 'Data User Tidak Ditemukan'));
        } else {
            $filter = array_filter($data, function ($k) {
                return (is_string($k));
            });

            $transResult = parent::_insertReturn('m_user_taruna', $filter, $userid);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();

            echo json_encode(array('success' => false, 'message' => $this->db->error()));
        } else {

            $this->db->transCommit();
            $transResult;
        }
    }


    function cetakregistrasi_download()
    {
        $data = json_decode($this->request->getPost('param'), true); //noaklong

        $rs['datataruna'] = $this->db->query("SELECT a.*, 
                                            if(a.id_gender=1, 'Laki - Laki' , 'Perempuan') as nama_gender, 
                                            c.batalyon as nama_batalyon,
                                            d.kompi as nama_kompi, e.peleton as nama_peleton, f.suku as nama_suku,
                                            g.agama as nama_agama, h.pendidikan as nama_tkpendid, i.nama as nama_kabkota_lhr,
                                            j.nama as nama_prov_ktp, k.nama as nama_kota_kab_ktp, l.nama as nama_kec_ktp, m.nama as nama_kel_ktp , n.kelompok as nama_kelompok,
                                            o.username, DATE_FORMAT(a.created_at, '%d-%m-%Y %H:%i:%s') as waktu_pendaftaran
                                            from m_user_taruna a 
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
                                            left join m_kelompok n on a.id_kelompok=n.id
                                            left join m_user o on a.id_m_user = o.id
                                            where a.id = ?", array($data['noaklong']))->getRow();
        if ($rs['datataruna'] == null) {
            $rs['datataruna'] = $this->db->query("SELECT a.*, 
                                            if(a.id_gender=1, 'Laki - Laki' , 'Perempuan') as nama_gender, 
                                            c.batalyon as nama_batalyon,
                                            d.kompi as nama_kompi, e.peleton as nama_peleton, f.suku as nama_suku,
                                            g.agama as nama_agama, h.pendidikan as nama_tkpendid, i.nama as nama_kabkota_lhr,
                                            j.nama as nama_prov_ktp, k.nama as nama_kota_kab_ktp, l.nama as nama_kec_ktp, m.nama as nama_kel_ktp , n.kelompok as nama_kelompok,
                                            o.username, DATE_FORMAT(a.created_at, '%d-%m-%Y %H:%i:%s') as waktu_pendaftaran
                                            from m_user_taruna a 
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
                                            left join m_kelompok n on a.id_kelompok=n.id
                                            left join m_user o on a.id_m_user = o.id
                                            where a.noaklong = ?", array($data['noaklong']))->getRow();
        }
        
        $rs['nama_file'] = "Bukti Pendaftaran -" . $rs['datataruna']->namataruna;
        setcookie("selected_id", $data['noaklong'], time() + (86400 * 30), "/");
        echo json_encode($rs);
    }

    function cetakregistrasis_download()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT a.*, 
    if(a.id_gender=1, 'Laki - Laki' , 'Perempuan') as nama_gender, 
    c.batalyon as nama_batalyon,
    d.kompi as nama_kompi, e.peleton as nama_peleton, f.suku as nama_suku,
    g.agama as nama_agama, h.pendidikan as nama_tkpendid, i.nama as nama_kabkota_lhr,
    j.nama as nama_prov_ktp, k.nama as nama_kota_kab_ktp, l.nama as nama_kec_ktp, m.nama as nama_kel_ktp , n.kelompok as nama_kelompok,
    o.username, DATE_FORMAT(a.created_at, '%d-%m-%Y %H:%i:%s') as waktu_pendaftaran
    from m_user_taruna a 
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
    left join m_kelompok n on a.id_kelompok=n.id
    left join m_user o on a.id_m_user = o.id
    -- left join m_semester n on b.id_semester = n.id
    where a.id = 12";

        $rs['datataruna'] = $this->db->query($query, array($data['id']))->getRow();


        $rs['nama_file'] = "Bukti Pendaftaran -" . $rs['datataruna']->namataruna;

        setcookie("selected_id", $data['id'], time() + (86400 * 30), "/");

        echo json_encode($rs);
    }

    function cektaruna()
    {
        $noak = $this->request->getPost('noak');
        $query = "SELECT a.*, 
        if(a.id_gender=1, 'Laki - Laki' , 'Perempuan') as nama_gender, 
        c.batalyon as nama_batalyon,
        d.kompi as nama_kompi, e.peleton as nama_peleton, f.suku as nama_suku,
        g.agama as nama_agama, h.pendidikan as nama_tkpendid, i.nama as nama_kabkota_lhr,
        j.nama as nama_prov_ktp, k.nama as nama_kota_kab_ktp, l.nama as nama_kec_ktp, m.nama as nama_kel_ktp , n.kelompok as nama_kelompok,
        o.username, DATE_FORMAT(a.created_at, '%d-%m-%Y %H:%i:%s') as waktu_pendaftaran
        from m_user_taruna a 
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
        left join m_kelompok n on a.id_kelompok=n.id
        left join m_user o on a.id_m_user = o.id
        -- left join m_semester n on b.id_semester = n.id
        where (a.noakshort = '" . $noak . "' or a.noaklong = '" . $noak . "')";

        // echo $query;

        // $user = $this->db->query($query)->getRow();
        if ($this->db->query($query)->getNumRows() == 2) {
            $user = $this->db->query($query)->getResult();
            foreach ($user as $key => $value) {
                if ($value->is_deleted == 0) {
                    $response = [
                        "success" => TRUE,
                        "icon" => "success",
                        "title" => "Success",
                        "text" => "Berhasil",
                        "user" => $value,
                    ];
                } else {
                    $response = [
                        "success" => false,
                        "icon" => "warning",
                        "title" => "Warning",
                        "text" => "Pengguna Sudah Tidak Aktif"
                    ];
                }
            }
        } else if ($this->db->query($query)->getNumRows() == 1) {
            $user = $this->db->query($query)->getRow();
            if ($user->is_deleted == 0) {
                $response = [
                    "success" => TRUE,
                    "icon" => "success",
                    "title" => "Success",
                    "text" => "Berhasil",
                    "user" => $user,
                ];
            } else {
                $response = [
                    "success" => false,
                    "icon" => "warning",
                    "title" => "Warning",
                    "text" => "Pengguna Sudah Tidak Aktif"
                ];
            }
        } else {
            $response = [
                "success" => false,
                "icon" => "error",
                "title" => "Error",
                "text" => "User Tidak Ditemukan"
            ];
        }

        echo json_encode($response);
    }


    function cekttd()
    {
        $data = json_decode($this->request->getPost('param'), true);


        // $query = "SELECT b.nama, b.jabatan, b.nrp,a.id_taruna, b.url_ttd, a.id_menu as nama_menu FROM t_ttd a inner join m_ttd b on a.id_ttd=b.id and b.is_deleted='0' where a.id_menu='".$data['menuname']."' and a.is_deleted='0' ";

        $query = "SELECT c.nama, b.jabatan as jabatan, c.nrp,a.id_taruna, c.url_ttd, a.id_menu as nama_menu FROM t_ttd a  inner join m_ttd_jabatan b on a.id_ttd_jabatan=b.id and b.is_deleted='0' inner join m_ttd c on b.id=c.id_jabatan and c.is_deleted='0' and c.is_aktiv='1' where a.id_menu='" . $data['menuname'] . "' and a.is_deleted='0' ";

        $user = $this->db->query($query)->getResult();

        echo json_encode($user);
    }

    public function generateOTP()
    {

        $data = json_decode($this->request->getPost('param'), true);


        $kode = mt_rand(1000, 9999);

        $otp_user = 1; //default 1

        $idUser = $data['id'];
        // $tipeuser = $_POST['tipe_user'];

        $query = $this->db->query("SELECT * from m_user where id='" . $idUser . "'")->getRow();

        $tipeuser = $query->type_code;

        // echo $tipeuser;
        if ($tipeuser == 'gdk') {

            $rsnew = $this->db->query("UPDATE m_user_pendidik set telp='0" . $data['telp'] . "' where id_m_user='" . $idUser . "' ");
            $nomor = $this->db->query("SELECT telp,(NOW() + INTERVAL 5 MINUTE)as expired from m_user_pendidik where id_m_user='" . $idUser . "'")->getRow();

            if ($otp_user == 1) {

                $userkey = '408cdfc4202d';
                $passkey = '48846c1d6fd82bded53065dc';
                $telepon =  '"' . $nomor->telp . '"';
                $message = 'Kode OTP untuk SIAKPOL System, masukan kode : *' . $kode . '* untuk melanjutkan proses verifikasi nomor anda,hanya berlaku 5 Menit. Terima Kasih';
                $url = 'https://console.zenziva.net/wareguler/api/sendWA/';
                $curlHandle = curl_init();
                curl_setopt($curlHandle, CURLOPT_URL, $url);
                curl_setopt($curlHandle, CURLOPT_HEADER, 0);
                curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
                curl_setopt($curlHandle, CURLOPT_POST, 1);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
                    'userkey' => $userkey,
                    'passkey' => $passkey,
                    'to' => $telepon,
                    'message' => $message
                ));

                $results = json_decode($this->utf8ize(curl_exec($curlHandle)), true);
                curl_close($curlHandle);

                $datalog =  [
                    'ip' =>  $this->request->getIPAddress(),
                    'nomor'  => $nomor->telp,
                    'resp'  => $kode,
                    'log' => json_encode($this->utf8ize($results))
                ];

                $this->db->table('log_otp')->insert($datalog);


                $rs = $this->db->query("UPDATE m_user a
                                            set a.uniq_code='" . $kode . "',a.expired_time='" . $nomor->expired . "' 
                                            where a.id='" . $idUser . "'");

                if ($this->db->affectedRows() > 0) {

                    $response = [
                        'status'  => 1,
                        'kodeOTP' => $kode,
                        'message' => 'Success',
                        'result'  => $results,
                        'is_otp'  => $otp_user
                    ];
                    return $this->response->setJSON($response);
                } else {

                    $response = [
                        'status' => 0,
                        'message' => 'Failed'
                    ];
                    return $this->response->setJSON($response);
                }
            } else {

                $response = [
                    'status' => 1,
                    'kodeOTP' => $kode,
                    'message' => 'Success',
                    'result'    => NULL,
                    'is_otp'  => $otp_user
                ];

                return $this->response->setJSON($response);
            }
        } else if ($tipeuser == 'trn') {
            $rsnew = $this->db->query("UPDATE m_user_taruna set telp='0" . $data['telp'] . "' where id_m_user='" . $idUser . "'");
            $nomor = $this->db->query("SELECT telp,(NOW() + INTERVAL 5 MINUTE)as expired from m_user_taruna where id_m_user='" . $idUser . "'")->getRow();

            if ($otp_user == 1) {

                $userkey = '408cdfc4202d';
                $passkey = '48846c1d6fd82bded53065dc';
                $telepon =  '"' . $nomor->telp . '"';
                $message = 'Kode OTP untuk SIAKPOL System, masukan kode : *' . $kode . '* untuk melanjutkan proses verifikasi nomor anda,hanya berlaku 5 Menit. Terima Kasih';
                $url = 'https://console.zenziva.net/wareguler/api/sendWA/';
                $curlHandle = curl_init();
                curl_setopt($curlHandle, CURLOPT_URL, $url);
                curl_setopt($curlHandle, CURLOPT_HEADER, 0);
                curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
                curl_setopt($curlHandle, CURLOPT_POST, 1);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
                    'userkey' => $userkey,
                    'passkey' => $passkey,
                    'to' => $telepon,
                    'message' => $message
                ));

                $results = json_decode($this->utf8ize(curl_exec($curlHandle)), true);
                curl_close($curlHandle);

                $datalog =  [
                    'ip' =>  $this->request->getIPAddress(),
                    'nomor'  => $nomor->telp,
                    'resp'  => $kode,
                    'log' => json_encode($this->utf8ize($results))
                ];

                $this->db->table('log_otp')->insert($datalog);


                $rs = $this->db->query("UPDATE m_user a
                                            set a.uniq_code='" . $kode . "',a.expired_time='" . $nomor->expired . "' 
                                            where a.id='" . $idUser . "'");

                if ($this->db->affectedRows() > 0) {

                    $response = [
                        'status'  => 1,
                        'kodeOTP' => $kode,
                        'message' => 'Success',
                        'result'  => $results,
                        'is_otp'  => $otp_user
                    ];
                    return $this->response->setJSON($response);
                } else {

                    $response = [
                        'status' => 0,
                        'message' => 'Failed'
                    ];
                    return $this->response->setJSON($response);
                }
            } else {

                $response = [
                    'status' => 1,
                    'kodeOTP' => $kode,
                    'message' => 'Success',
                    'result'    => NULL,
                    'is_otp'  => $otp_user
                ];

                return $this->response->setJSON($response);
            }
        } else {
            $response = [
                'status' => 0,
                'message' => 'Tipe User Tidak Ditemukan'
            ];
            return $this->response->setJSON($response);
        }
    }

    public function prestasi_upload()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $file = $this->request->getFile('file');
        $originalName = $data['originalName'];
        $extension = $data['extension'];
        $size = $data['size'];

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $dir = '';
            $file->move('./public/file_prestasi/' . $newName);
            $dir = 'public/file_prestasi/' . $newName;

            $data = [
                'dir' => $dir,
                'ext' => $extension,
                'originalName' => $originalName,
                'size' => $size
            ];
            $response = [
                'success' => true,
                'data' => $data
            ];

            echo json_encode($response);
        } else {
            $response = [
                'success' => false,
                'message' => $file->getErrorString() . '(' . $file->getError() . ')'
            ];
            echo json_encode($response);
        }
    }

    function prestasi_delete()
    {
        $dir = './' . $this->request->getPost('dir');
        $response = [
            'success' => false,
            'msg' => 'File gagal dihapus'
        ];

        if (is_file($dir) && @unlink($dir)) {
            // delete success
            $response = [
                'success' => true,
                'msg' => 'File berhasil dihapus'
            ];
        } else if (is_file($dir)) {
            $response = [
                'success' => false,
                'msg' => 'File gagal dihapus'
            ];
        } else {
            $response = [
                'success' => false,
                'msg' => 'File tidak ada'
            ];
        }
        echo json_encode($response);
    }

    // penambahan setelah di pentest zainal 8 september 2022

    function wrongdata()
    {
        $response = [
            "success" => false,
            "title" => "Error",
            "text" => "Data Tidak Ditemukan"
        ];
        echo json_encode($response);
    }

    // end penambahan setelah di pentest zainal 8 september 2022


}
