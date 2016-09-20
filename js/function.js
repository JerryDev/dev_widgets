/*
 * 一些常用的解决实际问题的方法
 */



/*
 * 修复链接在微信下会被缓存的问题
 * 动态给a标签增加 t=Math.random()
 * 需要jQuery
 */
$('a').each(function(i){
    var link = $(this).attr('href');
    var arr;
    if (link.search(/javascript/i) !== -1) {
        return;
    } else if (link.indexOf('?') === -1) {
        $(this).attr('href', link+'?t='+Math.random());
    // } else if (/(t=.*?)(?:&|$)/g.test(link)) {
    } else if ((arr = new RegExp('(t=.*?)(?:&|$)','g').exec(link)) != null) {
        $(this).attr('href', link.replace(arr[1], 't='+Math.random()));
    }
});




