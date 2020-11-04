$(function () {
    $('.arbitre').on('change', function () {
        let url = $(this).data('change-url');
        $.ajax({
            url: url,
            data:
                {
                    arbitre: $(this).val()
                }
        })
    });
    let $collectionHolder;
    $collectionHolder = $('.dates');
    $collectionHolder.data('index', $collectionHolder.find('input').length);
    $(".add-date").on('click', function (e) {
        addDateForm($collectionHolder);
    });

    let $collectionHolder2;
    $collectionHolder2 = $('.prizes');
    $collectionHolder2.data('index', $collectionHolder2.find('input').length);
    $(".add-prize").on('click', function(e) {
        addDateForm($collectionHolder2);
    });
});

let addDateForm = function ($collectionHolder) {
    let prototype = $collectionHolder.data('prototype');
    let index = $collectionHolder.data('index');
    let newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);
    $collectionHolder.find('tbody').append("<tr>"+newForm+"</tr>");
};
