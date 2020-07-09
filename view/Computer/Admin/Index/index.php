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
			<td width="185" height="100%" valign="top">
				<div id="left" <?php if (!empty($info['color_value'])) { ?> style="background-color: <?php echo $info['color_value'];?>" <?php } ?>>
					<div id="user-info">
						<div class="left avator">
							<?php if (!empty($info['avator'])) { ?>
								<img src="<?php echo $info['avator'];?>" alt="avator">
							<?php } else { ?>
								<img src="<?php echo url('image/img/male.jpg'); ?>" alt="avator">
							<?php } ?>
						</div>
						<div class="left name color-white">
							<div class="ellipsis-1">
								<span class="font-14"><?php echo $info['name'] ?? '暂无';?></span>
								<a class="right" href="<?php echo url('admin/login/logout');?>"><img src="<?php echo url('image/icon/exit.png');?>"></a>
							</div>
							<div class="ellipsis-1 color-red"><?php echo $info['mobile'] ?? '暂无';?></div>
						</div>
						<div class="clear"></div>
					</div>
					<div id="controller-list" class="relative">
						<div id="left-one" class="left" style="width: 40px;">
							<div class="toggle close" data-title="菜单切换开关">
								<img src="<?php echo url('image/icon/task.png');?>">
							</div>
							<?php if (!empty($list)) { ?>
							<?php foreach ($list as $value) { ?>
							<div id="feature-main-<?php echo $value['con_id'];?>" class="feature" data-feature-id="<?php echo $value['con_id'];?>" data-title="<?php echo $value['name'] ;?>">
					            <img src="<?php echo url('image/icon/'.$value['icon'].'.png');?>">
					            <p><?php echo $value['name'] ;?></p>
					        </div>
					        <?php } ?>
					    	<?php } ?>
						</div>
						<div class="left" id="left-two">
							<?php if (!empty($list)) { ?>
							<?php foreach ($list as $value) { ?>
							<div id="menu-main-<?php echo $value['con_id'];?> hidden">
								<div class="title">
									<span><?php echo $value['name'] ;?></span>
								</div>
								<div class="menu-content">
									<ul>
										<?php if (!empty($value['son'])) { ?>
										<?php foreach ($value['son'] as $v) { ?>
										<li data-src="<?php echo url('admin/'.$value['name_en'].'/'.$v['name_en']);?>" data-id="<?php echo $v['con_id'];?>">
											<span><?php echo $v['name'];?></span>
											<a title="新窗口打开" target="_blank" href="<?php echo url('admin/'.$value['name_en'].'/'.$v['name_en']);?>"></a>
										</li>
										<?php } ?>
										<?php } ?>
									</ul>
	        					</div>
							</div>
							<?php } ?>
							<?php } ?>
						</div>
					</div>
				</div>
			</td>
			<td valign="top" id="iframe-content">
				<div id="iframe-list">
					<ul>
						
					</ul>
				</div>
				<div id="iframe-list-content">
					
				</div>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">
$(document).ready(function(){
	INDEX.init();
});
</script>
<?php $this->load('Common.baseFooter');?>