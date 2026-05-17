<?php
class TL_cli
{
	public $data = array();

	private $db;
	private $tl;
	private $adm_id;
	private $period_uin;
	private $stud_id;
	private $tutor_id;
	private $dept_id;
	private $subject_id;
	private $chapter_id;
	private $question_id;
	
	public function __construct()
	{
		global $DB;
		$this->db = $DB;
		if (isset($_COOKIE['tl_sys'])) {
			$tl_sys = cook2auth($_COOKIE['tl_sys']);
			if ($tl_sys->id > 0) {
				$this->tl['id'] = $tl_sys->id;
				$this->tl['umod'] = $tl_sys->umod;
				
				if ($tl_sys->umod == "a") {
					$this->adm_id = $tl_sys->id;
					$this->tl['usr'] = $this->db->selectRow('SELECT * FROM ?_admins WHERE admin_id=?', $tl_sys->id);
					$this->tl['usr']['iid'] = $this->tl['umod'].$this->tl['usr']['admin_id'];
				}
				if ($tl_sys->umod == "t") {
					$this->tutor_id = $tl_sys->id;
					
					$this->tl['usr'] = $this->db->selectRow('SELECT * FROM ?_tutors WHERE tutor_id=?', $tl_sys->id);
					$this->tl['usr']['iid'] = $this->tl['umod'].$this->tl['usr']['tutor_id'];
				}
				if ($tl_sys->umod == "s") {
					$this->stud_id = $tl_sys->id;
					$this->tl['usr'] = $this->db->selectRow('SELECT * FROM ?_students WHERE stud_id=?', $tl_sys->id);
					$this->tl['usr']['iid'] = $this->tl['umod'].$this->tl['usr']['stud_id'];
				}
			}
		} else {
			$this->tl['id'] = 0;
			$this->tl['umod'] = "pub";
		}

	}

	public function tldata()
	{
		return $this->tl;
	}
	public function request_encode($key, $val)
	{
		$str = base64_encode($this->tl['usr']['iid'] . $key . "=" . $val);
		$str = substr_count($str, '=').str_replace("=","",$str);
		return $str;
	}
	public function request_decode($key, $str)
	{
		$equals_arr = array("0"=>"", "1"=>"=", "2"=>"==", "3"=>"===");
		$equals = substr($str, 0, 1);
		$base = substr($str, 1);
		$base = str_replace($this->tl['usr']['iid'],"", base64_decode($base . $equals_arr[$equals]));
		$arr = explode("=", $base);
		
		return ($arr[0] == $key) ? $arr[1] : 0;
	}

	public function dict(): array
	{
		$dict = array();
		$tmp = $this->db->select('SELECT * FROM ?_dict ORDER BY type, sort');
		foreach ($tmp as $r) {
			$dict[$r['type']][$r['id']] = $r;
		}
		return $dict;
	}
	public function dict_arrays(): array
	{
		$dict = array();
		$tmp = $this->db->select('SELECT * FROM ?_dict ORDER BY type, sort');
		foreach ($tmp as $r) {
			$dict[$r['type']][$r['id']] = $r['title'];
		}
		return $dict;
	}

	public function tutorsName(): array
	{
		return $this->db->selectCol('SELECT tutor_id AS ARRAY_KEY, CONCAT(tutor_id, ") ", tutor_fullname) FROM ?_tutor ORDER BY tutor_fullname');
	}

	// public function group($group_id): array
	// {
	// 	$group = [];
	// 	if ($group_id > 0) {
	// 		$group = $this->db->selectRow('SELECT *'
	// 			. ' FROM ?_groups'
	// 			. ' WHERE group_id=?'
	// 			, $group_id
	// 		);
	// 	}
	// 	return $group;
	// }

	// public function student($stud_id=false): array
	// {
	// 	return $this->db->selectRow('SELECT S.*, G.grup_uin, G.grup_title, D.dept_uin, D.dept_title, D.semester_id'
	// 		. ' FROM ?_students S'
	// 		. ' INNER JOIN ?_groups G ON S.group_id=G.group_id'
	// 		. ' INNER JOIN ?_groupments D ON S.dept_id=D.dept_id'
	// 		. ' WHERE stud_id=?'
	// 		, ( $stud_id ) ? $stud_id : $this->stud_id
	// 	);
	// }
	public function paymentBYdate(string $datestr): array
	{
		return $this->db->select('SELECT R.phone, R.where_go, R.delivery_to
			, P.amount, P.method, P.comment, P.status
			FROM ?_bk_payments P 
			JOIN ?_bk_reservations R ON P.reservation_id=R.reservation_id
			WHERE P.comment=?'
			, $datestr
		);
	}
}