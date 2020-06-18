<?php

namespace App\Http\Controllers;
use App\Services\GameService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller {
    
    
    
    public function __construct() {
        $this->middleware('auth');
    }


    /**
     * Создание игры
     * @param GameService $game (DI внедрение)
     * @param Request $request POST['string-game-field'] кстомное создание игрового поля, через строку
     */
    public function createNewGame(GameService $game, Request $request) {
        
        //валидация кастомного создания игры
        $stringFieldsValidator  = Validator::make($request->all(), [
            'string-game-field' => 'required|not_regex:/[^\d, ]/'
        ]);

         
        //если переданы параметры создания поля и у них нет ошибок то создаем кастомное поле
        $gameFieldString = null;
        if($request->has('string-game-field')){
            
            if($stringFieldsValidator->fails() === false){
                $gameFieldString = $request->post('string-game-field');
                
            } else {
                //return redirect()->back();
            }
            
            
           
        }

        
        //current userID
        $userID = Auth::id();
        //make game
        $game->newGame($userID, 100, $gameFieldString);
        //game id
        $gameID = $game->id;
        //game fields
        $fields = $game->fields;
        
    
        if($gameID == null || $fields == null){
            return redirect()->back();            
        }
             
        //view
        return view('newgame', compact(['gameID', 'fields']));        
       
    }
    

    /**
     * 
     * @param int $gameID 
     * @param Request $request POST['string-of-moves'] последовательность ходов разделенные запятой (формат: значения ячеек)
     * @param GameService $game (DI внедрение)
     */
    protected function checkSolve(int $gameID, Request $request, GameService $game) {

        $validator = Validator::make(
            [
             'string-of-moves'=>$request->post('string-of-moves'),
              'gameID'=>$gameID
            ], 
            [
              'string-of-moves'  => 'required|not_regex:/[^\d, ]/',
              'gameID' => 'required|required'
        ]);
        

        if($validator->fails()){            
            return redirect()->back();
        }
        
        //строка с ходами игрока
        $stringOfMoves= $request->post("string-of-moves");
        
        //проверка принадлежности user_id к айди игры
        $ownerisRight = $game->checkGameOwner(Auth::id(), $gameID);
        
        //solve check
        $checkResult = $game->checkSolve($stringOfMoves, $gameID);
                        
        //end game
        $game->endGame($checkResult, $gameID);
        
        
        //view answer
        return view('endgame', compact(['checkResult']));
        
    }
    
    
    
}
