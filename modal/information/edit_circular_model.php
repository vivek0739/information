<?php

class Edit_circular_model extends CI_Model
{

	var $table = 'info_circular_details';

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	function update($data)
	{
		$this->db->where('circular_id',$data['circular_id']);
		$this->db->update($this->table,$data);
	}
	
	function getCircularsByMinId($circular_id)
	{
		$query=$this->db->where('circular_id',$circular_id)->get($this->table);
		if($query->num_rows()==0)	return FALSE;
		else	return $query->row();
	}
	
	function insertM($circular_id)
	{
		$table = 'info_circular_modification_details';
		$query = $this->db->where('circular_id',$circular_id)->get($this->table);
		
		if($query->num_rows() == 0 ) return FALSE;
		else $ans = $query->row_array();
		
		$query1=$this->db->query("SELECT max(modification_value)
						FROM info_circular_modification_details 
						where circular_id='$circular_id'") ;
		if(!is_null($query1) && $ans['modification_value'] <= $query1->row()->mod  )
		{
			$this->db->where('circular_id',$circular_id);
			$this->db->where('modification_value',$query1->row()->mod);
			$this->db->update($table,$ans);
		}
		else
		$this->db->insert($table, $ans);
	}
	
	function get_circulars($user_id,$auth_id='')
	{
		
		$this->db->where('issued_by',$this->session->userdata('id'));
		$this->db->where('info_circular_details.issued_by',$user_id);
		$this->db->where('info_circular_details.auth_id',$auth_id);
		$query = $this->db->select("info_circular_details.*, user_details.*, auth_types.type as auth_name, departments.name as department, designations.name as designation")
						  ->from('info_circular_details')
						  ->join("user_details", "info_circular_details.issued_by = user_details.id")
						  ->join("auth_types", "info_circular_details.auth_id = auth_types.id")
						  ->join("emp_basic_details", "emp_basic_details.emp_no = user_details.id")
						  ->join("departments", "user_details.dept_id = departments.id")
						  ->join("designations", "designations.id = emp_basic_details.designation")
						  ->order_by("info_circular_details.posted_on", "desc")
						  ->get();

		return $query->result();
	}
}

/* End of file edit_circular_model.php */
/* Location: mis/application/models/edit_circular_model.php */