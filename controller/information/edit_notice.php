<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Edit_notice extends MY_Controller
{
	function __construct()
	{
		parent::__construct(array('hod','est_ar','exam_dr','dt','dsw','admin','da1','hos'));
		$this->addJS("information/information.js");
		$this->addJS("information/notice.js");
		$this->load->model('information/post_notice_help_model');
	}

	public function index($auth_id='',$notice_id='')
	{
		if($auth_id =='' || ($auth_id !='hod' && $auth_id !='dt' && $auth_id !='dsw' && $auth_id !='est_ar' && $auth_id !='exam_dr'
			&& $auth_id !='admin' && (($pos=strrpos($auth_id,"da1")) == FALSE) && $auth_id !='hos' ))
		{

			$this->session->set_flashdata('flashError','Access Denied!');
			redirect('home');
		}

			$data['result_dept']=$this->post_notice_help_model->get_depts();
			$data['result_dept_stu']=$this->post_notice_help_model->get_depts_stu();
			$data['result_course'] = $this->post_notice_help_model->get_course();
			$data['users']=$this->post_notice_help_model->getUsersByDeptAuth();
			$id_user=$this->session->userdata('id');
			$data['id_user']=$id_user;
			$data['group_notice']=$this->post_notice_help_model->get_groups($id_user);
			$data['auth_id'] = $auth_id;

		if($notice_id=='')
		{
			$this->load->model('information/edit_notice_model','',TRUE);
			//$data['secondLink'] = '<a href="'.base_url().'index.php/information/view_notice/index/archieved">List of Archieved Notices</a>';
			
			$data['notices'] = $this->edit_notice_model->get_notices($id_user,$auth_id);
			$data['auth_id'] = $auth_id;
			
			if(count($data['notices']) == 0)
			{
				$this->session->set_flashdata('flashError','There is no any notice to edit.');
				redirect('home');
			}
				
			$this->drawHeader('Edit Notice');
			$this->load->view('information/editNotice',$data);
			$this->drawFooter();

		}
		else
		{
			$this->load->model('information/view_notice_model','',TRUE);
			$data['no_of_individuals']=1;
			$data['group_name']=1;
			$data['d_general']=1;
			$data['d_general_stu']=1;
			$data['d_general_emp']=1;
			$data['notice_row'] = $this->view_notice_model->get_notice_row($notice_id);
			if(count($data['notice_row']) == 0)
			{
				$this->session->set_flashdata('flashError','There is no notice available with the notice id ('.$notice_id.')');
				redirect('home');
			}
			if($data['notice_row']->notice_cat == 'ind')
			{
				$data['individuals']=$this->view_notice_model
										->get_individual($data['notice_row']->notice_id);

				$data['no_of_individuals']=$data['individuals']->num_rows();
				
			}
			else if($data['notice_row']->notice_cat == 'grp')
			{
			
				$data['group_name']=$this->view_notice_model->get_group_name($data['notice_row']->notice_id);
				
			}
			else if($data['notice_row']->notice_cat == 'gen')
			{
				$data['d_general']=$this->view_notice_model->get_general_data($data['notice_row']->notice_id);
				
				if($data['d_general'][0]->notice_cat=='stu')
					{
						$data['d_general_stu']=$this->view_notice_model->get_general_stu_data($data['notice_row']->notice_id);
						
					}
				else
				$data['d_general_emp']=$this->view_notice_model->get_general_emp_data($data['notice_row']->notice_id);
			}

			$this->drawHeader('Edit Notice');
			$this->load->view('information/edit_notice',$data);
			$this->drawFooter();
		}
	}

	public function edit($notice_id,$auth_id)
	{
		if($notice_id =='')
		{
			$this->session->set_flashdata('flashError','Access Denied!');
			redirect('home');
		}
		$this->load->helper(array('form', 'url'));

		$this->load->library('form_validation');

		$this->form_validation->set_rules('notice_sub', 'Notice Subject', 'required');
		$this->form_validation->set_rules('notice_no', 'Notice Number', 'required');
		
		$this->load->model('information/edit_notice_model','',TRUE);
		
		$notice=$this->edit_notice_model->getnoticesByMinId($this->input->post('notice_id'));
		
		if(count($notice) == 0)
		{
			$this->session->set_flashdata('flashError','There is no notice with the notice id ('.$notice_id.') to edit');
			redirect('home');
		}
		
		if($_FILES['notice_path']['name'] != '')
		{
			
			$upload=$this->upload_file('notice_path',$this->input->post('notice_id'),$auth_id,$notice_id);
			if($upload)
			{
				//current date
				$date = date("Y-m-d H:i:s");
				
				$notice=$this->edit_notice_model->getnoticesByMinId($this->input->post('notice_id'));
				$old_file = $notice->notice_path;
				
				$data = array('notice_id'=>$this->input->post('notice_id'),
						  'notice_no'=>$this->input->post('notice_no'),
						  'notice_sub'=>$this->input->post('notice_sub'),
						  'notice_cat'=>$this->input->post('notice_cat'),
						  'notice_path'=>$upload['file_name'],
						  'last_date'=>$this->input->post('last_date'),
						  'posted_on'=>$date,
						  'modification_value'=>($this->input->post('modification_value') + 1)
						  );
			    
				$this->edit_notice_model->insertM($data['notice_id']);
				$this->edit_notice_model->update($data);
				
			$prev_cat=$this->input->post('prev_notice_cat');
			if($prev_cat=='ind')
			{
				$this->post_notice_help_model->delete_individual($data['notice_id']);
			}
			else if($prev_cat=='grp')
			{
				$this->post_notice_help_model->delete_group($data['notice_id']);
			}
			else if($prev_cat=='gen')
			{
				$this->post_notice_help_model->delete_general($data['notice_id']);
					
			}
			$notice_cat=$this->input->post('notice_cat');
			$data['notice_id']=$this->input->post('notice_id');
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
						redirect('information/edit_notice/index/'.$auth_id.'/'.$notice_id);
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

					$data['course_selection1']=$this->input->post('course_selection');
					$data['emp_selection1']=$this->input->post('emp_selection1');
					$data['sem_selection']=$this->input->post('sem_selection');
					$this->post_notice_help_model->put_general($data);
				}
				
				//if($old_file)	unlink(APPPATH.'../assets/files/information/notice/'.$old_file);
				$this->session->set_flashdata('flashSuccess','Notice has been successfully updated.');
				redirect('home');
			
			}
		}
		else
		{
				//current date
			$date = date("Y-m-d H:i:s");
			
			$data = array('notice_id'=>$this->input->post('notice_id'),
					  'notice_no'=>$this->input->post('notice_no'),
					  'notice_sub'=>$this->input->post('notice_sub'),
					  'notice_cat'=>$this->input->post('notice_cat'),
					  'last_date'=>$this->input->post('last_date'),
					  'posted_on'=>$date,
					  'modification_value'=>($this->input->post('modification_value') + 1)
					  );
				
			$this->edit_notice_model->insertM($data['notice_id']);
			$this->edit_notice_model->update($data);
			$notice_cat=$this->input->post('notice_cat');
			$prev_cat=$this->input->post('prev_notice_cat');
			if($prev_cat=='ind')
			{
				$this->post_notice_help_model->delete_individual($data['notice_id']);
			}
			else if($prev_cat=='grp')
			{
				$this->post_notice_help_model->delete_group($data['notice_id']);
			}
			else if($prev_cat=='gen')
			{
				$this->post_notice_help_model->delete_general($data['notice_id']);
					
			}
			$data['notice_id']=$this->input->post('notice_id');
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
						redirect('information/edit_notice/index/'.$auth_id.'/'.$notice_id);
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
				$this->session->set_flashdata('flashSuccess','Notice has been successfully updated.');
				redirect('home');
		}		
	}	

	
	private function upload_file($name ='',$sno = 0,$auth_id,$notice_id)
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
	        	redirect('information/edit_notice/index/'.$auth_id.'/'.$notice_id);
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
				redirect('information/edit_notice/index/'.$auth_id.'/'.$notice_id);
				return FALSE;
			}
			else
			{
				$upload_data = $this->upload->data();
				return $upload_data;
			}
	}
}
/* End of file edit_notice.php */
/* Location: mis/application/controllers/information/edit_notice.php */
