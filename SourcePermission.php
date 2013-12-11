<?php
/**
 * SourcePermission adds a new user-right (compatible with Lockdown extension).
 * @version 0.1 - 2012/07/05
 *
 * @link http://www.mediawiki.org/wiki/Extension:SourcePermission Documentation
 *
 * @file SourcePermission.php
 * @author Toniher
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'SourcePermission',
	'version' => '0.1',
	'url' => 'https://www.mediawiki.org/wiki/Extension:SourcePermission',
	'author' => array( 'Toniher' ),
	'descriptionmsg' => 'sourcepermission-desc',
);

$wgExtensionMessagesFiles['SourcePermission'] = dirname( __FILE__ ) . '/SourcePermission.i18n.php';
#$wgHooks['EditPage::showEditForm:initial'][] = 'wfEditPre';
$wgHooks['ArticleAfterFetchContent'][] = 'wfEditPreLock';

$wgGroupPermissions['*']['source'] = false;
$wgGroupPermissions['user'         ]['source'] = false;
$wgGroupPermissions['autoconfirmed']['source'] = false;
$wgGroupPermissions['bot'          ]['source'] = true; // registered bots
$wgGroupPermissions['sysop'        ]['source'] = true;
#$wgAvailableRights[] = 'source';


$wgNamespacePermissionLockdown[NS_MAIN]['source'] = array('sysop');


function wfEditPre ( $editPage, $output ) {

	global $wgUser;
	global $wgGroupPermissions;
	global $wgNamespacePermissionLockdown;

	$titlePage = $editPage->getTitle();
	$namespace = $titlePage->getNamespace();
	
	$user_groups = $wgUser->getEffectiveGroups();

	// First group permissions
	$allowsource = 0;

	foreach ( $user_groups as $user_group ) {

		if ( isset($wgGroupPermissions[$user_group]['source']) ) {
			if ( $wgGroupPermissions[$user_group]['source'] ) {
				$allowsource = 1;
				break;
			}
		}
	}

	// Later namespacce permissions
	foreach ( $user_groups as $user_group ) {

		if ( isset( $wgNamespacePermissionLockdown[$namespace]['source'] ) ) {
			
			$allowed_array = $wgNamespacePermissionLockdown[$namespace]['source'];
			if ( in_array($user_group, $allowed_array ) ) {
				$allowsource = 1;
				break;
			}
		}
	}

	if ( $allowsource < 1 )  {
		throw new PermissionsError( "sourcepermission", array() );
		return false;
	}
	else {
		return true;
	}
}

function wfEditPreLock ( $article, &$content ) {

	global $wgUser;


	#if ( $action == 'edit' && !$wgUser->isAllowed('source') ) {
	#	$content = "";
	#}
	#return true;

	global $wgGroupPermissions;
	global $wgNamespacePermissionLockdown;

	$titlePage = $article->getTitle();

	$namespace = $titlePage->getNamespace();
	
	$user_groups = $wgUser->getEffectiveGroups();

	$allowsource = 0;

	// Handle images when protected
	if ( $namespace == 6 ) return true;

	$action = "";

	if ( class_exists( 'MediaWiki' ) ) {
		$class = new MediaWiki();
		$action = $class->getAction();
	}

	else { return true; }


	// First group permissions
	foreach ( $user_groups as $user_group ) {

		if ( isset( $wgGroupPermissions[$user_group]['source'] )  ) {
			if ( $wgGroupPermissions[$user_group]['source'] ) {
				$allowsource = 1;
				break;
			}
		}
	}

	// Later namespacce permissions
	foreach ( $user_groups as $user_group ) {

		if ( isset( $wgNamespacePermissionLockdown[$namespace]['source'] ) ) {
			
			$allowed_array = $wgNamespacePermissionLockdown[$namespace]['source'];
			if ( in_array( $user_group, $allowed_array ) ) {
				$allowsource = 1;
				break;
			}
		}
	}

	if ( $allowsource < 1 && $action== 'edit' )  {
		$content = "";
	}

	return true;
	
}

