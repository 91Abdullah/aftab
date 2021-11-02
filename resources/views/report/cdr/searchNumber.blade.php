@extends('layouts.concept')

@section('page-title', 'Report')
@section('page-desc', 'Call Detail Records')

@section('breadcrum-title', 'Search Number')

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
            <form method="get" action="{{ action('Report\CdrController@getSearchNumber') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="number">Number</label>
                        <input value="{{ request('number') ?? '' }}" name="number" id="number" type="text" class="form-control form-control-lg">
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search-plus"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    @isset($data)
        <div class="card">
            <div class="card-body">
                <div class="m-b-10 float-right">
                    <form method="post" action="{{ action('Report\CdrController@getDownloadSearchNumberReport') }}">
                        @csrf
                        <input name="number" type="hidden" value="{{ request('number') }}">
                        <button type="submit" class="btn btn-secondary"><i class="fa fa-cloud-download-alt"></i> Download Report</button>
                    </form>
                </div>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Destination</th>
                        <th>Agent</th>
                        <th>Start time</th>
                        <th>End time</th>
                        <th>Duration</th>
                        <th>Call status</th>
                        <th>Code</th>
                        <th>Recording</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $d)
                        <tr>
                            <td>{{ $d->dst }}</td>
                            <td>{{ explode("-", explode("/", $d->channel)[1])[0] ?? null }}</td>
                            <td>{{ $d->start }}</td>
                            <td>{{ $d->end }}</td>
                            <td>{{ $d->billsec }}</td>
                            <td>{{ $d->disposition }}</td>
                            <td>{{ $d->response_codes->first()->name ?? null }}</td>
                            <td>
                                @if(isset($d->recordingfile) && $d->disposition === "ANSWERED")
                                    <form method="post" action="{{ action('Report\CdrController@getFile') }}">
                                        @csrf
                                        <input type="hidden" value="{{ $d->recordingfile }}" name="file">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-cloud-download-alt"></i> Download
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="float-right">
                    {{ $data->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    @endisset

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
