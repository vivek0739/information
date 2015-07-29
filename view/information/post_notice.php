
<?php
	$ui = new UI();
	$errors=validation_errors();
	if($errors!='')
		$this->notification->drawNotification('Validation Errors',validation_errors(),'error');
	$row = $ui->row()->open();
	
	$column1 = $ui->col()->width(2)->open();

	$column1->close();
	
	$column2 = $ui->col()->width(8)->open();
	$box = $ui->box()
			  
			  ->solid()	
			  ->uiType('primary')
			  ->open();
	$form = $ui->form()->action('information/post_notice/index/'.$auth_id)->extras('enctype="multipart/form-data"')->open();
	$star_notice=$ui->row()->open();
	//echo" Fields marked with <span style= 'color:red;'>*</span> are mandatory.";
	$star_notice->close();
	$compose_box=$ui->box()->icon($ui->icon('edit'))
					->title('Compose Notice')
					->solid()
					->uiType('primary')
					->open();
	$inputRow1 = $ui->row()->open();
		if($id->notice_id == NULL)
		{
			
			$ui->input()
			   ->label('Notice ID<span style= "color:red;"> *</span>')
			   ->type('text')
			   ->name('notice_ids')
			   ->required()
			   ->value('1')
			   ->disabled()
			   ->width(6)
			   ->show();

		}
		else
		{
			$ui->input()
			   ->type('text')
			   ->label('Notice ID<span style= "color:red;"> *</span>')
			   ->name('notice_ids')
			   ->required()
			   ->width(6)
			   ->value($id->notice_id + 1)
			   ->disabled()
			   ->show();			
		}

		 $ui->input()
		    ->type('text')
		    ->label('Notice Number<span style= "color:red;"> *</span>')
		    ->name('notice_no')
		    ->required()
		    ->width(6)
		    ->placeholder('Enter Notice Number  (Ex: CSE_NOTICE_10185)')
		    ->show();

	$inputRow1->close();

	$inputRow2= $ui->row()->open();
		 $ui->textarea()
		    ->label('Notice Subject<span style= "color:red;"> *</span>')
            ->placeholder('Enter the notice Subject in not more than 200 characters')
            ->name('notice_sub')
            ->required()->width(12)
            ->show();

	$inputRow2->close();

	$inputRow3 = $ui->row()->open();
     	 $ui->input()
		    ->label('Notice File<span style= "color:red;"> *</span>')
     	    ->type('file')
     	    ->name('notice_path')
     	    ->required()
     	    ->width(6)
     	    ->show();

		 $ui->datePicker()
			->name('last_date')
		    ->label('Last Date<span style= "color:red;"> *</span> (Atleast today)')			
			//->extras(min='date("Y-m-d")')
			->value(date("Y-mm-dd"))
			->dateFormat('yyyy-mm-dd')->width(6)
			->show();


	$inputRow3->close();
	echo"(Allowed Types: pdf, doc, docx, jpg, jpeg, png, xls, xlsx, csv and Max Size: 1.0 MB)";
	$value=1;
		if($id->notice_id != NULL)
		   $value = $id->notice_id +1;
				
				
		$ui->input()
		   ->type('hidden')
		   ->name('notice_id')
		   ->required()
		   ->value($value)
		   ->show();

    $compose_box->close();

    // post_box is box which will contain the posting procedure
/*****************************************************************************************************************************************************************************************************************************
***********************************************************/

   $post_box=$ui->box()
    			 ->id("post_box")
					->title('Post Notice')
					->icon($ui->icon("bullhorn"))
					->tooltip("Reload when something is not working.")
					->solid()
					->uiType('primary')
					->open();
	
		$inputRow4 = $ui->row()->open();

	//notice_col select the type of posting i.e single , group or general

			$col1 = $ui->col()->id('notice_col')->width(12)->open();
				$ui->select()
		  		   ->name('notice_cat')
		   		   ->id("notice_cat")
		   		   ->options(array($ui->option()->value('""')->text(' --------Whom to send------')->selected()->disabled(),
		   					$ui->option()->value('all')->text('All'),
		   					$ui->option()->value('emp')->text('All Employees'),
		   					$ui->option()->value('ft')->text('All faculty'),
		   					$ui->option()->value('stu')->text('All Students'),
		   					$ui->option()->value('ind')->text('Individual'),
		   					$ui->option()->value('grp')->text('Group(Customised)'),
		   					$ui->option()->value('gen')->text('Group(General)')))
				  ->required()
		          ->show();
			$col1->close();

	// innercol1 will be visible when individual opetion in notice_cat has been selected
			$inputRow4->close();
			$inputRow5 = $ui->row()->open();
			$innercol1 = $ui->col()->id('individual_col')->width(6)->open();
					$col1=$ui->col()->width(12)->open();
						$ui->input()
		    			   ->label('No. of person<span style= "color:red;"> *</span>')
     	    			   ->type('text')
     	    			   ->name('no_of_individuals')
     	    			   ->id("no_of_individuals")
     	    			   ->show();
   				    $col1->close();
   				    $r1col2 = $ui->col()->id('cant_rem_emp_id')->width(12)->m_width(6)->t_width(6)->open();
						?><a onClick="onclick_emp_id();" >
						<div align="right">
  							<?
							$ui->button()
								->value('Generate Emplyoee ID.')
	    						
	    						->id('emanage')
	    						->name('emanage')
	   						    ->show();

							?>
							<br/><br/>
						</div>
						</a><?
					$r1col2->close();
					$r1col3 = $ui->col()->id('cant_rem_admission_id')->width(12)->m_width(12)->t_width(12)->open();
						?><a onClick="onclick_stu_id();" >
						<div align="right">
							<?
							$ui->button()
								->value('Generate Admission NO.')
	    						
	    						->id('emanage')
	    						->name('emanage')
	   						    ->show();

							?>
						</div>
						</a><?
					$r1col3->close();
					
   					$col2=$ui->col()->width(12)->open();
    				
					$row2 = $ui->row()->id('search_eid')->style('display:none')->open();
							$r2col1 = $ui->col()->width(12)->open();
								$ui->select()
									->label('Department')
									->name('emp_dept')
									->id('emp_dept')
									->options(array($ui->option()->value('0')
											->text('Select Employee Department')->disabled()->selected()))
									->show();
							$r2col1->close();
					$row2->close();
					$row3 = $ui->row()->id('employee')->style('display:none')->open();
							$r3col1 = $ui->col()->width(12)->open();
								$ui->select()
									->label('Employee name')
									->name('employee_select')
									->id('employee_select')
									->options(array($ui->option()->value('0')->text('Select Employee')->disabled()->selected()))
									->show();
							$r3col1->close();
							$r1col1 = $ui->col()->id('emp_id_col')->width(12)->t_width(6)->m_width(6)->open();
							$ui->select()
								->label('Generated Employee Id')
								->name('emp_id')
								->id('emp_id')
								->options(array($ui->option()->value('0')->text('Emplyoee id will generated here <br/> just select ')->disabled()->selected()))
								->show();
							$r1col1->close();
					$row3->close();
					$col2->close();
					/*** for admission number***/					
					$col3=$ui->col()->width(12)->open();
    				
					$row2 = $ui->row()->id('search_sid')->style('display:none')->open();
							$r2col1 = $ui->col()->width(12)->open();
								$ui->select()
									->label('Department')
									->name('stu_dept')
									->id('stu_dept')
									->options(array($ui->option()->value('0')
											->text('Select Students Department')->disabled()->selected()))
									->show();
							$r2col1->close();
					$row2->close();
					$row3 = $ui->row()->id('courses_sid')->style('display:none')->open();
							$r2col1 = $ui->col()->width(12)->open();
								$ui->select()
									->label('Courses')
									->name('stu_course')
									->id('stu_course')
									->options(array($ui->option()->value('0')
											->text('Select Students Course')->disabled()->selected()))
									->show();
							$r2col1->close();
					$row3->close();
					$row4 = $ui->row()->id('students')->style('display:none')->open();
							$r3col1 = $ui->col()->width(12)->open();
								$ui->select()
									->label('Student name')
									->name('student_select')
									->id('student_select')
									->options(array($ui->option()->value('0')->text('Select Employee')->disabled()->selected()))
									->show();
							$r3col1->close();
							$r1col1 = $ui->col()->id('stu_id_col')->width(12)->t_width(6)->m_width(6)->open();
							$ui->select()
								->label('Generated Student Id')
								->name('stu_id')
								->id('stu_id')
								->options(array($ui->option()->value('0')->text('Emplyoee id will generated here <br/> just select ')->disabled()->selected()))
								->show();
							$r1col1->close();
					$row4->close();
			
						
						
					$col3->close();
   			$innercol1->close();
    
	 		$innerCol2 = $ui->col()->id("groups")->width(6)->open();
			$innerCol2->close();

			$inputRow5->close();

    //it is blank col which will be loaded by index fuction in script and entries in no_of individual will be greater than 1
/****************** group cotegories **********************************/
	$inputRow6 = $ui->row()->open();

	$innercol4= $ui->col()->id('add_grp_col')->width(12)->open();

	$array_options = array();
	$array_options[0] = $ui->option()->value('""')->text("Select Group")->disabled()->selected();
	$filter=array();
	foreach($group_notice as $filter_result)
  	 array_push($filter,$filter_result->group_id);
	$filter=array_unique($filter);
	foreach ($filter as $grp) 
		array_push($array_options,$ui->option()->value($grp)->text($grp));
										
		$ui->select()
			
		   ->name('grp_selection')
		   ->id("grp_selection")
		   ->containerId("cont_grp_selection")
		   ->options($array_options)
		   
		   ->show();
	
	
	$innercol4->close();
	$inputRow6->close();

/****************** general category *****************/
 $inputRow7 = $ui->row()->open();
	$innercol8 = $ui->col()->id('category_col')->width(6)->open();
		$ui->select()
		   ->label('Select Categories in a Department')
		   ->name('cat_selection')
		   ->id("cat_selection")
		   ->options(array($ui->option()->value('all')->text('All')->selected(),
		   					$ui->option()->value('emp')->text('Employees'),
		   					$ui->option()->value('stu')->text('Students')))
		 
		   ->show();
	$innercol8->close();


	$innercol9= $ui->col()->id('department_col')->width(6)->open();
		$array_options = array();
			$array_options[0] = $ui->option()->value('all')->text("All Department")->selected();
			foreach ($result_dept as $dept) 
				array_push($array_options,$ui->option()->value($dept->id)->text($dept->name));
										
				$ui->select()
				   ->label('Select Department')
				   ->name('dept_selection')
				   ->id("dept_selection")
				   ->containerId("cont_dept_selection")
				   ->options($array_options)

				   ->show();
	$innercol9->close();
	$innercol10= $ui->col()->id('department_col_stu')->width(6)->open();
		$array_options = array();
			$array_options[0] = $ui->option()->value('all')->text("All Department")->selected();
			foreach ($result_dept_stu as $dept) 
				array_push($array_options,$ui->option()->value($dept->id)->text($dept->name));
										
				$ui->select()
				   ->label('Select Department')
				   ->name('dept_selection_stu')
				   ->id("dept_selection_stu")
				   ->containerId("cont_dept_selection")
				   ->options($array_options)
				  
				   ->show();
	$innercol10->close();

	//for selecting courses when student are selected
	$innercol11=$ui->col()->id('course_col')->width(6)->open();
		$array_options = array();
						$array_options[0] = $ui->option()->value('all')->text("All Courses")->selected();
						foreach ($result_course as $course) 
						{
							array_push($array_options,$ui->option()->value($course->id)->text($course->name));
						}
							
										
							$ui->select()
								->label('Select Course')
								->name('course_selection1')
								->id("course_selection1")
								->options($array_options)
								->show();
	$innercol11->close();
	$innerCol13 = $ui->col()->id("sem")->width(6)->open();
	$innerCol13->close();

	$innercol14=$ui->col()->id('type_col')->width(6)->open();
							$ui->select()
								->label('Select type of Employee')
								->name('emp_selection1')
								->id("emp_selection1")
								->options(array($ui->option()->value('all')->text('All')->selected(),
												$ui->option()->value('ft')->text('faculty'),
		   										$ui->option()->value('nfta')->text('non faculty academic'),
		   										$ui->option()->value('nftn')->text('non faculty non academic')))
								->show();
	$innercol14->close();
	

	$inputRow7->close();
	$post_box->close();
?>
<center>
<?php
	 $ui->button()->icon($ui->icon("th"))
		->value('post')
	    ->uiType('primary')
	    ->submit()
	    ->name('mysubmit')
	    ->show();
	
	$form->close();
	$box->close();

	$column2->close();
	
	$row->close();
	$row2=$ui->row()->open();

	$column1 = $ui->col()->width(2)->open();

	$column1->close();
	
	$column2 = $ui->col()->width(8)->open();
	$innercol1=$ui->col()->width(4)->open();
?>
</center>
	<a href=<?php echo site_url('information/information_ajax/create_notice_group/'.strval($id_user).'/'.strval($auth_id));?> >
</center>
<?
	$ui->button()->icon($ui->icon('plus'))
		->value('Create Group')
	    ->uiType('success')
	    ->submit()
	    ->id('cmanage')
	    ->name('cmanage')
	    ->show();

?>
</a>
<?php
	$innercol1->close();
	$innercol2=$ui->col()->width(4)->open();
?>
<div align="center">
	<a href=<?php echo site_url('information/information_ajax/delete_notice_group/'.strval($id_user).'/'.strval($auth_id));?> >

<?
	$ui->button()->icon($ui->icon('trash-o'))
		->value('Delete Group')
	    ->uiType('danger')
	    ->submit()
	    ->id('dmanage')
	    ->name('dmanage')
	    ->show();

?>
</a>
</div>
<?php
	$innercol2->close();
$innercol2=$ui->col()->width(4)->open();
?>
<div align="right">
	<a href=<?php echo site_url('information/information_ajax/edit_notice_group/'.strval($id_user).'/'.strval($auth_id));?> >

<?
	$ui->button()->icon($ui->icon('pencil'))
		->value('Edit Group')
	    ->uiType('primary')
	    ->submit()
	    ->id('emanage')
	    ->name('emanage')
	    ->show();

?>
</a>
</div>
<?php
	$innercol2->close();
	$column2->close();
	$row2->close();


?>


<script type="text/javascript" language="javascript">
$(document).ready(function() {
	onload_emp_id();
	onload_stu_id();
	$('#employee_select').change(onclick_emp_nameid);
	$('#emp_dept').change(onclick_empname);
	$('#student_select').change(onclick_stu_nameid);
	
	$('#stu_dept').change(onclick_stucourse);
	$('#stu_course').change(onclick_stuname);
});
</script>
