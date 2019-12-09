//create an alert message

function makeAlert(title, message, type,align="center") {
    $.notify({
        // options
        icon: 'glyphicon glyphicon-warning-sign',
        title: "<b>"+title+" ! </b><br/>",
        message: message,
        url: '',
        target: '_blank'
    },{
        // settings
        element: 'body',
        position: null,
        type: type,
        allow_dismiss: true,
        newest_on_top: false,
        showProgressbar: false,
        placement: {
            from: "bottom",
            align: 'center'
        },
        offset: 20,
        spacing: 10,
        z_index: 1051,
        delay: 4000,
        timer: 1000,
        url_target: '_blank',
        mouse_over: null,
        animate: {
            enter: 'animated fadeInDown',
            exit: 'animated fadeOutUp'
        },
        onShow: null,
        onShown: null,
        onClose: null,
        onClosed: null,
        icon_type: 'class',
    });
}

function resizeSection() {
    var height = $(".tabulator").outerHeight();
    $("#mainContent").height(height);
    $("#mainContent .masonry-sizer").height(height);
    $("#mainContent .masonry").height(height);
}

function resizeHeight() {
    var height = $(".masonry-item").outerHeight();
    $("#mainContent").height(height);
    $("#mainContent .masonry-sizer").height(height);
    $("#mainContent .masonry").height(height);
}

Date.prototype.ddmmyyyy = function() {
    var mm = this.getMonth() + 1; // getMonth() is zero-based
    var dd = this.getDate();
  
    return [
            (dd>9 ? '' : '0') + dd,
            (mm>9 ? '' : '0') + mm,
            this.getFullYear(),
           ].join('/');
  };
  

function dateFormatter(dateString) {
    var d = new Date(dateString);
    return d.ddmmyyyy();
}

function currencyFormat(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

if (localStorage.getItem("web_message") !== null) {
    var message = JSON.parse(localStorage.getItem("web_message"));
    localStorage.removeItem("web_message");
    makeAlert(message['title'], message['content'], message['type']);
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

function validateNotNull(id, text) {
    var check = true;
    $('#small-' + id).html("");
    var value = $('#' + id).val();
    if (typeof value === 'string' || value instanceof String) {
        $('#' + id).val(value.trim());
    }
    if (!$('#' + id).val()) {
        check = false;
        $('#small-' + id).html(text + " không được để trống.");
    }
    if (id == "serial") {
        if (!$.isNumeric($('#' + id).val())) {
            check = false;
            $('#small-' + id).html(text + " phải là số.");
        }
    }
    return check;
}

$(document).ajaxError(function(event, jqXHR, settings, thrownError) {
    if (settings.handleErrors && settings.handleErrors.indexOf(jqXHR.status) !== -1) {
        return;
    }
    switch (jqXHR.status) {
        case 403:
            makeAlert("Ngăn chặn truy cập", "Bạn không có quyền truy cập chức năng này", "danger")
            break;
        default:
            console.log('Anyone knows what is going on here?');
    }
});