@extends('layouts.concept')

@section('page-title', 'Upload CSV List')
@section('page-desc', 'Upload CSV list based on numbers')

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
                        <th>Number</th>
                        <th>Name</th>
                        <th>City</th>
                        <th>Parent List</th>
                        <th>Status</th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($lists as $list)
                        <tr>
                            <td>{{ $list->id }}</td>
                            <td>{{ $list->number }}</td>
                            <td>{{ $list->name }}</td>
                            <td>{{ $list->city }}</td>
                            <td>
                                <a href="{{ route('list.index', $list->parent()->first()->id) }}">
                                    {{ $list->parent()->first()->name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-{{ $list->status ? 'success' : 'danger' }}">
                                    {{ $list->status ? 'Called' : 'Uncalled' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('sublist.edit', $list->id) }}" class="btn btn-primary"><i class="far fa-edit"></i> Edit</a>
                            </td>
                            <td>
                                {!! Form::open(['route' => ['sublist.destroy', $list->id], 'method' => 'DELETE', 'class' => 'form-inline']) !!}
                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete</button>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <th colspan="8">No lists in database.</th>
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

