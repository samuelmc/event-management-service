$(document).foundation();

$(document).on('change','#item-per-page-selector', (event) => {
    let $selector = $(event.target);
    let queryParams = $.extend($selector.data('queryParams'), {'page':'1','items':$selector.val()});
    window.location.search = $.param(queryParams);
});

$(document).on('submit','#search-widget', (event) => {
    event.preventDefault();
    let $form = $(event.target);
    let formData = $form.serializeArray();
    let queryParams = $.extend($form.data('queryParams'),{'search': formData[0].value});
    window.location.search = $.param(queryParams);
});
