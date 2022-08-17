<?php declare(strict_types = 1);

namespace WebChemistry\MessengerExtras\DI;

use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Fmasa\Messenger\DI\MessengerExtension as FmasaMessengerExtension;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransportFactory;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;
use Symfony\Component\Messenger\Transport\Sync\SyncTransportFactory;
use WebChemistry\MessengerExtras\Attribute\AttributeDispatcher;
use WebChemistry\MessengerExtras\Bridge\Doctrine\DoctrineEventSubscriber;
use WebChemistry\MessengerExtras\Bus\CommandBus;
use WebChemistry\MessengerExtras\Bus\WrappedCommandBus;
use WebChemistry\MessengerExtras\SendersLocator;

final class MessengerExtrasExtension extends CompilerExtension
{

	private string|int $extensionName;

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'buses' => Expect::structure([
				'command' => Expect::string(),
				'commandClass' => Expect::string(WrappedCommandBus::class),
			]),
			'doctrine' => Expect::structure([
				'bus' => Expect::string(),
			]),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		/** @var stdClass $config */
		$config = $this->getConfig();

		$builder->addDefinition($this->prefix('transportFactory.doctrine'))
			->setFactory(DoctrineTransportFactory::class)
			->addTag('messenger.transportFactory');

		$builder->addDefinition($this->prefix('transportFactory.sync'))
			->setFactory(SyncTransportFactory::class)
			->addTag('messenger.transportFactory');

		if ($config->buses->command) {
			$bus = $builder->getDefinition(sprintf('%s.%s.bus', $this->getExtensionName(), $config->buses->command))
				->setAutowired(false);

			$builder->addDefinition($this->prefix('bus.command'))
				->setType(CommandBus::class)
				->setFactory($config->buses->commandClass, [$bus]);
		}


	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		/** @var stdClass $config */
		$config = $this->getConfig();

		if ($name = $builder->getByType(SendersLocatorInterface::class)) {
			$builder->removeDefinition($name);
		}

		$builder->addDefinition($this->prefix('sendersLocator'))
			->setType(SendersLocatorInterface::class)
			->setFactory(SendersLocator::class);

		if ($config->doctrine->bus) {
			$dispatcher = $builder->addDefinition($this->prefix('doctrine.attribute.dispatcher'))
				->setFactory(
					AttributeDispatcher::class,
					[sprintf('@%s.%s.bus', $this->getExtensionName(), $config->doctrine->bus)]
				)
				->setAutowired(false);

			$subscriber = $builder->addDefinition($this->prefix('doctrine.subscriber'))
				->setFactory(DoctrineEventSubscriber::class, [$dispatcher])
				->setAutowired(false);

			$service = $builder->getDefinitionByType(EntityManagerInterface::class);

			assert($service instanceof ServiceDefinition);

			$service->addSetup('?->getEventManager()->addEventSubscriber(?)', ['@self', $subscriber]);
		}
	}

	private function getExtensionName(): string|int
	{
		if (!isset($this->extensionName)) {
			$name = array_key_first($this->compiler->getExtensions(FmasaMessengerExtension::class));
			if ($name === null) {
				throw new DomainException(sprintf('Extension %s is not registered.', FmasaMessengerExtension::class));
			}

			$this->extensionName = $name;
		}

		return $this->extensionName;
	}

}
