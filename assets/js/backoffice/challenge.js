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

});