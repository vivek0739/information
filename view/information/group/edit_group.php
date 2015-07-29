<?php
	$ui= new UI();
	$outer_row=$ui->row()->open();
	$column1=$ui->col()->width(1)->open();
	$column1->close();
	$column2=$ui->col()->width(10)->open();

	$innercol4= $ui->col()->id('add_grp_col')->width(12)->open();
		$row1=$ui->col()->width(12)->open();
		$array_options = array();
		$array_options[0] = $ui->option()->value('""')->text("Select Group")->disabled()->selected();
		$filter=array();
		foreach($group_notice as $filter_result)
  	 		array_push($filter,$filter_result->group_id);
		$filter=array_unique($filter);
		foreach ($filter as $grp) 
			array_push($array_options,$ui->option()->value($grp)->text($grp));
										
			$ui->select()
				->label('Group Name<span style= "color:red;"> *</span>')
				->required()
				->name('groups_name_id')
		   		->id("groups_name_id")
		   		->containerId("cont_groups_name_id")
		   		->options($array_options)
		   		->show();
		$row1->close();

	$innercol4->close();

			$column2=$ui->col()->width(12)->open();
?>
<br/>
<div id="move_details_of_sent_files" name='move_details_of_sent_files'>
	
</div>

<br/>

<br/>
<?php
	
			
	$column2->close();

	$innercol2=$ui->col()->width(12)->open();
	$box = $ui->box()
			  ->title('Add Member')
			  ->solid()	
			  ->uiType('primary')
			  ->open();
	if($total_rows !=0 ){
		$table = $ui->table()->hover()->bordered()
								->sortable()->searchable()->paginated()
							    ->open();							    
?>
						<thead>
							<tr>							
								<th>Member</th>
								<th >Department</th>
								<th> Add</th>							
							</tr>
						</thead>			
<?php 
					$sno=1;
					while($sno <= $total_rows)
					{
 ?>

							<tr>
									<td>
										
										<?echo $data_array[$sno][2]." ".$data_array[$sno][3]." ".$data_array[$sno][4]." (".$data_array[$sno][1]." )"?>
									</td>
									<td>
											<?echo $data_array[$sno][5] ?>
									</td>
									<td>
										&nbsp;&nbsp;&nbsp;
											<center>	
										<?php	$ui->button()->icon($ui->icon("plus"))
										->value('ADD')
										->id('submit'.$sno)
										->uiType('primary')
										->name('submit_track')
										->show(); 
										?>
										</center>
													 
									</td>
							</tr>
										
<?php
						$sno++;
					 }
					$table->close();
				}
			$box->close();
			$innercol2->close();
			$column2->close();
			$outer_row->close();
			
	$end_row=$ui->row()->open();
	$column2=$ui->col()->width(12)->open();
	
	$form = $ui->form()->action('information/information_ajax/create_complete/'.$id_user.'/'.$auth_id)->extras('enctype="multipart/form-data"')->open();
	
	$ui->button()->icon($ui->icon("check"))
		->value('Done')
	    ->uiType('success')
	    ->submit()
	    ->name('mysubmit')
	    ->show();
	
	$form->close();
	
	$column2->close();
	$end_row->close();
?>

<script charset="utf-8">
$(window).load(function(){
$("#groups_name_id2").hide();
});
$("#groups_name_id2").hide();
$("#groups_name_id").on('change', function()
{
	add_member_all(this.value,'<?php echo $id_user; ?> ');
});
	<?php
		$sno=1;
		while ($sno <= $total_rows)
		{
	?>	
			var submit_id = '#submit'+<?php echo $sno; ?>;
			$(submit_id).click(function(){
				var group_name=document.getElementById('groups_name_id').value;
				if(group_name.trim()=="")
				{
					alert("Please fill the group name");
					return false;
				}
				add_member($('#groups_name_id').val(),'<?php echo $id_user; ?>','<?php echo $data_array[$sno][1]; ?>','<?php echo $date; ?>');
			});
	<?php
			$sno++;
		}
	?>

</script>