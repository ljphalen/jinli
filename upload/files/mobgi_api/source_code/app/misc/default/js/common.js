function confirm_redirect(url,text) {
	if (confirm(text)) {
			window.location = url;
	}
}

function doselectAll(theBox){
  	xState=theBox.checked;
  	elm=theBox.form.elements;
	
  	for(i=0;i<elm.length;i++) {
   		if(elm[i].type=="checkbox") {
			elm[i].checked=xState;
		}
	}
}

function ToggleNode(nodeObject){
         if (nodeObject.style.display == '' || nodeObject.style.display == 'inline') {
             nodeObject.style.display = 'none';
             
         } else {
             nodeObject.style.display = 'inline';
            
         }
}

function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}


function JM_cc(ob){

var obj=MM_findObj(ob); if (obj) { 
obj.select();js=obj.createTextRange();js.execCommand("Copy");}
}

// =======================================================================================================

function initSelected(sObj, sValue)
{
	var i;

	for (i=0; i<sObj.length; i++) {
		if (sObj.options[i].value == sValue) {
			sObj.options[i].selected = true;
			return;
		}
	}
}

function resize_image(theimage,size) 
{
	if (theimage.width>theimage.height) {	
		if (theimage.width>size) theimage.width=size;
	} else {
		if (theimage.height>size) theimage.height=size;
	}
}


// Example: obj = findObj("image1");
function findObj(theObj, theDoc)
{
  var p, i, foundObj;
  
  if(!theDoc) theDoc = document;
  if( (p = theObj.indexOf("?")) > 0 && parent.frames.length)
  {
    theDoc = parent.frames[theObj.substring(p+1)].document;
    theObj = theObj.substring(0,p);
  }
  if(!(foundObj = theDoc[theObj]) && theDoc.all) foundObj = theDoc.all[theObj];
  for (i=0; !foundObj && i < theDoc.forms.length; i++) 
    foundObj = theDoc.forms[i][theObj];
  for(i=0; !foundObj && theDoc.layers && i < theDoc.layers.length; i++) 
    foundObj = findObj(theObj,theDoc.layers[i].document);
  if(!foundObj && document.getElementById) foundObj = document.getElementById(theObj);
  
  return foundObj;
}



/*
Select and Copy form element script- By Dynamicdrive.com
For full source, Terms of service, and 100s DTHML scripts
Visit http://www.dynamicdrive.com
*/

//specify whether contents should be auto copied to clipboard (memory)
//Applies only to IE 4+
//0=no, 1=yes
var copytoclip=1

function HighlightAll(tempval) {
	
	tempval.focus()
	tempval.select()
	if (document.all && copytoclip==1){
		therange = tempval.createTextRange()
		therange.execCommand("Copy")
		window.status = "Contents highlighted and copied to clipboard!"
		setTimeout("window.status=''", 1800)
	}
}

function go_format(editorId) {
	tinyMCE.getInstanceById(editorId).getBody();
	window.open("format_string.php?editorId="+editorId, "format_window","width=653,height=622,scrollbars=no") ;
}

function string_format(from, to) {
	var xmlHttp;
	var s = fixContent(GetTinyMceContent(from));
	var url = 'format_string.php?type=ajax';
	var content = 's='+encodeURIComponent(s);
	createXMLHttpRequest();
	xmlHttp.onreadystatechange = handleStateChange;
	xmlHttp.open('POST', url, true);
    xmlHttp.setRequestHeader("Content-Length", content.length);
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttp.send(content);	

	function createXMLHttpRequest() {
		if (window.ActiveXObject) {
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		} else if (window.XMLHttpRequest) {
			xmlHttp = new XMLHttpRequest();
		}
	}
		
	function handleStateChange() {
		if(xmlHttp.readyState == 4) {
			if(xmlHttp.status == 200) {
				SetTinyMceContent(to,xmlHttp.responseText) 
			}
		}
	}
}

function GetTinyMceContent(editorId) {
	return tinyMCE.getInstanceById(editorId).getBody().innerHTML;
}

function SetTinyMceContent(editorId,content) {
	tinyMCE.getInstanceById(editorId).getBody().innerHTML=content;
}

function fixContent(html) {
	html = html.replace(new RegExp('<(p|hr|table|tr|td|ol|ul|object|embed|li|blockquote)', 'gi'),'\n<$1');
	html = html.replace(new RegExp('<\/(p|ol|ul|li|table|tr|td|blockquote|object)>', 'gi'),'</$1>\n');
	html = tinyMCE.regexpReplace(html, '<br />','<br />\n','gi');
	html = tinyMCE.regexpReplace(html, '\n\n','\n','gi');
	return html;
}


function uploadImage(){
	var parm = "width=500px,height=200px,resizable=no,status=no,help=no,scroll=no,edge=raised";
	window.open('/image.php', 'window', parm);
}

function uploadImageComplete(filename){
	editor.tsetCommand('ImageS', 'http://misc.xiaozu.xunlei.com/upload_img/'+filename);	
}

var isNull     = function(a){ return typeof a == "object" && !a; };
var Browser = {
	getCookie: function(label){
		return isNull(document.cookie.match(new RegExp("(^"+label+"| "+label+")=([^;]*)"))) ? "" : decodeURIComponent(RegExp.$2);
	},
	setCookie: function(label, value, expireTime){
		var cookie = label + "=" + encodeURIComponent(value) +"; domain=xiaozu.xunlei.com; path=/;";
		if (isUndef(expireTime))
			document.cookie = cookie;
		else{
			var expires = new Date();
			expires.setTime(expires.getTime() + expireTime*1000);
			document.cookie = label + "=" + encodeURIComponent(value) +"; domain=xiaozu.xunlei.com; path=/; expires=" + expires.toGMTString() + ";";
		}
	},
	clearCookie: function(label){
		Browser.setCookie(label, "");
	},
	clearXLCookie: function(label){
		var cookie = label + "=; domain=xunlei.com; path=/;";
		document.cookie = cookie;
	}
};
Browser.isFireFox = (window.navigator.appName == "Netscape");
Browser.isOpera = (window.navigator.userAgent.indexOf("opera") != -1);
Browser.isSaf = ((window.navigator.userAgent.indexOf("applewebkit") != -1) || (navigator.vendor == 'Apple Computer, Inc.'));
Browser.isMSIE = ((window.navigator.userAgent.toLowerCase().indexOf("msie") != -1) && (!Browser.isOpera) && (!Browser.isSaf));