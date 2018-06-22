<?php
/**
* @author 		Peter Taiwo <peter@phoxphp.com>
* @package 		App\CommandTemplateParser
* @license 		MIT License
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*/

namespace App;

use App\Exceptions\TemplateTagNotFoundException;

class CommandTemplateParser
{

	/**
	* Checks for phx:* tags in a string.
	*
	* @param 	$tags <Array>
	* @param 	$string <String>
	* @access 	public
	* @return 	<Mixed>
	* @static
	*/
	public static function checkTags(Array $tags, String $string)
	{
		foreach($tags as $tag) {
			if (!preg_match_all($tag, $string)) {
				throw new TemplateTagNotFoundException(sprintf('template tag [`%s`] does not exist.', $tag));
			}
		}
	}

}