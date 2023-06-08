<?php 
namespace App\Core;

use CodeIgniter\Model;

class BaseModel extends Model
{
	protected $db;

	protected $session;

	function __construct() {
		$this->db = \Config\Database::connect();
		$this->session = \Config\Services::session();
	}

	function log_action($action, $url, $result, $userid, $ip, $param=NULL, $user_agent=NULL){
		$builder = $this->db->table('log_user_privileges');

		$log_data = [
			'action' => $action,
			'url' => $url,
			'result' => $result,
			'id_user' => $userid,
			'ip' => $ip,
			'param' => $param,
			'user_agent' => $user_agent
		];

		$builder->insert($log_data);
	}

	function base_get($table, $where){
		$builder = $this->db->table($table);
		$builder->where($where);

		return $builder->get();
	}

	function base_insert($data, $table){
		$builder = $this->db->table($table);

		return $builder->insert($data);
	}

	function base_insertbatch($data, $table){
		$builder = $this->db->table($table);

		return $builder->insertBatch($data);
	}

	function string_insert($data, $table){
		$builder = $this->db->table($table);

		return $builder->set($data)->getCompiledInsert();		
	}

	function base_update($data, $table, $where){
		$builder = $this->db->table($table);
		$builder->where($where);

		return $builder->update($data);	
	}

	function base_updatebatch($data, $table, $field){
		$builder = $this->db->table($table);

		return $builder->updateBatch($data, $field);
	}

	function base_delete($table, $where, $userid){
		$builder = $this->db->table($table);
		$builder->where($where);

		$updateData['is_deleted'] = 1;
		$updateData['last_edited_at'] = date('Y-m-d H:i:s');
		$updateData['last_edited_by'] = $userid;

		return $builder->update($updateData);
	}

	function base_load_datatable($baseQuery, $whereQuery, $whereTerm, $start, $length, $order){
		$q = ($whereTerm != "" ? $baseQuery . " and (" .implode(" or ", array_map(function($x) use ($whereTerm) {
			return $x . " like '%". $whereTerm ."%'";
		}, $whereQuery)) . ")" : $baseQuery) . " order by ".$order;

        $allData = count($this->db->query($baseQuery)->getResult());
        $filteredData = count($this->db->query($q)->getResult());

        $q .= $length > -1 ? " limit ".$start.",".$length : "";

        return [ "data" => $this->db->query($q)->getResult(), "allData" => $allData, "filteredData" => $filteredData ];
	}

    function base_load_select2($baseQuery, $whereField, $keyword, $page, $perpage){
        $q = $whereField != "" ? $baseQuery . " and (" .implode(" or ", array_map(function($x) use ($keyword) {
            return $x . " like '%". $keyword ."%'";
        }, $whereField)) . ")" : $baseQuery;

        // $q .= " limit ".($page * $perpage).",".$perpage;

        return $this->db->query($q)->getResult();
    }

    function base_load_select2orderby($baseQuery, $whereField, $keyword, $page, $perpage , $orderby){
        $q = $whereField != "" ? $baseQuery . " and (" .implode(" or ", array_map(function($x) use ($keyword) {
            return $x . " like '%". $keyword ."%'";
        }, $whereField)) . ") ". $orderby : $baseQuery .' '.$orderby;

        // $q .= " limit ".($page * $perpage).",".$perpage;

        return $this->db->query($q)->getResult();
    }

	function base_load_select2groupby($baseQuery, $whereField, $keyword, $page, $perpage , $groupby){
		$q = $whereField != "" ? $baseQuery . " and (" .implode(" or ", array_map(function($x) use ($keyword) {
			return $x . " like '%". $keyword ."%'";
		}, $whereField)) . ") ". $groupby : $baseQuery .' '.$groupby;

		// $q .= " limit ".($page * $perpage).",".$perpage;

		return $this->db->query($q)->getResult();
	}

    function base_load_select2pagging($baseQuery, $whereField, $keyword, $page, $perpage , $count){
        $q = $whereField != "" ? $baseQuery . " and (" .implode(" or ", array_map(function($x) use ($keyword) {
            return $x . " like '%". $keyword ."%'";
        }, $whereField)) . ")" : $baseQuery;
        if ($count) {
	        $q .= " limit ".$page.",".$perpage;
        }

        return $this->db->query($q)->getResult();
    }

    function base_load_list($baseQuery){
        $q = $baseQuery;
        return $this->db->query($q)->getResult();
    }
}