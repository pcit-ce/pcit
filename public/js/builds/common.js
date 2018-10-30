'use strict';

function mouseoutMethod(event) {
  event.target.style.color = 'black';
  event.target.style.borderBottomStyle = 'none';
}

// eslint-disable-next-line no-unused-vars
function mouseoverMethod(event) {
  event.target.style.color = 'green';
  event.target.style.borderBottomStyle = 'solid';
}

function more_options_click_handler(id) {
  $('#pull_requests').after(() => {
    let span_el = $('<span id="column_more_options"></span>');

    return span_el.append((id.slice(0, 1)).toUpperCase() + id.slice(1));
  });
}

module.exports = {
  column_span_click: (id) => {
    let span_el = $('#' + id);
    span_el.css('color', 'green');
    span_el.css('border-bottom-style', 'solid');
  },
  column_click_handle: (id) => {
    let column_el = $('.column span');

    if (-1 !== $.inArray(id, ['', 'settings', 'requests', 'caches', 'trigger_build'])) {
      more_options_click_handler(id);
      column_el.css('color', '#000000').css('border-bottom-style', 'none');
      $('.column #column_more_options').css('color', 'green').css('border-bottom-style', 'solid');

      return;
    }

    // 移除其他元素的颜色
    column_el.css('color', '#000000').css('border-bottom-style', 'none');
    // 启用其他元素的鼠标移出事件
    column_el.on({
      'mouseout': (event) => {
        mouseoutMethod(event);
      }
    });

    // 关闭该元素的鼠标移出事件
    $('#' + id).off('mouseout');

    // 最后对被点击元素
    id = document.getElementById(id);
    id.style.color = 'green';
    id.style.borderBottomStyle = 'solid';
  },
  column_remove: () => {
    // 移除四个主要元素之外的元素
    $('#build_id').remove();
    $('#column_ico').remove();
    $('#column_more_options').remove();
  }
};
