<?php
namespace Package\Cache\Interfaces;

interface DriverInterface {

	public function getName();
	public function register();
	public function add($key='', $value='', $duration='');
	public function get($key='');
	public function exists($key='');
	public function delete($key='');
	public function getCreatedDate($key='');
	public function getExpirationDate($key='');
	public function hasExpired($key='');
	public function increment($value='');
	public function decrement($value='');

}