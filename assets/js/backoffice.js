
// any CSS you import will output into a single css file (app.css in this case)
import '../css/backoffice.scss';
// import moment from 'moment/moment'
import $ from 'jquery';
import 'admin-lte/plugins/jquery-ui/jquery-ui'
import 'admin-lte/plugins/moment/moment-with-locales.min'
import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min'
import 'admin-lte/plugins/chart.js/Chart.bundle.min'
import 'admin-lte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min'
// import 'admin-lte/build/js/AdminLTE'
import 'admin-lte/plugins/summernote/summernote-bs4'
// import 'admin-lte/plugins/chart.js/Chart'
import 'admin-lte/dist/js/pages/dashboard'
import 'admin-lte/dist/js/adminlte.min'
import 'admin-lte/dist/js/demo'

export {
    $
}

/** petit hack pour bootstrap file form widget */
$(document).on("change",'[type=file]', function () {
    let value = $(this).val().replace('C:\\fakepath\\', '').trim();
    $(this).closest('div').find(".custom-file-label").text("" !== value ? value : $(this).attr('placeholder'));
});
