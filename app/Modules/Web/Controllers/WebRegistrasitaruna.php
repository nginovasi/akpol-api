<?php

namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;

class WebRegistrasitaruna extends BaseController
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
        $query = "select a.id as id, concat(b.semester, ' - ' ,a.jabatan) as text from m_tingkatan_detail a left join m_semester b on a.id_semester=b.id where a.is_deleted = 0 AND a.id != 1 AND a.id_semester is not null ";
        $where = ["a.jabatan", "a.kode_jabatan"];

        $orderby = "group by a.id_semester";

        parent::_loadSelect2orderby($data, $query, $where, $orderby);
    }

    function batalyon_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT  * from ( SELECT a.id , CONCAT(a.batalyon,'/' , a.angkatan ,'/' , b.semester ,'/' , c.tingkatan, '/' , a.tahun_masuk ) AS text , b.ganjil_genap, a.is_deleted, a.tahun_masuk , a.id_semester, c.id as id_tingkat, a.batalyon FROM m_sm_batalyon a LEFT JOIN m_semester b ON a.id_semester=b.id LEFT JOIN m_tingkatan c ON b.id_tingkat=c.id WHERE a.is_deleted='0' AND b.id!='9' ) a1 where a1.is_deleted='0'";
        $where = ["a1.text"];

        parent::_loadSelect2($data, $query, $where);
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
        $query = "select a.*, a.kompi as text from m_sm_kompi a where a.is_deleted = 0 ";
        $where = ["a.kompi"];

        parent::_loadSelect2($data, $query, $where);
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

    function kelompok_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.* , a.kelompok as text from m_kelompok a where a.is_deleted = 0 ";
        $where = ["a.kelompok"];

        parent::_loadSelect2($data, $query, $where);
    }

    // action ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function datataruna_load()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $id_batalyon = $data['id_batalyon']==''? ' ' : ' and a.id_batalyon="'.$data['id_batalyon'].'" ';
        $id_kompi = $data['id_kompi']==''? ' ' : ' and a.id_kompi="'.$data['id_kompi'].'" ';
        $id_peleton = $data['id_peleton']==''? ' ' : ' and a.id_peleton="'.$data['id_peleton'].'" ';
        
        $query = "SELECT a.* , b.batalyon AS nama_batalyon , c.semester AS nama_semester FROM m_user_taruna a
                    LEFT JOIN `m_sm_batalyon` b ON a.`id_batalyon`=b.id
                    LEFT JOIN `m_semester` c ON b.`id_semester`=c.id where a.is_deleted = 0 $id_batalyon $id_kompi $id_peleton";
        $where = ["a.nik", "a.namataruna", "a.noaklong", "b.batalyon", "c.semester", "b.angkatan"];
        parent::_loadDatatable($query, $where, $data);
        // var_dump($this->db->getLastQuery());
    }

    function datataruna_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $photo_taruna = $this->request->getFile('photo_taruna');
        $dataarray = array();
        $dir_kerabat = array();

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
                }
                array_push($dataarray, $elm);

                $hasil = parent::_insertbatch('m_saudara_taruna', $dataarray, $userid, null, true);
            }
        }

        if ($data['id_m_user'] == '' and $data['id'] == '') {
            echo json_encode(array('success' => false, 'message' => 'Data User Tidak Ditemukan'));
        } else {
            $filter = array_filter($data, function ($k) {
                return (is_string($k));
            });

            parent::_insert('m_user_taruna', $filter, $userid);
        }
    }

    function datataruna_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.*, 
        if(a.id_gender=1, 'Laki - Laki' , 'Perempuan') as nama_gender, 
        c.batalyon as nama_batalyon,
        d.kompi as nama_kompi, e.peleton as nama_peleton, f.suku as nama_suku,
        g.agama as nama_agama, h.pendidikan as nama_tkpendid, i.nama as nama_kabkota_lhr,
        j.nama as nama_prov_ktp, k.nama as nama_kota_kab_ktp, l.nama as nama_kec_ktp, m.nama as nama_kel_ktp , n.kelompok as nama_kelompok,
        o.username
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
        where a.id = '" . $data['id'] . "'";

        $taruna = $this->db->query($query)->getRowArray();

        if ($taruna) {
            $taruna["data_saudara"] = $this->db->query('select a.*, 
            if(a.jenis_kelamin=1, "Laki - Laki" , "Perempuan") as nama_gender,
            if(a.status=1, "Hidup" , "Meninggal") as nama_status
            from m_saudara_taruna a
			where a.id_m_user = "' . $taruna['id_m_user'] . '" and a.is_deleted = "0"')->getResult();
        }

        // $result = array_map(function($arr){
        // 	$x = $arr;

        // 	return $x;
        // }, $list);

        // parent::_edit('m_user_taruna', $data, null, $query);
        echo json_encode(array('success' => true, 'data' => $taruna));
    }

    function datataruna_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_user_taruna', $data, $userid);
    }


    // begin laporan data taruna

    function datataruna_download()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT a.*, IFNULL(a.asal_pengiriman, '') AS asal_pengiriman , b.semester as nama_semester , c.kelompok as nama_kelompok , d.batalyon as nama_batalyon , e.kompi as nama_kompi , f.peleton as nama_peleton , g.nama as nama_lokasi_lahir , h.suku as nama_suku , i.agama as nama_agama , j.nama as nama_prov_ktp , k.nama as nama_kota_kab_ktp , l.nama as nama_kec_ktp , m.nama as nama_kel_ktp 
                FROM m_user_taruna a
                left join m_semester b
                    on a.id_semester=b.id
                left join m_kelompok c
                    on a.id_kelompok=c.id
                left join m_sm_batalyon d
                    on a.id_batalyon=d.id
                left join m_sm_kompi e
                    on a.id_kompi=e.id
                left join m_sm_peleton f
                    on a.id_peleton=f.id
                left join m_lokasi_lahir g
                    on a.id_kabkota_lhr=g.kdkabkota
                left join m_suku h
                    on a.id_suku=h.id
                left join m_agama i
                    on a.id_agama=i.id
                left join m_lokasi_nik_ind j
                    on a.id_prov_ktp=j.kode
                left join m_lokasi_nik_ind k
                    on a.id_kota_kab_ktp=k.kode
                left join m_lokasi_nik_ind l
                    on a.id_kec_ktp=l.kode
                left join m_lokasi_nik_ind m
                    on a.id_kel_ktp=m.kode
                WHERE a.is_deleted='0' 
                and a.is_verif='1'
                and a.id_batalyon='" . $data['id_batalyon'] . "' order by c.kelompok asc, a.namataruna asc";

        $rs['batalyon'] = $this->db->query("SELECT a.semester , concat(b.batalyon , ' - ' , b.tahun_masuk) as batalyon from m_semester a
                                            left join m_sm_batalyon b on a.id=b.id_semester 
                                            where b.id='" . $data['id_batalyon'] . "' ")->getRow()->batalyon;

        $rs['data'] = $this->db->query($query)->getResult();

        $rs['nama_file'] = $rs['batalyon'];


        echo json_encode($rs);
    }

    function laporandatataruna_load()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $query = "SELECT a.batalyon AS nama_batalyon, b.* , IFNULL(b.asal_pengiriman, '') AS asal_pengiriman , c.kelompok as nama_kelompok, e.peleton as nama_peleton, d.kompi as nama_kompi  
                        FROM m_sm_batalyon a
                        LEFT JOIN m_user_taruna b ON a.id=b.id_batalyon
                                     left join m_kelompok c on b.id_kelompok=c.id
                                     left join m_sm_kompi d on b.id_kompi=d.id
                                     left join m_sm_peleton e on b.id_peleton = e.id
                        WHERE b.is_deleted='0' and b.is_verif='1'
                        and a.id='" . $data['id_batalyon'] . "'  order by c.kelompok asc, b.namataruna asc";

        $rs = $this->db->query($query)->getResult();
        $total = count($rs);

        if ($total == 0) {
            echo json_encode(array('success' => false, 'message' => 'Data Tidak Ditemukan'));
        } else if ($total > 0) {
            echo json_encode(array('success' => true, 'jml_data' => $total, 'data' => $rs));
        } else {
            echo json_encode(array('success' => false, 'message' => $this->db->error()['message']));
        }
    }

    function laporandatataruna_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_gedung', $data, $userid);
    }

    function laporandatataruna_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $query = "SELECT * from m_gedung a
            where a.id = '" . $data['id'] . "'";
        parent::_edit('m_gedung', $data, null, $query);
    }

    function laporandatataruna_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_gedung', $data, $userid);
    }

    // end laporan data taruna
}
