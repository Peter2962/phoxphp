<?php
namespace Package\View\Engines\Lite;

use Lite\Exceptions\InvalidArgumentQuotesException;

class Resolver {

	/**
	* @var 		$accessList
	* @access 	private
	*/
	private 	$accessList = ['Quotes']; 

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct() {}

	/**
	* @param 	$leftQuote <String>
	* @param 	$rightQuote <String>
	* @access 	public
	* @return 	void
	*/
	public static function resolveQuotes($leftQuote='', $rightQuote='') {
		if ($leftQuote !== $rightQuote) {
			throw new InvalidArgumentQuotesException(sprintf("Unable to parse arguments with left quote {%s} and right quote {%s}", $leftQuote, $rightQuote));
		}
	}

}