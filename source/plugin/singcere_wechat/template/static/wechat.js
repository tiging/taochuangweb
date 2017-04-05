jWeixin.previewImageAdapter = function(b) {
	var a = [];
    var _q = b ? b : ".wxpreview";

    var i = document.querySelectorAll(_q);
    for(var z = 0; z < i.length; z++) {
    	a.push(i[z].getAttribute('data-wcp'));
    	i[z].onclick = function(e) {
    		e.preventDefault();
    		if (!this.getAttribute("data-wcp") || !a || a.length == 0) {
                return
            }
            wx.previewImage({
			    current: this.getAttribute("data-wcp"), 
			    urls: a
			});
    	}
    }
};

jWeixin.ua = {};
//jWeixin.parseUA = function() {
    var agent = navigator.userAgent;
    if (jWeixin.ua.iPod = agent.indexOf("iPod") > -1, jWeixin.ua.iPad = agent.indexOf("iPad") > -1, jWeixin.ua.iPhone = agent.indexOf("iPhone") > -1, agent.indexOf("Android") > -1 && (jWeixin.ua.android = parseFloat(agent.slice(agent.indexOf("Android") + 8))), jWeixin.ua.iPad || jWeixin.ua.iPhone || jWeixin.ua.iPod) {
        var m = /OS (\d+)_/.exec(agent);
        jWeixin.ua.iOS = parseInt(m[1], 10) || !0
    }
    jWeixin.ua.wp = agent.indexOf("Windows Phone") > -1;
    var m = /MicroMessenger\/([0-9\.]+)/i.exec(agent);
    if(m) {
        jWeixin.ua.weixin = parseFloat(m[1], 10);
    }
//}

