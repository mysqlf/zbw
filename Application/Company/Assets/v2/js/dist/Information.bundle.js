webpackJsonp([6],{0:/*!**********************************!*\
  !*** ../js/entry/Information.js ***!
  \**********************************/
function(t,i,e){"use strict";var s=e(7),a=s.combinePageModule;a({msgList:e(78)})},78:/*!*****************************************!*\
  !*** ../js/page/Information/msgList.js ***!
  \*****************************************/
function(t,i,e){(function(i){"use strict";var s=e(6),a=s.msgDetail,n={init:function(){var t=this;t.msgDetail()},msgDetail:function(){i(".msgBtn").click(function(){var t=i(this),e=t.data(),s=e.id,n=t.closest("tbody"),c=t.parent();n.find(".detail_con").stop().animate({height:"30px"}),"30px"==c.css("height")?(n.find(".detail_box").html("").css({height:"0px"}),a({id:s},function(t){var i=t.result,e='<div class="de_tit">'+i.title+'</div><div class="de_con">'+i.detail+"</div>";c.find(".detail_box").html(e).css({height:"120px"}),c.stop().animate({height:"150px"}),c.parent().removeClass("un_read")})):(c.stop().animate({height:"30px"}),c.find(".detail_box").html("").css({height:"0px"}))})}};t.exports=n}).call(i,e(1))}});
//# sourceMappingURL=Information.bundle.js.map