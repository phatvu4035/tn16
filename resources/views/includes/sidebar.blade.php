<div class="sidebar-logo">
    <div class="peers ai-c fxw-nw">
        <div class="peer peer-greed">
            <a class="sidebar-link td-n" href="/">
                <div class="peers ai-c fxw-nw">
                    <div class="peer">
                        <div class="logo"><img src="{{URL::asset('assets/static/images/logo.png')}}" alt=""></div>
                    </div>
                    <div class="peer peer-greed">
                        <h5 class="lh-1 mB-0 logo-text">TN2018</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="peer">
            <div class="mobile-toggle sidebar-toggle"><a href="" class="td-n"><i class="ti-arrow-circle-left"></i></a>
            </div>
        </div>
    </div>
</div>
<ul class="sidebar-menu scrollable pos-r">
    <li class="nav-item mT-30">
        <a class="sidebar-link" href="/">
            <span class="icon-holder"><i class="far fa-user"></i></span>
            <span class="title">Thu nhập cá nhân</span>
        </a>
    </li>
    @if(Topica::can('index.order')||Topica::can('add.order'))
        <li class="nav-item dropdown">
            <a class="dropdown-toggle" href="javascript:void(0);">
            <span class="icon-holder">
                <i class="fab fa-wpforms"></i>
            </span>
                <span class="title">Bộ thanh toán</span>
                <span class="arrow"><i class="ti-angle-right"></i></span></a>
            <ul class="dropdown-menu">
                @if(Topica::can('index.order'))
                    <li><a class="sidebar-link" href="{{route('order.listOrders')}}">Danh sách</a></li>
                @endif
                @if(Topica::can('add.order'))
                    <li><a class="sidebar-link" href="{{route('order.create.salary')}}">Tạo bộ thanh toán lương</a></li>
                    <li><a class="sidebar-link" href="{{route('order.create')}}">Tạo bộ thanh toán</a></li>
                @endif
            </ul>
        </li>
    @endif
    @if(Topica::can('index.rent_employee'))
        <li class="nav-item dropdown">
            <a class="dropdown-toggle" href="javascript:void(0);">
            <span class="icon-holder">
                <i class="fas fa-users"></i>
            </span>
                <span class="title">Quản lý nhân sự</span>
                <span class="arrow"><i class="ti-angle-right"></i></span></a>
            <ul class="dropdown-menu">
                @if(Topica::can('index.rent_employee'))
                    <li><a class="sidebar-link" href="{{route('employees.index')}}">HR20</a></li>
                @endif
                @if(Topica::can('index.rent_employee'))
                    <li><a class="sidebar-link" href="{{route('emp_rent.index')}}">Nhân sự thuê khoán</a></li>
                @endif

                @if(Topica::can('Adminsitrator123'))
                    <li><a class="sidebar-link" href="{{route('employee.viewEmployee')}}">Xem chi tiết nhân sự</a></li>
                @endif
            </ul>
        </li>
    @endif
    @if(Topica::can('index.cross_check_status'))
        <li class="nav-item dropdown">
            <a class="dropdown-toggle" href="javascript:void(0);">
            <span class="icon-holder">
                <i class="far fa-check-square"></i>
            </span>
                <span class="title">Đối soát</span>
                <span class="arrow"><i class="ti-angle-right"></i></span></a>
            <ul class="dropdown-menu">
                <li><a class="sidebar-link" href="{{route('cross_check.listCrossCheck')}}">Đối soát tháng</a></li>
                <li><a class="sidebar-link" href="{{route('cross_check.listCrossCheckYear')}}">Đối soát năm</a></li>
            </ul>
        </li>
    @endif
    @if(Topica::can('export.401')||Topica::can('index.tn')||Topica::can('export.402')||Topica::can('export.403'))
        <li class="nav-item dropdown">
            <a class="dropdown-toggle" href="javascript:void(0);">
            <span class="icon-holder">
                <i class="far fa-file-excel" aria-hidden="true"></i>
            </span>
                <span class="title">Tra cứu dữ liệu</span>
                <span class="arrow"><i class="ti-angle-right"></i></span></a>
            <ul class="dropdown-menu">
                @if(Topica::can('index.tn'))
                    <li><a class="sidebar-link" href="{{route('summary.index')}}">Danh sách thu nhập</a></li>
                @endif
                @if(Topica::can('export.401'))
                    <li><a class="sidebar-link" href="{{ route('export.401') }}">Báo cáo 401</a></li>
                @endif
                @if(Topica::can('export.402'))
                    <li><a class="sidebar-link" href="{{ route('export.402') }}">Báo cáo 402</a>
                @endif
                @if(Topica::can('export.403'))
                    <li><a class="sidebar-link" href="{{ route('export.403') }}">Báo cáo 403</a></li>
                @endif
            </ul>
        </li>
    @endif
    @if (Topica::can("Bố đời"))
        <li class="nav-item dropdown">
            <a class="dropdown-toggle" href="javascript:void(0);">
            <span class="icon-holder">
                <i class="fa fa-cog" aria-hidden="true"></i>
            </span>
                <span class="title">Quản lý</span>
                <span class="arrow"><i class="ti-angle-right"></i></span></a>
            <ul class="dropdown-menu">
                @if(Topica::can('index.user'))
                    <li><a class="sidebar-link" href="{{ route('topican.index') }}">Danh sách thành viên</a></li>
                @endif
                @if(Topica::can('index.role'))
                    <li><a class="sidebar-link" href="{{route('roles.index')}}">Danh sách quyền</a></li>
                @endif
                <li><a class="sidebar-link" href="{{ route('type.index') }}">Loại chứng từ</a></li>
                <li><a class="sidebar-link" href="{{ route('sync.index') }}">Cập nhật thông tin</a></li>
            </ul>
        </li>
    @endif
</ul>