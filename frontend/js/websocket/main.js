let ws = new WebSocket('wss://ci2.khs1994.com:10000/websocket/server');

ws.onopen = function() {
  ws.send('message');
};

ws.onmessage = function(evt) {
  // console.log(evt);

  let div_el = document.getElementById('message');
  div_el.innerHTML = 1;
};

ws.onclose = function() {
  alert('websocket close');
};
