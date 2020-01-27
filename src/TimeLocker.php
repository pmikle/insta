<?php

namespace App;

class TimeLocker
{
    const FILE_LOCKER = 'timeLocker.txt';
    private $fileLocker;

    public function __construct()
    {
        $this->fileLocker = __DIR__ . '/../' . Config::$cacheFolder . '/' . self::FILE_LOCKER;
    }

    public function isTimeHasCome()
    {
        if (!file_exists($this->fileLocker)) {
            $this->setNextTriggerDateTime();
            return false;
        }
        if (!is_readable($this->fileLocker)) {
            return false;
        }

        $nextDTString = file_get_contents($this->fileLocker);
        $nextDT = \DateTime::createFromFormat('Y-m-d H:i:s', $nextDTString, new \DateTimeZone('Europe/Moscow'));
        $nowDT = new \DateTime('now');

        return $nextDT <= $nowDT;
    }

    public function setNextTriggerDateTime()
    {
        $periods = new Schedule(Config::$available_message_sending_periods);
        $nearestPeriod = $periods->getNearestAvailablePeriod();
        $randomDateTime = $this->getRandomDateTime($nearestPeriod);

        return $this->saveFileLocker($randomDateTime->format('Y-m-d H:i:s'));
    }

    private function getRandomDateTime($period)
    {
        if (!isset($period['begin'], $period['end'])) {
            return null;
        }
        $timeStamp = rand(
            $period['begin']->getTimestamp(),
            $period['end']->getTimestamp()
        );
        return (new \DateTime('', new \DateTimeZone('Europe/Moscow')))->setTimestamp($timeStamp);
    }

    private function saveFileLocker($value)
    {
        if (file_exists($this->fileLocker) && !is_writable($this->fileLocker)) {
            return false;
        }
        return file_put_contents($this->fileLocker, $value);
    }
}