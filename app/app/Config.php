<?php
/**
* MIT License
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:

* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.

* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*/

/**
* @author 	Peter Taiwo
* @package 	App\Config
*/

namespace App;

use App\AppManager;
use RuntimeException;

class Config
{

	/**
	* @var 		$app
	* @access 	protected
	*/
	protected 	$app;

	/**
	* @var 		$configPath
	* @access 	private
	*/
	private 	$configPath = 'public'.DS.'config'.DS;

	/**
	* @var 		$config
	* @access 	private
	*/
	private 	$config = '';

	/**
	* @param 	$config <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct($config='')
	{
		$this->config = $config;
	}

	/**
	* Returns either a configuration key value or an array of configuration
	* from @param $config only if it is an array.
	*
	* @param 	$config <String>
	* @param 	$key <String>
	* @param 	$parameters <Array>
	* @access 	public
	* @return 	Mixed
	*/
	public function get($config='', $key='', Array $parameters=[])
	{
		$path = $this->configPath;
			
		if ($this->config !== '' && !is_array($this->config)) {
			$key = $config;
			$config = $this->config;
		}

		if (!file_exists(AppManager::appLibExt($path . $config))) {
			throw new RuntimeException(
				sprintf(
					'Configuration file %s does not exist in %s',
					$config,
					$path . $config
				)
			);
		}

		if (sizeof(array_keys($parameters)) > 0) {
			foreach(array_keys($parameters) as $key) {
				$$key = $parameters[$key];
			}
		}
		
		$loadedConfig = include AppManager::appLibExt($path . $config);

		if (gettype($loadedConfig) !== 'array') {
			return;
		}

		return ($loadedConfig[$key]) ?? $loadedConfig;
	}

}