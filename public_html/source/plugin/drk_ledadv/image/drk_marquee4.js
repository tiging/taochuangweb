/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var drk_colee_right2 = document.getElementById("drk_colee_right2");
var drk_colee_right1 = document.getElementById("drk_colee_right1");
var drk_colee_right0  = document.getElementById("drk_colee_right0");
drk_colee_right0.style.width = document.getElementById("drk_ledtd").clientWidth-10;
drk_colee_right2.innerHTML = drk_colee_right1.innerHTML
function drk_Marquee4(){
if(drk_colee_right0.scrollLeft <= 0)
   drk_colee_right0.scrollLeft += drk_colee_right2.offsetWidth
else{
   drk_colee_right0.scrollLeft--
}
}
var drk_MyMar4 = setInterval(drk_Marquee4,speed)
drk_colee_right0.onmouseover = function() {clearInterval(drk_MyMar4)}
drk_colee_right0.onmouseout  = function() {drk_MyMar4 = setInterval(drk_Marquee4,speed)}
