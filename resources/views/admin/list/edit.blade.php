@extends('layouts.concept')

@section('page-title', 'Upload List')
@section('page-desc', 'Upload list of numbers.')

@section('breadcrum-title', 'Upload List')

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

            {!! Form::model($list, ['route' => ['list.update', $list->id], 'method' => 'patch']) !!}

            <div class="form-group">
                {!! Form::label('name', null, ['class' => 'col-form-label']) !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'autocomplete' => 'name', 'required' => true, 'autofocus' => 'true']) !!}
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

            {!! Form::button('<i class="far fa-edit"></i> Update', ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>
    </div>

@endsection

