<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras\Attribute;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

interface ConditionExpr
{

	/**
	 * @param mixed[] $values
	 */
	public function isValid(ExpressionLanguage $expressionLanguage, array $values): bool;

}
