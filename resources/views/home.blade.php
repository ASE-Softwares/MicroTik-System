@extends('admin.sidenav')

@section('section-Head')
    Dashboard
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-2.css') }}">
    <script src="{{ asset('js/admin/admin-2.js') }}"></script>

    <script src="{{ asset('js/admin/Chart.js') }}"></script>
    <script scr="{{ asset('js/admin/chart-area-demo.js') }}"></script>
    <script src="{{ asset('js/admin/chart-pie-demo.js') }}"></script>
    
@endsection

@section('content')
<div class="row">

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Earnings (Today)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Ksh. {{$todayEarnings ?? ''}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Earnings (This Month)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Ksh. {{$thisMonthEarnings ?? ''}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Earnings (Monthly) Card  -->


    <!-- Earnings (Yearly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Earnings (Annually) This Year</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Ksh. {{$thisYearEarnings}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Earnings (Monthly) Card  -->


    <!-- New Users Card-->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            New Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>

    {{-- Charts of traffic --}}
    <div class="row">

        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Interfaces:</div>
                            {{-- @foreach (\App\Http\Controllers\AdminControllerinterfaces() as $interface )
                                <a class="dropdown-item" href="#">{{ $interface }}</a>
                            @endforeach --}}
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                        <canvas id="myAreaChart" width="457" height="400" class="chartjs-render-monitor" style="display: block; height: 320px; width: 366px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- LIve chart --}}
        {{-- <div class="col-xl-4 col-lg-5">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">Live Chart</h3>
                    </div>
                    <div class="panel-body">
                        <div id="flot4" style="padding: 0px; position: relative;"><canvas class="flot-base" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 340px; height: 250px;" width="425" height="312"></canvas><div class="flot-text" style="position: absolute; inset: 0px; font-size: smaller; color: rgb(84, 84, 84);"><div class="flot-y-axis flot-y1-axis yAxis y1Axis" style="position: absolute; inset: 0px; display: block;"><div class="flot-tick-label tickLabel" style="position: absolute; top: 235px; left: 12px; text-align: right;">0</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 188px; left: 6px; text-align: right;">20</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 141px; left: 6px; text-align: right;">40</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 94px; left: 6px; text-align: right;">60</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 47px; left: 6px; text-align: right;">80</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 1px; left: 0px; text-align: right;">100</div></div></div><canvas class="flot-overlay" width="425" height="312" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 340px; height: 250px;"></canvas></div>
                    </div>
                </div>
        </div> --}}

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Traffic</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Interfaces:</div>
                            {{-- @foreach (\App\Http\Controllers\AdminControllerinterfaces() as $interface )
                                <a class="dropdown-item" href="#">{{ $interface }}</a>
                            @endforeach --}}
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                        <canvas id="myPieChart" width="303" height="306" class="chartjs-render-monitor" style="display: block; height: 245px; width: 243px;"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Basic
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Regular
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Premium
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- End of chart  --}}

</div>

@endsection
