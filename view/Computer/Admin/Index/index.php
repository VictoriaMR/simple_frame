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
	<div id="left" class="left" <?php if (!empty($info['color_value'])) { ?> style="background-color: <?php echo $info['color_value'];?>" <?php } ?>>
		<div id="person">
			<div class="avatar"><img src="./Baycheer ERP_files/male.jpg"></div>
		</div>
		<div id="click-list" <?php if (!empty($info['color_value'])) { ?> style="background-color: <?php echo $info['color_value'];?>" <?php } ?>>
			
		</div>
	</div>
	<div id="right" class="flex-1">
		<div id="tab-list"></div>
		<div id="tab-content">
			<div class="tab-item">
        		<iframe id="subframe-1" width="100%" height="100%" frameborder="0" src="<?php echo url('admin/index/show');?>"></iframe>
			</div>
			
		</div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	var width = $(window).width();
	var height = $(window).height();
	var offsetTop = $('#tab-content').offset().top+1;
	var offsetLeft = $('#left').width();
	console.log(width, height, offsetTop, offsetLeft);
	$('#tab-content .tab-item').css({width: (width-offsetLeft)+'px', height: (height-offsetTop)+'px'});
});
</script>

<?php $this->load('Common.baseFooter');?>