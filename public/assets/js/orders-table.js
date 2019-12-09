var ajaxParams = {
    per_page: 15
};

var sumFunction = function (values, data, calcParams) {

}

$("#list-orders-table").tabulator({
    ajaxURL: "/orders/get-data", //ajax URL
    ajaxParams: ajaxParams, //ajax parameters
    ajaxConfig: "GET", //ajax HTTP request type
    height: "", // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
    layout: "fitColumns", //fit columns to width of table (optional)
    selectable: 1,
    ajaxSorting:true,
    layoutColumnsOnNewData: true,
    pagination: "remote",
    widthShrink: [''],
    paginationDataReceived: {
        "max_pages": "last_page"
    },
    ajaxLoader: false,
    // ajaxLoaderLoading: '<div class="loader"></div>',
    paginationElement: $("#pagination-list-orders-table"),
    columns: [ //Define Table Columns
        {
            title: "#", field: "id", formatter: function (cell, formatterParams) {
                //cell - the cell component
                //formatterParams - parameters set for the column
                var page = $("#list-orders-table").tabulator("getPage") - 1;
                var size = ajaxParams['per_page'];
                return (page * size) + cell.getRow().getPosition(true) + 1;
            }, width: 50, headerSort: false
        },
        {
            title: "Tên bộ F", field: "id", formatter: function (cell, formatterParams) {
                //cell - the cell component
                //formatterParams - parameters set for the column

                return "F-" + cell.getValue();
            }, width: 140
        },
        {title: "Mã dự toán", field: "ma_du_toan", width: 140},
        {title: "Serial", field: "serial", width: 140},
        {title: "Nội dung", field: "noi_dung", visible: true},
        {title: "Người đề xuất", field: "nguoi_de_xuat", visible: true, width: 120},
        {
            title: "Ngày tạo", field: "ngay_de_xuat", visible: true, formatter: function (cell, formatterParams) {
                //cell - the cell component
                //formatterParams - parameters set for the column

                return dateFormatter(cell.getValue());
            }, width: 120
        },
        {
            title: "Thu nhập chịu thuế",
            field: "tong_tnct",
            visible: true,
            formatter: "money",
            formatterParams: {precision: 0, symbolAfter: true, symbol: " VNĐ"}
        },
        {
            title: "Thuế",
            field: "thue",
            visible: true,
            formatter: "money",
            formatterParams: {precision: 0, symbolAfter: true, symbol: " VNĐ"}
        },
        {
            title: "Thực nhận",
            field: "thuc_nhan",
            visible: true,
            formatter: "money",
            editor: false,
            formatterParams: {precision: 0, symbolAfter: true, symbol: " VNĐ"}
        },
        {
            title: "Loại",
            field: "isSalary",
            visible: true,
            editor: false,
            formatter: function (cell, formatterParams) {
                //cell - the cell component
                //formatterParams - parameters set for the column
                if(cell.getValue()==1){
                    cell.getRow().getElement().addClass('alert-primary');
                    return "Lương";

                }else{
                    return "Ngoài Lương"
                }
            }
        },
        {
            title: "Người tạo",
            field: "nguoi_tao",
            visible: true,
            editor: false
        },
        {
            title: "Me",
            field: "me",
            visible: false,
            formatter: "money",
            editor: false
        },

    ],
    cellEdited: function (cell) { //trigger an alert message when the row is clicked

    }, cellEditCancelled: function (cell) {

    },
    rowClick: function (e, row) {
        // console.log($("#order-table").tabulator("getSelectedData"));
        checkEdit = $('#checkRole').data('check-edit');
        console.log(checkEdit);

        var id = row.getCell("id").getValue();
        if (checkEdit == 'edit.order')
            window.open("/bo-thanh-toan/" + id, "_self");
        if (checkEdit == 'edit.order.self') {
            me = $('#me').data('me');
            created_by = row.getCell('me').getValue();
            if (me == created_by)
                window.open("/bo-thanh-toan/" + id, "_self");
        }
    },
    rowContext: function (e, row) {
        //e - the click event object
        //row - row component

        e.preventDefault(); // prevent the browsers default context menu form appearing.
    }, dataEdited: function (data) {

    }, ajaxRequesting: function (url, params) {
        //url - the URL of the request
        //params - the parameters passed with the request
        $("#loader").removeClass("fadeOut");
        $("#loader").addClass("fadeIn");
        $("#loader").css("opacity", 0.5);
    }, ajaxResponse: function (url, params, response) {
        //url - the URL of the request
        //params - the parameters passed with the request
        //response - the JSON object returned in the body of the response.
        $("#loader").addClass("fadeOut");
        $("#loader").removeClass("fadeIn");
        if (response['last_page'] == 1) {
            $("#pagination-list-orders-table").hide();
        } else {
            $("#pagination-list-orders-table").show();
        }
        return response; //return the response data to tabulator
    }, ajaxError: function (xhr, textStatus, errorThrown) {
        //xhr - the XHR object
        //textStatus - error type
        //errorThrown - text portion of the HTTP status
        $("#loader").addClass("fadeOut");
        $("#loader").removeClass("fadeIn");
    },
    locale: true,
    langs: {
        "vi": {
            "pagination": {
                "first": "First", //text for the first page button
                "first_title": "First Page", //tooltip text for the first page button
                "last": "Last",
                "last_title": "Last Page",
                "prev": "&larr;",
                "prev_title": "Prev Page",
                "next": "&rarr;",
                "next_title": "Next Page",
            }
        }
    },
});

$("#list-orders-table").tabulator({});

$("#cancel-search").click(function (e) {
    var url = $("#list-orders-table").tabulator("getAjaxUrl");
    $("#list-orders-table").tabulator('setData', url, ajaxParams);
})

var oldRequest = {'search': "@"};

function searchDataByRequest(request) {
    if (request['search'] == oldRequest['search']) {
        $("#loader").removeClass("fadeOut");
        $("#loader").addClass("fadeIn");
        $("#loader").css("opacity", 0.5);

        setTimeout(() => {
            $("#loader").addClass("fadeOut");
            $("#loader").removeClass("fadeIn");
        }, 500);
        return;
    }
    var url = $("#list-orders-table").tabulator("getAjaxUrl");
    $("#list-orders-table").tabulator("setData", url, request);
    oldRequest = request;
}

$("#search-data").click(function () {
    var searchValue = $("#search-value").val();
    if (searchValue.trim() == "") {
        return;
    }
    var request = {
        "search": searchValue,
        "per_page": ajaxParams.per_page
    };

    searchDataByRequest(request);
});

$("#search-value").on("keyup", function (e) {

    if (event.which == 13 || event.keyCode == 13) {
        $("#search-data").click();
    }

    if ($(this).val() == "" && e.keyCode != 13) {
        var request = {
            "search": "",
            "per_page": ajaxParams.per_page
        };
        searchDataByRequest(request);
    }
});