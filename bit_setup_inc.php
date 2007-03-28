<?php
global $gBitSystem, $gBitSmarty, $gPreviewStyle;

$registerHash = array(
	'package_name' => 'recommends',
	'package_path' => dirname( __FILE__ ).'/',
	'service' => 'recommendation',
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'recommends' ) ) {
	require_once( RECOMMENDS_PKG_PATH.'LibertyRecommends.php' );

	// if we are using a text browser theme, make sure not to use ajax
	if( $gPreviewStyle == 'lynx' ) {
		$gBitSystem->setConfig( 'recommends_use_ajax', FALSE );
	}

	$menuHash =
		array(
			'package_name' => RECOMMENDS_PKG_NAME,
			'menu_template' => 'bitpackage:recommends/menu_recommends.tpl',
			'index_url' => RECOMMENDS_PKG_URL.'index.php',
			);
	$gBitSystem->registerAppMenu($menuHash);

	$gLibertySystem->registerService( 'recommendation', RECOMMENDS_PKG_NAME, array(
//		'content_display_function'  => 'recommends_content_display',
		'content_load_sql_function' => 'recommends_content_load_sql',
		'content_list_sql_function' => 'recommends_content_list_sql',
		'content_expunge_function'  => 'recommends_content_expunge',
		'content_body_tpl'          => 'bitpackage:recommends/recommends_inline_service.tpl',
		//		'content_list_sort_tpl'     => 'bitpackage:recommends/recommends_list_sort_service.tpl',
		//		'content_list_tpl'          => 'bitpackage:recommends/recommends_list_service.tpl',
	) );
}
?>
