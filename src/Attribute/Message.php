<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras\Attribute;

use Attribute;
use WebChemistry\MessengerExtras\Transport\MessageTransport;

#[Attribute(Attribute::TARGET_CLASS)]
class Message
{

	public function __construct(
		public readonly MessageTransport $transport,
		public readonly bool $allowNoHandler = false,
	)
	{
	}

}
