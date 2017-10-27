<?php
##################################################
# RouteParameterValidator will be used to validate
# route parameters.
##################################################
namespace Package\Http\Router\Validators;

use Package\Http\Router\Validators\Interfaces\ValidatorInterface;
use Package\Http\Router\Exceptions\InvalidValidatorSizeException;
use Package\Http\Router\Validators\Bag as ValidatorsRepo;
use Package\Http\Router\Factory;

class RouteParameterValidator implements ValidatorInterface {

	/**
	* @var 		$validatorEventTrigger
	* @access 	private
	*/
	private 	$validatorEventTrigger=false;

	/**
	* @var 		$factory
	* @access 	private
	*/
	private 	$factory;

	/**
	* @param 	$factory Http\Router\Factory
	* @access 	public
	* @return 	void
	*/
	public function __construct(Factory $factory) {
		$this->factory = $factory;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getValidatorEvent() {
		return intval($this->validatorEventTrigger);
	}

	/**
	* This method will be used to validate url slugs/parameters based on the configuration
	* provided from routes.
	*
	* @todo 	Return fallback function if route slug/parameter does not match.
	* @access 	public
	* @return 	void
	*/
	public function dispatchValidator() {
		$validatorsRepo = new ValidatorsRepo();
		$canValidate = $this->factory->config('Router', 'allow_slug_validation');
		if (!$canValidate) {
			return;
		}

		$configuredRoute = (Object) $this->factory->getConfiguredRoute();
		$route = $configuredRoute->route;
		$callback = $configuredRoute->callback;
		$slugs = $configuredRoute->parameters;
		$validators = $configuredRoute->validator;
		$sharedMethod = $configuredRoute->shared_method;


		array_map(function($key) use ($validators, $slugs, $validatorsRepo, $sharedMethod, $route) {
			if (array_key_exists($key, $validators)) {
				$validator = $validators[$key];
				$validatorLength = strlen($validator);
				if ($validator[0] !== "/") {
					$validator = "/$validator";
				}

				if ($validator[$validatorLength - 1] !== "/") {
					$validator = "$validator/";
				}

				$validatorFallbackObjectArguments = $this->factory->config('Router', 'slug_validation_options');

				if (!preg_match($validator, $slugs[$key])) {
					$route = "/$route";
					$closures = $validatorsRepo->from($sharedMethod)->getClosure($route);
					$this->validatorEventTrigger = true;
					if ($closures) {
						array_map(function($closure) use ($validatorFallbackObjectArguments, $slugs) {
							$arguments = $validatorFallbackObjectArguments['fallback_method_default_arguments'];
							return call_user_func_array($closure, array_map(function($slug) { return $slug; }, array_values($slugs)));
						}, $closures);
						return true;
					}

					trigger_error(app()->load('en_msg')->getMessage('route_param_failed', ['param' => $slugs[$key], 'param_value' => $validator]));
					return false;
				}
			}
		}, array_keys($slugs));
	}

}