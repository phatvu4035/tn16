var currentEmp = null;
var validatePosMessage = "";
var validatePositionRule = {
    "title":        ["Nhân viên cùng pháp nhân", "Nhân viên khác pháp nhân", "CTV cùng pháp nhân", "CTV khác pháp nhân", "Nhân viên thuê khoán"],
    "Lương NV":     [1, 0, 0, 0, 0],
    "Lương CTV":    [0, 0, 1, 0, 0],
    "Com NV":       [1, 0, 0, 0, 0],
    "Com CTV":      [0, 0, 1, 0, 0],
    "Thưởng NV":    [1, 0, 0, 0, 0],
    "Thưởng CTV":   [0, 0, 1, 0, 0],
    "TKCM":         [2, 1, 1, 1, 1]
};
var NV_SAME = 0;
var NV_DIFF = 1;
var CTV_SAME = 2;
var CTV_DIFF = 3;
var RENT = 4;


$('.currency-mask').inputmask("numeric", {
    radixPoint: ".",
    groupSeparator: ",",
    digits: 2,
    autoGroup: true,
    rightAlign: false,
    allowMinus: true,
    oncleared: function () {
        $(this).val(0);
    }
});

function guid() {
    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16)
            .substring(1);
    }

    return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
}

var payment_type = payment_type == undefined ? "{}" : payment_type;

var paymentTypeValidator = function (cell, value, parameters) {
    //cell - the cell component for the edited cell
    //value - the new input value of the cell
    //parameters - the parameters passed in with the validator
    return JSON.parse(payment_type)[value] != undefined; //dont allow values divisible by 5; 
}

var printIcon = function (cell, formatterParams) { //plain text value
    return "<i class='fa fa-trash'></i>";
};

var editIcon = function (cell, formatterParams) { //plain text value
    return "<i class='fa fa-pencil'></i>";
};

function arraySearch(arr, val) {
    for (var k in arr) {
        if (arr[k] === val) {
            return k;
        }
    }
    return false;
}

function customSum(values, data, calcParams) {
    var sum = 0;
    values.forEach(function (value) {
        sum += parseInt(value);
    })

    $('#sumVouchers').html(currencyFormat(sum + " VNĐ"));
    updateRowsCount();

    return sum;
}

$("#order-table").tabulator({
    height: "400px", // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
    layout: "fitColumns", //fit columns to width of table (optional)
    selectable: 1,
    layoutColumnsOnNewData: true,
    columns: [ //Define Table Columns
        {title: "id", field: "id", visible: false},
        {title: "Mã nhận dạng", field: "identity_code"},
        {title: "Loại số", field: "identity_type", visible: false},
        {title: "Vị trí", field: "emp_pos", visible: false},
        {title: "Mã số thuế", field: "emp_tax_code", visible: false},
        {title: "Tên nhân viên", field: "emp_name", visible: true},
        {
            title: "Loại chứng từ",
            validator: paymentTypeValidator,
            field: "payment_type",
            align: "left",
            editor: "select",
            editorParams: JSON.parse(payment_type)
        },
        {
            title: "Số tiền",
            field: "payment_value",
            visible: true,
            validator: ["required"],
            formatter: "money",
            editor: true,
            formatterParams: {precision: 0, symbolAfter: true, symbol: " VNĐ"}
        },
        {
            title: "Thuế TNCN",
            field: "personal_tax",
            visible: true,
            validator: ["required"],
            formatter: "money",
            editor: true,
            formatterParams: {precision: 0, symbolAfter: true, symbol: " VNĐ"}
        },
        {
            title: "Thực nhận",
            field: "real_money",
            visible: true,
            formatter: "money",
            editor: false,
            formatterParams: {precision: 0, symbolAfter: true, symbol: " VNĐ"},
            bottomCalc: customSum,
            bottomCalcFormatter: "money",
            bottomCalcFormatterParams: {precision: 0, symbolAfter: true, symbol: " VNĐ"},
            columnCalcs: "group",
        },
        {title: "Ngày cấp", field: "emp_code_date", visible: false},
        {title: "Nơi cấp", field: "emp_code_place", visible: false},
        {title: "Quốc gia", field: "emp_country", visible: false},
        {title: "Tình trạng cư trú", field: "emp_live_status", visible: false},
        {title: "Số tài khoản ngân hàng", field: "emp_account_number", visible: false},
        {title: "Ngân hàng", field: "emp_account_bank", visible: false},
        {title: "Status", field: "status", visible: false},
        {title: "Status", field: "input_status", visible: false},
        {
            title: "Trạng thái",
            field: "data_validate",
            visible: false,
            formatter: "tickCross",
            tooltip: function (cell) {
                if (cell.getValue() == true) {
                    return "Thông tin chính xác";
                } else {
                    var message = cell.getRow().getData().message;
                    arrMess = message.split("|");
                    var tooltip = "";
                    var cols = $("#order-table").tabulator("getColumnDefinitions");
                    arrMess.forEach(mess => {
                        var title = "";
                        if (mess.trim() == "") {
                            return true;
                        }
                        cols.forEach(col => {
                            if (col.field == mess
                            ) {
                                title = col.title;
                                return false;
                            }
                        })
                        ;
                        tooltip += title + " sai cú pháp\n";
                    })
                    ;
                    return tooltip;
                }
            }
        },
        {
            title: "Trạng Thái", field: "emp_delete", visible: false, formatter: function (cell, param) {
                var value = cell.getValue();
                // console.log();
                if (value) {
                    cell.getRow().getElement().css({
                        "color": "#ddd"
                    });
                }
                return value;
            }
        },
        {
            formatter: printIcon, width: 40, align: "center", cellClick: function (e, cell) {
                if (cell.getRow().getCell("status").cell.value == "new") {
                    cell.getRow().delete();
                } else {
                    cell.getRow().update({"status": "deleted"});
                    $("#order-table").tabulator("addFilter", "status", "!=", "deleted");
                }
            }
        },
        {
            formatter: editIcon, width: 40, align: "center", cellClick: function (e, cell) {

                // emp_name: $("#emp-name").val(),
                // emp_code_date: $("#emp-code-date").val(),
                // emp_code_place: $("#emp-code-place").val(),
                // emp_tax_code: $("#emp-tax-code").val(),
                // emp_country: $("#emp-country").val(),
                // emp_live_status: $('input[name=emp-live-status]:checked').parent().text().trim(),
                // emp_account_number: $("#emp-account-number").val(),
                // emp_account_bank: $("#emp-account-bank").val(),
                //row.getIndex()
                if (cell.getRow().getCell("input_status").getValue() == 'search-fail') {
                    enableEditEmpFields();
                } else {
                    disableEditEmpFields();
                }
                $("#edit-emp-name").val(cell.getRow().getCell("emp_name").getValue());
                $("#edit-emp-code-type").val(cell.getRow().getCell("identity_type").getValue());
                $("#edit-emp-identity-code").val(cell.getRow().getCell("identity_code").getValue());
                $("#edit-emp-tax-code").val(cell.getRow().getCell("emp_tax_code").getValue());
                $("#edit-emp-code-date").val(cell.getRow().getCell("emp_code_date").getValue());
                $("#edit-emp-code-place").val(cell.getRow().getCell("emp_code_place").getValue());
                $("#edit-emp-country").val(cell.getRow().getCell("emp_country").getValue());
                $("#edit-emp-live-status").val($('input[name=edit-emp-live-status]:checked').parent().text().trim());
                $("#edit-emp-account-number").val(cell.getRow().getCell("emp_account_number").getValue());
                $("#edit-emp-account-bank").val(cell.getRow().getCell("emp_account_bank").getValue());
                $("#edit-index").val(cell.getRow().getIndex());
                $("#edit-emp-position").val(cell.getRow().getCell("emp_pos").getValue());
                $("#showEditModal").click();
            }
        },
    ],
    cellEdited: function (cell) { //trigger an alert message when the row is clicked
        if (cell.getRow().getCell("status").cell.value != "new" && cell.getRow().getCell("status").cell.value != "deleted") {
            cell.getRow().update({"status": "updated"});
        }

        var calMoney = cell.getRow().getCell("payment_value").cell.value - cell.getRow().getCell("personal_tax").cell.value;
        var index = cell.getRow().getCell("id").cell.value;
        $("#order-table").tabulator("updateRow", index, {
            real_money: calMoney,
        });
        $("#order-table").tabulator("redraw", true);
    }, cellEditCancelled: function (cell) {

    },
    rowClick: function (e, row) {
        // console.log($("#order-table").tabulator("getSelectedData"));
    },
    rowContext: function (e, row) {
        //e - the click event object
        //row - row component

        e.preventDefault(); // prevent the browsers default context menu form appearing.
    }, dataEdited: function (data) {
        //data - the updated table data
        updateRowsCount();
        updateSumVouchers();
    }, dataLoaded: function () {
        console.log('sss1');
        updateRowsCount();
        updateSumVouchers();
    }, rowDeleted: function (row) {
        console.log('sss2');
        updateRowsCount();
        updateSumVouchers();
    },
});

$("#order-table").tabulator("addFilter", "status", "!=", "deleted");

var tblData = $('#tblData').data('table');
if (tblData != '') {
    token = getParameterByName('_token', false);
    localStorage[token] = null;
    $("#order-table").tabulator('setData', tblData);
    updateRowsCount();
    updateSumVouchers();
}


function updateRowsCount() {
    count = $("#order-table").tabulator("getRows", true).length;
    $('#rows-count').html(count + " chứng từ");
}

function updateSumVouchers() {
    setTimeout(function () {

    }, 200)
    sum = $("#order-table").tabulator("getCalcResults").bottom.real_money;
    // console.log(sum);
    $('#sumVouchers').html(currencyFormat(sum + " VNĐ"));

}


//Custom filter example
function customFilter(data) {
    return data.car && data.rating < 3;
}

//Trigger setFilter function with correct parameters
function updateFilter() {
    if ($("#filter-value").val().trim() == "") {
        return;
    }

    var filter = $("#filter-field").val() == "function" ? customFilter : $("#filter-field").val();

    if ($("#filter-field").val() == "function") {
        $("#filter-type").prop("disabled", true);
        $("#filter-value").prop("disabled", true);
    } else {
        $("#filter-type").prop("disabled", false);
        $("#filter-value").prop("disabled", false);
    }

    var listRequiredParse = [">=", "<=", ">", "<"];

    var keyword = listRequiredParse.includes($("#filter-type").val()) ? parseInt($("#filter-value").val()) : $("#filter-value").val();

    $("#order-table").tabulator("setFilter", filter, $("#filter-type").val(), keyword);
}

//Update filters on value change
$("#filter-field, #filter-type").change(updateFilter);
$("#filter-value").keyup(updateFilter);

//Clear filters on "Clear Filters" button click
$("#filter-clear").click(function () {
    $("#filter-field").val("0");
    $("#filter-type").val("=");
    $("#filter-value").val("");

    $("#order-table").tabulator("clearFilter");
    resizeSection();
});
token = getParameterByName('_token', false);
//load sample data into the table
if ($("#order-table").length && (localStorage.getItem(token) !== null)) {
    token = getParameterByName('_token', false);
    $("#order-table").tabulator("setData", JSON.parse(localStorage[token]));
    updateRowsCount();
    updateSumVouchers();
}

$(window).bind('beforeunload', function (e) {
    token = getParameterByName('_token', false);
    if ($("#order-table").length && tblData == "") {
        localStorage[token] = JSON.stringify($("#order-table").tabulator("getData"));
    }
});

function validateInsertData() {
    if ($("#input-status").val() == "search-fail") {
        var emp_status = true;
        $(".emp-required").each(function (element) {
            if ($(this).is("input[type='text']")) {
                if ($(this).val().trim() == "") {
                    warningInput($(this).attr('id'), "không được để trống");
                    emp_status = false;
                    return false;
                }
            }

            if ($(this).is("select")) {
                if ($(this).val() == 0) {
                    warningInput($(this).attr('id'), "phải được chọn");
                    emp_status = false;
                    return false;
                }
            }
        });
        if (!emp_status)
            return false;
    }
    if ($("#identity-code").val().trim() == "") {
        warningInput("identity-code", "không được để trống");
        return false;
    }

    if ($("#payment-type").val() == 0) {
        warningInput("payment-type", "phải được xác định");
        return false;
    }

    if ($("#emp-postion").val() == 0) {
        warningInput("emp-postion", "phải được xác định");
        return false;
    }

    // if ($("#payment-value").inputmask('unmaskedvalue') == 0 || $("#payment-value").inputmask('unmaskedvalue') == "") {
    //     warningInput("payment-value", "không được để trống");
    //     return false;
    // }
    if ($('#personal-tax').val() === "") {
        warningInput("personal-tax", "không được để trống");
        return false;
    }

    if ($("#input-status").val() == "havent-search") {
        warningInput("search", "Bạn cần tra cứu thông tin nhân sự");
        return false;
    }


    return true;
}

function warningInput(elementId, message) {
    makeAlert("Lỗi", $("#label-" + elementId).html() + " " + message + ".", "danger");
    $('html, body').animate({
        scrollTop: $("#" + elementId).offset().top - 200
    }, 800, function () {
        $("#" + elementId).focus();
    });
}

$("#add-voucher").click(function (e) {
    if (!validateInsertData()) {
        return;
    }
    validatePosition = checkPositionWithPayment();
    switch (validatePosition) {
        case 0 :
            makeAlert("Cảnh báo", validatePosMessage, "danger");
            return;
        case 2 :
            if (!confirm("Bạn đang chọn thuê khoán chuyên môn cho nhân viên cùng pháp nhân! Tiếp tục ?")) {
                return;
            }
    }
    // type = (type);
    $("#order-table").tabulator("addRow", {
        id: guid(),
        identity_code: $("#emp-identity-code").val(),
        payment_type: type[$("#payment-type").val()],
        emp_pos: $("#emp-postion").val(),
        identity_type: $("#code-type").prop("value"),
        payment_value: $("#payment-value").inputmask('unmaskedvalue'),
        personal_tax: $("#personal-tax").inputmask('unmaskedvalue') ? $("#personal-tax").inputmask('unmaskedvalue') : 0,
        real_money: $("#real-money").inputmask('unmaskedvalue')? $("#real-money").inputmask('unmaskedvalue') : 0,
        emp_name: $("#emp-name").val(),
        emp_code_date: $("#emp-code-date").val(),
        emp_code_place: $("#emp-code-place").val(),
        emp_tax_code: $("#emp-tax-code").val(),
        emp_country: $("#emp-country").val(),
        emp_live_status: $('input[name=emp-live-status]:checked').parent().text().trim(),
        emp_account_number: $("#emp-account-number").val(),
        emp_account_bank: $("#emp-account-bank").val(),
        input_status: $("#input-status").val(),
        status: 'new',
        data_validate: true,
    }, true);
    clearEmpFields();
    $("#identity-code").val("").change();
    $("#emp-postion").val("").change();
    // $("#payment-type").val($("#payment-type option:first").val()).change();
    $("#payment-type").prop("disabled", true);
    inputClass = 'payment-value';
    $("select." + inputClass).val($("select." + inputClass + " option:first").val()).change();
    $("." + inputClass).prop('disabled', true);
    $("input." + inputClass).prop('value', '');
    $("#real-money").val("");
    disableEmpFields();
    $("#add-voucher").prop("disabled", true);
});

$("#download-json").click(function () {
    $("#order-table").tabulator("download", "xlsx", "data.xlsx", {sheetName: "My Data"});
});

function countTax() {
    var realMoney = $("#payment-value").inputmask('unmaskedvalue') - $("#personal-tax").inputmask('unmaskedvalue');
    $("#real-money").val(realMoney);
}

$("#payment-value").on("change keyup paste", function (edited) {
    countTax()
});
$("#personal-tax").on("change keyup paste", function (edited) {
    countTax()
});

function bindCodeTypeDropdown(dropId, inputClass) {
    $("#" + dropId).change(function (e) {
        if (this.value != 0) {
            $("." + inputClass).prop('disabled', false);
            $("input." + inputClass).prop('value', '0');
        } else {
            $("select." + inputClass).val($("select." + inputClass + " option:first").val()).change();
            $("." + inputClass).prop('disabled', true);
            $("input." + inputClass).prop('value', '');
        }
    });
}

bindCodeTypeDropdown("payment-type", "payment-value");

function checkPositionWithPayment() {
    var position = -1;
    var pn = $('input[type=hidden][name=phap_nhan]').val();

    if (currentEmp == null) {
        currentEmp = {
            emp_postion : "tk"
        };
    }
    switch (currentEmp.emp_postion) {
        case "nv" :
            if (currentEmp.emp_phap_nhan !== pn) {
                position = NV_DIFF;
            } else {
                position = NV_SAME;
            }
            break;
        case "ctv" :
            if (currentEmp.emp_phap_nhan !== pn) {
                position = CTV_DIFF;
            } else {
                position = CTV_SAME;
            }
            break;
        case "tk" :
        default :
            position = RENT;
            break;
    }

    var paymentType = $("#payment-type option:selected").text();

    if (typeof validatePositionRule[paymentType] == "undefined") {
        paymentType = "TKCM";
    }

    if (validatePositionRule[paymentType][position] == 0) {
        validatePosMessage = validatePositionRule["title"][position] + " <b>không</b> thể có " + paymentType;
    }

    return validatePositionRule[paymentType][position];
}

// validating input
function searchByCode(code) {

    if ($("#identity-code").val() == "") {
        $("#identity-code").focus();
        makeAlert("Cảnh báo", "Bạn cần nhập mã nhận dạng để tiến hành tìm kiếm", "danger");
        return;
    }


    var code_value = $("#identity-code").val()
    var code_type = $("#code-type-search").prop("value");
    $.ajax({
        url: "/orders/search-employee",
        context: document.body,
        data: {
            "code-type": code_type,
            "code-value": code_value
        }
    }).done(function (e) {
        if (e.data.length > 0) {
            var data = e.data[0];
            var bindData = {};
            if (data.employee_code != undefined) {
                bindData = {
                    identity_code: data.employee_code,
                    identity_type: 'mnv',
                    emp_name: data.last_name + " " + data.first_name,
                    emp_code_date: "",
                    emp_code_place: "",
                    emp_country: "VN",
                    emp_live_status: "yes",
                    emp_phap_nhan: data.phap_nhan,
                    emp_account_number: data.bank_account,
                    emp_account_bank: data.bank,
                    emp_postion: data.vi_tri == 'V' ? 'ctv' : 'nv'
                };
                $('#phap_nhan_name').closest('p').removeClass('text-danger');
                pn = $('input[type=hidden][name=phap_nhan]').val();
                $('#phap_nhan_name').html(data.phap_nhan);
                if (pn != data.phap_nhan) {
                    $('#phap_nhan_name').closest('p').addClass('text-danger');
                }
            } else {
                bindData = {
                    identity_code: data.identity_code,
                    identity_type: data.identity_type,
                    emp_name: data.emp_name,
                    emp_code_date: new Date(data.emp_code_date) == "Invalid Date" ? "" : dateFormatter(data.emp_code_date),
                    emp_code_place: data.emp_code_place,
                    emp_country: data.emp_country,
                    emp_phap_nhan: "",
                    emp_tax_code: data.emp_tax_code,
                    emp_live_status: data.emp_live_status == 1 ? "yes" : "no",
                    emp_account_number: data.emp_account_number,
                    emp_account_bank: data.emp_account_bank,
                    emp_postion: 'tk'
                };
                $('#phap_nhan_name').closest('p').removeClass('text-danger');
                $('#phap_nhan_name').html('--');
            }
            currentEmp = bindData;
            makeAlert("Thành công", "Tra cứu thành công", "info");
            bindEmpInfo(bindData);
            $("#input-status").val("search-success");
        } else {
            currentEmp = null;
            makeAlert("Thất bại", "Không tìm thây thông tin khớp với mã ! Vui lòng nhập thông tin nhân viên mới", "danger");
            setTimeout(() => {
                    $("#emp-name"
                    ).focus();
                },
                100
            )
            ;
            $("#input-status").val("search-fail");
            $("#emp-postion").val('tk');
            $('#phap_nhan_name').closest('p').removeClass('text-danger');
            $('#phap_nhan_name').html('--');
            clearEmpFields();
            enableCeateEmpFields();
        }
        $("#add-voucher").prop("disabled", false);
        $("#payment-type").prop("disabled", false);
        if ($("#payment-type").val() != 0) {
            $(".payment-value").prop("disabled", false);
        }
    });
}

function enableCeateEmpFields() {
    $(".emp-create").prop("disabled", false).change();
    $("#emp-identity-code").val($("#identity-code").val());
    $("#code-type").val($("#code-type-search").val());
}

function enableEditEmpFields() {
    $(".emp-edit").prop("disabled", false);
    $("#edit-emp-identity-code").val($("#identity-code").val());
    // $("#edit-emp-identity-code").prop("disabled", false);
    // $("#edit-emp-name").prop("disabled", false);
    // $("#edit-emp-code-date").prop("disabled", false);
    // $("#edit-emp-code-place").prop("disabled", false);
    // $("#edit-emp-tax-code").prop("disabled", false);
    // $("#edit-emp-country").prop("disabled", false);
    // $('input[name=edit-emp-live-status]').prop("disabled", false);
    // $("#edit-emp-account-number").prop("disabled", false);
    // $("#edit-emp-account-bank").prop("disabled", false);
}

function clearEmpFields() {
    $("#emp-identity-code").val("");
    $("#emp-name").val("");
    $("#emp-code-date").val("");
    $("#emp-code-place").val("");
    $("#emp-tax-code").val("");
    $("#emp-country").val("VN");
    $('input[name=emp-live-status][value="yes"]').prop("checked", true);
    $("#emp-account-number").val("");
    $("#emp-account-bank").val("");
}

function disableEmpFields() {
    $(".emp-create").prop("disabled", true);
}

function disableEditEmpFields() {
    $(".emp-edit").prop("disabled", true);
    // $("#edit-emp-identity-code").prop("disabled", true);
    // $("#edit-emp-name").prop("disabled", true);
    // $("#edit-emp-code-date").prop("disabled", true);
    // $("#edit-emp-code-place").prop("disabled", true);
    // $("#edit-emp-tax-code").prop("disabled", true);
    // $("#edit-emp-country").prop("disabled", true);
    // $('input[name=edit-emp-live-status]').prop("disabled", true);
    // $("#edit-emp-account-number").prop("disabled", true);
    // $("#edit-emp-account-bank").prop("disabled", true);
}

function bindEmpInfo(info) {
    $("#emp-identity-code").val(info.identity_code);
    $("#code-type").val(info.identity_type);
    $("#emp-postion").val(info.emp_postion);

    $("#emp-name").val(info.emp_name);
    $("#emp-code-date").val(info.emp_code_date);
    $("#emp-code-place").val(info.emp_code_place);
    $("#emp-tax-code").val(info.emp_tax_code);
    $("#emp-country").val(info.emp_country);
    // $("#emp-country").val(info.identity_type ? info.identity_type : 0);
    $('input[name=emp-live-status][value="' + info.emp_live_status + '"]').prop("checked", true);

    $("#emp-account-number").val(info.emp_account_number);
    $("#emp-account-bank").val(info.emp_account_bank);
    disableEmpFields();
}

$("#search").click(function (e) {
    searchByCode($("#identity-code").val());
});

function validateEmpInfo() {
    var result = true;
    $(".edit-emp-required").each(function (element) {
        if ($(this).is("input[type='text']")) {
            if ($(this).val().trim() == "") {
                makeAlert("Lỗi", $("#label-" + $(this).attr('name')).html() + " không được để trống", "danger");
                $(this).focus();
                result = false;
                return false;
            }
        }

        if ($(this).is("select")) {
            if ($(this).val() == 0) {
                makeAlert("Lỗi", $("#label-" + $(this).attr('name')).html() + " phải được chọn", "danger");
                $(this).focus();
                result = false;
                return false;
            }
        }
    });

    return result;
}

$("#save-edit").click(function (e) {
    if (!validateEmpInfo()) {
        return;
    }
    $("#order-table").tabulator("updateRow", $("#edit-index").val(), {
        emp_name: $("#edit-emp-name").val(),
        emp_code_date: $("#edit-emp-code-date").val(),
        emp_code_place: $("#edit-emp-code-place").val(),
        emp_tax_code: $("#edit-emp-tax-code").val(),
        emp_country: $("#edit-emp-country").val(),
        emp_live_status: $('input[name=edit-emp-live-status]:checked').parent().text().trim(),
        emp_account_number: $("#edit-emp-account-number").val(),
        emp_account_bank: $("#edit-emp-account-bank").val(),
        emp_pos: $("#edit-emp-position").val(),
        identity_type: $("#edit-emp-code-type").prop("value")
    });
    makeAlert("Thành công", "Cập nhật thông tin thành công", "success");
    $("#close-edit").click();
    $("#close-edit").click();
});

function getUrlVars() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

var urlParamsObject = getUrlVars();
var urlParams = {};

urlParamsObject.forEach(element => {
    urlParams[element] = urlParamsObject[element];
});

$("#save-data").click(function (e) {
    var data = {};
    data['order'] = urlParams;
    var isEdit = false;
    if ($(this).data('edit') == true && $('#order').data('order') != undefined) {
        data['order'] = $('#order').data('order');
        isEdit = true;
        var url = $(this).data('url');
    }
    data['vouchers'] = $("#order-table").tabulator("getData");

    $.ajax({
        url: "/luu-tru-bo-thanh-toan",
        method: 'post',
        data: data,
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    }).done(function (e) {
        if (e.result == 'fail') {
            if (typeof e.message !== 'undefined') {
                makeAlert("Không thành công", e.message, "danger");
                return;
            }
            $("#order-table").tabulator("setData", e.data);
            $("#order-table").tabulator("showColumn", "data_validate");
            makeAlert("Không thành công", "Thông tin chứng từ không đúng, vui lòng kiểm tra lại dữ liệu", "danger");
            $('html, body').animate({
                scrollTop: $("#order-table").offset().top - 200
            });
            $("#order-table").tabulator("redraw");
            updateRowsCount();
            updateSumVouchers();
        } else if (e.result == 'success') {
            localStorage['success'] = 'success';
            $("#order-table").tabulator("clearData");
            if (isEdit) {
                console.log(isEdit);
                window.location.replace(url);
            } else {
                window.location.replace("/danh-sach-bo-thanh-toan");
            }
        } else if (e.result = "redirect") {
            $("#order-table").tabulator("clearData");
            window.location.replace(e.url);
        }
    });
});

if (localStorage.getItem("success") === 'success') {
    localStorage['success'] = null;
    // makeAlert("Thành công", "Thêm dữ liệu thành công", "success");
}

$(".start-date").focus(function () {
    $(".day").unbind("click");
    $(".day").click(function () {
        $(".datepicker").remove();
    });
});

$("#cancel-insert").click(function () {
    $("#order-table").tabulator("clearData");
    window.open("/danh-sach-bo-thanh-toan", "_self");
})
$("#cancel-insert-edit").click(function () {
    window.location.href = $(this).data('href');
})

$("#identity-code").keypress(function (e) {
    if (e.which == 13) {
        $("#search").click();
    }
});