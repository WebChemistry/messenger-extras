<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras\Transport;

interface MessageTransport
{

	public function getTransportName(): string;

}
