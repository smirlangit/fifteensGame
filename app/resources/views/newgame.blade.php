@extends('layouts.main')

@section('content')
{{-- !!!ВНИМАНИЕ!!! фронтенд часть не "причесывалась" и писалась чтобы предоставить интерфейс, поэтому стили и js не разбиты на отдельные файлы --}}

<style>
    .square {
        width:50px; 
        height: 50px; 
        text-align: center;
        cursor: arrow;
    }
    .filled{
        background-color: lightgray;
    }
    .empty{
        background-color: white;
    }
    
</style>


    <div class="row">
        
    
    <table border = 1 style="margin: 0 auto;">

      <tbody >

        @foreach($fields as $row)      
            <tr>
              @foreach($row as $field)
              <td class="square {{ ($field !== 0) ? "filled":"empty" }}" data="{{ $field }}"> 
                  @if($field !== 0 ) {{ $field }} @endif
              </td>
              @endforeach

            </tr>

         @endforeach

      </tbody>
     
    </table>
        </div>
    
    <div class="row" style="margin-top: 50px">
        <form style="margin: 0 auto;" action="/api/game/{{$gameID}}/solve" method="POST"> 
            @csrf
            <input name="string-of-moves"  value="0" type="text"><br>
            <input type="submit" value="проверить решение">
        </form>        
    </div>
    


@endsection



