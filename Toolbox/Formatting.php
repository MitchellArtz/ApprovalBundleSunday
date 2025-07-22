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
        $weekNumber = (clone $dateTime)->format('W');

        // Ensure we're working with the start of the week (Sunday)
        $startOfWeek = clone $dateTime;
        if ($startOfWeek->format('D') !== 'Sun') {
            $startOfWeek->modify('last sunday');
        }
        
        $startWeekDay = $startOfWeek->format('d.m.Y');
        $endWeekDay = (clone $startOfWeek)->modify('+6 days')->format('d.m.Y');

        return $startOfWeek->format('F Y') . ' - ' . $this->translator->trans('agendaWeek') . ' ' . $weekNumber . ' [' . $startWeekDay . ' - ' . $endWeekDay . ']';
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
