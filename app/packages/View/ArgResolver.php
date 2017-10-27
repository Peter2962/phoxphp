<?php
namespace Package\View;

abstract class ArgResolver {

	const 		DS = DIRECTORY_SEPARATOR;

	/**
	* @param 	$template String
	* @access 	public
	* @return 	String
	*/
	public static function getResolvedTemplatePath($template='') {
		$path = str_replace('.', ArgResolver::DS, $template);
		return htmlFile($path);
	}

	/**
	* @param 	$script String
	* @access 	public
	* @return 	String
	*/
	public static function getResolvedScriptPath($script='') {
		$path = str_replace('.', ArgResolver::DS, $script);
		return phpFile($path);
	}

}