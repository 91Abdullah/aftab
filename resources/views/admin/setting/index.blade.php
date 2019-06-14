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

            {!! Form::open(['route' => 'setting.update', 'method' => 'patch']) !!}
            @foreach($settings as $key => $setting)

                <div class="form-group">
                    {!! Form::label($setting->key, $setting->key, ['class' => 'col-form-label']) !!}
                    {!! Form::text($setting->key, $setting->value, ['class' => 'form-control', 'autocomplete' => $setting->key, 'autofocus' => 'true']) !!}
                </div>

            @endforeach
            {!! Form::button('<i class="far fa-edit"></i> Update', ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>
    </div>

@endsection

