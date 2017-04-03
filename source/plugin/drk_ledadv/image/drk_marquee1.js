/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var drk_colee2 = document.getElementById("drk_colee2");
var drk_colee1 = document.getElementById("drk_colee1");
var drk_colee0 = document.getElementById("drk_colee0");
drk_colee2.innerHTML = drk_colee1.innerHTML;
function drk_Marquee1(){
   if(drk_colee2.offsetTop - drk_colee0.scrollTop <= 0){
      drk_colee0.scrollTop -= drk_colee1.offsetHeight;
   }else{
      drk_colee0.scrollTop++
   }
}
var drk_MyMar1 = setInterval(drk_Marquee1,speed)
drk_colee0.onmouseover = function() {clearInterval(drk_MyMar1)}
drk_colee0.onmouseout  = function(){drk_MyMar1 = setInterval(drk_Marquee1,speed)}

