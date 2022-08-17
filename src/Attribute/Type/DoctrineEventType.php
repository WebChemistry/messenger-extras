<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras\Attribute\Type;

enum DoctrineEventType: string implements EventType
{

	case Persist = 'persist';
	case Update = 'update';
	case Remove = 'remove';
	case PreRemove = 'preRemove';

	public function getType(): string
	{
		return $this->value;
	}

}
