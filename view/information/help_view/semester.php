<?php
$ui = new UI();
$row1=$ui->row()->open();
$innercol7=$ui->col()->id('semester_col')->width(12)->open();
//print_r($course);
		$array_options = array();
		foreach ($course as $key => $cor) {
			# code...
		
						
						$array_options[0] = $ui->option()->value('all')->text("All");
						for($i=1 ; $i <= ($cor->duration)*2; $i++)
							array_push($array_options,$ui->option()->value(strval($i))->text($i." sem"));
		}				
							
										
							$ui->select()
								->label('Select Semester')
								->name('sem_selection')
								->id("sem_selection")
								->options($array_options)
								->show();
	$innercol7->close();
$row1->close();
?>
<script type="text/javascript" language="javascript">
$(document).ready(function(){

$('#sem_selection').val('<? echo $sem;?>')


});

</script>