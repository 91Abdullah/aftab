@extends('layouts.concept')

@section('page-title', 'Response Codes')
@section('page-desc', 'Create a new response code.')

@section('breadcrum-title', 'Response Codes')

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


            {!! Form::model($responseCode, ['route' => ['responseCode.update', $responseCode->id], 'method' => 'PATCH']) !!}

            <div class="form-group">
                {!! Form::label('name', null, ['class' => 'col-form-label']) !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'autocomplete' => 'name', 'required' => true, 'autofocus' => 'true']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('code', null, ['class' => 'col-form-label']) !!}
                {!! Form::text('code', null, ['class' => 'form-control', 'required' => true]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('desc', null, ['class' => 'col-form-label']) !!}
                {!! Form::textarea('desc', null, ['class' => 'form-control', 'rows' => 2]) !!}
            </div>


            {!! Form::button('<i class="far fa-edit"></i> Update', ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>
    </div>

@endsection

