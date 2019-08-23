<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>录像显示页面</title>
</head>
<script src="client/Packet.js?v12"></script>
<script src="client/msgpack.js?v12"></script>
<body>
<img id="receiver" style="width:320px;height:240px"/>
<br><br>如果显示空白，说明当前没有人在直播，<a href="./camera" target="_blank">点击这里直播</a>
<script type="text/javascript" charset="utf-8">
var Init = {
    ws : null,
    url : "",
    timer : 0,
    reback_times : 100,   //断线重连次数
    dubug : true,

    //启动websocket
    webSock: function (url) {
        this.url = url;
        ws =  new WebSocket(url);
        var obj = this;
        //连接回调
        ws.onopen  = function(evt) {
            var timer = setInterval(function () {
                if(obj.ws.readyState == obj.ws.OPEN) {
                    obj.ws.send(parseInt(new Date().getTime()/1000));
                } else {
                    clearInterval(timer);
                }
            }, 5000);
            //清除定时器
            clearInterval(obj.timer);
            //获取用户状态
            obj.log('系统提示: 连接服务器成功');
        };

        //消息回调
        ws.onmessage = function(evt) {
            var data = evt.data;
            image.src = data;
            obj.log(data);
        };
        //关闭回调
        ws.onclose = function(evt) {
            //断线重新连接
            obj.timer = setInterval(function () {
                if(obj.reback_times == 0) {
                    clearInterval(obj.timer);
                }  else {
                    obj.reback_times--;
                    obj.webSock(obj.url);
                }
            },5000);
            obj.log('系统提示: 连接断开');
        };
        //socket错误回调
        ws.onerror  = function(evt) {
            obj.log('系统提示: 服务器错误'+evt.type);
        };
        this.ws = ws;
        return this;
    },

    //打印日志方法
    log: function(msg) {
        if(this.dubug) {
            console.log(msg);
        }
    }
}

var image = document.getElementById('receiver');
obj = Init.webSock("ws://"+document.domain+":18308/vedio");
</script>
</body>
</html>