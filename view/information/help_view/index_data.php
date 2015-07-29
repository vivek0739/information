<?php
	$ui = new UI();
	
	$row = $ui->row()->open();
	
	$column1=$ui->col()->open();
	$table = $ui->table()->bordered()->hover()->responsive()->open();
	
		$i=1;
		foreach ($individuals as $row1) {
		
			?><tr>
			
				<td><?
					$ui->input()->id("individual_name".$i)->name('individual_name'.$i)->required()
					   ->placeholder($row1->user_id)->value($row1->user_id)->show();
				?></td>
			</tr><?
			$i++;
		}
	
	$table->close();
	$column1->close();
	$row->close();
	

?>