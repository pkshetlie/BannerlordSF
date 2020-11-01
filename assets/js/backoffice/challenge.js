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
    var $collectionHolder;

// setup an "add a tag" link


        // Get the ul that holds the collection of tags
        $collectionHolder = $('.dates');

        // add the "add a tag" anchor and li to the tags ul

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $collectionHolder.data('index', $collectionHolder.find('input').length);

        $(".add-date").on('click', function(e) {
            // add a new tag form (see next code block)
            addDateForm($collectionHolder);
        });

});



var addDateForm = function ($collectionHolder) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');
    // get the new index
    var index = $collectionHolder.data('index');
    var newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);
    $collectionHolder.find('tbody').append("<tr>"+newForm+"</tr>");
};
