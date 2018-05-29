/*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
(function(b){if(typeof define==="function"&&define.amd){define(["jquery"],b);
}else{if(typeof exports==="object"){b(require("jquery"));}else{b(jQuery);}}}(function(m){var j=/\+/g;function o(a){return i.raw?a:encodeURIComponent(a);
}function l(a){return i.raw?a:decodeURIComponent(a);}function k(a){return o(i.json?JSON.stringify(a):String(a));}function p(b){if(b.indexOf('"')===0){b=b.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\");
}try{b=decodeURIComponent(b.replace(j," "));return i.json?JSON.parse(b):b;}catch(a){}}function n(b,c){var a=i.raw?b:p(b);return m.isFunction(c)?c(a):a;
}var i=m.cookie=function(b,c,x){if(c!==undefined&&!m.isFunction(c)){x=m.extend({},i.defaults,x);if(typeof x.expires==="number"){var a=x.expires,y=x.expires=new Date();
y.setTime(+y+a*86400000);}return(document.cookie=[o(b),"=",k(c),x.expires?"; expires="+x.expires.toUTCString():"",x.path?"; path="+x.path:"",x.domain?"; domain="+x.domain:"",x.secure?"; secure":""].join(""));
}var t=b?undefined:{};var z=document.cookie?document.cookie.split("; "):[];for(var d=0,f=z.length;d<f;d++){var e=z[d].split("=");var h=l(e.shift());var g=e.join("=");
if(b&&b===h){t=n(g,c);break;}if(!b&&(g=n(g))!==undefined){t[h]=g;}}return t;};i.defaults={};m.removeCookie=function(a,b){if(m.cookie(a)===undefined){return false;
}m.cookie(a,"",m.extend({},b,{expires:-1}));return !m.cookie(a);};}));