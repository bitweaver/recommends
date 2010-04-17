<?php
// $Header: /cvsroot/bitweaver/_bit_recommends/admin/admin_recommends_inc.php,v 1.8 2010/04/17 15:36:08 wjames5 Exp $
// Copyright (c) 2005 bitweaver Recommends
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

require_once( RECOMMENDS_PKG_PATH.'LibertyRecommends.php' );
$gBitSmarty->assign_by_ref( 'feedback', $feedback = array() );

$gBitSmarty->assign( 'recommendsIconSizes', array( 48 => 'large', 36=>'medium', 24=>'small') );

$formRecommendsDisplayOptions = array(
	"recommends_in_view" => array(
		'label' => 'Recommends In View',
		'note' => 'Shows the Recommendation buttons in the "view" location',
		'type' => 'toggle',
	),
	"recommends_in_nav" => array(
		'label' => 'Recommends In Nav',
		'note' => 'Shows the Recommendation buttons in the "nav" location',
		'type' => 'toggle',
	),
	"recommends_in_body" => array(
		'label' => 'Recommends In Body',
		'note' => 'Shows the Recommendation buttons in the "body" location',
		'type' => 'toggle',
	),
);
$gBitSmarty->assign( 'formRecommendsDisplayOptions', $formRecommendsDisplayOptions );


$formRecommendsOptions = array(
	"recommends_minimum_recommends" => array(
		'label' => 'Minimum Level',
		'note' => 'The minimum level of recommendations required before the content shows up in the recommends list',
		'type' => 'input',
	),
	"recommends_max_changes" => array(
		'label' => 'Max Changes',
		'note' => 'The maximum number of changes a user can make to their recommendation.',
		'type' => 'input',
	),
	"recommends_change_timeout" => array(
		'label' => 'Change Timeout',
		'note' => 'The maximum time after initial recommendation that the recommendation can be changed in minutes',
		'type' => 'input',
	),
	"recommends_recommend_period" => array(
		'label' => 'Recommend Period',
		'note' => 'The time the content is open for recommendation in hours. Set to 0 for no timeout.',
		'type' => 'input',
	),
	"recommends_timeout_days" => array(
		'label' => 'Days Before Timeout',
		'note' => 'The maximum number of days to show content in the recommends list. Set to 0 for no timeout.',
		'type' => 'input',
	),
	"recommends_use_ajax" => array(
		'label' => 'Use Ajax',
		'note' => 'Choosing this option will decrease load times when recommending, however requires modern browsers with javascript enabled to allow for recommendations.',
		'type' => 'toggle',
	),
);
$gBitSmarty->assign( 'formRecommendsOptions', $formRecommendsOptions );

// allow selection of what packages can have ratings
$exclude = array( 'bituser', 'tikisticky', 'pigeonholes' );
foreach( $gLibertySystem->mContentTypes as $cType ) {
	if( !in_array( $cType['content_type_guid'], $exclude ) ) {
		$formRecommendable['guids']['recommends_recommend_'.$cType['content_type_guid']]  = $gLibertySystem->getContentTypeName( $cType['content_type_guid'] );
	}
}

if( !empty( $_REQUEST['recommends_preferences'] ) ) {
	$recommends = array_merge($formRecommendsDisplayOptions, $formRecommendsOptions);
//	$recommends = $formRecommendsOptions;
	foreach( $recommends as $item => $data ) {
		if( $data['type'] == 'numeric' ) {
			simple_set_int( $item, RECOMMENDS_PKG_NAME );
		} elseif( $data['type'] == 'toggle' ) {
			simple_set_toggle( $item, RECOMMENDS_PKG_NAME );
		} elseif( $data['type'] == 'input' ) {
			simple_set_value( $item, RECOMMENDS_PKG_NAME );
		}
		simple_set_int( 'recommends_icon_width', RECOMMENDS_PKG_NAME );
		simple_set_int( 'recommends_icon_height', RECOMMENDS_PKG_NAME );
	}
	foreach( array_keys( $formRecommendable['guids'] ) as $recommendable ) {
		$gBitSystem->storeConfig( $recommendable, ( ( !empty( $_REQUEST['recommendable_content'] ) && in_array( $recommendable, $_REQUEST['recommendable_content'] ) ) ? 'y' : NULL ), RECOMMENDS_PKG_NAME );
	}
}

// check the correct packages in the package selection
foreach( $gLibertySystem->mContentTypes as $cType ) {
	if( $gBitSystem->getConfig( 'recommends_recommend_'.$cType['content_type_guid'] ) ) {
		$formRecommendable['checked'][] = 'recommends_recommend_'.$cType['content_type_guid'];
	}
}
$gBitSmarty->assign( 'formRecommendable', $formRecommendable );

?>
