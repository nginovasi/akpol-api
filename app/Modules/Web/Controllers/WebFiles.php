<?php

namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;

class WebFiles extends BaseController
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
    function matapelajaran_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, a.mata_pelajaran as text from m_mata_pelajaran a where a.is_deleted = 0";
        $where = ["a.mata_pelajaran"];

        parent::_loadSelect2($data, $query, $where);
    }

    function pertemuanke_select_get()
    {
        $data = $this->request->getGet();
        $id_mata_pelajaran = $data["id_mata_pelajaran"];
        $query = "SELECT a.pertemuan_ke as id,  a.judul, a.deskripsi, concat(a.pertemuan_ke,' - ',a.judul) as text from t_bahan_ajar a where a.is_deleted = 0 and a.is_ujian='0' and a.id_mata_pelajaran = '" . $id_mata_pelajaran . "' ";
        $where = ["a.judul", "a.pertemuan_ke"];

        parent::_loadSelect2($data, $query, $where);
    }

    function tingkat_select_get()
    {
        $data = $this->request->getGet();
        $query = "select a.id, a.tingkatan as text from m_tingkatan a where a.is_deleted = 0";
        $where = ["a.tingkatan"];

        parent::_loadSelect2($data, $query, $where);
    }

    function jabatan_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT * FROM (SELECT a.id as id, CONCAT(a.kode_jabatan,' - ', a.uts_uas) as text , a.is_deleted
                    from m_tingkatan_detail a
                    inner join m_semester b on a.id_semester=b.id
                    where a.is_deleted = 0 and b.id!='9'
                    group by a.id_semester) a where a.is_deleted='0' ";
        $where = ["a.text"];

        parent::_loadSelect2($data, $query, $where);
    }

    function filetype_get()
    {
        $data = $this->request->getGet();
        $tipe_file = $_POST['tipe_file'];
        $query = "select a.* from m_tipe_file a where a.tipe_file = '" . $tipe_file . "' ";
        $rs = $this->db->query($query)->getRow();

        return json_encode($rs);
    }

    function matapelajaranbybatalyon_select_get()
    {
        $data = $this->request->getGet();
        $id_batalyon = $data["id_batalyon"];
        $query = "SELECT
        a.id_mata_pelajaran as id,
        b.kode_mk,
        b.mata_pelajaran as text,
        d.semester
        from t_program_studi_mata_pelajaran a
        inner join m_mata_pelajaran b
            on b.id = a.id_mata_pelajaran
        inner join m_sm_batalyon c
            on a.id_semester = c.id_semester and a.id_batalyon=c.id
        inner join m_semester d
        on a.id_semester = d.id where a.id_batalyon = '" . $id_batalyon . "'
        and a.is_deleted='0' ";
        $where = ["b.mata_pelajaran"];
        $orderby = "";

        parent::_loadSelect2orderby($data, $query, $where, $orderby);
    }

    function batalyonsmt_select_get()
    {
        $data = $this->request->getGet();
        $query = "SELECT  * from ( SELECT a.id , concat(a.batalyon, ' ( ' , a.tahun_masuk , ' ) - ', b.semester) as text , a.is_deleted 
        from m_sm_batalyon a left join m_semester b on a.id_semester = b.id where a.is_deleted='0' and b.id < 9 ) a1 
        where a1.is_deleted='0' ";
        $where = ["a1.text"];

        parent::_loadSelect2($data, $query, $where);
    }

    // end ajax -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


    // controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function filemateri_upload()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $file = $this->request->getFile('file');
        $usertype = $data['usertype'];
        $username = $data['username'];
        $userdetail = $data['userdetail'];
        $originalName = $data['originalName'];
        $extension = $data['extension'];
        $size = $data['size'];

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $dir = '';
            if ($usertype == "sad") {
                $file->move('./public/file-materi/' . $usertype . '/' . $username, $newName);
                $dir = 'public/file-materi/' . $usertype . '/' . $username . '/' . $newName;
            } else {
                $file->move('./public/file-materi/' . $usertype . '/' . $userdetail['nik'], $newName);
                $dir = 'public/file-materi/' . $usertype . '/' . $userdetail['nik'] . '/' . $newName;
            }

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

    function file_delete()
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

    function filemateri_load()
    {
        $query = "SELECT a.*, b.mata_pelajaran, f.semester as jabatan, c.uts_uas, d.tipe_mime, e.batalyon, f.semester from t_file_materi a 
        left join m_mata_pelajaran b on a.id_mata_pelajaran = b.id
        left join m_tingkatan_detail c on a.id_tingkatan_detail = c.id
        left join m_tipe_file d on a.id_tipe_file = d.id
        left join m_sm_batalyon e on a.id_batalyon = e.id
        left join m_semester f on e.id_semester = f.id
            where a.is_deleted='0' ";
        $where = ["b.mata_pelajaran", "c.jabatan"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }


    function filemateri_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insertbatch('t_file_materi', $data, $userid);
    }

    function filemateri_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $dir = './' . $this->request->getPost('dir');;
        $response = '';
        if (is_file($dir) && @unlink($dir)) {
            // delete success
            parent::_delete('t_file_materi', $data, $userid);
        } else if (is_file($dir)) {
            $response = [
                'success' => false,
                'msg' => 'File gagal dihapus'
            ];
            echo json_encode($response);
        } else {
            $response = [
                'success' => false,
                'msg' => 'File tidak ada'
            ];
            echo json_encode($response);
        }
    }
    // end controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

}
