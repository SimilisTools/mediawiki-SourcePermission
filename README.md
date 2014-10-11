mediawiki-SourcePermission
==========================

MediaWiki extension for disabling source access to wikitext pages

REQUIRES Lockdown extension: https://www.mediawiki.org/wiki/Extension:Lockdown

For not allowing source not to be shown to not logged in users, in includes/EditPage.php the following line must be commented:

$this->showTextbox( $text, 'wpTextbox1', array( 'readonly' ) );

