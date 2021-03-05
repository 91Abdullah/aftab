@extends('layouts.concept')

@section('page-title', 'Settings')
@section('page-desc', 'Update settings')

@section('breadcrum-title', 'Settings')

@section('content')

    <div class="card">
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

                @if(session('failures'))
                    <div class="alert alert-danger">
                        <ul class="list-unstyled arrow">
                            @foreach(session('failures') as $failure)
                            <li>Row: {{ $failure->row() }}</li>
                            <li>Attribute: {{ $failure->attribute() }}</li>
                            <li>
                                <ul class="list-unstyled">
                                    @foreach($failure->errors() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </li>
                            <li>
                                <ul class="list un">
                                    @foreach($failure->values() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </li>
                            @endforeach
                        </ul> 
                    </div>
                @endif

            {!! Form::open(['route' => 'list.store', 'method' => 'post', 'files' => true]) !!}

                <div class="form-group">
                    {!! Form::label('name', null, ['class' => 'col-form-label']) !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'autocomplete' => 'name', 'required' => true, 'autofocus' => 'true']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('upload_file', null, ['class' => 'col-form-label']) !!}
                    {!! Form::file('upload_file', ['class' => 'form-control', 'required' => true]) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('active', null, ['class' => 'col-form-label']) !!}
                    <div class="switch-button switch-button-success">
                        {!! Form::checkbox('active', null, false, ['class' => 'form-control']) !!}
                        <span>
                            <label for="active"></label>
                        </span>
                    </div>
                </div>

            {!! Form::button('<i class="far fa-edit"></i> Create', ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>
    </div>

@endsection

