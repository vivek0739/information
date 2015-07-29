<?php
class Post_minute_help_model extends CI_Model
{
	var $table_depts = 'departments';
	var $table_course = 'courses';
	var $table_groups='notice_group';
	var $table_group='user_details';
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}
	public function insert_individual($data)
	{

		$no_of_individuals = $data['no_of_individuals'];

		for ($i = 1; $i <= $no_of_individuals; $i++)
		{
			$temp = $data['groups'][$i]['individual_name'];
			$minutes_id=$data['minutes_id'];
			
			$query = "INSERT INTO minutes_individuals VALUES ('$minutes_id','$temp')";
			$this->db->query($query);
		}
		return true;
	}
	public function delete_individual($minutes_id)
	{
		
		$query="DELETE from minutes_individuals where minutes_id='$minutes_id'";
		$this->db->query($query);
	}
	public function delete_group($minutes_id)
	{
		
		$query="DELETE from minutes_group_id where minutes_id='$minutes_id'";
		$this->db->query($query);
	}
	
	public function del_group($group_name,$id_user)
	{
		$query="DELETE FROM notice_group WHERE group_id='$group_name'and created_by='$id_user'";
		$this->db->query($query);
		$query="DELETE FROM minutes_group_id WHERE group_id='$group_name'and created_by='$id_user'";
		$this->db->query($query);
	}
	public function del_group_notice($group_name,$created_by)
	{
		
		$query="DELETE FROM minutes_group_id\ WHERE group_id='$group_name' and created_by='$created_by'";
		$this->db->query($query);

	}
	function get_groups($auth_id)
	{
		$query = $this->db->get_where($this->table_groups,array('created_by'=>$auth_id));
		
		return $query->result();
	}
	function get_group_member($group_id,$user_id)
	{

		$this->db->where(array('group_id'=>$group_id,'created_by'=>$user_id));
		$query =$this->db->select('user_id,user_details.first_name as first_name,
			user_details.middle_name as middle_name,user_details.last_name as last_name')
			->from($this->table_groups)
			->join("user_details", $this->table_groups.".user_id = user_details.id")
			->get();

		
		return $query->result();
	}
	function get_group($data)
	{
		$temp = $data['grp_selection'];
		$minutes_id=$data['minutes_id'];
		$created_by=$data['id_user'];
		$query = "INSERT INTO minutes_group_id VALUES ('$minutes_id','$temp','$created_by')";
		$this->db->query($query);
	}
	function put_group_class($data1,$data2,$data3,$data4)
	{		
			$query2=$this->db->query("SELECT * from notice_group where group_id='$data1' and created_by='$data2'and user_id='$data3'");
		
			if($query2->num_rows()==0)
			{
				$query = "INSERT INTO notice_group VALUES ('$data1','$data2','$data3','$data4')";
				$this->db->query($query);
			}
			
	}
	function del_group_class($data1,$data2,$data3,$data4)
	{		
			
				$query = "DELETE FROM notice_group 
				where  group_id='$data1' and created_by='$data2'
				 and user_id='$data3' ";
				$this->db->query($query);
	} 
	function search_group($data1)
	{		
			$query2=$this->db->query("SELECT * from notice_group where group_id='$data1'");
		
			return $query2->num_rows();
			
	}
	
	function no_of_groups($data1)
	{		
		$query2=$this->db->query("SELECT DISTINCT group_id from notice_group where created_by='$data1'");
		
			return $query2->num_rows();
			
	}
	function put_group_ind($data)
	{
		$members = $data['member_id'];
		$minutes_id=$data['minutes_id'];
		foreach ($members as $key => $member) {
			$query = "INSERT INTO minutes_individuals VALUES ('$minutes_id','$member')";
			$this->db->query($query);
		
		}
	}
	function create_group()
	{
		
		$res = $this->db->query("SELECT DISTINCT user_details.id, salutation, first_name, middle_name, last_name, departments.name as dept_name
		 FROM user_details INNER JOIN departments
		  ON user_details.dept_id = departments.id  ORDER BY dept_name DESC;");
		return $res;
	}
	
	public function delete_general($minutes_id)
	{
		
		$query="DELETE from minutes_general where minutes_id='$minutes_id'";
		$this->db->query($query);

		$query="DELETE from minutes_gen_emp where minutes_id='$minutes_id'";
		$this->db->query($query);
	}
	function get_depts()
	{
		$query = $this->db->get($this->table_depts);
		return $query->result();
	}
	function get_depts_stu()
	{
		$query = $this->db->get_where($this->table_depts,array('type' => 'academic' ));
		return $query->result();
	}
	function get_course()
	{
		
		
		$names = array('comm', 'honour', 'minor');
		$this->db->where_not_in('id', $names);
		$query = $this->db->get($this->table_course);
		return $query->result();
	}
	function get_course_by_dept($dept)
	{
		$this->db->where_not_in('id', $names);
		$query = $this->db->get($this->table_course);
		return $query->result();
	}
	function get_course_where($course)
	{
		$query = $this->db->get_where($this->table_course,array('id'=> $course));
		return $query->result();
	}
	
	function put_general($data)
	{
		$minutes_id=$data['minutes_id'];
		$meeting_cat = $data['cat_selection'];
		$dept_id = $data['dept_selection'];
		$auth_id = $data['emp_selection1'];
 		$course_id = $data['course_selection1'];
 		$sem= $data['sem_selection'];
 		if($meeting_cat =='stu' )
 		{

 			$query = "INSERT INTO minutes_general VALUES ('$minutes_id','$meeting_cat','$dept_id','$course_id','$sem')";
			$this->db->query($query);
 		}
 		else
 		{
 			$query = "INSERT INTO minutes_gen_emp VALUES ('$minutes_id','$meeting_cat','$dept_id','$auth_id')";
			$this->db->query($query);
 			
 		}
		
	}
	function get_auth($id_user)
	{
		$auth_id = $this->db->select('auth_id')->where('id',$id_user)->get('users');
		$auth_cat = $auth_id->row()->auth_id;
		echo $auth_cat;
		return $auth_cat;
	}
	
	function getUsersByDeptAuth($dept = 'all',$auth = 'all')
	{
		$query = $this->db->select('user_details.id, salutation, first_name, middle_name, last_name, departments.name as dept_name')
							->from('user_details')
							->join('departments','user_details.dept_id = departments.id');

		if($auth != 'all')	$query = $this->db->join('user_auth_types','user_details.id = user_auth_types.id')
												->where('user_auth_types.auth_id',$auth);
		if($dept != 'all')	$query = $this->db->where('user_details.dept_id',$dept);

		return $query->get()->result();
	}
	function delete_notice_send_incorectly($minutes_id)
	{
		echo $minutes_id;
		$query="DELETE from info_minute_details where minutes_id='$minutes_id'";
		$this->db->query($query);
	}
}