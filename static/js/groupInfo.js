function setDownloadLink(){
	//ÏÂÔØÁ´½ÓÊÊÅä  PCä¯ÀÀÆ÷ÊÊÅä
	var browser = {
		versions: function() {
			var u = navigator.userAgent;
			return {
				ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),
				android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, 
				iPhone: u.indexOf('iPhone') > -1 || u.indexOf('Mac') > -1,
				iPad: u.indexOf('iPad') > -1,
				mobile: !!u.match(/AppleWebKit.*Mobile.*/) || !!u.match(/AppleWebKit/),
			};
		}()
	}
	if (browser.versions.ios || browser.versions.iPhone || browser.versions.iPad) {
		var iosLink = "https://itunes.apple.com/app/id1051559419";
		document.getElementById("downLink").href = iosLink;
	}else if (browser.versions.android) {
		var androidLink = "https://play.google.com/store/apps/details?id=com.igg.android.linkmessenger";
		document.getElementById("downLink").href = androidLink;
	}
}

function setValues(){
	if(group.cover==""){
		group.cover="static/image/noCover.jpg";
	}
	document.getElementById("coverImg").src = group.cover;
	if(group.head==""){
		group.head="static/image/groupDefault.png"
	}
	document.getElementById("headDiv").style.backgroundImage = 'url(' + group.head + ')';
	document.getElementById("idSpan").innerHTML = group.id;
	if(allEnglish(group.name)){
		if(group.name.length>32){
			group.name=group.name.substr(0,30)+"...";
		}
	}else{
		if(group.name.length>17){
			group.name=group.name.substr(0,16)+"...";
		}
	}
	document.getElementById("nameSpan").innerHTML = group.name;
	//document.getElementById("dysSpan").innerHTML = group.days;
	document.getElementById("addCodeImg").src = 'http://chart.apis.google.com/chart?cht=qr&chld=M|1&chs=300x300&chl=http://linkmessenger.com/lm/d/?gid='+group.id;
	if(group.head=="static/image/groupDefault.png"){
		group.head="static/image/ivo.png"
	}
	document.getElementById("qrCodeImg").style.backgroundImage = 'url(' + getHeadUrl(group.head) + ')';
	if(group.about==""){
		document.getElementById("aboutDiv").innerHTML=labels.common_txt_null;
	}else{
		document.getElementById("aboutDiv").innerHTML = group.about;
	}
	document.getElementById("ownerHeadDiv").style.backgroundImage = 'url(' + getHeadUrl(group.owner.head) + ')';
	document.getElementById("ownerName").innerHTML = group.owner.name;
	document.getElementById("memberNum").innerHTML = group.memberNum;
	var id = 0;
	for(var member in group.members){
		var memberHead = getHeadUrl(group.members[member]);	
		var headDiv = document.getElementById("memberHead"+id);
		headDiv.style.backgroundImage = 'url(' + memberHead + ')';
		headDiv.onclick = function(){infoDownlod();};
		id++;
		if(6==id){
			document.getElementById("line2").style.display = "block";
		}else if(11==id){
			document.getElementById("line3").style.display = "block";
		}else if(id>13){
			break;
		}
	}
	if(group.memberMore=="yes"){
		document.getElementById("moreButton").style.display = "block";
		document.getElementById("moreButton").onclick = function(){infoDownlod();};
	}
}

function getHeadUrl(input){
	if(input=="0"){
			return "static/image/default.png";
		}else if(input=="1"){
			return "static/image/maleDefault.png";
		}else if(input=="2"){
			return "static/image/femaleDefault.png";
		}else{
			return input;
		}
}

function setLabels(){
	document.getElementById("app_name").innerHTML = labels.app_name;
	document.getElementById("web_group_txt_admin").innerHTML = labels.web_group_txt_admin;
	document.getElementById("web_group_txt_qrcode").innerHTML = replaceAppName(labels.web_group_txt_qrcode, labels.app_name);
	document.getElementById("web_group_txt_member").innerHTML = labels.web_group_txt_member;
	document.getElementById("app_name1").innerHTML = labels.app_name;
	document.getElementById("downLink").innerHTML = replaceAppName(labels.web_group_url_download, labels.app_name);
	document.getElementById("web_group_txt_scan").innerHTML = "&nbsp;"+labels.web_group_txt_scan+"&nbsp;";
	document.getElementById("web_group_txt_download").innerHTML = replaceAppName(labels.web_group_txt_download, labels.app_name);
}

function replaceAppName(old, replacement){
	return old.replace("%1$s", replacement);
}

function allEnglish(input){
	 for(var i=0; i<input.length; i++) { 
		var ch = input.charAt(i);
		if(ch<' ' || ch>'~'){
			return false;
		}
	 }
	 return true;
}

function infoDownlod(){
	alert(replaceAppName(labels.web_group_txt_more_tips, labels.app_name));
}
