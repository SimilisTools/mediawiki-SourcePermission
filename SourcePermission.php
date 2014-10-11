<?php
/**
 * SourcePermission adds a new user-right (compatible with Lockdown extension).
 * @version 0.2 - 2014/10/12
 * @version 0.1 - 2012/07/05
 *
 * @link https://www.mediawiki.org/wiki/User:Toniher Documentation
 *
 * @file SourcePermission.php
 * @author Toniher
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

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
$GLOBALS['wgHooks']['EditPage::showEditForm:initial'][] = 'wfEditPre';

$GLOBALS['wgAvailableRights'][] = 'source';

$GLOBALS['wgGroupPermissions']['*']['source'] = false;
$GLOBALS['wgGroupPermissions']['sysop']['source'] = true;
$GLOBALS['wgGroupPermissions']['user'         ]['source'] = false;
$GLOBALS['wgGroupPermissions']['autoconfirmed']['source'] = false;
$GLOBALS['wgGroupPermissions']['bot'          ]['source'] = true; // registered bots
$GLOBALS['wgGroupPermissions']['sysop'        ]['source'] = true;

// LockDown part
$GLOBALS['wgNamespacePermissionLockdown'][NS_MAIN]['source'] = array('sysop');

function wfEditPre ( $editPage, $output ) {

	global $wgUser;
	global $wgGroupPermissions;
	global $wgNamespacePermissionLockdown;

	$titlePage = $editPage->getTitle();
	$namespace = $titlePage->getNamespace();
	
	$user_groups = $wgUser->getEffectiveGroups();

	// First group permissions
	$allowsource = 0;

	if ( $wgUser->isAllowed( 'source' ) ) {
		$allowsource = 1;
	}

	// Later namespace, Lockdown, permissions
	foreach ( $user_groups as $user_group ) {

		if ( isset( $wgNamespacePermissionLockdown[$namespace]['source'] ) ) {
			
			$allowed_array = $wgNamespacePermissionLockdown[$namespace]['source'];
			if ( in_array( $user_group, $allowed_array ) ) {
				$allowsource = 1;
				break;
			}
		}
	}

	if ( $allowsource < 1 )  {
		throw new PermissionsError("sourcepermission", array());
		return false;
	}
	else {
		return true;
	}
}


