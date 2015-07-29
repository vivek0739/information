<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Edit_minute extends MY_Controller
{
	function __construct()
	{
		parent::__construct(array('hod','est_ar','exam_dr','dt','dsw','admin','da1','hos'));
		$this->addJS("information/information_minutes.js");
		$this->addJS("information/minutes.js");
		$this->load->model('information/post_minute_help_model');
	}

	
	public function index($auth_id='',$minute_id='')
	{
		$this->addJS("student_view_report/stu_report_file.js");
		if($auth_id =='' || ($auth_id !='hod' && $auth_id !='dt' && $auth_id !='dsw' && $auth_id !='est_ar' && $auth_id !='exam_dr'
			&& $auth_id !='admin' && (($pos=strrpos($auth_id,"da1")) == FALSE) && $auth_id !='hos' ))
		{

			$this->session->set_flashdata('flashError','Access Denied!');
			redirect('home');
		}
		$this->load->model('information/post_minute_model','',TRUE);
			$data['id'] = $this->post_minute_model->get_max_minute_id();
			$data['result_dept']=$this->post_minute_help_model->get_depts();
			$data['result_dept_stu']=$this->post_minute_help_model->get_depts_stu();
			$data['result_course'] = $this->post_minute_help_model->get_course();
			$data['users']=$this->post_minute_help_model->getUsersByDeptAuth();
			$id_user=$this->session->userdata('id');
			$data['id_user']=$id_user;
			$data['group_notice']=$this->post_minute_help_model->get_groups($id_user);

			$data['auth_id'] = $auth_id;
		if($minute_id=='')
		{
			$this->load->model('information/edit_minute_model','',TRUE);
			//$data['secondLink'] = '<a href="'.base_url().'index.php/information/view_minute/index/archieved">List of Archieved minutes</a>';
			
			$data['minutes'] = $this->edit_minute_model->get_minutes($id_user,$auth_id);
			$data['auth_id'] = $auth_id;
			
			if(count($data['minutes']) == 0)
			{
				$this->session->set_flashdata('flashError','There is no any Meeting Minutes to edit.');
				redirect('home');
			}
				
			$this->drawHeader('Edit Meeting Minutes');
			$this->load->view('information/editMinute',$data);
			$this->drawFooter();
		}
		else
		{
			$this->load->model('information/view_minute_model','',TRUE);
			$data['no_of_individuals']=1;
			$data['group_name']=1;
			$data['d_general']=1;
			$data['d_general_stu']=1;
			$data['d_general_emp']=1;
			$data['minute_row'] = $this->view_minute_model->get_minute_row($minute_id);
			if(count($data['minute_row']) == 0)
			{
				$this->session->set_flashdata('flashError','There is no meeting minutes available with the minute id ('.$minute_id.')');
				redirect('home');
			}
			if($data['minute_row']->meeting_cat == 'ind')
			{
				$data['individuals']=$this->view_minute_model
										->get_individual($data['minute_row']->minutes_id);

				$data['no_of_individuals']=$data['individuals']->num_rows();
				
			}
			else if($data['minute_row']->meeting_cat == 'grp')
			{
			
				$data['group_name']=$this->view_minute_model->get_group_name($data['minute_row']->minutes_id);
				
			}
			else if($data['minute_row']->meeting_cat == 'gen')
			{
				$data['d_general']=$this->view_minute_model->get_general_data($data['minute_row']->minutes_id);
				
				if($data['d_general'][0]->meeting_cat=='stu')
					{
						$data['d_general_stu']=$this->view_minute_model->get_general_stu_data($data['minute_row']->minutes_id);
						
					}
				else
				$data['d_general_emp']=$this->view_minute_model->get_general_emp_data($data['minute_row']->minutes_id);
			}
			
			$this->drawHeader('Edit Meeting Minutes');
			$this->load->view('information/edit_minute',$data);
			$this->drawFooter();
		}
	}

	public function edit($minute_id,$auth_id)
	{
		if($minute_id =='')
		{
			$this->session->set_flashdata('flashError','Access Denied!');
			redirect('home');
		}
		$this->load->helper(array('form', 'url'));

		$this->load->library('form_validation');

		$this->form_validation->set_rules('minute_sub', 'Minute Subject', 'required');
		$this->form_validation->set_rules('minutes_no', 'Minute Number', 'required');
		
		$this->load->model('information/edit_minute_model','',TRUE);
		
		$minute=$this->edit_minute_model->getminutesByMinId($this->input->post('minutes_id'));
		
		if(count($minute) == 0)
		{
			$this->session->set_flashdata('flashError','There is no meeting minutes with the minute id ('.$minute_id.') to edit');
			redirect('home');
		}
		
		if($_FILES['minutes_path']['name'] != '')
		{
			
			$upload=$this->upload_file('minutes_path',$this->input->post('minutes_id'),$auth_id,$minute_id);
			if($upload)
			{
				//current date
				$date = date("Y-m-d H:i:s");
				
				$minute=$this->edit_minute_model->getminutesByMinId($this->input->post('minutes_id'));
				$old_file = $minute->minute_path;
				
				$data = array('minutes_id'=>$this->input->post('minutes_id'),
						  'minutes_no'=>$this->input->post('minutes_no'),
						  'meeting_type'=>$this->input->post('meeting_type'),
						  'meeting_cat'=>$this->input->post('meeting_cat'),
						  'minutes_path'=>$upload['file_name'],
						  'valid_upto'=>$this->input->post('valid_upto'),
						  'date_of_meeting'=>$this->input->post('date_of_meeting'),
						  'place_of_meeting'=>$this->input->post('place_of_meeting'),
						  'posted_on'=>$date,
						  'modification_value'=>($this->input->post('modification_value') + 1)
						  );
			    
				$this->edit_minute_model->insertM($data['minutes_id']);
				$this->edit_minute_model->update($data);
				$meeting_cat=$this->input->post('meeting_cat');
			$prev_cat=$this->input->post('prev_notice_cat');
			if($prev_cat=='ind')
			{
				$this->post_minute_help_model->delete_individual($data['minutes_id']);
			}
			else if($prev_cat=='grp')
			{
				$this->post_minute_help_model->delete_group($data['minutes_id']);
			}
			else if($prev_cat=='gen')
			{
				$this->post_minute_help_model->delete_general($data['minutes_id']);
					
			}
			if($meeting_cat=='ind')
			{
				$data['no_of_individuals'] = $this->input->post('no_of_individuals');
				$data['groups'] = array();
				for ($i = 1; $i <= $data['no_of_individuals']; $i++)
				{
					$data['groups'][$i] = array();
					$data['groups'][$i]['individual_name'] = $this->input->post('individual_name'.$i);
					print_r($data['groups'][$i]['individual_name']);
				}
				if($data['no_of_individuals'] == 0)
				{
					$this->post_minute_help_model->delete_notice_send_incorectly($data['minutes_id']);
					$this->session->set_flashdata('flashError','Missing no of people entry in individual section.');
					redirect('information/post_minute/index/'.$auth_id.'/'.$minute_id);
				}
				$this->post_minute_help_model->insert_individual($data);
			}
			else if($meeting_cat=='grp')
			{
				/********** for group has been choosen *******/
				/******group insert into databases*********/
				$data['today_date'] = date("Y-m-d H:i:s");
				$data['id_user']=$this->session->userdata('id');
				$data['grp_selection']=$this->input->post('grp_selection');
				$this->post_minute_help_model->get_group($data);
				
				
				
			}
			else if($meeting_cat=='gen')
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
				$this->post_minute_help_model->put_general($data);
			}

				//if($old_file)	unlink(APPPATH.'../assets/files/information/minute/'.$old_file);
				$this->session->set_flashdata('flashSuccess','Meeting minutes has been successfully updated.');
				redirect('home');
			
			}
		}
		else
		{
				//current date
			$date = date("Y-m-d H:i:s");
			$meeting_type=$this->input->post('meeting_type');
			if($meeting_type=='others')
				$meeting_type=$this->input->post('meeting_others');
			$data = array('minutes_id'=>$this->input->post('minutes_id'),
						  'minutes_no'=>$this->input->post('minutes_no'),
						  'meeting_type'=>$meeting_type,
						  'meeting_cat'=>$this->input->post('meeting_cat'),
						  'valid_upto'=>$this->input->post('valid_upto'),
						  'date_of_meeting'=>$this->input->post('date_of_meeting'),
						  'place_of_meeting'=>$this->input->post('place_of_meeting'),
						  'posted_on'=>$date,
						  'modification_value'=>($this->input->post('modification_value') + 1)
						  );
				
			$this->edit_minute_model->insertM($data['minutes_id']);
			$this->edit_minute_model->update($data);
			$data['minutes_id']=$this->input->post('minutes_id');
			/***** for Individual has been choosen *****/
			$meeting_cat=$this->input->post('meeting_cat');
			$prev_cat=$this->input->post('prev_notice_cat');
			if($prev_cat=='ind')
			{
				$this->post_minute_help_model->delete_individual($data['minutes_id']);
			}
			else if($prev_cat=='grp')
			{
				$this->post_minute_help_model->delete_group($data['minutes_id']);
			}
			else if($prev_cat=='gen')
			{
				$this->post_minute_help_model->delete_general($data['minutes_id']);
					
			}
			if($meeting_cat=='ind')
			{
				$data['no_of_individuals'] = $this->input->post('no_of_individuals');
				$data['groups'] = array();
				for ($i = 1; $i <= $data['no_of_individuals']; $i++)
				{
					$data['groups'][$i] = array();
					$data['groups'][$i]['individual_name'] = $this->input->post('individual_name'.$i);
					print_r($data['groups'][$i]['individual_name']);
				}
				if($data['no_of_individuals'] == 0)
				{
					$this->post_minute_help_model->delete_notice_send_incorectly($data['minutes_id']);
					$this->session->set_flashdata('flashError','Missing no of people entry in individual section.');
					redirect('information/post_minute/index/'.$auth_id.'/'.$minute_id);
				}
				$this->post_minute_help_model->insert_individual($data);
			}
			else if($meeting_cat=='grp')
			{
				/********** for group has been choosen *******/
				/******group insert into databases*********/
				$data['today_date'] = date("Y-m-d H:i:s");
				$data['id_user']=$this->session->userdata('id');
				$data['grp_selection']=$this->input->post('grp_selection');
				$this->post_minute_help_model->get_group($data);
				
				
				
			}
			else if($meeting_cat=='gen')
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
				$this->post_minute_help_model->put_general($data);
			}

			$this->session->set_flashdata('flashSuccess','Meeting Minutes has been successfully updated.');
			redirect('home');
		}		
	}	
	
	private function upload_file($name ='',$sno = 0,$auth_id,$minute_id)
	{
		$config['upload_path'] = 'assets/files/information/minute';
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
                    $filename='MINUTE_'.date('YmdHis').$sno.$ext;
                }
	        }
	        else
	        {
	        	$this->session->set_flashdata('flashError','ERROR: File Name not set.');
	        	redirect('information/post_minute/index/'.$auth_id.'/'.$minute_id);
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
				redirect('information/post_minute/index/'.$auth_id.'/'.$minute_id);
				return FALSE;
			}
			else
			{
				$upload_data = $this->upload->data();
				return $upload_data;
			}
	}
}
/* End of file edit_minute.php */
/* Location: mis/application/controllers/information/edit_minute.php */
