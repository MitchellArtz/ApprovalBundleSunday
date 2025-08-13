<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\ApprovalBundle\Toolbox;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use KimaiPlugin\WorkContractBundle\Entity\Absence;
use KimaiPlugin\WorkContractBundle\Repository\AbsenceRepository;

class AbsenceService
{
    private bool $isAvailable;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->isAvailable = class_exists('KimaiPlugin\WorkContractBundle\Entity\Absence');
    }

    /**
     * Check if the WorkContractBundle is available
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    /**
     * Get absences for a user within a date range
     *
     * @param User $user
     * @param DateTime $start
     * @param DateTime $end
     * @return array
     */
    public function getAbsencesForUserInPeriod(User $user, DateTime $start, DateTime $end): array
    {
        // Check if WorkContractBundle is available
        if (!$this->isAvailable) {
            return [];
        }

        try {
            /** @var AbsenceRepository $absenceRepository */
            $absenceRepository = $this->entityManager->getRepository('KimaiPlugin\WorkContractBundle\Entity\Absence');
            
            // Use the existing findForPeriod method from the repository
            // true means we only want approved/locked absences
            return $absenceRepository->findForPeriod($start, $end, $user, true);
        } catch (\Exception $e) {
            // Log the error but don't break the application
            // In a production environment, you might want to use a proper logger
            error_log('AbsenceService: Error fetching absences: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get absences organized by date for a user within a date range
     *
     * @param User $user
     * @param DateTime $start
     * @param DateTime $end
     * @return array
     */
    public function getAbsencesByDateForUserInPeriod(User $user, DateTime $start, DateTime $end): array
    {
        $absences = $this->getAbsencesForUserInPeriod($user, $start, $end);
        $absencesByDate = [];

        foreach ($absences as $absence) {
            $dateKey = $absence->getDate()->format('Y-m-d');
            $absencesByDate[$dateKey] = [
                'type' => $absence->getType(),
                'comment' => $absence->getComment(),
                'halfDay' => $absence->isHalfDay(),
                'duration' => $absence->getDuration()
            ];
        }

        return $absencesByDate;
    }

    /**
     * Get absence icon class based on absence type
     *
     * @param string $type
     * @return string
     */
    public function getAbsenceIconClass(string $type): string
    {
        return match ($type) {
            Absence::SICKNESS => 'fas fa-user-injured text-danger',
            Absence::HOLIDAY => 'fas fa-umbrella-beach text-success',
            Absence::TIME_OFF => 'fas fa-calendar-day text-info',
            Absence::OTHER => 'fas fa-calendar-minus text-warning',
            default => 'fas fa-calendar-minus text-muted'
        };
    }

    /**
     * Get absence tooltip text based on absence type
     *
     * @param string $type
     * @param string $comment
     * @param bool $halfDay
     * @return string
     */
    public function getAbsenceTooltip(string $type, string $comment, bool $halfDay): string
    {
        $typeLabel = match ($type) {
            Absence::SICKNESS => 'Sick Leave',
            Absence::HOLIDAY => 'Holiday',
            Absence::TIME_OFF => 'Time Off',
            Absence::OTHER => 'Other',
            default => 'Absence'
        };

        $halfDayText = $halfDay ? ' (Half Day)' : '';
        $commentText = !empty($comment) ? " - $comment" : '';

        return $typeLabel . $halfDayText . $commentText;
    }
} 