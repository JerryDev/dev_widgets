
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
            get[kv[0]] = decodeURI(kv[1]);
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
            get[kv[0]] = decodeURI(kv[1]);
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




/**
* UTF16和UTF8转换对照表
* U+00000000 – U+0000007F   0xxxxxxx
* U+00000080 – U+000007FF   110xxxxx 10xxxxxx
* U+00000800 – U+0000FFFF   1110xxxx 10xxxxxx 10xxxxxx
* U+00010000 – U+001FFFFF   11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
* U+00200000 – U+03FFFFFF   111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
* U+04000000 – U+7FFFFFFF   1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
*/
var Base64 = {
    // 转码表
    table : [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
            'I', 'J', 'K', 'L', 'M', 'N', 'O' ,'P',
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
            'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
            'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
            'w', 'x', 'y', 'z', '0', '1', '2', '3',
            '4', '5', '6', '7', '8', '9', '+', '/'
    ],
    UTF16ToUTF8 : function(str) {
        var res = [], len = str.length;
        for (var i = 0; i < len; i++) {
            var code = str.charCodeAt(i);
            if (code > 0x0000 && code <= 0x007F) {
                // 单字节，这里并不考虑0x0000，因为它是空字节
                // U+00000000 – U+0000007F  0xxxxxxx
                res.push(str.charAt(i));
            } else if (code >= 0x0080 && code <= 0x07FF) {
                // 双字节
                // U+00000080 – U+000007FF  110xxxxx 10xxxxxx
                // 110xxxxx
                var byte1 = 0xC0 | ((code >> 6) & 0x1F);
                // 10xxxxxx
                var byte2 = 0x80 | (code & 0x3F);
                res.push(
                    String.fromCharCode(byte1), 
                    String.fromCharCode(byte2)
                );
            } else if (code >= 0x0800 && code <= 0xFFFF) {
                // 三字节
                // U+00000800 – U+0000FFFF  1110xxxx 10xxxxxx 10xxxxxx
                // 1110xxxx
                var byte1 = 0xE0 | ((code >> 12) & 0x0F);
                // 10xxxxxx
                var byte2 = 0x80 | ((code >> 6) & 0x3F);
                // 10xxxxxx
                var byte3 = 0x80 | (code & 0x3F);
                res.push(
                    String.fromCharCode(byte1), 
                    String.fromCharCode(byte2), 
                    String.fromCharCode(byte3)
                );
            } else if (code >= 0x00010000 && code <= 0x001FFFFF) {
                // 四字节
                // U+00010000 – U+001FFFFF  11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
            } else if (code >= 0x00200000 && code <= 0x03FFFFFF) {
                // 五字节
                // U+00200000 – U+03FFFFFF  111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
            } else /** if (code >= 0x04000000 && code <= 0x7FFFFFFF)*/ {
                // 六字节
                // U+04000000 – U+7FFFFFFF  1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
            }
        }

        return res.join('');
    },
    UTF8ToUTF16 : function(str) {
        var res = [], len = str.length;
        var i = 0;
        for (var i = 0; i < len; i++) {
            var code = str.charCodeAt(i);
            // 对第一个字节进行判断
            if (((code >> 7) & 0xFF) == 0x0) {
                // 单字节
                // 0xxxxxxx
                res.push(str.charAt(i));
            } else if (((code >> 5) & 0xFF) == 0x6) {
                // 双字节
                // 110xxxxx 10xxxxxx
                var code2 = str.charCodeAt(++i);
                var byte1 = (code & 0x1F) << 6;
                var byte2 = code2 & 0x3F;
                var utf16 = byte1 | byte2;
                res.push(Sting.fromCharCode(utf16));
            } else if (((code >> 4) & 0xFF) == 0xE) {
                // 三字节
                // 1110xxxx 10xxxxxx 10xxxxxx
                var code2 = str.charCodeAt(++i);
                var code3 = str.charCodeAt(++i);
                var byte1 = (code << 4) | ((code2 >> 2) & 0x0F);
                var byte2 = ((code2 & 0x03) << 6) | (code3 & 0x3F);
                utf16 = ((byte1 & 0x00FF) << 8) | byte2
                res.push(String.fromCharCode(utf16));
            } else if (((code >> 3) & 0xFF) == 0x1E) {
                // 四字节
                // 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
            } else if (((code >> 2) & 0xFF) == 0x3E) {
                // 五字节
                // 111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
            } else /** if (((code >> 1) & 0xFF) == 0x7E)*/ {
                // 六字节
                // 1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
            }
        }

        return res.join('');
    },
    encode : function(str) {
        if (!str) {
            return '';
        }
        var utf8    = this.UTF16ToUTF8(str); // 转成UTF8
        var i = 0; // 遍历索引
        var len = utf8.length;
        var res = [];
        while (i < len) {
            var c1 = utf8.charCodeAt(i++) & 0xFF;
            res.push(this.table[c1 >> 2]);
            // 需要补2个=
            if (i == len) {
                res.push(this.table[(c1 & 0x3) << 4]);
                res.push('==');
                break;
            }
            var c2 = utf8.charCodeAt(i++);
            // 需要补1个=
            if (i == len) {
                res.push(this.table[((c1 & 0x3) << 4) | ((c2 >> 4) & 0x0F)]);
                res.push(this.table[(c2 & 0x0F) << 2]);
                res.push('=');
                break;
            }
            var c3 = utf8.charCodeAt(i++);
            res.push(this.table[((c1 & 0x3) << 4) | ((c2 >> 4) & 0x0F)]);
            res.push(this.table[((c2 & 0x0F) << 2) | ((c3 & 0xC0) >> 6)]);
            res.push(this.table[c3 & 0x3F]);
        }

        return res.join('');
    },
    decode : function(str) {
        if (!str) {
            return '';
        }

        var len = str.length;
        var i   = 0;
        var res = [];

        while (i < len) {
            code1 = this.table.indexOf(str.charAt(i++));
            code2 = this.table.indexOf(str.charAt(i++));
            code3 = this.table.indexOf(str.charAt(i++));
            code4 = this.table.indexOf(str.charAt(i++));

            c1 = (code1 << 2) | (code2 >> 4);
            c2 = ((code2 & 0xF) << 4) | (code3 >> 2);
            c3 = ((code3 & 0x3) << 6) | code4;

            res.push(String.fromCharCode(c1));

            if (code3 != 64) {
                res.push(String.fromCharCode(c2));
            }
            if (code4 != 64) {
                res.push(String.fromCharCode(c3));
            }

        }

        return this.UTF8ToUTF16(res.join(''));
    }
};

console.group('Test Base64: ');
var b64 = Base64.encode('Hello, oschina！又是一年春来到~');
console.log(b64);
console.log(Base64.decode(b64));
console.groupEnd();














