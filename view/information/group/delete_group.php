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
	$form = $ui->form()->action('information/information_ajax/delete_selected_group/'.$id_user.'/'.$auth_id)->extras('enctype="multipart/form-data"')->open();
	$star_notice=$ui->row()->open();
	//echo" Fields marked with <span style= 'color:red;'>*</span> are mandatory.";
	$star_notice->close();


	$post_box=$ui->box()
    			 ->id("post_box")
					->title('Delete Group')
					->solid()
					->uiType('primary')
					->open();
	
	$inputRow4 = $ui->row()->open();


	/****** select a group to delete ***/
	$innercol4= $ui->col()->id('add_grp_col')->width(12)->open();
		$row1=$ui->col()->width(6)->open();
		$array_options = array();
		$array_options[0] = $ui->option()->value('""')->text("Select Group")->disabled()->selected();
		$filter=array();
		foreach($group_notice as $filter_result)
  	 		array_push($filter,$filter_result->group_id);
		$filter=array_unique($filter);
		foreach ($filter as $grp) 
			array_push($array_options,$ui->option()->value($grp)->text($grp));
										
			$ui->select()
				->required()
				->name('grp_selection')
		   		->id("grp_selection")
		   		->containerId("cont_grp_selection")
		   		->options($array_options)
		   		->show();
		$row1->close();

	$innercol4->close();
	$column2=$ui->col()->width(12)->open();
?>
<br/>
<div id="move_details_of_sent_files" name='move_details_of_sent_files'>
	
</div>
<br/><br/>
<?php
	
			
	$column2->close();

	$inputRow4->close();
	$post_box->close();
?>
<center>
<?php
	 $ui->button()->icon($ui->icon('trash-o'))
		->value('Delete')
	    ->uiType('danger')
	    ->id('delete')
	    
	    ->show();
	 echo '<br/><br/>';
	 $ui->button()->icon($ui->icon('trash-o'))
		->value('Are You Sure')
	    ->uiType('danger')
	    ->id('delete_sure')
	    ->submit()
	    ->name('mysubmit')
	    ->show();
	
	$form->close();
	$box->close();

	$column2->close();
	
	$row->close();
?>
<script type="text/javascript">
$('#delete_sure').hide();
$('#delete').on('click',function(){
	$('#delete_sure').show();
});
$("#grp_selection").on('change', function()
{
	$('#delete_sure').hide();
	add_member_all(this.value,'<?php echo $id_user; ?> ');
});
</script>