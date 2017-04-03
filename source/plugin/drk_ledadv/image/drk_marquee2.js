/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var drk_colee_bottom2 = document.getElementById("drk_colee_bottom2");
var drk_colee_bottom1 = document.getElementById("drk_colee_bottom1");
var drk_colee_bottom0 = document.getElementById("drk_colee_bottom0");
drk_colee_bottom2.innerHTML = drk_colee_bottom1.innerHTML
drk_colee_bottom0.scrollTop = drk_colee_bottom0.scrollHeight
function drk_Marquee2(){
   if(drk_colee_bottom1.offsetTop - drk_colee_bottom0.scrollTop>=0)
      drk_colee_bottom0.scrollTop += drk_colee_bottom2.offsetHeight
   else{
      drk_colee_bottom0.scrollTop--
   }
}
var drk_MyMar2 = setInterval(drk_Marquee2,speed)
drk_colee_bottom0.onmouseover = function() {clearInterval(drk_MyMar2)}
drk_colee_bottom0.onmouseout  = function() {drk_MyMar2 = setInterval(drk_Marquee2,speed)}

