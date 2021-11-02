@extends('layouts.concept')

@section('page-title', 'Report')
@section('page-desc', 'Call Detail Records')

@section('breadcrum-title', '')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css">
@endpush

@section('content')

    <div class="card">
        <div class="card-body">
            @isset($status)
                <div class="alert alert-danger">
                    {{ $status }}
                </div>
            @endisset
            <form method="post" action="{{ action('Report\CdrController@downloadReport') }}">
                @csrf
                <div class="form-group">
                    <label for="start">Select Date Range</label>
                    <div class="input-daterange input-group" id="datepicker">
                        <input required id="start" value="{{ old('start') }}" type="text" class="input-sm form-control" name="start" />
                        <span class="input-group-text">TO</span>
                        <input required id="end" value="{{ old('end') }}" type="text" class="input-sm form-control" name="end" />
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-cloud-download-alt"></i> Generate & Download
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.js"></script>
    <script>
        $(document).ready(function () {
            $('#datepicker').datepicker({
                format: "yyyy-mm-dd",
                todayBtn: "linked",
                clearBtn: true,
                keyboardNavigation: false,
                forceParse: false,
                todayHighlight: true,
                toggleActive: true,
                autoClose: true,
            });
        });
    </script>
@endpush
