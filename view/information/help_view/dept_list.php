<?php
	echo '<option disabled selected>'.'Select Department'.'</option>';
	foreach($departments as $row)
	{
	   echo '<option value="'.$row->id.'">'.$row->name.'</option>';
	}
?>