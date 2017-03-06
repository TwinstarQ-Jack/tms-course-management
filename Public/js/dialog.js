/*
 *   dialog.js
 *   用于处理弹框事件
 **/
//声明layer插件

var dialog = {
    // 错误弹出层
    error: function(message) {
        layui.use('layer', function () {
            var layer = layui.layer;
            layer.open({
                skin: 'layui-layer-molv',
                content : message,
                icon : 2,
                title : '错误提示',
                end: function () {
                    loading.end();
                },
            });
        })
    },
    //错误弹出层加返回
    errorto : function (message, url) {
        layui.use('layer', function () {
            var layer = layui.layer;
            layer.open({
                skin: 'layui-layer-molv',
                content : message,
                icon : 2,
                title : '错误提示',
                end: function () {
                    loading.end();
                    location.href = url;
                },
            });
        })
    },
    //成功弹出层
    success : function(message, url) {
        layui.use('layer', function () {
            var layer = layui.layer;
            layer.open({
                skin: 'layui-layer-molv',
                content : message,
                icon : 1,
                yes : function(){
                    location.href = url;
                },
                closeBtn : 0,
            });
        })
    },
    // 确认弹出层
    confirm : function(message, url) {
        layui.use('layer', function () {
            var layer = layui.layer;
            layer.open({
                skin: 'layui-layer-molv',
                content : message,
                icon: 3,
                btn : ['是','否'],
                yes : function(){
                    location.href = url;
                },
            });
        })
    },

    //无需跳转到指定页面的确认弹出层
    toconfirm : function(message) {
        layui.use('layer', function () {
            var layer = layui.layer;
            layer.open({
                skin: 'layui-layer-molv',
                content : message,
                icon : 3,
                closeBtn : 0,
                btn : ['确定'],
            });
        })
    },
};
var msg = {
    show : function (message) {
        layui.use('layer', function () {
            var layer = layui.layer;
            layer.msg(message);
        })
    },
    withtime : function (message, time) {
        layui.use('layer', function () {
            var layer = layui.layer;
            layer.msg(message, {
                time: time,
            })
        })
    },
    withbtn : function (message) {
        layui.use('layer', function () {
            var layer = layui.layer;
            layer.msg(message, {
                time: 10000,
                btn: ['知道了', '取消'],
            })
        })
    },
};
var loading = {
    show : function () {
        layui.use('layer', function () {
            var layer = layui.layer;
            layer.load(0, {shade: [0.1, '#393D49']});
        })
    },
    end : function () {
        layui.use('layer', function () {
            var layer = layui.layer;
            layer.closeAll('loading');
        })
    }
};

//处理空值
var public = {
    isnull : function ($bejudged, $message) {
        if(!$bejudged || $bejudged == "") {
            dialog.error($message + '不能为空');
            loading.end();
            exit();
        }
    }
}
//定义hash插件
!function(a) {
    a.hash || (a.hash = function(b, c) {
        function d(a) {
            return "string" == typeof a || "[object String]" === Object.prototype.toString.call(a)
        }
        var e, f, g, h;
        if (d(b) && "" != b) return e = new RegExp("(;" + b + "=[^;]*)|(\\b" + b + "=[^;]*&)|(\\b" + b + "=[^;]*)", "ig"),
            f = new RegExp(";*\\b" + b + "=[^;]*", "i"),
            "undefined" == typeof c ? (g = location.hash.match(f), g ? decodeURIComponent(a.trim(g[0].split("=")[1])) : null) : (null === c ? location.hash = location.hash.replace(e, "") : (c += "", h = location.hash.replace(e, ""), h += ";" + b + "=" + encodeURIComponent(c), location.hash = h), void 0)

    })

} (jQuery);