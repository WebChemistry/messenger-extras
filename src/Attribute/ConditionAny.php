<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras\Attribute;

use Attribute;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

#[Attribute(Attribute::TARGET_CLASS)]
final class ConditionAny implements ConditionExpr
{

	/**
	 * @param Condition[] $conditions
	 */
	public function __construct(
		public array $conditions,
	)
	{
	}

	/**
	 * @param mixed[] $values
	 */
	public function isValid(ExpressionLanguage $expressionLanguage, array $values): bool
	{
		foreach ($this->conditions as $condition) {
			if ($condition->isValid($expressionLanguage, $values)) {
				return true;
			}
		}

		return false;
	}

}
