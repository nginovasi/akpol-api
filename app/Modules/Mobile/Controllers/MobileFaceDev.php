<?php namespace App\Modules\Mobile\Controllers;

use App\Modules\Mobile\Models\MobileModel;
use App\Core\BaseController;

class MobileFaceDev extends BaseController
{
    private $mobileModel;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->mobileModel = new MobileModel();
    }

    public function index()
    {
        return redirect()->to(base_url()); 
    }

    // controller -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function testapi(){
        
        $data = [
            'status' => 1,
            'message' => "Success",
            'methode' => $this->request->getMethod(),
            'ip'	=>  $this->request->getIPAddress(),
            'date'	=>  date('Y-m-d H:i:s')

        ];

        return $this->response->setJSON($data);
        ///return $this->response->setXML($data); xml response CI4
    }

    public function getTahunAjaran(){
		$kode = 'TA';
		$tahun = $this->db->query("SELECT nilai as TA FROM m_config where kode='".$kode."'")->getRow();

		$TA = $tahun->TA;
		
		return $TA;
	}

    public function authFace(){

        $noakpol = $_POST['account'];
        $jenis = $_POST['type'];
        $datetime = $_POST['date_time'];
        $id_device = $_POST['id_device'];
        $image = $_POST['url_image'];
        $ruang_kelas = $this->db->query("SELECT * from m_hikvision_device where id_device='".$id_device."'");
        $DataRuang = $ruang_kelas->getRow();

        if($jenis == 'trn'){
            if($DataRuang != NULL){
                $taruna = $this->db->query("SELECT id,username from m_user where username='".$noakpol."'")->getrow();
                $id_user = $taruna->id;
                    if($taruna != NULL){
                        $jadwal = $this->db->query("SELECT a.id as id_absensi,
                                    concat(b.tanggal,' ',b.jam_mulai) as schedule, 
                                    c.noaklong as nrp,
                                    c.namataruna as name
                                    from t_absensi a
                                    left join t_jadwal b
                                    on a.id_jadwal = b.id
                                    left join m_user_taruna c
                                    on a.id_taruna = c.id
                                    left join m_hikvision_device d
                                    on b.id_ruang_kelas = d.id_ruang_kelas
                                    where c.noaklong = '".$id_user."'
                                    and a.is_deleted = 0
                                    and d.id_device = '".$id_device."'
                                    and '".$datetime."' between concat(b.tanggal,' ',date_add(b.jam_mulai,interval -20 minute)) and concat(b.tanggal,' ',date_add(b.jam_mulai,interval 30 minute))");
                    $DataJadwal = $jadwal->getRow();
                    
                    if($DataJadwal != NULL){
                        $id_absensi = $DataJadwal->id_absensi;
                        $this->db->query("UPDATE t_absensi set face_is_absen='1',face_created_at=NOW(),face_device_id='".$id_device."',face_image='".$image."' where id='".$id_absensi."'");
                        $response = [
                            'status' => 1,
                            'message' => 'Success',
                            'data' => $DataJadwal
                            ];
                        return $this->response->setJSON($response);   
                    }else{
                        $response = [
                            'status' => 0,
                            'message' => 'No Schedule'
                            ];
                        return $this->response->setJSON($response); 
                    }
                }else{
                    $response = [
                        'status' => 0,
                        'message' => 'No Akpol Not Found'
                        ];
                    return $this->response->setJSON($response);
                }
                
            }else{
                $response = [
                    'status' => 0,
                    'message' => 'Device Not Registered'
                    ];
                return $this->response->setJSON($response);
            }
        }else{
            if($DataRuang != NULL){
                $taruna = $this->db->query("SELECT id,username from m_user where username='".$noakpol."'")->getrow();
                $id_user = $taruna->id;
                    if($taruna != NULL){
                        $jadwal = $this->db->query(" SELECT a.id as id_absensi,a.id_user_pendidik,
                                        c.nrp as nrp,
                                        c.namagadik as name,
                                        concat(a.tanggal,' ',a.jam_mulai) as schedule,
                                        concat(a.tanggal,' ',date_add(a.jam_mulai,interval -20 minute)) as start_absence, 
                                        concat(a.tanggal,' ',date_add(a.jam_mulai,interval 30 minute)) as end_absence
                                        from t_jadwal a
                                        left join m_user b on a.id_user_pendidik=b.id
                                        left join m_user_pendidik c on a.id_user_pendidik=c.id_m_user
                                        left join m_hikvision_device d on a.id_ruang_kelas = d.id_ruang_kelas
                                    where a.id_user_pendidik = '".$id_user."'
                                    and a.is_deleted = 0
                                    and d.id_device = '".$id_device."'
                                    and '".$datetime."' between concat(a.tanggal,' ',date_add(a.jam_mulai,interval -20 minute)) and concat(a.tanggal,' ',date_add(a.jam_mulai,interval 30 minute))");
                    $DataJadwal = $jadwal->getRow();
                    
                    if($DataJadwal != NULL){
                        $id_absensi = $DataJadwal->id_absensi;
                        $this->db->query("UPDATE t_jadwal set face_is_absen='1',face_created_at=NOW(),face_device_id='".$id_device."',face_image='".$image."' where id='".$id_absensi."'");
                        $response = [
                            'status' => 1,
                            'message' => 'Success',
                            'data' => $DataJadwal
                            ];
                        return $this->response->setJSON($response);   
                    }else{
                        $response = [
                            'status' => 0,
                            'message' => 'No Schedule'
                            ];
                        return $this->response->setJSON($response); 
                    }
                }else{
                    $response = [
                        'status' => 0,
                        'message' => 'No Akpol Not Found'
                        ];
                    return $this->response->setJSON($response);
                }
                
            }else{
                $response = [
                    'status' => 0,
                    'message' => 'Device Not Registered'
                    ];
                return $this->response->setJSON($response);
            }
        }
      
        
        


    }

    public function attendanceTaruna(){
        $noakpol = $_POST['nrp'];
        $datetime = $_POST['date_time'];
        $id_device = $_POST['id_device'];

        $taruna = $this->db->query("SELECT 
        concat(b.tanggal,' ',b.jam_mulai) as schedule, 
        c.noaklong as nrp,
        c.namataruna as name
        from t_absensi a
        left join t_jadwal b
        on a.id_jadwal = b.id
        left join m_user_taruna c
        on a.id_taruna = c.id
        left join m_hikvision_device d
        on b.id_ruang_kelas = d.id_ruang_kelas
        where c.noaklong = '".$noakpol."'
        and a.is_deleted = 0
        and d.id_device = '".$id_device."'
        and '".$datetime."' between concat(b.tanggal,' ',date_add(b.jam_mulai,interval -20 minute)) and concat(b.tanggal,' ',date_add(b.jam_mulai,interval 30 minute))");
        $DataTaruna = $taruna->getRow();

        if($DataTaruna != NULL){
            $response = [
                'status' => 1,
                'message' => 'Success',
                'data' => $DataTaruna
                ];
            return $this->response->setJSON($response); 	
            
        }else{
            $response = [
                'status' => 0,
                'message' => 'Failed'
                ];
            return $this->response->setJSON($response);
        }
        
    }


}