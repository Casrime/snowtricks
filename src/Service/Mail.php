<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

final readonly class Mail
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function send(string $to, string $subject, string $template, array $context = []): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from('no-reply@snowtricks.com')
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context)
        ;

        $this->mailer->send($templatedEmail);
    }
}
