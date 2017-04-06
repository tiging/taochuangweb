// SINA天气
function Weather() {
    var city = timestamp = showid = '';
    var SWther = {
        w: [{}],
        add: {}
    }
};
Weather.prototype = {
    getWeather: function(city, timestamp, showid) {
        this.city = city;
        this.timestamp = timestamp;
        this.showid = showid;
        var thisTemp = this;
        // $.getScript("http://php.weather.sina.com.cn/iframe/index/w_cl.php?code=js&day=1&city=" + city + "&dfc=3",
        // function() {
        //     thisTemp.echo()
        // })

        $.ajax({
        	dataType:'script',
        	url: "http://php.weather.sina.com.cn/iframe/index/w_cl.php?code=js&day=1&city=" + city + "&dfc=3",
			scriptCharset:'gbk',
			success:function(){
				thisTemp.echo()
			}
        })
    },
    proc_wimg: function(weather) {
        var style_img = "template/singcere_pt163/src/img/index/s_13.png";
        if (weather.indexOf("多云") !== -1 || weather.indexOf("晴") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_1.png"
        } else if (weather.indexOf("多云") !== -1 && weather.indexOf("阴") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_2.png"
        } else if (weather.indexOf("阴") !== -1 && weather.indexOf("雨") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_3.png"
        } else if (weather.indexOf("晴") !== -1 && weather.indexOf("雨") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_12.png"
        } else if (weather.indexOf("晴") !== -1 && weather.indexOf("雾") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_12.png"
        } else if (weather.indexOf("晴") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_13.png"
        } else if (weather.indexOf("多云") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_2.png"
        } else if (weather.indexOf("阵雨") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_3.png"
        } else if (weather.indexOf("小雨") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_3.png"
        } else if (weather.indexOf("中雨") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_4.png"
        } else if (weather.indexOf("大雨") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_5.png"
        } else if (weather.indexOf("暴雨") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_5.png"
        } else if (weather.indexOf("冰雹") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_6.png"
        } else if (weather.indexOf("雷阵雨") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_7.png"
        } else if (weather.indexOf("小雪") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_8.png"
        } else if (weather.indexOf("中雪") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_9.png"
        } else if (weather.indexOf("大雪") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_10.png"
        } else if (weather.indexOf("暴雪") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_10.png"
        } else if (weather.indexOf("扬沙") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_11.png"
        } else if (weather.indexOf("沙尘") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_11.png"
        } else if (weather.indexOf("雾") !== -1) {
            style_img = "template/singcere_pt163/src/img/index/s_12.png"
        } else {
            style_img = "template/singcere_pt163/src/img/index/s_2.png"
        }
        return '<img src="' + style_img + '" width="48" height="48" alt="' + window.SWther.w[this.city][0].s1 + '">'
    },
    echo: function() {
        var today = new Date();
        today.setTime(this.timestamp * 1000);
        var weekday = new Array(7);
        weekday[0] = "星期日";
        weekday[1] = "星期一";
        weekday[2] = "星期二";
        weekday[3] = "星期三";
        weekday[4] = "星期四";
        weekday[5] = "星期五";
        weekday[6] = "星期六";
        $('#' + this.showid).html('<span class="data">' + (today.getMonth() + 1) + '-' + today.getDate() + ' ' + weekday[today.getDay()] + '</span> ' + ' <span class="temperature" id="T_temperature">' + window.SWther.w[this.city][0].t1 + '&deg;C~' + window.SWther.w[this.city][0].t2 + '&deg;C</span><span class="info" id="T_weather">' + window.SWther.w[this.city][0].s1 + '</span>')
    }
}

