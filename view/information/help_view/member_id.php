<?php
	foreach($members as $row)
	{
	   echo '<option value="'.$row->admn_no.'">'.$row->admn_no.'</option>';
	}
?>