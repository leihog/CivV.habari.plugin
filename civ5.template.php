
<style>

.civ5Container {
	color:#939393;
	background-color: #1B1B1B;
	
	height:40px;
	margin:0;
	padding:5px 4px 5px 8px;
	width:203px;

	font: 11px Arial,Helvetica,Verdana,sans-serif;
	white-space:nowrap;
	text-align: left;
}

.civ5IconContainer {
	float:left;
	margin:0 6px 0 0;
	padding:0;
	width:40px;
}

.civ5IconWrapper {
	background-color: #545454;
	border:0 none;
	height:40px;
	margin:0;
	padding:0;
	position:relative;
	width:40px;
}
.civ5IconWrapper img {
	border:0 none;
	height:32px;
	margin: 4px 4px;
	padding:0;
	width:32px;
}

</style>
<div class="civ5Container">
    <div class="civ5IconContainer">
    	<div class="civ5IconWrapper">
			<a href="http://steamcommunity.com/id/<?php echo $civ5['userid']; ?>/stats/CivV" title="View Civilization V stats for <?php echo $civ5['userid']; ?>"><img src="http://media.steampowered.com/steamcommunity/public/images/apps/8930/fbe80c4743e226f0bf65559c91b12953d4446808.jpg"></a>
		</div>
    </div>
    <div>Sid Meier's Civilization V</div>
    <span title="Time spent playing during the last two weeks"><?php echo $civ5['time']['hoursLast2Weeks']; ?> hrs</span> / <span title="Total time spent playing"><?php echo $civ5['time']['hoursOnRecord']; ?> hrs</span>
    <br>Achievements: <span title="Unlocked achievements"><?php echo $civ5['achievements']['unlocked']; ?></span> / <span title="Total number of achievements in the game"><?php echo $civ5['achievements']['total']; ?></span>
</div>