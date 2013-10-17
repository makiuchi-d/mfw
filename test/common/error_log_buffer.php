<?php
/** @file
 * error_log testing utilities.
 */

/// 
$stored_error_log = array();

function error_log_store($message, $message_type=0, $destination=null, $extra_headers=null)
{
	global $stored_error_log;
	$stored_error_log[] = $message;
}

runkit_function_rename('error_log','error_log_original');
runkit_function_add(
	'error_log',
	'$message, $message_type=0, $destination=null, $extra_headers=null',
	'return error_log_original($message,$message_type,$destination,$extra_headers);');

function elb_start()
{
	runkit_function_redefine(
		'error_log',
		'$message, $message_type=0, $destination=null, $extra_headers=null',
		'return error_log_store($message,$message_type,$destination,$extra_headers);');
}

function elb_get_clean()
{
	global $stored_error_log;
	$ret = implode("\n",$stored_error_log);
	$stored_error_log = array();

	runkit_function_redefine(
		'error_log',
		'$message, $message_type=0, $destination=null, $extra_headers=null',
		'return error_log_original($message,$message_type,$destination,$extra_headers);');

	return $ret;
}

