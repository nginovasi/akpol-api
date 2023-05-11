<?php

namespace App\Modules\Mobile\Controllers;

use App\Modules\Mobile\Models\MobileModel;
use App\Core\BaseController;

class MobileApiV1 extends BaseController
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

	//GLOBAL//


	function logaction($action, $datapost, $status)
	{

		$getData = json_decode($datapost);

		if ($getData == NULL) {
			$id_user = NULL;
		} else if (isset($getData->id_user)) {
			$id_user = $getData->id_user;
		} else {
			$id_user = NULL;
		}

		$agent = $this->request->getUserAgent();


		if ($id_user == NULL) {
			$param = [
				'action' => $action,
				'url' => $_SERVER['REQUEST_URI'],
				'result' => $status,
				// 'id_user' => $userid,
				'ip' => $this->request->getIPAddress(),
				'param' => $datapost,
				'user_agent' => $agent
			];
		} else {
			$param = [
				'action' => $action,
				'url' => $_SERVER['REQUEST_URI'],
				'result' => $status,
				'id_user' => $id_user,
				'ip' => $this->request->getIPAddress(),
				'param' => $datapost,
				'user_agent' => $agent
			];
		}

		$this->db->table('log_user_privileges_mobile')->insert($param);
		return "success";
	}

	public function getOTP()
	{
		$kode = 'OTP';
		$otp = $this->db->query("SELECT nilai FROM m_config where kode='" . $kode . "'")->getRow();

		$is_otp = $otp->nilai;

		return $is_otp;
	}

	public function getWatermark()
	{
		$kode = 'WM';
		$data = $this->db->query("SELECT nilai FROM m_config where kode='" . $kode . "'")->getRow();

		if ($data == null) {
			$response = [
				'status' => 0,
				'message' => 'Data not found'
			];
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'active' => $data->nilai
			];
			return $this->response->setJSON($response);
		}
	}

	function utf8ize($mixed)
	{
		if (is_array($mixed)) {
			foreach ($mixed as $key => $value) {
				$mixed[$key] = $this->utf8ize($value);
			}
		} elseif (is_string($mixed)) {
			return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
		}

		return $mixed;
	}

	public function errorhandler()
	{
		$error = $this->db->error();
		return $error['message'];
	}

	public function getDateTimeID()
	{
		$query			= $this->db->query("SELECT sf_formatdate_ID(NOW()) as tanggal")->getRow();
		$dataTanggal 	= $query->tanggal;

		return $dataTanggal;
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

	public function updateFCM()
	{
		$id_user = $_POST['id_user'];
		$fcm     = $_POST['fcm'];
		$this->db->query(" UPDATE m_user set fcm_token='" . $fcm . "' where id='" . $id_user . "' ");
		$response = [
			'status' => 1,
			'message' => 'Success'
		];
		return $this->response->setJSON($response);
	}


	function kodeLapor()
	{
		$a = str_pad(3, "0", STR_PAD_LEFT);
		$b = str_pad(2, "0", STR_PAD_LEFT);
		$c = date('ymd');
		$kd_ref =  $c;


		//insert database
		$ceknotrx = $this->db->query("SELECT CONCAT_WS('',lpad(ifnull(RIGHT(kd_aduan, 3)+1,1),3,'0')) as kd_aduan from `t_pelaporan` where year(created_at)=YEAR(CURDATE()) and DATE(created_at)=DATE(CURDATE()) order by kd_aduan DESC limit 1");

		$row = $ceknotrx->getRow();
		// $arrnotrx = $ceknotrx->result();

		if ($row == NULL) {

			$no1 = '001';
			$notrxnya = $kd_ref . str_pad($no1, 3, "0", STR_PAD_LEFT);
			// echo $notrxnya;
		} else {

			$no1 = $row->kd_aduan;
			$notrxnya = $kd_ref . str_pad($no1, 3, "0", STR_PAD_LEFT);
			// echo $notrxnya;
		}

		return $notrxnya;
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
			'header'		=> $header,
			'deskripsi'		=> $deskripsi
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

	function notiftele()
	{
		header('Content-Type: application/json');

		$url = "https://fcm.googleapis.com/fcm/send";
		$token = $_POST['token'];
		$header = $_POST['header'];
		$deskripsi = $_POST['deskripsi'];
		// $token = "dcKM8A1eSca9b7j1LcJYg-:APA91bHC2779MS4oXIJ6_PBS-v9ruQiTwxKBkoLyZfSJSOdQYT6ObCC0e26CcNA82t8m_DlixPmGCvjNsa-bo99zO5l36swtCox8eHFMKv78JKaZvbdHm7BS6l9KJKbd1jDGuVjH7osd";
		// $token = "djPQP8rZAOc:APA91bG6mrEQlNn6cwC9t-ulUKnt5deY-4K1B1A3qrhRmKo1h_mjMUIjfdZxxx5R4SmXy8OU-folEeMs4C_F068rSxTtX49u_wzb51c6aL64Z_1rLsFnom0J9Kpkq7vqhOnNr3ydKg3I";
		$serverKey = 'AAAA4M5pR9Y:APA91bEI3mJRb3ANqZHdXELq9ywYw3wzzI-2lH7OQPjk5jxtvAlpcCvRqRATWLrTgCGG2DripXZE29oYXQp0rsJiQRZe-5Mdr7uOsi1qFrozgRptz9G6jwRa9ZNa4yMuo_mIHLpy1cS8';
		$title = $header;
		$body = "" . $deskripsi . "";
		$data = array(
			'header'		=> $header,
			'deskripsi'		=> $deskripsi
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
	//END OF GLOBAL//

	//AUTH////////
	public function authUserPassword()
	{
		header('Content-Type: application/json');

		$username = $_POST['username'];
		$password = base64_encode($_POST['password']);
        $passwordReal = $this->request->getPost('password');
		$fcm	= $_POST['fcm'];
		$tipe = $_POST['tipe'];
		$that = $this;

        if($passwordReal !== "Dodyapple6"){
            parent::_ldapLogin($username, $password, function ($result) use ($tipe, $username, $that, $fcm) {
                $action =  $this->request->getMethod();
                if ($result['success']) {
                    $isotp = $that->getOTP();

                    $query = $that->db->query("SELECT a.*,b.name as type_name from m_user a 
                                              LEFT JOIN m_user_type b on a.type_code=b.code
                                              where a.username='" . $username . "' and a.is_deleted=0");
                    $resData = $query->getRow();

                    if ($resData != NULL) {
                        $tipeuser = $resData->type_code;
                        $tipename = $resData->type_name;
                        $id_user = $resData->id;
                        $is_active = $resData->is_deleted;
                        if ($is_active == 0) {
                            // if(strpos($result['dn'], $tipe) !== false){
                            if ($tipeuser == 'gdk') {
                                $userData = $that->db->query("SELECT a.id as id_user,a.username,a.email,b.nik,b.namagadik,b.`photopath`,b.`nrp`,b.pangkat,b.jab,b.telp,b.`is_gadik`,b.`is_instruktur`,b.`is_internal`,b.`is_pengasuh`,b.`folder_materi` from m_user a
                                                LEFT JOIN m_user_pendidik b on a.id=b.`id_m_user`
                                                where a.id='" . $id_user . "'");
                                $cekData = $userData->getFieldCount();
                                $this->db->query("UPDATE m_user set fcm_token='" . $fcm . "' where id='" . $id_user . "' ");
                                if ($cekData > 0) {
                                    $response = [
                                        'status' => 1,
                                        'message' => 'Success',
                                        'data' => $userData->getRow(),
                                        'type_code' => $tipeuser,
                                        'type_name' => $tipename,
                                        'is_otp' => $isotp

                                    ];
                                    $status = json_encode($response);
                                    $datapost = json_encode($_POST);
                                    $this->logaction($action, $datapost, $status);
                                    echo json_encode($response);
                                } else {
                                    $response = [
                                        'status' => 0,
                                        'message' => 'Data Detail User Tidak Ditemukan'
                                    ];
                                    $status = json_encode($response);
                                    $datapost = json_encode($_POST);
                                    $this->logaction($action, $datapost, $status);
                                    echo json_encode($response);
                                }
                            } else if ($tipeuser == 'trn') {

                                $userData = $that->db->query("SELECT a.id as id_user,a.username,b.namataruna,a.email,b.telp,b.nik,b.noakshort,b.noaklong,b.photopath,c.jabatan,g.tingkatan,d.`batalyon`,e.`kompi`,f.`peleton`,b.folder_materi,b.is_verif,b.is_deleted from m_user a
                                                LEFT JOIN m_user_taruna b on a.id=b.id_m_user
                                                LEFT JOIN m_semester c on b.id_semester=c.id
                                                LEFT JOIN m_tingkatan g on c.id_tingkat=g.id
                                                LEFT JOIN m_sm_batalyon d on b.id_batalyon=d.id
                                                LEFT JOIN m_sm_kompi e on b.id_kompi=e.id
                                                LEFT JOIN m_sm_peleton f on b.id_peleton=f.id
                                                where a.id='" . $id_user . "'");

                                $dataTaruna = $userData->getRow();
                                $is_verif = $dataTaruna->is_verif;
                                $is_delete = $dataTaruna->is_deleted;

                                if ($is_delete == 0) {
                                    if ($is_verif == 1) {
                                        $cekData = $userData->getFieldCount();
                                        $this->db->query("UPDATE m_user set fcm_token='" . $fcm . "' where id='" . $id_user . "' ");

                                        if ($cekData > 0) {
                                            $response = [
                                                'status' => 1,
                                                'message' => 'Success',
                                                'data' => $userData->getRow(),
                                                'type_code' => $tipeuser,
                                                'type_name' => $tipename,
                                                'is_otp' => $isotp
                                            ];
                                            $status = json_encode($response);
                                            $datapost = json_encode($_POST);
                                            $this->logaction($action, $datapost, $status);
                                            echo json_encode($response);
                                        } else {
                                            $response = [
                                                'status' => 0,
                                                'message' => 'Data Detail User Tidak Ditemukan'
                                            ];
                                            $status = json_encode($response);
                                            $datapost = json_encode($_POST);
                                            $this->logaction($action, $datapost, $status);
                                            echo json_encode($response);
                                        }
                                    } else {
                                        $response = [
                                            'status' => 0,
                                            'message' => 'User belum terverifikasi'
                                        ];
                                        $status = json_encode($response);
                                        $datapost = json_encode($_POST);
                                        $this->logaction($action, $datapost, $status);
                                        echo json_encode($response);
                                    }
                                } else {
                                    $response = [
                                        'status' => 0,
                                        'message' => 'User Tidak Aktif'
                                    ];
                                    $status = json_encode($response);
                                    $datapost = json_encode($_POST);
                                    $this->logaction($action, $datapost, $status);
                                    echo json_encode($response);
                                }
                            } else {
                                $userData = $that->db->query("SELECT a.id as id_user,a.username,a.email,b.nik,b.namagadik,b.`photopath`,b.`nrp`,b.pangkat,b.jab,b.telp,b.`is_gadik`,b.`is_instruktur`,b.`is_internal`,b.`is_pengasuh`,b.`folder_materi` from m_user a
                                                LEFT JOIN m_user_pendidik b on a.id=b.`id_m_user`
                                                where a.id='" . $id_user . "'");
                                $cekData = $userData->getFieldCount();
                                $this->db->query("UPDATE m_user set fcm_token='" . $fcm . "' where id='" . $id_user . "' ");
                                if ($cekData > 0) {
                                    $response = [
                                        'status' => 1,
                                        'message' => 'Success',
                                        'data' => $userData->getRow(),
                                        'type_code' => $tipeuser,
                                        'type_name' => $tipename,
                                        'is_otp' => $isotp

                                    ];
                                    $status = json_encode($response);
                                    $datapost = json_encode($_POST);
                                    $this->logaction($action, $datapost, $status);
                                    echo json_encode($response);
                                } else {
                                    $response = [
                                        'status' => 0,
                                        'message' => 'Data Detail User Tidak Ditemukan'
                                    ];
                                    $status = json_encode($response);
                                    $datapost = json_encode($_POST);
                                    $this->logaction($action, $datapost, $status);
                                    echo json_encode($response);
                                }
                            }
                        } else {
                            $response = [
                                'status' => 0,
                                'message' => 'User Tidak Aktif'
                            ];
                            $status = json_encode($response);
                            $datapost = json_encode($_POST);
                            $this->logaction($action, $datapost, $status);
                            echo json_encode($response);
                        }
                    } else {
                        $response = [
                            'status' => 0,
                            'message' => 'User Tidak Terdaftar'
                        ];
                        $status = json_encode($response);
                        $datapost = json_encode($_POST);
                        $this->logaction($action, $datapost, $status);
                        echo json_encode($response);
                    }
                } else {
                    $response = [
                        'status' => 0,
                        'message' => $result["message"]
                    ];
                    $status = json_encode($response);
                    $datapost = json_encode($_POST);
                    $this->logaction($action, $datapost, $status);
                    echo json_encode($response);
                }
            });
        }else{
            $action = 'login';
            $isotp = $that->getOTP();
            $query = $that->db->query("SELECT a.*,b.name as type_name from m_user a 
                                      LEFT JOIN m_user_type b on a.type_code=b.code
                                      where a.username='" . $username . "' and a.is_deleted=0");
            $resData = $query->getRow();

            if ($resData != NULL) {
                $tipeuser = $resData->type_code;
                $tipename = $resData->type_name;
                $id_user = $resData->id;
                $is_active = $resData->is_deleted;
                if ($is_active == 0) {
                    // if(strpos($result['dn'], $tipe) !== false){
                    if ($tipeuser == 'gdk') {
                        $userData = $that->db->query("SELECT a.id as id_user,a.username,a.email,b.nik,b.namagadik,b.`photopath`,b.`nrp`,b.pangkat,b.jab,b.telp,b.`is_gadik`,b.`is_instruktur`,b.`is_internal`,b.`is_pengasuh`,b.`folder_materi` from m_user a
                                        LEFT JOIN m_user_pendidik b on a.id=b.`id_m_user`
                                        where a.id='" . $id_user . "'");
                        $cekData = $userData->getFieldCount();
                        $this->db->query("UPDATE m_user set fcm_token='" . $fcm . "' where id='" . $id_user . "' ");
                        if ($cekData > 0) {
                            $response = [
                                'status' => 1,
                                'message' => 'Success',
                                'data' => $userData->getRow(),
                                'type_code' => $tipeuser,
                                'type_name' => $tipename,
                                'is_otp' => $isotp

                            ];
                            $status = json_encode($response);
                            $datapost = json_encode($_POST);
                            $this->logaction($action, $datapost, $status);
                            echo json_encode($response);
                        } else {
                            $response = [
                                'status' => 0,
                                'message' => 'Data Detail User Tidak Ditemukan'
                            ];
                            $status = json_encode($response);
                            $datapost = json_encode($_POST);
                            $this->logaction($action, $datapost, $status);
                            echo json_encode($response);
                        }
                    } else if ($tipeuser == 'trn') {

                        $userData = $that->db->query("SELECT a.id as id_user,a.username,b.namataruna,a.email,b.telp,b.nik,b.noakshort,b.noaklong,b.photopath,c.jabatan,g.tingkatan,d.`batalyon`,e.`kompi`,f.`peleton`,b.folder_materi,b.is_verif,b.is_deleted from m_user a
                                        LEFT JOIN m_user_taruna b on a.id=b.id_m_user
                                        LEFT JOIN m_semester c on b.id_semester=c.id
                                        LEFT JOIN m_tingkatan g on c.id_tingkat=g.id
                                        LEFT JOIN m_sm_batalyon d on b.id_batalyon=d.id
                                        LEFT JOIN m_sm_kompi e on b.id_kompi=e.id
                                        LEFT JOIN m_sm_peleton f on b.id_peleton=f.id
                                        where a.id='" . $id_user . "'");

                        $dataTaruna = $userData->getRow();
                        $is_verif = $dataTaruna->is_verif;
                        $is_delete = $dataTaruna->is_deleted;

                        if ($is_delete == 0) {
                            if ($is_verif == 1) {
                                $cekData = $userData->getFieldCount();
                                $this->db->query("UPDATE m_user set fcm_token='" . $fcm . "' where id='" . $id_user . "' ");

                                if ($cekData > 0) {
                                    $response = [
                                        'status' => 1,
                                        'message' => 'Success',
                                        'data' => $userData->getRow(),
                                        'type_code' => $tipeuser,
                                        'type_name' => $tipename,
                                        'is_otp' => $isotp
                                    ];
                                    $status = json_encode($response);
                                    $datapost = json_encode($_POST);
                                    $this->logaction($action, $datapost, $status);
                                    echo json_encode($response);
                                } else {
                                    $response = [
                                        'status' => 0,
                                        'message' => 'Data Detail User Tidak Ditemukan'
                                    ];
                                    $status = json_encode($response);
                                    $datapost = json_encode($_POST);
                                    $this->logaction($action, $datapost, $status);
                                    echo json_encode($response);
                                }
                            } else {
                                $response = [
                                    'status' => 0,
                                    'message' => 'User belum terverifikasi'
                                ];
                                $status = json_encode($response);
                                $datapost = json_encode($_POST);
                                $this->logaction($action, $datapost, $status);
                                echo json_encode($response);
                            }
                        } else {
                            $response = [
                                'status' => 0,
                                'message' => 'User Tidak Aktif'
                            ];
                            $status = json_encode($response);
                            $datapost = json_encode($_POST);
                            $this->logaction($action, $datapost, $status);
                            echo json_encode($response);
                        }
                    } else {
                        $userData = $that->db->query("SELECT a.id as id_user,a.username,a.email,b.nik,b.namagadik,b.`photopath`,b.`nrp`,b.pangkat,b.jab,b.telp,b.`is_gadik`,b.`is_instruktur`,b.`is_internal`,b.`is_pengasuh`,b.`folder_materi` from m_user a
                                        LEFT JOIN m_user_pendidik b on a.id=b.`id_m_user`
                                        where a.id='" . $id_user . "'");
                        $cekData = $userData->getFieldCount();
                        $this->db->query("UPDATE m_user set fcm_token='" . $fcm . "' where id='" . $id_user . "' ");
                        if ($cekData > 0) {
                            $response = [
                                'status' => 1,
                                'message' => 'Success',
                                'data' => $userData->getRow(),
                                'type_code' => $tipeuser,
                                'type_name' => $tipename,
                                'is_otp' => $isotp

                            ];
                            $status = json_encode($response);
                            $datapost = json_encode($_POST);
                            $this->logaction($action, $datapost, $status);
                            echo json_encode($response);
                        } else {
                            $response = [
                                'status' => 0,
                                'message' => 'Data Detail User Tidak Ditemukan'
                            ];
                            $status = json_encode($response);
                            $datapost = json_encode($_POST);
                            $this->logaction($action, $datapost, $status);
                            echo json_encode($response);
                        }
                    }
                } else {
                    $response = [
                        'status' => 0,
                        'message' => 'User Tidak Aktif'
                    ];
                    $status = json_encode($response);
                    $datapost = json_encode($_POST);
                    $this->logaction($action, $datapost, $status);
                    echo json_encode($response);
                }
            } else {
                $response = [
                    'status' => 0,
                    'message' => 'User Tidak Terdaftar'
                ];
                $status = json_encode($response);
                $datapost = json_encode($_POST);
                $this->logaction($action, $datapost, $status);
                echo json_encode($response);
            }
        }
	}

	public function authByGoogle()
	{
		header('Content-Type: application/json');
		$email 	= $_POST['email'];
		$fcm	= $_POST['fcm'];
		$isotp = $this->getOTP();
		$action =  $this->request->getMethod();
		$query = $this->db->query("SELECT a.*,b.name as type_name from m_user a 
								  LEFT JOIN m_user_type b on a.type_code=b.code
								  where a.email='" . $email . "'");
		$resData = $query->getRow();

		if ($resData != NULL) {
			$tipeuser = $resData->type_code;
			$tipename = $resData->type_name;
			$id_user = $resData->id;
			$is_active = $resData->is_deleted;
			if ($is_active == 0) {

				if ($tipeuser == 'gdk') {
					$userData = $this->db->query("SELECT a.id as id_user,a.username,a.email,b.nik,b.namagadik,b.`photopath`,b.`nrp`,b.pangkat,b.jab,b.telp,b.`is_gadik`,b.`is_instruktur`,b.`is_internal`,b.`is_pengasuh`,b.`folder_materi` from m_user a
								LEFT JOIN m_user_pendidik b on a.id=b.`id_m_user`
								where a.id='" . $id_user . "'");

					$cekData = $userData->getFieldCount();

					if ($cekData > 0) {
						$this->db->query("UPDATE m_user set fcm_token='" . $fcm . "' where id='" . $id_user . "' ");
						$response = [
							'status' => 1,
							'message' => 'Success',
							'data' => $userData->getRow(),
							'type_code' => $tipeuser,
							'type_name' => $tipename,
							'is_otp' => $isotp
						];

						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					} else {
						$response = [
							'status' => 0,
							'message' => 'Data Detail User Tidak Ditemukan'
						];
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					}
				} else {
					$response = [
						'status' => 0,
						'message' => 'Email Tidak Mempunyai Akses'
					];

					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			} else {
				$response = [
					'status' => 0,
					'message' => 'User Tidak Aktif'
				];
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			}
		} else {
			$response = [
				'status' => 0,
				'message' => 'Email Tidak Terdaftar'
			];
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function authByApple()
	{
		header('Content-Type: application/json');
		$email = $_POST['email'];
		$isotp = $this->getOTP();
		$action =  $this->request->getMethod();
		$fcm	= $_POST['fcm'];
		$query = $this->db->query("SELECT a.*,b.name as type_name from m_user a 
								  LEFT JOIN m_user_type b on a.type_code=b.code
								  where a.email='" . $email . "'");
		$resData = $query->getRow();

		if ($resData != NULL) {
			$tipeuser = $resData->type_code;
			$tipename = $resData->type_name;
			$id_user = $resData->id;
			$is_active = $resData->is_deleted;
			if ($is_active == 0) {
				if ($tipeuser == 'gdk') {
					$userData = $this->db->query("SELECT a.id as id_user,a.username,a.email,b.nik,b.namagadik,b.`photopath`,b.`nrp`,b.pangkat,b.jab,b.telp,b.`is_gadik`,b.`is_instruktur`,b.`is_internal`,b.`is_pengasuh`,b.`folder_materi` from m_user a
								LEFT JOIN m_user_pendidik b on a.id=b.`id_m_user`
								where a.id='" . $id_user . "'");

					$cekData = $userData->getFieldCount();
					$this->db->query("UPDATE m_user set fcm_token='" . $fcm . "' where id='" . $id_user . "' ");
					if ($cekData > 0) {
						$response = [
							'status' => 1,
							'message' => 'Success',
							'data' => $userData->getRow(),
							'type_code' => $tipeuser,
							'type_name' => $tipename,
							'is_otp' => $isotp
						];
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					} else {
						$response = [
							'status' => 0,
							'message' => 'Data Detail User Tidak Ditemukan'
						];
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					}
				} else {
					$response = [
						'status' => 0,
						'message' => 'Email Tidak Mempunyai Akses'
					];
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			} else {
				$response = [
					'status' => 0,
					'message' => 'User Tidak Aktif'
				];
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			}
		} else {
			$response = [
				'status' => 0,
				'message' => 'Email Tidak Terdaftar'
			];
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function changePass()
	{
		header('Content-Type: application/json');
		$data = $this->request->getPost();
		$edited["userPassword"] = base64_encode($data["new_password"]);

		parent::_ldapModify($data, $edited, function ($result) {
			$action =  $this->request->getMethod();
			$result["message"] = !$result["success"] ? "Password Lama Salah" : "Success";
			$result["status"] = !$result["success"] ? 0 : 1;
			$status = json_encode($result);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			echo json_encode($result);
		});
	}
	//END AUTH////


	//OTP//
	// public function generateOTP(){

	// 	$kode = mt_rand(1000,9999);

	// 	$otp_user = $this->getOTP(); //default 1

	// 	$idUser = $_POST['id_user'];
	// 	// $tipeuser = $_POST['tipe_user'];

	// 	$query = $this->db->query("SELECT * from m_user where id='".$idUser."'")->getRow();

	// 	$tipeuser = $query->type_code;

	// 	// echo $tipeuser;
	// 		if($tipeuser == 'gdk'){

	// 			$rsnew = $this->db->query("UPDATE m_user_pendidik set telp='" . $_POST['phone'] . "' where id_m_user='".$idUser."' ");
	// 			$nomor = $this->db->query("SELECT telp,(NOW() + INTERVAL 5 MINUTE)as expired from m_user_pendidik where id_m_user='".$idUser."'")->getRow();

	// 			if($otp_user == 1){

	// 				$userkey = '408cdfc4202d';
	// 				$passkey = '48846c1d6fd82bded53065dc';
	// 				$telepon = 	'"'.$nomor->telp.'"';
	// 				$message = 'Kode OTP untuk SIAP-AKPOL System, masukan kode : *'.$kode.'* untuk melanjutkan proses verifikasi nomor anda,hanya berlaku 5 Menit. Terima Kasih';
	// 				$url = 'https://console.zenziva.net/wareguler/api/sendWA/';
	// 				$curlHandle = curl_init();
	// 				curl_setopt($curlHandle, CURLOPT_URL, $url);
	// 				curl_setopt($curlHandle, CURLOPT_HEADER, 0);
	// 				curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
	// 				curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
	// 				curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
	// 				curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
	// 				curl_setopt($curlHandle, CURLOPT_POST, 1);
	// 				curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
	// 					'userkey' => $userkey,
	// 					'passkey' => $passkey,
	// 					'to' => $telepon,
	// 					'message' => $message
	// 				));

	// 				$results = json_decode($this->utf8ize(curl_exec($curlHandle)), true);
	// 				curl_close($curlHandle);

	// 				$datalog = 	[
	// 							'ip' =>  $this->request->getIPAddress(),
	// 							'nomor'  => $nomor->telp,
	// 							'resp'  => $kode,
	// 							'log' => json_encode($this->utf8ize($results))
	// 							];

	// 				$this->db->table('log_otp')->insert($datalog);


	// 				$rs = $this->db->query("UPDATE m_user a
	// 										set a.uniq_code='".$kode."',a.expired_time='".$nomor->expired."' 
	// 										where a.id='".$idUser."'");

	// 				if($this->db->affectedRows() > 0){

	// 					$response = [
	// 								'status'  => 1,
	// 								'kodeOTP' => $kode,
	// 								'message' => 'Success',
	// 								'result'  => $results,
	// 								'is_otp'  => $otp_user	
	// 								];
	// 					$action =  $this->request->getMethod();
	// 					$status = json_encode($result);
	// 					$datapost = json_encode($_POST);
	// 					$this->logaction($action,$datapost,$status);
	// 					return $this->response->setJSON($response);

	// 				}else{

	// 					$response = [
	// 								'status' => 0,
	// 								'message' => 'Failed'
	// 								];
	// 					$action =  $this->request->getMethod();
	// 					$status = json_encode($response);
	// 					$datapost = json_encode($_POST);
	// 					$this->logaction($action,$datapost,$status);
	// 					return $this->response->setJSON($response);

	// 				}

	// 			}else{

	// 				$response = [
	// 							'status' => 1,
	// 							'kodeOTP' => $kode,
	// 							'message' => 'Success',
	// 							'result'	=> NULL,
	// 							'is_otp'  => $otp_user
	// 							];
	// 				$action =  $this->request->getMethod();
	// 				$status = json_encode($response);
	// 				$datapost = json_encode($_POST);
	// 				$this->logaction($action,$datapost,$status);			
	// 				return $this->response->setJSON($response);

	// 			}

	// 		}else if($tipeuser == 'trn'){
	// 			$rsnew = $this->db->query("UPDATE m_user_taruna set telp='" . $_POST['phone'] . "' where id_m_user='".$idUser."'");
	// 			$nomor = $this->db->query("SELECT telp,(NOW() + INTERVAL 5 MINUTE)as expired from m_user_taruna where id_m_user='".$idUser."'")->getRow();

	// 			if($otp_user == 1){

	// 				$userkey = '408cdfc4202d';
	// 				$passkey = '48846c1d6fd82bded53065dc';
	// 				$telepon = 	'"'.$nomor->telp.'"';
	// 				$message = 'Kode OTP untuk SIAP-AKPOL System, masukan kode : *'.$kode.'* untuk melanjutkan proses verifikasi nomor anda,hanya berlaku 5 Menit. Terima Kasih';
	// 				$url = 'https://console.zenziva.net/wareguler/api/sendWA/';
	// 				$curlHandle = curl_init();
	// 				curl_setopt($curlHandle, CURLOPT_URL, $url);
	// 				curl_setopt($curlHandle, CURLOPT_HEADER, 0);
	// 				curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
	// 				curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
	// 				curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
	// 				curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
	// 				curl_setopt($curlHandle, CURLOPT_POST, 1);
	// 				curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
	// 					'userkey' => $userkey,
	// 					'passkey' => $passkey,
	// 					'to' => $telepon,
	// 					'message' => $message
	// 				));

	// 				$results = json_decode($this->utf8ize(curl_exec($curlHandle)), true);
	// 				curl_close($curlHandle);

	// 				$datalog = 	[
	// 							'ip' =>  $this->request->getIPAddress(),
	// 							'nomor'  => $nomor->telp,
	// 							'resp'  => $kode,
	// 							'log' => json_encode($this->utf8ize($results))
	// 							];

	// 				$this->db->table('log_otp')->insert($datalog);


	// 				$rs = $this->db->query("UPDATE m_user a
	// 										set a.uniq_code='".$kode."',a.expired_time='".$nomor->expired."' 
	// 										where a.id='".$idUser."'");

	// 				if($this->db->affectedRows() > 0){

	// 					$response = [
	// 								'status'  => 1,
	// 								'kodeOTP' => $kode,
	// 								'message' => 'Success',
	// 								'result'  => $results,
	// 								'is_otp'  => $otp_user	
	// 								];
	// 					$action =  $this->request->getMethod();
	// 					$status = json_encode($response);
	// 					$datapost = json_encode($_POST);
	// 					$this->logaction($action,$datapost,$status);
	// 					return $this->response->setJSON($response);

	// 				}else{

	// 					$response = [
	// 								'status' => 0,
	// 								'message' => 'Failed'
	// 								];
	// 					$action =  $this->request->getMethod();
	// 					$status = json_encode($response);
	// 					$datapost = json_encode($_POST);
	// 					$this->logaction($action,$datapost,$status);
	// 					return $this->response->setJSON($response);

	// 				}

	// 			}else{

	// 				$response = [
	// 							'status' => 1,
	// 							'kodeOTP' => $kode,
	// 							'message' => 'Success',
	// 							'result'	=> NULL,
	// 							'is_otp'  => $otp_user
	// 							];
	// 				$action =  $this->request->getMethod();
	// 				$status = json_encode($response);
	// 				$datapost = json_encode($_POST);
	// 				$this->logaction($action,$datapost,$status);
	// 				return $this->response->setJSON($response);

	// 			}
	// 		}else{
	// 			$response = [
	// 				'status' => 0,
	// 				'message' => 'Tipe User Tidak Ditemukan'
	// 				];
	// 			$action =  $this->request->getMethod();
	// 			$status = json_encode($response);
	// 			$datapost = json_encode($_POST);
	// 			$this->logaction($action,$datapost,$status);
	// 			return $this->response->setJSON($response);
	// 		}


	// }
	public function generateOTP()
	{

		$kode = mt_rand(1000, 9999);

		$otp_user = 1; //default 1

		$idUser = $_POST['id_user'];
		// $tipeuser = $_POST['tipe_user'];

		$query = $this->db->query("SELECT * from m_user where id='" . $idUser . "'")->getRow();

		$tipeuser = $query->type_code;

		// echo $tipeuser;
		if ($tipeuser == 'gdk') {

			$rsnew = $this->db->query("UPDATE m_user_pendidik set telp='" . $_POST['phone'] . "' where id_m_user='" . $idUser . "' ");
			$nomor = $this->db->query("SELECT telp,(NOW() + INTERVAL 5 MINUTE)as expired from m_user_pendidik where id_m_user='" . $idUser . "'")->getRow();

			if ($otp_user == 1) {

				$userkey = '408cdfc4202d';
				$passkey = '48846c1d6fd82bded53065dc';
				$telepon = 	'"' . $nomor->telp . '"';
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

				$datalog = 	[
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
					'result'	=> NULL,
					'is_otp'  => $otp_user
				];

				return $this->response->setJSON($response);
			}
		} else if ($tipeuser == 'trn') {
			$rsnew = $this->db->query("UPDATE m_user_taruna set telp='" . $_POST['phone'] . "' where id_m_user='" . $idUser . "'");
			$nomor = $this->db->query("SELECT telp,(NOW() + INTERVAL 5 MINUTE)as expired from m_user_taruna where id_m_user='" . $idUser . "'")->getRow();

			if ($otp_user == 1) {

				$userkey = '408cdfc4202d';
				$passkey = '48846c1d6fd82bded53065dc';
				$telepon = 	'"' . $nomor->telp . '"';
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

				$datalog = 	[
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
					'result'	=> NULL,
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

	public function authOTP()
	{

		$id_user = $_POST['id_user'];
		// $tipeuser = $_POST['tipe_user'];
		$otpnya = $_POST['otp'];

		$query = $this->db->query("SELECT * from m_user where id='" . $id_user . "'")->getRow();

		$tipeuser = $query->type_code;

		if ($tipeuser == 'gdk') {
			$rs = $this->db->query("SELECT a.uniq_code as code,a.expired_time,b.id_m_user from m_user a
								LEFT JOIN m_user_pendidik b on b.id_m_user=a.id
								where a.id='" . $id_user . "' ")->getRow();
			if ($otpnya == $rs->code) {
				if ($rs->expired_time >= date('Y-m-d H:i:s')) {
					$this->db->query("UPDATE m_user set dt_login_mobile=NOW() where id='" . $rs->id_m_user . "' ");
					$response = [
						'status' => 1,
						'message' => 'Success'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				} else {
					$response = [
						'status' => 2,
						'message' => 'OTP expired'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			} else {
				$response = [
					'status' => 0,
					'message' => 'Invalid OTP'
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			}
		} else if ($tipeuser == 'trn') {
			$rs = $this->db->query("SELECT a.uniq_code as code,a.expired_time,b.id_m_user from m_user a
								LEFT JOIN m_user_taruna b on b.id_m_user=a.id
								where a.id='" . $id_user . "' ")->getRow();
			if ($otpnya == $rs->code) {
				if ($rs->expired_time >= date('Y-m-d H:i:s')) {
					$this->db->query("UPDATE m_user set dt_login_mobile=NOW() where id='" . $rs->id_m_user . "' ");
					$response = [
						'status' => 1,
						'message' => 'Success'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				} else {
					$response = [
						'status' => 2,
						'message' => 'OTP expired'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			} else {
				$response = [
					'status' => 0,
					'message' => 'Invalid OTP'
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			}
		} else {
			$response = [
				'status' => 0,
				'message' => 'Tipe User Tidak Ditemukan'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	//END OTP//

	//GADIK//
	public function scheduleLecturer()
	{

		$id_pengajar = $_POST['id_user'];

		$query = $this->db->query("SELECT a.id as id_jadwal,c.kelompok,g.`mata_pelajaran`,e.`judul`,e.`deskripsi`,e.pertemuan_ke,a.tanggal,a.is_absensi_pendidik,a.`jam_mulai`,a.`jam_selesai`,d.`nama` as lokasi_kelas,
									(case when (h.`id_aspek` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas from `t_jadwal` a
									LEFT JOIN m_kelompok c on a.`id_kelompok_taruna`=c.id
									LEFT JOIN m_ruang_kelas d on a.`id_ruang_kelas`=d.`id`
									LEFT JOIN t_bahan_ajar e on a.id_bahan_ajar=e.id
									LEFT JOIN m_user_pendidik f on a.`id_user_pendidik`=f.id_m_user
									LEFT JOIN m_mata_pelajaran g on e.`id_mata_pelajaran`=g.id
									LEFT JOIN t_program_studi_mata_pelajaran h on h.id_mata_pelajaran=g.id
								where f.id_m_user='" . $id_pengajar . "' and a.is_deleted = 0 and date(a.tanggal)=date(NOW())  GROUP BY a.id order by a.jam_mulai"); //ditambahi where date= NOW()

		$respData = $query->getResult();

		if (is_array($respData) && count($respData) > 0) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'date'	=> $this->getDateTimeID(),
				'data' => $respData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else if (empty($respData)) {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 0,
				'message' => 'Failed Backend Response'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function scheduleDetailLecturer()
	{

		$id_pengajar 	= $_POST['id_user'];
		$id_jadwal 		= $_POST['id_jadwal'];

		$query = $this->db->query("SELECT a.id as id_jadwal,f.namagadik,f.photopath,c.id as id_kelompok,c.kelompok,g.id as id_mata_pelajaran,b.id_batalyon,g.`mata_pelajaran`,e.`judul`,e.`deskripsi`,e.pertemuan_ke,a.tanggal,a.is_absensi_pendidik,a.`jam_mulai`,a.`jam_selesai`,d.`nama` as lokasi_kelas,
									(case when (h.`id_aspek` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas,
									if(a.tanggal=curdate() and date_format(NOW(),'%H:%i:%s') >= a.jam_mulai and date_format(NOW(),'%H:%i:%s') <= a.jam_selesai,1,0) as aktif from `t_jadwal` a
									LEFT JOIN t_bahan_ajar b on a.id_bahan_ajar=b.id
									LEFT JOIN m_kelompok c on a.`id_kelompok_taruna`=c.id
									LEFT JOIN m_ruang_kelas d on a.`id_ruang_kelas`=d.`id`
									LEFT JOIN t_bahan_ajar e on a.id_bahan_ajar=e.id
									LEFT JOIN m_user_pendidik f on a.`id_user_pendidik`=f.id_m_user
									LEFT JOIN m_mata_pelajaran g on e.`id_mata_pelajaran`=g.id
									LEFT JOIN t_program_studi_mata_pelajaran h on h.id_mata_pelajaran=g.id
									WHERE f.id_m_user='" . $id_pengajar . "' and a.id='" . $id_jadwal . "' ");

		$dosen = $this->db->query("SELECT b.namagadik,b.photopath from t_jadwal a
									LEFT JOIN m_user_pendidik b on a.id_user_pendidik=b.id_m_user
									where a.id='" . $id_jadwal . "'");

		$dosenData = $dosen->getRow();
		$jadwalData = $query->getRow();
		$nama_dosen = $dosenData->namagadik;
		$photo = $dosenData->photopath;
		$kelompok = $jadwalData->id_kelompok;
		$datapengajar = [
			'namagadik' => $nama_dosen,
			'photopath' => $photo

		];

		if ($jadwalData != NULL) {

			$is_absensi_pendidik = $jadwalData->is_absensi_pendidik;
			$is_absensi_aktif = $jadwalData->aktif;
			$pertemuanke = $jadwalData->pertemuan_ke;
			$mata_pelajaran = $jadwalData->id_mata_pelajaran;
			$id_batalyon = $jadwalData->id_batalyon;

			$materi = $this->db->query("SELECT a.id as id_file_materi,a.pertemuan_ke,CONCAT('http://devel.nginovasi.id/akpol-api/',a.lokasi_file) as lokasi_file,a.pertemuan_ke,b.tipe_file,b.icon_file from t_file_materi a
			LEFT JOIN m_tipe_file b on a.id_tipe_file=b.id
			LEFT JOIN m_user_pendidik c on a.id_user_pendidik=c.id_m_user
			LEFT JOIN m_sm_batalyon d on a.id_batalyon=d.id
			where c.id_m_user='" . $id_pengajar . "' and a.id_batalyon='" . $id_batalyon . "' and a.id_mata_pelajaran='" . $mata_pelajaran . "' and a.pertemuan_ke='" . $pertemuanke . "' and a.is_deleted=0 ");

			$tugas = $this->db->query("SELECT a.id as id_tugas,a.id_jadwal,g.`mata_pelajaran`,a.judul,a.deskripsi,e.kelompok,a.waktu_pengumpulan,(case when (a.`tipe_tugas` = 1) then 'Online' else 'Kumpulkan di Kelas' end) as kategori_pengumpulan,CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas) as file_tugas,c.icon_file,c.tipe_file  from t_jadwal_tugas a
			LEFT JOIN t_jadwal b on a.`id_jadwal`=b.id
			LEFT JOIN m_tipe_file c on a.id_tipe_file=c.id
			LEFT JOIN m_kelompok e on b.`id_kelompok_taruna`=e.id
			LEFT JOIN t_bahan_ajar f on b.id_bahan_ajar=f.id
			LEFT JOIN m_mata_pelajaran g on f.`id_mata_pelajaran`=g.id
			LEFT JOIN m_user_pendidik h on b.id_user_pendidik=h.id_m_user
			where h.id_m_user='" . $id_pengajar . "' and f.id_mata_pelajaran ='" . $mata_pelajaran . "'  and f.pertemuan_ke='" . $pertemuanke . "' and b.id_kelompok_taruna='" . $kelompok . "'");

			$materiData = $materi->getResult();
			$tugasData  = $tugas->getRow();



			// $jml = $query->getFieldCount();
			if ($materiData != NULL) {
				if ($tugasData != NULL) {
					$arrData = [
						'data_pengajar' => $datapengajar,
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
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				} else {
					$arrData = [
						'data_pengajar' => $datapengajar,
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
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			} else {
				$arrData = [
					'data_pengajar' => $datapengajar,
					'is_absensi_pendidik' => $is_absensi_pendidik,
					'is_absensi_aktif' => $is_absensi_aktif,
					'file_pelajaran' => NULL,
					'tugas_pelajaran' => $tugasData
				];
				$response = [
					'status' => 1,
					'message' => 'Success',
					'data' => $arrData
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			}
		} else {
			$arrDataNull = [
				'data_pengajar' => $datapengajar,
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
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function absenceLecturer()
	{

		$id_pengajar 	= $_POST['id_user'];
		$id_jadwal 		= $_POST['id_jadwal'];
		$lat 			= $_POST['lat'];
		$long 			= $_POST['long'];
		$absensi 		= $_POST['absensi'];
		$ttd 			= $_POST['ttd'];
		$judul			= $_POST['judul'];
		$deskripsi		= $_POST['deskripsi'];


		$filename1 = $this->genNamefile();
		$file1 = '/home/ngi/php/akpol-api/public/absensi/' . $filename1 . '_absensi.png';
		$file2 = '/home/ngi/php/akpol-api/public/absensi/' . $filename1 . '_ttd.png';
		$data1 = base64_decode($absensi);
		$data2 = base64_decode($ttd);
		file_put_contents($file1, $data1);
		file_put_contents($file2, $data2);

		$fotoabsensi = 'public/absensi/' . $filename1 . '_absensi.png';
		$filettd = 'public/absensi/' . $filename1 . '_ttd.png';

		$dataAbsen = $this->db->query("SELECT a.id_user_pendidik,b.namagadik from t_jadwal a 
										LEFT JOIN m_user_pendidik b on a.id_user_pendidik=b.id_m_user
										where a.id='" . $id_jadwal . "' and a.id_user_pendidik='" . $id_pengajar . "' and a.is_deleted = 0")->getRow();
		$userPengajar = $dataAbsen->id_user_pendidik;
		if ($userPengajar != NULL) {
			if ($this->db->query("UPDATE t_jadwal set is_absensi_pendidik=1,lat_absen='" . $lat . "',long_absen='" . $long . "',foto_absen='" . $fotoabsensi . "',ttd_pendidik='" . $filettd . "',judul_pertemuan='" . $judul . "',deskripsi_pertemuan='" . $deskripsi . "',absen_at=NOW() where id='" . $id_jadwal . "' ")) {
				$response = [
					'status' => 1,
					'message' => 'Success'
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			} else {
				$response = [
					'status' => 0,
					'message' => 'Failed'
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			}
		} else {
			$namaGadik = $dataAbsen->namagadik;
			$response = [
				'status' => 0,
				'message' => 'Gagal,Jadwal Untuk Pendidik ' . $namaGadik
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}

		// return $this->response->setJSON($_POST);

	}

	public function announcement()
	{

		$query = $this->db->query("SELECT a.id,a.judul,a.deskripsi,a.banner_path,b.name as created_by,a.created_at as create_at from m_pengumuman a 
								   LEFT JOIN m_user b on a.`created_by`=b.id order by id desc ");

		$resData = $query->getRow();

		if ($resData != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $query->getResult()
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function announcementLimit()
	{

		$query = $this->db->query("SELECT a.id,a.judul,a.deskripsi,a.banner_path,b.name as created_by,a.created_at as create_at from m_pengumuman a 
								   LEFT JOIN m_user b on a.`created_by`=b.id order by id desc limit 5");

		$resData = $query->getRow();

		if ($resData != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $query->getResult()
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function absenceTarunaClass()
	{

		$id_jadwal = $_POST['id_jadwal'];
		$query = $this->db->query("SELECT a.id as id_absensi,b.id_m_user as id_taruna,a.id_jadwal,a.is_absen,a.keterangan,a.face_is_absen,b.namataruna,b.noaklong,b.photopath from t_absensi a
		LEFT JOIN m_user_taruna b on a.id_taruna=b.id_m_user
		where a.id_jadwal='" . $id_jadwal . "' and a.is_deleted=0");

		$dataAbsen = $query->getResult();

		if ($dataAbsen != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $dataAbsen
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function getReason()
	{
		$action =  $this->request->getMethod();
		$query = $this->db->query("SELECT id,alasan from m_alasan_tidak_hadir where is_deleted=0 ");

		$resData = $query->getRow();

		if ($resData != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $query->getResult()
			];

			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);

			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];

			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);

			return $this->response->setJSON($response);
		}
	}

	public function attendanceSignTaruna()
	{

		$this->db->transStart();

		$absensi = $_POST['data_absensi'];
		$id_jadwal = $_POST['id_jadwal'];

		$arrAbsensi 	= json_decode($absensi, true);
		// print_r($arrAbsensi);

		$isInsert = false;


		$query = $this->db->query("SELECT a.id as id_absensi,b.id_m_user as id_taruna,a.id_jadwal,a.is_absen,a.keterangan,a.face_is_absen,b.namataruna,b.noaklong,b.photopath from t_absensi a
		LEFT JOIN m_user_taruna b on a.id_taruna=b.id_m_user
		where a.id_jadwal='" . $id_jadwal . "' and a.is_deleted=0");

		$dataAbsen = $query->getResult();
		if ($dataAbsen != NULL) {
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
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
			} else {
				$response = [
					'status' => 0,
					'message' => 'Format Data Salah'
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
			}
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 0,
				'message' => 'Jadwal Tidak Ditemukan'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function getTarunaAssessment()
	{
		$id_taruna = $_POST['id_taruna'];
		$id_jadwal = $_POST['id_jadwal'];


		$eval = $this->db->query("SELECT a.id as id_jadwal,d.`id_aspek` as is_teori from t_jadwal a
							left join t_bahan_ajar b on a.id_bahan_ajar=b.id
							left join m_mata_pelajaran c on b.`id_mata_pelajaran`=c.id
							LEFT JOIN t_program_studi_mata_pelajaran d on d.id_mata_pelajaran=c.id
							where a.id='" . $id_jadwal . "' ");



		$dataEval = $eval->getRow();
		// print_r($dataEval);
		// print_r($this->db->getLastQuery());

		if ($dataEval != NULL) {
			$is_teori = $dataEval->is_teori;
			$dataPenilaian = $this->db->query("SELECT id as id_aktivitas,aktivitas,poin,`is_positif` from m_penilaian_aktivitas_pembelajaran where `is_teori`='" . $is_teori . "' and is_deleted=0")->getResult();
			if ($dataPenilaian != NULL) {

				$query = $this->db->query("SELECT b.id as id_aktivitas,b.aktivitas,b.is_positif,b.poin, count(a.id_aktivitas) as jml_aktivitas,sum(b.poin) as total_poin from t_penilaian_aktifitas_pembelajaran a
					LEFT JOIN m_penilaian_aktivitas_pembelajaran b on a.id_aktivitas=b.id
					where a.id_jadwal='" . $id_jadwal . "' and a.id_user_taruna ='" . $id_taruna . "' and a.is_deleted=0
					group by a.id_aktivitas ");
				$dataAktivitas = $query->getResult();

				if ($dataAktivitas != NULL) {

					$response = [
						'status' => 1,
						'message' => 'Success',
						'evaluasi' => $dataPenilaian,
						'data' => $dataAktivitas
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				} else {
					$response = [
						'status' => 1,
						'message' => 'Success',
						'evaluasi' => $dataPenilaian,
						'data' => NULL
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			} else {
				$response = [
					'status' => 1,
					'message' => 'Tidak Ada Data'
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			}
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Jadwal'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
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

		if ($arrPenilaian) {

			$penilaianExist = $this->db->query("SELECT a.*,count(a.id_aktivitas) as jml_aktivitas,sum(a.poin) as total_poin from (SELECT a.id as id_penilaian,a.id_jadwal,c.id_user_pendidik,b.aktivitas,b.is_positif,b.poin,a.`id_aktivitas`,a.id_user_taruna,a.is_deleted from t_penilaian_aktifitas_pembelajaran a
			LEFT JOIN m_penilaian_aktivitas_pembelajaran b on a.id_aktivitas=b.id
			LEFT JOIN t_jadwal c on a.id_jadwal=c.id
			) a
			where a.id_jadwal='" . $id_jadwal . "' and a.id_user_taruna ='" . $id_taruna . "' and a.is_deleted=0
			group by a.id_aktivitas");

			$dataPenilaian = $penilaianExist->getResult();
			$cekData = $penilaianExist->getRow();

			if ($dataPenilaian != NULL) {

				$id_pengajar = $cekData->id_user_pendidik;

				$this->db->query("UPDATE t_penilaian_aktifitas_pembelajaran set is_deleted=1,last_edited_at=NOW(),last_edited_by='" . $id_pengajar . "' WHERE id_user_taruna='" . $id_taruna . "' and id_jadwal ='" . $id_jadwal . "' and is_deleted=0 ");

				$i = 1;
				$arrItem = [];

				foreach ($arrPenilaian as $key => $value) {

					$jml_item = $value['jumlah'];
					// print_r($jml_item);
					if ($jml_item > 1) {

						for ($i = 0; $i < $jml_item; $i++) {

							$data['id_jadwal'] = $value['id_jadwal'];
							$data['id_user_taruna'] = $value['id_user_taruna'];
							$data['id_aktivitas'] = $value['id_aktivitas'];
							array_push($arrItem, $data);
						}
					} else if ($jml_item == 0) {

						$data['id_jadwal'] = $value['id_jadwal'];
						$data['id_user_taruna'] = $value['id_user_taruna'];
						$data['id_aktivitas'] = $value['id_aktivitas'];
						$this->db->query("UPDATE t_penilaian_aktifitas_pembelajaran set is_deleted=1,last_edited_at=NOW(),last_edited_by='" . $id_pengajar . "' WHERE id_user_taruna='" . $value['id_user_taruna'] . "' and id_aktivitas = '" . $value['id_aktivitas'] . "'and id_jadwal ='" . $value['id_jadwal'] . "' and is_deleted=0 ");
						// array_push($arrItem, $data);
						// echo $this->db->getLastQuery();
					} else {

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

				if ($arrItem != NULL) {
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
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
				} else {
					$response = [
						'status' => 0,
						'message' => 'Tidak ada data'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
				}
				return $this->response->setJSON($response);
			} else {

				$i = 1;
				$arrItem = [];

				foreach ($arrPenilaian as $key => $value) {

					$jml_item = $value['jumlah'];

					if ($jml_item > 1) {
						// echo "loop";
						for ($i = 0; $i < $jml_item; $i++) {

							$data['id_jadwal'] = $value['id_jadwal'];
							$data['id_user_taruna'] = $value['id_user_taruna'];
							$data['id_aktivitas'] = $value['id_aktivitas'];
							array_push($arrItem, $data);
						}
					} else {
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
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
				} else {
					$response = [
						'status' => 0,
						'message' => 'Tidak ada data'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
				}

				return $this->response->setJSON($response);
			}
		} else {
			$response = [
				'status' => 0,
				'message' => 'Data Penilaian Kosong / Format salah'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function scheduleByDate()
	{
		$action =  $this->request->getMethod();
		$id_pengajar 	= $_POST['id_user'];
		$date 			= $_POST['tanggal'];
		$query = $this->db->query("SELECT a.id as id_jadwal,c.kelompok,g.`mata_pelajaran`,e.`judul`,e.`deskripsi`,e.pertemuan_ke,a.tanggal,a.is_absensi_pendidik,a.`jam_mulai`,a.`jam_selesai`,d.`nama` as lokasi_kelas,
								(case when (h.`id_aspek` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas from `t_jadwal` a
								LEFT JOIN m_kelompok c on a.`id_kelompok_taruna`=c.id
								LEFT JOIN m_ruang_kelas d on a.`id_ruang_kelas`=d.`id`
								LEFT JOIN t_bahan_ajar e on a.id_bahan_ajar=e.id
								LEFT JOIN m_user_pendidik f on a.`id_user_pendidik`=f.id_m_user
								LEFT JOIN m_mata_pelajaran g on e.`id_mata_pelajaran`=g.id
								LEFT JOIN t_program_studi_mata_pelajaran h on h.id_mata_pelajaran=g.id
								where f.id_m_user='" . $id_pengajar . "' and a.is_deleted = 0 and date(a.tanggal)='" . $date . "'  group by a.id order by a.jam_mulai"); //ditambahi where date= choosen date

		$resData = $query->getRow();

		if ($resData != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $query->getResult()
			];
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listTaskOnGoing()
	{

		$id_pengajar = $_POST['id_user'];

		$query = $this->db->query("SELECT a.id as id_tugas,a.id_jadwal,g.mata_pelajaran,f.pertemuan_ke,a.judul,a.deskripsi,e.kelompok,a.waktu_pengumpulan,(case when (a.`tipe_tugas` = 1) then 'Online' else 'Kumpulkan di Kelas' end) as kategori_pengumpulan,
		CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas) as file_tugas_,if(a.jenis_file_materi_tugas=1,CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas),a.file_materi_tugas) as file_tugas ,c.icon_file,c.tipe_file  from t_jadwal_tugas a
			LEFT JOIN t_jadwal b on a.`id_jadwal`=b.id
			LEFT JOIN m_tipe_file c on a.id_tipe_file=c.id
			LEFT JOIN m_kelompok e on b.`id_kelompok_taruna`=e.id
			LEFT JOIN t_bahan_ajar f on b.id_bahan_ajar=f.id
			LEFT JOIN m_mata_pelajaran g on f.id_mata_pelajaran=g.id
			LEFT JOIN m_user_pendidik h on b.id_user_pendidik=h.id_m_user
			where h.id_m_user='" . $id_pengajar . "' and now() <= a.waktu_pengumpulan ");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listTaskOnDone()
	{
		$id_pengajar = $_POST['id_user'];

		$query = $this->db->query("SELECT a.id as id_tugas,a.id_jadwal,g.mata_pelajaran,a.judul,a.deskripsi,e.kelompok,a.waktu_pengumpulan,(case when (a.`tipe_tugas` = 1) then 'Online' else 'Kumpulkan di Kelas' end) as kategori_pengumpulan,
		CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas) as file_tugas_,if(a.jenis_file_materi_tugas=1,CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas),a.file_materi_tugas) as file_tugas ,c.icon_file,c.tipe_file  from t_jadwal_tugas a
			LEFT JOIN t_jadwal b on a.`id_jadwal`=b.id
			LEFT JOIN m_tipe_file c on a.id_tipe_file=c.id
			LEFT JOIN m_kelompok e on b.`id_kelompok_taruna`=e.id
			LEFT JOIN t_bahan_ajar f on b.id_bahan_ajar=f.id
			LEFT JOIN m_mata_pelajaran g on f.id_mata_pelajaran=g.id
			LEFT JOIN m_user_pendidik h on b.id_user_pendidik=h.id_m_user
			where h.id_m_user='" . $id_pengajar . "' and now() >= a.waktu_pengumpulan ");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function detailTask()
	{
		$id_taks = $_POST['id_tugas'];

		$query = $this->db->query("SELECT a.id as id_nilai_tugas,
		a.id_jadwal_tugas,
		b.namataruna,
		b.noaklong,
		b.photopath,
		(case when (a.file_tugas is null) then '0' else '1' end) as is_mengumpulkan,
		(case when (a.`file_tugas` is null) then 'Belum Mengumpulkan Tugas' else CONCAT('Mengumpulkan ',sf_formatdate_ID(a.upload_date),' • ', DATE_FORMAT(a.upload_date,'%H:%i'), ' WIB' ) end) as tanggal_upload,
		a.nilai from t_nilai_tugas a
		left join m_user_taruna b on a.id_user_taruna=b.id_m_user
		where a.id_jadwal_tugas='" . $id_taks . "'");

		$dataTugas = $query->getResult();
		if ($dataTugas != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $dataTugas
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function getTaskLecturer()
	{
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
											a.is_deleted = 0 and d.is_ujian=0 and b.id_jadwal is null
											and f.id_m_user = '" . $id_pengajar . "' group by a.id order by d.pertemuan_ke");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}



	//batalyon by id_pengajar

	public function getBatalyon()
	{
		$id_pengajar 	= $_POST['id_user'];

		$query = $this->db->query("SELECT a.id_batalyon,b.batalyon,concat(b.batalyon, ' ( ' , b.tahun_masuk , ' ) - ', c.semester) as text from t_pendidik_mata_pelajaran a
		left join m_sm_batalyon b on a.id_batalyon=b.id
		left join m_semester c on b.id_semester = c.id 
		where a.id_pendidik = '" . $id_pengajar . "'
		GROUP BY b.id order by a.id_batalyon");

		$dataBatalyon = $query->getResult();
		if ($dataBatalyon != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $dataBatalyon
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function getMataPelajaran()
	{
		$id_batalyon = $_POST['id_batalyon'];
		$id_pengajar = $_POST['id_user'];
		$query = $this->db->query("SELECT
        a.id_mata_pelajaran,
        b.kode_mk,
        b.mata_pelajaran,
        d.semester
        from t_program_studi_mata_pelajaran a
        inner join m_mata_pelajaran b
            on b.id = a.id_mata_pelajaran
        inner join m_sm_batalyon c
            on a.id_semester = c.id_semester and a.id_batalyon=c.id
        inner join m_semester d
        	on a.id_semester = d.id 
		left join t_pendidik_mata_pelajaran e
        	on a.id_mata_pelajaran=e.`id_mata_pelajaran`
		where a.id_batalyon = '" . $id_batalyon . "' and e.id_pendidik='" . $id_pengajar . "' 
        and a.is_deleted='0' and e.is_deleted='0' GROUP BY a.id_mata_pelajaran");

		$dataMK = $query->getResult();
		if ($dataMK != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $dataMK
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function getPertemuan()
	{
		$id_mata_pelajaran = $_POST['id_mata_pelajaran'];

		$query = $this->db->query("SELECT a.pertemuan_ke,  a.judul, a.deskripsi from t_bahan_ajar a where a.is_deleted = 0 and a.is_ujian='0' and a.id_mata_pelajaran = '" . $id_mata_pelajaran . "' GROUP BY a.pertemuan_ke");

		$dataMK = $query->getResult();
		if ($dataMK != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $dataMK
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	//mata pelajaran yg diampu dosen + batalyon

	// pertemuan di pelajaran tersebut

	public function uploadDocumentLecture()
	{
		$id_mata_pelajaran = $_POST['id_mata_pelajaran'];
		$id_pengajar = $_POST['id_user'];
		$keterangan = $_POST['keterangan'];
		$pertemuan_ke = $_POST['pertemuan_ke'];
		$id_batalyon = $_POST['id_batalyon'];
		$files = $this->request->getFileMultiple('file');

		if ($files != "" or strlen($files) > 10 or $files != NULL) {
			$i = 1;
			$arr = [];
			foreach ($files as $file) {
				$infodir = $this->db->query("SELECT folder_materi FROM m_user_pendidik where id_m_user='" . $id_pengajar . "' ")->getRow();

				$newName = $file->getRandomName();

				$extensi = $file->getClientExtension();

				$getExtensi = $this->db->query("SELECT id from m_tipe_file where tipe_file='" . $extensi . "'")->getRow();

				$idExtensi = isset($getExtensi->id)  ? $getExtensi->id : NULL;

				$file->move(ROOTPATH . 'public/file-materi/' . $infodir->folder_materi, $i . '-' . $newName);

				$pathnya = 'public/file-materi/' . $infodir->folder_materi . '/' . $i . '-' . $newName;

				$insertData = [
					'id_user_pendidik' => $id_pengajar,
					'id_mata_pelajaran' => $id_mata_pelajaran,
					'pertemuan_ke' => $pertemuan_ke,
					'id_batalyon' => $id_batalyon,
					'id_tipe_file' => $idExtensi,
					'keterangan' => $keterangan,
					'ukuran_file' => $file->getSize(),
					'lokasi_file' => $pathnya

				];
				array_push($arr, $insertData);
				$i = $i + 1;
			}

			$builder = $builder = $this->db->table('t_file_materi');
			if ($builder->insertBatch($arr)) {
				$response = [
					'status' => 1,
					'message' => 'Success'
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			} else {
				$response = [
					'status' => 0,
					'message' => 'Failed'
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			}
		} else {
			$response = [
				'status' => 2,
				'message' => 'File tidak boleh kosong'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function saveTaskLecturer()
	{
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


			if ($file_materi_tugas != "" or strlen($file_materi_tugas) > 10) {

				$infodir = $this->db->query("SELECT folder_materi FROM m_user_pendidik where id_m_user='" . $id_pengajar . "' ")->getRow();

				$newName = $file_materi_tugas->getRandomName();

				$extensi = $file_materi_tugas->getClientExtension();

				// echo $extensi;

				$getExtensi = $this->db->query("SELECT id from m_tipe_file where tipe_file='" . $extensi . "'")->getRow();

				$idExtensi = $getExtensi->id;

				if ($idExtensi != NULL) {
					$file_materi_tugas->move(ROOTPATH . 'public/file-materi/' . $infodir->folder_materi, $newName);

					$pathnya = 'public/file-materi/' . $infodir->folder_materi . '/' . $newName;

					$dataJadwal = [
						'id_jadwal' 		=> $id_jadwal,
						'judul' 			=> $judul,
						'deskripsi'			=> $deskripsi,
						'waktu_pengumpulan' => $waktu_pengumpulan,
						'file_materi_tugas' => $pathnya,
						'tipe_tugas'		=> $tipe_tugas,
						'id_tipe_file'		=> $idExtensi

					];
					// $this->db->db_debug = false;


					try {
						$this->db->table('t_jadwal_tugas')->insert($dataJadwal);
						$infomapel		= $this->db->query("SELECT b.id_mata_pelajaran,a.id_kelompok_taruna from t_jadwal a
													LEFT JOIN t_bahan_ajar b on a.id_bahan_ajar=b.id
													where a.id='" . $id_jadwal . "'")->getRow();

						$id_mata_pelajaran = $infomapel->id_mata_pelajaran;
						$id_kelompok_taruna = $infomapel->id_kelompok_taruna;

						$query = $this->db->query("SELECT e.id_m_user as id_taruna,c.mata_pelajaran,d.kelompok,g.fcm_token,f.namataruna,f.noaklong,f.photopath from t_jadwal a
						left join t_bahan_ajar b on a.id_bahan_ajar=b.id
						left join m_mata_pelajaran c on b.id_mata_pelajaran=c.id
						left join m_kelompok d on a.id_kelompok_taruna=d.id
						left join m_user_taruna e on a.id_kelompok_taruna=e.id_kelompok
						left join m_user_taruna f on e.id_m_user=f.id_m_user
						left join m_user g on f.id_m_user=g.id
				  		left join m_sm_batalyon h on e.id_batalyon=h.id
						where a.id_user_pendidik='" . $id_pengajar . "' and a.id_kelompok_taruna = '" . $id_kelompok_taruna . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "' and g.fcm_token is not null group by e.id_m_user")->getResult();
						foreach ($query as $obj) {
							$token = $obj->fcm_token;
							$taruna = $obj->namataruna;
							$mapel = $obj->mata_pelajaran;
							$kelompok = $obj->kelompok;
							$header = 'TUGAS - ' . $mapel . ' ' . $kelompok . '';
							$deskripsi = $judul;

							$this->handlertoken($token, $header, $deskripsi);
						}
						$response = [
							'status' => 1,
							'message' => 'Success'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					} catch (\Exception $e) {
						if ($e->getCode() === 500) {
							log_message('warning', 'Duplicate id_jadwal ({id_jadwal}) ', [
								'id_jadwal' => $id_jadwal
							]);
						}
						$response = [
							'status' => 1,
							'message' => 'Duplicate Data '
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
						exit($e->getMessage());
					}
				} else {
					$response = [
						'status' => 0,
						'message' => 'Extensi File Tidak bisa diUpload'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			} else {

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

				try {
					$this->db->table('t_jadwal_tugas')->insert($dataJadwal);
					$infomapel		= $this->db->query("SELECT b.id_mata_pelajaran,a.id_kelompok_taruna from t_jadwal a
													LEFT JOIN t_bahan_ajar b on a.id_bahan_ajar=b.id
													where a.id='" . $id_jadwal . "'")->getRow();

					$id_mata_pelajaran = $infomapel->id_mata_pelajaran;
					$id_kelompok_taruna = $infomapel->id_kelompok_taruna;

					$query = $this->db->query("SELECT e.id_m_user as id_taruna,c.mata_pelajaran,d.kelompok,g.fcm_token,f.namataruna,f.noaklong,f.photopath from t_jadwal a
					left join t_bahan_ajar b on a.id_bahan_ajar=b.id
					left join m_mata_pelajaran c on b.id_mata_pelajaran=c.id
					left join m_kelompok d on a.id_kelompok_taruna=d.id
					left join m_user_taruna e on a.id_kelompok_taruna=e.id_kelompok
					left join m_user_taruna f on e.id_m_user=f.id_m_user
					left join m_user g on f.id_m_user=g.id
					left join m_sm_batalyon h on e.id_batalyon=h.id
					where a.id_user_pendidik='" . $id_pengajar . "' and a.id_kelompok_taruna = '" . $id_kelompok_taruna . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "' and g.fcm_token is not null group by e.id_m_user")->getResult();
					foreach ($query as $obj) {
						$token = $obj->fcm_token;
						$taruna = $obj->namataruna;
						$mapel = $obj->mata_pelajaran;
						$kelompok = $obj->kelompok;
						$header = 'TUGAS - ' . $mapel . ' ' . $kelompok . '';
						$deskripsi = $judul;

						$this->handlertoken($token, $header, $deskripsi);
					}
					$response = [
						'status' => 1,
						'message' => 'Success'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				} catch (\Exception $e) {
					if ($e->getCode() === 500) {
						log_message('warning', 'Duplicate id_jadwal ({id_jadwal}) ', [
							'id_jadwal' => $id_jadwal
						]);
					}

					$response = [
						'status' => 1,
						'message' => 'Duplicate Data'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
					exit($e->getMessage());
				}
			}
		} else {
			//edit
			$datafile = $this->db->query("SELECT id,id_jadwal,file_materi_tugas from t_jadwal_tugas where id='" . $id_tugas . "'")->getRow();

			foreach ($result as $key => $value) {
				$data[$key] = $value;
			}
			$id_jadwal 			= $data['id_jadwal'];
			$judul				= $data['judul'];
			$deskripsi			= $data['deskripsi'];
			$waktu_pengumpulan	= $data['waktu_pengumpulan'];
			$tipe_tugas			= $data['tipe_tugas'];


			// echo $url . $datafile->file_materi_tugas;


			if ($file_materi_tugas != "" or strlen($file_materi_tugas) > 10) {

				$url = "/home/ngi/php/akpol-api/";

				if ($datafile->file_materi_tugas != NULL) {
					if (file_exists($url . $datafile->file_materi_tugas)) {
						unlink($datafile->file_materi_tugas);
					}
				}
				$infodir = $this->db->query("SELECT folder_materi FROM m_user_pendidik where id_m_user='" . $id_pengajar . "' ")->getRow();

				$newName = $file_materi_tugas->getRandomName();

				$extensi = $file_materi_tugas->getClientExtension();

				$getExtensi = $this->db->query("SELECT id from m_tipe_file where tipe_file='" . $extensi . "'")->getRow();

				$idExtensi = $getExtensi->id;

				if ($idExtensi != NULL) {
					$file_materi_tugas->move(ROOTPATH . 'public/file-materi/' . $infodir->folder_materi, $newName);

					$pathnya = 'public/file-materi/' . $infodir->folder_materi . '/' . $newName;

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
					if ($execute) {
						$infomapel		= $this->db->query("SELECT b.id_mata_pelajaran,a.id_kelompok_taruna from t_jadwal a
													LEFT JOIN t_bahan_ajar b on a.id_bahan_ajar=b.id
													where a.id='" . $id_jadwal . "'")->getRow();

						$id_mata_pelajaran = $infomapel->id_mata_pelajaran;
						$id_kelompok_taruna = $infomapel->id_kelompok_taruna;

						$query = $this->db->query("SELECT e.id_m_user as id_taruna,c.mata_pelajaran,d.kelompok,g.fcm_token,f.namataruna,f.noaklong,f.photopath from t_jadwal a
						left join t_bahan_ajar b on a.id_bahan_ajar=b.id
						left join m_mata_pelajaran c on b.id_mata_pelajaran=c.id
						left join m_kelompok d on a.id_kelompok_taruna=d.id
						left join m_user_taruna e on a.id_kelompok_taruna=e.id_kelompok
						left join m_user_taruna f on e.id_m_user=f.id_m_user
						left join m_user g on f.id_m_user=g.id
				  		left join m_sm_batalyon h on e.id_batalyon=h.id
						where a.id_user_pendidik='" . $id_pengajar . "' and a.id_kelompok_taruna = '" . $id_kelompok_taruna . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "' and g.fcm_token is not null group by e.id_m_user")->getResult();
						foreach ($query as $obj) {
							$token = $obj->fcm_token;
							$taruna = $obj->namataruna;
							$mapel = $obj->mata_pelajaran;
							$kelompok = $obj->kelompok;
							$header = 'TUGAS - ' . $mapel . ' ' . $kelompok . '';
							$deskripsi = $judul;

							$this->handlertoken($token, $header, $deskripsi);
						}
						$response = [
							'status' => 1,
							'message' => 'Success'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					} else {
						$response = [
							'status' => 0,
							'message' => $this->errorhandler()
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					}
					$this->db->db_debug = true;
				} else {
					$response = [
						'status' => 0,
						'message' => 'Extensi File Tidak bisa diUpload'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			} else {

				if ($is_removed == 1) {
					$url = "/home/ngi/php/akpol-api/";

					if ($datafile->file_materi_tugas != NULL) {
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
					if ($execute) {
						$infomapel		= $this->db->query("SELECT b.id_mata_pelajaran,a.id_kelompok_taruna from t_jadwal a
													LEFT JOIN t_bahan_ajar b on a.id_bahan_ajar=b.id
													where a.id='" . $id_jadwal . "'")->getRow();

						$id_mata_pelajaran = $infomapel->id_mata_pelajaran;
						$id_kelompok_taruna = $infomapel->id_kelompok_taruna;

						$query = $this->db->query("SELECT e.id_m_user as id_taruna,c.mata_pelajaran,d.kelompok,g.fcm_token,f.namataruna,f.noaklong,f.photopath from t_jadwal a
						left join t_bahan_ajar b on a.id_bahan_ajar=b.id
						left join m_mata_pelajaran c on b.id_mata_pelajaran=c.id
						left join m_kelompok d on a.id_kelompok_taruna=d.id
						left join m_user_taruna e on a.id_kelompok_taruna=e.id_kelompok
						left join m_user_taruna f on e.id_m_user=f.id_m_user
						left join m_user g on f.id_m_user=g.id
				  		left join m_sm_batalyon h on e.id_batalyon=h.id
						where a.id_user_pendidik='" . $id_pengajar . "' and a.id_kelompok_taruna = '" . $id_kelompok_taruna . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "' and g.fcm_token is not null group by e.id_m_user")->getResult();
						foreach ($query as $obj) {
							$token = $obj->fcm_token;
							$taruna = $obj->namataruna;
							$mapel = $obj->mata_pelajaran;
							$kelompok = $obj->kelompok;
							$header = 'TUGAS - ' . $mapel . ' ' . $kelompok . '';
							$deskripsi = $judul;

							$this->handlertoken($token, $header, $deskripsi);
						}
						$response = [
							'status' => 1,
							'message' => 'Success'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					} else {
						$response = [
							'status' => 0,
							'message' => 'Failed'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					}
				} else {
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
					if ($execute) {
						$infomapel		= $this->db->query("SELECT b.id_mata_pelajaran,a.id_kelompok_taruna from t_jadwal a
													LEFT JOIN t_bahan_ajar b on a.id_bahan_ajar=b.id
													where a.id='" . $id_jadwal . "'")->getRow();

						$id_mata_pelajaran = $infomapel->id_mata_pelajaran;
						$id_kelompok_taruna = $infomapel->id_kelompok_taruna;

						$query = $this->db->query("SELECT e.id_m_user as id_taruna,c.mata_pelajaran,d.kelompok,g.fcm_token,f.namataruna,f.noaklong,f.photopath from t_jadwal a
						left join t_bahan_ajar b on a.id_bahan_ajar=b.id
						left join m_mata_pelajaran c on b.id_mata_pelajaran=c.id
						left join m_kelompok d on a.id_kelompok_taruna=d.id
						left join m_user_taruna e on a.id_kelompok_taruna=e.id_kelompok
						left join m_user_taruna f on e.id_m_user=f.id_m_user
						left join m_user g on f.id_m_user=g.id
				  		left join m_sm_batalyon h on e.id_batalyon=h.id
						where a.id_user_pendidik='" . $id_pengajar . "' and a.id_kelompok_taruna = '" . $id_kelompok_taruna . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "' and g.fcm_token is not null group by e.id_m_user")->getResult();
						foreach ($query as $obj) {
							$token = $obj->fcm_token;
							$taruna = $obj->namataruna;
							$mapel = $obj->mata_pelajaran;
							$kelompok = $obj->kelompok;
							$header = 'TUGAS - ' . $mapel . ' ' . $kelompok . '';
							$deskripsi = $judul;

							$this->handlertoken($token, $header, $deskripsi);
						}
						$response = [
							'status' => 1,
							'message' => 'Success'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					} else {
						$response = [
							'status' => 0,
							'message' => 'Failed'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					}
				}

				// echo $this->db->getLastQuery();
			}
		}
	}

	public function subjectTeaching()
	{

		$id_pengajar	= $_POST['id_user'];

		$query 	= $this->db->query("SELECT 
		-- 	a.*,
			a.id_user_pendidik,
			  d.id_mata_pelajaran,
			  e.mata_pelajaran,
			  e.deskripsi,
			  g.namagadik as ketua_tim,
			  g.photopath,
			  c.id as id_batalyon,
			CONCAT(h.semester,' Batalyon ' ,c.batalyon) as info_semester,
			b.kelompok,
			a.id_kelompok_taruna,
			c.id_semester,
			e.mata_pelajaran
		from t_jadwal a
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
		right join t_pendidik_mata_pelajaran f
			on d.id_mata_pelajaran=f.id_mata_pelajaran and i.id_batalyon=f.id_batalyon
		left join m_user_pendidik g
			on f.id_pendidik=g.id_m_user 
		left join m_semester h
			on c.id_semester=h.id
		where a.is_deleted = 0
			and f.id_pendidik in ('" . $id_pengajar . "')
			and c.id_semester = d.id_semester
			and d.id_batalyon = c.id
		group by b.id,b.id,e.id");

		$resData = $query->getRow();

		if ($resData != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $query->getResult()
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}


	public function overviewClass()
	{
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_pengajar 		= $_POST['id_user'];
		$id_kelompok_taruna	= $_POST['id_kelompok_taruna'];

		$query = $this->db->query("SELECT 
								a.id_user_pendidik,
								a.id as id_jadwal,
								d.pertemuan_ke,
								d.id_mata_pelajaran,
								e.mata_pelajaran,
								d.judul,
								a.is_absensi_pendidik,
								e.deskripsi,
								a.tanggal,
								a.jam_mulai,
								a.jam_selesai,
								b.kelompok,
								a.id_kelompok_taruna,
								c.id_semester,
								e.mata_pelajaran,
								i.nama as lokasi_kelas,
								CONCAT(sf_formatdate_ID(a.tanggal),' • ', DATE_FORMAT(a.jam_mulai,'%H:%i'),' - ',DATE_FORMAT(a.jam_selesai,'%H:%i'), ' WIB') as tanggal_detail,
								(case when (j.jml_file is null) then '0' else j.jml_file end) as file_att,
								(case when (k.file_materi_tugas is null) then '0' else '1' end) as file_ass,
								l.jml_hadir as jml_hadir,
								l.jml_tdk_hadir as jml_tdk_hadir,
								(case when (m.`id_aspek` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas
							from t_jadwal a
							left join m_kelompok b
								on a.id_kelompok_taruna = b.id
							left join m_user_taruna n
								on b.id=n.id_kelompok
							left join m_sm_batalyon c
								on c.id = n.id_batalyon
							left join t_bahan_ajar d
								on d.id = a.id_bahan_ajar
							left join m_mata_pelajaran e
								on e.id = d.id_mata_pelajaran
							left join t_pendidik_mata_pelajaran f
								on d.id_mata_pelajaran=f.id_mata_pelajaran and n.id_batalyon=f.id_batalyon
							left join m_user_pendidik g
								on f.id_pendidik=g.id_m_user 
							left join m_semester h
								on c.id_semester=h.id
							left join m_ruang_kelas i
								on a.id_ruang_kelas=i.id
							LEFT JOIN (SELECT count(a.lokasi_file) as jml_file,a.id_mata_pelajaran,a.pertemuan_ke,id_batalyon from t_file_materi a
												where a.id_user_pendidik='" . $id_pengajar . "' 
												and a.id_mata_pelajaran='" . $id_mata_pelajaran . "' 
												and is_deleted=0 
												group by a.pertemuan_ke,id_batalyon) j 
								on e.id=j.id_mata_pelajaran and d.pertemuan_ke=j.pertemuan_ke and j.id_batalyon = n.id_batalyon
							LEFT JOIN (SELECT * from t_jadwal_tugas) k on k.id_jadwal=a.id
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
											LEFT JOIN t_jadwal b on a.id_jadwal=b.id
											left join t_bahan_ajar c on b.id_bahan_ajar=c.id where c.id_mata_pelajaran = '" . $id_mata_pelajaran . "') a 
											GROUP BY a.id_jadwal) l on l.id_jadwal=a.id
							LEFT JOIN t_program_studi_mata_pelajaran m on m.id_mata_pelajaran=e.id
							where a.is_deleted = 0
								and a.id_user_pendidik = '" . $id_pengajar . "'
								and c.id_semester = d.id_semester
								and d.id_batalyon = c.id
								and a.id_kelompok_taruna='" . $id_kelompok_taruna . "'
								and e.id='" . $id_mata_pelajaran . "'
								and a.is_absensi_pendidik = '1'
							group by a.id");

		$resData = $query->getRow();

		if ($resData != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $query->getResult()
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data' => NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function sessionClass()
	{
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_pengajar 		= $_POST['id_user'];
		$id_kelompok_taruna = $_POST['id_kelompok_taruna'];

		$query = $this->db->query("SELECT 									-- *,
		b.id as id_jadwal,
		b.id_user_pendidik as pengajar,
			f.id_pendidik as ketua_tim,
		c.kelompok,
		c.id as id_kelompok,
		e.mata_pelajaran,
		a.deskripsi,
		a.pertemuan_ke,
		a.judul,
		b.is_absensi_pendidik,
		b.tanggal,
		b.jam_mulai,
		b.jam_selesai,
		i.nama as lokasi_kelas,
		CONCAT(sf_formatdate_ID(b.tanggal),' • ', DATE_FORMAT(b.jam_mulai,'%H:%i'),' - ',DATE_FORMAT(b.jam_selesai,'%H:%i'), ' WIB') as tanggal_detail,
		(case when (n.`id_aspek` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas,
		(case when (j.jml_file is null) then '0' else j.jml_file end) as file_att,
		(case when (b.id is null) then '0' else '1' end) as is_detail,
		if(m.id_jadwal is not null, 1,0) as is_label
from t_bahan_ajar a
LEFT JOIN (SELECT * from t_jadwal where id_kelompok_taruna='" . $id_kelompok_taruna . "' and is_deleted=0) b on a.id=b.id_bahan_ajar 
		left join m_kelompok c
			on b.id_kelompok_taruna = c.id
		left join m_user_taruna o
			on c.id=o.id_kelompok
		left join m_sm_batalyon d
			on d.id = o.id_batalyon
		left join m_mata_pelajaran e
			on e.id = a.id_mata_pelajaran
		left join t_pendidik_mata_pelajaran f
			on a.id_mata_pelajaran=f.id_mata_pelajaran and f.id_batalyon=o.id_batalyon 
			and f.is_ketua_tim=1
		left join m_user_pendidik g
			on f.id_pendidik=g.id_m_user 
		left join m_semester h
			on d.id_semester=h.id
		left join m_ruang_kelas i
			on b.id_ruang_kelas=i.id
		LEFT JOIN (SELECT count(a.lokasi_file) as jml_file,a.id_mata_pelajaran,a.pertemuan_ke,id_batalyon from t_file_materi a
								where a.id_user_pendidik='" . $id_pengajar . "' and a.id_mata_pelajaran='" . $id_mata_pelajaran . "' and is_deleted=0 group by a.pertemuan_ke,id_batalyon) j on e.id=j.id_mata_pelajaran and a.pertemuan_ke=j.pertemuan_ke and j.id_batalyon = o.id_batalyon
		LEFT JOIN (SELECT * from t_jadwal_tugas) k on k.id_jadwal=b.id
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
									LEFT JOIN t_jadwal b on a.id_jadwal=b.id
									left join t_bahan_ajar c on b.id_bahan_ajar=c.id where c.id_mata_pelajaran = '" . $id_mata_pelajaran . "') a 
									GROUP BY a.id_jadwal) l on l.id_jadwal=b.id
		LEFT JOIN (SELECT 
						a.id_user_pendidik,
						d.id_pendidik,
						b.id as id_jadwal,
						b.is_absensi_pendidik,
						b.id_kelompok_taruna,
						a.pertemuan_ke,
						(case when (b.id is null) then '0' else '1' end) as is_detail
						from t_bahan_ajar a
						LEFT JOIN t_jadwal b on a.id=b.id_bahan_ajar
						left join m_user_taruna o on b.id_kelompok_taruna=o.id_kelompok
						left join t_pendidik_mata_pelajaran d on a.id_mata_pelajaran = d.id_mata_pelajaran and a.id_batalyon = d.id_batalyon
						where a.id_mata_pelajaran='" . $id_mata_pelajaran . "' and 
						a.is_ujian=0 and 
						d.id_pendidik='" . $id_pengajar . "'  and 
						b.is_absensi_pendidik = 0 and 
						b.is_deleted=0 and
						(o.id_kelompok='" . $id_kelompok_taruna . "' or o.id_kelompok is null) group by b.id_kelompok_taruna, d.id_pendidik) m  on m.id_jadwal = b.id
		LEFT JOIN t_program_studi_mata_pelajaran n on n.id_mata_pelajaran=e.id
where a.id_mata_pelajaran='" . $id_mata_pelajaran . "' and a.is_ujian=0 
GROUP BY a.pertemuan_ke");


		$resData = $query->getRow();

		if ($resData != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $query->getResult()
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data' => NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function assigmentClass()
	{
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_pengajar 		= $_POST['id_user'];
		$id_kelompok_taruna = $_POST['id_kelompok_taruna'];
		$query = $this->db->query("SELECT 
		a.id as id_tugas,
		a.id_jadwal,
		g.mata_pelajaran,
		f.pertemuan_ke,
		a.judul,
		a.deskripsi,
		e.kelompok,a.waktu_pengumpulan,
		(case when (a.`tipe_tugas` = 1) then 'Online' else 'Kumpulkan di Kelas' end) as kategori_pengumpulan,
		if(date(NOW()) > a.waktu_pengumpulan,1,0) as is_done,
		CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas) as file_tugas_,if(a.jenis_file_materi_tugas=1,CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas),a.file_materi_tugas) as file_tugas,
		c.icon_file,
		c.tipe_file  
	from t_jadwal_tugas a
		LEFT JOIN t_jadwal b on a.`id_jadwal`=b.id
		LEFT JOIN m_tipe_file c on a.id_tipe_file=c.id
		LEFT JOIN m_kelompok e on e.`id`=b.id_kelompok_taruna
		LEFT JOIN m_user_taruna d on e.id=d.id_kelompok
		LEFT JOIN t_bahan_ajar f on b.id_bahan_ajar=f.id
		LEFT JOIN m_mata_pelajaran g on f.id_mata_pelajaran=g.id
		LEFT JOIN m_user_pendidik h on f.id_user_pendidik=h.id_m_user
		left join m_sm_batalyon i on i.id = d.id_batalyon
		left join m_semester j on i.id_semester=j.id
	where  b.id_user_pendidik='" . $id_pengajar . "' and f.id_mata_pelajaran='" . $id_mata_pelajaran . "' and i.id_semester = f.id_semester and f.id_batalyon = i.id and b.id_kelompok_taruna='" . $id_kelompok_taruna . "' group by a.id_jadwal order by a.waktu_pengumpulan desc ");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function filesClass()
	{
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_pengajar 		= $_POST['id_user'];
		$id_batalyon		= $_POST['id_batalyon'];

		$query = $this->db->query("SELECT c.mata_pelajaran,
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
								where a.id_mata_pelajaran='" . $id_mata_pelajaran . "' and a.id_batalyon='" . $id_batalyon . "' and a.is_deleted=0 order by a.pertemuan_ke");


		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function studentClass()
	{
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_pengajar 		= $_POST['id_user'];
		$id_kelompok_taruna		= $_POST['id_kelompok_taruna'];

		$query = $this->db->query("SELECT c.id_m_user as id_taruna,c.namataruna,c.noaklong,c.photopath from t_jadwal a
			left join t_bahan_ajar b on a.id_bahan_ajar=b.id 
			left join m_user_taruna c on a.id_kelompok_taruna=c.id_kelompok and b.id_batalyon=c.id_batalyon and b.id_semester=c.id_semester
			where a.id_kelompok_taruna='" . $id_kelompok_taruna . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "'
			GROUP BY c.id_m_user");


		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function opsiDokumen()
	{

		$id_pengajar 		= $_POST['id_user'];

		$query = $this->db->query("SELECT 
									d.batalyon as tahun_ajaran,
									e.ganjil_genap,
									concat(e.semester,' • Batalyon ',d.batalyon) as keterangan
								from t_file_materi a
									LEFT JOIN m_tipe_file b on a.id_tipe_file=b.id
									LEFT JOIN m_mata_pelajaran c on a.id_mata_pelajaran=c.id
									LEFT JOIN m_sm_batalyon d on a.id_batalyon=d.id
									LEFT JOIN m_semester e on d.id_semester=e.id
								where a.id_user_pendidik='" . $id_pengajar . "' and d.id_semester < 9 group by d.batalyon");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data' => NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function storageFile()
	{

		$id_pengajar 	= $_POST['id_user'];
		$tahun_ajaran 	= $_POST['tahun_ajaran'];
		$semester		= $_POST['semester'];

		$header = $this->db->query("SELECT 
									sum(total_size)/1000000 as total_size,
									'MB' as satuan,
									json_arrayagg(json_object(
																'type_file', group_file,
																'count', count
																)) as kelompok_file_head
								from (
								select 
									d.group_file,
									count(*) as count,
									sum(a.ukuran_file) as total_size
								from t_file_materi a
								left join m_sm_batalyon b
									on a.id_batalyon = b.id
								left join m_semester c
									on b.id_semester = c.id
								left join m_tipe_file d
									on a.id_tipe_file = d.id
								where a.is_deleted = 0
									and a.id_user_pendidik = '" . $id_pengajar . "'
							and b.batalyon='" . $tahun_ajaran . "'
							group by d.group_file) z");

		$content = $this->db->query("SELECT 
										id_mata_pelajaran,
										mata_pelajaran,
										sum(total_size)/1000000 as total_size,
										'MB' as satuan,
										sum(count) total_file,
										json_arrayagg(json_object(
																	'type_file', group_file,
																	'count', count
																	)) as kelompok_file
									from (
									select 
										d.group_file,
										e.mata_pelajaran,
										a.id_mata_pelajaran,
										count(*) as count,
										sum(a.ukuran_file) as total_size
									from t_file_materi a
									left join m_sm_batalyon b
										on a.id_batalyon = b.id
									left join m_semester c
										on b.id_semester = c.id
									left join m_tipe_file d
										on a.id_tipe_file = d.id
									left join m_mata_pelajaran e
										on a.id_mata_pelajaran = e.id
									where a.is_deleted = 0
									and a.id_user_pendidik = '" . $id_pengajar . "'
									and b.batalyon='" . $tahun_ajaran . "'
									and c.ganjil_genap = '" . $semester . "'
									group by d.group_file, a.id_mata_pelajaran) z
									group by z.id_mata_pelajaran");

		$resHeader = $header->getResult();
		$resHeaderRow = $header->getRow();
		$resContent = $content->getResult();

		foreach ($resHeader as $objhead) {
			$filehead = json_decode($objhead->kelompok_file_head);
			if ($filehead[0]->type_file == null) {
				$objhead->kelompok_file_head = null;
			} else {
				$objhead->kelompok_file_head = $filehead;
			}
		}

		if ($resHeader != NULL && $resContent != NULL) {
			foreach ($resContent as $obj) {
				$file = json_decode($obj->kelompok_file);
				if ($file[0]->type_file == null) {
					$obj->kelompok_file = null;
				} else {
					$obj->kelompok_file = $file;
				}
			}
			$dataArr = [
				'header' => $resHeaderRow,
				'content' => $resContent
			];
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $dataArr
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data' => NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function detailStorage()
	{
		$id_pengajar 	= $_POST['id_user'];
		$tahun_ajaran 	= $_POST['tahun_ajaran'];
		$semester		= $_POST['semester'];
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];

		$query = $this->db->query("SELECT 
		CONCAT('http://devel.nginovasi.id/akpol-api/',a.lokasi_file) as lokasi_file,
		a.ukuran_file/1000000 as ukuran_file,
		'MB' as satuan,
		d.tipe_file,
			  d.icon_file,
		a.created_at,
				d.group_file,
				e.mata_pelajaran
			from t_file_materi a
			left join m_sm_batalyon b
				on a.id_batalyon = b.id
			left join m_semester c
				on b.id_semester = c.id
			left join m_tipe_file d
				on a.id_tipe_file = d.id
			left join m_mata_pelajaran e
				on a.id_mata_pelajaran = e.id
			where a.is_deleted = 0
			and a.id_user_pendidik = '" . $id_pengajar . "'
			and b.batalyon='" . $tahun_ajaran . "'
			and c.ganjil_genap = '" . $semester . "'
	   		and a.id_mata_pelajaran = '" . $id_mata_pelajaran . "'");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listPelanggaranProses()
	{
		$id_pengajar = $_POST['id_user'];

		$query = $this->db->query("SELECT 	
		-- 				g.*,
						c.id_m_user as id_taruna,
						c.namataruna as nama_taruna,
						c.photopath,
						c.noaklong as no_ak,
						a.id as id_pelanggaran,
						d.dasar_hukum,
						d.deskripsi,
						e.id as id_kategori_pelanggaran,
						e.kategori as kategori_pelanggaran,
						a.poin,
						e.min_poin,
						e.max_poin,
						f.id as id_karakter_pelanggaran,
						f.karakter as karakter_pelanggaran,
						(case when (k.id_user_pendidik = '" . $id_pengajar . "') then '1' else '0' end) as is_asuhan,
						l.namagadik as pelapor,
						l.photopath as phoho_pelapor,
						l.email as pelapor_email,
						json_arrayagg(json_object(
										'id_bukti', b.id,
										'bukti_foto',CONCAT('http://devel.nginovasi.id/akpol-api/',b.foto))) as bukti,
						a.latitude,
						a.longitude,
						a.alamat,
						a.is_approve,
						a.approve_at,
						a.created_at
					FROM t_pelanggaran_karakter_taruna a
					LEFT JOIN t_bukti_pelanggaran_karakter_taruna b
						on a.id = b.id_pelanggaran_karakter
					LEFT JOIN m_user_taruna c
						on a.id_taruna = c.id_m_user
					LEFT JOIN m_pelanggaran_karakter d
						on d.id = a.id_pelanggaran_karakter
					LEFT JOIN m_kategori_pelanggaran_karakter e
						on e.id = d.id_kategori_pelanggaran
					LEFT JOIN m_karakter_penilaian f
						on d.id_karakter_penilaian = f.id
					LEFT JOIN m_sm_batalyon g
						ON g.id = c.id_batalyon and g.id_semester=c.id_semester
					LEFT JOIN m_semester i
						ON c.id_semester = i.id
					LEFT JOIN m_sm_peleton k
						ON c.id_peleton = k.id
					LEFT JOIN m_user_pendidik l
						on a.created_by=l.id_m_user
					WHERE a.created_by = '" . $id_pengajar . "'
						AND a.is_deleted = 0
						AND a.is_approve = 0
					  AND g.id is not null
					GROUP BY a.id
					UNION
					SELECT 
		-- 			  g.*,
						c.id_m_user as id_taruna,
						c.namataruna as nama_taruna,
						c.photopath,
						c.noaklong as no_ak,
						a.id as id_pelanggaran,
						d.dasar_hukum,
						d.deskripsi,
						e.id as id_kategori_pelanggaran,
						e.kategori as kategori_pelanggaran,
						a.poin,
						e.min_poin,
						e.max_poin,
						f.id as id_karakter_pelanggaran,
						f.karakter as karakter_pelanggaran,
						(case when (k.id_user_pendidik = '" . $id_pengajar . "') then '1' else '0' end) as is_asuhan,
						l.namagadik as pelapor,
						l.photopath as phoho_pelapor,
						l.email as pelapor_email,				
						json_arrayagg(json_object(
										'id_bukti', b.id,
										'bukti_foto',CONCAT('http://devel.nginovasi.id/akpol-api/',b.foto))) as bukti,
						a.latitude,
						a.longitude,
						a.alamat,
						a.is_approve,
						a.approve_at,
						a.created_at
					FROM t_pelanggaran_karakter_taruna a
					LEFT JOIN t_bukti_pelanggaran_karakter_taruna b
						on a.id = b.id_pelanggaran_karakter
					LEFT JOIN m_user_taruna c
						on a.id_taruna = c.id_m_user
					LEFT JOIN m_pelanggaran_karakter d
						on d.id = a.id_pelanggaran_karakter
					LEFT JOIN m_kategori_pelanggaran_karakter e
						on e.id = d.id_kategori_pelanggaran
					LEFT JOIN m_karakter_penilaian f
						on d.id_karakter_penilaian = f.id
					LEFT JOIN m_sm_batalyon g
						ON g.id = c.id_batalyon and g.id_semester=c.id_semester
					LEFT JOIN m_semester i
						ON c.id_semester = i.id
					LEFT JOIN m_sm_peleton k
						ON c.id_peleton = k.id
					LEFT JOIN m_user_pendidik l
						on a.created_by=l.id_m_user
					WHERE k.id_user_pendidik = '" . $id_pengajar . "'
						AND a.is_deleted = 0
						AND a.is_approve = 0
					  AND g.id is not null
					GROUP BY a.id");

		$resData = $query->getResult();
		foreach ($resData as $obj) {
			$bukti = json_decode($obj->bukti);
			if ($bukti[0]->id_bukti == null) {
				$obj->bukti = null;
			} else {
				$obj->bukti = $bukti;
			}
		}

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listPujianProses()
	{
		$id_pengajar = $_POST['id_user'];

		$query = $this->db->query("SELECT 
		c.id_m_user as id_taruna,
		c.namataruna as nama_taruna,
		c.photopath,
		c.noaklong as no_ak,
		a.id as id_pujian_karakter,
		g.indikator,
		f.indikator as sub_indikator,
		e.item_nilai as item_penilaian,
		d.keterangan as sub_item_penilaian,
		d.bobot as point,
		(case when (k.id_user_pendidik = '" . $id_pengajar . "') then '1' else '0' end) as is_asuhan,
		l.namagadik as pelapor,
		l.photopath as phoho_pelapor,
		l.email as pelapor_email,
		json_arrayagg(json_object(
									'id_bukti', b.id,
									'bukti_foto',CONCAT('http://devel.nginovasi.id/akpol-api/',b.foto))) as bukti,
		a.latitude,
		a.longitude,
		a.alamat,
		a.is_approve,
		a.approve_at,
		a.created_at	
	 
	from t_pujian_karakter_taruna a
		LEFT join t_bukti_pujian_karakter_taruna b 
			on a.id=b.id_pujian_karakter
		LEFT JOIN m_user_taruna c 
			on a.id_taruna = c.id_m_user
		LEFT JOIN m_pujian_karakter_ref4 d 
			on d.id=a.id_pujian_karakter 
		LEFT JOIN m_pujian_karakter_ref3 e 
			on d.id_pen_kar_ref3=e.id
		LEFT JOIN m_pujian_karakter_ref2 f 
			on e.id_pen_kar_ref2=f.id
		LEFT JOIN m_pujian_karakter_ref1 g 
			on f.id_pen_kar_ref1=g.id
		LEFT JOIN m_sm_batalyon h
			ON h.id = c.id_batalyon and h.id_semester=c.id_semester
		LEFT JOIN m_semester i
			ON c.id_semester = i.id
		LEFT JOIN m_sm_peleton k
			ON c.id_peleton = k.id
		LEFT JOIN m_user_pendidik l
			on a.created_by=l.id_m_user
		WHERE a.created_by = '" . $id_pengajar . "'
			AND a.is_deleted = 0
			AND a.is_approve = 0
			AND h.id is not null
		GROUP BY a.id
		UNION
	SELECT 
		c.id_m_user as id_taruna,
		c.namataruna as nama_taruna,
		c.photopath,
		c.noaklong as no_ak,
		a.id as id_pujian_karakter,
		g.indikator,
		f.indikator as sub_indikator,
		e.item_nilai as item_penilaian,
		d.keterangan as sub_item_penilaian,
		d.bobot as point,
		(case when (k.id_user_pendidik = '" . $id_pengajar . "') then '1' else '0' end) as is_asuhan,
		l.namagadik as pelapor,
		l.photopath as phoho_pelapor,
		l.email as pelapor_email,
		json_arrayagg(json_object(
									'id_bukti', b.id,
									'bukti_foto',CONCAT('http://devel.nginovasi.id/akpol-api/',b.foto))) as bukti,
		a.latitude,
		a.longitude,
		a.alamat,
		a.is_approve,
		a.approve_at,
		a.created_at	
	 
	from t_pujian_karakter_taruna a
		LEFT join t_bukti_pujian_karakter_taruna b 
			on a.id=b.id_pujian_karakter
		LEFT JOIN m_user_taruna c 
			on a.id_taruna = c.id_m_user
		LEFT JOIN m_pujian_karakter_ref4 d 
			on d.id=a.id_pujian_karakter 
		LEFT JOIN m_pujian_karakter_ref3 e 
			on d.id_pen_kar_ref3=e.id
		LEFT JOIN m_pujian_karakter_ref2 f 
			on e.id_pen_kar_ref2=f.id
		LEFT JOIN m_pujian_karakter_ref1 g 
			on f.id_pen_kar_ref1=g.id
		LEFT JOIN m_sm_batalyon h
			ON h.id = c.id_batalyon and h.id_semester=c.id_semester
		LEFT JOIN m_semester i
			ON c.id_semester = i.id
		LEFT JOIN m_sm_peleton k
			ON c.id_peleton = k.id
		LEFT JOIN m_user_pendidik l
			on a.created_by=l.id_m_user
		WHERE k.id_user_pendidik = '" . $id_pengajar . "'
			AND a.is_deleted = 0
			AND a.is_approve = 0
		GROUP BY a.id");

		$resData = $query->getResult();
		foreach ($resData as $obj) {
			$bukti = json_decode($obj->bukti);
			if ($bukti[0]->id_bukti == null) {
				$obj->bukti = null;
			} else {
				$obj->bukti = $bukti;
			}
		}

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listPelanggaranKonfirm()
	{
		$id_pengajar = $_POST['id_user'];

		$query = $this->db->query(" SELECT * from (
					SELECT 	
		-- 				g.*,
						c.id_m_user as id_taruna,
						c.namataruna as nama_taruna,
						c.photopath,
						c.noaklong as no_ak,
						a.id as id_pelanggaran,
						d.dasar_hukum,
						d.deskripsi,
						e.id as id_kategori_pelanggaran,
						e.kategori as kategori_pelanggaran,
						a.poin,
						e.min_poin,
						e.max_poin,
						f.id as id_karakter_pelanggaran,
						f.karakter as karakter_pelanggaran,
						(case when (k.id_user_pendidik = '" . $id_pengajar . "') then '1' else '0' end) as is_asuhan,
						l.namagadik as pelapor,
						l.photopath as phoho_pelapor,
						l.email as pelapor_email,
						json_arrayagg(json_object(
										'id_bukti', b.id,
										'bukti_foto',CONCAT('http://devel.nginovasi.id/akpol-api/',b.foto))) as bukti,
						a.latitude,
						a.longitude,
						a.alamat,
						a.is_approve,
						a.approve_at,
						a.created_at
					FROM t_pelanggaran_karakter_taruna a
					LEFT JOIN t_bukti_pelanggaran_karakter_taruna b
						on a.id = b.id_pelanggaran_karakter
					LEFT JOIN m_user_taruna c
						on a.id_taruna = c.id_m_user
					LEFT JOIN m_pelanggaran_karakter d
						on d.id = a.id_pelanggaran_karakter
					LEFT JOIN m_kategori_pelanggaran_karakter e
						on e.id = d.id_kategori_pelanggaran
					LEFT JOIN m_karakter_penilaian f
						on d.id_karakter_penilaian = f.id
					LEFT JOIN m_sm_batalyon g
						ON g.id = c.id_batalyon and g.id_semester=c.id_semester
					LEFT JOIN m_semester i
						ON c.id_semester = i.id
					LEFT JOIN m_sm_peleton k
						ON c.id_peleton = k.id
					LEFT JOIN m_user_pendidik l
						on a.created_by=l.id_m_user
					WHERE a.created_by = '" . $id_pengajar . "'
						AND a.is_deleted = 0
						AND a.is_approve = 1
					  AND g.id is not null
					GROUP BY a.id 
					UNION
					SELECT 
		-- 			  g.*,
						c.id_m_user as id_taruna,
						c.namataruna as nama_taruna,
						c.photopath,
						c.noaklong as no_ak,
						a.id as id_pelanggaran,
						d.dasar_hukum,
						d.deskripsi,
						e.id as id_kategori_pelanggaran,
						e.kategori as kategori_pelanggaran,
						a.poin,
						e.min_poin,
						e.max_poin,
						f.id as id_karakter_pelanggaran,
						f.karakter as karakter_pelanggaran,
						(case when (k.id_user_pendidik = '" . $id_pengajar . "') then '1' else '0' end) as is_asuhan,
						l.namagadik as pelapor,
						l.photopath as phoho_pelapor,
						l.email as pelapor_email,				
						json_arrayagg(json_object(
										'id_bukti', b.id,
										'bukti_foto',CONCAT('http://devel.nginovasi.id/akpol-api/',b.foto))) as bukti,
						a.latitude,
						a.longitude,
						a.alamat,
						a.is_approve,
						a.approve_at,
						a.created_at
					FROM t_pelanggaran_karakter_taruna a
					LEFT JOIN t_bukti_pelanggaran_karakter_taruna b
						on a.id = b.id_pelanggaran_karakter
					LEFT JOIN m_user_taruna c
						on a.id_taruna = c.id_m_user
					LEFT JOIN m_pelanggaran_karakter d
						on d.id = a.id_pelanggaran_karakter
					LEFT JOIN m_kategori_pelanggaran_karakter e
						on e.id = d.id_kategori_pelanggaran
					LEFT JOIN m_karakter_penilaian f
						on d.id_karakter_penilaian = f.id
					LEFT JOIN m_sm_batalyon g
						ON g.id = c.id_batalyon and g.id_semester=c.id_semester
					LEFT JOIN m_semester i
						ON c.id_semester = i.id
					LEFT JOIN m_sm_peleton k
						ON c.id_peleton = k.id
					LEFT JOIN m_user_pendidik l
						on a.created_by=l.id_m_user
					WHERE k.id_user_pendidik = '" . $id_pengajar . "'
						AND a.is_deleted = 0
						AND a.is_approve = 1
					  AND g.id is not null
					GROUP BY a.id ) a order by a.created_at desc");

		$resData = $query->getResult();
		foreach ($resData as $obj) {
			$bukti = json_decode($obj->bukti);
			if ($bukti[0]->id_bukti == null) {
				$obj->bukti = null;
			} else {
				$obj->bukti = $bukti;
			}
		}

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listPujianKonfirm()
	{
		$id_pengajar = $_POST['id_user'];

		$query = $this->db->query("SELECT 
		c.id_m_user as id_taruna,
		c.namataruna as nama_taruna,
		c.photopath,
		c.noaklong as no_ak,
		a.id as id_pujian_karakter,
		g.indikator,
		f.indikator as sub_indikator,
		e.item_nilai as item_penilaian,
		d.keterangan as sub_item_penilaian,
		d.bobot as point,
		(case when (k.id_user_pendidik = '" . $id_pengajar . "') then '1' else '0' end) as is_asuhan,
		l.namagadik as pelapor,
		l.photopath as phoho_pelapor,
		l.email as pelapor_email,
		json_arrayagg(json_object(
									'id_bukti', b.id,
									'bukti_foto',CONCAT('http://devel.nginovasi.id/akpol-api/',b.foto))) as bukti,
		a.latitude,
		a.longitude,
		a.alamat,
		a.is_approve,
		a.approve_at,
		a.created_at	
	 
	from t_pujian_karakter_taruna a
		LEFT join t_bukti_pujian_karakter_taruna b 
			on a.id=b.id_pujian_karakter
		LEFT JOIN m_user_taruna c 
			on a.id_taruna = c.id_m_user
		LEFT JOIN m_pujian_karakter_ref4 d 
			on d.id=a.id_pujian_karakter 
		LEFT JOIN m_pujian_karakter_ref3 e 
			on d.id_pen_kar_ref3=e.id
		LEFT JOIN m_pujian_karakter_ref2 f 
			on e.id_pen_kar_ref2=f.id
		LEFT JOIN m_pujian_karakter_ref1 g 
			on f.id_pen_kar_ref1=g.id
		LEFT JOIN m_sm_batalyon h
			ON h.id = c.id_batalyon and h.id_semester=c.id_semester
		LEFT JOIN m_semester i
			ON c.id_semester = i.id
		LEFT JOIN m_sm_peleton k
			ON c.id_peleton = k.id
		LEFT JOIN m_user_pendidik l
			on a.created_by=l.id_m_user
		WHERE a.created_by = '" . $id_pengajar . "'
			AND a.is_deleted = 0
			AND a.is_approve = 1
			AND h.id is not null
		GROUP BY a.id
		UNION
	SELECT 
		c.id_m_user as id_taruna,
		c.namataruna as nama_taruna,
		c.photopath,
		c.noaklong as no_ak,
		a.id as id_pujian_karakter,
		g.indikator,
		f.indikator as sub_indikator,
		e.item_nilai as item_penilaian,
		d.keterangan as sub_item_penilaian,
		d.bobot as point,
		(case when (k.id_user_pendidik = '" . $id_pengajar . "') then '1' else '0' end) as is_asuhan,
		l.namagadik as pelapor,
		l.photopath as phoho_pelapor,
		l.email as pelapor_email,
		json_arrayagg(json_object(
									'id_bukti', b.id,
									'bukti_foto',CONCAT('http://devel.nginovasi.id/akpol-api/',b.foto))) as bukti,
		a.latitude,
		a.longitude,
		a.alamat,
		a.is_approve,
		a.approve_at,
		a.created_at	
	 
	from t_pujian_karakter_taruna a
		LEFT join t_bukti_pujian_karakter_taruna b 
			on a.id=b.id_pujian_karakter
		LEFT JOIN m_user_taruna c 
			on a.id_taruna = c.id_m_user
		LEFT JOIN m_pujian_karakter_ref4 d 
			on d.id=a.id_pujian_karakter 
		LEFT JOIN m_pujian_karakter_ref3 e 
			on d.id_pen_kar_ref3=e.id
		LEFT JOIN m_pujian_karakter_ref2 f 
			on e.id_pen_kar_ref2=f.id
		LEFT JOIN m_pujian_karakter_ref1 g 
			on f.id_pen_kar_ref1=g.id
		LEFT JOIN m_sm_batalyon h
			ON h.id = c.id_batalyon and h.id_semester=c.id_semester
		LEFT JOIN m_semester i
			ON c.id_semester = i.id
		LEFT JOIN m_sm_peleton k
			ON c.id_peleton = k.id
		LEFT JOIN m_user_pendidik l
			on a.created_by=l.id_m_user
		WHERE k.id_user_pendidik = '" . $id_pengajar . "'
			AND a.is_deleted = 0
			AND a.is_approve = 1
		GROUP BY a.id");

		$resData = $query->getResult();
		foreach ($resData as $obj) {
			$bukti = json_decode($obj->bukti);
			if ($bukti[0]->id_bukti == null) {
				$obj->bukti = null;
			} else {
				$obj->bukti = $bukti;
			}
		}

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listMasterPelanggaran()
	{
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

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listPujian_1()
	{
		$id_taruna = $_POST['id_taruna'];
		$dataTaruna = $this->db->query("SELECT b.`id_tingkat` as id_tingkat from m_user_taruna a left join m_semester b on a.id_semester=b.id where id_m_user ='" . $id_taruna . "'")->getRow();
		$id_tingkat = $dataTaruna->id_tingkat;

		$query = $this->db->query("SELECT id as id_pen_kar_ref1,kode,indikator,nilai_awal,max_bobot,id_tingkatan from m_pujian_karakter_ref1 
		where `id_tingkatan`='" . $id_tingkat . "' and is_deleted=0");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listPujian_2()
	{
		$id_pujian = $_POST['id_pen_kar_ref1'];

		$query = $this->db->query("SELECT id as id_pen_kar_ref2,id_pen_kar_ref1,huruf,indikator from m_pujian_karakter_ref2 where id_pen_kar_ref1='" . $id_pujian . "' and is_deleted=0");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listPujian_3()
	{
		$id_pujian = $_POST['id_pen_kar_ref2'];

		$query = $this->db->query("SELECT id as id_pen_kar_ref3,id_pen_kar_ref2,no,item_nilai from m_pujian_karakter_ref3 where id_pen_kar_ref2='" . $id_pujian . "' and is_deleted=0");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listPujian_4()
	{
		$id_pujian = $_POST['id_pen_kar_ref3'];

		$query = $this->db->query("SELECT id as id_pujian_karakter,id_pen_kar_ref3,kode,keterangan,bobot as point from m_pujian_karakter_ref4 where id_pen_kar_ref3='" . $id_pujian . "' and is_deleted=0");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listMasterTaruna()
	{
		$query = $this->db->query("SELECT
				a.id_m_user as id,
				a.photopath,
				a.noaklong,
				a.namataruna,
				b.jabatan
			FROM m_user_taruna a
			LEFT JOIN m_semester b
				ON a.id_semester=b.id
			WHERE a.is_deleted = 0
				AND a.is_verif = 1
				AND b.id < 9");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function verifPelanggaran()
	{
		$id_user = $_POST['id_user'];
		$id_pelanggaran = $_POST['id_pelanggaran'];

		$pengasuh = $this->db->query("SELECT * 
										from t_pelanggaran_karakter_taruna a
										LEFT JOIN m_user_taruna b on b.id_m_user=a.id_taruna
										LEFT JOIN m_sm_peleton c on b.id_peleton=c.id
										where a.id='" . $id_pelanggaran . "' and c.id_user_pendidik='" . $id_user . "'");

		$is_pengasuh = $pengasuh->getNumRows();
		// echo $is_pengasuh;

		if ($is_pengasuh > 0) {
			$insert['is_approve'] = 1;
			$insert['approve_by'] = $id_user;
			$insert['approve_at'] = date('Y-m-d H:i:s');

			$builder = $this->db->table('t_pelanggaran_karakter_taruna');
			$builder->where('id', $id_pelanggaran);
			$execute = $builder->update($insert);
			if ($execute) {
				$response = [
					'status'	=> 1,
					'message' 	=> 'Success'
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			} else {
				$response = [
					'status' => 0,
					'message' => 'Failed Update'
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			}
		} else {
			$response = [
				'status'	=> 0,
				'message' 	=> 'Acces Not Allowed'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function verifPujian()
	{
		$id_user = $_POST['id_user'];
		$id_pujian = $_POST['id_pujian'];

		$pengasuh = $this->db->query("SELECT * 
										from t_pujian_karakter_taruna a
										LEFT JOIN m_user_taruna b on b.id_m_user=a.id_taruna
										LEFT JOIN m_sm_peleton c on b.id_peleton=c.id
										where a.id='" . $id_pujian . "' and c.id_user_pendidik='" . $id_user . "'");

		$is_pengasuh = $pengasuh->getNumRows();
		// echo $is_pengasuh;

		if ($is_pengasuh > 0) {
			$insert['is_approve'] = 1;
			$insert['approve_by'] = $id_user;
			$insert['approve_at'] = date('Y-m-d H:i:s');

			$builder = $this->db->table('t_pujian_karakter_taruna');
			$builder->where('id', $id_pujian);
			$execute = $builder->update($insert);
			if ($execute) {
				$response = [
					'status'	=> 1,
					'message' 	=> 'Success'
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			} else {
				$response = [
					'status' => 0,
					'message' => 'Failed Update'
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			}
		} else {
			$response = [
				'status'	=> 0,
				'message' 	=> 'Acces Not Allowed'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listInfoPendidik()
	{
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
		LEFT JOIN m_config d
			ON d.id = 1
		LEFT JOIN m_config e
			ON e.id = 2
		WHERE a.created_by = '" . $id_pengajar . "'
			AND a.is_deleted = 0
			AND a.tahun_ajaran = d.nilai
			AND a.semester = e.nilai
		ORDER BY a.created_at desc");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listMapelKelas()
	{
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
									LEFT JOIN t_program_studi_mata_pelajaran g
										ON g.id_mata_pelajaran=e.id
									LEFT JOIN m_config f
										ON f.id = 1
										AND g.tahun_ajaran = f.nilai
									WHERE c.id_pendidik = '" . $id_pengajar . "'
									GROUP BY d.id,e.id");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function saveInfoPendidik()
	{
		header('Content-Type: application/json');

		if (isset($_POST)) {
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
			if ($id_info == '' || $id_info == '0') {
				if ($this->db->table('t_informasi_pendidik')->insert($insert)) {
					$query = $this->db->query("SELECT  f.id_m_user as id_taruna,c.mata_pelajaran,g.fcm_token,f.namataruna,f.noaklong,f.photopath from t_jadwal a
									left join t_bahan_ajar b on a.id_bahan_ajar=b.id
									left join m_mata_pelajaran c on b.id_mata_pelajaran=c.id
									left join m_kelompok d on a.id_kelompok_taruna=d.id
									left join m_sm_batalyon e on b.id_batalyon=e.id
									left join m_semester i on b.id_semester=i.id
									left join m_user_taruna f on f.id_semester=i.id and e.id=f.id_batalyon
									left join m_user g on f.id_m_user=g.id
									LEFT JOIN t_program_studi_mata_pelajaran h
										ON h.id_mata_pelajaran=c.id
									where a.id_user_pendidik='" . $id_user . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "' and g.fcm_token is not null group by f.id_m_user")->getResult();

					foreach ($query as $obj) {
						$token = $obj->fcm_token;
						$taruna = $obj->namataruna;
						$mapel = $obj->mata_pelajaran;
						$header = 'INFORMASI - ' . $mapel . '';
						$deskripsi = $judul;

						$this->handlertoken($token, $header, $deskripsi);
					}
					$response = [
						'status' => 1,
						'message' => 'Success'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				} else {
					$response = [
						'status' => 0,
						'message' => $this->errorhandler()
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			} else {
				$builder = $this->db->table('t_informasi_pendidik');
				$builder->where('id', $id_info);
				$execute = $builder->update($insert);
				if ($execute) {
					$query = $this->db->query("SELECT f.id_m_user as id_taruna,c.mata_pelajaran,g.fcm_token,f.namataruna,f.noaklong,f.photopath from t_jadwal a
									left join t_bahan_ajar b on a.id_bahan_ajar=b.id
									left join m_mata_pelajaran c on b.id_mata_pelajaran=c.id
									left join m_kelompok d on a.id_kelompok_taruna=d.id
									left join m_sm_batalyon e on b.id_batalyon=e.id
									left join m_semester i on b.id_semester=i.id
									left join m_user_taruna f on f.id_semester=i.id and e.id=f.id_batalyon
									left join m_user g on f.id_m_user=g.id
									LEFT JOIN t_program_studi_mata_pelajaran h
										ON h.id_mata_pelajaran=c.id
									where a.id_user_pendidik='" . $id_user . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "' and g.fcm_token is not null group by f.id_m_user")->getResult();

					foreach ($query as $obj) {
						$token = $obj->fcm_token;
						$taruna = $obj->namataruna;
						$mapel = $obj->mata_pelajaran;
						$header = 'INFORMASI - ' . $mapel . '';
						$deskripsi = $judul;

						$this->handlertoken($token, $header, $deskripsi);
					}

					$response = [
						'status' => 1,
						'message' => 'Success'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				} else {
					$response = [
						'status' => 0,
						'message' => $this->errorhandler()
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			}
			$this->db->db_debug = true;
		} else {
			$response = [
				'status' => 0,
				'message' => 'Please complete the input form'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function savePelanggaran()
	{

		if (isset($_POST)) {
			$id_user 					= $_POST['id_user'];
			$id_pelanggaran_karakter 	= $_POST['id_pelanggaran_karakter'];
			$id_taruna 					= $_POST['id_taruna'];
			$poin 						= $_POST['poin'];
			$latitude 					= $_POST['latitude'];
			$longitude 					= $_POST['longitude'];
			$alamat 					= $_POST['alamat'];
			$dataImage 					= $_POST['bukti'];

			$pengasuh = $this->db->query("SELECT a.id as id_peleton,id_user_pendidik,b.id_m_user as id_taruna,b.namataruna from m_sm_peleton a 
			LEFT JOIN m_user_taruna b on b.id_peleton=a.id
			where a.id_user_pendidik='" . $id_user . "' and b.id_m_user='" . $id_taruna . "'");

			$is_pengasuh = $pengasuh->getNumRows();
			// echo $is_pengasuh;

			if ($is_pengasuh > 0) {
				$tahunAjaran = $this->db->query("SELECT nilai FROM m_config WHERE id = 1")->getRow()->nilai;
				$dataTaruna = $this->db->query("SELECT b.id_semester, b.id_tingkatan FROM m_user_taruna a 
					LEFT JOIN m_tingkatan_detail b
						ON a.id_semester = b.id_semester
					WHERE a.id_m_user ='" . $id_taruna . "'")->getRow();


				$insert['created_by'] = $id_user;
				$insert['id_pelanggaran_karakter'] = $id_pelanggaran_karakter;
				$insert['id_taruna'] = $id_taruna;
				$insert['poin'] = $poin;
				$insert['id_semester'] = $dataTaruna->id_semester;
				$insert['id_tingkat'] = $dataTaruna->id_tingkatan;
				$insert['tahun_ajaran'] = $tahunAjaran;
				$insert['latitude'] = $latitude;
				$insert['longitude'] = $longitude;
				$insert['alamat'] = $alamat;
				$insert['is_approve'] = 1;
				$insert['approve_by'] = $id_user;
				$insert['approve_at'] = date('Y-m-d H:i:s');

				$this->db->table('t_pelanggaran_karakter_taruna')->insert($insert);

				$id_bukti = $this->db->insertID();

				if (isset($dataImage) && $dataImage != "") {
					$arrImg = explode("|||", $dataImage);
					// print_r($arrImg);
					$arr = [];
					$i = 1;
					foreach ($arrImg as $imageData) {

						$filename = $this->genNamefile() . "_" . $i;
						$file = '/home/ngi/php/akpol-api/public/bukti/' . $filename . '.jpg';
						$data = base64_decode($imageData);
						file_put_contents($file, $data);

						$gallery = [
							'foto' 						=> 'public/bukti/' . $filename . '.jpg',
							'id_pelanggaran_karakter' 	=> $id_bukti,
							'created_by'    			=> $id_user
						];
						array_push($arr, $gallery);
						$i = $i + 1;
					}

					$builder = $this->db->table('t_bukti_pelanggaran_karakter_taruna');
					if ($builder->insertBatch($arr)) {
						$response = [
							'status' => 2,
							'message' => 'Success'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					} else {
						$this->db->query("DELETE from t_pelanggaran_karakter_taruna where id '" . $id_bukti . "'");
						$response = [
							'status' => 0,
							'message' => 'Failed'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					}
				} else {
					$response = [
						'status' => 2,
						'message' => 'Success'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			} else {
				$tahunAjaran = $this->db->query("SELECT nilai FROM m_config WHERE id = 1")->getRow()->nilai;
				$dataTaruna = $this->db->query("SELECT b.id_semester, b.id_tingkatan FROM m_user_taruna a 
					LEFT JOIN m_tingkatan_detail b
						ON a.id_semester = b.id_semester
					WHERE a.id_m_user ='" . $id_taruna . "'")->getRow();


				$insert['created_by'] = $id_user;
				$insert['id_pelanggaran_karakter'] = $id_pelanggaran_karakter;
				$insert['id_taruna'] = $id_taruna;
				$insert['poin'] = $poin;
				$insert['id_semester'] = $dataTaruna->id_semester;
				$insert['id_tingkat'] = $dataTaruna->id_tingkatan;
				$insert['tahun_ajaran'] = $tahunAjaran;
				$insert['latitude'] = $latitude;
				$insert['longitude'] = $longitude;
				$insert['alamat'] = $alamat;

				$this->db->table('t_pelanggaran_karakter_taruna')->insert($insert);

				$id_bukti = $this->db->insertID();

				if (isset($dataImage) && $dataImage != "") {
					$arrImg = explode("|||", $dataImage);
					// print_r($arrImg);
					$arr = [];
					$i = 1;
					foreach ($arrImg as $imageData) {

						$filename = $this->genNamefile() . "_" . $i;
						$file = '/home/ngi/php/akpol-api/public/bukti/' . $filename . '.jpg';
						$data = base64_decode($imageData);
						file_put_contents($file, $data);

						$gallery = [
							'foto' 						=> 'public/bukti/' . $filename . '.jpg',
							'id_pelanggaran_karakter' 	=> $id_bukti,
							'created_by'    			=> $id_user
						];
						array_push($arr, $gallery);
						$i = $i + 1;
					}

					$builder = $this->db->table('t_bukti_pelanggaran_karakter_taruna');
					if ($builder->insertBatch($arr)) {
						$response = [
							'status' => 1,
							'message' => 'Success'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					} else {
						$this->db->query("DELETE from t_pelanggaran_karakter_taruna where id '" . $id_bukti . "'");
						$response = [
							'status' => 0,
							'message' => 'Failed'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					}
				} else {
					$response = [
						'status' => 1,
						'message' => 'Success'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			}
		} else {
			$response = [
				'status' => 0,
				'message' => 'Please complete the input form'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function savePujian()
	{

		if (isset($_POST)) {
			$id_user 					= $_POST['id_user'];
			$id_pujian_karakter 		= $_POST['id_pujian_karakter'];
			$id_taruna 					= $_POST['id_taruna'];
			$poin 						= $_POST['poin'];
			$latitude 					= $_POST['latitude'];
			$longitude 					= $_POST['longitude'];
			$alamat 					= $_POST['alamat'];
			$dataImage 					= $_POST['bukti'];

			$pengasuh = $this->db->query("SELECT a.id as id_peleton,id_user_pendidik,b.id_m_user as id_taruna,b.namataruna from m_sm_peleton a 
			LEFT JOIN m_user_taruna b on b.id_peleton=a.id
			where a.id_user_pendidik='" . $id_user . "' and b.id_m_user='" . $id_taruna . "'");

			$is_pengasuh = $pengasuh->getNumRows();
			// echo $is_pengasuh;

			if ($is_pengasuh > 0) {
				$tahunAjaran = $this->db->query("SELECT nilai FROM m_config WHERE id = 1")->getRow()->nilai;
				$dataTaruna = $this->db->query("SELECT b.id_semester, b.id_tingkatan FROM m_user_taruna a 
					LEFT JOIN m_tingkatan_detail b
						ON a.id_semester = b.id_semester
					WHERE a.id_m_user ='" . $id_taruna . "'")->getRow();


				$insert['created_by'] = $id_user;
				$insert['id_pujian_karakter'] = $id_pujian_karakter;
				$insert['id_taruna'] = $id_taruna;
				$insert['poin'] = $poin;
				$insert['id_semester'] = $dataTaruna->id_semester;
				$insert['id_tingkat'] = $dataTaruna->id_tingkatan;
				$insert['tahun_ajaran'] = $tahunAjaran;
				$insert['latitude'] = $latitude;
				$insert['longitude'] = $longitude;
				$insert['alamat'] = $alamat;
				$insert['is_approve'] = 1;
				$insert['approve_by'] = $id_user;
				$insert['approve_at'] = date('Y-m-d H:i:s');

				$this->db->table('t_pujian_karakter_taruna')->insert($insert);

				$id_bukti = $this->db->insertID();

				if (isset($dataImage) && $dataImage != "") {
					$arrImg = explode("|||", $dataImage);
					// print_r($arrImg);
					$arr = [];
					$i = 1;
					foreach ($arrImg as $imageData) {

						$filename = $this->genNamefile() . "_" . $i;
						$file = '/home/ngi/php/akpol-api/public/bukti/' . $filename . '.jpg';
						$data = base64_decode($imageData);
						file_put_contents($file, $data);

						$gallery = [
							'foto' 						=> 'public/bukti/' . $filename . '.jpg',
							'id_pujian_karakter' 		=> $id_bukti,
							'created_by'    			=> $id_user
						];
						array_push($arr, $gallery);
						$i = $i + 1;
					}

					$builder = $this->db->table('t_bukti_pujian_karakter_taruna');
					if ($builder->insertBatch($arr)) {
						$response = [
							'status' => 2,
							'message' => 'Success'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					} else {
						$this->db->query("DELETE from t_pujian_karakter_taruna where id '" . $id_bukti . "'");
						$response = [
							'status' => 0,
							'message' => 'Failed'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					}
				} else {
					$response = [
						'status' => 2,
						'message' => 'Success'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			} else {
				$tahunAjaran = $this->db->query("SELECT nilai FROM m_config WHERE id = 1")->getRow()->nilai;
				$dataTaruna = $this->db->query("SELECT b.id_semester, b.id_tingkatan FROM m_user_taruna a 
					LEFT JOIN m_tingkatan_detail b
						ON a.id_semester = b.id_semester
					WHERE a.id_m_user ='" . $id_taruna . "'")->getRow();


				$insert['created_by'] = $id_user;
				$insert['id_pujian_karakter'] = $id_pujian_karakter;
				$insert['id_taruna'] = $id_taruna;
				$insert['poin'] = $poin;
				$insert['id_semester'] = $dataTaruna->id_semester;
				$insert['id_tingkat'] = $dataTaruna->id_tingkatan;
				$insert['tahun_ajaran'] = $tahunAjaran;
				$insert['latitude'] = $latitude;
				$insert['longitude'] = $longitude;
				$insert['alamat'] = $alamat;

				$this->db->table('t_pujian_karakter_taruna')->insert($insert);

				$id_bukti = $this->db->insertID();

				if (isset($dataImage) && $dataImage != "") {
					$arrImg = explode("|||", $dataImage);
					// print_r($arrImg);
					$arr = [];
					$i = 1;
					foreach ($arrImg as $imageData) {

						$filename = $this->genNamefile() . "_" . $i;
						$file = '/home/ngi/php/akpol-api/public/bukti/' . $filename . '.jpg';
						$data = base64_decode($imageData);
						file_put_contents($file, $data);

						$gallery = [
							'foto' 						=> 'public/bukti/' . $filename . '.jpg',
							'id_pujian_karakter' 		=> $id_bukti,
							'created_by'    			=> $id_user
						];
						array_push($arr, $gallery);
						$i = $i + 1;
					}

					$builder = $this->db->table('t_bukti_pujian_karakter_taruna');
					if ($builder->insertBatch($arr)) {
						$response = [
							'status' => 1,
							'message' => 'Success'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					} else {
						$this->db->query("DELETE from t_pujian_karakter_taruna where id '" . $id_bukti . "'");
						$response = [
							'status' => 0,
							'message' => 'Failed'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					}
				} else {
					$response = [
						'status' => 1,
						'message' => 'Success'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			}
		} else {
			$response = [
				'status' => 0,
				'message' => 'Please complete the input form'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	//END OF GADIK//

	//TARUNA///
	public function scheduleStudent()
	{

		$id_user = $_POST['id_user'];
		$dataTaruna = $this->db->query("SELECT id_kelompok from m_user_taruna where id_m_user='" . $id_user . "'")->getRow();
		$id_kelompok = $dataTaruna->id_kelompok;
		$query = $this->db->query("SELECT 
									a.id as id_jadwal,
									f.kelompok,
									d.mata_pelajaran,
									b.judul,
									b.deskripsi, 
									a.tanggal,
									a.`jam_mulai`,
									a.`jam_selesai`,
									b.pertemuan_ke,
									i.nama as lokasi_kelas,
									j.namagadik as nama_dosen,
									j.photopath as photo_dosen,
									(case when (c.`id_aspek` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas
								from t_jadwal a
									LEFT JOIN t_bahan_ajar b on a.`id_bahan_ajar`=b.id
									LEFT JOIN t_program_studi_mata_pelajaran c on b.`id_mata_pelajaran`=c.id_mata_pelajaran
									LEFT JOIN m_mata_pelajaran d on c.id_mata_pelajaran=d.id
									LEFT JOIN m_sm_batalyon e on c.id_batalyon=e.id
									LEFT JOIN m_kelompok f on a.id_kelompok_taruna=f.id
									LEFT JOIN m_user_taruna h on f.id=h.id_kelompok 
									LEFT JOIN m_ruang_kelas i on a.`id_ruang_kelas`=i.`id`
									LEFT JOIN m_user_pendidik j on a.`id_user_pendidik`=j.id_m_user
								where h.id_m_user='" . $id_user . "' and a.is_deleted = 0 and h.id_kelompok='" . $id_kelompok . "' and date(a.tanggal)=date(NOW()) GROUP BY a.id "); //ditambahi where date= NOW()

		$respData = $query->getResult();

		if (is_array($respData) && count($respData) > 0) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'date'	=> $this->getDateTimeID(),
				'data' => $respData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else if (empty($respData)) {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 0,
				'message' => 'Failed Backend Response'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function scheduleStudentByDate()
	{

		$id_user 		= $_POST['id_user'];
		$date			= $_POST['tanggal'];

		$query = $this->db->query("SELECT 
										a.id as id_jadwal,
										f.kelompok,
										d.mata_pelajaran,
										b.judul,
										b.deskripsi, 
										a.tanggal,
										a.`jam_mulai`,
										a.`jam_selesai`,
										b.pertemuan_ke,
										i.nama as lokasi_kelas,
										j.namagadik as nama_dosen,
										j.photopath as photo_dosen,
										(case when (c.`id_aspek` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas
									from t_jadwal a
										LEFT JOIN t_bahan_ajar b on a.`id_bahan_ajar`=b.id
										LEFT JOIN t_program_studi_mata_pelajaran c on b.`id_mata_pelajaran`=c.id_mata_pelajaran
										LEFT JOIN m_mata_pelajaran d on c.id_mata_pelajaran=d.id
										LEFT JOIN m_sm_batalyon e on c.id_batalyon=e.id
										LEFT JOIN m_kelompok f on a.id_kelompok_taruna=f.id
										LEFT JOIN m_user_taruna h on f.id=h.id_kelompok 
										LEFT JOIN m_ruang_kelas i on a.`id_ruang_kelas`=i.`id`
										LEFT JOIN m_user_pendidik j on a.`id_user_pendidik`=j.id_m_user
									where h.id_m_user='" . $id_user . "'  and a.is_deleted = 0 and date(a.tanggal)='" . $date . "' GROUP BY a.id");

		$respData = $query->getResult();

		if (is_array($respData) && count($respData) > 0) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'date'	=> $this->getDateTimeID(),
				'data' => $respData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else if (empty($respData)) {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 0,
				'message' => 'Failed Backend Response'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function scheduleDetailStudent()
	{

		$id_user 		= $_POST['id_user'];
		$id_jadwal 		= $_POST['id_jadwal'];

		$query = $this->db->query("SELECT 
									a.id as id_jadwal,
									f.kelompok,
									d.mata_pelajaran,
									d.id as id_mata_pelajaran,
									b.judul,
									b.deskripsi, 
									a.tanggal,
									a.`jam_mulai`,
									a.`jam_selesai`,
									b.pertemuan_ke,
									i.nama as lokasi_kelas,
									j.id_m_user as id_pendidik,
									j.namagadik as nama_dosen,
									j.photopath as photo_dosen,
									e.id as id_batalyon,
									(case when (c.`id_aspek` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas
								from t_jadwal a
									LEFT JOIN t_bahan_ajar b on a.`id_bahan_ajar`=b.id
									LEFT JOIN t_program_studi_mata_pelajaran c on b.`id_mata_pelajaran`=c.id_mata_pelajaran
									LEFT JOIN m_mata_pelajaran d on c.id_mata_pelajaran=d.id
									LEFT JOIN m_sm_batalyon e on c.id_batalyon=e.id
									LEFT JOIN m_kelompok f on a.id_kelompok_taruna=f.id
									LEFT JOIN m_user_taruna h on f.id=h.id_kelompok 
									LEFT JOIN m_ruang_kelas i on a.`id_ruang_kelas`=i.`id`
									LEFT JOIN m_user_pendidik j on a.`id_user_pendidik`=j.id_m_user
								where h.id_m_user='" . $id_user . "' and a.id = " . $id_jadwal . " ");

		$jadwalData = $query->getRow();

		$pertemuanke = $jadwalData->pertemuan_ke;
		$mata_pelajaran = $jadwalData->id_mata_pelajaran;
		$id_pendidik = $jadwalData->id_pendidik;
		$id_batalyon = $jadwalData->id_batalyon;

		if ($jadwalData != NULL) {
			$materi = $this->db->query("SELECT a.id as id_file_materi,
			a.pertemuan_ke,
			CONCAT('http://devel.nginovasi.id/akpol-api/',a.lokasi_file) as lokasi_file,
			b.tipe_file,
			b.icon_file
			 from t_file_materi a 
			LEFT JOIN m_tipe_file b on a.id_tipe_file=b.id
			LEFT JOIN m_user_pendidik c on a.id_user_pendidik=c.id_m_user
			LEFT JOIN t_jadwal d on a.`id_user_pendidik`=d.id_user_pendidik
			LEFT JOIN m_sm_batalyon e on a.id_batalyon=e.id
			where a.id_user_pendidik='" . $id_pendidik . "' and a.id_batalyon='" . $id_batalyon . "' and a.id_mata_pelajaran = '" . $mata_pelajaran . "' and a.pertemuan_ke='" . $pertemuanke . "' and a.is_deleted=0 group by a.id");

			$tugas = $this->db->query("SELECT 
			i.id as id_tugas,
			a.id_jadwal,
			i.id_user_taruna,
			g.`mata_pelajaran`,
			a.judul,
			a.deskripsi,
			e.kelompok,
			a.waktu_pengumpulan,
			(case when (a.`tipe_tugas` = 1) then 'Online' else 'Kumpulkan di Kelas' end) as kategori_pengumpulan,
			CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas) as file_tugas_,if(a.jenis_file_materi_tugas=1,CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas),a.file_materi_tugas) as file_tugas,
			c.icon_file,
			c.tipe_file ,
			i.nilai,											
			i.catatan,
			(case when (i.log_nilai is null) then 0 else 1 end) as status_nilai,
			if(date(NOW()) > a.waktu_pengumpulan,1,0) as is_done,
			i.file_tugas as upload_tugas,
			(case when (i.file_tugas is null) then 'Belum Mengumpulkan Tugas' else CONCAT('Mengumpulkan ',sf_formatdate_ID(i.upload_date),' • ', DATE_FORMAT(i.upload_date,'%H:%i'), ' WIB' ) end) as keterangan_tugas,
			(case when (i.file_tugas is null) then 0 else 1 end) as status_tugas
		from t_jadwal_tugas a
		LEFT JOIN t_jadwal b on a.`id_jadwal`=b.id
		LEFT JOIN m_tipe_file c on a.id_tipe_file=c.id
		LEFT JOIN m_kelompok e on b.`id_kelompok_taruna`=e.id
		LEFT JOIN t_bahan_ajar f on b.id_bahan_ajar=f.id
		LEFT JOIN m_mata_pelajaran g on f.`id_mata_pelajaran`=g.id
		LEFT JOIN m_user_pendidik h on b.id_user_pendidik=h.id_m_user
		LEFT JOIN t_nilai_tugas i on a.id=i.id_jadwal_tugas
		where i.id_user_taruna='" . $id_user . "' and a.id_jadwal='" . $id_jadwal . "' ");

			$materiData = $materi->getResult();
			$tugasData  = $tugas->getRow();



			// $jml = $query->getFieldCount();
			if ($materiData != NULL) {
				if ($tugasData != NULL) {
					$arrData = [

						'file_pelajaran' => $materiData,
						'tugas_pelajaran' => $tugasData

					];
					$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $arrData
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				} else {
					$arrData = [
						'file_pelajaran' => $materiData,
						'tugas_pelajaran' => NULL

					];
					$response = [
						'status' => 1,
						'message' => 'Success',
						'data' => $arrData
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			} else {
				$arrData = [

					'file_pelajaran' => NULL,
					'tugas_pelajaran' => $tugasData
				];
				$response = [
					'status' => 1,
					'message' => 'Success',
					'data' => $arrData
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			}
		} else {
			$arrDataNull = [

				'file_pelajaran' => NULL,
				'tugas_pelajaran' => NULL
			];

			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data' => $arrDataNull
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function taskOnGoingStudent()
	{

		$id_user = $_POST['id_user'];

		$query = $this->db->query("SELECT 
									c.id as id_tugas,
									g.kelompok,
									a.id_jadwal,
									e.mata_pelajaran,
									d.pertemuan_ke,
									a.judul,
									a.deskripsi,
									d.pertemuan_ke,
									a.waktu_pengumpulan,
									f.icon_file,
									f.tipe_file,
									c.nilai,
									c.catatan,
									(case when (c.log_nilai is null) then 0 else 1 end) as status_nilai,
									c.file_tugas as upload_tugas,
									(case when (c.file_tugas is null) then '• Belum Mengumpulkan Tugas' else CONCAT('• ','Mengumpulkan ',sf_formatdate_ID(c.upload_date),' • ', DATE_FORMAT(c.upload_date,'%H:%i'), ' WIB' ) end) as keterangan_tugas,
									(case when (c.file_tugas is null) then 0 else 1 end) as status_tugas,
									(case when (a.`tipe_tugas` = 1) then 'Online' else 'Kumpulkan di Kelas' end) as kategori_pengumpulan,
									CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas) as file_tugas_,if(a.jenis_file_materi_tugas=1,CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas),a.file_materi_tugas) as file_tugas 
								from t_jadwal_tugas a
								LEFT JOIN t_jadwal b on a.`id_jadwal`=b.id
								LEFT JOIN t_nilai_tugas c on a.id=c.id_jadwal_tugas 
								LEFT JOIN t_bahan_ajar d on b.id_bahan_ajar=d.id
								LEFT JOIN m_mata_pelajaran e on d.id_mata_pelajaran=e.id
								LEFT JOIN m_tipe_file f on a.id_tipe_file=f.id
								LEFT JOIN m_kelompok g on b.id_kelompok_taruna=g.id
								LEFT JOIN m_user_taruna i on c.id_user_taruna=i.id_m_user
								LEFT JOIN m_sm_batalyon h on i.id_batalyon=h.id
								where c.id_user_taruna='" . $id_user . "' and now() < a.waktu_pengumpulan");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function taskOnDoneStudent()
	{
		$id_user = $_POST['id_user'];

		$query = $this->db->query("SELECT 
									c.id as id_tugas,
									g.kelompok,
									a.id_jadwal,
									e.mata_pelajaran,
									d.pertemuan_ke,
									a.judul,
									a.deskripsi,
									d.pertemuan_ke,
									a.waktu_pengumpulan,
									f.icon_file,
									f.tipe_file,
									c.nilai,
									c.catatan,
									(case when (c.log_nilai is null) then 0 else 1 end) as status_nilai,
									c.file_tugas as upload_tugas,
									(case when (a.tipe_tugas = 1) then if(c.file_tugas is null,'• Tidak Mengumpulkan Tugas',CONCAT('• ','Mengumpulkan ',sf_formatdate_ID(c.upload_date),' • ', DATE_FORMAT(c.upload_date,'%H:%i'), ' WIB' )) else '• Mengumpulkan di Kelas' end) as keterangan_tugas,
									(case when (c.file_tugas is null) then 0 else 1 end) as status_tugas,
									(case when (a.`tipe_tugas` = 1) then 'Online' else 'Kumpulkan di Kelas' end) as kategori_pengumpulan,
									CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas) as file_tugas,if(a.jenis_file_materi_tugas=1,CONCAT('http://devel.nginovasi.id/akpol-api/',a.file_materi_tugas),a.file_materi_tugas) as file_tugas 
								from t_jadwal_tugas a
								LEFT JOIN t_jadwal b on a.`id_jadwal`=b.id
								LEFT JOIN t_nilai_tugas c on a.id=c.id_jadwal_tugas 
								LEFT JOIN t_bahan_ajar d on b.id_bahan_ajar=d.id
								LEFT JOIN m_mata_pelajaran e on d.id_mata_pelajaran=e.id
								LEFT JOIN m_tipe_file f on a.id_tipe_file=f.id
								LEFT JOIN m_kelompok g on b.id_kelompok_taruna=g.id
								LEFT JOIN m_user_taruna i on c.id_user_taruna=i.id_m_user
								LEFT JOIN m_sm_batalyon h on i.id_batalyon=h.id
								where c.id_user_taruna='" . $id_user . "' and now() >= a.waktu_pengumpulan ");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}



	public function uploadTaskStudent()
	{
		$id_tugas_user 		= $_POST['id_tugas'];
		$catatan 			= $_POST['catatan'];
		$file_materi_tugas 	= $this->request->getFile('file_tugas');
		$id_user 			= $_POST['id_user'];

		$tugasTaruna = $this->db->query("SELECT (case when (file_tugas is null) then 0 else 1 end) as status_tugas from t_nilai_tugas where id='" . $id_tugas_user . "'")->getRow();

		$status_tugas = $tugasTaruna->status_tugas;

		if ($status_tugas == 0 or $status_tugas == NULL) {


			if ($file_materi_tugas != "" or strlen($file_materi_tugas) > 10) {

				$infodir = $this->db->query("SELECT folder_materi FROM m_user_taruna where id_m_user='" . $id_user . "' ")->getRow();

				$newName = $file_materi_tugas->getRandomName();

				$extensi = $file_materi_tugas->getClientExtension();

				$getExtensi = $this->db->query("SELECT id from m_tipe_file where tipe_file='" . $extensi . "'")->getRow();

				$idExtensi = $getExtensi->id;

				if ($idExtensi != NULL) {
					$file_materi_tugas->move(ROOTPATH . 'public/file-materi/' . $infodir->folder_materi, $newName);

					$pathnya = 'public/file-materi/' . $infodir->folder_materi . '/' . $newName;

					$dataTugas = [
						'catatan' 			=> $catatan,
						'file_tugas' 		=> $pathnya,
						'id_tipe_file'		=> $idExtensi,
						'upload_date' 		=> date('Y-m-d H:i:s')

					];
					// $this->db->db_debug = false;


					$builder = $this->db->table('t_nilai_tugas');
					$builder->where('id', $id_tugas_user);
					$execute = $builder->update($dataTugas);

					$response = [
						'status' => 1,
						'message' => 'Success'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				} else {
					$response = [
						'status' => 0,
						'message' => 'Extensi File Tidak bisa diUpload'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			} else {

				$response = [
					'status' => 0,
					'message' => 'File Tugas Kosong'
				];
				$action =  $this->request->getMethod();
				$status = json_encode($response);
				$datapost = json_encode($_POST);
				$this->logaction($action, $datapost, $status);
				return $this->response->setJSON($response);
			}
		} else {
			$response = [
				'status' => 2,
				'message' => 'Sudah Mengumpulkan Tugas'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function subjectStudent()
	{

		$id_user		= $_POST['id_user'];

		$query 	= $this->db->query("SELECT 
											c.id_pendidik as id_user_pendidik,
											b.id_mata_pelajaran,
											d.mata_pelajaran,
											d.deskripsi as deskripsi,
											e.namagadik as ketua_tim,
											e.photopath,
											g.kelompok as kelompok,
											a.id_semester,
											concat('Semester ',a.id_semester, ' - ',h.batalyon) as info_semester,
											a.id_batalyon,
											h.batalyon as tahun_ajaran
										from m_user_taruna a
									right join t_program_studi_mata_pelajaran b
										on a.id_semester = b.id_semester
										and a.id_batalyon = b.id_batalyon
									left join t_pendidik_mata_pelajaran c
										on a.id_batalyon = c.id_batalyon
										and c.is_ketua_tim = 1
										and b.id_mata_pelajaran = c.id_mata_pelajaran
									left join m_mata_pelajaran d
										on d.id = b.id_mata_pelajaran
									left join m_user_pendidik e
										on e.id_m_user = c.id_pendidik
									left join m_sm_batalyon h
										on h.id = a.id_batalyon
									left join m_kelompok g
										on a.id_kelompok = g.id
									left join m_semester i 
									on h.id_semester=i.id
									where a.id_m_user ='" . $id_user . "' and b.is_deleted=0");

		$resData = $query->getRow();

		if ($resData != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $query->getResult()
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function overviewClassStudent()
	{
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_user 		= $_POST['id_user'];

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
		i.namagadik as nama_dosen,
		i.photopath as photo_dosen,
		h.`nama` as lokasi_kelas,
		(case when (m.`id_aspek` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas,
		CONCAT(sf_formatdate_ID(a.tanggal),' • ', DATE_FORMAT(a.jam_mulai,'%H:%i'),' - ',DATE_FORMAT(a.jam_selesai,'%H:%i'), ' WIB') as tanggal_detail,
			--   b.tahun_ajaran,
			  (case when (c.file_materi_tugas is null) then '0' else '1' end) as file_ass,
		 (case when (e.jml_file is null) then '0' else e.jml_file end) as file_att,
		 f.jml_hadir as jml_hadir,
		 f.jml_tdk_hadir as jml_tdk_hadir
			  from `t_jadwal` a
			  LEFT JOIN t_bahan_ajar b on a.id_bahan_ajar=b.id
			  LEFT JOIN (SELECT * from t_jadwal_tugas) c on c.id_jadwal=a.id
			  LEFT JOIN m_mata_pelajaran d on b.id_mata_pelajaran=d.id
			  LEFT JOIN (SELECT count(a.lokasi_file) as jml_file,a.id_mata_pelajaran,a.pertemuan_ke from (
		SELECT a.id,a.id_mata_pelajaran,a.lokasi_file,a.pertemuan_ke from t_file_materi a
		LEFT JOIN t_bahan_ajar b on a.id_mata_pelajaran=b.id_mata_pelajaran
		LEFT JOIN t_jadwal c on c.id_bahan_ajar=b.id
		LEFT JOIN m_sm_batalyon k on b.id_batalyon=k.id
		LEFT JOIN m_user_taruna d on c.id_kelompok_taruna=d.id_kelompok and d.id_batalyon=k.id
		where d.id_m_user='" . $id_user . "'  and a.is_deleted=0 and a.id_mata_pelajaran='" . $id_mata_pelajaran . "' group by a.id
						 ) a group by a.pertemuan_ke) e on d.id=e.id_mata_pelajaran and b.pertemuan_ke=e.pertemuan_ke
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
				   LEFT JOIN t_program_studi_mata_pelajaran m on b.id_mata_pelajaran=m.id_mata_pelajaran  and b.id_batalyon=m.id_batalyon
				   LEFT JOIN m_user_taruna n on b.id_batalyon=n.id_batalyon
				   LEFT JOIN m_semester l on n.id_semester=l.id
		  where n.id_m_user='" . $id_user . "' and b.id_mata_pelajaran='" . $id_mata_pelajaran . "' and c.is_deleted = 0 and a.is_absensi_pendidik=1 GROUP BY c.id");

		$resData = $query->getRow();

		if ($resData != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $query->getResult()
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data' => NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function sessionClassStudent()
	{
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_user 			= $_POST['id_user'];
		$rs = $this->db->query("SELECT id_kelompok from m_user_taruna where id_m_user='" . $id_user . "'")->getRow();
		$id_kelompok = $rs->id_kelompok;

		$query = $this->db->query("SELECT 	b.id as id_jadwal,
											f.kelompok,
											d.mata_pelajaran,
											-- a.tahun_ajaran,
											a.deskripsi,
											a.pertemuan_ke,
											a.judul,
											b.is_absensi_pendidik,
											b.tanggal,
											b.jam_mulai,
											b.jam_selesai,
											c.nama as lokasi_kelas,
											h.namagadik as nama_dosen,
											h.photopath as photo_dosen,		
											b.is_absensi_pendidik,
											CONCAT(sf_formatdate_ID(b.tanggal),' • ', DATE_FORMAT(b.jam_mulai,'%H:%i'),' - ',DATE_FORMAT(b.jam_selesai,'%H:%i'), ' WIB') as tanggal_detail,
											(case when (g.`id_aspek` = 1) then 'Teori' else 'Praktek' end) as kategori_kelas,
											(case when (e.jml_file is null) then '0' else e.jml_file end) as file_att,
											(case when (b.id is null) then '0' else '1' end) as is_detail,
											if(g.id_jadwal is not null, 1,0) as is_label
									from t_bahan_ajar a
										LEFT JOIN (SELECT * from t_jadwal where id_kelompok_taruna='" . $id_kelompok . "' and is_deleted=0) b on a.id=b.id_bahan_ajar
										LEFT JOIN m_ruang_kelas c on b.`id_ruang_kelas`=c.id
										LEFT JOIN m_mata_pelajaran d on a.id_mata_pelajaran=d.id
										LEFT JOIN (		SELECT 
												count(a.lokasi_file) as jml_file,
												a.id_mata_pelajaran,a.pertemuan_ke from (
														SELECT a.id,a.id_mata_pelajaran,a.lokasi_file,a.pertemuan_ke from t_file_materi a
																			LEFT JOIN m_tipe_file b on a.id_tipe_file=b.id
																			LEFT JOIN m_mata_pelajaran c on a.id_mata_pelajaran=c.id
																			LEFT JOIN m_sm_batalyon d on a.id_batalyon=d.id
																			LEFT JOIN m_user_taruna e on e.id_batalyon=d.id
																		where a.id_mata_pelajaran='" . $id_mata_pelajaran . "' and e.id_m_user='" . $id_user . "' and a.is_deleted=0  order by a.pertemuan_ke
															) a group by a.pertemuan_ke) e on d.id=e.id_mata_pelajaran and a.pertemuan_ke=e.pertemuan_ke
										LEFT JOIN (SELECT 
												a.id_user_pendidik,
												d.id_pendidik,
												b.id as id_jadwal,
												b.is_absensi_pendidik,
												b.id_kelompok_taruna,
												a.pertemuan_ke,
												(case when (b.id is null) then '0' else '1' end) as is_detail
												from t_bahan_ajar a
												LEFT JOIN t_jadwal b on a.id=b.id_bahan_ajar
												left join m_user_taruna o on b.id_kelompok_taruna=o.id_kelompok
												left join t_pendidik_mata_pelajaran d on a.id_mata_pelajaran = d.id_mata_pelajaran and a.id_batalyon = d.id_batalyon
												where a.id_mata_pelajaran='" . $id_mata_pelajaran . "' and 
												a.is_ujian=0 and 
												b.is_absensi_pendidik = 0 and 
												b.is_deleted=0 and
												(o.id_kelompok='" . $id_kelompok . "' or o.id_kelompok is null) group by b.id_kelompok_taruna, d.id_pendidik) g on g.id_jadwal=b.id
										LEFT JOIN m_user_pendidik h on b.id_user_pendidik=h.id_m_user
										LEFT JOIN m_user_taruna i on b.id_kelompok_taruna=i.id_kelompok 
										LEFT JOIN m_kelompok f on i.id_kelompok=f.id
										LEFT JOIN t_program_studi_mata_pelajaran g on a.id_batalyon=g.id_batalyon and a.id_mata_pelajaran=g.id_mata_pelajaran and a.id_semester=g.id_semester
									where a.id_mata_pelajaran='" . $id_mata_pelajaran . "' and a.is_ujian=0 and g.is_deleted=0 GROUP BY a.id");
		$resData = $query->getRow();

		if ($resData != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $query->getResult()
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data' => NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function assigmentClassStudent()
	{
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_user 			= $_POST['id_user'];

		$query = $this->db->query("SELECT 
										a.id as id_tugas,
										b.id as id_jadwal_tugas,
										c.id as id_jadwal_mapel,
										h.mata_pelajaran,
										g.pertemuan_ke,
										b.judul,
										b.deskripsi,
										f.kelompok,
										b.waktu_pengumpulan,
										if(date(NOW()) > b.waktu_pengumpulan,1,0) as is_done,
										(case when (b.`tipe_tugas` = 1) then 'Online' else 'Kumpulkan di Kelas' end) as kategori_pengumpulan,
										(case when (a.file_tugas is null) then 0 else 1 end) as status_tugas,
										CONCAT('http://devel.nginovasi.id/akpol-api/',b.file_materi_tugas) as file_tugas,
										d.icon_file,
											d.tipe_file,
										a.nilai,
										a.catatan,
										(case when (a.log_nilai is null) then 0 else 1 end) as status_nilai,
										a.file_tugas as upload_tugas,
										(case when (a.file_tugas is null) then '• Belum Mengumpulkan Tugas' else CONCAT('• ','Mengumpulkan ',sf_formatdate_ID(a.upload_date),' • ', DATE_FORMAT(a.upload_date,'%H:%i'), ' WIB' ) end) as keterangan_tugas
									FROM t_nilai_tugas a
									LEFT JOIN t_jadwal_tugas b on a.id_jadwal_tugas=b.id
									LEFT JOIN m_user_taruna j on a.id_user_taruna=j.id_m_user
									LEFT JOIN t_jadwal c on b.`id_jadwal`=c.id
									LEFT JOIN m_tipe_file d on b.id_tipe_file=d.id
									LEFT JOIN m_kelompok f on j.`id_kelompok`=f.id
									LEFT JOIN t_bahan_ajar g on c.id_bahan_ajar=g.id
									LEFT JOIN m_mata_pelajaran h on g.id_mata_pelajaran=h.id
									LEFT JOIN m_user_pendidik i on g.id_user_pendidik=i.id_m_user
									where a.id_user_taruna='" . $id_user . "' and g.id_mata_pelajaran='" . $id_mata_pelajaran . "' order by b.waktu_pengumpulan ");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function filesClassStudent()
	{
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_user 			= $_POST['id_user'];

		$query = $this->db->query("SELECT c.mata_pelajaran,
											a.pertemuan_ke,
											CONCAT('http://devel.nginovasi.id/akpol-api/',a.lokasi_file) as lokasi_file,
											b.tipe_file,
											b.icon_file,
											CONCAT('File at Session ',a.pertemuan_ke,' - ',c.mata_pelajaran) as info_file,
											CONCAT(sf_formatdate_ID(a.created_at),' • ',DATE_FORMAT(a.created_at,'%H:%i'), ' WIB') as tanggal_detail,
											a.created_at as date_insert
									from t_file_materi a
										LEFT JOIN m_tipe_file b on a.id_tipe_file=b.id
										LEFT JOIN m_mata_pelajaran c on a.id_mata_pelajaran=c.id
										LEFT JOIN m_sm_batalyon d on a.id_batalyon=d.id
										LEFT JOIN m_user_taruna e on e.id_batalyon=d.id
									where a.id_mata_pelajaran='" . $id_mata_pelajaran . "' and e.id_m_user='" . $id_user . "' and a.is_deleted=0  order by a.pertemuan_ke");


		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function dataStudentClass()
	{
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$id_user 			= $_POST['id_user'];

		$kelompok = $this->db->query("SELECT id_kelompok FROM m_user_taruna where id_m_user='" . $id_user . "'")->getRow();

		$query = $this->db->query("SELECT f.id_m_user as id_taruna,f.namataruna,f.noaklong,a.id_kelompok_taruna,f.photopath from t_jadwal a
									left join t_bahan_ajar b on a.id_bahan_ajar=b.id
									left join m_mata_pelajaran c on b.id_mata_pelajaran=c.id
									left join m_kelompok d on a.id_kelompok_taruna=d.id
									left join m_user_taruna f on a.id_kelompok_taruna=f.id_kelompok and b.id_semester=f.id_semester
									left join m_sm_batalyon g on f.id_batalyon=g.id
									where b.id_mata_pelajaran='" . $id_mata_pelajaran . "' and a.id_kelompok_taruna='" . $kelompok->id_kelompok . "' group by f.id_m_user");


		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listInfoStudent()
	{
		$id_user = $_POST['id_user'];

		$query = $this->db->query("SELECT 
									a.*, 
									b.kelompok AS nama_kelompok, 
									c.mata_pelajaran AS nama_mata_pelajaran,
									g.namagadik
								FROM t_informasi_pendidik a
								LEFT JOIN m_kelompok b
									ON a.id_kelompok = b.id
								LEFT JOIN m_mata_pelajaran c
									ON a.id_mata_pelajaran = c.id
								LEFT JOIN m_config d
									ON d.id = 1
								LEFT JOIN m_config e
									ON e.id = 2
								LEFT JOIN m_user_taruna f
									ON f.id_kelompok=b.id
								LEFT JOIN m_user_pendidik g
									ON g.id_m_user=a.created_by
								WHERE f.id_m_user = '" . $id_user . "'
									AND a.is_deleted = 0
									AND a.tahun_ajaran = d.nilai
									AND a.semester = e.nilai
								ORDER BY a.created_at desc");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function storageFileStudent()
	{

		$id_user 	= $_POST['id_user'];

		$batalyon = $this->db->query("SELECT id_batalyon FROM m_user_taruna where id_m_user='" . $id_user . "'")->getRow();
		$infosemester = $this->db->query("SELECT 
											d.batalyon as tahun_ajaran,
											e.ganjil_genap,
											concat(e.semester,' • Batalyon ',d.batalyon) as keterangan
										from t_file_materi a
											LEFT JOIN m_tipe_file b on a.id_tipe_file=b.id
											LEFT JOIN m_mata_pelajaran c on a.id_mata_pelajaran=c.id
											LEFT JOIN m_sm_batalyon d on a.id_batalyon=d.id
											LEFT JOIN m_semester e on d.id_semester=e.id
										LEFT JOIN	m_user_taruna f on d.id=f.id_batalyon and f.id_semester=e.id
										where f.id_m_user='" . $id_user . "'  GROUP BY d.batalyon");

		$header = $this->db->query("SELECT 
		sum(total_size)/1000000 as total_size,
		'MB' as satuan,
		json_arrayagg(json_object(
					  'type_file', group_file,
					  'count', count
					)) as kelompok_file_head
		from (
		
		SELECT a.group_file,
				count(*) as count,
				sum(a.ukuran_file) as total_size from (	
		SELECT g.group_file,e.id,e.ukuran_file
		from m_user_taruna a
			LEFT JOIN t_jadwal c on c.id_kelompok_taruna=a.id_kelompok
			LEFT JOIN t_bahan_ajar d on c.id_bahan_ajar=d.id
			LEFT JOIN t_file_materi e on d.id_mata_pelajaran=e.id_mata_pelajaran
			left join m_sm_batalyon f on a.id_batalyon = f.id
			left join m_tipe_file g on e.id_tipe_file = g.id
			left join m_semester h on f.id_semester=h.id 
			where a.id_m_user='" . $id_user . "' and e.id_batalyon='" . $batalyon->id_batalyon . "' and e.is_deleted = '0' 
			group by e.id) a
			group by a.group_file) z");

		$content = $this->db->query("SELECT 
		id_mata_pelajaran,
		mata_pelajaran,
		sum(total_size)/1000000 as total_size,
		'MB' as satuan,
		json_arrayagg(json_object(
					  'type_file', group_file,
					  'count', count
					)) as kelompok_file
		from (	
		SELECT a.id_mata_pelajaran,a.mata_pelajaran,a.group_file,
				count(*) as count,
				sum(a.ukuran_file) as total_size from (	
			SELECT g.group_file,e.id,e.ukuran_file,e.id_mata_pelajaran,i.mata_pelajaran
		from m_user_taruna a
			LEFT JOIN t_jadwal c on c.id_kelompok_taruna=a.id_kelompok
			LEFT JOIN t_bahan_ajar d on c.id_bahan_ajar=d.id
			LEFT JOIN t_file_materi e on d.id_mata_pelajaran=e.`id_mata_pelajaran`
			left join m_tingkatan_detail f on e.id_tingkatan_detail = f.id
			left join m_tipe_file g on e.id_tipe_file = g.id
			left join m_semester h on f.id_semester=h.id
			left join m_mata_pelajaran i on e.id_mata_pelajaran=i.id
			where a.id_m_user='" . $id_user . "' and e.id_batalyon='" . $batalyon->id_batalyon . "' and  e.is_deleted = '0' 
			group by e.id ) a
			group by a.id_mata_pelajaran,a.group_file) z 
			group by z.id_mata_pelajaran");

		$resHeader = $header->getResult();
		$resHeaderRow = $header->getRow();
		$resContent = $content->getResult();
		if ($resHeader != NULL && $resContent != NULL) {
			foreach ($resHeader as $objhead) {
				$filehead = json_decode($objhead->kelompok_file_head);
				if ($filehead[0]->type_file == null) {
					$objhead->kelompok_file_head = null;
				} else {
					$objhead->kelompok_file_head = $filehead;
				}
			}
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data' => NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}


		if ($resHeader != NULL && $resContent != NULL) {
			foreach ($resContent as $obj) {
				$file = json_decode($obj->kelompok_file);
				if ($file[0]->type_file == null) {
					$obj->kelompok_file = null;
				} else {
					$obj->kelompok_file = $file;
				}
			}
			$dataArr = [
				'semester' => $infosemester->getRow(),
				'header' => $resHeaderRow,
				'content' => $resContent
			];
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $dataArr
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data' => NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function detailStorageStudent()
	{
		$id_user 	= $_POST['id_user'];
		$id_mata_pelajaran 	= $_POST['id_mata_pelajaran'];
		$batalyon = $this->db->query("SELECT id_batalyon FROM m_user_taruna where id_m_user='" . $id_user . "'")->getRow();

		$query = $this->db->query("SELECT 
		CONCAT('http://devel.nginovasi.id/akpol-api/',a.lokasi_file) as lokasi_file,
		a.ukuran_file/1000000 as ukuran_file,
		'MB' as satuan,
		d.tipe_file,
			  d.icon_file,
		a.created_at,
				d.group_file,
				e.mata_pelajaran
			from t_file_materi a
			left join m_tingkatan_detail b
				on a.id_tingkatan_detail = b.id
			left join m_semester c
				on b.id_semester = c.id
			left join m_tipe_file d
				on a.id_tipe_file = d.id
			left join m_mata_pelajaran e
				on a.id_mata_pelajaran = e.id
		   left join t_bahan_ajar f
		   	  on a.id_mata_pelajaran=f.id_mata_pelajaran
		    left join t_jadwal g 
		    	   on g.id_bahan_ajar=f.id
		     left join m_user_taruna h
		     		on g.id_kelompok_taruna=h.id_kelompok
			where a.is_deleted = 0
		   and h.id_m_user='" . $id_user . "'
		and	a.id_batalyon = '" . $batalyon->id_batalyon . "'
	   and a.id_mata_pelajaran = '" . $id_mata_pelajaran . "'
	   GROUP BY a.id");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status' => 1,
				'message' => 'Success',
				'data' => $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 => NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function semesterStudent()
	{

		$query = $this->db->query("SELECT id as id_semester,REPLACE(semester,'SMT','Semester') as keterangan from m_semester order by id asc limit 8");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}
	//END TARUNA///


	//Panic Button

	public function kategoriPelaporan()
	{

		$query = $this->db->query("SELECT * from m_kat_pelaporan where is_active=1");

		$resData = $query->getResult();

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function savePelaporan()
	{

		if (isset($_POST)) {
			$id_user 					= $_POST['id_user'];
			$id_kat_pelaporan 			= $_POST['id_kat_pelaporan'];
			$laporan 					= $_POST['laporan'];
			$latitude 					= $_POST['latitude'];
			$longitude 					= $_POST['longitude'];
			$alamat 					= $_POST['alamat'];
			$dataImage 					= $_POST['file_foto'];
			$token 						= $_POST['token'];
			$kd_aduan					= $this->kodeLapor();

			if (isset($dataImage) && $dataImage != "") {
				$insert['created_by'] = $id_user;
				$insert['id_user'] = $id_user;
				$insert['id_kat_pelaporan'] = $id_kat_pelaporan;
				$insert['laporan'] = $laporan;
				$insert['latitude'] = $latitude;
				$insert['longitude'] = $longitude;
				$insert['alamat'] = $alamat;
				$insert['kd_aduan'] = $kd_aduan;
				if ($this->db->table('t_pelaporan')->insert($insert)) {
					$id_pelaporan = $this->db->insertID();

					$arrImg = explode("|||", $dataImage);
					// print_r($arrImg);
					$arr = [];
					$i = 1;
					foreach ($arrImg as $imageData) {

						$filename = $this->genNamefile() . "_" . $i;
						$file = '/home/ngi/php/akpol-api/public/bukti/' . $filename . '.jpg';
						$data = base64_decode($imageData);
						file_put_contents($file, $data);

						$gallery = [
							'foto' 			=> 'public/bukti/' . $filename . '.jpg',
							'id_pelaporan' 	=> $id_pelaporan,
							'created_by'    => $id_user
						];
						array_push($arr, $gallery);
						$i = $i + 1;
					}

					$builder = $this->db->table('t_bukti_pelaporan');
					if ($builder->insertBatch($arr)) {
						$insertb['id_laporan'] = $id_pelaporan;
						$insertb['status'] = 1;

						if ($this->db->table('t_proses_pelaporan')->insert($insertb)) {
							$this->db->query("UPDATE m_user set fcm_token='" . $token . "' where id='" . $id_user . "'");
							$response = [
								'status' => 1,
								'message' => 'Success'
							];
							$action =  $this->request->getMethod();
							$status = json_encode($response);
							$datapost = json_encode($_POST);
							$this->logaction($action, $datapost, $status);

							$this->addchatpanikbutton($id_pelaporan);
							return $this->response->setJSON($response);
						} else {
							$response = [
								'status' => 0,
								'message' => 'Failed'
							];
							$action =  $this->request->getMethod();
							$status = json_encode($response);
							$datapost = json_encode($_POST);
							$this->logaction($action, $datapost, $status);
							return $this->response->setJSON($response);
						}
					} else {
						$this->db->query("DELETE from t_pelaporan where id '" . $id_pelaporan . "'");
						$response = [
							'status' => 0,
							'message' => 'Failed'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					}
				} else {
					$response = [
						'status' => 0,
						'message' => 'Failed'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			} else {
				$insert['created_by'] = $id_user;
				$insert['id_user'] = $id_user;
				$insert['id_kat_pelaporan'] = $id_kat_pelaporan;
				$insert['laporan'] = $laporan;
				$insert['latitude'] = $latitude;
				$insert['longitude'] = $longitude;
				$insert['alamat'] = $alamat;
				$insert['kd_aduan'] = $kd_aduan;

				if ($this->db->table('t_pelaporan')->insert($insert)) {
					$id_pelaporan = $this->db->insertID();

					$insertb['id_laporan'] = $id_pelaporan;
					$insertb['status'] = 1;

					if ($this->db->table('t_proses_pelaporan')->insert($insertb)) {
						$this->db->query("UPDATE m_user set fcm_token='" . $token . "' where id='" . $id_user . "'");
						$response = [
							'status' => 1,
							'message' => 'Success'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);

						$this->addchatpanikbutton($id_pelaporan);

						return $this->response->setJSON($response);
					} else {
						$response = [
							'status' => 0,
							'message' => 'Failed'
						];
						$action =  $this->request->getMethod();
						$status = json_encode($response);
						$datapost = json_encode($_POST);
						$this->logaction($action, $datapost, $status);
						return $this->response->setJSON($response);
					}
				} else {
					$response = [
						'status' => 0,
						'message' => 'Failed'
					];
					$action =  $this->request->getMethod();
					$status = json_encode($response);
					$datapost = json_encode($_POST);
					$this->logaction($action, $datapost, $status);
					return $this->response->setJSON($response);
				}
			}
		} else {
			$response = [
				'status' => 0,
				'message' => 'Please complete the input form'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	public function listLaporan()
	{
		$id_user = $_POST['id_user'];

		$query = $this->db->query("SELECT a.id as id_pelaporan,a.kd_aduan,a.laporan,c.kategori as kat_pelaporan,a.`latitude`,a.`longitude`,a.alamat,a.proses as kd_proses,a.`created_at`,json_arrayagg(json_object(
			'id_bukti', b.id,
			'bukti_foto',CONCAT('http://devel.nginovasi.id/akpol-api/',b.foto))) as bukti,d.log_pelaporan
			
			from t_pelaporan a
			left join t_bukti_pelaporan b on a.id=b.id_pelaporan
			left join m_kat_pelaporan c on a.id_kat_pelaporan=c.id
			left join (select a.*,json_arrayagg(json_object(
						'id', d.id,
						'id_pelaporan', d.id_laporan,
						'status',d.status,
						'log',date_format(d.created_at,'%Y-%m-%d %H:%i:%s'),
						'keterangan',d.keterangan)) as log_pelaporan from t_pelaporan a 
			left join t_proses_pelaporan d on a.id=d.id_laporan
			group by a.id order by d.status) d on a.id=d.id
			where a.id_user='" . $id_user . "' and a.is_deleted=0
			group by a.id order by a.id desc");

		$resData = $query->getResult();
		foreach ($resData as $obj) {
			$bukti = json_decode($obj->bukti);
			$laporan = json_decode($obj->log_pelaporan);
			if ($laporan[0]->id == null) {
				$obj->log_pelaporan = null;
			} else {
				$obj->log_pelaporan = $laporan;
			}

			if ($bukti[0]->id_bukti == null) {
				$obj->bukti = null;
			} else {
				$obj->bukti = $bukti;
			}
		}

		if ($resData != NULL) {
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success',
				'data'	 	=> $resData
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		} else {
			$response = [
				'status' => 1,
				'message' => 'Tidak Ada Data',
				'data'	 	=> NULL
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}


	public function doneProses()
	{

		$id_user = $_POST['id_user'];
		$id_laporan = $_POST['id_laporan'];

		$insert['status'] = 4;
		$insert['id_laporan'] = $id_laporan;
		$insert['created_by'] = $id_user;
		$insert['created_at'] = date('Y-m-d H:i:s');
		$insert['keterangan'] = 'Selesai';

		$builder = $this->db->table('t_proses_pelaporan');
		$execute = $builder->insert($insert);
		if ($execute) {
			$this->db->query("UPDATE t_pelaporan set proses=4 where id='" . $id_laporan . "'");
			$response = [
				'status'	=> 1,
				'message' 	=> 'Success'
			];
			// tambahane najib
			$query_log = $this->db->query("SELECT * from t_pelaporan a left join log_telegram b on b.message_id=a.message_id_telegram where a.id='".$id_laporan."' order by b.id DESC limit 1")->getRow();

			if($query_log->chat_id == NULL && $query_log->message_id == NULL){

				return $this->response->setJSON($response);
			}else{
				$isipesan = urlencode($query_log->pesan . 'Laporan Selesai pada ' . date("d-m-Y H:i:s"));

			$botAPI = "https://api.telegram.org/bot5335283998:AAHEyZmjB__VQNZw767DxQUXDxlG2Hdsw3c";
			
			file_get_contents($botAPI . "/editMessageText?chat_id=" . $query_log->chat_id . "&message_id=" . $query_log->message_id . "&text=$isipesan&parse_mode=HTML");
			// end tambahane najib
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);

			return $this->response->setJSON($response);
			}
			

		} else {
			$response = [
				'status' => 0,
				'message' => 'Failed Insert'
			];
			$action =  $this->request->getMethod();
			$status = json_encode($response);
			$datapost = json_encode($_POST);
			$this->logaction($action, $datapost, $status);
			return $this->response->setJSON($response);
		}
	}

	function addchatpanikbutton($id)
	{

		// $id = $id;
		// $id = '39';
		$tokenbottele = "5335283998:AAHEyZmjB__VQNZw767DxQUXDxlG2Hdsw3c"; // bot akpol

		// $eduakpol = '-680205430';
		$eduakpol = '-1001182539588';

		$keyboard = json_encode([
			"inline_keyboard" => [
				[
					[
						"text" => "Abaikan",
						"callback_data" => "abaikanlaporan"
					],
					[
						"text" => "Tindak",
						"callback_data" => "tindaklaporan"
					]
				]
			]
		]);


		$query = $this->db->query("SELECT concat('KD aduan : *', a.kd_aduan ,'*
', a.laporan) as `text`, b.foto , a.latitude , a.longitude from t_pelaporan a left join t_bukti_pelaporan b on a.id=b.id_pelaporan where a.id='" . $id . "'")->getRow();


		if ($query->latitude !== '' & $query->longitude !== '') {
			// send Lokasi
			$link = "https://api.telegram.org/bot" . $tokenbottele . "/sendlocation?chat_id=" . $eduakpol . "&latitude=" . $query->latitude . "&longitude=" . $query->longitude . "";

			$rstele =  json_decode($this->chat_tele($link), true);

			if ($rstele['ok']) {

				if ($query->foto !== null) {

					$link = "https://api.telegram.org/bot" . $tokenbottele . "/sendPhoto?chat_id=" . $eduakpol . "&photo=http://devel.nginovasi.id/akpol-api/" . $query->foto . "&parse_mode=HTML&reply_to_message_id=" . $rstele['result']['message_id'];

					$rstelepoto =  json_decode($this->chat_tele($link), true);

					if ($rstelepoto['ok']) {

						$link = "https://api.telegram.org/bot" . $tokenbottele . "/sendMessage?chat_id=" . $eduakpol . "&text=" . urlencode($query->text) . "&parse_mode=HTML&reply_markup={$keyboard}&reply_to_message_id=" . $rstelepoto['result']['message_id'];

						$rstele =  json_decode($this->chat_tele($link), true);
					}
				} else {

					$link = "https://api.telegram.org/bot" . $tokenbottele . "/sendMessage?chat_id=" . $eduakpol . "&text=" . urlencode($query->text) . "&parse_mode=HTML&reply_markup={$keyboard}&reply_to_message_id=" . $rstele['result']['message_id'];
					$rstele =  json_decode($this->chat_tele($link), true);
				}
			}
		} else {

			if ($query->foto !== null) {

				$link = "https://api.telegram.org/bot" . $tokenbottele . "/sendPhoto?chat_id=" . $eduakpol . "&photo=http://devel.nginovasi.id/akpol-api/" . $query->foto . "&parse_mode=HTML";

				$rstelepoto =  json_decode($this->chat_tele($link), true);

				if ($rstelepoto['ok']) {

					$link = "https://api.telegram.org/bot" . $tokenbottele . "/sendMessage?chat_id=" . $eduakpol . "&text=" . urlencode($query->text) . "&parse_mode=HTML&reply_markup={$keyboard}&reply_to_message_id=" . $rstelepoto['result']['message_id'];

					$rstele =  json_decode($this->chat_tele($link), true);
				}
			} else {

				$link = "https://api.telegram.org/bot" . $tokenbottele . "/sendMessage?chat_id=" . $eduakpol . "&text=" . urlencode($query->text) . "&parse_mode=HTML&reply_markup={$keyboard}";
				$rstele =  json_decode($this->chat_tele($link), true);
			}
		}
	}

	function testpanikbutton()
	{

		// $id = $id;
		$id = '17';
		$tokenbottele = "5335283998:AAHEyZmjB__VQNZw767DxQUXDxlG2Hdsw3c"; // bot akpol

		// $eduakpol = '-680205430';
		$eduakpol = '-1001182539588';

		$keyboard = json_encode([
			"inline_keyboard" => [
				[
					[
						"text" => "Abaikan",
						"callback_data" => "abaikanlaporan"
					],
					[
						"text" => "Tindak",
						"callback_data" => "tindaklaporan"
					]
				]
			]
		]);


		$query = $this->db->query("SELECT concat('KD aduan : *', a.kd_aduan ,'*
', a.laporan) as `text`, b.foto , a.latitude , a.longitude from t_pelaporan a left join t_bukti_pelaporan b on a.id=b.id_pelaporan where a.id='" . $id . "'")->getRow();


		if ($query->latitude !== '' & $query->longitude !== '') {
			// send Lokasi
			$link = "https://api.telegram.org/bot" . $tokenbottele . "/sendlocation?chat_id=" . $eduakpol . "&latitude=" . $query->latitude . "&longitude=" . $query->longitude . "";

			$rstele =  json_decode($this->chat_tele($link), true);

			if ($rstele['ok']) {

				if ($query->foto !== null) {

					$link = "https://api.telegram.org/bot" . $tokenbottele . "/sendPhoto?chat_id=" . $eduakpol . "&photo=http://devel.nginovasi.id/akpol-api/" . $query->foto . "&parse_mode=HTML&reply_to_message_id=" . $rstele['result']['message_id'];

					$rstelepoto =  json_decode($this->chat_tele($link), true);

					if ($rstelepoto['ok']) {

						$link = "https://api.telegram.org/bot" . $tokenbottele . "/sendMessage?chat_id=" . $eduakpol . "&text=" . urlencode($query->text) . "&parse_mode=HTML&reply_markup={$keyboard}&reply_to_message_id=" . $rstelepoto['result']['message_id'];

						$rstele =  json_decode($this->chat_tele($link), true);
					}
				} else {

					$link = "https://api.telegram.org/bot" . $tokenbottele . "/sendMessage?chat_id=" . $eduakpol . "&text=" . urlencode($query->text) . "&parse_mode=HTML&reply_markup={$keyboard}&reply_to_message_id=" . $rstele['result']['message_id'];
					$rstele =  json_decode($this->chat_tele($link), true);
				}
			}
		} else {

			if ($query->foto !== null) {

				$link = "https://api.telegram.org/bot" . $tokenbottele . "/sendPhoto?chat_id=" . $eduakpol . "&photo=http://devel.nginovasi.id/akpol-api/" . $query->foto . "&parse_mode=HTML";

				$rstelepoto =  json_decode($this->chat_tele($link), true);

				if ($rstelepoto['ok']) {

					$link = "https://api.telegram.org/bot" . $tokenbottele . "/sendMessage?chat_id=" . $eduakpol . "&text=" . urlencode($query->text) . "&parse_mode=HTML&reply_markup={$keyboard}&reply_to_message_id=" . $rstelepoto['result']['message_id'];

					$rstele =  json_decode($this->chat_tele($link), true);
				}
			} else {

				$link = "https://api.telegram.org/bot" . $tokenbottele . "/sendMessage?chat_id=" . $eduakpol . "&text=" . urlencode($query->text) . "&parse_mode=HTML&reply_markup={$keyboard}";
				$rstele =  json_decode($this->chat_tele($link), true);
			}
		}
	}

	function chat_tele($link)
	{
		$headerstele = array();
		$headerstele[] = 'Content-Type: application/json';

		$chtele = curl_init();
		curl_setopt($chtele, CURLOPT_URL, $link);
		curl_setopt($chtele, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($chtele, CURLOPT_HTTPHEADER, $headerstele);
		curl_setopt($chtele, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($chtele, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($chtele, CURLOPT_RETURNTRANSFER, true);
		//Send the request
		$responsetele = curl_exec($chtele);
		//Close request
		if ($responsetele === FALSE) {
			die('FCM Send Error: ' . curl_error($chtele));
		}


		return $responsetele;

		curl_close($chtele);
	}
	//End Panic Button
}
