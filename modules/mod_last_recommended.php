<?php
/**
 * required setup
 */
if( !defined( 'MAX_RECOMMENDS_PREVIEW_LENGTH' ) ) {
	define ('MAX_RECOMMENDS_PREVIEW_LENGTH', 100);
}

include_once( RECOMMENDS_PKG_PATH.'LibertyRecommends.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

global $gBitSmarty, $gQueryUserId, $module_rows, $module_params, $gBitSystem;

$listHash = array( 'user_id' => $gQueryUserId, 'sort_mode' => 'created_desc', 'max_records' => $module_rows, 'parse_data' => TRUE );
if ( isset($module_params['content_type']) ){ $listHash['content_type'] = $module_params['content_type'];}
$listHash['timeout'] = $gBitSystem->getUTCTime() - ($gBitSystem->getConfig('recommends_timout_days', 15) * RECOMMENDS_TIMEOUT_DAYS_SCALE);
$listHash['recommends'] = $gBitSystem->getConfig('recommends_minimum_recommends', '10');

$recommend = new LibertyRecommends();
$ranking = $recommend->getList( $listHash );

$gBitThemes = new BitThemes();
$modParams = $gBitThemes->getModuleParameters('bitpackage:recommends/mod_last_recommended.tpl', $gQueryUserId);

$maxPreviewLength = (!empty($modParams['max_preview_length']) ? $modParams['max_preview_length'] : MAX_RECOMMENDS_PREVIEW_LENGTH);

$gBitSmarty->assign('maxPreviewLength', $maxPreviewLength);
$gBitSmarty->assign('modLastRecommends', $ranking);
$gBitSmarty->assign('modLastrecommendsTitle',(isset($module_params["title"])?$module_params["title"]:""));
$gBitSmarty->assign('recommendsPackageActive', $gBitSystem->isPackageActive('recommends'));
?>
