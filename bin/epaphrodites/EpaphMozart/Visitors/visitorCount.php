<?php

declare(strict_types=1);

namespace Epaphrodites\epaphrodites\EpaphMozart\Visitors;

use DateTimeImmutable;

class visitorCount{

    private const FILE = 'counter.json';
    private const COOKIE_NAME = 'user_visited';
    private const COOKIE_DURATION = 86400; // 24 hours in seconds
    private array $counters;

    /**
     * Constructor: Initializes the counter by loading existing data and starting the session.
     */
    public function __construct() {
        $this->loadCounters();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Records a new visit by incrementing the counters for day, month, and year,
     * but only for new users or users who haven't visited in the last 24 hours.
     */
    public function recordVisit(): void {
        
        // Check if the user has already been counted in the last 24 hours
        if ($this->isRecentUser()) {
            return;
        }

        $date = new DateTimeImmutable();
        $day = $date->format('Y-m-d');
        $month = $date->format('Y-m');
        $year = $date->format('Y');

        // Increment counters
        $this->counters['day'][$day] = ($this->counters['day'][$day] ?? 0) + 1;
        $this->counters['month'][$month] = ($this->counters['month'][$month] ?? 0) + 1;
        $this->counters['year'][$year] = ($this->counters['year'][$year] ?? 0) + 1;

        $this->saveCounters();

        // Mark this user as counted
        $this->markUserCounted();
    }

    /**
     * Retrieves the visit data for the current day, month, and year.
     *
     * @return array An array containing visit counts for day, month, and year.
     */
    public function retrieveData(): array {
        $date = new DateTimeImmutable();
        $day = $date->format('Y-m-d');
        $month = $date->format('Y-m');
        $year = $date->format('Y');

        return [
            'day' => $this->counters['day'][$day] ?? 0,
            'month' => $this->counters['month'][$month] ?? 0,
            'year' => $this->counters['year'][$year] ?? 0,
        ];
    }

    /**
     * Summary of getDayList
     * 
     * @param int $number
     * @return array
     */
    private function getDayList(
        int $number
    ): array {
        $currentDate = new DateTimeImmutable();
        $lastDays = [];
    
        for ($i = 0; $i < $number; $i++) {
            $lastDays[] = $currentDate->format('Y-m-d');
            $currentDate = $currentDate->modify('-1 day');
        }
    
        $result = [];
        foreach ($lastDays as $day) {
            $result[] = [
                'key' => $day,
                'value' => $this->counters['day'][$day] ?? 0,
            ];
        }
    
        return $result;
    }
    
    /**
     * Summary of showListVisitedPerDay
     * 
     * @param int $number
     * @return array
     */
    public function showListVisitedPerDay(
        int $number = 1
    ): array {
       
        return array_reverse($this->getDayList($number));
    }
    
    /**
     * Checks if the current user has visited in the last 24 hours.
     *
     * @return bool True if the user has been counted in the last 24 hours, false otherwise.
     */
    private function isRecentUser(): bool {
        return isset($_SESSION[self::COOKIE_NAME]) || isset($_COOKIE[self::COOKIE_NAME]);
    }

    /**
     * Marks the current user as counted using both session and a 24-hour cookie.
     */
    private function markUserCounted(): void {
        $_SESSION[self::COOKIE_NAME] = true;
        setcookie(self::COOKIE_NAME, '1', time() + self::COOKIE_DURATION, '/', '', true, true);
    }

    /**
     * Loads the counters from the JSON file or initializes them if the file doesn't exist.
     */
    private function loadCounters(): void {
        if (file_exists(self::FILE)) {
            $fileContent = file_get_contents(self::FILE);
            
            if (empty($fileContent) || trim($fileContent) === '') {
                $this->counters = ['day' => [], 'month' => [], 'year' => []];
            } else {
                try {
                    $this->counters = json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {

                    $this->counters = ['day' => [], 'month' => [], 'year' => []];
                }
            }
        } else {

            $this->counters = ['day' => [], 'month' => [], 'year' => []];
        }
    }

    /**
     * Saves the current state of the counters to the JSON file.
     */
    private function saveCounters(): void {
        file_put_contents(self::FILE, json_encode($this->counters, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }
}