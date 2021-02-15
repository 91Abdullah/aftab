@extends('layouts.concept')

@section('page-title', 'Live call monitoring')
@section('page-desc', 'Listen, whisper and barge.')

@section('breadcrum-title', 'Live monitoring')

@section('content')
    <div data-auth-id="{{ Auth::user()->id }}" id="example"></div>
@endsection

@push('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
@endpush
