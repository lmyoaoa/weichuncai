var smjq = jQuery;
smjq(document).ready(function(){
		var getwidth = getCookie("historywidth");
		var getheight = getCookie("historyheight");
		if(getwidth != null && getheight != null){
			var width = getwidth;
			var height = getheight;
		}else{
			var width = document.documentElement.clientWidth- 200 - imagewidth;
			var height = document.documentElement.clientHeight- 180 - imageheight;
		}

		var cwidth = document.documentElement.clientWidth-100;
		var cheight = document.documentElement.clientHeight-20;
		//var height = document.body.clientHeight-200;
		var moveX = 0;
		var moveY = 0;
		var moveTop = 0;
		var moveLeft = 0;
		var moveable = false;
		var docMouseMoveEvent = document.onmousemove;
		var docMouseUpEvent = document.onmouseup;

		smjq("body").append('<div id="smchuncai" onfocus="this.blur();" style="color:#626262;z-index:999;"><div id="chuncaiface"></div><div id="dialog_chat"><div id="chat_top"></div><div id="dialog_chat_contents"><div id="dialog_chat_loading"></div><div id="tempsaying"></div><div id="showchuncaimenu"><ul class="wcc_mlist" id="shownotice">show notice</ul><ul class="wcc_mlist" id="chatTochuncai">Chat</ul><ul class="wcc_mlist" id="foods">Eat Foods</ul><ul class="wcc_mlist" id="blogmanage">Dashboard</ul><ul class="wcc_mlist" id="lifetimechuncai">Life Time</ul><ul class="wcc_mlist" id="closechuncai">Close Chobit</ul></div><div><ul id="chuncaisaying"></ul></div><div id="getmenu"> </div></div><div id="chat_bottom"></div></div></div>');
		smjq("#smchuncai").append('<div id="addinput"><div id="inp_l"><input id="talk" type="text" name="mastersay" value="" /> <input id="talkto" type="button" value=" " /></div><div id="inp_r"> X </div></div>');
		smjq("body").append('<div id="callchuncai">Call Chobit</div>');
		//判断春菜是否处于隐藏状态
		var is_closechuncai = getCookie("is_closechuncai");
		if(is_closechuncai == 'close'){
			closechuncai_init();
		}
		//设置初始状态
		getdata("getnotice");
		setFace(1);

		smjq("#smchuncai").css('left', width+'px');
		smjq("#smchuncai").css('top', height+'px');
		smjq("#smchuncai").css('width', imagewidth+'px');
		smjq("#smchuncai").css('height', imageheight+'px');
		smjq("#callchuncai").attr("style", "top:"+cheight+"px; left:"+cwidth+"px; text-align:center;");

		smcc = document.getElementById("smchuncai");
		smcc.onmousedown = function(){
			var ent = getEvent();
			moveable = true;
			moveX = ent.clientX;
			moveY = ent.clientY;
			var obj = document.getElementById("smchuncai");
			moveTop = parseInt(obj.style.top);
			moveLeft = parseInt(obj.style.left);
			if(isFirefox=navigator.userAgent.indexOf("Firefox")>0){
				window.getSelection().removeAllRanges();
			}			
			document.onmousemove = function(){
				if(moveable){
					var ent = getEvent();
					var x = moveLeft + ent.clientX - moveX;
					var y = moveTop + ent.clientY - moveY;
					var w = 200;
					var h = 200;	//w,h为浮层宽高
					obj.style.left = x + "px";
					obj.style.top = y + "px";
				}
			};
			document.onmouseup = function(){
				if(moveable){
					var historywidth = obj.style.left;
					var historyheight = obj.style.top;
					historywidth = historywidth.replace('px', '');
					historyheight = historyheight.replace('px', '');
					setCookie("historywidth", historywidth, 60*60*24*30*1000);
					setCookie("historyheight", historyheight, 60*60*24*30*1000);
					document.onmousemove = docMouseMoveEvent;
					document.onmouseup = docMouseUpEvent;
					moveable = false; 
					moveX = 0;
					moveY = 0;
					moveTop = 0;
					moveLeft = 0;
				}
			}
		};
		smjq("#getmenu").click(function(){
				chuncaiMenu();
				setFace(1);
				});
		smjq("#shownotice").click(function(){
				getdata("getnotice");
                                setFace(1);
		});
		smjq("#closechuncai").click(function(){
				setFace(3);
				closechuncai();
				});
		smjq("#callchuncai").click(function(){
				setFace(2);
				callchuncai();
				setCookie("is_closechuncai", '', 60*60*24*30*1000);
				});
		smjq("#shownotice").click(function(){
				setFace(1);
				closeChuncaiMenu();
				});
		smjq("#lifetimechuncai").click(function(){
				closeChuncaiMenu();
				closeNotice();
				setFace(2);
				getdata('showlifetime');
				});
		smjq("#chatTochuncai").click(function(){
				showInput();
				});
		smjq("#inp_r").click(function(){
				closeInput();
				chuncaiSay('Do not chat?(→_→)');
				setFace(3);
				});
		smjq("#talkto").click(function(){
				getdata("talking");
				});
		smjq("#blogmanage").click(function(){
				closeChuncaiMenu();
				closeNotice();
				smjq("#getmenu").css("display", "none");
				chuncaiSay("Let's go to Dashboard～～～");
				setFace(2);
				setTimeout(function(){
					window.location.href = path+'/wp-admin/';
					}, 2000);
				});
		smjq("#foods").click(function(){
				closeChuncaiMenu();
				closeNotice();
				getdata("foods");
				});
/*		smjq("#showchuncaimenu").hover(function(){
				},function(){
				smjq("#showchuncaimenu").slideUp('slow').show();
				});*/
		document.onmousemove = function(){
			stoptime();
			tol = 0;
			setTime();
			//chuncaiSay("啊，野生的主人出现了！ ～～～O口O");
		}
		talkSelf(talktime);
		document.getElementById("smchuncai").onmouseover = function(){
			if(talkobj){
				clearTimeout(talkobj);
			}
			talktime = 0;
			talkSelf(talktime);
		}
		});

function getEvent() {
	return window.event || arguments.callee.caller.arguments[0];
}

var eattimes = 0;
function eatfood(obj){
	var gettimes = getCookie("eattimes");
	if(parseInt(gettimes) > parseInt(9)){
		chuncaiSay("Master is a villain!!!");
		setFace(3);
		closechuncai_evil();
	}else if(parseInt(gettimes) > parseInt(7)){
		chuncaiSay(".....................Belly will bomb!!! I want not eat any more~~~TAT");
		setFace(3);
	}else if(parseInt(gettimes) == parseInt(5)){
		chuncaiSay("I'm full...Wantn't eat any more......");
		setFace(3);
	}else if(parseInt(gettimes) == parseInt(3)){
		chuncaiSay("Thank for foods，I am full ~~~╰（￣▽￣）╭");
		setFace(2);
	}else{
		var id = obj.replace("f",'');
		getdata('eatsay', id);
	}
	eattimes++;
	setCookie("eattimes", eattimes, 60*10*1000);
}
function chuncaiMenu(){
	//smjq("#showchuncaimenu").slideDown('fast').show();
	clearChuncaiSay();
	closeInput();
	chuncaiSay("What do you want to do?");
	smjq("#showchuncaimenu").css("display", "block");
	smjq("#getmenu").css("display", "none");
	smjq("#chuncaisaying").css("display", "none");
}
function closeChuncaiMenu(){
	clearChuncaiSay();
	smjq("#showchuncaimenu").css("display", "none");
	//smjq("#chuncaisaying").css("display", "block");
	showNotice();
	smjq("#getmenu").css("display", "block");
}
function showNotice(){
	smjq("#chuncaisaying").css("display", "block");
}
function closechuncai(){
	stopTalkSelf();
	chuncaiSay("Please remember call me again...");
	smjq("#showchuncaimenu").css("display", "none");
	setTimeout(function(){
			smjq("#smchuncai").fadeOut(1200);
			smjq("#callchuncai").css("display", "block");}, 2000);
	//保存关闭状态的春菜
	setCookie("is_closechuncai", 'close', 60*60*24*30*1000);
}
function closechuncai_evil(){
	stopTalkSelf();
	smjq("#showchuncaimenu").css("display", "none");
	setTimeout(function(){
			smjq("#smchuncai").fadeOut(1200);
			smjq("#callchuncai").css("display", "block");}, 2000);
}
function closechuncai_init(){
	stopTalkSelf();
	smjq("#showchuncaimenu").css("display", "none");
	setTimeout(function(){
			smjq("#smchuncai").css("display", "none");
			smjq("#callchuncai").css("display", "block");}, 30);
}
function callchuncai(){
	talkSelf(talktime);
	smjq("#smchuncai").fadeIn('normal');
	smjq("#callchuncai").css("display", "none");
	closeChuncaiMenu();
	closeNotice();
	chuncaiSay("I am back");
	setCookie("is_closechuncai", '', 60*60*24*30*1000);
}

function chuncaiSay(s){
	clearChuncaiSay();
	smjq("#tempsaying").append(s);
	smjq("#tempsaying").css("display", "block");
}
function clearChuncaiSay(){
	document.getElementById("tempsaying").innerHTML = '';
}
function closeNotice(){
	smjq("#chuncaisaying").css("display", "none");
}
function showInput(){
	closeChuncaiMenu();
	closeNotice();
	chuncaiSay("............?");
	//setFace(1);
	smjq("#addinput").css("display", "block");
}
function closeInput(){
	setFace(3);
	smjq("#addinput").css("display", "none");
}
function clearInput(){
	document.getElementById("talk").value = '';
}
function createFace(a, b, c){
	smjq("head").append('<div id="hiddenfaces"><img id="hf1" src="'+a+'" /><img id="hf2" src="'+b+'" /><img id="hf3" src="'+c+'" /></div>');
	setFace(1);
}
function setFace(num){
	obj = document.getElementById("hf"+num).src;
	smjq("#chuncaiface").attr("style", "background:url("+obj+") no-repeat scroll 50% 0% transparent; width:"+imagewidth+"px;height:"+imageheight+"px;");
}
function getdata(el, id){
	smjq.ajax({
		type:	'GET',
		url:	path+'/?a=getdata',
		cache:	'false',
		dataType: 'html',
		contentType: 'application/json; charset=utf8',
		beforeSend: function(){
			//smjq("#dialog_chat").fadeOut("normal");
			smjq("#tempsaying").css('display', "none");
			smjq("#dialog_chat_loading").fadeIn("normal");
		},
		success: function(data){
			smjq("#dialog_chat_loading").css('display', "none");
			//smjq("#dialog_chat").fadeIn("normal");
			smjq("#tempsaying").css('display', "");
			var dat = eval("("+data+")");
			if(el == 'defaultccs'){
				chuncaiSay(dat.defaultccs);
			}else if(el == 'getnotice'){
				chuncaiSay(dat.notice);
				setFace(1);
			}else if(el == 'showlifetime'){
				chuncaiSay(dat.showlifetime);
			}else if(el == 'talking'){
				var talkcon = smjq("#talk").val();
				var i = in_array(talkcon, dat.ques);
				var types = typeof(i);
				if(types != 'boolean'){
					chuncaiSay(dat.ans[i]);
					setFace(2);
				}else{
					chuncaiSay('.......................what?');
					setFace(3);
				}
				clearInput();
			}else if(el == 'foods'){
				var str='';
				var arr = dat.foods;
				var preg = /function/;
				for(var i in arr){
					if(arr[i] != '' && !preg.test(arr[i]) ){
						str +='<ul id="f'+i+'" class="eatfood" onclick="eatfood(this.id)">'+arr[i]+'</ul>';
					}
				}
				chuncaiSay(str);
			}else if(el = "eatsay"){
				var str = dat.eatsay[id];
				chuncaiSay(str);
				setFace(2);
			}else if(el = "talkself"){
				var arr = dat.talkself;
				return arr;
			}
		},
		error: function(){
			chuncaiSay('something is wrong...please contact admin.');
		}
		});
}

function in_array(str, arr){
	for(var i in arr){
		if(arr[i] == str){
			return i;
		}
	}
	return false;
}

var timenum;
var tol=0;
//10分钟后页面没有响应就停止活动
var goal = 10*60;
function setTime(){
	tol++;
	//document.body.innerHTML(tol);
	timenum = window.setTimeout("setTime('"+tol+"')", 1000);
	if(parseInt(tol) == parseInt(goal)){
		stopTalkSelf();
		closeChuncaiMenu();
		closeNotice();
		closeInput();
		chuncaiSay("Where is my master....?");
		setFace(3);
		stoptime();
	}
}
function stoptime(){
	if(timenum){
		clearTimeout(timenum);
	}
}
var talktime = 0;
//设置自言自语频率（单位：秒）
var talkself = 60;
var talkobj;
var tsi = 0;
var talkself_arr = [
	["Master!Master!Master! Where are you", "3"],
	["I'm tiny super man~~", "2"],
];

function talkSelf(talktime){
	talktime++;
	var tslen = talkself_arr.length;
/*	if(parseInt(tsi) >= parseInt(tslen)){
		tsi = 0;
	}*/
	var yushu = talktime%talkself;
	if(parseInt(yushu) == parseInt(9)){
		closeChuncaiMenu();
		closeNotice();
		closeInput();
		tsi = Math.floor(Math.random() * talkself_arr.length + 1)-1;
		chuncaiSay(talkself_arr[tsi][0]);
		setFace(talkself_arr[tsi][1]);
	}
	talkobj = window.setTimeout("talkSelf("+talktime+")", 1000);
}
function stopTalkSelf(){
	if(talkobj){
		clearTimeout(talkobj);
	}
}
function arrayShuffle(arr){
	var result = [],
	len = arr.length;
	while(len--){
		result[result.length] = arr.splice(Math.floor(Math.random()*(len+1)),1);
	}
	return result;
}
function setCookie(name, val, ex){
	var times = new Date();
	times.setTime(times.getTime() + ex);
	if(ex == 0){
		document.cookie = name+"="+val+";";
	}else{
		document.cookie = name+"="+val+"; expires="+times.toGMTString();
	}
}

function getCookie(name){
	var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));   
	if(arr != null) return unescape(arr[2]); return null;
}
