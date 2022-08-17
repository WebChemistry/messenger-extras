<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras\Bus;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class WrappedCommandBus implements CommandBus
{

	public function __construct(
		private MessageBusInterface $bus,
	)
	{
	}

	public function dispatch(object $message, array $stamps = []): Envelope
	{
		return $this->bus->dispatch($message, $stamps);
	}

}
