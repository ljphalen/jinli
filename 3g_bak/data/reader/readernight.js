/**
 @fileOverview
 Night mode apply script
 @author Wunaichao <wunaichao@maxthon.net>
 */




(function(window){
 
 var NIGHT_ID = "_maxthon3_night_mode_script";
 
 /**
  构造夜间模式的样式
  @returns {string} The night-mode css string.
  */
 function buildNightStyle(config) {
 
 var bgColor = config ? config.crbkgrd : "#272729" ;
 var color = config ? config.crtext : "#777777";
 var linkColor = config ? config.crlinktxt : "#8e8e8e";
 var linkVisitedColor = config ? config.crvisitedlnk : "#8e8e8e";
 
 var element = null;
 var elementStyle = null;
 var fontStyle = null;
 var linkElement = [];
 var linkStyle = null;
 var linkVisitedElement = [];
 var linkVisitedStyle = null;
 
 element = "header,nav,table,th,tr,td,dl,ul,li,ol,fieldset,form,"+
 "h1,h2,h3,h4,h5,h6,pre";
 
 elementStyle = "body {" +
 "background: "+ bgColor + " !important;" +
 "}";
 elementStyle += "div {" +
 "background-color: "+ bgColor + " !important;" +
 "}";
 elementStyle += (element + " {" +
                  "background: transparent !important;" +
                  "}");
 elementStyle += "section {background: -webkit-gradient(linear, left top, left bottom, from(#0F1001), to(#0F1001), color-stop(0.2, #121314), color-stop(0.8, #121314));background-size: 100% 15px;background-repeat: no-repeat;}";
 
 
 fontStyle = "* {color: "+ color +" !important;}";
 
 linkElement.push("a:link");
 linkElement.push("a:link *");
 linkElement.push("a:link:hover");
 linkElement.push("a:link:hover *");
 linkElement.push("a:link:active");
 linkElement.push("a:link:active *");
 linkStyle = linkElement.join(",")+" {"+
 "color: "+ linkColor +" !important;"+
 "}";
 
 linkVisitedElement.push("a:visited");
 linkVisitedElement.push("a:visited *");
 linkVisitedElement.push("a:visited:hover");
 linkVisitedElement.push("a:visited:hover *");
 linkVisitedElement.push("a:visited:active");
 linkVisitedElement.push("a:visited:active *");
 linkVisitedStyle = linkVisitedElement.join(",")+" {"+
 "color: "+ linkVisitedColor +" !important;"+
 "}";
 return elementStyle + fontStyle + linkStyle + linkVisitedStyle;
 // return cssTest;
 }
 
 /**
  向页面中插入夜间模式样式表结点
  @param {string} styleStr
  */
 function insertStyle(styleStr){
 // 处理IE兼容模式
 if (window.ActiveXObject) {
 var styleSheet = document.createStyleSheet();
 styleSheet[NIGHT_ID] = NIGHT_ID;
 styleSheet.cssText = styleStr;
 }
 else {
 var styleNode = document.createElement("style");
 styleNode.id = NIGHT_ID;
 styleNode.appendChild(document.createTextNode(styleStr));
 document.documentElement.appendChild(styleNode);
 }
 }
 
 /**
  应用夜间模式
  */
 function applyNightMode() {
 var styleStr = buildNightStyle(null);
 // 如果存在夜间模式脚本,先删除原来样式再应用
 removeNightMode();
 insertStyle(styleStr);
 }
 
 /**
  去除夜间模式
  */
 function removeNightMode() {
 var styleElement = null;
 // 处理IE兼容模式
 if (window.ActiveXObject) {
 for (var i = 0,l = document.styleSheets.length; i < l; i++ )
 {
 var styleSheet = document.styleSheets[i];
 if (styleSheet[NIGHT_ID] == NIGHT_ID) {
 styleElement = styleSheet.owningElement;
 }
 }
 }
 else {
 styleElement = document.getElementById(NIGHT_ID);
 }
 
 if (styleElement) {
 styleElement.parentNode.removeChild(styleElement);
 }
 }
 
 window.mxNightModeTool = {
 removeNightMode: removeNightMode,
 applyNightMode: applyNightMode
 };
 
 })(window);