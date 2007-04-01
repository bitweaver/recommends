<?php
$tables = array(
	'recommends_sum' => "
		content_id I4 PRIMARY,
		recommending I2 NOTNULL,
		votes I4 NOTNULL
		CONSTRAINT '
			, CONSTRAINT `recommends_sum_content_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content`( `content_id` )'
	",
	'recommends' => "
		content_id I4 NOTNULL,
		user_id I4 NOTNULL,
		recommending I2 NOTNULL,
		changes I4 NOTNULL DEFAULT 0,
		recommending_time I8 NOTNULL DEFAULT 0
		CONSTRAINT '
			, CONSTRAINT `recommends_content_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content`( `content_id` )
			, CONSTRAINT `recommends_user_ref` FOREIGN KEY (`user_id`) REFERENCES `".BIT_DB_PREFIX."users_users`( `user_id` )'
	",
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( RECOMMENDS_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( RECOMMENDS_PKG_NAME, array(
	'description' => "A recommendation package that allows users to recommend any content using a basic interface.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( RECOMMENDS_PKG_NAME, array(
	array( 'p_recommends_send', 'Can make recommendations', 'registered',  RECOMMENDS_PKG_NAME ),
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( RECOMMENDS_PKG_NAME, array(
	//array( RECOMMENDS_PKG_NAME, "recommends_display_width", "125" ),
	array( RECOMMENDS_PKG_NAME, "recommends_in_body", "y" ),
	//array( RECOMMENDS_PKG_NAME, "recommends_in_view", "n" ),
	//array( RECOMMENDS_PKG_NAME, "recommends_in_nav", "n" ),
	array( RECOMMENDS_PKG_NAME, "recommends_timeout_days", "15"),
	array( RECOMMENDS_PKG_NAME, "recommends_max_changes", "1"),
	array( RECOMMENDS_PKG_NAME, "recommends_change_timeout", "1"),
	array( RECOMMENDS_PKG_NAME, "recommends_recommend_period", "48"),
	array( RECOMMENDS_PKG_NAME, "recommends_minimum_recommends", "10" ),
	array( RECOMMENDS_PKG_NAME, "recommends_icon_width", "48" ),
) );
?>