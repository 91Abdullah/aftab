@extends('layouts.concept')

@section('page-title', 'Report')
@section('page-desc', 'Call Detail Records')

@section('breadcrum-title', 'Index')

@section('content')

    <div class="card">
        <div class="card-body">

            @if (session('status'))
                <div class="alert alert-info">
                    {{ session('status') }}
                </div>
            @endif

            <div class="table-responsive">
                <table id="myTable" class="table table-striped first">
                    <thead>
                    <tr>
                        <th>Source</th>
                        <th>Dest</th>
                        <th>Start</th>
                        <th>Answer</th>
                        <th>End</th>
                        <th>Duration</th>
                        <th>Disposition</th>
                        <th>Recording</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($cdrs as $cdr)
                        <tr>
                            <td>{{ $cdr->src }}</td>
                            <td>{{ $cdr->dst }}</td>
                            <td>{{ $cdr->start }}</td>
                            <td>{{ $cdr->answer }}</td>
                            <td>{{ $cdr->end }}</td>
                            <td>{{ $cdr->billsec }}</td>
                            <td>{{ $cdr->disposition }}</td>
                            <td>{{ $cdr->recordingfile }}</td>
                        </tr>
                    @empty
                        <tr>
                            <th colspan="8">No records in database.</th>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

