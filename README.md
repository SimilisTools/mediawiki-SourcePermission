# mediawiki-SourcePermission

MediaWiki extension for disabling source access to wikitext pages

## INSTALLATION

Via Packagist:

https://packagist.org/packages/mediawiki/source-permission

REQUIRES Lockdown extension: https://www.mediawiki.org/wiki/Extension:Lockdown

## EXTRA

For not allowing source to be shown to not logged in users, in includes/EditPage.php the following line must be commented:

$this->showTextbox( $text, 'wpTextbox1', array( 'readonly' ) );

