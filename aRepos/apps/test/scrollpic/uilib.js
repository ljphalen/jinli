// Swipe Contents
var theContentWidth = new Array();
var theContentWidth_prev = new Array();
var theContentCnt = new Array();
var theContentNum = new Array();
var theContentNum_prev = new Array();
var theSwipeHeight = new Array();

var theSwipeSpeed=300;

var pageOnMark = "o";
var pageOffMark = "x";
var pageSepMark = " ";

var firstLoad = "Y";
var tgObjYN = "N";

var $tgObj;

var swipeOnOff = 1; // 1:on,  0:off
var pagebarOnOff = 1; // 1:on,  0:off

var jsDate_s,jsDate_e;

var arrLowerAgent = new Array("BlackBerry9700","Opera Mini"); //Lower Device Data
//var arrLowerAgent = new Array("BlackBerry9700","IEMobile","Opera Mini","MSIE 7.0"); //Lower Device Data
var theDeviceCheck = 0;
var theWrapWidth = $("#wrap").width();

$(document).ready(function(){
  if($("body").find(".theSwiper").size()){
    theDeviceCheck = rtnDeviceCheck();
    if(theDeviceCheck){
      $(".theSwiper .theList").each(function(i,o){
        ($(o).find(".theContent")).each(function(j,p){
          if (j>0){
            $(p).remove();
          }
        });
      });
    }
    startSwipe();
  }
  if ($("#history")){
    $("#history").hide();  //history Hide
  }
});

function startSwipe(){
  if($("body").find(".theSwiper").size()){
    if (firstLoad == "Y"){
      var oldonresize = window.onresize;
      if(typeof(window.onresize) != "function"){
        window.onorientationchange = window.onresize = window.onorientation = function(){
          if (theWrapWidth != $("#wrap").width()){
            theWrapWidth = $("#wrap").width();
            resize();
          }
        }
      }else{
        window.onorientationchange = window.onresize = window.onorientation = function(){
          oldonresize();
          if (theWrapWidth != $("#wrap").width()){
            theWrapWidth = $("#wrap").width();
            resize();
          }
        }
      }

      theWrapWidth = $("#wrap").width();
      var oldonload = window.onload;
      if (typeof(window.onload) != "function"){
        window.onload = function(){
          resize();
        }
      }else{
        window.onload = function(){
          oldonload();
          resize();
        }
      }

      threshold = Math.round(theWrapWidth/4); //Drag Swiper Div Width 1/4

      //Histroy
      if ($("#welcome .icoBtn")){
        $("#welcome .icoBtn").click(function(){
          $(this).attr("href","#none");
          $("#history").show();
          resize();
          $("#welcome").hide();
        });
      }

    }else{
      if(!theDeviceCheck){
        for(t1=0;t1<theContentWidth.length;t1++){
          theContentWidth_prev[t1] = new Array();
          for(t2=0;t2<theContentWidth[t1].length;t2++){
            theContentWidth_prev[t1][t2] = theContentWidth[t1][t2];
          }
        }

        for(t1=0;t1<theContentNum.length;t1++){
          theContentNum_prev[t1] = theContentNum[t1];
        }
      }else{
        $(".theSwiper .theList").each(function(i,o){
          ($(o).find(".theContent")).each(function(j,p){
            if (j>0){
              $(p).remove();
            }
          });
        });
      }
    }

    if(!theDeviceCheck){
      $(".theSwiper .theList .theContent").css("position","absolute");
      $(".theSwiper .theList .theContent").height("auto");
      $(".theSwiper .theList").each(function(i,o){
        if (firstLoad == "Y"){
          theContentNum[i] = 0;
        }else{
          if(typeof($(o).attr("pid"))=="undefined"){
            theContentNum[i] = 0;
          }else{
            theContentNum[i] = theContentNum_prev[$(o).attr("pid")];
          }
        }
		
        theSwipeHeight[i] = $(o).find("div.theContent:first-child").height();
        if (pagebarOnOff){
          if ($(o).parent().find(".pagebar").size()){
            $(o).parent().find(".pagebar").attr("id","pagebar"+i);
            $(o).parent().find(".pagebar").attr("pid",i);
          }
          if (!$(o).parent().find(".pagebar").size() && $(o).find(".theContent").size() > 1){
            $(o).parent().append("<div class=\"pagebar\" id=\"pagebar"+i+"\" pid=\""+i+"\"></div>");
          }
        }
        
        theContentCnt[i] = $(o).find(".theContent").size();

        theContentWidth[i] = new Array();
        if (theContentCnt[i]>1){
          ($(o).find(".theContent")).each(function(j,p){
            if (typeof($(o).attr("pid"))=="undefined"){
              if (j == (theContentCnt[i]-1)){
                $(p).css("left","-100%");
                 theContentWidth[i][j] = -100;
              }else{
                $(p).css("left",(j*100)+"%");
                theContentWidth[i][j] = j* 100;
              }

              //firefox Mobile
              $(p).find("div").attr("pid",i);
              $(p).find("p").attr("pid",i);
              $(p).find("img").attr("pid",i);
              $(p).find("a").attr("pid",i);
              $(p).find("span").attr("pid",i);
              $(p).find("strong").attr("pid",i);
              $(p).find("ul").attr("pid",i);
              $(p).find("li").attr("pid",i);
              $(p).attr("pid",i);


            }else{
              theContentWidth[i][j] = theContentWidth_prev[$(o).attr("pid")][j];
            }
            if ($(p).height() > theSwipeHeight[i]){
              theSwipeHeight[i] = $(p).height();
            }
          });
        }else{
          $(o).find(".theContent").css("left","0%");
        }

        pagebarLoad(i);

        if (swipeOnOff && typeof($(o).attr("pid"))=="undefined" && $(o).find(".theContent").size()>1){
          $(o).swipe( {data:$(o), triggerOnTouchEnd : true,	swipeStatus : swipeStatus, allowPageScroll:"vertical", threshold:threshold} );
        }
        $(o).parent().attr("pid",i);
        $(o).attr("pid",i);
        $(o).attr("id","theList"+i);

      });

    }
    resize();
  }
  if (firstLoad == "Y"){
    firstLoad = "N";
  }
}
function resize(){
  if($("body").find(".theSwiper").size()){
    if (theWrapWidth > 460){
      $(".categorySumm").css("min-height","110px");
    }else{
      $(".categorySumm").css("min-height","");
    }
    $(".theSwiper .theList .theContent").height("auto");
  }
  if(!theDeviceCheck){
    setTimeout(function (){
      if($("body").find(".theSwiper").size()){
        $(".theSwiper").each(function(i,o){
          theSwipeHeight[i] = 0;
          ($(o).find(".theContent")).each(function(j,p){
            if ($(p).height()>theSwipeHeight[i]){
              theSwipeHeight[i] = $(p).height();
            }
          });
        });
      }
      resizeHeightSwipe();
    },1);
  }
}

function pagebarLoad(tgNum){
  if (theContentCnt[tgNum] > 1){
    tempPageValue = "<button class=\"prev\" onClick=\"previousDiv($('#theList"+tgNum+"'),1);\">&lt;</button> ";
    for(var l=0;l<theContentCnt[tgNum];l++){
        if (l>0){
          tempPageValue = tempPageValue+pageSepMark;
        }
        if(theContentNum[tgNum]==l){
          tempPageValue = tempPageValue+"<a class=\"current\">"+pageOnMark+"</a>";
        }else{
          if (theContentNum[tgNum] < l){
            tempPageValue = tempPageValue+"<a class=\"goPage\" onClick=\"nextDiv($('#theList"+tgNum+"'),"+(l-theContentNum[tgNum])+");\" >"+pageOffMark+"</a>";
          }else{
            tempPageValue = tempPageValue+"<a class=\"goPage\" onClick=\"previousDiv($('#theList"+tgNum+"'),"+(theContentNum[tgNum]-l)+");\">"+pageOffMark+"</a>";
          }
        }
    }
    tempPageValue = tempPageValue+" <button class=\"next\" onClick=\"nextDiv($('#theList"+tgNum+"'),1);\">&gt;</button>";
    $("#pagebar"+tgNum).html(tempPageValue);
  }
}

function resizeHeightSwipe(){
  $(".theSwiper").each(function(i,o){
    if (pagebarOnOff){
      $(o).height(theSwipeHeight[i]);
    }else{
      $(o).height(theSwipeHeight[i]);
    }
    if (theSwipeHeight[i]==0){
      $(o).css("min-height","0px");
    }else{
      $(o).css("min-height","90px");
    }
    $(o).find(".theList").height(theSwipeHeight[i]);
    $(o).find(".theList .theContent").height(theSwipeHeight[i]);
  });
}


/**
* Catch each phase of the swipe.
* move : we drag the div.
* cancel : we animate back to where we were
* end : we animate to the next image
*/

function swipeStatus(event, phase, direction, distance){

  ////$("#debugView3").html("tgObjYN : " + tgObjYN);
  //$("#debugView1").html(this+ "|" + event + " | " + phase + " | " + direction + " | " + distance);
  if (phase=="start" && tgObjYN =="N"){
    ListNum = $(this).attr("pid");
    if (!ListNum){
      ListNum = $(this).parent().attr("pid");
    }
    $tgObj = $("#theList"+ListNum);
    tgObjYN = "Y"
  }

  if (phase=="start"){
    var jsDate = new Date();
    jsDate_s = jsDate.getTime();
    preDirection = "";
  }
  //If we are moving before swipe, and we are going Lor R in X mode, or U or D in Y mode then drag.
  if( phase=="move" && (direction=="left" || direction=="right") ) {
    var duration=0;
    var movePercent = Math.round(distance/theWrapWidth*100);
    if (movePercent > 100){
      movePercent = 100;
    }
    if (direction == "left"){
      //$("#debugView5").html("direction : "+direction +" | "+distance);
      $tgObj.find(".theContent").each(function(i,o){
        if ($tgObj.find(".theContent").size()==2){
          twidth = Math.abs(theContentWidth[$tgObj.attr("pid")][i]) - movePercent;
        }else{
          twidth = theContentWidth[$tgObj.attr("pid")][i] - movePercent;
        }
        $(o).css("left",twidth+"%");
      });
      preDirection = "left";
    }else if (direction == "right"){
      var twidth_temp = new Array();
      $tgObj.find(".theContent").each(function(i,o){
        twidth = theContentWidth[$tgObj.attr("pid")][i] + movePercent;
        $(o).css("left",twidth+"%");
        twidth_temp[i] = twidth;
      });
      preDirection = "right";

    }
  }else if ( phase == "cancel"){
    ListNum = $tgObj.attr("pid");
    $tgObj.find(".theContent").each(function(i,o){
      //twidth = Math.round(parseInt($(o).css("left"))/100)*100;
      twidth = theContentWidth[ListNum][i];
      $(o).animate({"left" : twidth+"%"}, theSwipeSpeed);
    });
    tgObjYN = "N";
    var jsDate = new Date();
    jsDate_e = jsDate.getTime();
    if (jsDate_e-jsDate_s < 500 && distance<5){
      var aTagHREF = "";
      if($(event.srcElement.parentElement).get(0).tagName == "A"){
        if ($(event.srcElement.parentElement).attr("href")){
          aTagHREF = $(event.srcElement.parentElement).attr("href");
        }
      }else if($(event.srcElement.parentElement).parent().get(0).tagName == "A"){
        if ($(event.srcElement.parentElement).parent().attr("href")){
          aTagHREF = $(event.srcElement.parentElement).parent().attr("href");
        }
      }else if($(event.srcElement.parentElement).parent().parent().get(0).tagName == "A"){
        if ($(event.srcElement.parentElement).parent().parent().attr("href")){
          aTagHREF = $(event.srcElement.parentElement).parent().parent().attr("href");
        }
      }
      if (aTagHREF!="" && aTagHREF!="#none" && aTagHREF!="#"){
        location.href = aTagHREF;
      }
    }
  }else if ( phase =="end" )  {
    if (direction == "right"){
      previousDiv($tgObj, 1);
    }else if (direction == "left"){
      nextDiv($tgObj, 1);
    }else{
      if (preDirection == "right"){
        previousDiv($tgObj, 1);
      }else if (preDirection == "left"){
        nextDiv($tgObj, 1);
      }
    }
  }
}

function previousDiv(tgobj, moveCnt){
  ListNum = tgobj.attr("pid");
  if (theContentNum[ListNum] == 0){
    theContentNum[ListNum] = theContentCnt[ListNum]-moveCnt;
  }else{
    theContentNum[ListNum] = theContentNum[ListNum]-moveCnt;
  }
  tgobj.find(".theContent").each(function(i,o){
    theContentWidth[ListNum][i] = theContentWidth[ListNum][i]+(100*moveCnt);
    if (theContentWidth[ListNum][i] > ((theContentCnt[ListNum]-2)*100)){
        theContentWidth[ListNum][i] = theContentWidth[ListNum][i]-(theContentCnt[ListNum]*100);
    }
    if (theContentWidth[ListNum][i] == 100){
      $(o).animate({"left" : theContentWidth[ListNum][i]+"%"}, theSwipeSpeed);
    }else if (theContentWidth[ListNum][i]== 0){
      if (parseInt($(o).css("left").replace("%",""))>0){
        $(o).css("left","-100%").animate({"left" : theContentWidth[ListNum][i]+"%"}, theSwipeSpeed);
      }else{
        $(o).animate({"left" : theContentWidth[ListNum][i]+"%"}, theSwipeSpeed);
      }
    }else{
      $(o).css("left",theContentWidth[ListNum][i]+"%").animate({"left" : theContentWidth[ListNum][i]+"%"}, theSwipeSpeed);
    }
  });
   pagebarLoad(ListNum);
   tgObjYN = "N";
}

function nextDiv(tgobj, moveCnt){
  ListNum = tgobj.attr("pid");
  if (theContentNum[ListNum] == (theContentCnt[ListNum]-1)){
    theContentNum[ListNum] = 0;
  }else{
    theContentNum[ListNum] = theContentNum[ListNum] + moveCnt;
  }
  tgobj.find(".theContent").each(function(i,o){
    theContentWidth[ListNum][i] = theContentWidth[ListNum][i]-(100*moveCnt);
    if (theContentWidth[ListNum][i] < -100 ){
      theContentWidth[ListNum][i] = theContentWidth[ListNum][i]+(theContentCnt[ListNum]*100);
    }
    if (theContentWidth[ListNum][i]== -100){
      $(o).animate({"left" : theContentWidth[ListNum][i]+"%"}, theSwipeSpeed);
    }else if (theContentWidth[ListNum][i] == 0){
      if (parseInt($(o).css("left").replace("%",""))<0){
        $(o).css("left","100%").animate({"left" : theContentWidth[ListNum][i]+"%"}, theSwipeSpeed);
      }else{
        $(o).animate({"left" : theContentWidth[ListNum][i]+"%"}, theSwipeSpeed);
      }
    }else{
      //$(o).css("left",theContentWidth[ListNum][i]+"%");
      $(o).css("left",theContentWidth[ListNum][i]+"%").animate({"left" : theContentWidth[ListNum][i]+"%"}, theSwipeSpeed);
    }
  });
   pagebarLoad(ListNum);
   tgObjYN = "N";
}

function rtnDeviceCheck(){
  var rtnValue = 0; // Default : 0 (normal ver)
  var tempDeviceAgent = window.navigator.userAgent;

  for (var ac=0;ac<arrLowerAgent.length ;ac++ ){
    if(tempDeviceAgent.search(arrLowerAgent[ac])>-1){
      rtnValue = 1;
    }
  }
  return rtnValue;
}
