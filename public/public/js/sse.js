var sse = new EventSource('/sse_server');

sse.onopen = function () {
  document.getElementById('sse').innerHTML = 'open';
};

sse.onmessage = function (evt) {
  document.getElementById('sse').innerHTML = evt.data;
};

sse.onerror = function () {
  //document.getElementById('sse').innerHTML = 'error';
};

