let ws = new WebSocket('wss://ci2.khs1994.com:10000/websocket_server');

ws.onopen = function () {
    ws.send('message');
};

ws.onmessage = function (evt) {

};

ws.onclose = function () {
    alert('websocket close');
};
