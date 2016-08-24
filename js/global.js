
/*
* 一些js常用的公共函数
* 
*/


/**
 * 去掉字符串首尾空格
 * @author guozhenyi
 */
String.prototype.trim = function () {
    return this.replace(/(^\s*)|(\s*$)/g, "");
}


/**
 * 数组中是否有值存在
 * @author guozhenyi
 * 不能用Array.prototype.has 这样的原型链来扩展
 * 不然任何数组会在最新的浏览器中被for-in循环出这个函数
 *
 */
function in_array (val, arr) {
    for (var i = 0; i < arr.length; i++) {
        if (arr[i] == val) return true;
    }
    return false;
}

Array.prototype.has = function(val) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == val) return true;
    }
    return false;
}


/*
 * 复制一份对象
 *
 */
Object.prototype.clone = function(){
    var objClone;
    if ( this.constructor == Object ) objClone = new this.constructor(); 
    else objClone = new this.constructor(this.valueOf()); 
    for ( var key in this ) {
        if ( objClone[key] != this[key] ) {
            if ( typeof(this[key]) == 'object' ) {
                objClone[key] = this[key].clone();
            } else {
                objClone[key] = this[key];
            }
        }
    }
    objClone.toString = this.toString;
    objClone.valueOf = this.valueOf;
    return objClone;
}


/*
 * 获得url参数值
 * @datetime 2016-08-24 17:08
 */
function GetQuery(key) {
    var url = window.document.location.href.toString();
    var urls = url.split("?");
    if (typeof urls[1] == "string" && urls[1].length > 0){
        var qArr = urls[1].split("&");
        var get = {};
        for (var i=0; i < qArr.length; i++) {
            var kv = qArr[i].split('=');
            get[kv[0]] = kv[1];
        }
        if (get[key]) {
            return get[key];
        } else {
            return undefined;
        }
        // return get;
    } else {
        return undefined;
    }
}

/*
 * 模拟PHP的$_GET超全局变量
 */
var $_GET = (function(){
    var url = window.document.location.href.toString();
    var urls = url.split("?");
    if (typeof urls[1] == "string" && urls[1].length > 0){
        var qArr = urls[1].split("&");
        var get = {};
        for (var i=0; i < qArr.length; i++) {
            var kv = qArr[i].split('=');
            get[kv[0]] = kv[1];
        }
        return get;
    } else {
        return {};
    }
})();


/**
 * 格式化日期
 * @author guozhenyi
 * @date 2016-03-23 17:16
 */
Date.prototype.format = function (fmt) {
    var map,i;
    if (fmt == undefined) {
        return '';
    }
    map = {
        'Y' : this.getFullYear(),
        'y' : this.getFullYear().toString().substr(2),
        'M' : (this.getMonth()+1) < 10 ? '0'+(this.getMonth()+1) : (this.getMonth()+1),
        'm' : this.getMonth()+1,
        'D' : this.getDate() < 10 ? '0'+this.getDate() : this.getDate(),
        'd' : this.getDate(),
        'H' : this.getHours() < 10 ? '0'+this.getHours() : this.getHours(),
        'h' : this.getHours(),
        'I' : this.getMinutes() < 10 ? '0'+this.getMinutes() : this.getMinutes(),
        'i' : this.getMinutes(),
        'S' : this.getSeconds() < 10 ? '0'+this.getSeconds() : this.getSeconds(),
        's' : this.getSeconds(),
        'u' : this.getMilliseconds(),
        'q' : Math.floor((this.getMonth()+3)/3)
    };
    for (i in map) {
        if (fmt.indexOf(i) != -1) {
            fmt = fmt.replace(i, map[i]);
        }
    }
    return fmt;
}


/*
 *  为IE8增加nextElementSibling 属性
 */
if(!("nextElementSibling" in document.documentElement)){
    Object.defineProperty(Element.prototype, "nextElementSibling", {
        get: function(){
            var e = this.nextSibling;
            while(e && 1 !== e.nodeType)
                e = e.nextSibling;
            return e;
        }
    });
}


/*
 * 解决websocket在onopen里执行send失败的问题
 * 一般会报错：
 * Uncaught InvalidStateError: Failed to execute 'send' on 'WebSocket': Still in CONNECTING state.
 *
 * @datetime 2016-08-24 17:12
 */
WebSocket.prototype.waitForConnection = function (callback, interval) {
    if (this.readyState === 1) {
        callback();
    } else {
        var that = this;
        setTimeout(function () {
            that.waitForConnection(callback, interval);
        }, interval);
    }
};
WebSocket.prototype.safeSend = function (message, callback) {
    var that = this;
    that.waitForConnection(function () {
        that.send(message);
        if (typeof callback !== 'undefined') {
          callback();
        }
    }, 1000);
};




/*
 * websocket 连接实例
 *
 * @datetime 2016-08-24 18:12
 */

var ws;
var payload = {
    'type':'login',
    'uid': '1',
    'rid': '1'
}
try {
    ws = new WebSocket('ws://127.0.0.1:6666');
} catch (e) {
    console.log(e);
    // throw exception('websocket connect error');
}
ws.onopen = function () {
    console.log("Connected to WebSocket server.\n");
    ws.safeSend(JSON.stringify(payload));
};
ws.onclose = function (eve) {
    ws.close();
    console.warn('websocket连接被关闭');
};
ws.onerror = function (eve) {
    console.error('websocket连接错误');
    console.log(eve);
};
ws.onmessage = function (eve) {
    console.log(eve.data, '\n\n');

    var response = {};
    try {
        response = JSON.parse(eve.data);
    } catch (e) {
        console.log(e);
        return;
    }
}




