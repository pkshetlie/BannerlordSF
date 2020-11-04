import * as $ from "jquery";

function loadRun(challenger) {
    $.ajax({
        url: '/admin/run/user/' + challenger,
        async: true,
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                $("#runScore").html(data.html);
                $('[id^=run_runSettings]').each(function () {
                    updateLigne($(this).closest('tr'));
                });
            } else {
                $("#runScore").html(data.message);
            }
        }
    });
}

function totalRun() {
    let sum = 0;
    $(".total-line").each(function () {
        let value = $(this).text();
        // add only if the value is number
        if (!isNaN(value) && value.length !== 0) {
            sum += parseFloat(value);
        }
    });
    $(".total-run").html(sum);
}

function updateLigne(ligne) {
    let ratio = parseFloat(ligne.find('.ratio').text().replace(',', '.'));
    let value = parseFloat(ligne.find("input").val().replace(',', '.'));
    let total = ratio * value;
    if (value > 0) {
        ligne.find('.total-line').html(total);
    }
    totalRun();
}

$(function () {
    let cancelableXhr = null;
    $(".twitcher li a").on('click', function () {
        let url = $(this).data('url');
        loadRun($(this).data('challenger'))
        $("#twitch_player").attr('src', url);
        return false;
    })


    $(document).on('keyup', '[id^=run_runSettings]', function () {
        let ligne = $(this).closest('tr');
        updateLigne(ligne);
    });
    $(document).on('keyup change', '[id^=run_runSettings_]', function () {
        $("form#runForm").submit();
    });
    $(document).on('keyup change', '#run_comment', function () {
        $("form#runForm").submit();
    });
    $(document).on('submit', "form#runForm", function () {
        if (cancelableXhr !== null) {
            cancelableXhr.abort();
        }
        cancelableXhr = $.ajax({
            url: $(this).attr('action'),
            type: 'post',
            data: $(this).serialize()
        });

        return false;
    });
});