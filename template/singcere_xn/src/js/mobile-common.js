var POPMENU=new Object;var popup={init:function(){var a=this;$(".popup").each(function(c,d){d=$(d);var b=$(d.attr("href"));if(b&&b.attr("popup")){b.css({display:"none"});d.on("click",function(f){a.open(b)})}});this.maskinit()},maskinit:function(){var a=this;$("#mask").off().on("tap",function(){a.close()})},open:function(a,c,b){this.close();this.maskinit();if(typeof a=="string"){$("#ntcmsg").remove();if(c=="alert"){a='<div class="tip"><dt>'+a+'</dt><dd><input class="zg-btn-blue" type="button" value="确定" onclick="popup.close();"></dd></div>'}else{if(c=="confirm"){a='<div class="tip"><dt>'+a+'</dt><dd><input class="redirect button2" type="button" value="确定" href="'+b+'"><a href="javascript:;" onclick="popup.close();">取消</a></dd></div>'}}$("body").append('<div id="ntcmsg" style="display:none;">'+a+"</div>");a=$("#ntcmsg")}if(POPMENU[a.attr("id")]){$("#"+a.attr("id")+"_popmenu").html(a.html()).css({height:a.height()+"px",width:a.width()+"px"})}else{a.parent().append('<div class="dialogbox" id="'+a.attr("id")+'_popmenu" style="height:'+a.height()+"px;width:"+a.width()+'px;">'+a.html()+"</div>")}var f=$("#"+a.attr("id")+"_popmenu");var e=(window.innerWidth-f.width())/2;var d=(document.documentElement.clientHeight-f.height())/2;f.css({display:"block",position:"fixed",left:e,top:d,"z-index":120,opacity:1});$("#mask").css({display:"block",width:"100%",height:"100%",position:"fixed",top:"0",left:"0",background:"black",opacity:"0.2","z-index":"100"});POPMENU[a.attr("id")]=a},close:function(){$("#mask").css("display","none");$.each(POPMENU,function(a,b){$("#"+a+"_popmenu").css("display","none")})}};var dialog={init:function(){$(document).on("click",".dialog",function(){var a=$(this);popup.open('<img src="'+STATICURL+'template/singcere_faith/src/img/imageloading.gif">');$.ajax({type:"GET",url:a.attr("href")+"&inajax=1",dataType:"xml"}).success(function(b){popup.open(b.lastChild.firstChild.nodeValue);evalscript(b.lastChild.firstChild.nodeValue)}).error(function(){window.location.href=a.attr("href");popup.close()});return false})},};var formdialog={init:function(){$(document).on("click",".formdialog",function(){popup.open('<img src="'+STATICURL+'template/singcere_faith/src/img/imageloading.gif">');var b=$(this);var a=$(this.form);$.ajax({type:"POST",url:a.attr("action")+"&handlekey="+a.attr("id")+"&inajax=1",data:a.serialize(),dataType:"xml"}).success(function(c){popup.open(c.lastChild.firstChild.nodeValue);evalscript(c.lastChild.firstChild.nodeValue)}).error(function(){window.location.href=b.attr("href");popup.close()});return false})}};var redirect={init:function(){$(document).on("click",".redirect",function(){var a=$(this);popup.close();window.location.href=a.attr("href")})}};var DISMENU=new Object;var display={init:function(){var a=this;$(".display").each(function(c,d){d=$(d);var b=$(d.attr("href"));if(b&&b.attr("display")){b.css({display:"none"});b.css({"z-index":"102"});DISMENU[b.attr("id")]=b;d.on("click",function(f){if(in_array(f.target.tagName,["A","IMG","INPUT"])){return}a.maskinit();if(b.attr("display")=="true"){b.css("display","block");b.attr("display","false");$("#mask").css({display:"block",width:"100%",height:"100%",position:"fixed",top:"0",left:"0",background:"transparent","z-index":"100"})}return false})}})},maskinit:function(){var a=this;$("#mask").off().on("touchstart",function(){a.hide()})},hide:function(){$("#mask").css("display","none");$.each(DISMENU,function(a,b){b.css("display","none");b.attr("display","true")})}};function mygetnativeevent(a){while(a&&typeof a.originalEvent!=="undefined"){a=a.originalEvent}return a}function evalscript(c){if(c.indexOf("<script")==-1){return c}var d=/<script[^\>]*?>([^\x00]*?)<\/script>/ig;var a=[];while(a=d.exec(c)){var e=/<script[^\>]*?src=\"([^\>]*?)\"[^\>]*?(reload=\"1\")?(?:charset=\"([\w\-]+?)\")?><\/script>/i;var b=[];b=e.exec(a[0]);if(b){appendscript(b[1],"",b[2],b[3])}else{e=/<script(.*?)>([^\x00]+?)<\/script>/i;b=e.exec(a[0]);appendscript("",b[2],b[1].indexOf("reload=")!=-1)}}return c}var safescripts={},evalscripts=[];function appendscript(f,d,a,h){var g=hash(f+d);if(!a&&in_array(g,evalscripts)){return}if(a&&$("#"+g)[0]){$("#"+g)[0].parentNode.removeChild($("#"+g)[0])}evalscripts.push(g);var b=document.createElement("script");b.type="text/javascript";b.id=g;b.charset=h?h:(!document.charset?document.characterSet:document.charset);try{if(f){b.src=f;b.onloadDone=false;b.onload=function(){b.onloadDone=true;JSLOADED[f]=1};b.onreadystatechange=function(){if((b.readyState=="loaded"||b.readyState=="complete")&&!b.onloadDone){b.onloadDone=true;JSLOADED[f]=1}}}else{if(d){b.text=d}}document.getElementsByTagName("head")[0].appendChild(b)}catch(c){}}function hash(c,a){var a=a?a:32;var e=0;var b=0;var d="";filllen=a-c.length%a;for(b=0;b<filllen;b++){c+="0"}while(e<c.length){d=stringxor(d,c.substr(e,a));e+=a}return d}function stringxor(e,c){var f="";var g="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";var a=Math.max(e.length,c.length);for(var d=0;d<a;d++){var b=e.charCodeAt(d)^c.charCodeAt(d);f+=g.charAt(b%52)}return f}function in_array(c,b){if(typeof c=="string"||typeof c=="number"){for(var a in b){if(b[a]==c){return true}}}return false}function isUndefined(a){return typeof a=="undefined"?true:false}function setcookie(d,f,e,c,a,g){if(f==""||e<0){f="";e=-2592000}if(e){var b=new Date();b.setTime(b.getTime()+e*1000)}a=!a?cookiedomain:a;c=!c?cookiepath:c;document.cookie=escape(cookiepre+d)+"="+escape(f)+(b?"; expires="+b.toGMTString():"")+(c?"; path="+c:"/")+(a?"; domain="+a:"")+(g?"; secure":"")}function getcookie(c,a){c=cookiepre+c;var e=document.cookie.indexOf(c);var d=document.cookie.indexOf(";",e);if(e==-1){return""}else{var b=document.cookie.substring(e+c.length+1,(d>e?d:document.cookie.length));return !a?unescape(b):b}}var img={init:function(a){var b=this.errorhandle;$("img").on("load",function(){var c=$(this);c.attr("zsrc",c.attr("src"));if(c.width()<5&&c.height()<10&&c.css("display")!="none"){return b(c,a)}c.css("display","inline");c.css("visibility","visible");if(c.width()>window.innerWidth){c.css("width",window.innerWidth)}c.parent().find(".loading").remove();c.parent().find(".error_text").remove()}).on("error",function(){var c=$(this);c.attr("zsrc",c.attr("src"));b(c,a)})},errorhandle:function(d,c){if(d.attr("noerror")=="true"){return}d.css("visibility","hidden");d.css("display","none");var b=d.parent();b.find(".loading").remove();b.append('<div class="loading" style="background:url('+STATICURL+"template/singcere_faith/src/img/imageloading.gif) no-repeat center center;width:"+b.width()+"px;height:"+b.height()+'px"></div>');var a=parseInt(d.attr("load"))||0;if(a<3){d.attr("src",d.attr("zsrc"));d.attr("load",++a);return false}if(c){var b=d.parent();b.find(".loading").remove();b.append('<div class="error_text">点击重新加载</div>');b.find(".error_text").one("click",function(){d.attr("load",0).find(".error_text").remove();b.append('<div class="loading" style="background:url('+STATICURL+"template/singcere_faith/src/img/imageloading.gif) no-repeat center center;width:"+b.width()+"px;height:"+b.height()+'px"></div>');d.attr("src",d.attr("zsrc"))})}return false}};$(document).ready(function(){if($("img").length>0){img.init(1)}if($(".popup").length>0){popup.init()}if($(".display").length>0){display.init()}dialog.init();formdialog.init();redirect.init()});

