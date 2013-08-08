<?php 
/*
 * Plugin Name: weichuncai(WP伪春菜)
 * Plugin URI: http://www.lmyoaoa.com/inn/?p=3134
 * Description: 为了WP的萌化，特制伪春菜插件一枚!
 * Version: 1.4
 * Author: lmyoaoa(油饼小明猪)
 * Author URI: http://www.lmyoaoa.com
 */

load_plugin_textdomain('weichuncai', PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)) . '/lang');
$wcc = get_option('sm-weichuncai');

//获得春菜的详细数据与js交互
function dataToJs(){
	global $wcc;
	if($_GET['a'] == 'getdata'){
		if( preg_match('/userdefccs_/i', $wcc['defaultccs']) )
			$key = str_replace( 'userdefccs_', '', $wcc['defaultccs']);
		else
			$key = $wcc['defaultccs'];
		
		$lifetime = get_wcc_lifetime($wcc['lifetime'][$key]);
		$wcc['showlifetime'] = '我已经与主人 '.$wcc["adminname"].' 一起生存了 <font color="red">'.$lifetime["day"].'</font> 天 <font color="red">'.$lifetime["hours"].'</font> 小时 <font color="red">'.$lifetime["minutes"].'</font> 分钟 <font color="red">'.$lifetime["seconds"].'</font> 秒的快乐时光啦～*^_^*';
		$wcc['notice'] = stripslashes($wcc['notice']);
		$wcc = json_encode($wcc);
		echo $wcc;
		die();
	}
}
add_action('init', 'dataToJs');

//获得春菜
function get_chuncai(){
	echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('siteurl').'/wp-content/plugins/weichuncai/css/style.css">';
	$wcc = get_option('sm-weichuncai');
	$talkself_user = get_option('sm-wcc-talkself_user');
	if($wcc == ''){
		sm_init();
		$wcc = get_option('sm-weichuncai');
	}

	if( preg_match('/userdefccs_/i', $wcc['defaultccs']) ) {
		$key = str_replace( 'userdefccs_', '', $wcc['defaultccs']);
		$fpath = $wcc['userdefccs'][$key]['face'];
		$fpath1 = $fpath[0];
		$fpath2 = $fpath[1] ? $fpath[1] : $fpath1;
		$fpath3 = $fpath[2] ? $fpath[2] : $fpath1;
	}else {
		$path = 'wp-content/plugins/weichuncai/skin/'.$wcc[defaultccs].'/';
		$fpath1 = plugins_url('weichuncai/skin/'.$wcc[defaultccs].'/face1.gif');
		$fpath2 = plugins_url('weichuncai/skin/'.$wcc[defaultccs].'/face2.gif');
		$fpath3 = plugins_url('weichuncai/skin/'.$wcc[defaultccs].'/face3.gif');
		$fpath2 = file_exists($path.'face2.gif') ? $fpath2 : $fpath1;
		$fpath3 = file_exists($path.'face3.gif') ? $fpath3 : $fpath1;
	}

	$size = getimagesize($fpath1);
	$notice_str = '&nbsp;&nbsp;'.$wcc['notice'].'<br />';
	echo '<script>var path = "'.get_bloginfo('siteurl').'";';
	echo "var imagewidth = '{$size[0]}';";
	echo "var imageheight = '{$size[1]}';";	
	echo '</script>';
	echo '<script src="'.get_bloginfo('siteurl').'/wp-content/plugins/weichuncai/js/common.js"></script>';
	echo '<script>createFace("'.$fpath1.'", "'.$fpath2.'", "'.$fpath3.'");</script>';
	echo '<script>';
	//自定义自言自语
	if(!empty($talkself_user) && is_array($talkself_user)) {
		$talkself_user_str = 'var talkself_user = [ ';
		$dot = '';
		foreach($talkself_user['says'] as $k=>$v) {
			$tmpf = $talkself_user['face'][$k] ? $talkself_user['face'][$k] : 1;
			$talkself_user_str .= $dot.'["'.$v.'", "'.$tmpf.'"]';
			$dot = ',';
		}
		$talkself_user_str .= ' ];';
	}else{
		echo "var talkself_user = [];";
	}
	echo $talkself_user_str;
	echo 'var talkself_arr = talkself_arr.concat(talkself_user);';
	echo '</script>';
}

wp_enqueue_script('jquery');
add_filter('wp_head', 'get_chuncai');
add_action('admin_menu', 'chuncaiadminPage');


function chuncaiadminPage(){
	$wcc = get_option('sm-weichuncai');
	if($wcc == ''){
		sm_init();
		$wcc = get_option('sm-weichuncai');
	}
	//////////去除第一版的默认春菜中华娘 V1.1以上版本使用= =///////////
/*	if(!empty($wcc[lifetime]['中华娘'])){
		unset($wcc[lifetime]['中华娘']);
		foreach($wcc['ccs'] as $k=>$v){
			if($v == '中华娘'){
				unset($wcc['ccs'][$k]);
			}
		}
		update_option('sm-weichuncai', $wcc);
	}
 */
	///////////++END++//////////
	if(function_exists('add_options_page')){
		add_options_page(__('伪春菜控制面板', "weichuncai"), __('伪春菜控制面板', "weichuncai"), 9, 'weichuncai/sm-options.php');
	}
}

//默认的春菜设置
function sm_init(){
	global $wcc;
	$lifetime = time();
	$wcc = array(
		'notice'=>'主人暂时还没有写公告呢，这是主人第一次使用伪春菜吧',
		'adminname'=>'',
		'isnotice'=>'',
		'ques'=>array('早上好', '中午好', '下午好', '晚上好', '晚安'),
		'ans'=>array('早上好～', '中午好～', '下午好～', '晚上好～', '晚安～'),
		'lifetime'=>array(
			'rakutori'=>$lifetime,
			'neko'=>$lifetime,
			'chinese_moe'=>$lifetime,
			),
		'ccs'=>array('rakutori','neko','chinese_moe'),
		'defaultccs'=>'rakutori',
		'foods'=>array('金坷垃', '咸梅干'),
		'eatsay'=>array('吃了金坷垃，一刀能秒一万八～！', '吃咸梅干，变超人！哦耶～～～'),
	);
	update_option('sm-weichuncai', $wcc);
}

//获得伪春菜生存时间
function get_wcc_lifetime($starttime){
	$endtime = time();
	$lifetime = $endtime-$starttime;
	$day = intval($lifetime / 86400);
	$lifetime = $lifetime % 86400;
	$hours = intval($lifetime / 3600);
	$lifetime = $lifetime % 3600;
	$minutes = intval($lifetime / 60);
	$lifetime = $lifetime % 60;
	return array('day'=>$day, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$lifetime);
}
function get_pic_path($name){
	$fpath1 = dirname(__FILE__).'/skin/'.$name.'/face1.gif';
	$fpath2 = dirname(__FILE__).'/skin/'.$name.'/face2.gif';
	$fpath3 = dirname(__FILE__).'/skin/'.$name.'/face3.gif';
	return array($fpath1, $fpath2, $fpath3);
}

function isset_face($array){
	foreach($array as $k=>$v){
		if(file_exists($v)){
			$narr[] = $v;
		}
	}
	if(empty($narr)){
		echo '<script>alert("'._e("您没有上传表情，暂时无法使用伪春菜的说").'");</script>>';
	}else{
		return $narr;
	}
}
?>
