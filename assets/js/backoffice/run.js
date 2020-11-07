import * as $ from "jquery";

function inputCreation() {
    $('input').each(function () {
        let t = $(this);
        let type = t.closest('td').data('input-type')
        let value = t.closest('td').data('default-value')
        switch (type) {
            case 200:
                let input = "<select name='"+t.attr('name')+"' class='form-control form-control-sm'><option value=''>-- séléctionner -- </option>";
                let values= value.split(';');
                for(let i = 0;values.length>i; i++){
                    input +="<option "+(values[i] === t.val()?"selected='selected'":"")+" value='"+values[i]+"'>"+values[i]+"</option>";
                }
                input +="</select>";
                t.replaceWith(input);
                break;
            case 300:
                let checkbox = "<input type='checkbox' "+(values[i] === t.val()?"checked='checked'":"")+" name='"+t.attr('name')+"' class='form-control form-control-sm' value='value'/>";
                t.replaceWith(checkbox);
                break;
            default:
            case 100:
                break;

        }

    })
}


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
                    inputCreation();
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
        let tr = $(this).closest('tr');
        if (tr.data("isusedforscore") === 1) {
            if (!isNaN(value) && value.length !== 0) {
                sum += parseFloat(value);
            }
        }
    });
    let malus = parseFloat($("#malus-run").data('malus').toString().replace(',', '.'));
    let total_malus = sum * malus;
    $('.total-run-with-malus').html(total_malus);
    $(".total-run").html(sum);
}

function updateLigne(ligne) {
    let ratio = parseFloat(ligne.find('.ratio').data('ratio').replace(',', '.'));
    let value = parseFloat(ligne.find("input").val().replace(',', '.'));
    let total = ratio * value;
    if (value > 0) {
        ligne.find('.total-line').html(total);
    }
    if (ligne.data("issteptovictory") === 1) {
        let min = parseFloat(ligne.data("steptovictorymin"));
        let max = parseFloat(ligne.data("steptovictorymax"));
        if (isNaN(min)) {
            min = -999999999;
        }
        if (isNaN(max)) {
            max = 999999999;
        }
        console.log(min, max);
        if (value <= max && value >= min) {
            ligne.removeClass('bg-orange');
            ligne.addClass('bg-green');
        } else {
            ligne.removeClass('bg-green');
            ligne.addClass('bg-orange');
        }
    }


    if (ligne.data("isusedforscore") === 1) {
        totalRun();
    }
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

    $(document).on('click', "form#runForm button", function () {
        if (cancelableXhr !== null) {
            cancelableXhr.abort();
        }
        let t = $(this)
        let form = $(this).closest('form');
        cancelableXhr = $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize() + "&button=" + $(this).attr('id'),
            success: function (data) {
                if (data.refesh) {
                    loadRun(form.data('challenger'));
                }
            }
        });
        return false;
    });
    $(document).on('submit', "form#runForm", function () {
        if (cancelableXhr !== null) {
            cancelableXhr.abort();
        }
        let t = $(this)
        cancelableXhr = $.ajax({
            url: $(this).attr('action'),
            type: 'post',
            data: $(this).serialize(),
            success: function (data) {
                if (data.refresh) {
                    loadRun(t.data('challenger'));
                }
            }
        });
        return false;
    });


});