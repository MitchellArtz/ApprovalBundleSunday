<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\ApprovalBundle\Toolbox;

use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

final class Formatting
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function parseDate(DateTime $dateTime)
    {
        // Calculate week number manually for Sunday-starting weeks
        $weekNumber = $this->calculateWeekNumber($dateTime);

        // Ensure we're working with the start of the week (Sunday)
        $startOfWeek = clone $dateTime;
        if ($startOfWeek->format('D') !== 'Sun') {
            $startOfWeek->modify('last sunday');
        }
        
        $startWeekDay = $startOfWeek->format('d.m.Y');
        $endWeekDay = (clone $startOfWeek)->modify('+6 days')->format('d.m.Y');

        return $startOfWeek->format('F Y') . ' - ' . $this->translator->trans('agendaWeek') . ' ' . $weekNumber . ' [' . $startWeekDay . ' - ' . $endWeekDay . ']';
    }

    private function calculateWeekNumber(DateTime $dateTime): int
    {
        // Get the start of the week (Sunday)
        $startOfWeek = clone $dateTime;
        if ($startOfWeek->format('D') !== 'Sun') {
            $startOfWeek->modify('last sunday');
        }
        
        // Get January 1st of the same year
        $january1st = new DateTime($startOfWeek->format('Y') . '-01-01');
        
        // Adjust January 1st to the previous Sunday if it's not a Sunday
        if ($january1st->format('D') !== 'Sun') {
            $january1st->modify('last sunday');
        }
        
        // Calculate the difference in days and divide by 7 to get weeks
        $diff = $startOfWeek->diff($january1st);
        $weeks = floor($diff->days / 7) + 1;
        
        return $weeks;
    }

    public function formatDuration(int $duration): string
    {
        $prefix = $duration < 0 ? '-' : '';
        $mins = abs($duration) / 60;
        $hours = floor($mins / 60);
        $mins = $mins - ($hours * 60);
        $preZero = $mins < 9 ? '0' : '';

        return $prefix . $hours . ':' . $preZero . $mins;
    }
}
