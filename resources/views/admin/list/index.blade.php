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
                        <th>Name</th>
                        <th>File Name</th>
                        <th>Active</th>
                        <th>Path</th>
                        <th>Created</th>
                        <th>Last Updated</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($lists as $list)
                        <tr>
                            <td>{{ $list->id }}</td>
                            <td>{{ $list->name }}</td>
                            <td>{{ $list->file_name }}</td>
                            <td>
                                <span class="badge badge-{{ $list->active ? 'success' : 'danger' }}">
                                    {{ $list->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $list->file_path }}</td>
                            <td>{{ $list->created_at->diffForHumans() }}</td>
                            <td>{{ $list->updated_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ route('list.edit', ['list' => $list->id]) }}" class="btn btn-primary"><i class="far fa-edit"></i> Edit</a>
                            </td>
                            <td>
                                {!! Form::open(['route' => ['list.destroy', $list->id], 'method' => 'DELETE', 'class' => 'form-inline']) !!}
                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete</button>
                                {!! Form::close() !!}
                            </td>
                            <td>
                                <a href="{{ route('sublist.index', ['parent' => $list->id]) }}" class="btn btn-success"><i class="fas fa-clipboard-list"></i> Show</a>
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

