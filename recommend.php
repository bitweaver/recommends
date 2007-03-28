<?php
/**
* $Header: /cvsroot/bitweaver/_bit_recommends/recommend.php,v 1.1 2007/03/28 01:01:05 nickpalmer Exp $
* date created 2006/02/10
* @author xing <xing@synapse.plus.com>
* @version $Revision: 1.1 $ $Date: 2007/03/28 01:01:05 $
* @package recommends
*/

/**
 * Setup
 */
require_once( "../bit_setup_inc.php" );
$gBitSystem->verifyPackage( 'recommends' );
$recommendsfeed = array();

if( @BitBase::verifyId( $_REQUEST['content_id'] ) && @BitBase::verifyId( $_REQUEST['recommends_recommending'] ) ) {
	if( $tmpObject = LibertyBase::getLibertyObject( $_REQUEST['content_id'] ) ) {
		// check if this feature allows recommending
		if( $gBitSystem->isFeatureActive( 'recommends_recommend_'.$tmpObject->getContentType() ) ) {
			$recommendsfeed = array();
			$recommends = new LibertyRecommends( $tmpObject->mContentId );

			if( !$gBitUser->isRegistered() ) {
				$recommendsfeed['error'] = tra( "You need to log in to recommend." );
			} else {
				if( $recommends->store( $_REQUEST ) ) {
					//$recommendsfeed['success'] = tra( "Thank you for recommending." );
				} else {
					$recommendsfeed['error'] = $recommends->mErrors;
				}
			}
		}
	}
	// get up to date reading
	$recommends->load();
	$serviceHash = array_merge( $tmpObject->mInfo, $recommends->mInfo, $recommends->getUserRecommending( $tmpObject->mContentId ) );
	$gBitSmarty->assign( 'serviceHash', $serviceHash );
} else {
	$recommendsfeed['warning'] = tra( "There was a problem trying to recommend." );
}
$gBitSmarty->assign( "recommendsfeed", $recommendsfeed );

if( $gBitSystem->isAjaxRequest() ) {
	echo $gBitSmarty->fetch( 'bitpackage:recommends/recommends_inline_service.tpl' );
} elseif( !empty( $tmpObject ) ) {
	header( "Location:".$tmpObject->getDisplayUrl() );
	die;
}
?>
