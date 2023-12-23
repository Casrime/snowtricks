<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

final class Mail
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function send(string $to, string $subject, string $template, array $context = []): void
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@snowtricks.com')
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context)
        ;

        $this->mailer->send($email);
    }
}
