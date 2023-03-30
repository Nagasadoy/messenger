<?php

namespace App\Command;

use App\Model\User\Entity\User\UserRepository;
use Knp\Snappy\Pdf;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;

#[AsCommand(
    name: 'app:send-email',
    description: 'send email command',
)]
class SendEmailCommand extends Command
{
    private readonly MailerInterface $mailer;
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly Environment $twig,
        private readonly Pdf $pdf,
        MailerInterface $mailer,
        string $name = null)
    {
        $this->mailer = $mailer;
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
//        $io->note(sprintf('You passed an argument: %s', $arg1));

        $users = $this->userRepository->findAll();

        $io->progressStart(count($users));

        foreach ($users as $user) {

            $html = $this->twig->render('email/report.html.twig', [
                'name' => $user->getEmail()
            ]);

            $pdf = $this->pdf->getOutputFromHtml($html);

            $email = (new TemplatedEmail())
                ->from(new Address('nagasadoy@gmail.com', 'Nagasadoy'))
                ->to(new Address($user->getEmail(), $user->getEmail()))
                ->subject('Test mail')
                ->htmlTemplate('email/welcome.html.twig')
                ->attach($pdf, sprintf('weekly-report-%s.pdf', date('Y-m-d')));

            $this->mailer->send($email);

            $io->progressAdvance();
        }
        $io->progressFinish();
        $io->success('Finish');

        return Command::SUCCESS;
    }
}
