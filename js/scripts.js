var Done=[];
var maxDoneSize=25;
var toDo=[];
var additional=[]; var toDoSize=0;
var enumer=0;
var attachedFiles=[];
var outServers={
	active: false,
	position: 0,
	servers: [],
	servers_hash: "",
	updateServers: function() {
		if(this.servers_hash==$("#out_servers").val()) return;
		this.servers_hash=$("#out_servers").val();
		var tmpservers=this.servers_hash.split("\n");
		this.servers=[];
		for(var i in tmpservers) {
			if(tmpservers[i].length<3) continue;
			this.servers.push(tmpservers[i]);
		}
		this.position=0;
	},
	getServer: function() {
		if(this.position>=this.servers.length) this.position=0;
		var serv=this.servers[this.position];
		this.position++;
		return serv;
	}
}
var threadNum=4;
var timeoutNum=0;
var AddNum=1;
var status="stop";
var lastSendWasBackground=true;
var diagInfo=null;

window.navigator.sayswho= (function(){
    var ua= navigator.userAgent, tem, 
    M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
    if(/trident/i.test(M[1])){
        tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
        return 'IE '+(tem[1] || '');
    }
    if(M[1]=== 'Chrome'){
        tem= ua.match(/\bOPR\/(\d+)/)
        if(tem!= null) return 'Opera '+tem[1];
    }
    M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
    if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
    return M[0];
})();

function ChangePass(login, pass) {
	$.post('%PHP_SELF%?changepass',{login:login,pass:pass}, function(data) {
		var response=$.parseJSON(data);
		if(response['result']=='ok') 
			$("#passchangesuccess").show();
		else
			$("#passchangeerror").show();

	});
}
function pingoutservers() {
	var servers=$("#out_servers").val();
	if(servers=="") return;
	window.pingout_servers=servers.split("\n");
	window.pingout_servers_todo=window.pingout_servers.length;
	$("#out_servers").val("");
	$("#pingout_log").html("");

	update_pingoutprogress();
	for(var i=0; i<4; i++)
		pingout_server();
}
function pingout_server() {
	if(window.pingout_servers.length==0) return;
	var server=window.pingout_servers.pop();
	$.post('%PHP_SELF%?pingoutserver',{server:server}, function(data) {
		var result=$.parseJSON(data);
		var log=$("#pingout_log").html();
		if(result.status=="GOOD") {
			$("#pingout_log").html(log+"<span style='color:green;'>"+result.server+" работает</span><br>");
			$("#out_servers").val($("#out_servers").val()+result.server+"\n");
		} else {
			$("#pingout_log").html(log+"<span style='color:red;'>"+result.server+" %error%: "+result.error+"</span><br>");
		}
		$("#pingout_log").scrollTop($("#pingout_log").prop("scrollHeight"));

		update_pingoutprogress();
		setTimeout(pingout_server, 0);
	});
}
function update_pingoutprogress() {
	var pingoutservers_done=window.pingout_servers_todo-window.pingout_servers.length;
	var opb=$("#outprogressbar");
	$(opb).find(".barcounter").text(pingoutservers_done+"/"+window.pingout_servers_todo);
	var percent=parseInt(pingoutservers_done/(window.pingout_servers_todo/100));
	$(opb).find(".bar").css("width",percent+"%");
}
function Send(){
	cleValidate()
	if($("#sendInBackground").is(':checked')) {
		lastSendWasBackground=true;
		return SendInBackground();
	} else {
		lastSendWasBackground=false;
	}
	
	enumer=0;
	if($("#use_out_servers").is(':checked')) {
		outServers.updateServers();
		outServers.active=true;
	} else {
		outServers.active=false;
	}

	
	var res=$("#to").val().match(/\[FILE:(.+?)\]/);
	if(res!=null) {
		var pattern=res[0];
		var file_path=res[1];
		$.post("?linesinfile",{file_path:file_path},function(data){
			var linesinfile=parseInt(data);
			toDo=[];
			for(var i=0;i<linesinfile;i++)
				toDo.push(pattern);
			toDoSize=toDo.length;
			SetProgressBar(0,"0/"+toDoSize);
			ResumeSendMail();
		});
	} else {
		toDo=$("#to").val().split("\n");
		var perMail=parseInt($("#testEmailCounter").val());
		if(perMail>0) {
			var testEmail=$("#testEmail").val();
			toDo=mixToArchive(toDo, testEmail, perMail);
		}
		toDoSize=toDo.length;
		SetProgressBar(0,"0/"+toDoSize);
		ResumeSendMail();
	}
}
function SendInBackground() {
	var to=$("#to").val();
	var perMail=parseInt($("#testEmailCounter").val());
	if(perMail>0) {
		var testEmail=$("#testEmail").val();
		toDo=to.split("\n");
		toDo=mixToArchive(toDo, testEmail, perMail);
		to=toDo.join("\n");
	}
	var params={
		"to":$("#to").val(),
		"fromname":$("#fromname").val(),
		"frommail":$("#frommail").val(),
		"replymail":$("#replymail").val(),
		"subject"	:$("#tema").val(),
		"text"	:$("#text").val(),
		'type':$("#type").val(),
		'files':JSON.stringify(attachedFiles),
		'timeout':timeoutNum,
		'randomTimeout':$("#randomTimeout").is(":checked"),
		'sendInBase64':$("#sendInBase64").is(":checked"),
		'saveLogInTxt':$("#saveLogInTxt").is(":checked")
		//'outservers':$("#out_servers").val()
	};
	if($("#use_out_servers").is(':checked'))
		params['outservers']=$("#out_servers").val();
	var additional=new Array();
	if(additional.length==0)
		$(".additional").each(function(){
			additional.push(ReplaceEnum($(this).val()));
		});
	else
		$(".additional").each(function(index, value){
			if(index<additional.length) return;
			additional.push(ReplaceEnum($(this).val()));
		});
	for(i in additional) {
		params["additional["+i+"]"]=additional[i];
	}
	$.post("?sendInBackground", params);
	clearInterval(window.backgroudStateChecker);
	window.backgroudStateChecker=setInterval(updateBackgroundState, 5000);
}
function updateBackgroundState() {
	$.post("?getBackgroundState", function(data) {
		if(data=="null") return;
		try {
			var state=$.parseJSON(data);
		} catch(e) {
			console.log(e);
			return;
		}
		
		if(state.log!=undefined) {
			var log=state.log.split("\n");
			for(var i in log) {
				if(log[i]=="") continue;
				var logitem=log[i].split("|");
				if(window.lastBackgroundLogUpdate==undefined)
					window.lastBackgroundLogUpdate=0;
				if(window.lastBackgroundLogUpdate<logitem[0]) {
					window.lastBackgroundLogUpdate=logitem[0];
					AddDone(logitem[1]);
					DrawDone();
				}	
			}
			
		}
		
		//console.log(state);
		if(state.isRunning==false) {
			if(lastSendWasBackground) {
				SetProgressBar(state.position/(state.count/100), state.position+"/"+state.count);
			}
			$("#mainSendButton").prop("disabled", false);
			//clearInterval(window.backgroudStateChecker);
			$("#StopSendMail").prop("disabled",true);
			$(".status span").removeClass("label-success label-danger").addClass("label-warning").text("%status-idle%");
		} else {
			SetProgressBar(state.position/(state.count/100), state.position+"/"+state.count);
			$("#mainSendButton").prop("disabled", true);
			$("#StopSendMail").prop("disabled",false);
			$(".status span").removeClass("label-danger label-warning").addClass("label-success").text("%status-sending%");
		}
	});
}
function stopBackgroundSend() {
	$.post("?setBackgroundState",{isRunning:false});
}
function PauseSendMail() {
	status="pause";
	$("#PauseSendMail").prop("disabled",true);
	$("#ResumeSendMail, #StopSendMail").prop("disabled",false);
	$(".status span.tr-status-idle").removeClass("label-success label-warning").addClass("label-danger").text("%status-pause%");
}
function ResumeSendMail() {
	status="start";
	$("#ResumeSendMail").prop("disabled",true);
	$("#PauseSendMail, #StopSendMail").prop("disabled",false);
	$(".status span.tr-status-idle").removeClass("label-danger label-warning").addClass("label-success").text("%status-sending%");
	for(var i=0; i<threadNum; i++) SendMail();
}
function StopSendMail() {
	if($("#sendInBackground").is(':checked'))
		stopBackgroundSend()
	status="stop";
	StopSendMailHandler();
}
function Preview(){
	cleValidate();
	var params={
		to		:"null@null.null",
		fromname:ReplaceEnum($("#fromname").val(), false),
		frommail:ReplaceEnum($("#frommail").val(), false),
		replymail:ReplaceEnum($("#replymail").val(), false),
		tema	:ReplaceEnum($("#tema").val(), false),
		type	:$("#type").val(),
		text	:ReplaceEnum($("#text").val()),
		enumer  :(toDoSize-toDo.length)
	};
	if(additional.length==0)
		$(".additional").each(function(){
			additional.push(ReplaceEnum($(this).val()));
		});
	else
		$(".additional").each(function(index, value){
			if(index<additional.length) return;
			additional.push(ReplaceEnum($(this).val()));
		});
	for(i in additional) {
		params["additional["+i+"]"]=additional[i];
	}
	$.post('%PHP_SELF%?preview', params, function(data){
		if($("#type").val()=='text')
			showPreviewWindow('data:text/plain;charset=utf-8;base64,'+Base64.encode(data));
		else
			showPreviewWindow('data:text/html;charset=utf-8;base64,'+Base64.encode(data));
			});
}
function StopSendMailHandler() {
	//alert("Рассылка завершена!");
	$(".status span.tr-status-idle").removeClass("label-success label-danger").addClass("label-warning").text("%status-idle%");
	$(".status .btn").prop("disabled",true);
}
function SendMail() {
	if(status=="pause")
		return;
	else if(status=="stop") {
		//toDo=[];
		//toDoSize=0;
		//SetProgressBar((toDoSize-toDo.length)/(toDoSize/100),(toDoSize-toDo.length)+"/"+toDoSize);
		return;
	}
	
	if(toDo.length==0) {//!email || email=="" || email==" ") {
		//останавливаем остальные потоки и генерируем событие окончания рассылки
		status="stop";
		StopSendMailHandler();
		return;
	}
	var email=toDo.pop();
	
	additional=new Array();
	if(email.indexOf(";")!=-1) {
		var emailadds=email.split(";");
		for(var adds in emailadds) {
			if(adds==0) 
				email=emailadds[0];
			else
				additional.push(emailadds[adds]);
		}
	}
	var params={
		to		:email,
		fromname:ReplaceEnum($("#fromname").val(), false),
		frommail:ReplaceEnum($("#frommail").val(), false),
		replymail:ReplaceEnum($("#replymail").val(), false),
		tema	:ReplaceEnum($("#tema").val(), false),
		type	:$("#type").val(),
		captcha_code:$("input[name=captcha_code]").val(),
		text	:ReplaceEnum($("#text").val()),
		enumer  :(toDoSize-toDo.length),
		sendInBase64:$("#sendInBase64").is(":checked"),
		saveLogInTxt:$("#saveLogInTxt").is(":checked")
	};
	//1.6.5 (16.09.2013) прокси
	if($("#use_proxy_server").is(":checked")) {
		params['PROXY']=$("#proxy_server_uri").val();
		//2.0.8 (08.12.2014) multiproxy
		if(params['PROXY']) {
			var proxy_list=params['PROXY'].split("\n");
			if(proxy_list.length>1) params['PROXY']=proxy_list[params['enumer']%proxy_list.length];
		}
	}

	if(attachedFiles.length!=0) params['files']=JSON.stringify(attachedFiles);

	if(additional.length==0)
		$(".additional").each(function(){
			additional.push(ReplaceEnum($(this).val()));
		});
	else
		$(".additional").each(function(index, value){
			if(index<additional.length) return;
			additional.push(ReplaceEnum($(this).val()));
		});
	for(i in additional) {
		params["additional["+i+"]"]=additional[i];
	}
	if(outServers.active) params["outserver"]=outServers.getServer();

	$.post('%PHP_SELF%?send', params, function(data){
		if(window.alexusMailerServiceMode) {
			if(data=="CAPTCHA ERROR") {
				$("#console").html("%badcaptcha%");
				$("#console").css("background-color","Tomato");
				document.getElementById('captcha').src = '/securimage/securimage_show.php?' + Math.random();
				return;
			} else if(data=="OUT OF LIMIT") {
				$("#console").html("%sendlimit%");
				$("#console").css("background-color","Tomato");
				document.getElementById('captcha').src = '/securimage/securimage_show.php?' + Math.random();
				return;
			}
			document.getElementById('captcha').src = '/securimage/securimage_show.php?' + Math.random();
		}	
		
		$("#console").html(data.replace("<","&lt;").replace(">","&gt;"));
		$("#console").css("background-color","YellowGreen");
		var email=data.split(" ");
		if(email.length>3)
			AddDone(email[3].replace("<","").replace(">",""));
		else
			AddDone(email[2]);
		DrawDone();
		SetProgressBar((toDoSize-toDo.length)/(toDoSize/100),(toDoSize-toDo.length)+"/"+toDoSize);
		if(timeoutNum==0)
			SendMail();
		else {
			ctimeoutNum=timeoutNum;
			if($("#randomTimeout").is(":checked")) {
				ctimeoutNum+=getRandomInt(-3, 3);
				if(ctimeoutNum<0) timeoutNum=0;
			}
			setTimeout(function(){SendMail()},ctimeoutNum*1000);
		}
	});
}
function SetProgressBar(count,text) {
	var psize=parseInt($("#progressbar").css("width"))/100;
	$("#progressbar .bar").css("width",count+"%");
	$("#progressbar .barcounter").text(text);
}
function AddDone(item) {
	if(Done.length>maxDoneSize)
		Done.splice(0,1);
	Done.push(item);
}
function DrawDone() {
	$("#ext-console").html("");
	var txt="";
	for(i in Done) {
		txt=Done[i]+"<br>"+txt;
	}
	$("#ext-console").html(txt);
}
/*function dThreads(){
	if(threadNum==1) return;
	threadNum--;
	ShowThreads();
}
function iThreads(){
	if(threadNum==100) return;
	threadNum++;
	ShowThreads();
}*/
function ShowThreads() {
	$("#THREADS").text(threadNum);
}
//универсальный загручик
function showUniversalUpload() {
	if($("#univarsalUpload").css("display")=="none") {
		$("#univarsalUpload").find("iframe").attr("src","%PHP_SELF%?upload_universal");
		$("#univarsalUpload").modal("show");
	} else $("#univarsalUpload").modal("hide");
}
function UploadClickHandler(object) {
	$(object).parent().parent().find("iframe").contents().find("form").submit();
}
window.uploadFinishedHandler=null;

//аплоад аттача
function attachFileHandler(data) {
	if(data==null) return;
	var file=$.parseJSON(Base64.decode(data));
	attachedFiles.push(file);
	updateFileList();
	$("#univarsalUpload").modal("hide");
}
function showAttachUpload() {
	$("#univarsalUpload").find("h4").html("%attachfile%");	
	window.uploadFinishedHandler=attachFileHandler;
	showUniversalUpload();
}
//прикрепление и удаление аттача
function updateFileList() {
	$("#attachedFiles").html("");
	var i=0;
	for(var file in attachedFiles) {
		if(attachedFiles[file]==null) continue;
		if(file=='remove') continue;
		$("#attachedFiles").html($("#attachedFiles").html()+(i!=0?"<br>":"")+"<i class='glyphicon glyphicon-file'></i> "+attachedFiles[file]['name']+" <button class='btn btn-default' onclick='removeFile("+file+");return false'>%delete%</button>");
		i++;
	}
}
function removeFile(id) {
	delete attachedFiles[id];
	updateFileList();
}
//аплоад списочного поля
function uploadListField(element) {
	$("#univarsalUpload").find("h4").html("%uploadlist%");
	window.uploadFinishedHandler=uploadListFieldHandler;
	$(element).addClass("uploadListField");
	showUniversalUpload();
}
function uploadListFieldHandler(data) {
	if(data==null) return;
	var file=$.parseJSON(Base64.decode(data));
	$(".uploadListField").val(Base64.decode(file.content));
	$(".uploadListField").removeClass("uploadListField");
	$("#univarsalUpload").modal("hide");
}
//аплоад шаблона
function uploadTemplate() {
	$("#univarsalUpload").find("h4").html("%uploadtemplate%");
	window.uploadFinishedHandler=uploadTemplateHandler;
	showUniversalUpload();
}
function uploadTemplateHandler(data) {
	if(data==null) return;
	var file=$.parseJSON(Base64.decode(data));
	//loadsave(Base64.decode(file.content));
	LoadTemplate(Base64.decode(file.content));
	$("#univarsalUpload").modal("hide");
}
//загрузка шаблона
function loadsave(data) {
	var content=data.split("[TEXT]");
	$("#text").val(content[1].replaceAll("&amp;","&"));
	content=content[0].split("\n");
	for(var i in content) {
		if(content[i].indexOf("[FROM-NAME]")!=-1) {
			content[i]=content[i].replace("[FROM-NAME]","");
			$("#fromname").val(content[i]);
		} else if(content[i].indexOf("[FROM-EMAIL]")!=-1) {
			content[i]=content[i].replace("[FROM-EMAIL]","");
			$("#frommail").val(content[i]);
		} else if(content[i].indexOf("[THEME]")!=-1) {
			content[i]=content[i].replace("[THEME]","");
			$("#tema").val(content[i]);
		} else if(content[i].indexOf("[TYPE]")!=-1) {
			content[i]=content[i].replace("[TYPE]","");
			$("#type [value='"+content[i]+"']").attr("selected", "selected");
		} else if(content[i].indexOf("[FILES]")!=-1) {
			content[i]=content[i].replace("[FILES]","");
			attachedFiles=$.parseJSON(content[i]);
			updateFileList();
		} else if(content[i].indexOf("[ADD")!=-1) {
			var result=/\[ADD(\d+)\]/.exec(content[i]);
			if(AddNum<=result[1]) {
				AddField($(".addfield:last"));
			}
			content[i]=content[i].replace(result[0],"");
			$("#additional"+result[1]).val(content[i]);
		}
	}
}
function LoadTemplate(raw_data) {
	//определяем формат шаблона (Новый\Старый)
	try {
		var data=$.parseJSON(raw_data);
	} catch(e) {
		console.log(e);
		console.log("Using old template format");
		loadsave(raw_data);
		window.cle.updateFrame();
		return;
	}
	//новый формат
	$("#text").val(Base64.decode(data.text));
	$("#fromname").val(data.fromname);
	$("#frommail").val(data.frommail);
	$("#replymail").val(data.replymail);
	$("#tema").val(data.subject);
	$("#type [value='"+data.type+"']").attr("selected", "selected");
	
	if(data.files!=null) {
		attachedFiles=data.files;
		updateFileList();	
	}

	for(var i in data.additional) {
		if(AddNum<=i) AddField($(".addfield:last"));
		$("#additional"+i).val(data.additional[i]);
	}
	window.cle.updateFrame();
}
//сохранение шаблона
function SaveTemplate() {
	cleValidate();
	$("#univarsalUpload").find("iframe").attr("src","%PHP_SELF%?savedata").load(function(){
		$("univarsalUpload").find("iframe").unbind("load");
		var data={
			fromname: $("#fromname").val(),
			frommail: $("#frommail").val(),
			replymail: $("#replymail").val(),
			subject: $("#tema").val(),
			type: $("#type").val(),
			additional: [],
			files: null,
			text: Base64.encode($("#text").val())
		};
		
		$(".additional").each(function(index, value){
			data.additional.push($(value).val());
		});	
		if(attachedFiles.length!=0) {
			data.files=attachedFiles;
		}

		var filedata=JSON.stringify(data);

		$("input[name=filename]",$(this).contents()).val("template.json");
		$("textarea",$(this).contents()).val(filedata);
		$("form",$(this).contents()).submit();
	});
}
//устаревшее сохранение шаблона
/*
function SaveData() {
	$("#univarsalUpload").find("iframe").attr("src","%PHP_SELF%?savedata").load(function(){
		$("univarsalUpload").find("iframe").unbind("load");
				var data="[FROM-NAME]"+$("#fromname").val()+"\n"+"[FROM-EMAIL]"+$("#frommail").val()+"\n"+"[THEME]"+$("#tema").val()+"\n"+"[TYPE]"+$("#type").val()+"\n";
				$(".additional").each(function(index, value){
			data+="[ADD"+index+"]"+$(value).val()+"\n";
		});	
				if(attachedFiles.length!=0) data+='[FILES]'+JSON.stringify(attachedFiles)+"\n";
		
				data+="[TEXT]"+$("#text").val().replaceAll("&","&amp;");
		
		$("textarea",$(this).contents()).val(data);
		$("form",$(this).contents()).submit();
	});
}
*/
//Добавление доп. поля
function AddField(object) {
	$(object).parent().parent().after('<div class="col-sm-7 input-group extadd"><span class="input-group-addon">[ADD'+AddNum+']</span><input type="text" name="additional'+AddNum+'" id="additional'+AddNum+'" class="input-xlarge txtinput additional form-control" placeholder="%addfield2% '+AddNum+'"> <span class="input-group-addon"><span class="addfield" onclick="AddField(this)">+</span></span></div>');
	$(object).parent().remove();
	AddNum++;
}
function textareaResizable() {
	if(navigator.sayswho=="Chrome" || navigator.sayswho=="Opera") {
		$("#resizableTextContainer").css({
			resizable:"vertical"
		});
	} /*else {//if(navigator.sayswho=="Firefox") {
		//Тут устанавливаются обработчики размеров
		$("#resizableTextContainer").resize(function() {
			var size=$(this).height();
			console.log(size);
			$("#resizableTextContainer .cleditorMain").height(size);
			$("#resizableTextContainer .cleditorMain iframe").height(size-27);
		});
	}*/
}
$(document).ready(function(){
	
	$("#prime, .prime-button").click(function(){
		$(".section-screen").hide();
		$("#prime-screen").show();
	});
	$("#help").click(function(){
		$(".section-screen").hide();
		$("#help-screen").show();
	});
	$("#settings").click(function(){
		$(".section-screen").hide();
		$("#settings-screen").show();
	});
	$(".btn-checkbox").click(function() {
		if($(this).hasClass("btn-success")) {
			$($(this).attr("data-toggle")).prop("checked",false);
			$(this).removeClass("btn-success").addClass("btn-danger").children("i").removeClass("glyphicon glyphicon-ok").addClass("glyphicon glyphicon-remove");
		} else {
			$($(this).attr("data-toggle")).prop("checked",true);
			$(this).removeClass("btn-danger").addClass("btn-success").children("i").removeClass("glyphicon glyphicon-remove").addClass("glyphicon glyphicon-ok");
		}
	});
	if(navigator.sayswho=="Chrome") {// || navigator.sayswho=="Opera") {
		window.cle=$("#text").cleditor({height: "auto"})[0];
	} else {
		window.cle=$("#text").cleditor({height: 200})[0];
		$("#resizableTextContainer").css({
			"overflow":"hidden"
		}).attrchange({
	        callback: function (e) {
	            var height=$(this).height();
	            $("#resizableTextContainer .cleditorMain").height(height-3);
	            $("#resizableTextContainer .cleditorMain iframe").height(height-30);
	            /*var curHeight = $(this).height();            
	            if (prevHeight !== curHeight) {
	               $('#logger').text('height changed from ' + prevHeight + ' to ' + curHeight);
	                
	                prevHeight = curHeight;
	            } */           
	        }
	    });
	}
	//textareaResizable();
	$("#type").change(function() {
		if($(this).val()=="text")
			cleToTextMode(true);
		else
			cleToTextMode(false);
	});
	$("#maxDoneSize").change(function() {
		maxDoneSize=$(this).val();
	});
	//alexusRange.create($("#TIMEOUT_RANGE"));
	/*alexusRange.change($("#TIMEOUT_RANGE"),function(range) {
		isetTimeout(alexusRange.get($(range)));
		$("#TIMEOUT").val(timeoutNum);
	});
	*/
	$("#TIMEOUT").change(function() {
		isetTimeout($(this).val());
		$("#TIMEOUT").val(timeoutNum);
	//	alexusRange.set($("#TIMEOUT_RANGE"),timeoutNum);
	});
	$("#THREADS").change(function() {
		var threads=$(this).val();
		if(threads<1)
			threadNum=1;
		else if(threads>100)
			threadNum=100;
		else
			threadNum=threads;
	});
	$("#timezone").change(function() {
		setCookie('timezone',$(this).val(),{path:"/"});
	});
	$("#testEmailCounter").change(function() {
		var count=parseInt($(this).val());
		if(count>0)
			$("#testEmail").prop("readonly", false);
		else {			
			$("#testEmail").val("").prop("readonly", true);
		}
	});
	/*alexusRange.set($("#TIMEOUT_RANGE"),timeoutNum);*/
	$("[rel=tooltip]").tooltip();
	ShowThreads();
	loadSettings();
	if(!window.alexusMailerServiceMode) {
		updateBackgroundState();
		window.backgroudStateChecker=setInterval(updateBackgroundState, 5000);
	}
	selfDiagnostics();
	//Пробуем запустить функции сервиса если они есть
	try {
		serviceFunc();
	} catch(e) {}
});

function isetTimeout(val) {
	var newTimeout=parseInt(val);
	if(newTimeout<0) 
		timeoutNum=0;
	else if(newTimeout>14400)
		timeoutNum=14400;
	else
		timeoutNum=newTimeout;
}
function MakeBold() {
	wrapText("text","<b>","</b>");
}
function MakeItalic() {
	wrapText("text","<i>","</i>");
}
function MakeUnderline() {
	wrapText("text","<u>","</u>");
}
function ReplaceEnum(data) {
	return data;
	//Этот функционал перенесен в php часть
}
function MakeReverse() {
	var textArea = $("#text");
    var len = textArea[0].value.length;
    var start = textArea[0].selectionStart;
    var end = textArea[0].selectionEnd;
    var selectedText = textArea[0].value.substring(start, end);
    selectedText=selectedText.split("").reverse().join("");
    var replacement = "<span style=\"direction: rtl;unicode-bidi: bidi-override;\">" + selectedText + "</span>";
    textArea[0].value=textArea[0].value.substring(0, start) + replacement + textArea[0].value.substring(end, len);
}
function wrapText(elementID, openTag, closeTag) {
    var textArea = $('#' + elementID);
    var len = textArea[0].value.length;
    var start = textArea[0].selectionStart;
    var end = textArea[0].selectionEnd;
    var selectedText = textArea[0].value.substring(start, end);
    var replacement = openTag + selectedText + closeTag;
    textArea[0].value=textArea[0].value.substring(0, start) + replacement + textArea[0].value.substring(end, len);
}
function showPreviewWindow(link) {
	$("#preview-screen").find("iframe").attr("src", link);
	$(".section-screen").hide();
	$("#preview-screen").show();
	$(window).scrollTop(0);
}
/*var alexusRange={
	mouseX:0,
	newX:0,
	rangeCounter:0,
	changeHandlers:{},
	create:function(range) {
		$(range).addClass("alexus-range").addClass("well").append('<div class="crange"></div><div class="range-controller btn"></div>');
		if(alexusRange.rangeCounter==0) $(document).mouseup(alexusRange.mouseupHandler);
		$(range).children(".range-controller").mousedown(alexusRange.mousedownHandler);
		alexusRange.rangeCounter++;
		$(range).attr("ruqid",alexusRange.rangeCounter);
	},
	RangeMouseMoveHandler:function(e) {
				if(alexusRange.mouseX==0) alexusRange.mouseX=e.pageX;
		alexusRange.newX=e.pageX
		if(e.pageX!=alexusRange.mouseX)
			$(".alexus-range .range-controller[state=inmove]").each(function() {
				var max_pos=parseInt($(this).parent().css("width"))-parseInt($(this).css("width"))-2;
				var cur_pos=parseInt($(this).css("margin-left"));
				var delta=alexusRange.mouseX-alexusRange.newX;
				if(cur_pos-delta<0) 
					cur_pos=0;
				else if(cur_pos-delta>max_pos) 
					cur_pos=max_pos;
				else
					cur_pos-=delta;
				$(this).css("margin-left", cur_pos);
				$(this).parent().children(".crange").css("width",parseInt(cur_pos/(max_pos/100))+"%");
				if(alexusRange.changeHandlers[alexusRange.uniqueId($(this).parent())]!=undefined) 
					alexusRange.changeHandlers[alexusRange.uniqueId($(this).parent())]($(this).parent());
			});
		alexusRange.mouseX=alexusRange.newX;
	},
	change:function(range, handler) {
		alexusRange.changeHandlers[alexusRange.uniqueId($(range))]=handler;
	},
	mousedownHandler:function() {
				$(this).attr("state","inmove");
		$(document).mousemove(alexusRange.RangeMouseMoveHandler); 
	},
	mouseupHandler:function() {
		$(".alexus-range .range-controller[state=inmove]").attr("state","relax");
		$(document).unbind("mousemove", alexusRange.RangeMouseMoveHandler);
		alexusRange.ResetPos(); 
	},
	ResetPos:function() {
		alexusRange.mouseX=0;
	},
	get:function(range) {
		var max_pos=parseInt($(range).css("width"))-parseInt($(range).children(".range-controller").css("width"))-2;
		var cur_pos=parseInt($(range).children(".range-controller").css("margin-left"));
		var max_val=parseInt($(range).attr("max"));
		var min_val=parseInt($(range).attr("min"));
		return parseInt((max_val-min_val)*(cur_pos/max_pos))+min_val;	
	},
	set:function(range, val) {
		var max_pos=parseInt($(range).css("width"))-parseInt($(range).children(".range-controller").css("width"))-2;
		var cur_pos=parseInt($(range).children(".range-controller").css("margin-left"));
		var max_val=$(range).attr("max");
		var min_val=$(range).attr("min");
		var pos=parseInt(max_pos*(((val-min_val)/(max_val-min_val))));
		if(pos<0) 
			pos=0;
		else if(pos>max_pos) 
			pos=max_pos;
		$(range).children(".range-controller").css("margin-left",pos);
		$(range).children(".crange").css("width",parseInt(pos/(max_pos/100))+"%");
	},
	uniqueId:function(range) {
		return $(range).attr("ruqid");
	}
}*/
function setLang(code) {
	if(code=='ru') {
		setCookie('translation','ru',{path:"/"});
		document.location.reload();
	} else {
		setCookie('translation',code,{path:"/"});
		//document.location="/"+code+"/";
		document.location.reload();
	}
}
function setCookie(name, value, props) {
    props = props || {}
    var exp = props.expires
    if (typeof exp == "number" && exp) {
        var d = new Date()
        d.setTime(d.getTime() + exp*1000)
        exp = props.expires = d
    }
    if(exp && exp.toUTCString) { props.expires = exp.toUTCString() }
 
    value = encodeURIComponent(value)
    var updatedCookie = name + "=" + value
    for(var propName in props){
        updatedCookie += "; " + propName
        var propValue = props[propName]
        if(propValue !== true){ updatedCookie += "=" + propValue }
    }
    document.cookie = updatedCookie
 
}
function cleValidate() {
	var current_mode_is_text=$(".cleditorButton:last").attr("title")=="Show Rich Text";
	if(!current_mode_is_text)
		window.cle.updateTextArea();
}
function cleToTextMode(textMode) {
	var current_mode_is_text=$(".cleditorButton:last").attr("title")=="Show Rich Text";
	if(textMode && !current_mode_is_text) {
		$(".cleditorButton:last").click();
		return;
	} 
	if(!textMode && current_mode_is_text) {
		$(".cleditorButton:last").click();
		return;
	}
}
function saveSettings() {
	var settings={
		threads: threadNum,
		timeout: timeoutNum,
		randomTimeout: $("#randomTimeout").is(":checked"),
		maxDoneSize: maxDoneSize,
		useProxy: $("#use_proxy_server").is(":checked"),
		proxy: $("#proxy_server_uri").val(),
		useOutServers: $("#use_out_servers").is(":checked"),
		outServers: $("#out_servers").val(),
		sendInBackground: $("#sendInBackground").is(":checked"),
		sendInBase64: $("#sendInBase64").is(":checked"),
		saveLogInTxt: $("#saveLogInTxt").is(":checked")
	};
	$.post("%PHP_SELF%?saveSettings", {settings: JSON.stringify(settings)}, function(data) {
		bootbox.alert(data);
	});
}
function removeSettings() {
	$.post("%PHP_SELF%?removeSettings", function(data) {
		bootbox.alert(data);
		var default_settings={
			threads: 4,
			timeout: 0,
			maxDoneSize: 25,
			useProxy: false,
			proxy: "",
			useOutServers: false,
			outServers: "",
			sendInBase64: false,
			saveLogInTxt: false,
			randomTimeout: false
		};
		applySettings(default_settings);
	});
}
function loadSettings() {
	$.post("%PHP_SELF%?loadSettings", function(data) {
		if(data=="") return;
		var settings=$.parseJSON(data);
		applySettings(settings);
	});
}
function applySettings(settings) {
	threadNum=settings.threads;
	ShowThreads();
	isetTimeout(settings.timeout);
	$("#TIMEOUT").val(settings.timeout);
	maxDoneSize=settings.maxDoneSize;
	$("#maxDoneSize").val(settings.maxDoneSize);

	$("#proxy_server_uri").val(settings.proxy);
	$("#out_servers").val(settings.outServers);

	var checkboxes={
		useProxy: "use_proxy_server",
		useOutServers: "use_out_servers",
		sendInBackground: "sendInBackground",
		sendInBase64: "sendInBase64",
		saveLogInTxt: "saveLogInTxt",
		randomTimeout: "randomTimeout"
	};
	for(var name in checkboxes) {
		if(!settings[name]) {
			$("#"+checkboxes[name]).prop("checked",false);
			$("[data-toggle=#"+checkboxes[name]+"]").removeClass("btn-success").addClass("btn-danger").children("i").removeClass("glyphicon glyphicon-ok").addClass("glyphicon glyphicon-remove");
		} else {
			$("#"+checkboxes[name]).prop("checked",true);
			$("[data-toggle=#"+checkboxes[name]+"]").removeClass("btn-danger").addClass("btn-success").children("i").removeClass("glyphicon glyphicon-remove").addClass("glyphicon glyphicon-ok");
		}
	}
}
function selfDiagnostics() {
	$("#diagnostics-tab table").animate({opacity:0}, "fast", function() {
		$.post("?selfDiagnostics", function(data) {
			diagInfo=$.parseJSON(data);
			showDiagInfo();
			$("#diagnostics-tab table").animate({opacity:1}, "fast");
		});
	});	
}
function showDiagInfo() {
	//console.log(diagInfo);
	$("#diagnostics-tab tr").removeClass("diag-good diag-bad");
	for(var key in diagInfo) {
		var text="";
		switch(typeof diagInfo[key]) {
			case "boolean":
				text=diagInfo[key]?"%state_set%":"%state_unset%";
				if(key=="shells_available")
					text=diagInfo[key]?"%state_exist%":"%state_not_exist%";
				if(key=="allow_url_fopen")
					text=diagInfo[key]?"%state_allowed%":"%state_not_allowed%";
				break;
			default:
				text=diagInfo[key];
				break;
		}
		$("#diagnostics-tab ."+key).
		addClass(diagInfo[key]?"diag-good":"diag-bad").
		find("td:nth-child(2)").text(text);
	}
	//Вывод предупреждений
	if(!diagInfo.file_is_writable) 
		$("#settings-security-notwritable").show();
	else
		$("#settings-security-notwritable").hide();

	if(!diagInfo.settings_is_writable) 
		$("#settings-notwritable").show();
	else
		$("#settings-notwritable").hide();

	if(!diagInfo.shells_available || !diagInfo.allow_url_fopen) 
		$("#shells-notavalable").show();
	else
		$("#shells-notavalable").hide();

	if(!diagInfo.bgfiles_is_writable)
		$("#send-in-background-notavalable").show();
	else
		$("#send-in-background-notavalable").hide();
}
String.prototype.replaceAll = function(search, replace){
  return this.split(search).join(replace);
}
function mixToArchive(archive, item, per) {
	var new_archive=[];
	for(var i in archive) {
		new_archive.push(archive[i]);
		if(i!=0 && i%per==0)
			new_archive.push(item);
	}
	return new_archive;
}
function getRandomInt(min, max) {
  return Math.floor(Math.random() * (max - min)) + min;
}
var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
