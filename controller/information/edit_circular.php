<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Edit_circular extends MY_Controller
{
	function __construct()
	{
		parent::__construct(array('hod','est_ar','exam_dr','dt','dsw','admin','da1','hos'));
		$this->addJS("information/information_circular.js");
		$this->addJS("information/circular.js");
		$this->load->model('information/post_circular_help_model');
	}

	
	public function index($auth_id='',$circular_id='')
	{
		if($auth_id =='' || ($auth_id !='hod' && $auth_id !='dt' && $auth_id !='dsw' && $auth_id !='est_ar' && $auth_id !='exam_dr'
			&& $auth_id !='admin' && (($pos=strrpos($auth_id,"da1")) == FALSE) && $auth_id !='hos' ))
		{

			$this->session->set_flashdata('flashError','Access Denied!');
			redirect('home');
		}
			$data['result_dept']=$this->post_circular_help_model->get_depts();
			$data['result_dept_stu']=$this->post_circular_help_model->get_depts_stu();
			$data['result_course'] = $this->post_circular_help_model->get_course();
			$data['users']=$this->post_circular_help_model->getUsersByDeptAuth();
			$id_user=$this->session->userdata('id');
			$data['id_user']=$id_user;
			$data['group_notice']=$this->post_circular_help_model->get_groups($id_user);

			$data['auth_id'] = $auth_id;
		if($circular_id=='')
		{
			$this->load->model('information/edit_circular_model','',TRUE);
			//$data['secondLink'] = '<a href="'.base_url().'index.php/information/view_circular/index/archieved">List of Archieved circulars</a>';
			
			$data['circulars'] = $this->edit_circular_model->get_circulars($id_user,$auth_id);
			$data['auth_id'] = $auth_id;
			
			if(count($data['circulars']) == 0)
			{
				$this->session->set_flashdata('flashError','There is no any Circular to edit.');
				redirect('home');
			}
				
			$this->drawHeader('Edit Circular');
			$this->load->view('information/editCircular',$data);
			$this->drawFooter();
		}
		else
		{
			$this->load->model('information/view_circular_model','',TRUE);
			$data['no_of_individuals']=1;
			$data['group_name']=1;
			$data['d_general']=1;
			$data['d_general_stu']=1;
			$data['d_general_emp']=1;
			$data['circular_row'] = $this->view_circular_model->get_circular_row($circular_id);
			if(count($data['circular_row']) == 0)
			{
				$this->session->set_flashdata('flashError','There is no Circular available with the circular id ('.$circular_id.')');
				redirect('home');
			}

			if($data['circular_row']->circular_cat == 'ind')
			{
				$data['individuals']=$this->view_circular_model
										->get_individual($data['circular_row']->circular_id);

				$data['no_of_individuals']=$data['individuals']->num_rows();
				
			}
			else if($data['circular_row']->circular_cat == 'grp')
			{
				
				$data['group_name']=$this->view_circular_model->get_group_name($data['circular_row']->circular_id);
				
			}
			else if($data['circular_row']->circular_cat == 'gen')
			{
				$data['d_general']=$this->view_circular_model->get_general_data($data['circular_row']->circular_id);
				
				if($data['d_general'][0]->circular_cat=='stu')
					{
						$data['d_general_stu']=$this->view_circular_model->get_general_stu_data($data['circular_row']->circular_id);
						
					}
				else{
				$data['d_general_emp']=$this->view_circular_model->get_general_emp_data($data['circular_row']->circular_id);
				
				}
			}
			
			$this->drawHeader('Edit Circular');

			$this->load->view('information/edit_circular',$data);
			$this->drawFooter();
		}
	}

	public function edit($circular_id,$auth_id)
	{
		if($circular_id =='')
		{
			$this->session->set_flashdata('flashError','Access Denied!');
			redirect('home');
		}
		$this->load->helper(array('form', 'url'));

		$this->load->library('form_validation');

		$this->form_validation->set_rules('circular_sub', 'Circular Subject', 'required');
		$this->form_validation->set_rules('circular_no', 'Circular Number', 'required');
		
		$this->load->model('information/edit_circular_model','',TRUE);
		
		$circular=$this->edit_circular_model->getcircularsByMinId($this->input->post('circular_id'));
		
		if(count($circular) == 0)
		{
			$this->session->set_flashdata('flashError','There is no Circular with the circular id ('.$circular_id.') to edit');
			redirect('home');
		}
		
		if($_FILES['circular_path']['name'] != '')
		{
			
			$upload=$this->upload_file('circular_path',$this->input->post('circular_id'),$auth_id,$circular_id);
			if($upload)
			{
				//current date
				$date = date("Y-m-d H:i:s");
				
				$circular=$this->edit_circular_model->getcircularsByMinId($this->input->post('circular_id'));
				$old_file = $circular->circular_path;
				
				$data = array('circular_id'=>$this->input->post('circular_id'),
						  'circular_no'=>$this->input->post('circular_no'),
						  'circular_sub'=>$this->input->post('circular_sub'),
						  'circular_cat'=>$this->input->post('circular_cat'),
						  'circular_path'=>$upload['file_name'],
						  'valid_upto'=>$this->input->post('valid_upto'),
						  'posted_on'=>$date,
						  'modification_value'=>($this->input->post('modification_value') + 1)
						  );
			    
				$this->edit_circular_model->insertM($data['circular_id']);
				$this->edit_circular_model->update($data);
				$circular_cat=$this->input->post('notice_cat');
				$prev_cat=$this->input->post('prev_circular_cat');
				if($prev_cat=='ind')
				{
					$this->post_circular_help_model->delete_individual($data['circular_id']);
				}
				else if($prev_cat=='grp')
				{
					$this->post_circular_help_model->delete_group($data['circular_id']);
				}
				else if($prev_cat=='gen')
				{
					$this->post_circular_help_model->delete_general($data['circular_id']);
					
				}
				$data['circular_id']=$this->input->post('circular_id');
				if($circular_cat=='ind')
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
						$this->post_circular_help_model->delete_notice_send_incorectly($data['circular_id']);
						$this->session->set_flashdata('flashError','Missing no of people entry in individual section.');
						redirect('information/edit_circular/index/'.$auth_id.'/'.$circular_id);
					}
					$this->post_circular_help_model->insert_individual($data);
				}
				else if($circular_cat=='grp')
				{
					/********** for group has been choosen *******/
					/******group insert into databases*********/
					$data['today_date'] = date("Y-m-d H:i:s");
					$data['id_user']=$this->session->userdata('id');
					$data['grp_selection']=$this->input->post('grp_selection');
					$this->post_circular_help_model->get_group($data);
				}
				else if($ciruclar_cat=='gen')
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
					$this->post_circular_help_model->put_general($data);
				}
				//if($old_file)	unlink(APPPATH.'../assets/files/information/circular/'.$old_file);
				$this->session->set_flashdata('flashSuccess','Circular has been successfully updated.');
				redirect('home');
			
			}
		}
		else
		{
				//current date
			$date = date("Y-m-d H:i:s");
			
			$data = array('circular_id'=>$this->input->post('circular_id'),
					  'circular_no'=>$this->input->post('circular_no'),
					  'circular_sub'=>$this->input->post('circular_sub'),
					  'circular_cat'=>$this->input->post('circular_cat'),
					  'valid_upto'=>$this->input->post('valid_upto'),
					  'posted_on'=>$date,
					  'modification_value'=>($this->input->post('modification_value') + 1)
					  );
				
			$this->edit_circular_model->insertM($data['circular_id']);
			$this->edit_circular_model->update($data);
				$circular_cat=$this->input->post('circular_cat');
				$prev_cat=$this->input->post('prev_circular_cat');
				if($prev_cat=='ind')
				{
					$this->post_circular_help_model->delete_individual($data['circular_id']);
				}
				else if($prev_cat=='grp')
				{
					$this->post_circular_help_model->delete_group($data['circular_id']);
				}
				else if($prev_cat=='gen')
				{
					$this->post_circular_help_model->delete_general($data['circular_id']);
					
				}
				$data['circular_id']=$this->input->post('circular_id');
				if($circular_cat=='ind')
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
						$this->post_circular_help_model->delete_notice_send_incorectly($data['notice_id']);
						$this->session->set_flashdata('flashError','Missing no of people entry in individual section.');
						redirect('information/edit_circular/index/'.$auth_id.'/'.$circular_id);
					}
					$this->post_circular_help_model->insert_individual($data);
				}
				else if($circular_cat=='grp')
				{
					/********** for group has been choosen *******/
					/******group insert into databases********/
					$data['today_date'] = date("Y-m-d H:i:s");
					$data['id_user']=$this->session->userdata('id');
					$data['grp_selection']=$this->input->post('grp_selection');
					$this->post_circular_help_model->get_group($data);
				}
				else if($circular_cat=='gen')
				{
					/**************for general categories *****************/
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
					$this->post_circular_help_model->put_general($data);
				}
			$this->session->set_flashdata('flashSuccess','Circular has been successfully updated.');
			redirect('home');
		}		
	}	
	
	
	private function upload_file($name ='',$sno = 0,$auth_id,$circular_id)
	{
		$config['upload_path'] = 'assets/files/information/circular';
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
                    $filename='CIRCULAR_'.date('YmdHis').$sno.$ext;
                }
	        }
	        else
	        {
	        	$this->session->set_flashdata('flashError','ERROR: File Name not set.');
	        	redirect('information/edit_circular/index/'.$auth_id.'/'.$circular_id);
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
				redirect('information/edit_circular/index/'.$auth_id.'/'.$circular_id);
				return FALSE;
			}
			else
			{
				$upload_data = $this->upload->data();
				return $upload_data;
			}
	}
}
/* End of file edit_circular.php */
/* Location: mis/application/controllers/information/edit_circular.php */
