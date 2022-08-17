<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras\Bus;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;

interface CommandBus extends MessageBusInterface
{

}
