<?php

namespace Oro\Bundle\ImapBundle;

use Oro\Bundle\ImapBundle\Async\Topics;
use Oro\Bundle\ImapBundle\DependencyInjection\Compiler\CredentialsNotificationSenderPass;
use Oro\Bundle\MessageQueueBundle\DependencyInjection\Compiler\AddTopicDescriptionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * OroImapBundle bundle class
 */
class OroImapBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $addTopicPass = AddTopicDescriptionPass::create()
            ->add(Topics::CLEAR_INACTIVE_MAILBOX, 'Clear inactive mailbox')
            ->add(Topics::SYNC_EMAIL, 'Synchronization email via IMAP')
            ->add(Topics::SYNC_EMAILS, 'Synchronization emails via IMAP')
        ;
        $container->addCompilerPass($addTopicPass);
        $container->addCompilerPass(new CredentialsNotificationSenderPass());
    }
}
