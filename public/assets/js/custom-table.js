ajaxUrl = $('#table-list').data('url');
col = $('#table-list').data('col');
paginator = $('#table-list').data('paginator');

$("#table-list").tabulator({
    ajaxURL: ajaxUrl, //ajax URL
    ajaxConfig: "GET", //ajax HTTP request type
    layout: "fitColumns", //fit columns to width of table (optional)
    layoutColumnsOnNewData: true,
    pagination: "remote",
    paginationDataReceived: {
        "max_pages": "last_page"
    },
    ajaxLoaderLoading: "Đang tải",
    paginationElement: $(paginator),
    columns: col,
    cellEdited: function (cell) { //trigger an alert message when the row is clicked

    }, cellEditCancelled: function (cell) {

    },
    rowClick: function (e, row) {

    },
    rowContext: function (e, row) {
        //e - the click event object
        //row - row component

        e.preventDefault(); // prevent the browsers default context menu form appearing.
    },
    dataEdited: function (data) {

    },
    tableBuilt: function () {

    },

});

$("#table-list").tabulator({});

$("#cancel-search").click(function (e) {
    var url = $("#table-list").tabulator("getAjaxUrl");
    $("#table-list").tabulator('setData', url, ajaxParams);
})

$("#search-value").on("keyup", function (event) {
    if (event.which == 13 || event.keyCode == 13) {
        $("#search-data").click();
    }
});

$("#search-data").click(function () {
    var url = $("#table-list").tabulator("getAjaxUrl");
    var searchValue = $("#search-value").val();
    $("#table-list").tabulator("setData", url, {
        "search": searchValue,
        "per_page": ajaxParams.per_page
    });
});
