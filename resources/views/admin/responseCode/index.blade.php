@extends('layouts.concept')

@section('page-title', 'Response Codes')
@section('page-desc', 'Add response codes to be added after call hangup')

@section('breadcrum-title', 'Index')

@push('styles')

    <link rel="stylesheet" href="{{ asset('DataTables/datatables.min.css') }}">

@endpush

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
                        <th>ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th>Last Updated</th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($codes as $responseCode)
                        <tr>
                            <td>{{ $responseCode->id }}</td>
                            <td>{{ $responseCode->code }}</td>
                            <td>{{ $responseCode->name }}</td>
                            <td>{{ $responseCode->desc }}</td>
                            <td>{{ $responseCode->created_at->diffForHumans() }}</td>
                            <td>{{ $responseCode->updated_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ route('responseCode.edit', ['responseCode' => $responseCode->id]) }}" class="btn btn-primary"><i class="far fa-edit"></i> Edit</a>
                            </td>
                            <td>
                                {!! Form::open(['route' => ['responseCode.destroy', $responseCode->id], 'method' => 'DELETE', 'class' => 'form-inline']) !!}
                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete</button>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <th colspan="8">No codes in database.</th>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script type="text/javascript" src="{{ asset('DataTables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#myTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf'
                ]
            });
        });
    </script>
@endpush

