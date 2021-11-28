<?php

namespace Home\Entity;

use Befew\Entity;

class CalendarStory extends Entity
{
    private static array $stories = [
        '1' => [
            'title' => 'title day 1',
            'content' => 'texte day 1'
        ],
        '2' => [
            'title' => 'title day 2',
            'content' => 'texte day 2'
        ],
        '3' => [
            'title' => 'title day 3',
            'content' => 'texte day 3'
        ],
        '28' => [
            'title' => 'title day 28',
            'content' => 'texte day 28'
        ],
        '29' => [
            'title' => 'title day 29',
            'content' => 'texte day 29'
        ],
        '30' => [
            'title' => 'title day 30',
            'content' => 'texte day 30'
        ],
    ];

    public static function getTodayStory(): array {
        $today = date('d');

        return self::$stories[$today];
    }
}