<?php
require_once 'lib/controller/MySQL_Controller.php';
require_once 'lib/functions/IDB_Functions.php';

class MySQL_Functions implements IDB_Functions {
    
    // NOTE: The function of this class is to package the information given by the DB_Controller and make an SQL query for it, which 
    //      can be called by the SQL controller.
    
    private static $m_oController;
    
    private static function getDB(){
        if (!isset(self::$m_oController)) self::$m_oController = new MySQL_Controller();
        return self::$m_oController;
    }
    
    public static function AddActivity($activity){
        $sql = "INSERT INTO Activity (UserID, GameID, ChallengeInstID, Time, Points, PayoffID, VerifiedBy, VerifiedID, Result) VALUES(?,?,?,?,?,?,?,?,?)";
        $aToInsert = array();
        $aToInsert[] = $activity->UserID();
        $aToInsert[] = $activity->GameID();
        $aToInsert[] = $activity->ChallengeInstID();
        $aToInsert[] = $activity->Time();
        $aToInsert[] = $activity->Points();
        $aToInsert[] = $activity->PayoffID();
        $aToInsert[] = $activity->VerifiedBy();
        $aToInsert[] = $activity->VerifiedID();
        $aToInsert[] = $activity->Result();
        self::getDB()->executeAction($sql, $aToInsert);
    }
	
    public static function AddGame($game){
        $sql = "INSERT INTO Game (Name, Description, Criteria_to_Play, Start_Date, End_Date, Winning_Points) VALUES(?,?,?,?,?,?)";
        $aToInsert = array();
        $aToInsert[] = $game->Name();
        $aToInsert[] = $game->Description();
        $aToInsert[] = $game->Criteria();
        $aToInsert[] = $game->StartDate();
        $aToInsert[] = $game->EndDate();
        $aToInsert[] = $game->WinningPoints();
        self::getDB()->executeAction($sql, $aToInsert);
    }
	
	public static function AddChallenge($challenge){
        $sql = "INSERT INTO Challenge (Name, Instructions, Game_ID, Start_Date, End_Date, Min_Players, Max_Players, VerificationID) VALUES(?,?,?,?,?,?,?,?)";
        $aToInsert = array();
        $aToInsert[] = $challenge->Name();
        $aToInsert[] = $challenge->Instructions();
        $aToInsert[] = $challenge->GameID();
        $aToInsert[] = $challenge->StartDate();
        $aToInsert[] = $challenge->EndDate();
        $aToInsert[] = $challenge->MinPlayers();
		$aToInsert[] = $challenge->MaxPlayers();
		$aToInsert[] = $challenge->VerificationID();
        self::getDB()->executeAction($sql, $aToInsert);
    }
	
    public static function AddPoints($points, $userId, $gameId){
        $sql = "UPDATE Points SET Points=(Points+?) WHERE UserID=? AND GameID=?";
        $aToInsert = array();
        $aToInsert[] = $points;
        $aToInsert[] = $userId;
        $aToInsert[] = $gameId;
        self::getDB()->executeAction($sql, $aToInsert);
        $sumSql="UPDATE User SET Total_Points=(select SUM(Points) from Points where UserID =?) WHERE UserID=?";
        $bToInsert = array();
        $bToInsert[] = $userId;
        $bToInsert[] = $userId;
        self::getDB()->executeAction($sumSql, $bToInsert);
    }
    
    public static function AddUser($user){
        $sql = "INSERT INTO User (MAC_Address, Username, Password) VALUES(?,?,?)";
        $aToInsert = array();
        $aToInsert[] = $user->MacAddress();
        $aToInsert[] = $user->Username();
        $aToInsert[] = $user->Password(); //something to save
        self::getDB()->executeAction($sql, $aToInsert);
    }
    
    // Add ChallengeInstance and return its corresponding ID in the table
    public static function AddChallengeInstance($challengeInstance)
    {
        $sql = "INSERT INTO ChallengeInstance (ChallengeID, UserIDs, Status, Date) VALUES(?,?,?,?)";
        $aToInsert = array();
        $aToInsert[] = $challengeInstance->ChallengeID();
        $aToInsert[] = $challengeInstance->UserIDs();
        $aToInsert[] = 'pending';
        $aToInsert[] = $challengeInstance->DateTime();
        self::getDB()->executeAction($sql, $aToInsert);

        $sql = "SELECT ChallengeInstanceID FROM ChallengeInstance WHERE ChallengeId=? AND UserIDs=? AND Status=? AND Date=? ORDER By ChallengeInstanceID DESC";
        $result = self::getDB()->executeQuery($sql, $aToInsert);
        return $result[0]['challengeinstanceid'];
    }
    
    public static function AddChallengeAcceptance($challengeAcceptance)
    {
        $sql = "INSERT INTO ChallengeAcceptance (ChallengeInstanceID, UserID, Accepts) VALUES(?,?,?)";
        $aToInsert = array();
        $aToInsert[] = $challengeAcceptance->ChallengeInstanceID();
        $aToInsert[] = $challengeAcceptance->UserID();
        $aToInsert[] = $challengeAcceptance->Accepts();
        self::getDB()->executeAction($sql, $aToInsert);
    }
	
	public static function DeleteChallenge($challenge){
        $sql = "DELETE FROM Challenge WHERE ChallengeID=?";
        $aParams = array();
        $aParams[] = $challenge->ID();
        self::getDB()->executeAction($sql, $aParams);
    }
	
	public static function DeleteChallengeInstance($challengeInstance){
        $sql = "DELETE FROM ChallengeInstance WHERE ChallengeInstanceID=?";
        $aParams = array();
        $aParams[] = $challengeInstance->ID();
        self::getDB()->executeAction($sql, $aParams);
    }
	
	public static function DeleteChallengeAcceptance($challengeAcceptance){
        $sql = "DELETE FROM ChallengeAcceptance WHERE ChallengeAcceptanceID=?";
        $aParams = array();
        $aParams[] = $challengeAcceptance->ID();
        self::getDB()->executeAction($sql, $aParams);
    }
	
	public static function DeleteGame($game){
        $sql = "DELETE FROM Game WHERE GameID=?";
        $aParams = array();
        $aParams[] = $game->ID();
        self::getDB()->executeAction($sql, $aParams);
    }
	
	public static function DeleteUser($user){
        $sql = "DELETE FROM User WHERE UserId=?";
        $aParams = array();
        $aParams[] = $user->ID();
        print($user->ID());
        self::getDB()->executeAction($sql, $aParams);
    }
	
    public static function GetActivities($user){
        $sql = "SELECT * FROM Activity WHERE userid=?";
        $aParams = array();
        $aParams[] = $user->ID();
        $aActivities = self::getDB()->executeQuery($sql, $aParams);
        return $aActivities;
    }
    
    public static function GetAllGame() {
        $sql = "SELECT * FROM Game";
        $aGames = self::getDB()->executeQuery($sql);
        return $aGames;
    }
    
    // Find which games the user is playing based on the point total that he has in the Points table
    // Find all challenges associated with the games found in the first query
    public static function GetChallenges($user){
        $sql = "SELECT * FROM Points WHERE UserId=?";
        $aParams = array();
        $aParams[] = $user->ID();
        $aPoints = self::getDB()->executeQuery($sql, $aParams);
		$sql = "SELECT COUNT(*) FROM Game";
       	$oCountQueryResults = self::getDB()->executeQuery($sql);
        $iNumGames = $oCountQueryResults[0]['count(*)'];

        // If this user is not playing all of the games that the opponent is playing, add them to the game they wish to play
        // For now, this is a hack to populate the games associated with it
        if (count($aPoints) < $iNumGames)
       	{
            for($i = 0; $i < $iNumGames; $i++)
			{
	       		$sql = "INSERT INTO Points (GameID, UserID, Points, Is_Active) VALUES(?,?,?,?)";
				$aToInsert = array();
				$aToInsert[] = $i + 1;
				$aToInsert[] = $user->ID();
				$aToInsert[] = "0";
				$aToInsert[] = "1";
                self::getDB()->executeAction($sql, $aToInsert);
            }

		}
        //self::GetChallenges($user);
        $sql = "SELECT * FROM Points WHERE UserId=?";
        $aPoints = self::getDB()->executeQuery($sql, $aParams);

        $aChallenges = array();
        $sql = "SELECT * FROM Challenge WHERE Game_ID=?";
        foreach ($aPoints as $point){
            $aChallenges[] = self::getDB()->executeQuery($sql, array($point['gameid']));
        }
        return $aChallenges;
    }
    
    public static function GetChallengeInstances($user, $aStatuses) {
        $sql = "SELECT Challenge.*, ChallengeInstance.*, Game.Name AS gamename, ChallengeAcceptance.Accepts as useracceptance FROM Challenge, ChallengeInstance, ChallengeAcceptance, Game 
                WHERE ChallengeAcceptance.UserID=? 
                AND ChallengeAcceptance.ChallengeInstanceID=ChallengeInstance.ChallengeInstanceID 
                AND ChallengeInstance.ChallengeID=Challenge.ChallengeID 
                AND Game.GameID=Challenge.Game_ID
                AND(";
        $aParams = array();
        $aParams[] = $user->ID();
        $statusSQL = "";
        foreach ($aStatuses as $status) {
            if ($statusSQL == "") {
                $statusSQL .= "ChallengeInstance.Status=?";
            } else {
                $statusSQL .= " OR ChallengeInstance.Status=?";
            }
            $aParams[] = $status;
        }
        $sql .= $statusSQL . ")";
        $aInstances = self::getDB()->executeQuery($sql, $aParams);
        return $aInstances;
    }
    
    public static function GetChallengeInstance($user, $instanceID) {
        $sql = "SELECT Challenge . * , ChallengeInstance . * , Game.Name AS gamename, ChallengeAcceptance.Accepts AS useracceptance
                FROM Challenge, ChallengeInstance, ChallengeAcceptance, Game
                WHERE ChallengeAcceptance.UserID=?
                AND ChallengeAcceptance.ChallengeInstanceID = ChallengeInstance.ChallengeInstanceID
                AND ChallengeInstance.ChallengeID = Challenge.ChallengeID
                AND Game.GameID = Challenge.Game_ID
                AND ChallengeInstance.ChallengeInstanceID=?";
        $aInstances = self::getDB()->executeQuery($sql, array($user->ID(), $instanceID));
        return $aInstances;
    }
    
    public static function GetGame($gameName) {
        $sql = "SELECT * FROM Game WHERE Name=?";
        $aGame = self::getDB()->executeQuery($sql, array($gameName));
        return $aGame;
    }
    
    public static function GetLogs($iLimit, $strFunction, $strMAC) {
        $sql = "SELECT * FROM Log";
        $strWhere = "";
        $aParams = array();
        if($strFunction != "") {
            if($strWhere == "") {
                $strWhere .= " WHERE Function_Called=?";
            } else {
                $strWhere .= " AND Function_Called=?";
            }
            $aParams[] = $strFunction;
        }
        if($strMAC != "") {
            if($strWhere == "") {
                $strWhere .= " WHERE MAC_Address=?";
            } else {
                $strWhere .= " AND MAC_Address=?";
            }
            $aParams[] = $strMAC;
        }
        $sql .= $strWhere;
        $sql .= " ORDER BY Time DESC LIMIT 0, " . $iLimit;
        $aLogs = self::getDB()->executeQuery($sql, $aParams);
        return $aLogs;
    }
    
    public static function GetPointsForUser($user) {
        $sql = "SELECT * FROM Points WHERE UserID=?";
        $aPoints = self::getDB()->executeQuery($sql, array($user->ID()));
        return $aPoints;
    }
    
    public static function GetOpponents($user) {
        $sql = "SELECT User.Username, User.UserID FROM User 
                WHERE User.UserID IN
                    (SELECT distinct ChallengeAcceptance.UserID FROM ChallengeAcceptance 
                     WHERE ChallengeAcceptance.ChallengeInstanceID IN 
                        (SELECT ChallengeAcceptance.ChallengeInstanceID FROM ChallengeAcceptance 
                        WHERE UserID=?) 
                     AND ChallengeAcceptance.UserID <> ?)";
        $aOpponents = self::getDB()->executeQuery($sql, array($user->ID(), $user->ID()));
        return $aOpponents;
    }
    
    public static function GetStandings($limit, $gameID) {
        $sql = "SELECT User.Username, Game.Name, Points.Points FROM User, Game, Points
                WHERE Points.GameID=? AND Points.GameID=Game.GameID AND User.UserID=Points.UserID
                Order By Points.Points DESC LIMIT 0, " . $limit;
        $aStandings = self::getDB()->executeQuery($sql, array($gameID));
        return $aStandings;
    }
    
    public static function GetUsers(){
        $sql = "SELECT * FROM User";
        $aUsers = self::getDB()->executeQuery($sql);
        return $aUsers;
    }
    
    public static function GetPayoffByID($payoffID){
        $sql = "SELECT * FROM Payoffs WHERE PayoffID=?";
        $aUsers = self::getDB()->executeQuery($sql, array($payoffID));
        return $aUsers;
    }
    
    public static function GetChallengeByID($challengeID){
        $sql = "SELECT * FROM Challenge WHERE ChallengeID=?";
        $aUsers = self::getDB()->executeQuery($sql, array($challengeID));
        return $aUsers;
    }
    
    public static function GetChallengeInstByID($challengeInstID){
        $sql = "SELECT * FROM ChallengeInstance WHERE ChallengeInstanceID=?";
        $aUsers = self::getDB()->executeQuery($sql, array($challengeInstID));
        return $aUsers;
    }
    
    public static function GetGlobalStats() {
        $sql = "SELECT
                (SELECT COUNT(*) FROM User) AS TotalUsers,
                (SELECT COUNT(DISTINCT MAC_Address) FROM Log WHERE Time > (CURDATE() - 2)) AS activeusers,
                (SELECT COUNT(*) FROM Game WHERE End_Date < CURDATE()) AS gamescompleted,
                (SELECT COUNT(*) FROM Game WHERE End_Date >= CURDATE()) AS gamesinprogress, 
                (SELECT COUNT(*) FROM Game WHERE Start_Date <= CURDATE()) AS gamesstarted";
        $aGlobalStats = self::getDB()->executeQuery($sql);
        return $aGlobalStats;
    }
    
    public static function GetSocialMediaBySiteAndName($website, $webUsername) {
        $sql = "SELECT * FROM SocialMedia WHERE username=? AND service=?";
        $aSocialMedia = self::getDB()->executeQuery($sql, array($webUsername, $website));
        return $aSocialMedia;
    }
    
    public static function GetUserByEmail($email){
        $sql = "SELECT * FROM User WHERE Facebook_Email=?";
        $aUsers = self::getDB()->executeQuery($sql, array($email));
        return $aUsers;
    }
    
    public static function GetUserByID($userID){
        $sql = "SELECT * FROM User WHERE UserID=?";
        $aUsers = self::getDB()->executeQuery($sql, array($userID));
        return $aUsers;
    }
    
    public static function GetUserByMac($MACAddress){
        $sql = "SELECT * FROM User WHERE MAC_Address=?";
        $aUsers = self::getDB()->executeQuery($sql, array($MACAddress));
        return $aUsers;
    }
    
    public static function GetUserByUsername($username) {
        $sql = "SELECT * FROM User WHERE Username=?";
        $aUsers = self::getDB()->executeQuery($sql, array($username));
        return $aUsers;
    }
    
    public static function GetUserIdFromSocialMedia($socialMediaUsername, $service){
        $sql = "SELECT userid FROM SocialMedia WHERE username=? AND service=?";
        $aUsers = self::getDB()->executeQuery($sql, array($socialMediaUsername, $service));
        return $aUsers;
    }
    
    public static function GetUserChallengesCompleted($user) {
        $sql = "SELECT COUNT(ActivityID) AS Count FROM Activity WHERE UserID=?";
        $aCount = self::getDB()->executeQuery($sql, array($user->ID()));
        return $aCount;
    }
    
    public static function GetUserGameStats($user) {
        $sql = "SELECT Points.GameID, Game.Name AS GameName, Game.Description AS GameDesc, Points.Points AS TotalPoints FROM Points, Game WHERE Points.GameID=Game.GameID AND Points.UserID=? GROUP BY Points.GameID";
        $aStats = self::getDB()->executeQuery($sql, array($user->ID()));
        return $aStats;
    }
    
    public static function GetVerification($verifyID){
        $sql = "SELECT * FROM Verification WHERE VerificationID=?";
        $aVerification = self::getDB()->executeQuery($sql, array($verifyID));
        return $aVerification;
    }
    
    public static function GetChallengeInstanceAcceptances($challengeInstanceID){
        $sql = "SELECT ChallengeAcceptance.ChallengeAcceptanceID, ChallengeAcceptance.UserID, ChallengeAcceptance.Accepts, 
                ChallengeInstance.ChallengeInstanceID, ChallengeInstance.ChallengeID, ChallengeInstance.UserIDs, ChallengeInstance.Status 
                FROM ChallengeAcceptance, ChallengeInstance
                WHERE ChallengeAcceptance.ChallengeInstanceID = ChallengeInstance.ChallengeInstanceID 
                AND ChallengeAcceptance.ChallengeInstanceID=?";
        $aAcceptances = self::getDB()->executeQuery($sql, array($challengeInstanceID));
        return $aAcceptances;
    }
    
    public static function GetChallengeInstanceAcceptance($challengeInstanceID, $userID){
        $sql = "SELECT * FROM ChallengeAcceptance WHERE ChallengeInstanceID=? AND UserID=?";
        $aAcceptances = self::getDB()->executeQuery($sql, array($challengeInstanceID, $userID));
        return $aAcceptances;
    }
    
    public static function LinkUser($website, $webUsername, $userId){
        $sql = "INSERT INTO SocialMedia (userid, username, service) VALUES(?,?,?)";
        $aToInsert = array();
        $aToInsert[] = $userId;
        $aToInsert[] = $webUsername;
        $aToInsert[] = $website;
        self::getDB()->executeAction($sql, $aToInsert); 
    }

    public static function Log($MACAddress, $functionName){
        $sql = "INSERT INTO Log (MAC_Address, Function_Called, Time)  VALUES(?,?,?)";
        $aToInsert = array();
        $aToInsert[] = $MACAddress;
        $aToInsert[] = $functionName;
        $aToInsert[] = date('Y-m-d H:i:s');
        self::getDB()->executeAction($sql, $aToInsert);
    }

    public static function UpdateUser($existingUser){
        $sql = "UPDATE User SET MAC_Address=?, Facebook_Email=?, Name=?, Username=? WHERE UserID=?";
        $aToInsert = array();
        $aToInsert[] = $existingUser->MacAddress();
        $aToInsert[] = $existingUser->Facebook();
        $aToInsert[] = $existingUser->Name();
        $aToInsert[] = $existingUser->Username();
        $aToInsert[] = $existingUser->ID();
        self::getDB()->executeAction($sql, $aToInsert);
    }

    public static function UpdateChallengeInstance($existingChallengeInstance){
        $sql = "UPDATE ChallengeInstance SET ChallengeID=?, UserIDs=?, Status=?, Accepts=? WHERE ChallengeInstanceID=?";
        $aToInsert = array();
        $aToInsert[] = $existingChallengeInstance->ChallengeID();
        $aToInsert[] = $existingChallengeInstance->UserIDs();
        $aToInsert[] = $existingChallengeInstance->Status();
        $aToInsert[] = $existingChallengeInstance->Accepts();
        $aToInsert[] = $existingChallengeInstance->ID();
        self::getDB()->executeAction($sql , $aToInsert);
    }

    public static function UpdateChallengeAcceptance($existingChallengeAcceptance){
        $sql = "UPDATE ChallengeAcceptance SET ChallengeInstanceID=?, UserID=?, Accepts=? WHERE ChallengeAcceptanceID=?";
        $aToInsert = array();
        $aToInsert[] = $existingChallengeAcceptance->ChallengeInstanceID();
        $aToInsert[] = $existingChallengeAcceptance->UserID();
        $aToInsert[] = $existingChallengeAcceptance->Accepts();
        $aToInsert[] = $existingChallengeAcceptance->ID();
        self::getDB()->executeAction($sql, $aToInsert);
    }
    
    public static function UnsetMacAddressForOthers($macAddress, $username){
        $sql = "UPDATE User SET MAC_Address='' WHERE Username<>? AND MAC_Address=?";
        $aToInsert = array();
        $aToInsert[] = $username;
        $aToInsert[] = $macAddress;
        self::getDB()->executeAction($sql, $aToInsert);
    }
}

?>