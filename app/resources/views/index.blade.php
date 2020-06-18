@extends('layouts.main')

@section('content')
    logout
    
    <form>
        @csrf
        <input type="text">
        <input type="button" value="создать игру">
    </form>
@endsection