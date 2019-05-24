@extends('layouts.concept')

@section('page-title', 'Users')
@section('page-desc', 'Create new user')

@section('breadcrum-title', 'Create')

@section('content')

    <div class="card">

        <div class="card-header">
            <div class="card-header-title">Add Agent</div>
        </div>

        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="list-unstyled arrow">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {!! Form::open(['route' => 'user.store', 'method' => 'post']) !!}
                <div class="form-group">
                    {!! Form::label('name', 'Name', ['class' => 'col-form-label']) !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'autocomplete' => 'name', 'required' => 'true', 'autofocus' => 'true']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('email', 'Email address', ['class' => 'col-form-label']) !!}
                    {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'name@example.com', 'autocomplete' => 'email', 'required' => 'true']) !!}
                    <small class="form-text text-muted">We'll never share your email with anyone else.</small>
                </div>
                <div class="form-group">
                    {!! Form::label('password', 'Password', ['class' => 'col-form-label']) !!}
                    {!! Form::password('password', ['class' => 'form-control', 'autocomplete' => 'new-password', 'require' => 'true']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('password_confirmation', 'Confirm Password', ['class' => 'col-form-label']) !!}
                    {!! Form::password('password_confirmation', ['class' => 'form-control', 'autocomplete' => 'new-password', 'required' => 'true']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('agent_id', 'Agent ID', ['class' => 'col-form-label']) !!}
                    {!! Form::text('agent_id', null, ['class' => 'form-control']) !!}
                    <small class="form-text text-muted">Type agent username to be used as their ID.</small>
                    {!! Form::button('<i class="fas fa-check"></i> Validate', ['class' => 'btn btn-rounded btn-success', 'id' => 'validate_agent']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('agent_password', 'Agent Password', ['class' => 'col-form-label']) !!}
                    {!! Form::text('agent_password', null, ['class' => 'form-control']) !!}
                    <small class="form-text text-muted">Type agent password to be used as their agent password.</small>
                </div>
                {!! Form::button('<i class="far fa-edit"></i> Create', ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>
    </div>

    <div class="card">

        <div class="card-header">
            <div class="card-header-title">Add Reporting User</div>
        </div>

        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="list-unstyled arrow">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {!! Form::open(['route' => 'user.store', 'method' => 'post']) !!}
                {!! Form::hidden('reporter', true) !!}
            <div class="form-group">
                {!! Form::label('name', 'Name', ['class' => 'col-form-label']) !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'autocomplete' => 'name', 'required' => 'true', 'autofocus' => 'true']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('email', 'Email address', ['class' => 'col-form-label']) !!}
                {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'name@example.com', 'autocomplete' => 'email', 'required' => 'true']) !!}
                <small class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
                {!! Form::label('password', 'Password', ['class' => 'col-form-label']) !!}
                {!! Form::password('password', ['class' => 'form-control', 'autocomplete' => 'new-password', 'require' => 'true']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('password_confirmation', 'Confirm Password', ['class' => 'col-form-label']) !!}
                {!! Form::password('password_confirmation', ['class' => 'form-control', 'autocomplete' => 'new-password', 'required' => 'true']) !!}
            </div>
            {!! Form::button('<i class="far fa-edit"></i> Create', ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let url = "{!! route('validate_agent') !!}";
            let validate = document.getElementById('validate_agent');
            validate.onclick = function (event) {
                if(document.getElementById('agent_id').value.length === 0) {
                    return;
                }
                axios.get(url, {
                    params: {
                        agent_id: document.getElementById('agent_id').value
                    }
                })
                    .then(function (response) {
                        Swal.fire("success", "Agent ID not found in database. " + response.data);
                        document.getElementById('agent_id').classList.add('is-valid');
                    })
                    .catch(function (error) {
                        console.log(error);
                        Swal.fire("error", "Choose a different ID. "  + error.response.data);
                        document.getElementById('agent_id').classList.add('is-invalid');
                    })
            };
        });
    </script>
@endpush

