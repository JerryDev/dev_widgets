
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










