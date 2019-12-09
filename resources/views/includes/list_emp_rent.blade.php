<h3 class="c-grey-900">@if(isset($title)){{$title}}@endif</h3>

<div class="mT-30">
    <div class="group-search">
        <form class="frm-search" action="{{route('emp_rent.index')}}" id="formSearch">
            @if(isset($filter) && $filter)
                @foreach($filter as $f)
                    @if($f['type']=='text')
                        <input style="width: 200px;margin-left: 10px" type="text" class="form-control"
                               name="{{$f['name']}}"
                               value="@if(isset($getData[$f['name']])&&$getData[$f['name']]){{$getData[$f['name']]}}@elseif(isset($f['value'])){{$f['value']}}@endif"
                               placeholder="{{$f['placeholder']}}"
                        />
                    @endif
                    @if($f['type']=='select')
                        <select style="width: 200px;margin-left: 10px" name="{{$f['name']}}"
                                id="{{isset($f['id']) ? $f['id'] : ''}}" class="form-control">
                            @foreach($f['option'] as $k=>$v)
                                <option value="{{$k}}">{{$v}}</option>
                            @endforeach
                        </select>
                    @endif
                @endforeach

                <div class="group-btn">
                    <button class="btn btn-primary" type="button" id="btnSearch">Tìm kiếm</button>
                </div>
            @endif

        </form>
        <div class="group-btn">
            @if(isset($checkRole['add']) && Topica::can($checkRole['add']))
                @if(isset($adding))
                    <a href="@if( isset($adding) ) {{ route($adding) }} @else {{route('emp_rent.create')}} @endif"
                       class="btn btn-primary">
                        <i class="fa fa-plus"></i>
                    </a>
                @endif
            @endif
        </div>
    </div>

    <div class="btn-group pull-right">
        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false" style="margin-bottom: 10px">
            Chọn cột hiển thị
        </button>
        <div class="dropdown-menu dropdown-menu-right"
             style="max-height: 500px;overflow-x: scroll;top:100px;width: 700px;padding: 10px;">
            <div style="border-bottom: 1px solid #ddd;padding-bottom: 10px">Chọn trường hiển
                thị:
                <span
                        class="btnChooseTable btnChooseTableChecked"
                        id="btnChooseAll">Chọn tất cả</span> | <span class="btnChooseTable"
                                                                     id="btnCustom">Tùy chỉnh</span>
            </div>
            <div id="fillHtml">

            </div>
        </div>
    </div>
    <div class="mT-10 form-group" id="lstTable">
    </div>

</div>
<style>

</style>
@section('script_inline')
    <script>
        @if (session()->has('errors'))
        makeAlert('', "{!! session()->get('errors') !!}", "errors");
        @endif
        @if (session()->has('success'))
        makeAlert("", "{!! session()->get('success') !!}", 'success');
        @endif

        $(document).on('click', '#form-delete', function (e) {
            if (!confirm("Xác nhận dừng làm việc với nhân sự này ?")) {
                e.preventDefault();
            }
        });

        $(document).on('click', '#form-restore', function (e) {
            if (!confirm("Tiếp tục làm việc với nhân sự này ?")) {
                e.preventDefault();
            }
        });


        $(document).on('click', '#form-active', function (e) {
            if (!confirm("Bạn có chắc chắn muốn mở khóa nhân viên này?")) {
                e.preventDefault();
            }
        });

        $('.dropdown-item.choose_col').click(function (e) {
            e.stopPropagation();
            statusCheckbox = $(this).find('input[type=checkbox]').prop('checked');
            if (e.target.tagName != 'INPUT') {
                checkboxIsDisabled = $(this).find('input[type=checkbox]').prop('disabled');
                if (!checkboxIsDisabled) {
                    if (statusCheckbox) {
                        $(this).find('input[type=checkbox]').prop('checked', false);
                        $(this).find('input[type=checkbox]').data('visable', false);
                    } else {
                        $(this).find('input[type=checkbox]').prop('checked', true);
                        $(this).find('input[type=checkbox]').data('visable', true);
                    }
                }
            } else {
                $(this).find('input[type=checkbox]').data('visable', statusCheckbox);
            }
            renderTable();
        });
        var listTable = {

            init: function (settings) {
                listTable.config = {
                    isHtml: true,
                    page: 1,
                    url: '',
                    opacity: 1,
                    isSearch: false,
                    listData: []
                }
                ;

                // Allow overriding the default config
                $.extend(listTable.config, settings);

                this.getTable();
                this.pagination();
                this.search();
                this.config.opacity = 0.5;
                this.showTable();


            },
            loadFadeIn: function () {
                $('#loader').removeClass('fadeOut').addClass('fadeIn').css('opacity', this.config.opacity);
            },
            loadFadeOut: function () {
                setTimeout(function () {
                    $('#loader').removeClass('fadeIn').addClass('fadeOut')
                }, 500);

            },
            getTable: function () {
                me = this;
                if (me.config.url !== '') {
                    me.loadFadeIn();
                    $.ajax({
                        url: me.config.url,
                        data: $('#formSearch').serialize() + "&isHtml=" + me.config.isHtml + "&page=" + me.config.page,
                        dataType: 'json',
                        method: 'GET',
                        success: function (r) {
                            $('#lstTable').html(r);
                            me.renderChooseTable();
                            me.loadFadeOut();
                            $('#lstTable table  tbody tr').each(function (k, v) {
                                if ($(v).find('td').data('delete')) {
                                    $(v).addClass('delete')
                                }
                            });
                            me.getAjaxModal();
                        },
                        error: function (result, textStatus) {
                            console.log(textStatus);
                        }
                    });
                }

            },
            renderChooseTable: function () {
                me = this;
                var listData = [];
                var i = 0;

                $('#lstTable table thead tr th').each(function () {
                    if ($(this).attr('id') != undefined && $(this).html() != '') {
                        listData[i] = [];
                        listData[i]['name'] = $(this).html();
                        listData[i]['key'] = $(this).attr('id');
                        listData[i]['display'] = $(this).data('display');
                        i++;
                    }
                });
                if (me.config.listData.length == 0) {
                    me.config.listData = listData;
                }
                html = "";
                $.each(me.config.listData, function (k, v) {
                    html += '<a class="dropdown-item choose_col"\n' +
                        '                    style="width:48%;display: inline-block;word-break: break-all;white-space: normal;"\n' +
                        '                    href="javascript:void(0)">';

                    if (v['display']) {
                        html += '<input checked class="form-check-input"' +
                            '                    data-visable="' + v['display'] + '"' +
                            '                    value="' + v['key'] + '" type="checkbox"' +
                            '>';
                    } else {
                        html += '<input class="form-check-input"' +
                            '                    data-visable="' + v['display'] + '"' +
                            '                    value="' + v['key'] + '" type="checkbox"' +
                            '>';
                    }

                    html += '<span>' + v['name'] + '</span>' +
                        '                        </a>';
                    html += '</a>';
                });
                $('#fillHtml').html(html);
                me.displayCollum();

            },
            getAjaxModal: function () {
                $(document).find('a[data-view=ajax]').click(function (e) {
                    url = $(this).data('href');
                    phap_nhan = $(this).data('pn');
                    month_year = $(this).data('my');
                    employee_code = $(this).data('employee');
                    params = {phap_nhan: phap_nhan, month_year: month_year, employee_code: employee_code};
                    $.ajax({
                        url: url,
                        data: params,
                        dataType: 'json',
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (r) {
                            if (r.status == 1) {
                                $('#infoEmployee .modal-body').html(r.message);
                                $('#btnModel').trigger('click');
                            } else {
                                console.log(r)
                            }
                        },
                        error: function (result, textStatus) {
                            console.log(textStatus);
                        }
                    });
                });
            },
            displayCollum: function () {
                me = this;
                me.showTable();
                $('#btnChooseAll').click(function (e) {
                    e.stopPropagation();

                    if ($(this).hasClass('btnChooseTableChecked')) {
                        $('#fillHtml input[type=checkbox]').each(function () {
                            $(this).prop('checked', true);
                        });
                        // đóng tất cả các checkbox
                        $('#fillHtml input[type=checkbox]').each(function () {
                            $(this).prop('disabled', true);
                            $(this).data('isDisabled', true);
                        });
                    }


                    $('.btnChooseTable').removeClass('btnChooseTableChecked');
                    $('#btnCustom').addClass('btnChooseTableChecked');
                    me.showTable();
                });
                $('#btnCustom').click(function (e) {
                    e.stopPropagation();
                    if ($(this).hasClass('btnChooseTableChecked')) {
                        $('#fillHtml input[type=checkbox]').each(function () {
                            $(this).prop('checked', false);
                            var visibleCol = $(this).data('visable');
                            if (visibleCol) {
                                $(this).prop('checked', true);
                            }
                            // mở tất cả các checkbox
                            $('#fillHtml input[type=checkbox]').each(function () {
                                $(this).prop('disabled', false);
                                $(this).data('isDisabled', false);
                            });
                        });
                    }

                    $('.btnChooseTable').removeClass('btnChooseTableChecked');
                    $('#btnChooseAll').addClass('btnChooseTableChecked');
                    me.showTable();
                });

                $('.dropdown-item.choose_col').click(function (e) {
                    e.stopPropagation();
                    var target = $(this).find('input[type=checkbox]');
                    var statusCheckbox = target.prop('checked');
                    $.each(me.config.listData, function (k, v) {
                        if (v['key'] == target.val()) {
                            v['display'] = !statusCheckbox;
                        }
                    })
                    if (e.target.tagName != 'INPUT') {
                        checkboxIsDisabled = $(this).find('input[type=checkbox]').prop('disabled');
                        if (!checkboxIsDisabled) {
                            if (statusCheckbox) {
                                $(this).find('input[type=checkbox]').prop('checked', false);
                                $(this).find('input[type=checkbox]').data('visable', false);
                            } else {
                                $(this).find('input[type=checkbox]').prop('checked', true);
                                $(this).find('input[type=checkbox]').data('visable', true);
                            }
                        }
                    } else {
                        $(this).find('input[type=checkbox]').data('visable', statusCheckbox);
                    }
                    me.showTable();
                });

            },
            showTable: function () {
                $(document).find('#fillHtml input[type=checkbox]').each(function () {
                    $('th[data-id=' + $(this).val() + ']').hide();
                    $('td[data-id=' + $(this).val() + ']').hide();
                });
                $(document).find('#fillHtml input[type=checkbox]:checked').each(function () {
                    $('th[data-id=' + $(this).val() + ']').show();
                    $('td[data-id=' + $(this).val() + ']').show();
                });
            },
            pagination: function () {
                me = this;
                $(document).on('click', '.page-item a', function (e) {
                    me.config.page = $(this).data('page');
                    me.getTable();
                });
            },
            search: function () {
                me = this;
                $('#btnSearch').click(function () {
                    me.config.page = 1;
                    if (me.checkFormIsNotEmpty()) {
                        me.config.isSearch = true;
                        me.getTable();
                    }
                });
                $('#btnClear').click(function (e) {
                    me.config.page = 1;

                    if (me.checkFormIsNotEmpty()) {
                        $(':input', '#formSearch')
                            .not(':button, :submit, :reset, :hidden')
                            .val('')
                            .prop('checked', false)
                            .prop('selected', false);
                        if (me.config.isSearch) {
                            me.getTable();
                        }
                        me.config.isSearch = false;
                    }
                });
                $('#formSearch').submit(function (e) {
                    e.preventDefault();
                });
                $('#formSearch').keypress(function (e) {
                    me.config.page = 1;
                    if (e.which == 13) {
                        if (me.checkFormIsNotEmpty()) {
                            me.config.isSearch = true;
                            me.getTable();
                        }
                    }
                });
            },
            checkFormIsNotEmpty: function () {
                var dataInput = $('#formSearch').serializeArray();

                var check = true;

                $.each(dataInput, function (k, v) {
                    if (v.name == 'search' && v.value == "") {
                        check = check && false;
                    }
                });

                return true;
                return check;
            }
        };
    </script>

@endsection