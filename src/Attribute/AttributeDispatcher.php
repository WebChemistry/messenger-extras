<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras\Attribute;

use ReflectionClass;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class AttributeDispatcher
{

	private ExpressionLanguage $expressionLanguage;

	public function __construct(
		private MessageBusInterface $bus,
		?ExpressionLanguage $expressionLanguage = null,
	)
	{
		$this->expressionLanguage = $expressionLanguage ?? new ExpressionLanguage();
	}

	/**
	 * @param mixed[] $values
	 * @return Envelope[]
	 */
	public function dispatch(object $object, array $values = []): array
	{
		$envelopes = [];

		foreach ($this->createMessages($object, $values) as $message) {
			$envelopes[] = $this->bus->dispatch($message);
		}

		return $envelopes;
	}

	/**
	 * @param mixed[] $values
	 * @return object[]
	 */
	public function createMessages(object $object, array $values = []): array
	{
		$values['this'] = $object;
		$messages = [];

		$reflection = new ReflectionClass($object);
		foreach ($reflection->getAttributes(DispatchGroup::class) as $attribute) {
			/** @var DispatchGroup $group */
			$group = $attribute->newInstance();

			if ($group->condition?->isValid($this->expressionLanguage, $values) === false) {
				continue;
			}

			foreach ($group->dispatches as $dispatch) {
				$message = $this->processDispatch($dispatch, $values);

				if ($message) {
					$messages[] = $message;
				}
			}
		}

		foreach ($reflection->getAttributes(Dispatch::class) as $attribute) {
			/** @var Dispatch $dispatch */
			$dispatch = $attribute->newInstance();

			$message = $this->processDispatch($dispatch, $values);

			if ($message) {
				$messages[] = $message;
			}
		}

		return $messages;
	}

	/**
	 * @param mixed[] $values
	 */
	private function processDispatch(Dispatch $dispatch, array $values): ?object
	{
		if ($dispatch->condition?->isValid($this->expressionLanguage, $values) === false) {
			return null;
		}

		return new ($dispatch->message)(... array_map(
			fn (string $expression): mixed => $this->expressionLanguage->evaluate($expression, $values),
			$dispatch->arguments,
		));
	}

}
