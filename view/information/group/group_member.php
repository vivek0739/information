<?php 
$ui= new UI();
	$outer_row=$ui->row()->open();
	$column1=$ui->col()->width(0)->open();
	$column1->close();
	$column2=$ui->col()->width(12)->open();
	$post_box=$ui->box()
    			 ->id("mem_box")
					->title('Added Members')
					->solid()
					->uiType('primary')
					->open();

	$table2 = $ui->table()->hover()->bordered()
				 ->sortable()->searchable()->paginated() 	
				 ->open();

?>
						<thead>
							<tr>							
								<th>Id</th>
								<th >Name</th>
								<th>Remove</th>							
							</tr>
						</thead>			
<?php 
	$srno=1;
	$data_array[]=array();
	foreach($members as $member)
	{
?>	
		<tr>
			<td> <?echo $member->user_id; ?></td>
			<td> <?echo $member->first_name." ".$member->middle_name." ".$member->last_name;  $data_array[$srno]=$member->user_id;?></td>
			<td>
				<?php
                    $btn_string = 'onclick=' . '"clickEvent(\'' . $data1 . '\',\'' . $data2 . '\',\'' . $member->user_id . '\',\'' . $data4 . '\')"'	;
                    $ui->button()->icon($ui->icon("remove"))->id('remove')->extras($btn_string)->width(4)->uiType('danger')->name('remove')->value('Remove')->show();
                   ?>
			</td>
		</tr>
<?
}

	$table2->close();
	$post_box->close();
	$total=$srno;

	$column2->close();
	$outer_row->close();
?>



