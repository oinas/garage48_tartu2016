<?php

/** Throw out 404 error, so search engine crawler will ignore this */
header('HTTP/1.0 403 Forbidden', true, 404);

$HTML[] = <<<EOF
<div class="front-text">

<h1>Requested page access is forbidden!</h1>

This error occurs for following reasons:
<ul>
	<li>User has logged out of system and tries to access page inside
	<li>Browser has been closed
	<li>Session expired
</ul>

</div>
EOF;
