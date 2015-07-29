<?php if ( !defined ('BASEPATH')) exit ('No direct script access allowed');

class Information_ajax extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->addJS("information/information.js");
		$this->load->model('information/post_notice_help_model');
	}

	public function index($no_of_individuals)
	{
		$data['individuals']=NULL;
		$data['no_of_individuals'] = $no_of_individuals;
		$this->load->view('information/help_view/index',$data);
		
	}
	public function index_individual($no_of_individuals,$notice_id)
	{
		$this->load->model('information/view_notice_model');
		$data['individuals']=$this->view_notice_model
										->get_individual($notice_id)->result();
		
		$data['no_of_individuals'] = $no_of_individuals;
		//print_r($data['individuals']);
		$this->load->view('information/help_view/index_data',$data);
		
	}
	public function create_notice_group($id_user,$auth_id)
	{
		$query=$this->post_notice_help_model->create_group();
		$sno=1;
		$total_rows=$query->num_rows();
		foreach ($query->result() as $row) {
			$data_array[$sno] = array();
			$j=1;
			$data_array[$sno][$j++]=$row->id;
			$data_array[$sno][$j++]=$row->first_name;
			$data_array[$sno][$j++]=$row->middle_name;
			$data_array[$sno][$j++]=$row->last_name;
			$data_array[$sno][$j++]=$row->dept_name;
			$sno++;
		}
		$data['data_array']=$data_array;
		$data['total_rows']=$total_rows;
		$data['id_user']=$id_user;
		$data['auth_id']=$auth_id;
		$data['date']=date("Y-m-d H:i:s");
		$this->drawHeader ("Create Group");
		$this->load->view('information/group/create_group.php',$data);
		$this->drawFooter ();
		
	}
	public function delete_notice_group($id_user,$auth_id)
	{
		$data['id_user']=$id_user;
		$data['auth_id']=$auth_id;
		$data['group_notice']=$this->post_notice_help_model->get_groups($id_user);
		$this->drawHeader ("");
		$this->load->view('information/group/delete_group.php',$data);
		$this->drawFooter ();

	}
	public function edit_notice_group($id_user,$auth_id)
	{
		$query=$this->post_notice_help_model->create_group();
		$sno=1;
		$total_rows=$query->num_rows();
		foreach ($query->result() as $row) {
			$data_array[$sno] = array();
			$j=1;
			$data_array[$sno][$j++]=$row->id;
			$data_array[$sno][$j++]=$row->first_name;
			$data_array[$sno][$j++]=$row->middle_name;
			$data_array[$sno][$j++]=$row->last_name;
			$data_array[$sno][$j++]=$row->dept_name;
			$sno++;
		}
		$data['data_array']=$data_array;
		$data['total_rows']=$total_rows;
		$data['id_user']=$id_user;
		$data['auth_id']=$auth_id;
		$data['group_notice']=$this->post_notice_help_model->get_groups($id_user);
		
		$data['date']=date("Y-m-d H:i:s");
		$this->drawHeader ("Edit Group");
		$this->load->view('information/group/edit_group.php',$data);
		$this->drawFooter ();

	}
	public function create_complete($id_user,$auth_id)
	{
		
		$this->session->set_flashdata('flashSuccess','group '.$data.' has been created.');
		redirect('information/post_notice/index/'.$auth_id);
	}

	public function delete_selected_group($id_user,$auth_id)
	{
		$data=$this->input->post('grp_selection');
		$this->post_notice_help_model->del_group($data,$id_user);
		$this->session->set_flashdata('flashSuccess','group '.$data.' has been deleted.');
		redirect('information/post_notice/index/'.$auth_id);
	}
	public function add_member($data1,$data2,$data3,$data4)
	{
		$no_groups=$this->post_notice_help_model->no_of_groups($data2);
		$data['auth_id']=$data4;
		$data['id_user']=$data2;
		if($no_groups>10)
		{
			$this->load->view('information/group/group_exceed',$data);
			$this->drawFooter ();
		}
		else
		{
			$data4=date("Y-m-d H:i:s");
			$this->post_notice_help_model->put_group_class($data1,$data2,$data3,$data4);
			$data['members']=$this->post_notice_help_model->get_group_member($data1,$data2);
			$data['data1']=$data1;
			$data['data2']=$data2;
			$data['data4']=$data4;
		
			$this->load->view('information/group/group_member',$data);
			$this->drawFooter ();
		}
		
	}
	function add_member_all($data1,$data2)
	{
		$data4=date("Y-m-d H:i:s");
		$data['members']=$this->post_notice_help_model->get_group_member($data1,$data2);
		$data['data1']=$data1;
		$data['data2']=$data2;
		$data['data4']=$data4;
		
		$this->load->view('information/group/group_member',$data);
		$this->drawFooter ();
	}
	/*public function search_group($data1);
	{
		$result=$this->post_notice_help_model->search_group($data1);
		if($result==1)
	}*/
	public function rm_member($data1,$data2,$data3,$data4)
	{

		$this->post_notice_help_model->del_group_class($data1,$data2,$data3,$data4);
		$data['members']=$this->post_notice_help_model->get_group_member($data1,$data2);
		$data['data1']=$data1;
		$data['data2']=$data2;
		$data['data4']=$data4;
		if($data['members']==NULL)
		{
			
			$this->post_notice_help_model->del_group_notice($data1,$data2);
		}
		$this->load->view('information/group/group_member',$data);
		$this->drawFooter ();
	}
	public function semester($course,$sem='')
	{
		//print_r($course);
		if($sem=='undefined' || $sem=='0')
		{
			$sem='all';

		}
			
		$data['course']=$this->post_notice_help_model->get_course_where($course);
		$data['sem']=$sem;
		$this->load->view('information/help_view/semester',$data);
		
	}
	public function get_courses($dept,$courses='')
	{
		
		if($courses=='undefined' || $courses=='0')
		{
			$courses='all';

		}
		$this->load->model('student_view_report/report_model','',TRUE);
		$result = $this->report_model->get_course_bydept($dept);
		$data['course'] = $result;
		$data['courses']=$courses;
		
		if($dept=='all')
		{
			$data['course']=$this->post_notice_help_model->get_course();
			$this->load->view('information/help_view/courses',$data);
		}
		else
		{
			$this->load->view('information/help_view/courses',$data);
		}
		
	}
	public function del_group($group_name)
	{
		$this->post_notice_help_model->del_group($group_name);
	}
	public function get_dept()
	{
		$this->load->model('departments_model','',TRUE);
		$data['departments']=$this->departments_model->get_departments();
		$this->load->view('information/help_view/dept_list',$data);
	}
	public function get_depts()
	{
		$this->load->model('departments_model','',TRUE);
		$data['departments']=$this->departments_model->get_departments('academic');
		$this->load->view('information/help_view/dept_list',$data);
	}
	public function get_emp_id()
	{
		$this->load->model('employee/emp_basic_details_model','',TRUE);
		$data['members']=$this->emp_basic_details_model->getAllEmployeesId();
		$this->load->view('information/help_view/member_id_list',$data);
	}
	public function get_stu_id()
	{
		$this->load->model('student/student_education_details_model','',TRUE);
		$data['members']=$this->student_education_details_model->getAllStudentId();

		$this->load->view('information/help_view/member_id',$data);
	}
}