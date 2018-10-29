module.exports = {
  column_span_click: (id) => {
    let span_el = $('#' + id);
    span_el.css('color', 'green');
    span_el.css('border-bottom-style', 'solid');
  }
};
