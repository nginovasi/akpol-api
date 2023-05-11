<?php namespace App\Modules\Mobile\Controllers;

use App\Modules\Mobile\Models\MobileModel;
use App\Core\BaseController;


class MobileApiTuwo extends BaseController
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

    public function testget(){
    	if ($this->request->getMethod() == 'get') {
	        $query = $this->mobileModel->getSemester();
	        
	        $jml = $query->getFieldCount();

	        if($jml > 0){

	            $data = [
	                'status' => 1,
	                'message' => "Success",
	                'data'  => $query->getResult()
	            ];

	            return $this->response->setJSON($data);

	        }else{

	            $data = [
	                'status' => 0,
	                'message' => "Failed"
	            ];

	            return $this->response->setJSON($data);

	        }
	    }else{
	     	 $data = [
	            'status' => 0,
	            'message' => "Method False"
       		];

        return $this->response->setJSON($data);
	     }

    }

	public function getTahunAjaran(){
		$kode = 'TA';
		$tahun = $this->db->query("SELECT nilai as TA FROM m_config where kode='".$kode."'")->getRow();

		$TA = $tahun->TA;
		
		return $TA;
	}

	public function getDateTimeID(){
		$query			= $this->db->query("SELECT sf_formatdate_ID(NOW()) as tanggal")->getRow();
		$dataTanggal 	= $query->tanggal;

		return $dataTanggal;
	}

    public function batalyon(){
    	if ($this->request->getMethod() == 'post') {

	        $id =  $this->request->getPost('id'); 

	        $query = $this->mobileModel->getBatalyonID($id);
	        
	        $jml = $query->getFieldCount();

	        if($jml > 0){

	            $data = [
	                'status' => 1,
	                'message' => "Success",
	                'data'  => $query->getRow()
	            ];

	            return $this->response->setJSON($data);

	        }else{

	            $data = [
	                'status' => 0,
	                'message' => "Failed"
	            ];

	            return $this->response->setJSON($data);

	        }

	     }else{
	     	 $data = [
	            'status' => 0,
	            'message' => "Method False"
       		];

        return $this->response->setJSON($data);
	     }

    }

    public function provinsi(){

		$query = $this->mobileModel->getProvinsi();
        
        $jml = $query->getFieldCount();
		if($jml > 0){
			$response = [
		            'status' => 1,
		            'message' => 'Success',
		            'data' => $query->getResult()
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

	public function kabkota(){
			$idprov =  $this->request->getPost('provinsi_id'); 

	        $query = $this->mobileModel->getKota($idprov);
	        
	        $jml = $query->getFieldCount();

			if($jml > 0){
				$response = [
			            'status' => 1,
			            'message' => 'Success',
			            'data' => $query->getResult()
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

	public function kecamatan(){
			$idkota =  $this->request->getPost('kota_id'); 

	        $query = $this->mobileModel->getKec($idkota);
	        
	        $jml = $query->getFieldCount();

			if($jml > 0){
				$response = [
			            'status' => 1,
			            'message' => 'Success',
			            'data' => $query->getResult()
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

	public function kelurahan(){
		$idkec =  $this->request->getPost('kec_id'); 

        $query = $this->mobileModel->getKel($idkec);
        
        $jml = $query->getFieldCount();

		if($jml > 0){
			$response = [
		            'status' => 1,
		            'message' => 'Success',
		            'data' => $query->getResult()
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

	//login standart
	public function authByGoogle(){
		$email = $_POST['email'];
		$isotp = 1;
		$query = $this->db->query("SELECT a.*,b.name as type_name from m_user a 
								  LEFT JOIN m_user_type b on a.type_code=b.code
								  where a.email='".$email."'");
		$resData = $query->getRow();

		if($resData != NULL){			
			$tipeuser = $resData->type_code;
			$tipename = $resData->type_name;
			$id_user = $resData->id;
			$is_active = $resData->is_deleted;
			if($is_active == 0){
				if($tipeuser == 'gdk'){
					$userData = $this->db->query("SELECT a.id as id_user,a.email,b.nik,b.namagadik,b.`photopath`,b.`nrp`,b.pangkat,b.jab,b.telp,b.`is_gadik`,b.`is_instruktur`,b.`is_internal`,b.`is_pengasuh`,b.`folder_materi` from m_user a
								LEFT JOIN m_user_pendidik b on a.id=b.`id_m_user`
								where a.id='".$id_user."'");
					
					$cekData = $userData->getFieldCount();
	
					if($cekData > 0){
						$response = [
									'status' => 1,
									'message' => 'Success',
									'data' => $userData->getRow(),
									'type_code' => $tipeuser,
									'type_name' => $tipename,
									'is_otp' => $isotp


									];
						return $this->response->setJSON($response);
					}else{
						$response = [
									'status' => 0,
									'message' => 'Data Detail User Tidak Ditemukan'
									];
						return $this->response->setJSON($response);
					}
				}else{
					$userData = $this->db->query("SELECT a.id as id_user,b.namataruna,a.email,b.telp,b.nik,b.noakshort,b.noaklong,b.photopath,c.jabatan,d.`batalyon`,e.`kompi`,f.`peleton` from m_user a
								LEFT JOIN m_user_taruna b on a.id=b.id_m_user
								LEFT JOIN m_tingkatan_detail c on b.id_tingkat_detail=c.id
								LEFT JOIN m_sm_batalyon d on b.id_batalyon=d.id
								LEFT JOIN m_sm_kompi e on b.id_kompi=e.id
								LEFT JOIN m_sm_peleton f on b.id_peleton=f.id
								where a.id='".$id_user."'");
					
					$cekData = $userData->getFieldCount();
	
					if($cekData > 0){
						$response = [
									'status' => 1,
									'message' => 'Success',
									'data' => $userData->getRow(),
									'type_code' => $tipeuser,
									'type_name' => $tipename,
									'is_otp' => $isotp
									];
						return $this->response->setJSON($response);
					}else{
						$response = [
									'status' => 0,
									'message' => 'Data Detail User Tidak Ditemukan'
									];
						return $this->response->setJSON($response);
					}
				}
			}else{
				$response = [
					'status' => 0,
					'message' => 'User Tidak Aktif'
					];
				return $this->response->setJSON($response);
			}
		}else{
			$response = [
						'status' => 0,
						'message' => 'Email Tidak Terdaftar'
						];
			return $this->response->setJSON($response);
		}

	}

	public function authUserPassword(){
		$username = $_POST['username'];
		$password = $_POST['password'];
		$isotp = 1;

		$query = $this->db->query("SELECT a.*,b.name as type_name from m_user a 
								  LEFT JOIN m_user_type b on a.type_code=b.code
								  where a.username='".$username."' and a.password=md5('".$password."') ");
		$resData = $query->getRow();

		if($resData != NULL){			
			$tipeuser = $resData->type_code;
			$tipename = $resData->type_name;
			$id_user = $resData->id;
			$is_active = $resData->is_deleted;
			if($is_active == 0){
				if($tipeuser == 'gdk'){
					$userData = $this->db->query("SELECT a.id as id_user,a.email,b.nik,b.namagadik,b.`photopath`,b.`nrp`,b.pangkat,b.jab,b.telp,b.`is_gadik`,b.`is_instruktur`,b.`is_internal`,b.`is_pengasuh`,b.`folder_materi` from m_user a
								LEFT JOIN m_user_pendidik b on a.id=b.`id_m_user`
								where a.id='".$id_user."'");
					
					$cekData = $userData->getFieldCount();
	
					if($cekData > 0){
						$response = [
									'status' => 1,
									'message' => 'Success',
									'data' => $userData->getRow(),
									'type_code' => $tipeuser,
									'type_name' => $tipename,
									'is_otp' => $isotp

									];
						return $this->response->setJSON($response);
					}else{
						$response = [
									'status' => 0,
									'message' => 'Data Detail User Tidak Ditemukan'
									];
						return $this->response->setJSON($response);
					}
				}else{
					$userData = $this->db->query("SELECT a.id as id_user,b.namataruna,a.email,b.telp,b.nik,b.noakshort,b.noaklong,b.photopath,c.jabatan,d.`batalyon`,e.`kompi`,f.`peleton` from m_user a
								LEFT JOIN m_user_taruna b on a.id=b.id_m_user
								LEFT JOIN m_tingkatan_detail c on b.id_tingkat_detail=c.id
								LEFT JOIN m_sm_batalyon d on b.id_batalyon=d.id
								LEFT JOIN m_sm_kompi e on b.id_kompi=e.id
								LEFT JOIN m_sm_peleton f on b.id_peleton=f.id
								where a.id='".$id_user."'");
					
					$cekData = $userData->getFieldCount();
	
					if($cekData > 0){
						$response = [
									'status' => 1,
									'message' => 'Success',
									'data' => $userData->getRow(),
									'type_code' => $tipeuser,
									'type_name' => $tipename,
									'is_otp' => $isotp
									];
						return $this->response->setJSON($response);
					}else{
						$response = [
									'status' => 0,
									'message' => 'Data Detail User Tidak Ditemukan'
									];
						return $this->response->setJSON($response);
					}
				}
			}else{
				$response = [
					'status' => 0,
					'message' => 'User Tidak Aktif'
					];
				return $this->response->setJSON($response);
			}
		}else{
			$response = [
						'status' => 0,
						'message' => 'Username/Password Salah'
						];
			return $this->response->setJSON($response);
		}

	}

    //LDAP
    public function test_login(){
        $adServer = "ldap://winsvr.nginovasi.id:389";

        $ldap = ldap_connect($adServer);
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        $bind = @ldap_bind($ldap, $username, $password);

        if ($bind) {
            $result = ldap_search($ldap,"dc=winsvr,dc=nginovasi,dc=id","(cn=$username)") or die ("Error in search query: ".ldap_error($ldap));
            $data = ldap_get_entries($ldap, $result);

            echo json_encode($this->utf8ize($this->cleanUpEntry($data)));

            @ldap_close($ldap);
        } else {
            $msg = ["username" => $username, "password" => $password, "error" => ldap_error($ldap)];

            echo json_encode($msg);
        }
    }

    function cleanUpEntry( $entry ) {
        $retEntry = array();
        for ( $i = 0; $i < $entry['count']; $i++ ) {
            if (is_array($entry[$i])) {
                $subtree = $entry[$i];
                //This condition should be superfluous so just take the recursive call
                //adapted to your situation in order to increase perf.
                if (!empty($subtree['dn']) && !isset($retEntry[$subtree['dn']])) {
                    $retEntry[$subtree['dn']] = $this->cleanUpEntry($subtree);
                }
                else {
                    $retEntry[] = $this->cleanUpEntry($subtree);
                }
            }
            else {
                $attribute = $entry[$i];
                if ( $entry[$attribute]['count'] == 1 ) {
                    $retEntry[$attribute] = $entry[$attribute][0];
                } else {
                    for ( $j = 0; $j < $entry[$attribute]['count']; $j++ ) {
                        $retEntry[$attribute][] = mb_convert_encoding($entry[$attribute][$j], 'UTF-8', 'UTF-8');
                    }
                }
            }
        }

        return $retEntry;
    }

	function utf8ize( $mixed ) {
        if(is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        }elseif(is_string($mixed)) {
            return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
        }

        return $mixed;
    }

	public function generateOTP(){
		
		$kode = mt_rand(1000,9999);

		$otp_user = 1; //default 1

		$idUser = $_POST['id_user'];
		// $tipeuser = $_POST['tipe_user'];

		$query = $this->db->query("SELECT * from m_user where id='".$idUser."'")->getRow();

		$tipeuser = $query->type_code;

			if($tipeuser == 'gdk'){

				$rsnew = $this->db->query("UPDATE m_user_pendidik set telp='" . $_POST['phone'] . "' where id_m_user='".$idUser."' ");
				$nomor = $this->db->query("SELECT telp,(NOW() + INTERVAL 5 MINUTE)as expired from m_user_pendidik where id_m_user='".$idUser."'")->getRow();

				if($otp_user == 1){

					$userkey = '408cdfc4202d';
					$passkey = '48846c1d6fd82bded53065dc';
					$telepon = 	'"'.$nomor->telp.'"';
					$message = 'Kode OTP untuk SIAKPOL System, masukan kode : *'.$kode.'* untuk melanjutkan proses verifikasi nomor anda,hanya berlaku 5 Menit. Terima Kasih';
					$url = 'https://console.zenziva.net/wareguler/api/sendWA/';
					$curlHandle = curl_init();
					curl_setopt($curlHandle, CURLOPT_URL, $url);
					curl_setopt($curlHandle, CURLOPT_HEADER, 0);
					curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
					curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
					curl_setopt($curlHandle, CURLOPT_POST, 1);
					curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
						'userkey' => $userkey,
						'passkey' => $passkey,
						'to' => $telepon,
						'message' => $message
					));
				
					$results = json_decode($this->utf8ize(curl_exec($curlHandle)), true);
					curl_close($curlHandle);

					$datalog = 	[
								'ip' =>  $this->request->getIPAddress(),
								'nomor'  => $nomor->telp,
								'resp'  => $kode,
								'log' => json_encode($this->utf8ize($results))
								];
					
					$this->db->table('log_otp')->insert($datalog);
					

					$rs = $this->db->query("UPDATE m_user a
											set a.uniq_code='".$kode."',a.expired_time='".$nomor->expired."' 
											where a.id='".$idUser."'");

					if($this->db->affectedRows() > 0){

						$response = [
									'status'  => 1,
									'kodeOTP' => $kode,
									'message' => 'Success',
									'result'  => $results,
									'is_otp'  => $otp_user	
									];
						return $this->response->setJSON($response);

					}else{

						$response = [
									'status' => 0,
									'message' => 'Failed'
									];
						return $this->response->setJSON($response);

					}
						
				}else{

					$response = [
								'status' => 1,
								'kodeOTP' => $kode,
								'message' => 'Success',
								'result'	=> NULL,
								'is_otp'  => $otp_user
								];

					return $this->response->setJSON($response);

				}

			}else if($tipeuser == 'trn'){
				$rsnew = $this->db->query("UPDATE m_user_taruna set telp='" . $_POST['phone'] . "' where id='".$idUser."'");
				$nomortaruna = $this->db->query("SELECT telp,(NOW() + INTERVAL 5 MINUTE)as expired from m_user_taruna where id_m_user='".$idUser."'")->getRow();

				if($otp_user == 1){

					$userkey = '408cdfc4202d';
					$passkey = '48846c1d6fd82bded53065dc';
					$telepon = 	'"'.$nomortaruna->telp.'"';
					$message = 'Kode OTP untuk SIAKPOL TARUNA, masukan kode : *'.$kode.'* untuk melanjutkan proses verifikasi nomor anda,hanya berlaku 5 Menit. Terima Kasih';
					$url = 'https://console.zenziva.net/wareguler/api/sendWA/';
					$curlHandle = curl_init();
					curl_setopt($curlHandle, CURLOPT_URL, $url);
					curl_setopt($curlHandle, CURLOPT_HEADER, 0);
					curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
					curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
					curl_setopt($curlHandle, CURLOPT_POST, 1);
					curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
						'userkey' => $userkey,
						'passkey' => $passkey,
						'to' => $telepon,
						'message' => $message
					));
				
					$results = json_decode($this->utf8ize(curl_exec($curlHandle)), true);
					curl_close($curlHandle);

					$datalog = 	[
								'ip' =>  $this->request->getIPAddress(),
								'nomor'  => $nomortaruna->telp,
								'resp'  => $kode,
								'log' => json_encode($this->utf8ize($results))
								];
					
					$this->db->table('log_otp')->insert($datalog);

					$rs = $this->db->query("UPDATE m_user a
											set a.uniq_code='".$kode."',a.expired_time='".$nomor->expired."' 
											where a.id='".$idUser."'");

					if($this->db->affectedRows() > 0){

						$response = [
									'status'  => 1,
									'kodeOTP' => $kode,
									'message' => 'Success',
									'result'  => $results,
									'is_otp'  => $otp_user	
									];
						return $this->response->setJSON($response);

					}else{

						$response = [
									'status' => 0,
									'message' => 'Failed'
									];
						return $this->response->setJSON($response);

					}
						
				}else{

					$response = [
								'status' => 1,
								'kodeOTP' => $kode,
								'message' => 'Success',
								'result'	=> NULL,
								'is_otp'  => $otp_user
								];

					return $this->response->setJSON($response);

				}
			}else{
				$response = [
					'status' => 0,
					'message' => 'Tipe User Tidak Ditemukan'
					];
				return $this->response->setJSON($response);
			}

			
	}

	public function authOTP(){
		
		$id_user = $_POST['id_user'];
		// $tipeuser = $_POST['tipe_user'];
		$otpnya = $_POST['otp'];

		$query = $this->db->query("SELECT * from m_user where id='".$id_user."'")->getRow();

		$tipeuser = $query->type_code;

		if($tipeuser == 'gdk'){
			$rs = $this->db->query("SELECT a.uniq_code as code,a.expired_time,b.id_m_user from m_user a
								LEFT JOIN m_user_pendidik b on b.id_m_user=a.id
								where a.id='".$id_user."' ")->getRow();
			if($otpnya == $rs->code){
				if($rs->expired_time >= date('Y-m-d H:i:s')){
					$this->db->query("UPDATE m_user set dt_login_mobile=NOW() where id='".$rs->id_m_user."' ");
					$response = [
								'status' => 1,
								'message' => 'Success'
								];
					return $this->response->setJSON($response); 	
				}else{
					$response = [
								'status' => 2,
								'message' => 'OTP expired'
								];
					return $this->response->setJSON($response);  
				}
			}else{
				$response = [
						'status' => 0,
						'message' => 'Invalid OTP'
						];
				return $this->response->setJSON($response);
			}
		}else if($tipeuser == 'trn'){
			$rs = $this->db->query("SELECT a.uniq_code as code,a.expired_time,b.id_m_user from m_user a
								LEFT JOIN m_user_taruna b on b.id_m_user=a.id
								where a.id='".$id_user."' ")->getRow();
			if($otpnya == $rs->code){
				if($rs->expired_time >= date('Y-m-d H:i:s')){
					$this->db->query("UPDATE m_user set dt_login_mobile=NOW() where id='".$rs->id_m_user."' ");
					$response = [
								'status' => 1,
								'message' => 'Success'
								];
					return $this->response->setJSON($response); 	
				}else{
					$response = [
								'status' => 2,
								'message' => 'OTP expired'
								];
					return $this->response->setJSON($response);  
				}
			}else{
				$response = [
						'status' => 0,
						'message' => 'Invalid OTP'
						];
				return $this->response->setJSON($response);
			}
		}else{
			$response = [
				'status' => 0,
				'message' => 'Tipe User Tidak Ditemukan'
				];
			return $this->response->setJSON($response);
		}
		
	}

	//Pengajar///

	public function scheduleLecturer(){

		$id_pengajar = $_POST['id_user'];
		$tahun_ajaran 	= $this->getTahunAjaran();
	

		$query = $this->db->query("SELECT a.id as id_jadwal,c.kelompok,g.`mata_pelajaran`,e.`judul`,e.`deskripsi`,e.pertemuan_ke,a.tanggal,a.is_absensi_pendidik,a.`jam_mulai`,a.`jam_selesai`,d.`nama` as lokasi_kelas,
									(case when (g.`is_teori` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas from `t_jadwal` a
									LEFT JOIN m_kelompok c on a.`id_kelompok_taruna`=c.id
									LEFT JOIN m_ruang_kelas d on a.`id_ruang_kelas`=d.`id`
									LEFT JOIN t_bahan_ajar e on a.id_bahan_ajar=e.id
									LEFT JOIN m_user_pendidik f on a.`id_user_pendidik`=f.id_m_user
									LEFT JOIN m_mata_pelajaran g on e.`id_mata_pelajaran`=g.id
								where f.id_m_user='".$id_pengajar."' and e.tahun_ajaran='".$tahun_ajaran."' and date(a.tanggal)=date(NOW()) group by g.mata_pelajaran"); //ditambahi where date= NOW()

		$respData = $query->getResult();
		
		if(is_array($respData) && count($respData)>0){
			$response = [
						'status' => 1,
						'message' => 'Success',
						'date'	=> $this->getDateTimeID(),
						'data' => $respData
						];
			return $this->response->setJSON($response);
		}else if(empty($respData)) {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
				];
			return $this->response->setJSON($response);
		}else{
			$response = [
						'status' => 0,
						'message' => 'Failed Backend Response'
						];
			return $this->response->setJSON($response);
		}
	}

	public function scheduleDetailLecturer(){

		$id_pengajar 	= $_POST['id_user'];
		$tahun_ajaran 	= $this->getTahunAjaran();
		$id_jadwal 		= $_POST['id_jadwal'];

		$query = $this->db->query("SELECT a.id as id_jadwal,c.kelompok,g.`mata_pelajaran`,e.`judul`,e.`deskripsi`,e.pertemuan_ke,a.tanggal,a.is_absensi_pendidik,a.`jam_mulai`,a.`jam_selesai`,d.`nama` as lokasi_kelas,
									(case when (g.`is_teori` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas,
									if(a.tanggal=curdate(),1,0) as aktif from `t_jadwal` a
									LEFT JOIN m_kelompok c on a.`id_kelompok_taruna`=c.id
									LEFT JOIN m_ruang_kelas d on a.`id_ruang_kelas`=d.`id`
									LEFT JOIN t_bahan_ajar e on a.id_bahan_ajar=e.id
									LEFT JOIN m_user_pendidik f on a.`id_user_pendidik`=f.id_m_user
									LEFT JOIN m_mata_pelajaran g on e.`id_mata_pelajaran`=g.id
									WHERE f.id_m_user='".$id_pengajar."' and a.id='".$id_jadwal."' and e.tahun_ajaran='".$tahun_ajaran."'");
		
		$jadwalData = $query->getRow();
		$is_absensi_pendidik = $jadwalData->is_absensi_pendidik;
		$is_absensi_aktif = $jadwalData->aktif;
		
		
		if($jadwalData != NULL){
			$materi = $this->db->query("SELECT a.id as id_file_materi,a.pertemuan_ke,CONCAT('http://devel.nginovasi.id/akpol-api/',a.lokasi_file) as lokasi_file,a.pertemuan_ke,a.tahun_ajaran,b.tipe_file,b.icon_file from t_file_materi a
			LEFT JOIN m_tipe_file b on a.id_tipe_file=b.id
			LEFT JOIN m_user_pendidik c on a.id_user_pendidik=c.id_m_user
			where c.id_m_user='".$id_pengajar."' and a.pertemuan_ke='".$jadwalData->pertemuan_ke."' and a.is_deleted=0 and a.tahun_ajaran='".$tahun_ajaran."'");

			$tugas = $this->db->query("SELECT a.id as id_tugas,a.id_jadwal,g.`mata_pelajaran`,a.judul,a.deskripsi,e.kelompok,a.waktu_pengumpulan,(case when (a.`tipe_tugas` = 1) then 'Online' else 'Kumpulkan di Kelas' end) as kategori_pengumpulan,CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas) as file_tugas,c.icon_file,c.tipe_file  from t_jadwal_tugas a
			LEFT JOIN t_jadwal b on a.`id_jadwal`=b.id
			LEFT JOIN m_tipe_file c on a.id_tipe_file=c.id
			LEFT JOIN m_kelompok e on b.`id_kelompok_taruna`=e.id
			LEFT JOIN t_bahan_ajar f on b.id_bahan_ajar=f.id
			LEFT JOIN m_mata_pelajaran g on f.`id_mata_pelajaran`=g.id
			LEFT JOIN m_user_pendidik h on b.id_user_pendidik=h.id_m_user
			where h.id_m_user='".$id_pengajar."' and f.pertemuan_ke='".$jadwalData->pertemuan_ke."' ");	

			$materiData = $materi->getResult();
			$tugasData  = $tugas->getRow();

			

			// $jml = $query->getFieldCount();
			if($materiData != NULL){
				if($tugasData != NULL){
					$arrData = [
						'is_absensi_pendidik' => $is_absensi_pendidik,
						'is_absensi_aktif' => $is_absensi_aktif,
						'file_pelajaran' => $materiData,
						'tugas_pelajaran' => $tugasData
						
					];
					$response = [
								'status' => 1,
								'message' => 'Success',
								'data' => $arrData
								];
					return $this->response->setJSON($response);
				}else{
					$arrData = [
						'is_absensi_pendidik' => $is_absensi_pendidik,
						'is_absensi_aktif' => $is_absensi_aktif,
						'file_pelajaran' => $materiData,
						'tugas_pelajaran' => NULL
						
					];
					$response = [
								'status' => 1,
								'message' => 'Success',
								'data' => $arrData
								];
					return $this->response->setJSON($response);
				}
			
			}else{
				$arrData = [
					'is_absensi_pendidik' => $is_absensi_pendidik,
					'is_absensi_aktif' => $is_absensi_aktif,
					'file_pelajaran' => NULL,
					'tugas_pelajaran' => NULL
				];
				$response = [
							'status' => 1,
							'message' => 'Success',
							'data' => $arrData
							];
				return $this->response->setJSON($response);
			}
			
		}else{
			$arrDataNull = [
				'is_absensi_pendidik' => NULL,
				'is_absensi_aktif' => NULL,
				'file_pelajaran' => NULL,
				'tugas_pelajaran' => NULL
			];
	
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data' => $arrDataNull
				];
			return $this->response->setJSON($response);
		}


	}

	public function absenceLecturer(){

		$id_pengajar 	= $_POST['id_user'];
		$id_jadwal 		= $_POST['id_jadwal'];
		$lat 			= $_POST['lat'];
		$long 			= $_POST['long'];
		$absensi 		= $_POST['absensi'];
		$ttd 			= $_POST['ttd'];
	

		$filename1 = $this->genNamefile();
		$file1 = '/home/ngi/php/akpol-api/assets/dataimage/'.$filename1.'_absensi.png';
		$file2 = '/home/ngi/php/akpol-api/assets/dataimage/'.$filename1.'_ttd.png';
		$data1 = base64_decode($absensi);
		$data2 = base64_decode($ttd);
		file_put_contents($file1, $data1);
		file_put_contents($file2, $data2);
		
		$fotoabsensi = 'http://devel.nginovasi.id/akpol-api/assets/dataimage/'.$filename1.'_absensi.png';
		$filettd = 'http://devel.nginovasi.id/akpol-api/assets/dataimage/'.$filename1.'_ttd.png';


		if($this->db->query("UPDATE t_jadwal set is_absensi_pendidik=1,lat_absen='".$lat."',long_absen='".$long."',foto_absen='".$fotoabsensi."',ttd_pendidik='".$filettd."' where id='".$id_jadwal."' ")){
			$response = [
				'status' => 1,
				'message' => 'Success'
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

	public function absenceTarunaClass(){

		$id_jadwal = $_POST['id_jadwal'];
		$query = $this->db->query("SELECT a.id as id_absensi,b.id_m_user as id_taruna,a.id_jadwal,a.is_absen,a.keterangan,a.face_is_absen,b.namataruna,b.noaklong,b.photopath from t_absensi a
		LEFT JOIN m_user_taruna b on a.id_taruna=b.id_m_user
		where a.id_jadwal='".$id_jadwal."' and a.is_deleted=0");

		$dataAbsen = $query->getResult();

		if($dataAbsen != NULL){
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $dataAbsen
				];
			return $this->response->setJSON($response);
		}else{
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
				];
			return $this->response->setJSON($response);
		}
	}

	

	public function uplodafoto(){
		$foto = $_POST['foto'];

		$filename1 = $this->genNamefile();
		$file1 = '/home/ngi/php/akpol-api/assets/dataimage/'.$filename1.'_foto.png';
		$data1 = base64_decode($foto);
		file_put_contents($file1, $data1);
		
		$filenya = 'http://devel.nginovasi.id/akpol-api/assets/dataimage/'.$filename1.'_foto.png';
		
		echo $filenya;

	}

	public function do_upload(){
        $periksa = $this->validate([
            'userfile' => [
                'uploaded[userfile]',
                'mime_in[userfile,application/pdf,application/doc,application/docx,application/xls,application/xlsx,application/zip,application/msword,application/x-tar]',
                'max_size[userfile,2048]',
            ],
        ]);
 
        if($periksa){
            $file = $this->request->getFile('userfile');
			$newName = $file->getRandomName();
			$file->move(ROOTPATH . 'public/uploads/berkas', $newName);
			echo $file->getClientExtension();
			echo 'Upload Berhasil';

        }else{

            echo 'Upload Gagal';

        }
    }

	public function saveFile(){
		// if (!$this->validate([
		// 	'keterangan' => [
		// 		'rules' => 'required',
		// 		'errors' => [
		// 			'required' => '{field} Tidak boleh kosong'
		// 		]
		// 	],
		// 	'berkas' => [
		// 		'rules' => 'uploaded[berkas]|mime_in[berkas,image/jpg,image/jpeg,image/gif,image/png]|max_size[berkas,2048]',
		// 		'errors' => [
		// 			'uploaded' => 'Harus Ada File yang diupload',
		// 			'mime_in' => 'File Extention Harus Berupa jpg,jpeg,gif,png',
		// 			'max_size' => 'Ukuran File Maksimal 2 MB'
		// 		]
 
		// 	]
		// ])) {
		// 	session()->setFlashdata('error', $this->validator->listErrors());
		// 	return redirect()->back()->withInput();
		// }
 
		// $berkas = new MobileModel();
		$dataBerkas = $this->request->getFile('berkas');
		$fileName = $dataBerkas->getRandomName();
		
		echo $dataBerkas->getClientExtension();

		echo base_url();

		// echo $dataBerkas;
		// echo $fileName;
		// $berkas->insert([
		// 	'berkas' => $fileName,
		// 	'keterangan' => $this->request->getPost('keterangan')
		// ]);
		// $filenya = $dataBerkas->move('uploads/berkas/', $fileName);
		$filenya = $dataBerkas->move(ROOTPATH . 'public/uploads/berkas');
	
		// session()->setFlashdata('success', 'Berkas Berhasil diupload');
		// return redirect()->to(base_url('berkas'));
		// echo base_url('berkas');
	}

	public function getinfodir(){
		helper('filesystem');
		$pathnya = './assets/img/_video.png';
		$file = new \CodeIgniter\Files\File($pathnya);
		$type = $file->getMimeType();
		$map = directory_map('./writable/uploads/', false, true);
		$size  = $file->getSize();
		$megabytes = $file->getSizeByUnit('kb'); 
		echo $pathnya;
		echo "\n";
		echo $size;
		echo "\n";
		echo $type;
		echo "\n";
		echo $megabytes.' Kb';
		echo "\n";

		print_r($map);
	
	}

	private function genNamefile()
	{

		$micro_date = microtime();
		$date_array = explode(" ", $micro_date);
		$date = date("YmdHis", $date_array[1]);
		$datetime = str_replace(".", "", $date . $date_array[0]);
		$namafix = md5(strtolower($datetime) . mt_rand(1000, 9999));

		return $namafix;
	}

	public function announcement(){

		$query = $this->db->query("SELECT a.id,a.judul,a.deskripsi,a.banner_path,b.name as created_by,a.created_at as create_at from m_pengumuman a 
								   LEFT JOIN m_user b on a.`created_by`=b.id order by id desc ");

		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
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

	public function announcementLimit(){

		$query = $this->db->query("SELECT a.id,a.judul,a.deskripsi,a.banner_path,b.name as created_by,a.created_at as create_at from m_pengumuman a 
								   LEFT JOIN m_user b on a.`created_by`=b.id order by id desc limit 2");

		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
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

	public function addAnnouncement(){

	}

	public function detailAnnouncement(){

	}

	public function listTaskOnGoing(){

		$id_pengajar = $_POST['id_user'];

		$query = $this->db->query("SELECT a.id as id_tugas,a.id_jadwal,g.mata_pelajaran,f.pertemuan_ke,a.judul,a.deskripsi,e.kelompok,a.waktu_pengumpulan,(case when (a.`tipe_tugas` = 1) then 'Online' else 'Kumpulkan di Kelas' end) as kategori_pengumpulan,CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas) as file_tugas,c.icon_file,c.tipe_file  from t_jadwal_tugas a
			LEFT JOIN t_jadwal b on a.`id_jadwal`=b.id
			LEFT JOIN m_tipe_file c on a.id_tipe_file=c.id
			LEFT JOIN t_kelompok_taruna d on b.`id_kelompok_taruna`=d.id
			LEFT JOIN m_kelompok e on d.`id_kelompok`=e.id
			LEFT JOIN t_bahan_ajar f on b.id_bahan_ajar=f.id
			LEFT JOIN m_mata_pelajaran g on f.id_mata_pelajaran=g.id
			LEFT JOIN m_user_pendidik h on f.id_user_pendidik=h.id_m_user
			where h.id_m_user='".$id_pengajar."' and now() <= a.waktu_pengumpulan ");

		$resData = $query->getResult();

		if($resData != NULL){
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
				];
			return $this->response->setJSON($response);
		}else{
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
				];
			return $this->response->setJSON($response);
		}	
	}

	public function listTaskOnDone(){
		$id_pengajar = $_POST['id_user'];
		
		$query = $this->db->query("SELECT a.id as id_tugas,a.id_jadwal,g.mata_pelajaran,a.judul,a.deskripsi,e.kelompok,a.waktu_pengumpulan,(case when (a.`tipe_tugas` = 1) then 'Online' else 'Kumpulkan di Kelas' end) as kategori_pengumpulan,CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas) as file_tugas,c.icon_file,c.tipe_file  from t_jadwal_tugas a
			LEFT JOIN t_jadwal b on a.`id_jadwal`=b.id
			LEFT JOIN m_tipe_file c on a.id_tipe_file=c.id
			LEFT JOIN t_kelompok_taruna d on b.`id_kelompok_taruna`=d.id
			LEFT JOIN m_kelompok e on d.`id_kelompok`=e.id
			LEFT JOIN t_bahan_ajar f on b.id_bahan_ajar=f.id
			LEFT JOIN m_mata_pelajaran g on f.id_mata_pelajaran=g.id
			LEFT JOIN m_user_pendidik h on f.id_user_pendidik=h.id_m_user
			where h.id_m_user='".$id_pengajar."' and now() >= a.waktu_pengumpulan ");
		
		$resData = $query->getResult();

		if($resData != NULL){
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
				];
			return $this->response->setJSON($response);
		}else{
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
				];
			return $this->response->setJSON($response);
		}	
	}

	// AWAL INFO
	public function listInfoPendidik(){
		$id_pengajar = $_POST['id_user'];
		
		$query = $this->db->query("SELECT 
			a.*, 
			b.kelompok AS nama_kelompok, 
			c.mata_pelajaran AS nama_mata_pelajaran
		FROM t_informasi_pendidik a
		LEFT JOIN m_kelompok b
			ON a.id_kelompok = b.id
		LEFT JOIN m_mata_pelajaran c
			ON a.id_mata_pelajaran = c.id
		WHERE a.created_by = '".$id_pengajar."'
			AND a.is_deleted = 0
		ORDER BY a.created_at desc");
		
		$resData = $query->getResult();

		if($resData != NULL){
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
				];
			return $this->response->setJSON($response);
		}else{
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
				];
			return $this->response->setJSON($response);
		}	
	}

	public function listMapelKelas(){
		$id_pengajar = $_POST['id_user'];
		
		$query = $this->db->query("SELECT 
				d.id AS id_kelompok,
				d.kelompok,
				e.id AS id_mata_pelajaran,
				e.mata_pelajaran
			FROM t_jadwal a
			LEFT JOIN t_bahan_ajar b
				ON a.id_bahan_ajar = b.id
			LEFT JOIN t_pendidik_mata_pelajaran c
				ON b.id_mata_pelajaran = c.id_mata_pelajaran
			LEFT JOIN m_kelompok d
				ON a.id_kelompok_taruna = d.id
			LEFT JOIN m_mata_pelajaran e
				ON c.id_mata_pelajaran = e.id
			LEFT JOIN m_config f
				ON f.id = 1
				AND b.tahun_ajaran = f.nilai
			WHERE c.id_pendidik = '".$id_pengajar."'
			GROUP BY d.id,e.id");
		
		$resData = $query->getResult();

		if($resData != NULL){
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
				];
			return $this->response->setJSON($response);
		}else{
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
				];
			return $this->response->setJSON($response);
		}	
	}

	public function saveInfoPendidik()
	{
		header('Content-Type: application/json');

		if(isset($_POST)
		// &&isset($_POST['id_kelompok'])&&isset($_POST['id_mata_pelajaran'])&&isset($_POST['judul'])&&isset($_POST['deskripsi'])&&
		// 	$_POST['id_user']!='',$_POST['id_kelompok']!=''&&$_POST['id_mata_pelajaran']!=''&&$_POST['judul']!=''&&$_POST['deskripsi']!=''
			){
			$id_user 			= $_POST['id_user'];
			$id_kelompok 		= $_POST['id_kelompok'];
			$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
			$judul 				= $_POST['judul'];
			$deskripsi 			= $_POST['deskripsi'];
			$id_info 			= $_POST['id_info'];

			$insert['created_by'] = $id_user;
			$insert['id_kelompok'] = $id_kelompok;
			$insert['id_mata_pelajaran'] = $id_mata_pelajaran;
			$insert['judul'] = $judul;
			$insert['deskripsi'] = $deskripsi;
			
			$this->db->db_debug = false;
			if($id_info==''||$id_info=='0'){
				if($this->db->table('t_informasi_pendidik')->insert($insert)){
					$response = [
						'status' => 1,
						'message' => 'Success'
						];
					return $this->response->setJSON($response);
				}else{
					$response = [
						'status' => 0,
						'message' => $this->errorhandler()
					];
					return $this->response->setJSON($response);
				}
			}else{
				$builder = $this->db->table('t_informasi_pendidik');
				$builder->where('id', $id_info);
				$execute = $builder->update($insert);
				if($execute){
					$response = [
						'status' => 1,
						'message' => 'Success'
						];
					return $this->response->setJSON($response);
				}else{
					$response = [
						'status' => 0,
						'message' => $this->errorhandler()
					];
					return $this->response->setJSON($response);
				}
			}
			$this->db->db_debug = true;
		}else{
			$response = [
				'status' => 0,
				'message' => 'Please complete the input form'
				];
			return $this->response->setJSON($response);
		}
	}
	// AKHIR INFO

	// AWAL INFO
	public function listPelanggaranProses(){
		$id_pengajar = $_POST['id_user'];

		$query = $this->db->query("SELECT 
				c.id as id_taruna,
				c.namataruna as nama_taruna,
				c.noaklong as no_ak,
				d.id as id_pelanggaran,
				d.dasar_hukum,
				d.deskripsi,
				e.id as id_kategori_pelanggaran,
				e.kategori as kategori_pelanggaran,
				a.poin,
				e.min_poin,
				e.max_poin,
				f.id as id_karakter_pelanggaran,
				f.karakter as karakter_pelanggaran,
				json_arrayagg(json_object(
								'id_bukti', b.id,
								'bukti_foto', b.foto)) as bukti,
				a.latitude,
				a.longitude,
				a.is_approve,
				a.approve_at,
				a.created_at
			FROM t_pelanggaran_karakter_taruna a
			LEFT JOIN t_bukti_pelanggaran_karakter_taruna b
				on a.id = b.id_pelanggaran_karakter
			LEFT JOIN m_user_taruna c
				on a.id_taruna = c.id
			LEFT JOIN m_pelanggaran_karakter d
				on d.id = a.id_pelanggaran_karakter
			LEFT JOIN m_kategori_pelanggaran_karakter e
				on e.id = d.id_kategori_pelanggaran
			LEFT JOIN m_karakter_penilaian f
				on d.id_karakter_penilaian = f.id
			LEFT JOIN m_config g
				ON g.id = 1
				AND a.tahun_ajaran = g.nilai
			LEFT JOIN m_tingkatan_detail h
				ON c.id_tingkat = h.id
			LEFT JOIN m_semester i
				ON h.id_semester = i.id
			LEFT JOIN m_semester j
				ON a.id_semester = j.id
			LEFT JOIN m_sm_peleton k
				ON c.id_peleton = k.id
			WHERE a.id_pelapor = '".$id_pengajar."'
				AND i.ganjil_genap = j.ganjil_genap
				AND a.is_deleted = 0
				AND a.is_approve = 0
			GROUP BY a.id
			UNION
			SELECT 
				c.id as id_taruna,
				c.namataruna as nama_taruna,
				c.noaklong as no_ak,
				d.id as id_pelanggaran,
				d.dasar_hukum,
				d.deskripsi,
				e.id as id_kategori_pelanggaran,
				e.kategori as kategori_pelanggaran,
				a.poin,
				e.min_poin,
				e.max_poin,
				f.id as id_karakter_pelanggaran,
				f.karakter as karakter_pelanggaran,
				json_arrayagg(json_object(
								'id_bukti', b.id,
								'bukti_foto', b.foto)) as bukti,
				a.latitude,
				a.longitude,
				a.is_approve,
				a.approve_at,
				a.created_at
			FROM t_pelanggaran_karakter_taruna a
			LEFT JOIN t_bukti_pelanggaran_karakter_taruna b
				on a.id = b.id_pelanggaran_karakter
			LEFT JOIN m_user_taruna c
				on a.id_taruna = c.id
			LEFT JOIN m_pelanggaran_karakter d
				on d.id = a.id_pelanggaran_karakter
			LEFT JOIN m_kategori_pelanggaran_karakter e
				on e.id = d.id_kategori_pelanggaran
			LEFT JOIN m_karakter_penilaian f
				on d.id_karakter_penilaian = f.id
			LEFT JOIN m_config g
				ON g.id = 1
				AND a.tahun_ajaran = g.nilai
			LEFT JOIN m_tingkatan_detail h
				ON c.id_tingkat = h.id
			LEFT JOIN m_semester i
				ON h.id_semester = i.id
			LEFT JOIN m_semester j
				ON a.id_semester = j.id
			LEFT JOIN m_sm_peleton k
				ON c.id_peleton = k.id
			WHERE k.id_user_pendidik = '".$id_pengajar."'
				AND i.ganjil_genap = j.ganjil_genap
				AND a.is_deleted = 0
				AND a.is_approve = 0
			GROUP BY a.id");
		
		$resData = $query->getResult();
		foreach ($resData as $obj) {
			$bukti = json_decode($obj->bukti);
			if($bukti[0]->id_bukti == null){
				$obj->bukti = null;
			} else {
				$obj->bukti = $bukti;
			}
		}

		if($resData != NULL){
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
				];
			return $this->response->setJSON($response);
		}else{
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
				];
			return $this->response->setJSON($response);
		}	
	}

	public function listPelanggaranKonfirm(){
		$id_pengajar = $_POST['id_user'];

		$query = $this->db->query("SELECT 
				c.id as id_taruna,
				c.namataruna as nama_taruna,
				c.noaklong as no_ak,
				d.id as id_pelanggaran,
				d.dasar_hukum,
				d.deskripsi,
				e.id as id_kategori_pelanggaran,
				e.kategori as kategori_pelanggaran,
				a.poin,
				e.min_poin,
				e.max_poin,
				f.id as id_karakter_pelanggaran,
				f.karakter as karakter_pelanggaran,
				json_arrayagg(json_object(
								'id_bukti', b.id,
								'bukti_foto', b.foto)) as bukti,
				a.latitude,
				a.longitude,
				a.is_approve,
				a.approve_at,
				a.created_at
			FROM t_pelanggaran_karakter_taruna a
			LEFT JOIN t_bukti_pelanggaran_karakter_taruna b
				on a.id = b.id_pelanggaran_karakter
			LEFT JOIN m_user_taruna c
				on a.id_taruna = c.id
			LEFT JOIN m_pelanggaran_karakter d
				on d.id = a.id_pelanggaran_karakter
			LEFT JOIN m_kategori_pelanggaran_karakter e
				on e.id = d.id_kategori_pelanggaran
			LEFT JOIN m_karakter_penilaian f
				on d.id_karakter_penilaian = f.id
			LEFT JOIN m_config g
				ON g.id = 1
				AND a.tahun_ajaran = g.nilai
			LEFT JOIN m_tingkatan_detail h
				ON c.id_tingkat = h.id
			LEFT JOIN m_semester i
				ON h.id_semester = i.id
			LEFT JOIN m_semester j
				ON a.id_semester = j.id
			LEFT JOIN m_sm_peleton k
				ON c.id_peleton = k.id
			WHERE a.id_pelapor = '".$id_pengajar."'
				AND i.ganjil_genap = j.ganjil_genap
				AND a.is_deleted = 0
				AND a.is_approve = 1
			GROUP BY a.id
			UNION
			SELECT 
				c.id as id_taruna,
				c.namataruna as nama_taruna,
				c.noaklong as no_ak,
				d.id as id_pelanggaran,
				d.dasar_hukum,
				d.deskripsi,
				e.id as id_kategori_pelanggaran,
				e.kategori as kategori_pelanggaran,
				a.poin,
				e.min_poin,
				e.max_poin,
				f.id as id_karakter_pelanggaran,
				f.karakter as karakter_pelanggaran,
				json_arrayagg(json_object(
								'id_bukti', b.id,
								'bukti_foto', b.foto)) as bukti,
				a.latitude,
				a.longitude,
				a.is_approve,
				a.approve_at,
				a.created_at
			FROM t_pelanggaran_karakter_taruna a
			LEFT JOIN t_bukti_pelanggaran_karakter_taruna b
				on a.id = b.id_pelanggaran_karakter
			LEFT JOIN m_user_taruna c
				on a.id_taruna = c.id
			LEFT JOIN m_pelanggaran_karakter d
				on d.id = a.id_pelanggaran_karakter
			LEFT JOIN m_kategori_pelanggaran_karakter e
				on e.id = d.id_kategori_pelanggaran
			LEFT JOIN m_karakter_penilaian f
				on d.id_karakter_penilaian = f.id
			LEFT JOIN m_config g
				ON g.id = 1
				AND a.tahun_ajaran = g.nilai
			LEFT JOIN m_tingkatan_detail h
				ON c.id_tingkat = h.id
			LEFT JOIN m_semester i
				ON h.id_semester = i.id
			LEFT JOIN m_semester j
				ON a.id_semester = j.id
			LEFT JOIN m_sm_peleton k
				ON c.id_peleton = k.id
			WHERE k.id_user_pendidik = '".$id_pengajar."'
				AND i.ganjil_genap = j.ganjil_genap
				AND a.is_deleted = 0
				AND a.is_approve = 1
			GROUP BY a.id");
		
		$resData = $query->getResult();
		foreach ($resData as $obj) {
			$bukti = json_decode($obj->bukti);
			if($bukti[0]->id_bukti == null){
				$obj->bukti = null;
			} else {
				$obj->bukti = $bukti;
			}
		}

		if($resData != NULL){
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
				];
			return $this->response->setJSON($response);
		}else{
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
				];
			return $this->response->setJSON($response);
		}	
	}

	public function listMasterPelanggaran(){
		$query = $this->db->query("SELECT 
				a.id as id_pelanggaran,
				a.dasar_hukum,
				a.deskripsi,
				b.id as id_kategori_pelanggaran,
				b.kategori as kategori_pelanggaran,
				b.min_poin,
				b.max_poin,
				c.id as id_karakter_pelanggaran,
				c.karakter as karakter_pelanggaran
			FROM m_pelanggaran_karakter a
			LEFT JOIN m_kategori_pelanggaran_karakter b
				on b.id = a.id_kategori_pelanggaran
			LEFT JOIN m_karakter_penilaian c
				on a.id_karakter_penilaian = c.id
			WHERE a.is_deleted = 0");
		
		$resData = $query->getResult();

		if($resData != NULL){
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
				];
			return $this->response->setJSON($response);
		}else{
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
				];
			return $this->response->setJSON($response);
		}	
	}

	public function listMasterTaruna(){
		$query = $this->db->query("SELECT
				a.id,
				a.noaklong as no_ak,
				a.namataruna as nama,
				b.jabatan
			FROM m_user_taruna a
			LEFT JOIN m_tingkatan_detail b
				ON a.id_tingkat = b.id
			WHERE a.is_deleted = 0
				AND a.is_verif = 1
				AND a.id_tingkat>1 AND a.id_tingkat<17");
		
		$resData = $query->getResult();

		if($resData != NULL){
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
				];
			return $this->response->setJSON($response);
		}else{
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
				];
			return $this->response->setJSON($response);
		}	
	}
	// AKHIR PELANGGARAN

	public function detailTask(){
		$id_taks = $_POST['id_tugas'];
		
		$query = $this->db->query("SELECT a.id as id_nilai_tugas,a.id_jadwal_tugas,b.namataruna,b.noaklong,b.photopath,(case when (a.file_tugas is null) then '0' else '1' end) as is_mengumpulkan,(case when (a.`file_tugas` is null) then 'Belum Mengumpulkan Tugas' else CONCAT('Mengumpulkan ',sf_formatdate_ID(a.upload_date),' • ', DATE_FORMAT(a.upload_date,'%H:%i'), ' WIB' ) end) as tanggal_upload,a.nilai from t_nilai_tugas a
		left join m_user_taruna b on a.id_user_taruna=b.id_m_user
		where a.id_jadwal_tugas='".$id_taks."'");

		$dataTugas = $query->getResult();
		if($dataTugas != NULL){
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $dataTugas
				];
			return $this->response->setJSON($response);
		}else{
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
				];
			return $this->response->setJSON($response);
		}
		
		
	}
	

	public function getClassNow(){

	}

	public function getCourseNow(){

	}

	public function getMeetClass(){

	}

	public function getIdSchedule(){

	}

	public function getTaskLecturer(){
		$id_pengajar 	= $_POST['id_user'];
		$query			= $this->db->query("SELECT 
											a.id as id_jadwal,
											c.kelompok,
											e.mata_pelajaran,
											d.pertemuan_ke,
											if(b.id_jadwal is null,0,1) as sudah_ada_tugas,
											(case when (b.id_jadwal is null) then 'Belum ada tugas' else 'Sudah ada tugas' end) as ket_status_tugas,
											a.id_user_pendidik
											from t_jadwal a
											left join t_jadwal_tugas b
												on a.id = b.id_jadwal
											left join m_kelompok c
												on c.id = a.id_kelompok_taruna
											left join t_bahan_ajar d
												on a.id_bahan_ajar = d.id
											left join m_mata_pelajaran e
												on d.id_mata_pelajaran = e.id
											left join m_user_pendidik f on a.id_user_pendidik = f.id_m_user
											where 
											a.is_deleted = 0 and b.id_jadwal is null
											and f.id_m_user = '".$id_pengajar."' group by a.id order by d.id_mata_pelajaran;");
		
		$resData = $query->getResult();

		if($resData != NULL){
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
				];
			return $this->response->setJSON($response);
		}else{
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	=> NULL
				];
			return $this->response->setJSON($response);
		}	
		
	}

	public function addTaskLecturer(){
		$id_jadwal 			= $_POST['id_jadwal'];
		$judul 				= $_POST['judul'];
		$deskripsi 			= $_POST['deskripsi'];
		$waktu_pengumpulan 	= $_POST['waktu_pengumpulan'];
		$file_materi_tugas 	= $this->request->getFile('file_materi_tugas');
		$tipe_tugas 		= $_POST['tipe_tugas'];
		$id_pengajar 		= $_POST['id_user'];
		
		$infodir = $this->db->query("SELECT folder_materi FROM m_user_pendidik where id_m_user='".$id_pengajar."' ")->getRow();	
		
		$newName = $file_materi_tugas->getRandomName();
		
		$extensi = $file_materi_tugas->getClientExtension();
		
		$getExtensi = $this->db->query("SELECT id from m_tipe_file where tipe_file='".$extensi."'")->getRow();

		$idExtensi = $getExtensi->id;

		if($idExtensi != NULL){
			$file_materi_tugas->move(ROOTPATH . 'public/file-materi/'.$infodir->folder_materi, $newName);

			$pathnya = base_url().'/public/file-materi/'.$infodir->folder_materi.'/'.$newName;

			$dataJadwal = [
				'id_jadwal' 		=> $id_jadwal,
				'judul' 			=> $judul,
				'deskripsi'			=> $deskripsi,
				'waktu_pengumpulan' => $waktu_pengumpulan,
				'file_materi_tugas' => $pathnya,
				'tipe_tugas'		=> $tipe_tugas,
				'id_tipe_file'		=> $idExtensi

			];

			if($this->db->table('t_jadwal_tugas')->insert($dataJadwal)){
				$response = [
					'status' => 1,
					'message' => 'Success'
					];
				return $this->response->setJSON($response);
			}else{
				$response = [
					'status' => 0,
					'message' => 'Failed'
					];
				return $this->response->setJSON($response);
			}
		}else{
			$response = [
				'status' => 0,
				'message' => 'Extensi File Tidak bisa diUpload'
				];
			return $this->response->setJSON($response);
		}
		// echo $idExtensi;

		

	}

	function errorhandler(){
		$error = $this->db->error();
		return $error['message'];
	}

	function duplicatehandler(){
		$msg = '-';
		$error = $this->db->error();
		if($error['code'] == 500){
			$msg = 'Duplicate';
		}

		return $msg;
	}

	public function saveTaskLecturer()
	{
		header('Content-Type: application/json');

		$id_tugas 			= $_POST['id_tugas'];
		$is_removed 		= $_POST['is_removed'];
		$datatugas 			= $_POST['data_tugas'];
		$file_materi_tugas 	= $this->request->getFile('file_materi_tugas');
		$id_pengajar 		= $_POST['id_user'];

		$result 			= json_decode($datatugas);
		// print_r($result);

		if ($id_tugas == 0 or $id_tugas == NULL) {
			foreach ($result as $key => $value) {
				$data[$key] = $value;
			}
			$id_jadwal 			= $data['id_jadwal'];
			$judul				= $data['judul'];
			$deskripsi			= $data['deskripsi'];
			$waktu_pengumpulan	= $data['waktu_pengumpulan'];
			$tipe_tugas			= $data['tipe_tugas'];
			
			
			if($file_materi_tugas != "" or strlen($file_materi_tugas) > 10){
				
				$infodir = $this->db->query("SELECT folder_materi FROM m_user_pendidik where id_m_user='".$id_pengajar."' ")->getRow();	
				
				$newName = $file_materi_tugas->getRandomName();
				
				$extensi = $file_materi_tugas->getClientExtension();
				
				$getExtensi = $this->db->query("SELECT id from m_tipe_file where tipe_file='".$extensi."'")->getRow();

				$idExtensi = $getExtensi->id;

				if($idExtensi != NULL){
					$file_materi_tugas->move(ROOTPATH . 'public/file-materi/'.$infodir->folder_materi, $newName);

					$pathnya = 'public/file-materi/'.$infodir->folder_materi.'/'.$newName;

					$dataJadwal = [
						'id_jadwal' 		=> $id_jadwal,
						'judul' 			=> $judul,
						'deskripsi'			=> $deskripsi,
						'waktu_pengumpulan' => $waktu_pengumpulan,
						'file_materi_tugas' => $pathnya,
						'tipe_tugas'		=> $tipe_tugas,
						'id_tipe_file'		=> $idExtensi

					];
					$this->db->db_debug = false;
					if($this->db->table('t_jadwal_tugas')->insert($dataJadwal)){
						$response = [
							'status' => 1,
							'message' => 'Success'
							];
						return $this->response->setJSON($response);
					}else{
						$response = [
							'status' => 0,
							'message' => $this->errorhandler()
						];
						return $this->response->setJSON($response);
					}
					$this->db->db_debug = true;
				}else{
					$response = [
						'status' => 0,
						'message' => 'Extensi File Tidak bisa diUpload'
						];
					return $this->response->setJSON($response);
				}

			}else{

				$dataJadwal = [
					'id_jadwal' 		=> $id_jadwal,
					'judul' 			=> $judul,
					'deskripsi'			=> $deskripsi,
					'waktu_pengumpulan' => $waktu_pengumpulan,
					'file_materi_tugas' => NULL,
					'tipe_tugas'		=> $tipe_tugas,
					'id_tipe_file'		=> NULL

				];
				
				// $this->db->db_debug = false;
				if($this->db->table('t_jadwal_tugas')->insert($dataJadwal)){
					
					$response = [
						'status' => 1,
						'message' => 'Success'
						];
					return $this->response->setJSON($response);
				}else{
					
					$response = [
						'status' => 0,
						'message' => $this->errorhandler()
					];
					return $this->response->setJSON($response);
				}
				
				
				// $this->db->db_debug = true;
			}
			
		} else {
			//edit
			$datafile = $this->db->query("SELECT id,id_jadwal,file_materi_tugas from t_jadwal_tugas where id='" .$id_tugas. "'")->getRow();
			
			foreach ($result as $key => $value) {
				$data[$key] = $value;

			}
			$id_jadwal 			= $data['id_jadwal'];
			$judul				= $data['judul'];
			$deskripsi			= $data['deskripsi'];
			$waktu_pengumpulan	= $data['waktu_pengumpulan'];
			$tipe_tugas			= $data['tipe_tugas'];
			
			
			// echo $url . $datafile->file_materi_tugas;
			
		
			if ($file_materi_tugas !="" or strlen($file_materi_tugas) > 10) {
				
				$url = "/home/ngi/php/akpol-api/";
					
				if($datafile->file_materi_tugas != NULL){
					if (file_exists($url . $datafile->file_materi_tugas)) {
						unlink($datafile->file_materi_tugas);
					}
				}
				$infodir = $this->db->query("SELECT folder_materi FROM m_user_pendidik where id_m_user='".$id_pengajar."' ")->getRow();	
				
				$newName = $file_materi_tugas->getRandomName();
				
				$extensi = $file_materi_tugas->getClientExtension();
				
				$getExtensi = $this->db->query("SELECT id from m_tipe_file where tipe_file='".$extensi."'")->getRow();

				$idExtensi = $getExtensi->id;

				if($idExtensi != NULL){
					$file_materi_tugas->move(ROOTPATH . 'public/file-materi/'.$infodir->folder_materi, $newName);

					$pathnya = 'public/file-materi/'.$infodir->folder_materi.'/'.$newName;

					$dataJadwal = [
						'id_jadwal' 		=> $id_jadwal,
						'judul' 			=> $judul,
						'deskripsi'			=> $deskripsi,
						'waktu_pengumpulan' => $waktu_pengumpulan,
						'file_materi_tugas' => $pathnya,
						'tipe_tugas'		=> $tipe_tugas,
						'id_tipe_file'		=> $idExtensi

					];
					$this->db->db_debug = false;
					$builder = $this->db->table('t_jadwal_tugas');
					$builder->where('id', $id_tugas);
					$execute = $builder->update($dataJadwal);
					if($execute){
					
						$response = [
							'status' => 1,
							'message' => 'Success'
							];
						return $this->response->setJSON($response);
					}else{
						$response = [
							'status' => 0,
							'message' => $this->errorhandler()
						];
						return $this->response->setJSON($response);
					}
					$this->db->db_debug = true;
				}else{
					$response = [
						'status' => 0,
						'message' => 'Extensi File Tidak bisa diUpload'
						];
					return $this->response->setJSON($response);
				}
			} else {

				if($is_removed == 1){
					$url = "/home/ngi/php/akpol-api/";
						
					if($datafile->file_materi_tugas != NULL){
						if (file_exists($url . $datafile->file_materi_tugas)) {
							unlink($datafile->file_materi_tugas);
						}
					}
					$dontchange = $this->db->query("SELECT id,file_materi_tugas,id_tipe_file from t_jadwal_tugas where id='" . $id_tugas . "'")->getRow();
					$dataJadwal = [
						'id_jadwal' 		=> $id_jadwal,
						'judul' 			=> $judul,
						'deskripsi'			=> $deskripsi,
						'waktu_pengumpulan' => $waktu_pengumpulan,
						'file_materi_tugas' => NULL,
						'tipe_tugas'		=> $tipe_tugas,
						'id_tipe_file'		=> NULL
	
					];
					$builder = $this->db->table('t_jadwal_tugas');
					$builder->where('id', $id_tugas);
					$execute = $builder->update($dataJadwal);
					if($execute){
						$response = [
							'status' => 1,
							'message' => 'Success'
						];
						return $this->response->setJSON($response);
					} else {
						$response = [
							'status' => 0,
							'message' => 'Failed'
						];
						return $this->response->setJSON($response);
					}
				}else{
					$dontchange = $this->db->query("SELECT id,file_materi_tugas,id_tipe_file from t_jadwal_tugas where id='" . $id_tugas . "'")->getRow();
					$dataJadwal = [
						'id_jadwal' 		=> $id_jadwal,
						'judul' 			=> $judul,
						'deskripsi'			=> $deskripsi,
						'waktu_pengumpulan' => $waktu_pengumpulan,
						'file_materi_tugas' => $dontchange->file_materi_tugas,
						'tipe_tugas'		=> $tipe_tugas,
						'id_tipe_file'		=> $dontchange->id_tipe_file
	
					];
					$builder = $this->db->table('t_jadwal_tugas');
					$builder->where('id', $id_tugas);
					$execute = $builder->update($dataJadwal);
					if($execute){
						$response = [
							'status' => 1,
							'message' => 'Success'
						];
						return $this->response->setJSON($response);
					} else {
						$response = [
							'status' => 0,
							'message' => 'Failed'
						];
						return $this->response->setJSON($response);
					}
				}
			
				// echo $this->db->getLastQuery();
			}
		}
	}

	public function getReason(){
		$query = $this->db->query("SELECT id,alasan from m_alasan_tidak_hadir where is_deleted=0 ");
		
		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
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

	public function getViolationCategory(){
		$query = $this->db->query("SELECT id,kategori from m_kategori_pelanggaran_karakter");
		
		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
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


	public function getCharacterValue(){
		$query = $this->db->query("SELECT id,karakter from m_karakter_penilaian");
		
		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
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

	public function getViolationTaruna(){
		$kat_pelanggaran = $_POST['id_katpelanggaran'];
		$karakter 		 = $_POST['id_karakter'];

		$query = $this->db->query("SELECT * from m_pelanggaran_karakter where id_kategori_pelanggaran='".$kat_pelanggaran."' and id_karakter_penilaian='".$karakter."'");
		
		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
						];
			return $this->response->setJSON($response);
		}else{
			$response = [
						'status' => 0,
						'message' => 'Data Tidak Ditemukan'
						];
			return $this->response->setJSON($response);
		}
		

	}

	public function SaveViolationTaruna(){
		$id_pengajar = $_POST['id_user'];
		$id_taruna 	 = $_POST['id_taruna'];


	}

	public function listViolations(){
		$id_pengajar = $_POST['id_user'];

		$query = $this->db->query("");

		$jml = $query->getFieldCount();

		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
						];
			return $this->response->setJSON($response);
		}else{
			$response = [
						'status' => 0,
						'message' => 'Data Tidak Ditemukan'
						];
			return $this->response->setJSON($response);
		}

	}

	public function listViolationsChecked(){
		$id_pengajar = $_POST['id_user'];
		
		$query = $this->db->query("");

		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
						];
			return $this->response->setJSON($response);
		}else{
			$response = [
						'status' => 0,
						'message' => 'Data Tidak Ditemukan'
						];
			return $this->response->setJSON($response);
		}

	}

	public function scheduleByDate(){
		$id_pengajar 	= $_POST['id_user'];
		$date 			= $_POST['tanggal'];
		$query = $this->db->query("SELECT a.id as id_jadwal,c.kelompok,g.`mata_pelajaran`,e.`judul`,e.`deskripsi`,e.pertemuan_ke,a.tanggal,a.is_absensi_pendidik,a.`jam_mulai`,a.`jam_selesai`,d.`nama` as lokasi_kelas,
								(case when (g.`is_teori` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas from `t_jadwal` a
								LEFT JOIN m_kelompok c on a.`id_kelompok_taruna`=c.id
								LEFT JOIN m_ruang_kelas d on a.`id_ruang_kelas`=d.`id`
								LEFT JOIN t_bahan_ajar e on a.id_bahan_ajar=e.id
								LEFT JOIN m_user_pendidik f on a.`id_user_pendidik`=f.id_m_user
								LEFT JOIN m_mata_pelajaran g on e.`id_mata_pelajaran`=g.id
								where f.id_m_user='".$id_pengajar."' and date(a.tanggal)='".$date."' group by g.mata_pelajaran"); //ditambahi where date= choosen date

		$resData = $query->getRow();

		if($resData != NULL){			
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
						];
			return $this->response->setJSON($response);
		}else{
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
				];
			return $this->response->setJSON($response);
		}
	
	}

	public function evaluationClass(){
		$id_jadwal = $_POST['id_jadwal'];
		$query = $this->db->query("SELECT a.id as id_jadwal,c.`is_teori` from t_jadwal a
							left join t_bahan_ajar b on a.id_bahan_ajar=b.id
							left join m_mata_pelajaran c on b.`id_mata_pelajaran`=c.id
							where a.id='".$id_jadwal."' ");
		$dataJadwal = $query->getRow();
		if($dataJadwal != NULL){
			$is_teori = $dataJadwal->is_teori;
			$dataPenilaian = $this->db->query("SELECT id as id_aktivitas,aktivitas,poin,`is_positif` from m_penilaian_aktivitas_pembelajaran where `is_teori`='".$is_teori."' and is_deleted=0")->getResult();
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $dataPenilaian
				];
			return $this->response->setJSON($response);
		}else{
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Jadwal'
				];
			return $this->response->setJSON($response);
		}
	}

	public function getTarunaAssessment(){
		$id_taruna = $_POST['id_taruna'];
		$id_jadwal = $_POST['id_jadwal'];
		

		$eval = $this->db->query("SELECT a.id as id_jadwal,c.`is_teori` from t_jadwal a
							left join t_bahan_ajar b on a.id_bahan_ajar=b.id
							left join m_mata_pelajaran c on b.`id_mata_pelajaran`=c.id
							where a.id='".$id_jadwal."' ");
		

		
		$dataEval = $eval->getRow();

		
		if($dataEval != NULL){
			$is_teori = $dataEval->is_teori;
			$dataPenilaian = $this->db->query("SELECT id as id_aktivitas,aktivitas,poin,`is_positif` from m_penilaian_aktivitas_pembelajaran where `is_teori`='".$is_teori."' and is_deleted=0")->getResult();
			if($dataPenilaian != NULL){

				$query = $this->db->query("SELECT b.id as id_aktivitas,b.aktivitas,b.is_positif,b.poin, count(a.id_aktivitas) as jml_aktivitas,sum(b.poin) as total_poin from t_penilaian_aktifitas_pembelajaran a
					LEFT JOIN m_penilaian_aktivitas_pembelajaran b on a.id_aktivitas=b.id
					where a.id_jadwal='".$id_jadwal."' and a.id_user_taruna ='".$id_taruna."' and a.is_deleted=0
					group by a.id_aktivitas ");
				$dataAktivitas = $query->getResult();

				if($dataAktivitas != NULL){
					
					$response = [
						'status' => 1,
						'message' => 'Success',
						'evaluasi' => $dataPenilaian,
						'data' => $dataAktivitas
						];
					return $this->response->setJSON($response);
				}else{
					$response = [
						'status' => 1,
						'message' => 'Success',
						'evaluasi' => $dataPenilaian,
						'data' => NULL
						];
					return $this->response->setJSON($response);
				}
			}else{
				$response = [
					'status' => 1,
					'message' => 'Tidak Ada Data'
					];
				return $this->response->setJSON($response);
			}
			
		}else{
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Jadwal'
				];
			return $this->response->setJSON($response);
		}
		

		
	}

	public function saveTarunaAssessment()
	{
		
		$this->db->transStart();

		$id_taruna = $_POST['id_taruna'];
		$id_jadwal = $_POST['id_jadwal'];
		$penilaian = $_POST['penilaian'];

		$arrPenilaian 	= json_decode($penilaian, true);
		// print_r($arrPenilaian);
		
		$isInsert = false;

		if($arrPenilaian){

			$penilaianExist = $this->db->query("SELECT a.*,count(a.id_aktivitas) as jml_aktivitas,sum(a.poin) as total_poin from (SELECT a.id as id_penilaian,a.id_jadwal,c.id_user_pendidik,b.aktivitas,b.is_positif,b.poin,a.`id_aktivitas`,a.id_user_taruna,a.is_deleted from t_penilaian_aktifitas_pembelajaran a
			LEFT JOIN m_penilaian_aktivitas_pembelajaran b on a.id_aktivitas=b.id
			LEFT JOIN t_jadwal c on a.id_jadwal=c.id
			) a
			where a.id_jadwal='".$id_jadwal."' and a.id_user_taruna ='".$id_taruna."' and a.is_deleted=0
			group by a.id_aktivitas");

			$dataPenilaian = $penilaianExist->getResult();
			$cekData = $penilaianExist->getRow();

			if($dataPenilaian != NULL){
		
				$id_pengajar = $cekData->id_user_pendidik;
		
				$this->db->query("UPDATE t_penilaian_aktifitas_pembelajaran set is_deleted=1,last_edited_at=NOW(),last_edited_by='".$id_pengajar."' WHERE id_user_taruna='".$id_taruna."' and id_jadwal ='".$id_jadwal."' and is_deleted=0 ");
				
				$i = 1;
				$arrItem = [];
				
				foreach ($arrPenilaian as $key => $value) {
					
					$jml_item = $value['jumlah'];
					// print_r($jml_item);
					if($jml_item > 1){
						
						for ($i=0; $i < $jml_item; $i++) {

							$data['id_jadwal'] = $value['id_jadwal'];
							$data['id_user_taruna'] = $value['id_user_taruna'];	        	
							$data['id_aktivitas'] = $value['id_aktivitas'];
							array_push($arrItem, $data);
						}
						
					}else if($jml_item == 0){
						
						$data['id_jadwal'] = $value['id_jadwal'];
						$data['id_user_taruna'] = $value['id_user_taruna'];	        	
						$data['id_aktivitas'] = $value['id_aktivitas'];
						$this->db->query("UPDATE t_penilaian_aktifitas_pembelajaran set is_deleted=1,last_edited_at=NOW(),last_edited_by='".$id_pengajar."' WHERE id_user_taruna='".$value['id_user_taruna']."' and id_aktivitas = '".$value['id_aktivitas']."'and id_jadwal ='".$value['id_jadwal']."' and is_deleted=0 ");
						// array_push($arrItem, $data);
						// echo $this->db->getLastQuery();
					}else{
						
						$data['id_jadwal'] = $value['id_jadwal'];
						$data['id_user_taruna'] = $value['id_user_taruna'];	        	
						$data['id_aktivitas'] = $value['id_aktivitas'];
						array_push($arrItem, $data);
					}
					// echo $jml_item;
					$arrPenilaian[$key] = $value;
					// print_r($value);
					

					$i = $i + 1;
				}
				
				if($arrItem != NULL){
					$builder = $this->db->table('t_penilaian_aktifitas_pembelajaran');
					$builder->insertBatch($arrItem);
					$isInsert = true;
				}

				
				
				if ($this->db->transStatus() === FALSE) {
					$iscommit = false;
					$this->db->transRollback();
				} else {
					$iscommit = true;
					$this->db->transCommit();
				}

				if ($iscommit === true) {

					$response = [
						'status' => 1,
						'message' => 'Success'
					];
				} else {
					$response = [
						'status' => 0,
						'message' => 'Tidak ada data'
					];
				}
				return $this->response->setJSON($response);
				
			}else{
				
				$i = 1;
				$arrItem = [];
				
				foreach ($arrPenilaian as $key => $value) {
					
					$jml_item = $value['jumlah'];
				
					if($jml_item > 1){
						// echo "loop";
						for ($i=0; $i < $jml_item; $i++) {

							$data['id_jadwal'] = $value['id_jadwal'];
							$data['id_user_taruna'] = $value['id_user_taruna'];	        	
							$data['id_aktivitas'] = $value['id_aktivitas'];
							array_push($arrItem, $data);
						}
						
					}else{
						$data['id_jadwal'] = $value['id_jadwal'];
						$data['id_user_taruna'] = $value['id_user_taruna'];	        	
						$data['id_aktivitas'] = $value['id_aktivitas'];
						array_push($arrItem, $data);
					}
					
					
					$arrPenilaian[$key] = $value;
					// print_r($value);
					
					$i = $i + 1;
				}
				
				
				
				$builder = $this->db->table('t_penilaian_aktifitas_pembelajaran');
				$builder->insertBatch($arrItem);
				$isInsert = true;
				
				if ($this->db->transStatus() === FALSE) {
					$iscommit = false;
					$this->db->transRollback();
				} else {
					$iscommit = true;
					$this->db->transCommit();
				}

				if ($iscommit === true) {

					$response = [
						'status' => 1,
						'message' => 'Success'
					];
				} else {
					$response = [
						'status' => 0,
						'message' => 'Tidak ada data'
					];
				}
				
				return $this->response->setJSON($response);
				
			}

		}else{
			$response = [
				'status' => 0,
				'message' => 'Data Penilaian Kosong / Format salah'
			];
			return $this->response->setJSON($response);
		}
		
	
	}

	public function attendanceSignTaruna(){
		
		$this->db->transStart();

		$absensi = $_POST['data_absensi'];
		$id_jadwal = $_POST['id_jadwal'];

		$arrAbsensi 	= json_decode($absensi, true);
		// print_r($arrAbsensi);
		
		$isInsert = false;

		
		$query = $this->db->query("SELECT a.id as id_absensi,b.id_m_user as id_taruna,a.id_jadwal,a.is_absen,a.keterangan,a.face_is_absen,b.namataruna,b.noaklong,b.photopath from t_absensi a
		LEFT JOIN m_user_taruna b on a.id_taruna=b.id_m_user
		where a.id_jadwal='".$id_jadwal."' and a.is_deleted=0");

		$dataAbsen = $query->getResult();
		if($dataAbsen != NULL){
			foreach ($arrAbsensi as $key => $value) {

				$arrAbsensi[$key] = $value;
		
			}
			
			$builder = $this->db->table('t_absensi');
			$builder->updateBatch($arrAbsensi, 'id');
			$isInsert = true;
			
			if ($this->db->transStatus() === FALSE) {
				$iscommit = false;
				$this->db->transRollback();
			} else {
				$iscommit = true;
				$this->db->transCommit();
			}

			if ($iscommit === true) {

				$response = [
					'status' => 1,
					'message' => 'Success'
				];
			} else {
				$response = [
					'status' => 0,
					'message' => 'Format Data Salah'
				];
			}
			return $this->response->setJSON($response);
		}else{
			$response = [
				'status' => 0,
				'message' => 'Jadwal Tidak Ditemukan'
				];
			return $this->response->setJSON($response);
		}

		
	}


	public function subjectTeaching(){
	
		$id_pengajar	= $_POST['id_user'];
		$tahun_ajaran 	= $this->getTahunAjaran();	

		$query 	= $this->db->query("SELECT a.id_pendidik as id_user_pendidik,a.id_mata_pelajaran,d.mata_pelajaran,d.deskripsi,c.ketua_tim,c.photopath,b.kelompok,id_semester,CONCAT(IF(LEFT(id_semester, 1) % 2 <> 0, 'Semester Ganjil', 'Semester Genap'),' - ','Tahun ajaran ', a.tahun_ajaran_awal,'/',a.tahun_ajaran_akhir) as info_semester,b.tahun_ajaran
		FROM t_pendidik_mata_pelajaran a
	    LEFT JOIN m_mata_pelajaran d on a.id_mata_pelajaran=d.id
		LEFT JOIN (SELECT a.id,b.id_mata_pelajaran,c.kelompok,b.tahun_ajaran from t_jadwal a
					LEFT JOIN t_bahan_ajar b on a.id_bahan_ajar=b.id
					LEFT JOIN m_kelompok c on a.id_kelompok_taruna=c.id
					LEFT JOIN m_user_pendidik d on a.id_user_pendidik=d.id_m_user
					where d.id_m_user='".$id_pengajar."' group by b.id_mata_pelajaran) b on d.id=b.id_mata_pelajaran
		LEFT JOIN (SELECT a.id as id_mapel,b.id_pendidik,b.is_ketua_tim,c.namagadik as ketua_tim,c.photopath from m_mata_pelajaran a
					LEFT JOIN t_pendidik_mata_pelajaran b on a.id=b.id_mata_pelajaran
					LEFT JOIN `m_user_pendidik` c on b.id_pendidik=c.id_m_user
					WHERE b.is_ketua_tim=1) c on b.id_mata_pelajaran=c.id_mapel
	  
		LEFT JOIN m_user_pendidik e on a.id_pendidik=e.id_m_user
		where e.id_m_user='".$id_pengajar."' and b.tahun_ajaran='".$tahun_ajaran."'");

		// $query 	= $this->db->query("SELECT a.id_pendidik as id_user_pendidik,a.id_mata_pelajaran,c.mata_pelajaran,c.deskripsi,c.ketua_tim,c.photopath,b.kelompok,id_semester,CONCAT(IF(LEFT(id_semester, 1) % 2 <> 0, 'Semester Ganjil', 'Semester Genap'),' - ','Tahun ajaran ', a.tahun_ajaran_awal,'/',a.tahun_ajaran_akhir) as info_semester,b.tahun_ajaran
		// FROM t_pendidik_mata_pelajaran a
		// LEFT JOIN m_mata_pelajaran d on a.id_mata_pelajaran=d.id
		// LEFT JOIN m_user_pendidik e on a.id_user_pendidik=e.id_m_user
		// LEFT JOIN (SELECT a.id,b.id_mata_pelajaran,c.kelompok,b.tahun_ajaran from t_jadwal a
		// 			LEFT JOIN t_bahan_ajar b on a.id_bahan_ajar=b.id
		// 			LEFT JOIN m_kelompok c on a.`id_kelompok_taruna`=c.id
		// 			LEFT JOIN m_user_pendidik d on a.id_user_pendidik=d.id_m_user
		// 			where d.id_m_user='".$id_pengajar."' group by b.id_mata_pelajaran) b on c.id=b.id_mata_pelajaran
		// LEFT JOIN (SELECT a.id as id_mapel,b.id_pendidik,b.is_ketua_tim,c.namagadik as ketua_tim,c.photopath from m_mata_pelajaran a
		// 			LEFT JOIN t_pendidik_mata_pelajaran b on a.id=b.id_mata_pelajaran
		// 			LEFT JOIN `m_user_pendidik` c on b.id_pendidik=c.id
		// 			WHERE b.is_ketua_tim=1) c on b.id_mata_pelajaran=c.id_mapel
		// where e.id_m_user='".$id_pengajar."' and b.tahun_ajaran='".$tahun_ajaran."'");



		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
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

	public function scheduleTeaching(){
	
		$id_pengajar	= $_POST['id_user'];
		$tahun_ajaran 	= $this->getTahunAjaran();	

		$query 	= $this->db->query("SELECT a.id_pendidik as id_user_pendidik,a.id_mata_pelajaran,a.is_ketua_tim,c.mata_pelajaran,c.deskripsi,b.kelompok,id_semester,CONCAT(IF(LEFT(id_semester, 1) % 2 <> 0, 'Semester Ganjil', 'Semester Genap'),' - ','Tahun Ajaran ', a.tahun_ajaran_awal,'/',a.tahun_ajaran_akhir) as info_semester,b.tahun_ajaran
		FROM t_pendidik_mata_pelajaran a
		LEFT JOIN m_mata_pelajaran c on a.id_mata_pelajaran=c.id
		LEFT JOIN (SELECT a.id,b.id_mata_pelajaran,c.kelompok,b.tahun_ajaran from t_jadwal a
		LEFT JOIN t_bahan_ajar b on a.id_bahan_ajar=b.id
		LEFT JOIN m_kelompok c on a.`id_kelompok_taruna`=c.id
		where a.id_user_pendidik=5 group by b.id_mata_pelajaran) b on c.id=b.id_mata_pelajaran
		where a.id_pendidik='".$id_pengajar."' and b.tahun_ajaran='".$tahun_ajaran."' ");

		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
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
	
	public function overviewClass() {
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_pengajar 		= $_POST['id_user'];
		$tahun_ajaran 	= $this->getTahunAjaran();

		$query = $this->db->query("SELECT a.id as id_jadwal,
		g.kelompok,
		d.mata_pelajaran,
		b.judul,
	   	b.deskripsi,
		b.pertemuan_ke,
		a.is_absensi_pendidik,
		a.tanggal,
		a.jam_mulai,
		a.jam_selesai,
		h.`nama` as lokasi_kelas,
		(case when (d.`is_teori` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas,
			CONCAT(sf_formatdate_ID(a.tanggal),' • ', a.jam_mulai,' ',a.jam_selesai, ' WIB') as tanggal_detail,
			  b.tahun_ajaran,
			  (case when (c.file_materi_tugas is null) then '0' else '1' end) as file_ass,
		 (case when (e.jml_file is null) then '0' else e.jml_file end) as file_att,
		 f.jml_hadir as jml_hadir,
		 f.jml_tdk_hadir as jml_tdk_hadir
			  from `t_jadwal` a
			  LEFT JOIN t_bahan_ajar b on a.id_bahan_ajar=b.id
			  LEFT JOIN (SELECT * from t_jadwal_tugas) c on c.id_jadwal=a.id
			  LEFT JOIN m_mata_pelajaran d on b.id_mata_pelajaran=d.id
			  LEFT JOIN (SELECT count(a.lokasi_file) as jml_file,a.id_mata_pelajaran,a.pertemuan_ke from t_file_materi a
						  where a.id_user_pendidik='".$id_pengajar."' and is_deleted=0 and a.tahun_ajaran='".$tahun_ajaran."' group by a.pertemuan_ke) e on d.id=e.id_mata_pelajaran and b.pertemuan_ke=e.pertemuan_ke
			   LEFT JOIN (SELECT id_jadwal,
							SUM(CASE 
								WHEN a.is_absen = 1 THEN 1
								ELSE 0
								END) AS jml_hadir,
							SUM(CASE 
								WHEN a.is_absen = 0 THEN 1
								ELSE 0
					 		END) AS jml_tdk_hadir from 
							(SELECT a.id_jadwal,a.is_absen from t_absensi a 
					 		LEFT JOIN t_jadwal b on a.id_jadwal=b.id) a 
							GROUP BY a.id_jadwal) f on f.id_jadwal=a.id
					LEFT JOIN m_kelompok g on a.id_kelompok_taruna=g.id
					LEFT JOIN m_ruang_kelas h on a.`id_ruang_kelas`=h.`id`
				   	LEFT JOIN m_user_pendidik i on a.id_user_pendidik=i.id_m_user
		  where i.id_m_user='".$id_pengajar."' and b.id_mata_pelajaran='".$id_mata_pelajaran."' and a.is_absensi_pendidik=1");

		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
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

	public function sessionClass(){
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_pengajar 		= $_POST['id_user'];
		$tahun_ajaran 	= $this->getTahunAjaran();

		$query = $this->db->query("SELECT a.id as id_jadwal,
		g.kelompok,
		d.mata_pelajaran,
		b.judul,
	   	b.deskripsi,
		b.pertemuan_ke,
		a.is_absensi_pendidik,
		a.tanggal,
		a.jam_mulai,
		a.jam_selesai,
		h.`nama` as lokasi_kelas,
		(case when (d.`is_teori` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas,
			CONCAT(sf_formatdate_ID(a.tanggal),' • ', a.jam_mulai,' ',a.jam_selesai, ' WIB') as tanggal_detail,
			  b.tahun_ajaran,
			  (case when (c.file_materi_tugas is null) then '0' else '1' end) as file_ass,
		 (case when (e.jml_file is null) then '0' else e.jml_file end) as file_att,
		 f.jml_hadir as jml_hadir,
		 f.jml_tdk_hadir as jml_tdk_hadir
			  from `t_jadwal` a
			  LEFT JOIN t_bahan_ajar b on a.id_bahan_ajar=b.id
			  LEFT JOIN (SELECT * from t_jadwal_tugas) c on c.id_jadwal=a.id
			  LEFT JOIN m_mata_pelajaran d on b.id_mata_pelajaran=d.id
			  LEFT JOIN (SELECT count(a.lokasi_file) as jml_file,a.id_mata_pelajaran,a.pertemuan_ke from t_file_materi a
						  where a.id_user_pendidik='".$id_pengajar."' and id_mata_pelajaran ='".$id_mata_pelajaran."' and is_deleted=0 and a.tahun_ajaran='".$tahun_ajaran."' group by a.pertemuan_ke) e on d.id=e.id_mata_pelajaran and b.pertemuan_ke=e.pertemuan_ke
			   LEFT JOIN (SELECT id_jadwal,
							SUM(CASE 
								WHEN a.is_absen = 1 THEN 1
								ELSE 0
								END) AS jml_hadir,
							SUM(CASE 
								WHEN a.is_absen = 0 THEN 1
								ELSE 0
					 		END) AS jml_tdk_hadir from 
							(SELECT a.id_jadwal,a.is_absen from t_absensi a 
					 		LEFT JOIN t_jadwal b on a.id_jadwal=b.id) a 
							GROUP BY a.id_jadwal) f on f.id_jadwal=a.id
					LEFT JOIN m_kelompok g on a.id_kelompok_taruna=g.id
					LEFT JOIN m_ruang_kelas h on a.`id_ruang_kelas`=h.`id`
				   	LEFT JOIN m_user_pendidik i on a.id_user_pendidik=i.id_m_user
		  where i.id_m_user='".$id_pengajar."' and b.id_mata_pelajaran='".$id_mata_pelajaran."' and a.is_absensi_pendidik=1");

		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
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

	public function assigmentClass(){
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_pengajar 		= $_POST['id_user'];

		$query = $this->db->query("SELECT a.id,c.kelompok,g.`mata_pelajaran`,a.`jam_mulai`,a.`jam_selesai`,d.`nama` as lokasi_kelas,a.tanggal,sf_formatdate_ID(a.tanggal) as tanggal_detail,e.deskripsi,
		(case when (g.`is_teori` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas from `t_jadwal` a
		LEFT JOIN  t_kelompok_taruna b on a.`id_kelompok_taruna`=b.id
		LEFT JOIN m_kelompok c on b.`id_kelompok`=c.id
		LEFT JOIN m_ruang_kelas d on a.`id_ruang_kelas`=d.`id`
		LEFT JOIN t_bahan_ajar e on a.id_bahan_ajar=e.id
		LEFT JOIN m_user_pendidik f on e.`id_user_pendidik`=f.id
		LEFT JOIN m_mata_pelajaran g on e.`id_mata_pelajaran`=g.id
		where f.id='".$id_pengajar."' and e.id_mata_pelajaran='".$id_mata_pelajaran."'");

		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
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

	public function filesClass(){
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_pengajar 		= $_POST['id_user'];

		$query = $this->db->query("SELECT a.id,c.kelompok,g.`mata_pelajaran`,a.`jam_mulai`,a.`jam_selesai`,d.`nama` as lokasi_kelas,a.tanggal,sf_formatdate_ID(a.tanggal) as tanggal_detail,e.deskripsi,
		(case when (g.`is_teori` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas from `t_jadwal` a
		LEFT JOIN  t_kelompok_taruna b on a.`id_kelompok_taruna`=b.id
		LEFT JOIN m_kelompok c on b.`id_kelompok`=c.id
		LEFT JOIN m_ruang_kelas d on a.`id_ruang_kelas`=d.`id`
		LEFT JOIN t_bahan_ajar e on a.id_bahan_ajar=e.id
		LEFT JOIN m_user_pendidik f on e.`id_user_pendidik`=f.id
		LEFT JOIN m_mata_pelajaran g on e.`id_mata_pelajaran`=g.id
		where f.id='".$id_pengajar."' and e.id_mata_pelajaran='".$id_mata_pelajaran."'");

		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
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

	public function studentClass(){
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_pengajar 		= $_POST['id_user'];

		$query = $this->db->query("SELECT a.id,c.kelompok,g.`mata_pelajaran`,a.`jam_mulai`,a.`jam_selesai`,d.`nama` as lokasi_kelas,a.tanggal,sf_formatdate_ID(a.tanggal) as tanggal_detail,e.deskripsi,
		(case when (g.`is_teori` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas from `t_jadwal` a
		LEFT JOIN  t_kelompok_taruna b on a.`id_kelompok_taruna`=b.id
		LEFT JOIN m_kelompok c on b.`id_kelompok`=c.id
		LEFT JOIN m_ruang_kelas d on a.`id_ruang_kelas`=d.`id`
		LEFT JOIN t_bahan_ajar e on a.id_bahan_ajar=e.id
		LEFT JOIN m_user_pendidik f on e.`id_user_pendidik`=f.id
		LEFT JOIN m_mata_pelajaran g on e.`id_mata_pelajaran`=g.id
		where f.id='".$id_pengajar."' and e.id_mata_pelajaran='".$id_mata_pelajaran."'");

		$resData = $query->getRow();

		if($resData != NULL){		
			$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $query->getResult()
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
	//END OF PENGAJAR///


	/// TARUNA///

	//END OF TARUNA///


}
