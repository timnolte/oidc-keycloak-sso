#!/usr/bin/php
<?php // phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
/**
 * Used to rename html files to md files.
 * Also fixes the urls in the md files.
 */

/**
 * Lists the generated documentation files.
 *
 * @param string $dir The startging point to search.
 * @param array  $results The results placed in this array.
 * @return array the list of files.
 */
function getDirContents( $dir = null, &$results = array() ) {
	$files = scandir( $dir );

	foreach ( $files as $key => $value ) {
		$path = realpath( $dir . DIRECTORY_SEPARATOR . $value );
		if ( ! is_dir( $path ) ) {
			if ( ! preg_match( '/\.html$/', $path ) ) {
				continue;
			}
			$results[] = $path;
		} elseif ( $value != '.' && $value != '..' ) {
			getDirContents( $path, $results );
		}
	}

	return $results;
}
$xmlContent = file_get_contents( 'phpdoc.dist.xml' );
$xml = simplexml_load_string( $xmlContent );
$target = (array) $xml->transformer->target;
$target = $target[0];

$htmlFiles = getDirContents( $target );

foreach ( $htmlFiles as $htmlFilePath ) {
	print( "$htmlFilePath\n" );
	$content = file_get_contents( $htmlFilePath );
	// .html)
	$content = str_replace( '.html)', '.md)', $content );
	// .html#property_id)
	$content = preg_replace( '/\.html(\#[\w\_]+)\)/', '.md$1)', $content );
	file_put_contents( $htmlFilePath, $content );
	$mdFilePath = preg_replace( '/\.html$/', '.md', $htmlFilePath );
	rename( $htmlFilePath, $mdFilePath );
	print( "$mdFilePath\n" );
}
