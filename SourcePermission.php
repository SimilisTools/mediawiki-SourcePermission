<?php
/**
 * SourcePermission adds a new user-right (compatible with Lockdown extension).
 * @version 0.2 - 2014/10/11
 *
 * @link https://www.mediawiki.org/wiki/Extension:SourcePermission Documentation
 *
 * @file SourcePermission.php
 * @author Toniher
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

/** It requires Lockdown extension !! **/

$GLOBALS['wgExtensionCredits']['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'SourcePermission',
	'version' => '0.2',
	'url' => 'https://www.mediawiki.org/wiki/User:Toniher',
	'author' => array( 'Toniher' ),
	'descriptionmsg' => 'sourcepermission-desc',
);

$GLOBALS['wgMessagesDirs']['SourcePermission'] = __DIR__ . '/i18n';
$GLOBALS['wgExtensionMessagesFiles']['SourcePermission'] = dirname( __FILE__ ) . '/SourcePermission.i18n.php';
$GLOBALS['$wgHooks']['ArticleAfterFetchContentObject'][] = 'wfEditPreLock';

$GLOBALS['wgAvailableRights'][] = 'source';

$GLOBALS['wgGroupPermissions']['*']['source'] = false;
$GLOBALS['wgGroupPermissions']['user'         ]['source'] = false;
$GLOBALS['wgGroupPermissions']['autoconfirmed']['source'] = false;
$GLOBALS['wgGroupPermissions']['bot'          ]['source'] = true; // registered bots
$GLOBALS['wgGroupPermissions']['sysop'        ]['source'] = true;


$GLOBALS['wgNamespacePermissionLockdown'][NS_MAIN]['source'] = array('sysop');


function wfEditPreLock ( $article, &$content ) {
	
	global $wgUser;

	global $wgGroupPermissions;
	global $wgNamespacePermissionLockdown;

	$action = "";

	if ( class_exists('MediaWiki') ) {
		$class = new MediaWiki();
		$action = $class->getAction();
	} else { 
		return true; 
	}
	
	if ( $action == 'edit' && !$wgUser->isAllowed('source') ) {
		throw new PermissionsError("sourcepermission", array());
	}


	$titlePage = $article->getTitle();

	$namespace = $titlePage->getNamespace();
	
	$user_groups = $wgUser->getEffectiveGroups();

	$allowsource = 0;

	// Handle images when protected -> Change to WhiteList NS
	if ( $namespace == 6 ) return true;

	// First group permissions
	foreach ( $user_groups as $user_group ) {

		if ( isset( $wgGroupPermissions[$user_group]['source'] ) ) {
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
		throw new PermissionsError("sourcepermission", array());
	}

	return true;
	
}

