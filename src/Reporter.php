<?php

declare(strict_types=1);

namespace Nette\TypeFixer;


interface Reporter
{
	const TYPE_ERROR = 'ERROR';

	const TYPE_WARNING = 'WARNING';

	const TYPE_FIXED = 'FIXED';

	function add(string $message, string $type): void;

	function progress(string $message): void;
}
