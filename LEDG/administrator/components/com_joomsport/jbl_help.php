<?php
/*
BearDev.com
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<div style=" width:100%;float:left; padding:10px;font-family: verdana, arial, sans-serif; font-size: 9pt;">
	<div style="float:left; width:70%;">
		<h1>JoomSport Help</h1>
	</div>
	<div style="float:right;margin-right:20px;">
		<?php
			echo '<a href="http://beardev.com" target="_blank">'.JHTML::_('image.site',  'logobigbright.png', '/components/com_joomsport/img/', NULL, NULL, 	'BearDev.com' ).'</a>'
		?>
	</div>	
	<div style="float:left; width:75%">
		<p>
			<a href="#1">1. Tournament</a><br />
			<a href="#2">2. Season</a><br />
			<a href="#3">3. Teams</a><br />
			<a href="#4">4. Match Day</a><br />
			<a href="#5">5. Players</a><br />
			<a href="#6">6. Events</a><br />
			<a href="#7">7. Groups</a><br />
			<a href="#8">8. Moderators</a><br />
			<a href="#9">9. Extra fields</a><br />
			<a href="#10">10. Maps</a><br />
			<a href="#11">11. Venue</a><br />
			<a href="#12">12. Configuration</a><br />
			<a href="#13">13. FE access</a><br />
			<a href="#14">14. FE Managment</a><br />
			<a href="#15">15. Esport features</a><br />
		</p>

		<div>
			<a name="1"></a>
			<h4>1. Tournament</h4>
			In this section you can create <i>Tournament</i> for any teams.<br />
			Tournaments are used as categories for the seasons<br />
			<ul>
				<li>Go to <b>Components -> JoomSport -> Tournaments</b></li>
				<li>Click <b>New</b> button</li>
				<li>Enter the Tournament name</li>
                <li>If you want the Tournament to be published, select the Yes radio-button next to the Published field</li>
				<li>Select the Play mode: Team or Single competition</li>
                <li>Select between two Tournament Types: Group or Knockout one</li>
				<li>Also you can upload Logo for tournament and write some description about it.</li>
			    <li>Press the <b>Save</b> button</li>
			</ul>	
		</div>

		<div>
			<a name="2"></a>
			<h4>2. Season</h4>
			In this section you can create and edit <i>Season</i>.  To create new season
			<ul>
				<li>Go to <b>Components -> JoomSport -> Season</b></li>
				<li>Press <b>New</b> button</li>
				<li>Select tournament</li>
				<li>Set won, draw, lost points</li>
				<li>Enable the Extra time option if needed</li>
				<li>Add teams to the season</li>
				<li>Press the <b>Save</b> button</li>
				<li>Also in this section you can specify site Users who have FE access to edit the Season-<i>Season administrators</i>.</li>
				<li>You may specify <i>Tournament Table Columns</i> which should be displayed at FE</li>
				<li><i>Set ranking criteria.</i> The order of the sorting Teams on the tournament table when the Points are equal.
			The checkmark in this field means that when there is equal points game the ranking will be accomplished according to the points between these teams and taking into consideration only results of games between the teams with equal points (including quantity of points and difference of  goals and passed balls).</li>
			</ul>
			<p><i>NOTE:</i></p>
            <p><i>*Season administrator can edit players, create Match Days/Matches</i></p><br />
		</div>
          <i><b>JoomSport options</i></b><br />

         You may specify registration possibilities and describe season rules that will be displayed at the Front End. To do this, go to the JoomSport options tab.
	        <ul>
	            <li>Set number of participants</li>
                <li>Enable or disable registration option</li>
                <li>Set registration start and end dates using the calendar button</li>
                <li>Add a map from the drop-down list.</li>
                <li>Enter season rules.</li>
			</ul>
		  <i><b>Table Colors</i></b><br />
			Specify the colors to highlight the Tournament Table lines (Tab 'Table Colors'). </p>
			To highlight the line you need: 
			<ul>
				<li>Select the Color <b>Season -> Tab 'Table colors'</b></li>
				<li>Press <b>...</b> button or input the color value. Ex. <i>'#FFFFFF'</i></li>
				<li>Input the line number for selected color. Ex. <i>'1,2,3'</i> or <i>'1-3'</i></li>
				<li>To add other color press <i>'Add new'</i> button</li>
				<li>Press <b>Save</b> button</li>
			</ul>
		<div>
			<a name="3"></a>
		    <h4>3. Teams</h4>
			In this section you can create <i>Teams.</i> To create new team
			<ul>
				<li>Go to <b>Comopnents -> JoomSport -> Team</b></li>
				<li>Press <b>New</b> button</li>
				<li>Fill all necessary fields*</li>
				<li>If you want to <a href="#11">highlight your team</a> in Season Table you need mark CheckBox 'Your Team'</li>
				<li>Press <b>Save</b> button</li>
			</ul>
			<p><i>NOTE:</i></p><br />
            <p><i>*Please add Seasons to the team, otherwise team will not be displayed in Match settings.</i></p><br />
			<b><i>Players</b></i>
			<br />
			In the Players tab you can add members to the team according to the Season. (make sure they added to the list of players. See the Players paragraph):
			<ul>
			    <li>Go to the Players tab</li>
                <li>Choose the Season</li>
                <li>Add Players according to the Season</li>
                <li>On the Front End on the Team page players will be displayed according to the Season</li>
				<li>Press the <b>Save</b> button</li>
			</ul>	
				
			<b><i>Bonuses</b></i>
			<br />
			You can set bonuses for each team for each tournament in the Bonuses tab. Bonuses are used for a player/team to be able to start a tournament with gained points.
            <ul>
			<li>After all changes are done, press the <b>Save</b> button</li>
			</ul>
			<p><i>*If the standard fields are not enough you can create extra text fields</i> ( <a href="#9">Extra fields</a> )</p>
			<p><i>Attention!!  If you keep any Extra Team field empty, it will not display at FE</i></p>
			
		</div>

		<div>
			<a name="4"></a>
			<h4>4. Match Day</h4>
			In that section you can create new <i>Match Day.</i> To create new Match Day
			<ul>
				<li>Go to <b>Components -> JoomSport -> Match Day</b></li>
				<li>Select tournament in DropDown List under match day list</li>
				<li>Press <b>New</b> button</li>
				<li>Enter the Match Day name</li>
				<li>Select the playoff option if needed (If the option is enabled, specified Match Day is not counted in tournament table)</li>
                <li>Select teams from the drop-down lists</li>
                <li>Enter the score</li>
                <li>Select the date, time and extra time</li>
                <li>Press the Apply or <b>Save</b> button</li><br />
			</ul>	
		<b><i>Friendly Match*new</i></b><br />
        Friendly matches are not counted in Season table.
			<ul>
                <li>Go to <b>Components -> JoomSport-> Match Day</b></li>
                <li>Select a <b>Friendly match</b> in the drop-down list</li>
                <li>Press the <b>New</b> button</li>
                <li>Enter the <b>Match Day name</b></li>
                <li>Select teams from the drop-down lists</li>
                <li>Enter the score</li>
                <li>Select the date, time and extra time</li>
                <li>Press the Apply or <b>Save</b> button</li>
			</ul>
		You can also add details** to the match. There are player events and match statistics. 
			<ul>
  		        <li>To add player events choose event, player and set minutes.</li> 
				<li>To add statistic choose event and set count.</li>
				<li>After that press <b>Save</b> button.</li>
				<li>Also in match details section you can specify bonus points for each team which will be calculated as extra points in the tournament table</li>
			</ul>	
		<b><i>Teams Squad.</b></i><br />
				               
                <b>Line Up</b> Choose players from the list.<br />
                <b>Substitutes</b> Choose players from the  list according to the substitutes occurred.<br />
				<p><i>NOTE:</i></p>
                <p><i>If the Tournament is knockout you will create a table-tree</i></p>
				<ul>
				  <li>Choose knockout tournament</li>
                  <li>Press the New button</li>
                  <li>Enter the Match Day name</li>
                  <li>Choose the quantity of participants press Generate button</li>
                  <li>Select participants of first round and enter scores press <b>Save</b> button</li>
				  <li>press the <b>Save</b> button after entering the data of each round</li>
                </ul>
				To add Match details press to the picture that is marked out by the arrows
		</div>	

		<div>
			<a name="5"></a>
			<h4>5. Players</h4>
			In this section you can create <i>Players.</i> 
			<ul>	
				<li>Go to <b>Comopnents -> JoomSport -> Players</b></li>
				<li>Press <b>New</b> button</li>
				<li>Enter First, Last and Nick name, upload photo, set player position, player team, enter player description***</li>
				<li>Press <b>Save</b> button</li></br>
			</ul>
			<p><i>*All players are linked to users registered on the site</p></i>
			<p><i>**If the standard fields are not enough you can create extra text fields</i> ( <a href="#9">Extra fields</a> )</p>
			<p><i>Attention!!  If you keep any Extra Player field empty, it will not display at FE</i></p>		
		</div>

		<div>
			<a name="6"></a>
			<h4>6. Events</h4>
			In this section you can create player events or statistics. You can also add an icon to the events which will be displayed at the FE near event name.<br/>
		If you are creating event for player you can choose its type: <i>Average/Sum</i> (<i> Average</i>- In Statistic average amount will be displayed; <i>Sum</i>- In Statistics sum will displayed,If you are creating Sum of events, please choose events to sum)<br/>
        Press the <b>Save</b> button
		</div>

		<div>
			<a name="7"></a>
			<h4>7. Groups</h4>
			If you have Group tournament in that section you can create groups and attached teams. At the FE tournament table will be displayed according to your groups.
			<p><i>*To add teams to a Group first you need press <b>Apply</b> button and then sort teams.</i></p>
		to create new group:
		<ul>
				<li>Go to <b>Comopnents -> JoomSport -> Groups</b></li>
				<li>Press <b>New</b> button</li>
				<li>Enter the Group Name</li>
				<li>Specify the Season</li>
				<li>Add Teams to the Group</li>
				<li>Press <b>Save</b> button</li>
			</ul>
		</div>	
        <div>
			<a name="8"></a>
			<h4>8. Moderators</h4>
			In this section you can specify teams which the User can moderate from FE.<br />
			The section contains the list of web site Moderators.<br />
            <b>Moderator</b> can :<br />
            Edit team information<br />
            Manage team results<br />
            Add players to the team.<br />
			To add new moderator, do the following steps:
			<ul>
			    <li>Go to <b>Components -> JoomSport ->Moderators</b> </li>
                <li>Choose the User </li>
                <li>Add Teams </li>
                 <li>Press the <b>Save</b> button </li>
            </ul>
			<p><i>NOTE:</i></p>
                <p><i>The user should be registered on the site</i></p>
			To follow in moderate area you need to click Edit icon. The icon will be appear on FE near the Team name in the Tournamnet table when the user is logged in.
		</div>		
		<div>
			<a name="9"></a>
			<h4>9. Extra fields</h4>
			In this section you can create extra text fields to improve teams/players or match info.<br />
		</div>to create new extra field:
		<ul>
                <li>Go to Components -> JoomSport -> Extra Fields</li>
                <li>Press the <b>New</b> button</li>
                <li>Enter the name of the extra field</li>
                <li>For the field to be displayed in the tournament/player statistics table (Season page/Team page) select the <b>Yes</b> radio-button next to the <b>Publish</b> field</li>
                <li>Select the <b>field type</b> (Text Field, Radio Button, Text Area, Select Box, Link)</li>
                <li>Select the <b>type</b> of the field - Player/Team/Match/Season. This extra field will be displayed on the Team page or Tournament table or on the Match page according to the selection</li>
                <li>Choose the <b>table view</b> if required</li>
                <li>Set the viewing permission (<b>visible for all users</b> or only for <b>registered users</b>)</li>
                <li>Press <b>Save</b> button</li>
		    </ul>
		<div>
			<a name="10"></a>
			<h4>10. Maps</h4>
			This tool allows to create and upload game maps for on-line tournaments.<br />
			To create new game map, do the following steps:: 
			<ul>
				<li>Go to <b>Components -> JoomSport->Maps</b></li>
				<li>Press <b>New</b> button </li>
				<li>Enter the map <b>name</b></li>
                <li>Add <b>description</b> to the map</li>
                <li>Upload an Image, create an article if it is required</li>
                <li>Press the <b>Save</b> button</li>
			</ul>
		</div>
		<div>
			<a name="11"></a>
			<h4>11. Venue</h4>
			In this section you can create Venue<br />
			To create new Venue you need: 
			<ul>
				<li>Go to Components -> JoomSport -> Venue</li>
                <li>Press the <b>New</b> button</li>
                <li>Enter the <b>name</b> of the <b>Venue</b></li>
                <li>Specify it's <b>address,latitude and longtitude</b></li>
                <li>Press the <b>Save</b> button</li>
			</ul>
		</div>

		<div>
			<a name="12"></a>
			<h4>12. Configuration</h4>
			In this section you can
	In this section you can:
           <ul>
               <li>set a <b>Date Format</b> you want to display at the Front End</li>
               <li>specify <b>Your Team Color</b> to highlight your team in the Season Table</li>
               <li>to enable <b>Match comments</b></li>
               <li><b>Player</b> and <b>Team registrations</b></li>
               <li><b>Choose the additional field for players order which will be used as default in team view (by name or birth etc)</b></li>
               <li>specify <b>The height of the team emblem in the table view</b></li>
               <li>Enable <b>venue</b></li>
               <li><b>Display venue field in calendar layout</b></li>
               <li>Show <b>played matches</b></li>
               <li>Specify <b>Nickname or First name</b> to be displayed on FE</li>
               <li><b>Knockout layout Vertical</b></li>
            </ul>
			<b><i>Registration Fields</b></i></br>
			It allows you to configure Registration layout for the <b>Player/Team.</b> (Nickname/Country etc.)</br>
		<b><i>Administrators/Moderators rights</b></i></br>
Here you can limit Season administrator and Moderator rights
		<b>Season admin:</b></br>
		Can't edit players</br>
        Can't delete players</br>
        <b>Moderator:</b></br>
Allow Moderator to manage players</br>
specify the quantity of teams can be created from 1 account</br>
specify the quantity of players can be created from 1 account</br>
<b><i>Esport configuration</b></i></br>
Specify Invite option</br>
Can invite unregistered players</br>
Player can join team</br>
Moderator can invite players to match</br>
</br>
After all changes are made press the <b>Save</b> button
		</div>
	
		<div>
			<a name="13"></a><h4>13. FE access</h4></br>
			You can create FE access to the: 
			<ul>
				<li>Team Layout </li>
				<li>Season Table</li>
				<li>Match Day Layout </li>
				<li>Match Layout</li>
                <li>Player Layout</li>
                <li>Season List</li>
                <li>Team List</li>
                <li>Player List</li>
                <li>Player Registration Layout</li>
                <li>Team Registration Layout</li>
			</ul>
			<b>Team Layout</b></br>
In order to display the Team at the Front End, first, create a new menu item - go to<b> Menus > Main Menu</b>, click the <b>New</b> button. Choose the the <b>JoomSportMenu Item Type-> JoomSport -> Team Layout</b>.</br>
		<b><i>Details</b></i></br>
		Enter <b>Menu Title</b></br>
        In the <b>Required Settings</b> section select the <i>Season</i> and then select the <i>Team</i> you want to display at the Front End, add Menu Item Details.</br>
        Press the <b>Save </b>button</br>
		</br>
		<b>Season (Calendar or Table layout)</b></br>
		In order to display the <b>Season (Calendar or Table layout)</b> at the Front End, first, create a new menu item. Choose the <b>JoomSport Menu Item Type -> JoomSport -> Season Table/Calendar Layout</b> </br>
		<b><i>Details</b></i></br>
		Enter <b>Menu Title</b></br>
        In the <b>Required Settings</b> section select the <i>Season</i> and then select the <i>Team</i> you want to display at the Front End, add Menu Item Details.</br>
        Calendar has a filter for displaying matches accroding to the specified <b>Team/Date/Matchday</b></br>
		Press the <b>Save</b>button</br>
		</br>
		<b>Match Day</b></br>
		In order to display the Match Day at the Front End, first, create a new menu item. Choose the type <b>JoomSport Menu Item Type -> JoomSport -> MatchDay Layout</b></br>
		<b><i>Details</b></i></br>
		Enter <b>Menu Title</b></br>
        In the <b>Required Settings</b> section select the <i>Match Day</i> and then select the <i>Team</i> you want to display at the Front End, add Menu Item Details.</br>
        Press the <b>Save</b> button</br>
		</br>
		<b>Match</b></br>
		In order to display the Match at the Front End, first, create a new menu item. Choose the <b>JoomSport Menu Item Type -> JoomSport -> Match Layout</b></br>
        In the <b>Required Settings</b> section</br>

        Select the <b>Match</b> you want to display at the Front End</br>
        Add Menu Item Details.</br>
        Press the <b>Save</b> button.</br>
		</br>
		<b>Player layout</b></br>
		In order to display the Player at the Front End, first, create a new menu item. Choose <b>JoomSport Menu Item Type -> JoomSport -> Player Layout</b>, add <b>Menu Item Details.</b></br>
		<b><i>Details</b></i></br>
		Enter <b>Menu Title</b></br>
        In the <b>Required Settings</b> section select the <i>Player</i> you want to display at the Front End, add Menu Item Details.</br>
        Press the <b>Save</b> button</br>
		<i>*Player's statistic is displayed according to the Season.</i></br>
		</br>
		<b>Season list</b></br>
		In order to display the Season List at the Front End, first, create a new menu item. Choose <b>JoomSport Menu Item Type -> JoomSport -> Season List</b></br>
		<b><i>Details</b></i></br>
		Enter <b>Menu Title</b></br>
        In the <b>Required Settings</b> section select the <i>Season</i> you want to display at the Front End, add Menu Item Details.</br>
        Press the <b>Save</b> button</br>
		</br>
		<b>Team list</b></br>
		In order to display the Team List at the Front End, first, create a new menu item. Choose <b>JoomSport Menu Item Type -> JoomSport -> Team List</b></br>
		<b><i>Details</b></i></br>
		Enter <b>Menu Title</b></br>
        In the <b>Required Settings</b> section select the <i>Team</i> you want to display at the Front End, add Menu Item Details.</br>
        Press the <b>Save</b> button</br>
		</br>
		<b>Player list</b></br>
		In order to display the Player List at the Front End, first, create a new menu item. Choose <b>JoomSport Menu Item Type -> JoomSport -> Player List</b></br>
		<b><i>Details</b></i></br>
		Enter <b>Menu Title</b></br>
        In the <b>Required Settings</b> section select the <i>Player</i> you want to display at the Front End, add Menu Item Details.</br>
        Press the <b>Save</b> button</br>
		</br>
		<b>Player Registration</b></br>
		In order to display the Player Registration at the Front End, first, create a new menu item. Choose <b>JoomSport Menu Item Type -> JoomSport -> Player Registraion</b></br>
		Add Menu Item Details.</br>
        Press the <b>Save</b> button</br>
		</br>
		<b>Team Registration</b></br>
		In order to display the Team Registration at the Front End, first, create a new menu item. Choose <b>JoomSport Menu Item Type -> JoomSport -> Player Registraion</b></br>
		Add Menu Item Details.</br>
        Press the <b>Save</b> button</br>
		</div>


		<div>
			<a name="14"></a>
			<h4>14. FE Managment</h4>
			JoomSport allows Site users manage Season and specified Team from FE.<br />
			To specify Users who can manage the Season you need:
			<ul>
				<li>Go to <b>Components -> JoomSport -> Season</b> </li>
				<li>In section 'Add Season Administrators' specify users you want to manage the Season </li>
				<li>Press <b>Save</b> button </li>
				<li>Create menu item to the season (Menus ->Main menu->New item)</li>
				<li>Go to FE</li>
				<li>Login as specified user</li>
				<li>Click on the Season link under the Season table</li>
			</ul>
			<p>Season Administrators can manage teams, players and Match Days. </p>

			To specify Users who can manage only Team you need(Moderators):
			<ul>
				<li>Go to <b>Components -> JoomSport -> Moderators -> New</b> </li>You want 
				<li>Select the user you want to be a Moderator</li>
				<li>Specify the teams for the user account</li>
				<li>Press <b>Save</b> button </li>
				<li>Create menu item to the season (Menus -> New item)</li>
				<li>Go to FE</li>
				<li>Login as specified user</li>
				<li>Click on the Edit icon near the team name in the tournament table</li>
			</ul>
			<p>Moderator can manage Team info, Team players and Team matches. </p>
			</div>
			<p><i>Attention!!  Moderator cannot tick the match as played. Because the admin should approve the results which was inserted by Moderator. </i></p>
			
		<div>
			<p><b><i>Create your own league and enjoy the simplicity of the product!</i></b></p>
		</div>
		
		<div>
			<a name="15"></a>
			<h4>15.Esport features </h4>
			 For managing Esport options go to <b>Components -> JoomSport -> Configuration->Esport Configuration</b>.can the whole site including JoomSport component.</br>
             Specify:
			<ul>
				<li><b>Invite option</b> <i>Moderator  adds player to the team/Moderator invites player to the team</i></li>
                <li><b>Can invite unregistered players</b></li>
                <li><b>Player can join team</b></li>
                <li><b>Moderator can invite players to match</b></li>
			</ul>
		<b>1)<i>Invite option</i></b>
		    <ul>		
		        <li>Moderator adds player to the team- means that Moderators can add player to the team via FE. (Players will not be notified ;Players are able not to have Joomla user account.)</li>
                <li>Moderator invites player to the team- means that Moderator invites players, but they will be added to the team only aftertheir approval.</li>
		    </ul>
			<i>NOTE:</i></br>
            <i>*Moderator can invite only players who is registered as Joomla user.</i></br>
			<b>The order of actions:</b>
			<ul>
			1)Moderator invites the player</br>
            2)The player gets a notification on email and if he is agree he confirm the invitation(go through the link in the message) and automatically added to the team. If player does not want to be added to the team he ignores the email.
		    </ul>   
		<b>2)Can invite unregistered players</b></br>
         Moderator will have an ability to invite unregistered players, he will enter the Email and player will get a invitation.</br>
		<b>3)Player can join team</b></br>
       Player will have an ability to join a team via FE.</br>
	   The player will be added only after Moderator approval.</br>
	   <b>4)Moderator can invite players to match</b></br>
       Moderator will have an ability to invite players of his team to the match via FE.
	       <ul>
              <li>Login as Moderator go to <b>Your team->Matchday->Creates match->Save it</b></li>
              <li>Go <b>Match details</b></li>
              <li>Choose option Invite players <b>All from team/From  main squad</b></li>
              <li>Edit <b>Email title</b> and <b>text</b></li>
		   </ul>  
	<i>* Only players with Joomla user accounts will receive an Email for confirmation.</i></br>
	</div>
		
	</div>
</div>
	