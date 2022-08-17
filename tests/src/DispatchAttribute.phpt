<?php declare(strict_types = 1);

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Messenger\MessageBus;
use Tester\Assert;
use WebChemistry\Messenger\Attribute\AttributeDispatcher;
use WebChemistry\Messenger\Attribute\Condition;
use WebChemistry\Messenger\Attribute\Dispatch;
use WebChemistry\Messenger\Attribute\Type\DoctrineEventType;

require __DIR__ . '/../bootstrap.php';

class Message
{

	public function __construct(
		public int $id,
	)
	{
	}

}

#[Dispatch(Message::class, arguments: ['self.id'], condition: new Condition('self.id != 1'))]
class Foo
{

	public function __construct(
		public int $id = 1,
	)
	{
	}

}

#[Dispatch(
	Message::class,
	arguments: ['self.id'],
	condition: new Condition('event == arguments.event', ['event' => DoctrineEventType::Persist])
)]
class FooEvent
{

	public function __construct(
		public int $id = 1,
	)
	{
	}

}

$dispatcher = new AttributeDispatcher(new MessageBus());

Assert::equal([], $dispatcher->createMessages(new Foo()));
Assert::equal([new Message(2)], $dispatcher->createMessages(new Foo(2)));
Assert::equal([new Message(2)], $dispatcher->createMessages(new FooEvent(2), ['event' => DoctrineEventType::Persist]));
Assert::equal([], $dispatcher->createMessages(new FooEvent(2), ['event' => DoctrineEventType::Update]));
