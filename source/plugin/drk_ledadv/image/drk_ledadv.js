/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function drk_announcement() {
    var ann = new Object();
    ann.anndelay = 3000;
    ann.annst = 0;ann.annstop = 0;
    ann.annrowcount = 0;
    ann.anncount = 0;
    ann.annlis = $('drkleda').getElementsByTagName('li');
    ann.annrows = new Array();
    ann.announcementScroll = function(){
      if(this.annstop){this.annst = setTimeout(function(){ann.announcementScroll();}, this.anndelay);return;}
      if(!this.annst){
          var lasttop = -1;
          for(i = 0;i < this.annlis.length;i++){
              if(lasttop != this.annlis[i].offsetTop){
                  if(lasttop == -1) lasttop = 0;
                  this.annrows[this.annrowcount] = this.annlis[i].offsetTop - lasttop;this.annrowcount++;
              }
              lasttop = this.annlis[i].offsetTop;
          }
          if(this.annrows.length == 1){
              $('drkled').onmouseover = $('drkled').onmouseout = null;
          } else {
              this.annrows[this.annrowcount] = this.annrows[1];
              $('drkledb').innerHTML += $('drkledb').innerHTML;
              this.annst = setTimeout(function(){ann.announcementScroll();}, this.anndelay);
              $('drkled').onmouseover = function(){ann.annstop = 1;};
              $('drkled').onmouseout = function(){ann.annstop = 0;};
          }
          this.annrowcount = 1;
          return;
      }
      if(this.annrowcount >= this.annrows.length){
          $('drkleda').scrollTop = 0;
          this.annrowcount = 1;
          this.annst = setTimeout(function (){ann.announcementScroll();}, this.anndelay);
      } else {
          this.anncount = 0;
          this.announcementScrollnext(this.annrows[this.annrowcount]);
      }
    };
    ann.announcementScrollnext = function (time) {
        $('drkleda').scrollTop++;
        this.anncount++;
        if(this.anncount != time) {
            this.annst = setTimeout(function () {ann.announcementScrollnext(time);}, 10);
        } else {
            this.annrowcount++;
            this.annst = setTimeout(function () {ann.announcementScroll();}, this.anndelay);
        }
    };
    ann.announcementScroll();
}

var speed = 30
var colee2 = document.getElementById("colee2");
var colee1 = document.getElementById("colee1");
var colee0 = document.getElementById("colee0");
colee2.innerHTML = colee1.innerHTML
colee0.scrollTop = colee0.scrollHeight
function udMarquee(){
    if(colee1.offsetTop - colee0.scrollTop>=0)
        colee0.scrollTop += colee2.offsetHeight
    else
        colee0.scrollTop--
}
var udMyMar = setInterval(udMarquee,speed)
colee0.onmouseover = function() {clearInterval(udMyMar)}
colee0.onmouseout  = function() {udMyMar = setInterval(udMarquee,speed)}


function lrMarquee(){
    if(colee0.scrollLeft<=0)
        colee0.scrollLeft += colee2.offsetWidth
    else
        colee0.scrollLeft--
}
var lrMyMar = setInterval(lrMarquee,speed)
colee0.onmouseover = function() {clearInterval(lrMyMar)}
colee0.onmouseout  = function() {lrMyMar = setInterval(lrMarquee,speed)}