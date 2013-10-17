<?php

echo "METHOD: {$_SERVER['REQUEST_METHOD']}\n";

$headers = getallheaders();
foreach($headers as $k => $v){
	echo "HEADER: $k: $v\n";
}

$stdin = file_get_contents('php://input');
echo "STDIN: $stdin\n";

