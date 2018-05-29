/**
 * Created by li on 14-5-4.
 */

var Base = (function(){

    function Base(){

        this.$= $;
        this.root = getRoot();
        this.loadCss = loadCss;
        this.loadJs = loadJs;

        //loadJs(this.root +"/sdk/jquery/jquery-1.10.2.js");
    }
    function getRoot(){
        var rootPath = getRootPath();
        function getRootPath(){
            //获取当前网址，如： http://localhost:8083/uimcardprj/share/meun.jsp
            var curWwwPath=window.document.location.href;
            //获取主机地址之后的目录，如： uimcardprj/share/meun.jsp
            var pathName=window.document.location.pathname;
            var pos=curWwwPath.indexOf(pathName);
            //获取主机地址，如： http://localhost:8083
            var localhostPaht=curWwwPath.substring(0,pos);
            //获取带"/"的项目名，如：/uimcardprj
            var projectName=pathName.substring(0,pathName.substr(1).indexOf('/')+1);
            return(localhostPaht+projectName);
        }

        return rootPath;
    }
    function loadCss(url){
        var path =  '<link rel="stylesheet" type="text/css" href='+url+" />";
        $("head").append(path);
    }

    function loadJs(url){
        var path =  '<script type="text/javascript" src='+url+"></script>";
        $("body").appendTo(path);

    }

    return Base;
})($)
var Base = new Base();

console.log(Base.root);