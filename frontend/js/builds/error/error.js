function error_info(
  header,
  title = '',
  content = '',
  border_type = 'secondary',
  text_type = 'danger',
) {
  return $('<div></div>')
    .addClass('card mb-12 text-center')
    .addClass('border-' + border_type)
    .append(
      $('<div></div>')
        .addClass('card-header')
        .append(header),
    )
    .append(
      $('<div></div>')
        .addClass('card-body text-' + text_type)
        .append(
          $('<h5></h5>')
            .addClass('card-title')
            .append(title),
        )
        .append(
          $('<p></p>')
            .addClass('card-text text-center')
            .append(content),
        )
        .append(
          $('<a></a>')
            .addClass('btn btn-outline-primary')
            .append('Get Help')
            .css('margin-right', '20')
            .attr({
              href: '//ci.khs1994.com/issues',
              target: '_block',
            }),
        )
        .append(
          $('<img>')
            .addClass('card-img-bottom')
            .attr({
              src:
                'https://user-images.githubusercontent.com/16733187/41330207-9416717c-6f04-11e8-961f-c606303e7bb5.jpg',
            })
            .css({ 'margin-top': '20px' }),
        ),
    );
}

module.exports.error_info = error_info;
