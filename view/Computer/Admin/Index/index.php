<?php $this->load('Common.baseHeader');?>
<div class="container">
	<div id="header" <?php if (!empty($info['color_value'])) { ?> style="background-color: <?php echo $info['color_value'];?>" <?php } ?>>
	    <a href="<?php echo url('admin/login/logout');?>" class="right toolbox" data-title="退出系统">
	    	<img src="<?php echo url('image/icon/exit.png');?>">
	    </a>
	    <a target="subframe" href="<?php echo url('admin/index/setting');?>" class="right toolbox setting" data-title="个人设置"> 
	    	<img src="<?php echo url('image/icon/setting.png');?>">
	    </a>
	    <a target="subframe" href="<?php echo url('admin/message');?>" class="right toolbox" data-title="消息通知"> 
	    	<img src="<?php echo url('image/icon/notice.png');?>">
	    </a>
	</div>
</div>
<div id="content" class="flex">
	<table width="100%" border="0">
		<tr>
			<td width="10%" height="100%">
				<div id="left" <?php if (!empty($info['color_value'])) { ?> style="background-color: <?php echo $info['color_value'];?>" <?php } ?>>
					
				</div>
			</td>
			<td width="90%">
				2
			</td>
		</tr>
	</table>
</div>

<script type="text/javascript">
$(document).ready(function(){
	var width = $(window).width();
	var height = $(window).height();
	var topH = $('#header').height();

	$('#left').css({height: height - topH - 1 + 'px'});
});
</script>

<?php $this->load('Common.baseFooter');?>