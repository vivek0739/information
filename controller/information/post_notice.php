<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post_notice extends MY_Controller
{
	function __construct()
	{
		parent::__construct(array('hod','est_ar','exam_dr','dt','dsw','admin','da1','hos'));
		//added the javascript file to call index javascript fuction in post_notice view
		$this->addJS("information/information.js");
		$this->addJS("information/notice.js");
		$this->load->model('information/post_notice_help_model');

	}

	public function index($auth_id='')
	{
		$this->addJS("student_view_report/stu_report_file.js");
		//$pos=strrpos($auth_id,"DA1");
	if($auth_id =='' || ($auth_id !='hod' && $auth_id !='dt' && $auth_id !='dsw' && $auth_id !='est_ar' && $auth_id !='exam_dr'
			&& $auth_id !='admin' && (($pos=strrpos($auth_id,"da1")) == FALSE) && $auth_id !='hos' ))
		{

			$this->session->set_flashdata('flashError','Access Denied!');
			redirect('home');
		}
		$this->load->helper(array('form', 'url'));

		$this->load->library('form_validation');

		$this->form_validation->set_rules('notice_no', 'Notice Number', 'required');
		$this->form_validation->set_rules('notice_sub', 'Subject', 'required');
		//$this->form_validation->set_rules('notice_path', 'File', 'required');
		$this->form_validation->set_rules('last_date', 'Last Date', 'required');

		
		$this->load->model('information/post_notice_model','',TRUE);

		$this->load->model('course_structure/basic_model','',TRUE);
		
		//title for the page
		//$header['title']='Post Notice';
		//header left blank
		$this->drawHeader("");

		
		if ($this->form_validation->run() == FALSE)
		{
			$data['id'] = $this->post_notice_model->get_max_notice_id();

			$data['result_dept']=$this->post_notice_help_model->get_depts();
			$data['result_dept_stu']=$this->post_notice_help_model->get_depts_stu();
			$data['result_course'] = $this->post_notice_help_model->get_course();
			$data['users']=$this->post_notice_help_model->getUsersByDeptAuth();
			$id_user=$this->session->userdata('id');
			$data['id_user']=$id_user;
			$data['group_notice']=$this->post_notice_help_model->get_groups($id_user);

			$data['auth_id'] = $auth_id;
			$this->load->view('information/post_notice',$data);
		}
		else
		{
			$upload=$this->upload_file('notice_path',$this->input->post('notice_id'),$auth_id);
			if($upload)
			{
				$date = date("Y-m-d H:i:s");
				$data = array('notice_id'=>$this->input->post('notice_id'),
						  'notice_no'=>$this->input->post('notice_no'),
						  'notice_cat'=>$this->input->post('notice_cat'), 
						  'notice_sub'=>$this->input->post('notice_sub'),
						  'notice_path'=>$upload['file_name'],
						  'issued_by'=>$this->session->userdata('id'),
						  'auth_id'=>$auth_id,
						  'posted_on'=>$date,
						  'last_date'=>$this->input->post('last_date'),
						  'modification_value'=>0
						  );
			
			$this->post_notice_model->insert($data);

			$data['notice_id']=$this->input->post('notice_id');
			/***** for Individual has been choosen *****/
			$notice_cat=$this->input->post('notice_cat');
			if($notice_cat=='ind')
			{
				$data['no_of_individuals'] = $this->input->post('no_of_individuals');
				$data['groups'] = array();
				for ($i = 1; $i <= $data['no_of_individuals']; $i++)
				{
					$data['groups'][$i] = array();
					$data['groups'][$i]['individual_name'] = $this->input->post('individual_name'.$i);
				}
				if($data['no_of_individuals'] == 0)
				{
					$this->post_notice_help_model->delete_notice_send_incorectly($data['notice_id']);
					$this->session->set_flashdata('flashError','Missing no of people entry in individual section.');
					redirect('information/post_notice/index/'.$auth_id);
				}
				$this->post_notice_help_model->insert_individual($data);
			}
			else if($notice_cat=='grp')
			{
				/********** for group has been choosen *******/
				/******group insert into databases*********/
				$data['today_date'] = date("Y-m-d H:i:s");
				$data['id_user']=$this->session->userdata('id');
				$data['grp_selection']=$this->input->post('grp_selection');
				$this->post_notice_help_model->get_group($data);
				
				
				
			}
			else if($notice_cat=='gen')
			{
				/**************for general categories ******************/
				$data['cat_selection']=$this->input->post('cat_selection');
				if($data['cat_selection']=='stu')
				{
					$data['dept_selection']=$this->input->post('dept_selection_stu');
				}
				else
				{
					$data['dept_selection']=$this->input->post('dept_selection');
				}
				$data['course_selection1']=$this->input->post('course_selection1');
				$data['emp_selection1']=$this->input->post('emp_selection1');
				$data['sem_selection']=$this->input->post('sem_selection');
				$this->post_notice_help_model->put_general($data);
			}

			$this->session->set_flashdata('flashSuccess','Notice has been successfully posted.');
			redirect('home');
			//$this->load->view('information/post_notice_success');
			}
		}
		$this->drawFooter();
	}
	
	
	private function upload_file($name ='',$sno = 0,$auth_id='')
	{
		$config['upload_path'] = 'assets/files/information/notice';
		$config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png|xls|xlsx|csv';
		$config['max_size']  = '1050';

			if(isset($_FILES[$name]['name']))
        	{
                if($_FILES[$name]['name'] == "")
            		$filename = "";
                else
				{
                    $filename=$this->security->sanitize_filename(strtolower($_FILES[$name]['name']));
                    $ext =  strrchr( $filename, '.' ); // Get the extension from the filename.
                    $filename='NOTICE_'.date('YmdHis').$sno.$ext;
                }
	        }
	        else
	        {
	        	$this->session->set_flashdata('flashError','ERROR: File Name not set.');
	        	redirect('information/post_notice/index/'.$auth_id);
				return FALSE;
	        }
	   
			$config['file_name'] = $filename;
			//$this->load->view('welcome_message',array('d'=>array('photo_image'=>$_FILES,'config'=>$config)));
			//return FALSE;

			if(!is_dir($config['upload_path']))	//create the folder if it's not already exists
			{
				mkdir($config['upload_path'],0777,TRUE);
			}

			$this->load->library('upload', $config);

			if ( ! $this->upload->do_multi_upload($name))		//do_multi_upload is back compatible with do_upload
			{
				$this->session->set_flashdata('flashError',$this->upload->display_errors('',''));
				redirect('information/post_notice/index/'.$auth_id);
				return FALSE;
			}
			else
			{
				$upload_data = $this->upload->data();
				return $upload_data;
			}
	}
}
/* End of file post_notice.php */
/* Location: mis/application/controllers/information/post_notice.php */
