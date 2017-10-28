<?php
namespace Database\Schema\Column\Interfaces;
/**
* @author 		Peter Taiwo
* @version 		1.0.0
* @package 		Schema.Column.ColumnInterface
* @copyright 	MIT License
* Copyright (c) 2017 PhoxPHP
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

interface ColumnInterface {

	function char($field, $length);

	function varchar($field, $length);

	function tinytext($field);

	function text($field);

	function smalltext($field);

	function mediumtext($field);

	function longtext($field);

	function tinyblob($field);

	function mediumblob($field);

	function blob($field);

	function longblob($field);

	function enum($field, array $data=[]);

	function binary($field, $length);

	function varbinary($field, $length);

	function int($field, $length);

	function tinyint($field, $length);

	function smallint($field, $length);

	function mediumint($field, $length);

	function bigint($field, $length);

	function decimal($field, $length);

	function float($field, $length);

	function double($field, $length);

	function date($field);

	function datetime($field);

	function timestamp($field);

	function time($field);

	function autoincrement();

	function null($null);

	function isdefault($default);

	function unsigned();

	function primary();

	function foreignkey(array $keys=[]);

	function reference($referencedTable='', array $columns=[]);

	function ondelete($referenceOption='');

	function onupdate($referenceOption='');

	function charset($charset='');

	function collate($collate='');

	function comment($comment='');

	function index();

	function unique();

	function createdAt();

	function updatedAt();

	function deletedAt();

	function isDeleted();

	function appendColumn($field, $length='', $text);

	function appendAttribute($attribute);

}