<?php

require_once $appRoot.'Classes/Game.php';
require_once $appRoot.'Database/DB_Controller.php';

class GetGames {
    
    private static $m_aGames;
    
    // RETURN CODES:
    // 0  = success
    
    public static function Execute (){
        // Get all games from the database and return them
        self::$m_aGames = DB_Controller::GetAllGame();
        
        DB_Controller::Log("", "Get All Games");
        return 0;
    }
    
    public static function Games(){
        return self::$m_aGames;
    }
}

?>