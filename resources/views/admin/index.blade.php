@extends('layouts.concept')

@section('page-title', 'Dashboard')
@section('page-desc', 'Daily stats')

@section('breadcrum-title', 'Index')

@section('content')

    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-muted">Total Calls <small>(monthly)</small></h5>
                    <div class="metric-value d-inline-block">
                        <h1 class="mb-1">{{ $data['calls'] }}</h1>
                    </div>
                    <div class="metric-label d-inline-block float-right text-{{ $data['sign'] == '+' ? 'success' : 'danger' }} font-weight-bold">
                        <span><i class="fa fa-fw fa-arrow-{{ $data['sign'] == '+' ? 'up' : 'down' }}"></i></span><span>{{ $data['increase'] }}%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-muted">Total Answered <small>(monthly)</small></h5>
                    <div class="metric-value d-inline-block">
                        <h1 class="mb-1">{{ $data['answered_calls'] }}</h1>
                    </div>
                    <div class="metric-label d-inline-block float-right text-{{ $data['answered_sign'] == '+' ? 'success' : 'danger' }} font-weight-bold">
                        <span><i class="fa fa-fw fa-arrow-{{ $data['answered_sign'] == '+' ? 'up' : 'down' }}"></i></span><span>{{ $data['answered_increase'] }}%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-muted">Highest Duration <small>(secs)</small></h5>
                    <div class="metric-value d-inline-block">
                        <h1 class="mb-1">{{ $data['max_calls'] }}</h1>
                    </div>
                    <div class="metric-label d-inline-block float-right text-{{ $data['max_sign'] == '+' ? 'success' : 'danger' }} font-weight-bold">
                        <span><i class="fa fa-fw fa-arrow-{{ $data['max_sign'] == '+' ? 'up' : 'down' }}"></i></span><span>{{ $data['max_increase'] }}%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-muted">Avg Durations <small>(secs)</small></h5>
                    <div class="metric-value d-inline-block">
                        <h1 class="mb-1">{{ $data['avg_calls'] }}</h1>
                    </div>
                    <div class="metric-label d-inline-block float-right text-{{ $data['avg_sign'] == '+' ? 'success' : 'danger' }} font-weight-bold">
                        <span><i class="fa fa-fw fa-arrow-{{ $data['avg_sign'] == '+' ? 'up' : 'down' }}"></i></span><span>{{ $data['avg_increase'] }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
