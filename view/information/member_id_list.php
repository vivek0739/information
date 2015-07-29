<?php
	foreach($members as $row)
	{
	   echo '<option value="'.$row->id.'">'.$row->id.'</option>';
	}
?>