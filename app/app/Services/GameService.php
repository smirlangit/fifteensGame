<?php

namespace App\Services;

use App\Game;

class GameService {

    public $id = null;
    public $fields = null;
    //шаблон игрового поля
    protected $gameFieldsTemplate = [];
    //значение для пустой ячейки
    protected $emptyFieldValue = 0;

    /**
     * Создает игру с привзкой к айди игрока
     * @param int $userID
     * @param type $gameDifficult сложность игры (количество итераций для случайного перемешивания игрового поля)
     * @param string $stringGameField строковое создание игрового поля
     */
    public function newGame(int $userID, $gameDifficult = 100, $stringGameField = null) {

        //стартовые значения игрового поля
        $this->gameFieldsTemplate = $this->makeStartFileds();

        
        //создание игрового поля
        if($stringGameField != null){  
            //кастомное поле
            $customFields = $this->stringToGameFields($stringGameField);
            if($customFields){
                $this->fields = $customFields;
            } else {
                $this->fields = null;
                return false;
                
            }           
            
            
        } else {
            //перемешивание стартовых значений для генерации игрового поля
            $this->fields = $this->randomizeGameFields($this->gameFieldsTemplate, $gameDifficult);
        }
        
        
        //игровое поле в виде строки, для записи в базу
        $stringGameField = $this->gamefieldToString($this->fields);
            
        //game id
        $this->id = $this->makeGameID($userID, $stringGameField);
        

        
    }

    /**
     * Функция перемещает пустое поле в рандомном направлении, учитывая легальные ходы
     * @param array $gameFields
     * @param type $difficultLevel
     * @return array массив с рандомным игровым полем
     */
    protected function randomizeGameFields(array $gameFields, $difficultLevel = 100): array {


        //циклы рандомного перемещения пустой ячейки по полю
        for ($i = 0; $i < $difficultLevel; $i++) {
            //получение возможных ходов для пустого поля
            $legalMoves = $this->legalMoveFields($gameFields, $this->emptyFieldValue);

            //случайный выбор для следующего хода, из списка возможных ходов
            $nextmove = $legalMoves[random_int(0, count($legalMoves) - 1)];

            //смена пустой ячейки и нового хода местами
            $gameFields = $this->swapFieds($this->emptyFieldValue, $nextmove, $gameFields);
        }


        return $gameFields;
    }

    /**
     * Создает запись о новой игре в базе, отдает айди записи
     * @param type $userId айди залогиненого пользователя
     * @param string $stringGameField игровое поле в виде строки
     * @return int айди созданной игры
     */
    protected function makeGameID(int $userId, string $stringGameField): int {

        $game = new \App\Game;
        $game->user_id = $userId;
        $game->startgame = now();
        $game->gamefield = $stringGameField;
        $game->save();

        $id = $game->id;

        return $id;
    }
    
    /**
     * из массива поля в строку
     * @param array $gameField
     * @return type
     */
    protected function gamefieldToString(array $gameField) {
  
        $stringField = "";
        foreach($gameField as $value){
            $stringField .= implode(",", $value).","; 
        }
        $stringField = substr($stringField, 0, -1);
        
        return $stringField;
        
    }
    
    /**
     * конвертация строки в игровое поле
     * @param type $gameFieldString
     * @return mixed array|bool
     */
    protected function stringToGameFields($gameFieldString) {
        
        //создаем из него игровое поле в виде массива
        $gameFiled = explode(",", $gameFieldString);
       
        //принудительная конвертация строк в целые числа
        foreach($gameFiled as $key => $value){
            $gameFiled[$key] = intval($value);
        }
        //принудительная конвертация значений в целые чсила
        $gameFiled = $this->arrayConvertToInt($gameFiled);
        
        
        //получаем ширину поля
        $gameFieldWidth = sqrt(count($gameFiled));
        
        //проверка квадратности поля
        if(round($gameFieldWidth) < $gameFieldWidth){
            return false;
        }
        
        //проверка минимального размера поля
        if($gameFieldWidth < 2){            
            return false;
        }
        
        //делаем из линейного массива поля, квадратное
        $virtualGameField = array_chunk($gameFiled, $gameFieldWidth);
        
        return $virtualGameField;
    }



    /**
     * Создает стартовое игровое поле
     * @return array
     */
    protected function makeStartFileds(): array {

        $fieldsTemplate[0] = [1, 2, 3, 4];
        $fieldsTemplate[1] = [5, 6, 7, 8];
        $fieldsTemplate[2] = [9, 10, 11, 12];
        $fieldsTemplate[3] = [13, 14, 15, 0];
        
        return $fieldsTemplate;
    }

    /**
     * отдает возможные варианты ходов для пустой ячейки в ее текущей позиции
     * @param array $fields массив игрового поля
     * @param int $fieldValueForMove значение поля, для которого вычисляются варинаты ходов
     * @return array значения возможных ходов
     */
    protected function legalMoveFields(array $fields, $fieldValueForMove): array {
        $legalMoves = [];
        //находим текущую позицию пустой ячейки 
        foreach ($fields as $level => $row) {
            $emptyFieldPosition = array_search($fieldValueForMove, $row);

            //проверка доступности ходов с 4х сторон для позиции искомого поля
            if ($emptyFieldPosition !== false) {

                //с лева
                if (isset($row[$emptyFieldPosition - 1])) {
                    $legalMoves[] = $row[$emptyFieldPosition - 1];
                }
                //с права
                if (isset($row[$emptyFieldPosition + 1])) {
                    $legalMoves[] = $row[$emptyFieldPosition + 1];
                }
                //сверху
                if (isset($fields[$level - 1][$emptyFieldPosition])) {
                    $legalMoves[] = $fields[$level - 1][$emptyFieldPosition];
                }
                //снизу
                if (isset($fields[$level + 1][$emptyFieldPosition])) {
                    $legalMoves[] = $fields[$level + 1][$emptyFieldPosition];
                }

                break;
            }
        }

        return $legalMoves;
    }

    /**
     * Ищет значение поля в массиве поля игры, отдает строку и столбец в виде массива, если нашел
     * @param type $valueToFind значение искомого поля
     * @param type $fields массив текущего игрового поля
     * @return array ['level', 'position']
     */
    protected function findFieldPosition($valueToFind, $fields): array {

        $fieldPostionInGameMatrix = ['level' => null, 'position' => null];

        foreach ($fields as $level => $row) {

            $fieldPosition = array_search($valueToFind, $row);

            if ($fieldPosition !== false) {
                $fieldPostionInGameMatrix = ['level' => $level, 'position' => $fieldPosition];
                break;
            }
        }


        return $fieldPostionInGameMatrix;
    }

    /**
     * Смена местами двух ячеек. Смена реализована через поиск значения, чтобы не привзяваться к структуре игрового поля.
     * @param type $firstField
     * @param type $secondField
     */
    protected function swapFieds($firstField, $secondField, $gameFields) {
        //поиск позиций
        $firstPosition = $this->findFieldPosition($firstField, $gameFields);
        $seconPosition = $this->findFieldPosition($secondField, $gameFields);

        //смена местами
        $gameFields[$firstPosition['level']][$firstPosition['position']] = $secondField;
        $gameFields[$seconPosition['level']][$seconPosition['position']] = $firstField;

        //отдача нового массива
        return $gameFields;
    }
    
    /**
     * Проверка ходов игрока
     * Анализирует ходы игрока и сравнивает итоговое поле с шаблонным
     * @param string $stringOfMoves последовательность номеров полей для перемещения, разделенные запятой
     * @param int $gameID айди игры
     * @return boolean статус успешности пройденной игры
     */
    public function checkSolve(string $stringOfMoves, int $gameID) {
        /** @var \App\Game $game  */
        
        //извлекаем поле игры по айди
        $game = \App\Game::find($gameID);
        $gameFieldString = $game->gamefield;
        //проверка наличия записи поля игры
        if($gameFieldString === false){
            return false;
        }
        
        //конвертация строки в массив игрового поля
        $virtualGameField = $this->stringToGameFields($gameFieldString);
                         
        //разбиваем строку с ходами на массив
        $moves = explode(",", $stringOfMoves);
        //принудительная конвертация значений в целые чсила
        $moves = $this->arrayConvertToInt($moves);
        
        //перемещаем ходы в созданном виртуальном поле
        foreach ($moves as $move){
           $virtualGameField = $this->swapFieds($move, $this->emptyFieldValue, $virtualGameField);           
            
        }
        
        $checkField = $this->validateSolvedField($virtualGameField);
        return $checkField;
        
    }
    
    /**
     * Проверка правельной сборки игрового поля согласно правилам пятнашек
     * @param array $field
     * @return boolean
     */
    protected function validateSolvedField(array $fields) {
        $firstField = -1;
        foreach ($fields as $level){
            foreach ($level as $field){  

                $lastLevel = count($fields)-1;
                $lastPosition = count($level)-1;
                
                if($fields[$lastLevel][$lastPosition] === 0){                    
                    break;
                    return true;
                }
                
                if($firstField > $field ){
                    breake;
                    return false;
                }
            }
            
        }
        return true;
    }


    /**
     * конвертация строк в целые числа по всему массиву
     * @param array $array
     */
    protected function arrayConvertToInt(array $array){
        
        foreach($array as $key => $value){
            $array[$key] = intval($value);
        }
        
       return $array;
    }
    
    /**
     * Завершение игры с сохранением статуса выигрыша
     * @param type $winStatus
     * @param type $gameID
     */
    public function endGame($winStatus, $gameID){
        /** @var \App\Game $game  */
        $game = \App\Game::find($gameID);
        $game->win = $winStatus;
        $game->endgame = now();
        $game->save();
        
    }
    
    /**
     * Проверка прав на игру для текущего игрока
     * @param type $userID
     * @param type $gameID
     * @return boolean
     */
    public function checkGameOwner($userID, $gameID){
         /** @var \App\Game $game  */
        $game = \App\Game::find($gameID);
        if($game->user_id === $userID){
            return true;
        }
        
        return false;
    }

}
