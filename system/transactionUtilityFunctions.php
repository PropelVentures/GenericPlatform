<?php
session_start();
include_once($GLOBALS['DATABASE_APP_DIR']."db.php");

function getCurrentUserWallet(){
	
	/*Query for the getting last transaction data of the current user wallet information*/
	$wsql = "SELECT * FROM wallet WHERE user_id=".$_SESSION['uid']." ORDER BY  wallet_transaction_id DESC LIMIT 1";
	$wresult = mysql_query($wsql);
	$myWallet = mysql_fetch_array($wresult);
	$wallet = $myWallet['wallet_balance'];
	
	if($wallet != "" && $wallet >0){
		return $wallet;
	}
	else{
		return 0;
	}
}

function getUserWalletBalance($uid){
	
	/*Query for the getting last transaction data of the user wallet information*/
	$wsql = "SELECT * FROM wallet WHERE user_id=".$uid." ORDER BY  wallet_transaction_id DESC LIMIT 1";
	$wresult = mysql_query($wsql);
	$myWallet = mysql_fetch_array($wresult);
	$wallet = $myWallet['wallet_balance'];
	
	if($wallet != "" && $wallet >0){
		return $wallet;
	}
	else{
		return 0;
	}
}


function isFirstTransaction(){
	$ftsql = "SELECT * FROM transactions";
	$ftresult = mysql_query($ftsql);
	$rows = mysql_num_rows($ftresult);
	
	if($rows==0){
		return true;
	}
	else{
		return false;
	}
}

function isFirstWallet(){
	$ftsql = "SELECT * FROM wallet";
	$ftresult = mysql_query($ftsql);
	$rows = mysql_num_rows($ftresult);
	
	if($rows==0){
		return true;
	}
	else{
		return false;
	}
}

function debitCurrentUserWallet($transId, $currentBalance, $amount, $uid){
	$debit = $amount;
	$wallet_balance = $currentBalance - $amount;
	$wallet_date = date("Y-m-d H:i:s");
	$wallet_transaction_id = $transId;
	
	$dsql = "INSERT INTO wallet(debit, wallet_balance, user_id, wallet_date, wallet_transaction_id) values('".$debit."','".$wallet_balance."', ".$uid.", '".$wallet_date."', ".$wallet_transaction_id." )";
	
	$dresult = mysql_query($dsql);	
	if($dresult){
	 	return mysql_insert_id();
	}else{
		return FALSE;
	}	
}

function creditCurrentUserWallet($transId, $currentBalance, $amount, $ownerId){
	
	$credit = $amount;
	$wallet_balance = $currentBalance + $amount;
	$wallet_date = date("Y-m-d H:i:s");
	$wallet_transaction_id = $transId;
	
	$csql = "INSERT INTO wallet(credit, wallet_balance, user_id, wallet_date, wallet_transaction_id) values('".$credit."','".$wallet_balance."', ".$ownerId.", '".$wallet_date."', ".$wallet_transaction_id." )";
	
	$cresult = mysql_query($csql);	
	if($cresult){
	 	return mysql_insert_id();
	}else{
		return FALSE;
	}	
}
?>