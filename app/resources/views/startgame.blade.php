@extends('layouts.main')

@section('content')
   
    
    <form action="/api/game" method="post">
        @csrf
        По умолчанию, это поле пустое <br>
        <input type="text" name='string-game-field' placeholder="данные для игрового поля">
        <input type="submit" value="создать игру">
    </form>
@endsection