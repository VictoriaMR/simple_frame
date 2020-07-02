<?php $this->load('Common.baseHeader');?>
<div class="container">
	<?php if ($list['total'] > 0) { ?>
		<?php foreach ($list['list'] as $key => $value) { ?>
			<div>
				<img src="<?php echo $value['pro_image'];?>">
			</div>
		<?php } ?>
	<?php } ?>
</div>
<?php $this->load('Common.baseFooter');?>