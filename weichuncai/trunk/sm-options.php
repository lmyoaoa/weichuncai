<style>
.chuncaidiv {
float:left;padding:3px;text-align:center; width:168px;position:relative;
overflow:hidden;
}
.chuncaidiv img {
	height:50px;
}
</style>
<div class="wrap">
<?php

$talkself_user = get_option('sm-wcc-talkself_user');
#删除自言自语
if($_POST['del_tku_sub']) {
	if(!$_POST['del_tku']) {
		$msg = __('请先选择一个要删除的项目!', 'weichuncai');
	}else {
		foreach($talkself_user['says'] as $k=>$v) {
			if(in_array($k, $_POST['del_tku'])) {
				unset($talkself_user['says'][$k]);
				unset($talkself_user['face'][$k]);
			}
		}
		update_option('sm-wcc-talkself_user', $talkself_user);
		$msg = __('设置已保存!', 'weichuncai');
	}
}

#新增自言自语
if($_POST['talkself_user_sub']) {
	if( count($talkself_user['says']) < 50 ) {
		$talkself_user['says'][] = $_POST['talkself_user']['says'];
		$talkself_user['face'][] = $_POST['talkself_user']['face'];
		update_option('sm-wcc-talkself_user', $talkself_user);
		$msg = __('设置已保存!', 'weichuncai');
	}else{
		$msg = __('设置未保存! 你的自言自语设置有点多了哦，删除一些吧.', 'weichuncai');
	}
}

if($_POST['editchuncai']){
	$wcc = get_option('sm-weichuncai');
	$wcc['defaultccs'] = $_POST['defaultccs'];
	update_option('sm-weichuncai', $wcc);
	$msg = __('春菜更新成功!', 'weichuncai');
}

$wcc = get_option('sm-weichuncai');
#print_r($wcc);
if($_POST['subnotice']){
	$wcc = get_option('sm-weichuncai');
	$wcc['notice'] = 	$_POST['notice'];
	$wcc['adminname'] = 	$_POST['adminname'];
	$wcc['isnotice'] = 	$_POST['isnotice'];
	$wcc['ques'] =		$_POST['ques'];
	$wcc['ans'] =		$_POST['ans'];
	$wccnew = $_POST['wccnew'];
	if($wccnew != ''){
		$wcc['lifetime'][$wccnew] =	time();
		$wcc['ccs'][] = $wccnew;
	}
	update_option('sm-weichuncai', $wcc);
	$msg = __('设置已保存!', 'weichuncai');
}

if($_GET['del']){
	$id = $_GET['ccsid'];
	$preg = '/userdefccs_/i';
	if(preg_match($preg, $id) ) {
		$id = str_replace( 'userdefccs_', '', $id );
		unset($wcc['userdefccs'][$id]);
		unset($wcc['lifetime'][$id]);
		update_option('sm-weichuncai', $wcc);
	}else{
		$pic = get_pic_path($wcc['ccs'][$id]);
		foreach($pic as $k=>$v){
			if(file_exists($v)){@unlink($v);}
		}
		$dir = dirname(__FILE__).'/skin/'.$wcc['ccs'][$id].'/';
		@rmdir($dir);
		unset($wcc['lifetime'][$wcc['ccs'][$id]]);
		unset($wcc['ccs'][$id]);
		update_option('sm-weichuncai', $wcc);
		echo '<script>window.location.href="?page=weichuncai/sm-options.php";</script>';
	}
}
if($_POST['additional']){
	$wcc['foods'] = $_POST['foods'];
	$wcc['eatsay'] = $_POST['eatsay'];
	update_option('sm-weichuncai', $wcc);
	$msg = __('附加设置更新成功!', 'weichuncai');
}

#添加春菜页面
if( $_GET['cp'] == 1 && $_POST['new_userdef_ccs_sub']) {
	#print_r($_POST);
	$wcc['userdefccs'][$_POST['userdefccs']] = array('name'=>$_POST['userdefccs'], 
	'face'=>$_POST['face'],
	);
	$wcc['lifetime'][$_POST['userdefccs']] = time();;
	update_option('sm-weichuncai', $wcc);
	$msg = __('添加伪春菜成功!', 'weichuncai');
}

if(isset($msg)){
	echo '<div id="message" class="updated fade"><p>'.$msg.'</p></div>';
}
?>
<h1><?php _e("伪春菜控制面板", "weichuncai"); ?></h1>
<p><a href="options-general.php?page=weichuncai/sm-options.php"><?php _e('通用设置', 'weichuncai'); ?></a> | <a href="options-general.php?page=weichuncai/sm-options.php&cp=1"><?php _e('创建伪春菜', 'weichuncai'); ?></a></p>
<hr>
<?php 
if($_GET['cp'] != '1') {

?>
<form action="" method="post">
<h4><?php _e("设置默认春菜", "weichuncai"); ?></h4>
<p>
<div style="float:left;width:100%;*width:672px;">
<?php
	foreach($wcc['ccs'] as $k=>$v){
		if($v == $wcc['defaultccs']){
			echo '<div style="" class="chuncaidiv"><img src="../wp-content/plugins/weichuncai/skin/'.$v.'/face1.gif"><p><input type="radio" name="defaultccs" value="'.$v.'" checked> '.$v.'</p></div>';
		}else{
			$isdelcc = __("删除？", "weichuncai");
			echo '<div class="chuncaidiv" style=""><img src="../wp-content/plugins/weichuncai/skin/'.$v.'/face1.gif"><p><input type="radio" name="defaultccs" value="'.$v.'"> '.$v.'(<a href="?page=weichuncai/sm-options.php&del=del&ccsid='.$k.'">'.$isdelcc.'</a>)</p></div>';
		}
	}

	if( !empty($wcc['userdefccs']) ) {
		foreach( $wcc['userdefccs'] as $k=>$v ) {
			if( 'userdefccs_'.$k == $wcc['defaultccs'] ) {
				echo '<div class="chuncaidiv"><img src="'.$v['face'][0].'"><p><input type="radio" name="defaultccs" value="userdefccs_'.$k.'" checked="checked"> '.$k.'</p></div>';
			}else{
				$isdelcc = __("删除？", "weichuncai");
				echo '<div class="chuncaidiv" style=""><img src="'.$v['face'][0].'"><p><input type="radio" name="defaultccs" value="userdefccs_'.$k.'"> '.$k.'(<a href="?page=weichuncai/sm-options.php&del=del&ccsid=userdefccs_'.$k.'">'.$isdelcc.'</a>)</p></div>';
			}
		}
	}
?>
</div>
</p>


<p style="clear:left;" class="submit"><input class="button-primary" type="submit" name="editchuncai" value="<?php _e('更新春菜', 'weichuncai'); ?>"></p>
</form>

<hr>
<h4><?php _e('春菜基本设置', 'weichuncai'); ?></h4>
<form action="" method="post">
	<label>1. <?php _e('你希望伪春菜如何称呼你呢？', 'weichuncai'); ?></label>
	<p><input type="text" name="adminname" value="<?php echo $wcc['adminname']; ?>"></p>
	<label>2. <?php _e('公告：', 'weichuncai'); ?></label>
	<p><textarea name="notice" cols="40" rows="7"><?php echo $wcc['notice']; ?></textarea></p>

	<label>3. <?php _e('对话回应', 'weichuncai'); ?><p style="color:red">(<?php _e('在这里设置了问题与回答后，在前台的聊天功能中输入相关问题伪春菜就会回答，如输入：早上好，伪春菜会回答：“早上好～”,暂时最多只支持5个问答', 'weichuncai'); ?>)</p></label>
<?php
	$i = 1;
	foreach($wcc['ques'] as $k=>$v){
		echo '<p>问'.$i.'：<input type="text" name="ques['.$k.']" value="'.$v.'"> 答'.$i.'：<input type="text" name="ans['.$k.']" value="'.$wcc["ans"][$k].'"></p>';
		$i++;
	}
?>

<p class="submit"><input class="button-primary" type="submit" name="subnotice" value="<?php _e('更新设置', 'weichuncai'); ?>"></p>
</form>
<hr>

<h4><?php _e('自言自语设置', 'weichuncai'); ?></h4>
<p><?php _e('设置伪春菜自言自语时说的话，', 'weichuncai');?><font color="red"><?php _e('最多允许自定义50项。', 'weichuncai'); ?></font></p>
<form action="" method="post">
<?php
	if(!empty($talkself_user) && is_array($talkself_user) ) {
		$tmpk = 1;
		foreach($talkself_user['says'] as $k=>$v) {
			echo '<p><input type="checkbox" name="del_tku[]" value="'.$k.'">设定言语'.$tmpk.': '.stripslashes($v).'</p>';
			$tmpk++;
		}
		echo '<input class="button-primary" type="submit" name="del_tku_sub" value="删除选中项" />';
	}
?>
</form>
<form action="" method="post">
<?php
	echo '<p>'.__('新增', 'weichuncai').'：<input type="text" name="talkself_user[says]" style="width:300px;" value=""> '.__('对应表情', 'weichuncai').'：';
	echo '<select type="text" name="talkself_user[face]">';
	echo '<option value="1">表情1</option>';
	echo '<option value="2">表情2</option>';
	echo '<option value="3">表情3</option>';
	echo '</select>';
	echo ' <input  class="button-primary" type="submit" name="talkself_user_sub" value="'.__('添加', 'weichuncai').'" /></p>';
?>
</form>
<hr>
<h4><?php _e('附加设置', 'weichuncai'); ?></h4>

<form action="" method="post">
	<p><?php _e('零食：', 'weichuncai'); ?></p>
<?php
	$fom = 1;
	for($fo=0; $fo < 5; $fo++){
		echo '<p>'.__('零食', 'weichuncai').$fom.'：<input type="text" name="foods['.$fo.']" value="'.$wcc["foods"][$fo].'"> '.__('回答', 'weichuncai').$fom.'：<input type="text" name="eatsay['.$fo.']" value="'.$wcc["eatsay"][$fo].'"></p>';
		++$fom;
	}
?>
<p class="submit"><input class="button-primary" type="submit" name="additional" value="<?php _e('保存附加设置', 'weichuncai'); ?>" /></p>
</form>
<hr>
<h4><?php _e('基本状态', 'weichuncai'); ?></h4>
<?php
	foreach($wcc['lifetime'] as $key=>$val){
		$lifetime = get_wcc_lifetime($wcc['lifetime'][$key]);
		echo '<p>'.__('春菜', 'weichuncai').' <font color="red">'.$key.'</font> '.__("已经与主人一起生存了 ", "weichuncai").$lifetime["day"].__(" 天 ", "weichuncai").$lifetime["hours"].__(" 小时 ", "weichuncai").$lifetime["minutes"].__(" 分钟 ", "weichuncai").$lifetime["seconds"].__(" 秒的快乐时光。", "weichuncai").'</p>';
	}
?>

<p>
</p>

<?php
}elseif($_GET['cp'] == 1) {
?>
<form action="" method="post">
<p><?php _e('春菜名字', 'weichuncai'); ?>：<input type="text" name="userdefccs" value="" /></p>
<p><?php _e('提示：复制图片地址到下面文本框。或者你可以到', 'weichuncai'); ?><a target="_blank" href="media-new.php"><?php _e('上传图片', 'weichuncai'); ?></a><?php _e('先进行上传。图片应小于160像素。', 'weichuncai'); ?></p>
<p><font color="red">*</font><?php _e('表&nbsp;&nbsp;&nbsp;&nbsp;情1(普通)：', 'weichuncai'); ?><input style="width:350px;" type="text" name="face[]" value="" />
<p>&nbsp;&nbsp;<?php _e('表&nbsp;&nbsp;&nbsp;&nbsp;情2(开心)：', 'weichuncai'); ?><input style="width:350px;" type="text" name="face[]" value="" />
<p>&nbsp;&nbsp;<?php _e('表&nbsp;&nbsp;&nbsp;&nbsp;情3(悲伤)：', 'weichuncai'); ?><input style="width:350px;" type="text" name="face[]" value="" />

<p><?php _e('注：图片可以只上传第一张，发挥想象创造吧～', 'weichuncai'); ?></p>
<p><input class="button-primary" type="submit" name="new_userdef_ccs_sub" value="<?php _e('新增春菜', 'weichuncai'); ?>" />
</form>
<?php
}
?>
</div>
