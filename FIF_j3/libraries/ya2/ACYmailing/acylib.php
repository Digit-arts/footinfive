<?php


function AddUser() {
	
	if(!include_once(rtrim(JPATH_ADMINISTRATOR,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php')){
		echo 'This code can not work without the AcyMailing Component';
		return false;
	}
	
	$myUser = new stdClass();
	
	
	$myUser->email = 'emailaddressoftheuser';
	
	$myUser->name = 'myname'; //this information is optional
	
	//If you require a confirmation but don't want the user to have to confirm his subscription via the API, you can set the confirmed field to 1:
	//$myUser->confirmed = 1;
	
	//You can add as many extra fields as you want if you already created them in AcyMailing
	//$myUser->country = 'france';
	//$myUser->phone = '064872754';
	//...
	
	$subscriberClass = acymailing_get('class.subscriber');
	
	$subid = $subscriberClass->save($myUser); //this function will return you the ID of the user inserted in the AcyMailing table
}

function SubscribeRemoveUserFromList() {
	$subscribe = array(2,4,6); //Id of the lists you want the user to be subscribed to (can be empty)
	$remove = array(1,3); //Id of the lists you want the user to be removed from (can be empty)
	$memberid = '23'; //ID of the Joomla User or user e-mail (this code supposes that the user is already inserted in AcyMailing!)
	
	if(!include_once(rtrim(JPATH_ADMINISTRATOR,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php')){
		echo 'This code can not work without the AcyMailing Component';
		return false;
	}
	
	$userClass = acymailing_get('class.subscriber');
	
	$newSubscription = array();
	if(!empty($subscribe)){
		foreach($subscribe as $listId){
			$newList = array();
			$newList['status'] = 1;
			$newSubscription[$listId] = $newList;
		}
	}
	if(!empty($remove)){
		foreach($remove as $listId){
			$newList = array();
			$newList['status'] = 0;
			$newSubscription[$listId] = $newList;
		}
	}
	
	if(empty($newSubscription)) return; //there is nothing to do...
	
	$subid = $userClass->subid($memberid); //this function returns the ID of the user stored in the AcyMailing table from a Joomla User ID or an e-mail address
	if(empty($subid)) return false; //we didn't find the user in the AcyMailing tables
	
	$userClass->saveSubscription($subid,$newSubscription);
}

?>