@extends('layouts.master')
@section('title') @lang('translation.E_Charts') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Charts @endslot
@slot('title') Echarts @endslot
@endcomponent
<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Line Chart</h4>
            </div>
            <div class="card-body">
                <div id="line-chart" data-colors='["--bs-primary","--bs-success"]' class="e-charts"></div>
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Mix Line-Bar</h4>
            </div>
            <div class="carzd-body">
                <div id="mix-line-bar" data-colors='["--bs-primary", "--bs-danger", "--bs-success"]' class="e-charts"></div>
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Doughnut Chart</h4>
            </div>
            <div class="card-body">
                <div id="doughnut-chart" data-colors='["--bs-primary", "--bs-danger", "--bs-info","--bs-warning","--bs-success"]' class="e-charts"></div>
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Pie Chart</h4>
            </div>
            <div class="card-body">
                <div id="pie-chart" data-colors='["--bs-success", "--bs-danger", "--bs-info","--bs-warning","--bs-primary"]' class="e-charts"></div>
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Scatter Chart</h4>
            </div>
            <div class="card-body">
                <div id="scatter-chart" data-colors='["--bs-success"]' class="e-charts"></div>
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Bubble Chart</h4>
            </div>
            <div class="card-body">
                <div id="bubble-chart" data-colors='["--bs-primary", "--bs-primary", "--bs-primary", "--bs-success", "--bs-success",  "--bs-success"]' class="e-charts"></div>
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Candlestick Chart</h4>
            </div>
            <div class="card-body">
                <div id="candlestick-chart" data-colors='["--bs-primary","--bs-success"]' class="e-charts"></div>
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Gauge Chart</h4>
            </div>
            <div class="card-body">
                <div id="gauge-chart" data-colors='["--bs-success", "--bs-primary","--bs-danger"]' class="e-charts"></div>
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
@endsection
@section('script')
<script src="{{ URL::asset('assets/libs/echarts/echarts.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/pages/allchart.js') }}"></script>
<script src="{{ URL::asset('assets/js/pages/echarts.init.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
