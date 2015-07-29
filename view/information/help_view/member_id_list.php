<?php
	foreach($members as $row)
	{
	   echo '<option value="'.$row->emp_no.'">'.$row->emp_no.'</option>';
	}
?>