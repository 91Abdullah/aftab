@extends('layouts.concept')

@section('page-title', 'List Number')
@section('page-desc', 'Update list number')

@section('breadcrum-title', 'List Number')

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

            {!! Form::model($listNumber, ['route' => ['sublist.update', $listNumber->id], 'method' => 'patch']) !!}

            <div class="form-group">
                {!! Form::label('number', null, ['class' => 'col-form-label']) !!}
                {!! Form::text('number', null, ['class' => 'form-control', 'autocomplete' => 'number', 'required' => true, 'autofocus' => 'true']) !!}
            </div>


            {!! Form::button('<i class="far fa-edit"></i> Update', ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>
    </div>

@endsection

