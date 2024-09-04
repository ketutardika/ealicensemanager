@extends('layouts.master')
@section('title') @lang('translation.Apex_Charts') @endsection
@section('content')

@component('components.breadcrumb')
@slot('li_1') Charts @endslot
@slot('title') Apexcharts @endslot
@endcomponent
<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Line with Data Labels</h4>
            </div>
            <div class="card-body">

                <div id="line_chart_datalabel" data-colors='["--bs-primary", "--bs-success"]' class="apex-charts" dir="ltr"></div>
            </div>
        </div><!--end card-->
    </div>

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Dashed Line</h4>
            </div>
            <div class="card-body">

                <div id="line_chart_dashed" data-colors='["--bs-primary", "--bs-danger", "--bs-success"]' class="apex-charts" dir="ltr"></div>
            </div>
        </div><!--end card-->
    </div>
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Spline Area</h4>
            </div>
            <div class="card-body">
                <div id="spline_area" data-colors='["--bs-primary", "--bs-success"]' class="apex-charts" dir="ltr"></div>
            </div>
        </div><!--end card-->
    </div>

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Column Chart</h4>
            </div>
            <div class="card-body">
                <div id="column_chart" data-colors='["--bs-success", "--bs-primary", "--bs-danger"]' class="apex-charts" dir="ltr"></div>
            </div>
        </div><!--end card-->
    </div>
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Column with Data Labels</h4>
            </div>
            <div class="card-body">
                <div id="column_chart_datalabel" data-colors='["--bs-primary"]' class="apex-charts" dir="ltr"></div>
            </div>
        </div><!--end card-->
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Bar Chart</h4>
            </div>
            <div class="card-body">
                <div id="bar_chart" data-colors='["--bs-success"]' class="apex-charts" dir="ltr"></div>
            </div>
        </div><!--end card-->
    </div>
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Line, Column & Area Chart</h4>
            </div>
            <div class="card-body">
                <div id="mixed_chart" data-colors='["--bs-danger", "--bs-primary", "--bs-success"]' class="apex-charts" dir="ltr"></div>
            </div>
        </div><!--end card-->
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Radial Chart</h4>
            </div>
            <div class="card-body">
                <div id="radial_chart" data-colors='["--bs-primary", "--bs-danger", "--bs-success","--bs-warning"]' class="apex-charts" dir="ltr"></div>
            </div>
        </div><!--end card-->

    </div>
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Pie Chart</h4>
            </div>
            <div class="card-body">

                <div id="pie_chart" data-colors='["--bs-primary", "--bs-danger", "--bs-success","--bs-warning","--bs-info"]' class="apex-charts" dir="ltr"></div>
            </div>
        </div>
    </div>
    <!-- end col -->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Donut Chart</h4>
            </div>
            <div class="card-body">
                <div id="donut_chart" data-colors='["--bs-primary", "--bs-danger", "--bs-success","--bs-warning","--bs-info"]' class="apex-charts"  dir="ltr"></div>
            </div>
        </div>
    </div>
    <!-- end col -->
</div>
<!-- end row -->

@endsection
@section('script')
<script src="{{ URL::asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/pages/apexcharts.init.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
