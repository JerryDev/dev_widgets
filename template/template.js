/**
 *
 * @authors Your Name (you@example.org)
 * @date    2015-06-23 13:54:10
 * @version $Id$
 */


// 普通函数调用
function test(){
    console.log('function test');
}
test(); //调用

/*
* 匿名函数 会直接执行一遍的函数
*/

// 原型
// ()();


// 应用
(function(){
    console.log('execute directly');
})();

(function(name){
    console.log('hello '+ name);
})('Jerry');

// 包装jQuery
(function($){
    console.log('body width: ' + $('body').width());
})(jQuery);




















