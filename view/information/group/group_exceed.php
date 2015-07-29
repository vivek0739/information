<?php
$ui=new UI();
$ui->callout()->title("No of group has been exceeded the limit")
			   ->uiType("warning")->id('grp_exceed')->desc('delete any group')->show();
$innercol2=$ui->col()->width(12)->open();
?>
<div align="left">
	<a href=<?php echo site_url('information/information_ajax/delete_notice_group/'.strval($id_user).'/'.strval($auth_id));?> >

<?
	$ui->button()
		->value('Delete Group')
	    ->uiType('danger')
	    ->submit()
	    ->id('dmanage')
	    ->name('dmanage')
	    ->show();

?>
</a>
<br/><br/>
</div>
<?php
	$innercol2->close();

?>
	