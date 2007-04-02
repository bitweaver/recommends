<?php
/**
* $Header: /cvsroot/bitweaver/_bit_recommends/LibertyRecommends.php,v 1.7 2007/04/02 15:05:26 wjames5 Exp $
* date created 2006/02/10
* @author xing <xing@synapse.plus.com>
* @version $Revision: 1.7 $ $Date: 2007/04/02 15:05:26 $
* @package recommends
*/

/**
 * Setup
 */
require_once( KERNEL_PKG_PATH.'BitBase.php' );

define('RECOMMENDS_PERIOD_SCALE', 3600); // 60 * 60
define('RECOMMENDS_TIMEOUT_CHANGE_SCALE', 60);
define('RECOMMENDS_TIMEOUT_DAYS_SCALE', 86400); // 24 * 60 * 60

/**
 * Liberty Recommends
 * 
 * @package recommends
 */
class LibertyRecommends extends LibertyBase {
	var $mContentId;

	/**
	 * Initiate Liberty Recommends
	 * 
	 * @param array $pContentId Content id of the item being recommended
	 * @access public
	 * @return void
	 */
	function LibertyRecommends( $pContentId=NULL ) {
		LibertyBase::LibertyBase();
		$this->mContentId = $pContentId;
	}

	/**
	 * Load the data from the database
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function load() {
		if( $this->isValid() ) {
			global $gBitSystem, $gBitUser;
			$query = "
				SELECT  lc.`created`, lc.`content_id`, rcms.`recommending` AS recommends_recommending, rcms.`votes` AS recommends_votes, rcm.`recommending_time` AS recommends_time, rcm.`changes` AS recommends_changes, rcm.`recommending` AS recommends_user_recommending
				FROM `".BIT_DB_PREFIX."liberty_content` lc
				LEFT JOIN `".BIT_DB_PREFIX."recommends_sum` rcms ON (lc.`content_id` = rcms.`content_id`)
				LEFT JOIN `".BIT_DB_PREFIX."recommends` rcm ON (lc.`content_id` = rcm.`content_id` AND rcm.`user_id` = ? )
				WHERE lc.`content_id`=?";
			$this->mInfo = $this->mDb->getRow( $query, array( $gBitUser->mUserId, $this->mContentId ) );
		}
		return( count( $this->mInfo ) );
	}

	/**
	 * get list of all recommended content
	 *
	 * @param $pListHash contains array of items used to limit search results
	 * @param $pListHash[sort_mode] column and orientation by which search results are sorted
	 * @param $pListHash[find] search for a pigeonhole title - case insensitive
	 * @param $pListHash[max_records] maximum number of rows to return
	 * @param $pListHash[offset] number of results data is offset by
	 * @access public
	 * @return array of recommended content
	 **/
	function getList( &$pListHash ) {
		global $gBitSystem, $gBitUser, $gLibertySystem;

		$ret = $bindVars = array();
		$where = $join = $select = '';

		// set custom sorting before we call prepGetList()
		if( !empty( $pListHash['sort_mode'] )) {
			$order = " ORDER BY ".$this->mDb->convertSortmode( $pListHash['sort_mode'] )." ";
		} else {
			// set a default sort_mode
			$order = " ORDER BY rcm.`recommending` DESC";
		}

		LibertyContent::prepGetList( $pListHash );

		if( !empty( $pListHash['user_id'] )) {
			$where      .= empty( $where ) ? ' WHERE ' : ' AND ';
			$where      .= " rcmh.`user_id`=? ";
			$bindVars[]  = $pListHash['user_id'];
			$select     .= ", rcmh.`recommending` AS `user_recommending`";
			$join       .= " LEFT JOIN `".BIT_DB_PREFIX."recommends` rcmh ON( rcm.`content_id` = rcmh.`content_id` AND rcmh.`recommending` != 0) ";
			$order       = " ORDER BY rcmh.`recommending` DESC";
		}

		if( !empty( $pListHash['timeout'] ) ) {
			$where	.= empty( $where) ? ' WHERE ' : ' AND ';
			$where	.= " lc.`created` >= ? ";
			$bindVars[] = $pListHash['timeout'];
		}

		if( !empty( $pListHash['recommends'] ) ) {
			$where .= empty( $where ) ? ' WHERE ' : ' AND ';
			$where .= " rcm.recommending >= ? ";
			$bindVars[] = $pListHash['recommends'];
		}

		if( !empty( $pListHash['find'] )) {
			$where      .= empty( $where ) ? ' WHERE ' : ' AND ';
			$where      .= " UPPER( lc.`title` ) LIKE ? ";
			$bindVars[]  = '%'.strtoupper( $pListHash['find'] ).'%';
		}

		if ( !empty( $pListHash['content_type'] ) ) {
			if( is_array( $pListHash['content_type'] ) ) {			
				$where      .= empty( $where ) ? ' WHERE ' : ' AND ';
				$where .= " lc.`content_type_guid` IN( ".implode( ',',array_fill( 0,count( $pListHash['content_type'] ),'?' ) )." )";
				$bindVars = array_merge ( $bindVars, $pListHash['content_type_guid'] );
			}
			else {
				$where      .= empty( $where ) ? ' WHERE ' : ' AND ';
				$where .= " lc.`content_type_guid` = ? ";
				$bindVars[] = $pListHash['content_type'];
			}
		}
		
		$query = "
			SELECT rcm.*, lch.`hits`, lch.`last_hit`, lc.`event_time`, lc.`title`,
			lc.`last_modified`, lc.`content_type_guid`, lc.`ip`, lc.`created`, uu.`email`, uu.`login`, uu.`real_name` $select
			FROM `".BIT_DB_PREFIX."recommends_sum` rcm
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON ( lc.`content_id` = rcm.`content_id` )
				INNER JOIN		`".BIT_DB_PREFIX."users_users`			 uu ON ( uu.`user_id`			   = lc.`user_id` )
				LEFT JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON ( lc.`content_id` = lch.`content_id` )
			$join $where $order";
		$result = $this->mDb->query( $query, $bindVars, $pListHash['max_records'], $pListHash['offset'] );

		while( $aux = $result->fetchRow() ) {
			$type = &$gLibertySystem->mContentTypes[$aux['content_type_guid']];
			if( empty( $type['content_object'] )) {
				include_once( $gBitSystem->mPackages[$type['handler_package']]['path'].$type['handler_file'] );
				$type['content_object'] = new $type['handler_class']();
			}
			if( !empty( $gBitSystem->mPackages[$type['handler_package']] )) {
				$aux['display_link'] = $type['content_object']->getDisplayLink( $aux['title'], $aux );
				$aux['title']        = $type['content_object']->getTitle( $aux );
				$aux['display_url']  = $type['content_object']->getDisplayUrl( $aux['content_id'], $aux );
			}
			$ret[] = $aux;
		}

		$query = "
			SELECT COUNT( rcm.`content_id` )
			FROM `".BIT_DB_PREFIX."recommends` rcm
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON ( lc.`content_id` = rcm.`content_id` )
				LEFT JOIN `".BIT_DB_PREFIX."liberty_content_hits` lch ON ( lc.`content_id` = lch.`content_id` )
			$join $where";
		$pListHash['cant'] = $this->mDb->getOne( $query, $bindVars );

		LibertyContent::postGetList( $pListHash );
		return $ret;
	}

	/**
	 * Get the recommending history of a loaded content
	 * 
	 * @param boolean $pExtras loading the extras will get all users who have recommended in the past and their recommendings
	 * @access public
	 * @return TRUE on success, FALSE on failure
	 */
	function getRecommendingDetails( $pExtras = FALSE ) {
		if( $this->isValid() ) {
			global $gBitSystem;
			$query = "
				SELECT `recommending` AS `recommends_recommending`, votes AS `recommends_votes`, `content_id`
				FROM `".BIT_DB_PREFIX."recommends_sum`
				WHERE `content_id`=?";
			$obj = $this->getLibertyObject( $this->mContentId );
			$this->mInfo = $this->mDb->getRow( $query, array( $this->mContentId ) );
			$this->mInfo = array_merge( $this->mInfo, $obj->mInfo );
			$this->mInfo['display_url'] = $obj->getDisplayUrl();
			if( $pExtras ) {
				$query = "
					SELECT rcm.`content_id` as `hash_key`, rcm.`recommending`, uu.`login`, uu.`real_name`, uu.`user_id`
					FROM `".BIT_DB_PREFIX."recommends` rcm
						INNER JOIN `".BIT_DB_PREFIX."users_users` uu ON rcm.`user_id`=uu.`user_id`
					WHERE rcm.`content_id`=?
					ORDER BY rcm.`recommending` ASC";
				$this->mInfo['user_recommendings'] = $this->mDb->getAll( $query, array( $this->mContentId ) );
			}
		}
		return( count( $this->mInfo ) );
	}

	/**
	 * Quick method to get a nice summary of past recommendings for a given content
	 * 
	 * @param array $pContentId 
	 * @access public
	 * @return usable hash with a summary of recommendings of a given content id
	 */
	function getRecommendingSummary( $pContentId = NULL ) {
		if( !@BitBase::verifyId( $pContentId ) && $this->isValid() ) {
			$pContentId = $this->mContentId;
		}

		$ret['sum'] = $ret['count'] = 0;
		if( @BitBase::verifyId( $pContentId ) ) {
			$query = "
				SELECT
					rcmh.`recommending`,
					COUNT( rcmh.`recommending`) AS `update_count`
				FROM `".BIT_DB_PREFIX."recommends_history` rcmh
				WHERE rcmh.`content_id`=? AND rcmh.`recommending` != 0
				GROUP BY rcmh.`recommending`";
			$result = $this->mDb->getAll( $query, array( $pContentId ) );

			foreach( $result as $set ) {
				$ret['sum']    += $set['recommending'];
				$ret['count']  += $set['update_count'];
			}
		}
		return $ret;
	}

	/**
	 * @param array pParams hash of values that will be used to store the page
	 *
	 * @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	 * @access public
	 **/
	function store( &$pParamHash ) {
		global $gBitUser, $gBitSystem;
		if( $this->verify( $pParamHash ) ) {
			$table = BIT_DB_PREFIX."recommends";
			$table_sum = BIT_DB_PREFIX."recommends_sum";
			$this->mDb->StartTrans();			
			if( $this->getUserRecommending( $pParamHash['content_id'] )) {
				$locId = array( "content_id" => $pParamHash['recommends_store']['content_id'], "user_id" => $pParamHash['recommends_store']['user_id'] );
				unset($pParamHash['recommends_store']['recommending_time']);
				$result = $this->mDb->associateUpdate( $table, $pParamHash['recommends_store'], $locId );
				unset($locId['user_id']);
				$pParamHash['recommends_sum_store']['content_id'] = $pParamHash['recommends_store']['content_id'];
				$pParamHash['recommends_sum_store']['recommending'] = $this->mDb->getOne("SELECT SUM(`recommending`) FROM ".$table." WHERE `content_id` = ? ", array($pParamHash['recommends_sum_store']['content_id']));
				$pParamHash['recommends_sum_store']['votes'] = $this->mDb->getOne("SELECT COUNT(`recommending`) FROM ".$table." WHERE `content_id` = ? AND recommending != 0", array($pParamHash['recommends_sum_store']['content_id']));
				$result = $this->mDb->associateUpdate( $table_sum, $pParamHash['recommends_sum_store'], $locId);
			} else {
				$result = $this->mDb->associateInsert( $table, $pParamHash['recommends_store'] );
				$pParamHash['recommends_sum_store']['content_id'] = $pParamHash['recommends_store']['content_id'];
				$pParamHash['recommends_sum_store']['recommending'] = $this->mDb->getOne("SELECT SUM(recommending) FROM ".$table." WHERE `content_id` = ? ", array($pParamHash['recommends_store']['content_id']));
				$pParamHash['recommends_sum_store']['votes'] = $this->mDb->getOne("SELECT COUNT(`recommending`) FROM ".$table." WHERE `content_id` = ? AND recommending != 0", array($pParamHash['recommends_sum_store']['content_id']));
				
				if ($this->getRecommending( $pParamHash['content_id'] ) ) {
					$result = $this->mDb->associateUpdate( $table_sum, $pParamHash['recommends_sum_store'], array('content_id' => $pParamHash['recommends_sum_store']['content_id']));
				}
				else {
					$result = $this->mDb->associateInsert( $table_sum, $pParamHash['recommends_sum_store'] );
				}
			}
			$this->mDb->CompleteTrans();			
		}
		return( count( $this->mErrors )== 0 );
	}

	/**
	 * Make sure the data is safe to store
	 *
	 * @param array pParams reference to hash of values that will be used to store the page, they will be modified where necessary
	 * @return bool TRUE on success, FALSE if verify failed. If FALSE, $this->mErrors will have reason why
	 * @access private
	 **/
	function verify( &$pParamHash ) {
		global $gBitUser, $gBitSystem;

		if( $gBitUser->isRegistered() && $this->isValid() ) {
			$this->load();
			$timeout = $gBitSystem->getUTCTime() - ($gBitSystem->getConfig('recommends_recommend_period', 15) * RECOMMENDS_PERIOD_SCALE);
			$timeout_change = $gBitSystem->getUTCTime() - ($gBitSystem->getConfig('recommends_change_timeout', 1) * RECOMMENDS_TIMEOUT_CHANGE_SCALE);
			if( $this->mInfo['created'] > $timeout && (empty($this->mInfo['recommends_time']) || $this->mInfo['recommends_time'] > $timeout_change)) {
				if( empty($this->mInfo['recommends_changes']) || $this->mInfo['recommends_changes'] < $gBitSystem->getConfig('recommends_max_changes', 1) ) {
					$pParamHash['content_id'] = $this->mContentId;
					$pParamHash['recommending'] = $pParamHash['recommends_recommending'];
					if( ($pParamHash['recommending'] == 1 || $pParamHash['recommending'] == -1 || $pParamHash['recommending'] == 0) && $this->isValid() ) {			  	
						// recommends table
						$pParamHash['recommends_store']['content_id']  = $pParamHash['recommends_store']['content_id'] = ( int )$this->mContentId;
						$pParamHash['recommends_store']['recommending']      = ( int )$pParamHash['recommending'];
						$pParamHash['recommends_store']['recommending_time'] = ( int )BitDate::getUTCTime();
						$pParamHash['recommends_store']['user_id']     = ( int )$gBitUser->mUserId;
						$pParamHash['recommends_store']['changes']     = !isset($this->mInfo['recommends_changes']) ? 0 : $this->mInfo['recommends_changes'] + 1;
					} else {
						$this->mErrors['recommending_bad'] = tra("Invalid recommendation.");
					}
				} else {
					$this->mErrors['recommending_changes'] = tra('Maximum number of changes already made.');
				}
			}
			else {
				$this->mErrors['recommending_timeout'] = tra("Recommendation period has expired");
			}
		} else {
			$this->mErrors['unregistered'] = tra("You have to be registered to recommend content.");
		}
		return( count( $this->mErrors )== 0 );
	}

	/**
	 * Get the recommending of the currently active user for the specified content
	 * 
	 * @param array $pContentId 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getUserRecommending( $pContentId = NULL ) {
		global $gBitSystem, $gBitUser;
		$ret = FALSE;
		if( !@BitBase::verifyId( $pContentId ) && $this->isValid() ) {
			$pContentId = $this->mContentId;
		}

		if( @BitBase::verifyId( $pContentId ) ) {
			$query = "
				SELECT `recommending`
				FROM `".BIT_DB_PREFIX."recommends`
				WHERE `content_id`=? AND `user_id`=?";
			$ret = $this->mDb->getRow( $query, array( $pContentId, $gBitUser->mUserId ) );
		}
		return $ret;
	}

	/**
	 * Get the recommending for the specified content
	 * 
	 * @param array $pContentId 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getRecommending( $pContentId = NULL ) {
		global $gBitSystem, $gBitUser;
		$ret = FALSE;
		if( !@BitBase::verifyId( $pContentId ) && $this->isValid() ) {
			$pContentId = $this->mContentId;
		}

		if( @BitBase::verifyId( $pContentId ) ) {
			$query = "
				SELECT `recommending`
				FROM `".BIT_DB_PREFIX."recommends_sum`
				WHERE `content_id`=?";
			$ret = $this->mDb->getRow( $query, array( $pContentId ) );
		}
		return $ret;
	}

	/**
	 * Check if the mContentId is set and valid
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function isValid() {
		return( @BitBase::verifyId( $this->mContentId ) );
	}

	/**
	 * This function removes a recommends entry
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function expunge() {
		$ret = FALSE;
		if( $this->isValid() ) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."recommends` WHERE `content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			$query = "DELETE FROM `".BIT_DB_PREFIX."recommends_sum` WHERE `content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
		}
		return $ret;
	}
}

/********* SERVICE FUNCTIONS *********/

/**
 * Content list sql service function
 * 
 * @param array $pObject 
 * @access public
 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
 */
function recommends_content_list_sql( &$pObject ) {
	global $gBitSystem, $gBitUser, $gBitSmarty;

	if( !empty($pObject->mContentId) && $gBitSystem->isFeatureActive( 'recommends_recommend_'.$pObject->getContentType() ) ) {
		$ret['select_sql'] = ",
			lc.`content_id` AS `recommends_load`,
			rcms.`recommending` AS recommends_recommending,
			rcms.`votes` AS recommends_votes,
			rcm.`recommending` AS recommends_user_recommending ";
		$ret['join_sql'] = "
			LEFT JOIN `".BIT_DB_PREFIX."recommends_sum` rcms
				ON ( lc.`content_id`=rcms.`content_id` )
			LEFT JOIN `".BIT_DB_PREFIX."recommends` rcm
				ON ( lc.`content_id`=rcm.`content_id` AND rcm.`user_id`='".$gBitUser->mUserId."' )";
		return $ret;
	}
}

/**
 * Content load sql service function
 * 
 * @param array $pObject 
 * @access public
 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
 */
function recommends_content_load_sql( &$pObject ) {
	global $gBitSystem, $gBitUser, $gBitSmarty;
	if( $gBitSystem->isFeatureActive( 'recommends_recommend_'.$pObject->getContentType() ) ) {
		if( $gBitSystem->isFeatureActive( 'recommends_use_ajax' ) ) {
			$gBitSmarty->assign( 'loadAjax', TRUE );
		}
		$ret['select_sql'] = ",
			lc.`content_id` AS `recommends_load`,
			rcms.`recommending` AS recommends_recommending,
			rcms.`votes` AS recommends_votes,
			( rcm.`recommending` ) AS recommends_user_recommending,
			( rcm.`changes` ) AS recommends_changes,
			( rcm.`recommending_time` ) AS recommends_time
";
		$ret['join_sql'] = "
			LEFT JOIN `".BIT_DB_PREFIX."recommends_sum` rcms
				ON ( lc.`content_id`=rcms.`content_id` )
			LEFT JOIN `".BIT_DB_PREFIX."recommends` rcm
				ON ( lc.`content_id`=rcm.`content_id` AND rcm.`user_id`='".$gBitUser->mUserId."' )";
		$dt = $gBitSystem->getUTCTime();
		$gBitSmarty->assign('recommends_user_timeout', $dt - ($gBitSystem->getConfig('recommends_change_timeout', 1) * RECOMMENDS_TIMEOUT_CHANGE_SCALE));
		$gBitSmarty->assign('recommends_timeout', $dt - ($gBitSystem->getConfig('recommends_recommend_period', 15) * RECOMMENDS_PERIOD_SCALE));
		return $ret;
	}
}

/**
 * Content expunge sql service function
 * 
 * @param array $pObject 
 * @param array $pParamHash 
 * @access public
 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
 */
function recommends_content_expunge( &$pObject, &$pParamHash ) {
	$recommends = new LibertyRecommends( $pObject->mContentId );
	$recommends->expunge();
}
?>
