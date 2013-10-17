#!/usr/bin/php
<?php
require realpath(__DIR__.'/../../initialize.php');
require __DIR__.'/../vendor/optionparse.php';
require __DIR__.'/../mfwTwigTemplate.php';


$parser = new Optionparse(array(
		'description' => 'compile twig template to php.',
		'arguments' => 'templatename',
		));
$parser->addOption('input',array(
		'short_name' => '-i',
		'long_name' => '--input',
		'help_name' => 'TWIGFILE',
		'description' => 'Source file used instead of stdin',
		));
$parser->addOption('dir',array(
		'short_name' => '-d',
		'long_name' => '--dir',
		'help_name' => 'BASEDIR',
		'description' => 'Base directory to output file.',
		'default' => APP_ROOT.'/data/',
		));
$parser->addOption('help',array(
		'short_name' => '-h',
		'long_name' => '--help',
		'description' => 'Show this message and exit.',
		));
$opts = $parser->parse();
if($opts['help']){
	$parser->displayUsage();
	exit(0);
}


if(empty($opts['_arguments_'])){
	fputs(STDERR,"{$argv[0]}: missing templatename\n");
	exit(-1);
}
$name = $opts['_arguments_'][0];

if($opts['input']){
	$input = $opts['input'];
	if(!file_exists($input)){
		fputs(STDERR,"{$argv[0]}: input file not found\n");
		exit(-1);
	}
}
else{
	$input = 'php://stdin';
}

$twig = new mfwTwigEnv();

if(!$twig->setBaseDir($opts['dir'])){
	fputs(STDERR,"{$argv[0]}: output directory is invalid.\n");
	exit(-1);
}


$output = $twig->getCacheFilename($name);

if(!is_dir(dirname($output))){
	fputs(STDERR,"{$argv[0]}: output directory not found.\n(".dirname($output).")\n");
	exit(-1);
}
echo "$input -> $output\n";

$src = file_get_contents($input);
$out = $twig->compileSource($src,$name);

file_put_contents($output,$out);

