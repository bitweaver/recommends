<?php
$recommends = new LibertyRecommends();
$listHash = array(
	'user_id' => $gQueryUserId,
);
$userRecommendings = $recommends->getList( $listHash );

// calculate this users average recommending
$sum = 0;
foreach( $userRecommendings as $recommending ) {
	$sum += $recommending['user_recommending'];
}

if (count( $userRecommendings ) > 0 ) {
	$average = round( $sum / count( $userRecommendings ));
} else {
	$average = 0;
}

$recommends = $gBitSystem->getConfig( 'recommends_used_in_display', 2 );
$pixels = $recommends *  $gBitSystem->getConfig( 'recommends_icon_width', 48 );
$average_pixels = $average * $pixels / 100;
$gBitSmarty->assign( 'average_pixels', $average_pixels );
?>
