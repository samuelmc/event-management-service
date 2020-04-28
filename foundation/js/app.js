$(document).foundation();

$(document).on('change','#item-per-page-selector', (event) => {
    let $selector = $(event.target);
    let queryParams = $.extend($selector.data('queryParams'), {'page':'1','items':$selector.val()});
    window.location.search = $.param(queryParams);
});