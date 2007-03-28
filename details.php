<?php
/**
* $Header: /cvsroot/bitweaver/_bit_recommends/details.php,v 1.2 2007/03/28 15:18:35 nickpalmer Exp $
* date created 2006/02/10
* @author xing <xing@synapse.plus.com>
* @version $Revision: 1.2 $ $Date: 2007/03/28 15:18:35 $
* @package recommends
*/

/**
 * Setup
 */
require_once( "../bit_setup_inc.php" );
require_once( RECOMMENDS_PKG_PATH."LibertyRecommends.php" );

$gBitSystem->verifyPackage( 'recommends' );

if( !@BitBase::verifyId( $_REQUEST['content_id'] ) && !@BitBase::verifyId( $_REQUEST['user_id'] )) {
	header( "Location: ".BIT_ROOT_URL );
}

if( @BitBase::verifyId( $_REQUEST['content_id'] )) {
	// content details
	$recommends = new LibertyRecommends( $_REQUEST['content_id'] );
	$recommends->getRecommendingDetails(TRUE);
	$gBitSmarty->assign( 'recommendsDetails', $recommends->mInfo );
} elseif( @BitBase::verifyId( $_REQUEST['user_id'] )) {
	// user details
	$recommends = new LibertyRecommends();
	$listHash = array(
		'user_id' => $_REQUEST['user_id'],
	);
	$userRecommendings = $recommends->getList( $listHash );

	// calculate this users average recommendings
	$sum = 0;
	foreach( $userRecommendings as $recommending ) {
		if ($recommending['user_recommending'] > 0) {
			$sum += $recommending['user_recommending'];
		}
	}
	if (count($userRecommendings) > 0) {
	  $average = round($sum / count( $userRecommendings ) * 100);
	}
	else {
	  $average = 0;
	}
	$gBitSmarty->assign( 'userAverage', $average );
	$gBitSmarty->assign( 'userRecommendings', $userRecommendings );
}

$gBitSystem->display( 'bitpackage:recommends/details.tpl', tra( 'Details of Recommended Content' ) );
?>
