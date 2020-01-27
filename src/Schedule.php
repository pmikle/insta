<?php

namespace App;

class Schedule
{
    private $periods = [];

    public function __construct(array $periods)
    {
        $this->periods = $this->getPeriodsFromArray($periods);
    }

    private function getPeriodsFromArray($array)
    {
        $out = [];
        $intervalDay = new \DateInterval('P1D');
        foreach ($array as $period) {
            if (!isset($period[0], $period[1])) {
                continue;
            }
            $out[] = [
                'begin' => \DateTime::createFromFormat('H:i:s', $period[0], new \DateTimeZone('Europe/Moscow')),
                'end' => \DateTime::createFromFormat('H:i:s', $period[1], new \DateTimeZone('Europe/Moscow')),
            ];
            $out[] = [
                'begin' => \DateTime::createFromFormat('H:i:s', $period[0], new \DateTimeZone('Europe/Moscow'))->add($intervalDay),
                'end' => \DateTime::createFromFormat('H:i:s', $period[1], new \DateTimeZone('Europe/Moscow'))->add($intervalDay),
            ];
        }
        return $out;
    }

    public function getNearestAvailablePeriod()
    {
        uasort($this->periods, function ($period1, $period2){
            return $period1['begin'] <=> $period2['begin'];
        });
        $now = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));
        $availablePeriods = array_filter($this->periods, function ($period) use ($now) {
            return $period['begin'] > $now;
        });
        return array_shift($availablePeriods);
    }
}