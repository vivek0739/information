<?php
$ui = new UI();
$row1=$ui->row()->open();
$innercol7=$ui->col()->id('course_col')->width(12)->open();
		
//print_r($course);
		$array_options = array();
		$array_options[0] = $ui->option()->value('all')->text("All Courses")->selected();
		if($course!=NULL)
		foreach ($course as $key => $cor)
			array_push($array_options,$ui->option()->value($cor->id)->text($cor->name));
						
							
										
							$ui->select()
								->label('Select Courses')
								->name('course_selection1')
								->id("course_selection1")
								->options($array_options)
								->show();
	$innercol7->close();
$row1->close();
?>
<script type="text/javascript">

$("#course_selection1").val('<? echo $courses;?>');
$("#course_selection1").on('change' , function()
						{
							if(this.value!='all')
							{
								$('#sem').show();
								semester(this.value);
							}
							
							else
							$('#sem').hide();
						});

</script>