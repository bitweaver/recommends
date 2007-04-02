<?php
require_once( "../bit_setup_inc.php" );
require_once( RECOMMENDS_PKG_PATH."LibertyRecommends.php" );

$gBitSystem->verifyPackage( 'recommends' );

$recommends = new LibertyRecommends();

$listHash = $_REQUEST;
$listHash['timeout'] = $gBitSystem->getUTCTime() - ($gBitSystem->getConfig('recommends_timout_days', 15) * RECOMMENDS_TIMEOUT_DAYS_SCALE);
$listHash['recommends'] = $gBitSystem->getConfig('recommends_minimum_recommends', '10');
$recommendedContent = $recommends->getList( $listHash );

$gBitSmarty->assign( 'recommendedContent', $recommendedContent );
$gBitSmarty->assign( 'listInfo', $listHash['listInfo'] );
$contentTypes = array( '' => 'All Content' );
foreach( $gLibertySystem->mContentTypes as $cType ) {
	if( $gBitSystem->getConfig( 'recommends_recommend_'.$cType['content_type_guid'] ) ) {
		$contentTypes[$cType['content_type_guid']] = $cType['content_description'];
	}
}
$gBitSmarty->assign( 'contentTypes', $contentTypes );
$gBitSystem->display( 'bitpackage:recommends/recommended.tpl', tra( 'Recommended Content' ) );
?>
