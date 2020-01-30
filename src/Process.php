<?php

namespace App;

use InstagramAPI\Instagram;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\Store\FlockStore;


class Process extends Command
{
    /** @var LockInterface $lockByProcess*/
    private $lockByProcess;

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $factory = new LockFactory(new FlockStore(sys_get_temp_dir()));
        $this->lockByProcess = $factory->createLock(Config::$lockResource);
    }

    protected function configure()
    {
        $this->setName('instagram-send-message')
            ->setDescription('Sending sweet message on instagram!')
            ->setHelp('')
            ->addArgument('targetUid', InputArgument::REQUIRED, 'Target user id.')
            ->addArgument('login', InputArgument::OPTIONAL, 'Login of the original user.')
            ->addArgument('pass', InputArgument::OPTIONAL, 'Password of the original user.')
            ->addArgument('message', InputArgument::OPTIONAL, 'Message.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dateLog = (new \DateTime('now', new \DateTimeZone('Europe/Moscow')))->format('[Y-m-d H:i:s]');

        if (!$this->lockByProcess->acquire()) {
            if ($output->isVerbose()) {
                $output->writeln($dateLog . ' The task is in progress. Please call the script later.');
            }
            return 0;
        }

        $timeLocker = new TimeLocker();
        if (!$timeLocker->isTimeHasCome()) {
            if ($output->isVerbose()) {
                $output->writeln($dateLog . ' The time has not come yet.');
            }
            return 0;
        }

        $message = new Message();
        $login = $input->getArgument('message') ?: Config::$login;
        $password = $input->getArgument('pass') ?: Config::$password;
        $msg = $input->getArgument('message') ?: $message->getMessage();
        $targetUid = $input->getArgument('targetUid');

        if (!$msg) {
            if ($output->isVerbose()) {
                $output->writeln($dateLog . ' Empty message.');
            }
            return 0;
        }

        $result = $this->processSendMessage($targetUid, $login, $password, $msg);
        if (!$result) {
            if ($output->isVerbose()) {
                $output->writeln($dateLog . ' Error login.');
            }
            return 0;
        }

        $message->updateIdLastMessage();
        $timeLocker->setNextTriggerDateTime();

        $output->writeln(date($dateLog) . ' Message sent successfully');
        return 1;
    }

    private function processSendMessage($targetUid, $login, $password, $message)
    {
        $ig = new Instagram();
        if ($ig->login($login, $password)) {
            return false;
        }
        $recipients = [
            'users' => [$targetUid],
        ];

        return $ig->direct->sendText($recipients, $message);
    }
}