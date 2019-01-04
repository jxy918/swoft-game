<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>录像页面</title>
</head>
<script src="client/Packet.js?v12"></script>
<script src="client/msgpack.js?v12"></script>
<body>
<video autoplay id="sourcevid" style="width:320;height:240px"></video>
<br>
提示：最好用火狐测试，谷歌浏览器升级了安全策略，谷歌浏览器只能在https下才能利用html5打开摄像头。
<canvas id="output" style="display:none"></canvas>
<script type="text/javascript" charset="utf-8">
var back = document.getElementById('output');
var backcontext = back.getContext('2d');
var video = document.getElementsByTagName('video')[0];
var success = function(stream){
    video.src = window.URL.createObjectURL(stream);
}

var draw = function(socket){
    try{
        backcontext.drawImage(video,0,0, back.width, back.height);
    }catch(e){
        if (e.name == "NS_ERROR_NOT_AVAILABLE") {
            return setTimeout(draw, 1000);
        } else {
            throw e;
        }
    }
    if(video.src){
        if(socket.readyState == socket.OPEN) {
			var img_data = back.toDataURL("image/jpeg", 0.5);
            socket.send(img_data);
        }
    }
    setTimeout(draw,1000);
}

var Init = {
    ws : null,
    url : "",
    timer : 0,
    reback_times : 100,   //断线重连次数
    dubug : false,

    //启动websocket
    webSock: function (url) {
        this.url = url;
        ws =  new WebSocket(url);
        var obj = this;
        //连接回调
        ws.onopen  = function(evt) {
            var timer = setInterval(function () {
                if(obj.ws.readyState == obj.ws.OPEN) {
                    obj.ws.send((new Date()).valueOf());
                } else {
                    clearInterval(timer);
                }
            }, 5000);
            //清除定时器
            clearInterval(obj.timer);
            //获取用户状态
            obj.log('系统提示: 连接服务器成功');
            //触发录像
            draw(ws);
        };

        //消息回调
        ws.onmessage = function(evt) {
            obj.log(evt);
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
obj = Init.webSock("ws://"+document.domain+":10000/vedio");

navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
navigator.getUserMedia({video:true, audio:false}, success, console.log);
</script>
</body>
</html>