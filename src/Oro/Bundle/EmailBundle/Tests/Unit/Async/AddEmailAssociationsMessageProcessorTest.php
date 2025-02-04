<?php

namespace Oro\Bundle\EmailBundle\Tests\Unit\Async;

use Oro\Bundle\EmailBundle\Async\AddEmailAssociationsMessageProcessor;
use Oro\Bundle\EmailBundle\Async\Topics;
use Oro\Bundle\MessageQueueBundle\Entity\Job;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Job\JobRunner;
use Oro\Component\MessageQueue\Transport\Message;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback;
use Psr\Log\LoggerInterface;

class AddEmailAssociationsMessageProcessorTest extends \PHPUnit\Framework\TestCase
{
    public function testCouldBeConstructedWithRequiredArguments()
    {
        new AddEmailAssociationsMessageProcessor(
            $this->createMock(MessageProducerInterface::class),
            $this->createMock(JobRunner::class),
            $this->createMock(LoggerInterface::class)
        );
    }

    public function testShouldRejectMessageIfEmailIdsIsMissing()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('critical')
            ->with('Got invalid message');

        $message = new Message();
        $message->setBody(json_encode([
            'targetClass' => 'class',
            'targetId' => 123,
        ], JSON_THROW_ON_ERROR));

        $processor = new AddEmailAssociationsMessageProcessor(
            $this->createMock(MessageProducerInterface::class),
            $this->createMock(JobRunner::class),
            $logger
        );

        $result = $processor->process($message, $this->createMock(SessionInterface::class));

        $this->assertEquals(MessageProcessorInterface::REJECT, $result);
    }

    public function testShouldRejectMessageIfTargetClassMissing()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('critical')
            ->with('Got invalid message');

        $message = new Message();
        $message->setBody(json_encode([
            'emailIds' => [],
            'targetId' => 123,
        ], JSON_THROW_ON_ERROR));

        $processor = new AddEmailAssociationsMessageProcessor(
            $this->createMock(MessageProducerInterface::class),
            $this->createMock(JobRunner::class),
            $logger
        );

        $result = $processor->process($message, $this->createMock(SessionInterface::class));

        $this->assertEquals(MessageProcessorInterface::REJECT, $result);
    }

    public function testShouldRejectMessageIfTargetIdMissing()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('critical')
            ->with('Got invalid message');

        $message = new Message();
        $message->setBody(json_encode([
            'emailIds' => [],
            'targetClass' => 'class',
        ], JSON_THROW_ON_ERROR));

        $processor = new AddEmailAssociationsMessageProcessor(
            $this->createMock(MessageProducerInterface::class),
            $this->createMock(JobRunner::class),
            $logger
        );

        $result = $processor->process($message, $this->createMock(SessionInterface::class));

        $this->assertEquals(MessageProcessorInterface::REJECT, $result);
    }

    public function testShouldProcessAddAssociation()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())
            ->method('critical');
        $logger->expects($this->once())
            ->method('info')
            ->with('Sent "2" messages');

        $producer = $this->createMock(MessageProducerInterface::class);
        $producer->expects($this->exactly(2))
            ->method('send')
            ->withConsecutive(
                [
                    'oro.email.add_association_to_email',
                    [
                        'emailId'     => 1,
                        'targetClass' => 'class',
                        'targetId'    => 123,
                        'jobId'       => 12345
                    ]
                ],
                [
                    'oro.email.add_association_to_email',
                    [
                        'emailId'     => 2,
                        'targetClass' => 'class',
                        'targetId'    => 123,
                        'jobId'       => 54321
                    ]
                ]
            );

        $body = [
            'emailIds' => [1,2],
            'targetClass' => 'class',
            'targetId' => 123
        ];

        $message = new Message();
        $message->setBody(json_encode($body, JSON_THROW_ON_ERROR));
        $message->setMessageId('message-id');

        $jobRunner = $this->createMock(JobRunner::class);
        $jobRunner->expects($this->once())
            ->method('runUnique')
            ->with('message-id', 'oro.email.add_association_to_emails' . ':class:123:'.md5('1,2'))
            ->willReturnCallback(function ($ownerId, $name, $callback) use ($jobRunner) {
                $callback($jobRunner);

                return true;
            });

        $jobRunner->expects($this->exactly(2))
            ->method('createDelayed')
            ->withConsecutive(
                ['oro.email.add_association_to_email'.':class:123:1'],
                ['oro.email.add_association_to_email'.':class:123:2']
            )
            ->willReturnOnConsecutiveCalls(
                new ReturnCallback(function ($name, $callback) use ($jobRunner) {
                    $job = new Job();
                    $job->setId(12345);

                    $callback($jobRunner, $job);
                }),
                new ReturnCallback(function ($name, $callback) use ($jobRunner) {
                    $job = new Job();
                    $job->setId(54321);

                    $callback($jobRunner, $job);
                })
            );

        $processor = new AddEmailAssociationsMessageProcessor(
            $producer,
            $jobRunner,
            $logger
        );

        $result = $processor->process($message, $this->createMock(SessionInterface::class));

        $this->assertEquals(MessageProcessorInterface::ACK, $result);
    }

    public function testShouldReturnSubscribedTopics()
    {
        $this->assertEquals(
            [Topics::ADD_ASSOCIATION_TO_EMAILS],
            AddEmailAssociationsMessageProcessor::getSubscribedTopics()
        );
    }
}
