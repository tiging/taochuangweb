
//实现js文件的缓存
var JC = {
    HEAD : "head" ,
    VERSION : "20150524" ,
    KEYPREFIX : "XN_MOBILE_",
    
    load : function(filePath ,version, params) {
        version = version || JC.VERSION;

        if(filePath.indexOf('.js') != -1) {
            var source = JC.getCacheJSData(filePath ,version);
            if(!source) {
                return;
            }
            if(params) {
                source = source.replace('[%params%]',JSON.stringify(params));
            }

            var element = document.createElement("script");
            element.language = "javascript";
            element.type = "text/javascript";
            element.setAttribute('filename', filePath.slice(filePath.lastIndexOf('/')+1, -3));
            element.defer = true;
            element.text = source;
        } else {
            var element = document.createElement("link");
            element.rel = "stylesheet";
            element.type = "text/css";
            element.href = filePath;
        }

        var heads = document.getElementsByTagName(JC.HEAD);
        if(heads.length) {
            heads[heads.length-1].appendChild(element);
        } else {
            document.documentElement.appendChild(element);
        }
    },
    getCacheJSData : function(filePath ,version) {
        var source = null;
        if(version) { //获得版本号
            source = localStorage.getItem(JC.KEYPREFIX + filePath);
            if(source) {
                if(version == localStorage.getItem(JC.KEYPREFIX + filePath + "version")) {
                //    alert("use cache!");
                    return source; //版本号一致直接返回
                }
                localStorage.removeItem(JC.KEYPREFIX + filePath);
                localStorage.removeItem(JC.KEYPREFIX + filePath + "version");
            }
        }
        //如果没有缓存则网络请求js
        jQuery.ajax({ //同步请求js文件
            async : false,
            cache : false ,
            dataType: "text",
            url: filePath,
            success: function(data) {
                source = data;
            }
        });
         //缓存js
        if(source && version) {
            var i = 2;
            while(i--) {
                try {
                    localStorage.setItem(JC.KEYPREFIX + filePath ,source);
                    localStorage.setItem(JC.KEYPREFIX + filePath + "version" ,version);
                    break;
                } catch(err) {
                    if(err.name == 'QuotaExceededError') {
                        localStorage.clear(); //如果缓存已满则清空
                    }
                }
            }
        }
        return source;
    }
};

//实现tpl文件的缓存
var TC = {
    BODY : "body" ,
    VERSION : "201406191532" ,
    KEYPREFIX : "XN_MOBILE_TMPL_",

    //加载js文件
    load : function(filePath ,version ,params) {

        if (arguments.length == 2 && typeof(arguments[1]) == 'object') {
            params = arguments[1];
            version = null;
            tag = null;
        };

        version = version || JC.VERSION;
        var source = TC.getCacheTSData(filePath ,version);
        if(!source) {
            return;
        }

        if(params) {
            source = source.replace('[%params%]',JSON.stringify(params));
        }

        jQuery(source).appendTo('body');
    } ,
    //请求js文件
    getCacheTSData : function(filePath ,version) {
        var source = null;
        if(version) { //获得版本号
            source = localStorage.getItem(TC.KEYPREFIX + filePath);
            if(source) {
                if(version == localStorage.getItem(TC.KEYPREFIX + filePath + "version2")) {
                    return source; //版本号一致直接返回
                }
                localStorage.removeItem(TC.KEYPREFIX + filePath);
                localStorage.removeItem(TC.KEYPREFIX + filePath + "version2");
            }
        }
        //如果没有缓存则网络请求js
        jQuery.ajax({ //同步请求js文件
            async : false,
            cache : false ,
            dataType: "text",
            url: filePath,
            success: function(data) {
                source = data;
            }
        });
         //缓存js
        if(source && version) {
            var i = 2;
            while(i--) {
                try {
                    localStorage.setItem(TC.KEYPREFIX + filePath ,source);
                    localStorage.setItem(TC.KEYPREFIX + filePath + "version2" ,version);
                    break;
                } catch(err) {
                    if(err.name == 'QuotaExceededError') {
                        localStorage.clear(); //如果缓存已满则清空
                    }
                }
            }
        }
        return source;
    }
};

