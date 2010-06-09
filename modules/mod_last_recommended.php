<?php
/**
 * @version $Header$
 * @package recommends
 * @subpackage modules
 */

/**
 * Initial Setup
 */
include_once( RECOMMENDS_PKG_PATH.'LibertyRecommends.php' );

// moduleParams contains lots of goodies: extract for easier handling
extract( $moduleParams );

$listHash = array(
	//user_id can not be used here cause it picks up the value from whomever is browsing. This needs to be changed to a unique prop like user and the getList method needs to change to recognize it. See bitblogpost.php and blog post module for an example -wjames5
	//'user_id'      => $gQueryUserId,
	'max_records'  => $moduleParams['module_rows'],
	'parse_data'   => TRUE,
	'content_type' => !empty( $module_params['content_type'] ) ? $module_params['content_type'] : NULL,
	'timeout'      => $gBitSystem->getUTCTime() - ( $gBitSystem->getConfig( 'recommends_timout_days', 15 ) * RECOMMENDS_TIMEOUT_DAYS_SCALE ),
	'recommends'   => $gBitSystem->getConfig( 'recommends_minimum_recommends', '10' ),
);
$recommend = new LibertyRecommends();
$gBitSmarty->assign( 'modLastRecommends', $recommend->getList( $listHash ));
$gBitSmarty->assign( 'maxPreviewLength', ( !empty( $modParams['max_preview_length'] ) ? $modParams['max_preview_length'] : 100 ));
?>
