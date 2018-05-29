/*!
 * Copyright 2011, ICAT seed
 * MIT Licensed
 * @Author jndream221@gmail.com
 * @Time 2012-05-17 08:30:00
 */
(function(b,g){if(g[b]===undefined){g[b]={}}b=g[b];b.loadContainer={};var c=window.location.href,f=document,h=f.head||f.getElementsByTagName("head")[0]||f.documentElement,e=(function(){var r,m=/\w*(\.source)?\.(js|css)/g,n=f.getElementsByTagName("script"),i,q=function(w,j){var x=h.childNodes,v;for(var u=0,t=x.length;u<t;u++){var s=x[u];if(s.nodeType===1&&s.nodeName.toLowerCase()===j&&/sys\/core(\.source)?\.css/i.test(s.href)){v=s}}return v};for(var p=0,l;l=n[p++];){i=!!f.querySelector?l.src:l.getAttribute("src",4);if(i&&/seed(\.source)?\.js/i.test(i)){b.refFile=l;b.appRef=i.substring(0,i.lastIndexOf("/assets"));var k=q(l,"link"),o=k.href;if(k&&o){r=b.sysRef=o.substr(0,o.lastIndexOf("/core"))}break}}return r})();var a=b.refFile.getAttribute("corelib"),d=e+"/icat/1.1.0/icat"+(/debug/i.test(c)?".source":"")+".js";if(a!=undefined){a=a==""?e+"/jquery.js":a;a=/\w+:\/\/.*/i.test(a)?a:e+a;f.write('<script src="'+a+'"><\/script>');b.loadContainer[a]=true}f.write('<script src="'+d+'"><\/script>');b.loadContainer[d]=true})("ICAT",this);
