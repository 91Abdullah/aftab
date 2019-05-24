@extends('layouts.concept')

@section('page-title', 'Users')
@section('page-desc', 'Reporting Users database')

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
                        <th>Email</th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <a href="{{ route('user.edit', ['id' => $user['id']]) }}" class="btn btn-primary"><i class="far fa-edit"></i> Edit</a>
                            </td>
                            <td>
                                {!! Form::open(['route' => ['user.destroy', $user->id], 'method' => 'DELETE', 'class' => 'form-inline']) !!}
                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete</button>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <th colspan="5">No users in database.</th>
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

