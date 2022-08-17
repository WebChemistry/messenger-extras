<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras\Attribute;

use Attribute;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

#[Attribute(Attribute::TARGET_CLASS)]
final class Condition implements ConditionExpr
{

	/**
	 * @param array<string, mixed> $arguments
	 */
	public function __construct(
		public string $condition,
		public array $arguments = [],
	)
	{
	}

	/**
	 * @param mixed[] $values
	 */
	public function isValid(ExpressionLanguage $expressionLanguage, array $values): bool
	{
		$values['args'] = (object) $this->arguments;

		return $expressionLanguage->evaluate($this->condition, $values) === true;
	}

}
