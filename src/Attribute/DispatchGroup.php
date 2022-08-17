<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class DispatchGroup
{

	/**
	 * @param Dispatch[] $dispatches
	 */
	public function __construct(
		public array $dispatches,
		public ?ConditionExpr $condition = null,
	)
	{
	}

}
