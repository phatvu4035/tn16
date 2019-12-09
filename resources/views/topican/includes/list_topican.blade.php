<h3 class="c-grey-900 mT-10 mB-30">@if(isset($title)){{$title}}@endif</h3>

<div class="mT-30">
    <div class="group-search">
    	
        <form class="frm-search" action="{{route('topican.index')}}" id="formSearch">
            @if(isset($filter) && $filter)
                @foreach($filter as $f)
                    @if($f['type']=='text')
                        <input type="text" class="form-control form-control-alter" name="{{$f['name']}}"
                               value="@if(isset($getData[$f['name']])&&$getData[$f['name']]){{$getData[$f['name']]}}@endif"
                               placeholder="@if( isset( $f['placeholder'] ) ) {{ $f['placeholder'] }} @endif"
                        />
                    @endif
                @endforeach

                <div class="group-btn">
                    <button class="btn btn-info" type="button" id="btnClear">Clear</button>
                </div>

                <div class="group-btn group-btn-alter">
                    <button class="btn btn-primary" type="button" id="btnSearch">Tìm kiếm</button>
                </div>
            @endif

        </form>

        <div class="group-btn">
            <a href="{{route('topican.create')}}" class="btn btn-primary">
                <i class="fa fa-plus"></i>
            </a>
        </div>

    </div>
    <div class="mT-10" id="lstTable">

    </div>

</div>


@section('script_inline')
    <script>
        var listTable = {

            init: function (settings) {
                listTable.config = {
                    isHtml: true,
                    page: 1,
                    url: '',
                    opacity: 1
                };

                // Allow overriding the default config
                $.extend(listTable.config, settings);

                this.getTable();
                this.pagination();
                this.search();
                this.config.opacity = 0.5;
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
                            me.loadFadeOut();
                        },
                        error: function (result, textStatus) {
                            console.log(textStatus);
                        }
                    });
                }

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
                console.log(me.config.page);
                $('#btnSearch').click(function () {
                    me.config.page = 1;
                    me.getTable();
                });
                $('#btnClear').click(function (e) {
                    me.config.page = 1;
                    $(':input', '#formSearch')
                        .not(':button, :submit, :reset, :hidden')
                        .val('')
                        .prop('checked', false)
                        .prop('selected', false);
                    me.getTable();
                });
                $('#formSearch').submit(function (e) {
                    e.preventDefault();
                });
                $('#formSearch').keypress(function (e) {
                    me.config.page = 1;
                    if (e.which == 13) {
                        me.getTable();
                    }
                });
            }
        };
    </script>

@endsection