(function(){var a=window.parent;dialog=a.$EDITORUI[window.frameElement.id.replace(/_iframe$/,"")];editor=dialog.editor;UE=a.UE;domUtils=UE.dom.domUtils;
utils=UE.utils;browser=UE.browser;ajax=UE.ajax;$G=function(b){return document.getElementById(b);};$focus=function(b){setTimeout(function(){if(browser.ie){var c=b.createTextRange();
c.collapse(false);c.select();}else{b.focus();}},0);};utils.loadFile(document,{href:editor.options.themePath+editor.options.theme+"/dialogbase.css?cache="+Math.random(),tag:"link",type:"text/css",rel:"stylesheet"});
lang=editor.getLang(dialog.className.split("-")[2]);if(lang){domUtils.on(window,"load",function(){var c=editor.options.langPath+editor.options.lang+"/images/";
for(var h in lang["static"]){var f=$G(h);if(!f){continue;}var e=f.tagName,k=lang["static"][h];if(k.src){k=utils.extend({},k,false);k.src=c+k.src;}if(k.style){k=utils.extend({},k,false);
k.style=k.style.replace(/url\s*\(/g,"url("+c);}switch(e.toLowerCase()){case"var":f.parentNode.replaceChild(document.createTextNode(k),f);break;case"select":var d=f.options;
for(var g=0,l;l=d[g];){l.innerHTML=k.options[g++];}for(var b in k){b!="options"&&f.setAttribute(b,k[b]);}break;default:domUtils.setAttributes(f,k);}}});
}})();