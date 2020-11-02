// any CSS you import will output into a single css file (app.css in this case)
import '../css/backoffice.scss';
// import moment from 'moment/moment'
import * as $ from 'jquery';
import 'admin-lte/plugins/jquery-ui/jquery-ui'
// import 'admin-lte/plugins/moment/moment-with-locales.min'
import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min'
import 'admin-lte/plugins/chart.js/Chart.bundle.min'
import 'admin-lte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min'
// import 'admin-lte/build/js/AdminLTE'
import 'admin-lte/plugins/summernote/summernote-bs4'
import 'trumbowyg/dist/trumbowyg.min'
import 'trumbowyg/dist/langs/fr.min'
import 'trumbowyg/plugins/table/trumbowyg.table'
import 'trumbowyg/plugins/allowtagsfrompaste/trumbowyg.allowtagsfrompaste'
// import 'admin-lte/plugins/chart.js/Chart'
import 'admin-lte/dist/js/pages/dashboard'
import 'admin-lte/dist/js/adminlte.min'
import 'admin-lte/dist/js/demo'
import 'select2/dist/js/select2.full.min'

export {
    $
}

function loadRun(challenger) {
    $.ajax({
        url: '/admin/run/user/' + challenger,
        async: true,
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                $("#runScore").html(data.html);
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
    console.log(ratio, value);
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
    $.trumbowyg.svgPath = "/build/icons_trumbowyg.svg";
    /** petit hack pour bootstrap file form widget */
    $(document).on("change", '[type=file]', function () {
        let value = $(this).val().replace('C:\\fakepath\\', '').trim();
        $(this).closest('div').find(".custom-file-label").text("" !== value ? value : $(this).attr('placeholder'));
    });
    $(".select2").select2();
    $(".ajax-link").on('click', function () {
        var t = $(this);

        $.ajax({
            url: t.attr('href'),
            dataType: 'json',
            type: 'get',
            success: function (data) {
                console.log(data);
                if (data.success) {
                    if (t.data('replace') === "self") {
                        t.html(data.replace);
                    }
                    if (t.data('remove') === "closestTr") {
                        t.closest("tr").remove();
                    }
                } else {
                    Swal({
                        type: 'error',
                        message: "La requete a échoué, contactez le developpeur."
                    });
                }
            }
        });
        return false;
    })
    $('#challenge_description').trumbowyg({
        lang: 'fr',
        btns: [
            ['viewHTML'],
            ['undo', 'redo'], // Only supported in Blink browsers
            ['formatting'],
            ['strong', 'em', 'del'],
            ['superscript', 'subscript'],
            ['link'],
            ['insertImage'],
            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['removeformat'],
            ['fullscreen']
        ],
        plugins: {}
    });
    $(".delete-line").on('click', function () {
        $(this).closest('tr').remove();
        return false;
    });
});