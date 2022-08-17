<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras\Bridge\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use WebChemistry\MessengerExtras\Attribute\AttributeDispatcher;
use WebChemistry\MessengerExtras\Attribute\Type\DoctrineEventType;

final class DoctrineEventSubscriber implements EventSubscriber
{

	public function __construct(
		private AttributeDispatcher $attributeDispatcher,
	)
	{
	}

	/**
	 * @return mixed[]
	 */
	public function getSubscribedEvents(): array
	{
		return [
			Events::postPersist,
			Events::postRemove,
			Events::preRemove,
			Events::postUpdate,
		];
	}

	/**
	 * @param LifecycleEventArgs<EntityManagerInterface> $args
	 */
	public function postPersist(LifecycleEventArgs $args): void
	{
		$this->attributeDispatcher->dispatch($args->getObject(), ['event' => DoctrineEventType::Persist]);
	}

	/**
	 * @param LifecycleEventArgs<EntityManagerInterface> $args
	 */
	public function postRemove(LifecycleEventArgs $args): void
	{
		$this->attributeDispatcher->dispatch($args->getObject(), ['event' => DoctrineEventType::Remove]);
	}

	/**
	 * @param LifecycleEventArgs<EntityManagerInterface> $args
	 */
	public function preRemove(LifecycleEventArgs $args): void
	{
		$this->attributeDispatcher->dispatch($args->getObject(), ['event' => DoctrineEventType::PreRemove]);
	}

	/**
	 * @param LifecycleEventArgs<EntityManagerInterface> $args
	 */
	public function postUpdate(LifecycleEventArgs $args): void
	{
		$this->attributeDispatcher->dispatch($args->getObject(), ['event' => DoctrineEventType::Update]);
	}

}
