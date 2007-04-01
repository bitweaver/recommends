<?php
require_once( "../bit_setup_inc.php" );
require_once( RECOMMENDS_PKG_PATH."LibertyRecommends.php" );

$gBitSystem->verifyPackage( 'recommends' );

/*
	if ( !empty($_REQUEST['content_type_guid']) ){
		//forces return of $contentList from get_content_list_inc.php
		$_REQUEST['output'] = 'raw';
		include_once( LIBERTY_PKG_PATH.'list_content.php' );
		$gBitSmarty->assign_by_ref('listcontent', $contentList["data"]);
	}
*/

//get content types in database list  
if( empty( $contentTypes ) ) {
	$contentTypes = array( '' => tra( 'All Content' ) );
	foreach( $gLibertySystem->mContentTypes as $cType ) {
		$contentTypes[$cType['content_type_guid']] = $cType['content_description'];
	}
}

$recommends = new LibertyRecommends();

$listHash = $_REQUEST;
$listHash['timeout'] = $gBitSystem->getUTCTime() - ($gBitSystem->getConfig('recommends_timout_days', 15) * RECOMMENDS_TIMEOUT_DAYS_SCALE);
$listHash['recommends'] = $gBitSystem->getConfig('recommends_minimum_recommends', '10');
$recommendedContent = $recommends->getList( $listHash );

$gBitSmarty->assign_by_ref('contentTypes', $contentTypes);

$gBitSmarty->assign( 'recommendedContent', $recommendedContent );
$gBitSmarty->assign( 'listInfo', $listHash['listInfo'] );
$gBitSystem->display( 'bitpackage:recommends/recommended.tpl', tra( 'Recommended Content' ) );
?>
