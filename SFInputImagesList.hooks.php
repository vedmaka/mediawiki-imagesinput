<?php

/**
 * Hooks for SFInputImagesList extension
 *
 * @file
 * @ingroup Extensions
 */
class SFInputImagesListHooks
{

	public static function onExtensionLoad()
	{
		
	}

	public static function onParserFirstCallInit( $parser )
	{
		global $sfgFormPrinter;

		$sfgFormPrinter->registerInputType( 'SFInputImagesList' );

		return true;
	}

}
