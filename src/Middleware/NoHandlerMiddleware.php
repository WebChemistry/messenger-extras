<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras\Middleware;

use Fmasa\Messenger\LazyHandlersLocator;
use Nette\Utils\Arrays;
use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use WebChemistry\MessengerExtras\Attribute\Message;

final class NoHandlerMiddleware implements MiddlewareInterface
{


	public function __construct(
		private LazyHandlersLocator $lazyHandlersLocator,
	)
	{
	}

	public function handle(Envelope $envelope, StackInterface $stack): Envelope
	{
		$message = $envelope->getMessage();
		$reflection = new ReflectionClass($message);
		$attributes = array_map(
			fn (ReflectionAttribute $attribute): Message => $attribute->newInstance(),
			$reflection->getAttributes(Message::class),
		);

		$noHandler = Arrays::some(
			$attributes,
			fn (Message $message) => $message->allowNoHandler,
		);

		if ($noHandler) {
			$handlers = $this->lazyHandlersLocator->getHandlers($envelope);

			if (is_array($handlers) && count($handlers) === 0) {
				return $envelope;
			}
		}

		return $stack->next()->handle($envelope, $stack);
	}

}
