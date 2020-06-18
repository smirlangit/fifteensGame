<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id айди зписи
 * @property int $user_id айди игрока создавшего игру
 * @property boolean $win была ли игра выигрышной или нет
 * @property date $startgame время начала запуска игры
 * @property date $endgame время завершения игры
 * @property string gamefield игровое поле в виде строкового набора символов
 */

class Game extends Model
{
        
    protected $fillable = [
        'user_id', 'win', 'startgame', 'endgame', 'gamefield'
    ];
    

}
