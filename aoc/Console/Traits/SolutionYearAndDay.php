<?php

namespace Mike\AdventOfCode\Console\Traits;

use Mike\AdventOfCode\Console\Command;
use Mike\AdventOfCode\Console\Exceptions\TerminateExceptionBuilder;
use Mike\AdventOfCode\Solutions\Solution;
use Symfony\Component\Finder\Finder;

/**
 * @mixin \Mike\AdventOfCode\Console\Command
 */
trait SolutionYearAndDay
{
    /**
     * The year for the event solution.
     */
    protected int $year;

    /**
     * The day for the event solution.
     */
    protected int $day;

    /**
     * If year or day are not provided through options, ask for them interactively.
     */
    public function ensureYearAndDayAvailable(): void
    {
        $year = $this->option('year');

        if (is_null($year)) {
            $years = $this->getAvailableYears();
            $choices = array_combine($years, array_map(fn(int $y) => "Year $y", $years));
            $year = str_replace('Year ', '', $this->choice('The solution for which year should run?', $choices));
        }

        $this->year = (int) $year;

        $day = $this->option('day');

        $days = $this->getAvailableDays($year);

        if (empty($days)) {
            $this->terminate(function (TerminateExceptionBuilder $builder) use ($year) {
                $builder->setExitCode(Command::FAILURE);

                $builder->warn("Year <white>[$year]</> of Advent of Code does not have any solutions implemented, yet!");
                $builder->newLine();
                $builder->info("Start by calling \"<white>advent make:solution --year $year</>\"");
            });
        }

        if (is_null($day)) {
            $choices = array_combine($days, array_map(fn(int $d) => "Day $d", $days));
            $day = str_replace('Day ', '', $this->choice("The solution for which day in $year should run?", $days));
        }

        $this->day = (int) $day;
    }

    /**
     * If year or day are not provided through options, ask for them interactively.
     */
    public function ensureRemainingYearAndDayAvailable(): void
    {
        $year = $this->option('year');

        if (is_null($year)) {
            $years = $this->getRemainingYears();
            $choices = array_combine($years, array_map(fn(int $y) => "Year $y", $years));
            $year = str_replace('Year ', '', $this->choice('The solution for which year should run?', $choices));
        }

        $this->year = (int) $year;

        $day = $this->option('day');

        $days = $this->getRemainingDays($year);

        if (empty($days)) {
            $this->terminate(function (TerminateExceptionBuilder $builder) use ($year) {
                $builder->setExitCode(Command::FAILURE);

                $builder->warn("Advent of Code year <white>[$year]</> has all event solutions ready!");
            });
        }

        if (is_null($day)) {
            $choices = array_combine($days, array_map(fn(int $d) => "Day $d", $days));
            $day = str_replace('Day ', '', $this->choice("The solution for which day in $year should run?", $days));
        }

        $this->day = (int) $day;
    }

    /**
     * Get the available years for which there is a solution for.
     */
    protected function getAvailableYears(): array
    {
        $dirs = Finder::create()
            ->in($this->app->path('Solutions'))
            ->directories()
            ->name('/^Year\d{4}$/i');

        $years = [];
        $currentYear = intval(date('Y'));
        $currentMonth = intval(date('m'));

        foreach ($dirs as $dir) {
            $year = intval(str_replace('Year', '', $dir->getRelativePathname()));

            if ($year < 2015 || $year > $currentYear) {
                continue;
            }

            if ($year == $currentYear && $currentMonth < 12) {
                continue;
            }

            $years[] = $year;
        }

        return $years;
    }

    /**
     * Get the available days for specified year for which there is a solution for.
     */
    protected function getAvailableDays(int $year): array
    {
        $currentYear = intval(date('Y'));
        $currentMonth = intval(date('m'));

        if ($year < 2015 || $year > $currentYear
            || ($year == $currentYear && $currentMonth < 12)) {
            return [];
        }

        $days = [];

        $currentDay = intval(date('d'));

        $namespace = $this->app->getNamespace();

        $maxDays = $year == $currentYear && $currentDay < 26 ? $currentDay+1 : 26;

        for($i = 1; $i < $maxDays; $i ++) {
            $dayClass = "$namespace\\Solutions\\Year$year\\Day$i";

            if (class_exists($dayClass) && is_subclass_of($dayClass, Solution::class)) {
                $days[] = $i;
            }
        }

        return $days;
    }

    /**
     * Get the remaining years for which there is not a solution for.
     */
    protected function getRemainingYears(): array
    {
        $remaining = [];

        $currentYear = intval(date('Y'));
        $currentMonth = intval(date('m'));

        $maxYear = $currentMonth < 12 ? $currentYear-1 : $currentYear;

        for ($y = 2015; $y <= $maxYear; $y++) {
            if (count($this->getRemainingDays($y)) > 0) {
                $remaining[] = $y;
            }
        }

        return $remaining;
    }

    /**
     * Get the remaining days for specified year for which there is not a solution for.
     */
    protected function getRemainingDays(int $year): array
    {
        $currentYear = intval(date('Y'));
        $currentMonth = intval(date('m'));

        if ($year < 2015 || $year > $currentYear
            || ($year == $currentYear && $currentMonth < 12)) {
            return [];
        }

        $remaining = [];

        $currentDay = intval(date('d'));

        $namespace = $this->app->getNamespace();

        $maxDays = $year == $currentYear && $currentDay < 26 ? $currentDay+1 : 26;

        for($i = 1; $i < $maxDays; $i ++) {
            $dayClass = "{$namespace}Solutions\\Year$year\\Day$i";

            if (! class_exists($dayClass) || !is_subclass_of($dayClass, Solution::class)) {
                $remaining[] = $i;
            }
        }

        return $remaining;
    }
}
