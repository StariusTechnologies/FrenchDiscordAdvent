<?php

namespace Home\Entity;

use Befew\Entity;

class CalendarStory extends Entity
{
    private static array $stories = [
        '1' => [
            'title' => 'title day 1',
            'content' => 'texte day 1',
        ],
        '2' => [
            'title' => 'title day 2',
            'content' => 'texte day 2',
        ],
        '3' => [
            'title' => 'title day 3',
            'content' => 'texte day 3',
        ],
        '4' => [
            'title' => 'title day 4',
            'content' => 'texte day 4',
        ],
        '5' => [
            'title' => 'title day 5',
            'content' => 'texte day 5',
        ],
        '6' => [
            'title' => 'title day 6',
            'content' => 'texte day 6',
        ],
        '7' => [
            'title' => 'title day 7',
            'content' => 'texte day 7',
        ],
        '8' => [
            'title' => 'title day 8',
            'content' => 'texte day 8',
        ],
        '9' => [
            'title' => 'title day 9',
            'content' => 'texte day 9',
        ],
        '10' => [
            'title' => 'title day 10',
            'content' => 'texte day 10',
        ],
        '11' => [
            'title' => 'title day 11',
            'content' => 'texte day 11',
        ],
        '12' => [
            'title' => 'title day 12',
            'content' => 'texte day 12',
        ],
        '13' => [
            'title' => 'title day 13',
            'content' => 'texte day 13',
        ],
        '14' => [
            'title' => 'title day 14',
            'content' => 'texte day 14',
        ],
        '15' => [
            'title' => 'title day 15',
            'content' => 'texte day 15',
        ],
        '16' => [
            'title' => 'title day 16',
            'content' => 'texte day 16',
        ],
        '17' => [
            'title' => 'title day 17',
            'content' => 'texte day 17',
        ],
        '18' => [
            'title' => 'title day 18',
            'content' => 'texte day 18',
        ],
        '19' => [
            'title' => 'title day 19',
            'content' => 'texte day 19',
        ],
        '20' => [
            'title' => 'title day 20',
            'content' => 'texte day 20',
        ],
        '21' => [
            'title' => 'title day 21',
            'content' => 'texte day 21',
        ],
        '22' => [
            'title' => 'title day 22',
            'content' => 'texte day 22',
        ],
        '23' => [
            'title' => 'title day 23',
            'content' => 'texte day 23',
        ],
        '24' => [
            'title' => 'title day 24',
            'content' => 'texte day 24',
        ],
        '25' => [
            'title' => 'title day 25',
            'content' => 'texte day 25',
        ],
        '26' => [
            'title' => 'title day 26',
            'content' => 'texte day 26',
        ],
        '27' => [
            'title' => 'title day 27',
            'content' => 'texte day 27',
        ],
        '28' => [
            'title' => 'title day 28',
            'content' => 'texte day 28',
        ],
        '29' => [
            'title' => 'title day 29',
            'content' => 'texte day 29',
        ],
        '30' => [
            'title' => 'title day 30',
            'content' => 'texte day 30',
        ],
        '31' => [
            'title' => 'title day 31',
            'content' => 'texte day 31',
        ],
    ];

    public static function getDayStory(string $day): array {
        return self::$stories[$day];
    }

    public static function getTodayStory(): array {
        $today = date('d');

        return self::getDayStory($today);
    }
}