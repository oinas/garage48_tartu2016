<?php

/** Throw out 404 error, so search engine crawler will ignore this */
header('HTTP/1.0 404 Not Found', true, 404);

$HTML[] = <<<EOF
<h1>Requested page /{$PAGE} does not exist!</h1>

This error occurs for following reasons:
<ul>
	<li>Page has been moved or deleted
	<li>User entered wrong URL
	<li>Referred link is wrong
</ul>
EOF;
