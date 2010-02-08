<?php
require_once( "../kernel/setup_inc.php" );
require_once( RECOMMENDS_PKG_PATH."LibertyRecommends.php" );

$gBitSystem->verifyPackage( 'recommends' );

$recommends = new LibertyRecommends();

$listHash = $_REQUEST;
if ($gBitSystem->isFeatureActive('recommends_timeout_days')) {
	$listHash['timeout'] = $gBitSystem->getUTCTime() - ($gBitSystem->getConfig('recommends_timout_days', 15) * RECOMMENDS_TIMEOUT_DAYS_SCALE);
}
$listHash['recommends'] = $gBitSystem->getConfig('recommends_minimum_recommends', '10');
$recommendedContent = $recommends->getList( $listHash );

$gBitSmarty->assign( 'recommendedContent', $recommendedContent );
$gBitSmarty->assign( 'listInfo', $listHash['listInfo'] );
$contentType = 'Content';
$contentTypes = array( '' => 'All Content' );
$contentSelect = '';
foreach( $gLibertySystem->mContentTypes as $cType ) {
	if( $gBitSystem->isFeatureActive( 'recommends_recommend_'.$cType['content_type_guid'] ) ) {
		$contentTypes[$cType['content_type_guid']] = $cType['content_description'];
		if (isset($_REQUEST['content_type']) && $_REQUEST['content_type'] == $cType['content_type_guid']){
			$contentType = $cType['content_description']."s";
			$contentSelect = $cType['content_type_guid'];
		}
	}
}
$gBitSmarty->assign( 'recommendContentTypes', $contentTypes );
$gBitSmarty->assign_by_ref( 'listInfo', $_REQUEST['listInfo'] ); 
$gBitSmarty->assign( 'contentType', $contentType);
$gBitSmarty->assign( 'contentSelect', $contentSelect);
$gBitSystem->display( 'bitpackage:recommends/recommended.tpl', tra( 'Recommended Content' ) , array( 'display_mode' => 'display' ));
?>
