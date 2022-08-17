<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Dispatch
{

	/**
	 * @param class-string $message
	 * @param string[] $arguments
	 */
	public function __construct(
		public string $message,
		public array $arguments = [],
		public ?ConditionExpr $condition = null,
	)
	{
	}

}
