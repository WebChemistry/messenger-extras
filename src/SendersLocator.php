<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras;

use Fmasa\Messenger\Exceptions\SenderNotFound;
use Fmasa\Messenger\Transport\SendersLocator as FmasaSendersLocator;
use Nette\DI\Container;
use ReflectionClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;
use WebChemistry\MessengerExtras\Attribute\Message;

final class SendersLocator implements SendersLocatorInterface
{

	public function __construct(
		private Container $container,
	)
	{
	}

	/**
	 * @return iterable<string, SenderInterface>
	 */
	public function getSenders(Envelope $envelope): iterable
	{
		$message = $envelope->getMessage();
		$reflection = new ReflectionClass($message);

		$attributes = $reflection->getAttributes(Message::class);

		if (!$attributes) {
			trigger_error(
				sprintf('Message %s does not have attribute %s.', $message::class, Message::class),
				E_USER_WARNING
			);
		}

		foreach ($attributes as $attribute) {
			/** @var Message $instance */
			$instance = $attribute->newInstance();
			$transport = $instance->transport->getTransportName();

			yield $transport => $this->getSenderByTransport($transport);
		}
	}

	public function getSenderByTransport(string $transport): SenderInterface
	{
		foreach ($this->container->findByTag(FmasaSendersLocator::TAG_SENDER_ALIAS) as $serviceName => $serviceAlias) {
			if ($serviceAlias !== $transport) {
				continue;
			}

			/** @var SenderInterface */
			return $this->container->getService($serviceName);
		}

		throw SenderNotFound::withAlias($transport);
	}

}
