<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\ApprovalBundle\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\PageSetup;
use DateTime;
use KimaiPlugin\ApprovalBundle\Entity\ApprovalStatus;
use KimaiPlugin\ApprovalBundle\Repository\ApprovalRepository;
use KimaiPlugin\ApprovalBundle\Toolbox\SecurityTool;
use KimaiPlugin\ApprovalBundle\Toolbox\SettingsTool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/approval')]
class ApprovalStatisticsReportController extends BaseApprovalController
{
    public function __construct(
        private SecurityTool $securityTool,
        private UserRepository $userRepository,
        private ApprovalRepository $approvalRepository,
        private SettingsTool $settingsTool
    ) {
    }

    #[Route(path: '/statistics', name: 'approval_statistics_report', methods: ['GET'])]
    #[IsGranted('view_hours_approval')]
    public function approvalStatisticsReport(Request $request): Response
    {
        $users = $this->getUsers();
        $statistics = [];

        foreach ($users as $user) {
            $userStats = $this->calculateUserApprovalStatistics($user);
            $statistics[] = [
                'user' => $user,
                'unsubmitted' => $userStats['unsubmitted'],
                'submitted' => $userStats['submitted'],
                'pending' => $userStats['pending'],
                'approved' => $userStats['approved'],
                'denied' => $userStats['denied'],
                'total' => $userStats['total']
            ];
        }

        // Sort by user display name
        usort($statistics, function ($a, $b) {
            return strcmp($a['user']->getDisplayName(), $b['user']->getDisplayName());
        });

        $page = new PageSetup('approval.statistics.report');
        $page->setHelp('approval.statistics.help');

        return $this->render('@Approval/approval_statistics_report.html.twig', [
            'page_setup' => $page,
            'statistics' => $statistics,
            'currentUser' => $this->getUser(),
            'current_tab' => 'approval_statistics',
        ] + $this->getDefaultTemplateParams($this->settingsTool));
    }

    private function calculateUserApprovalStatistics(User $user): array
    {
        // Get all approvals for this user
        $approvals = $this->approvalRepository->findBy(['user' => $user]);
        
        $unsubmitted = 0;
        $submitted = 0;
        $pending = 0;
        $approved = 0;
        $denied = 0;
        $total = 0;

        foreach ($approvals as $approval) {
            $total++;
            $latestHistory = $this->getLatestHistory($approval);
            
            if ($latestHistory === null) {
                $unsubmitted++;
                continue;
            }

            $status = $latestHistory->getStatus()->getName();
            
            switch ($status) {
                case ApprovalStatus::NOT_SUBMITTED:
                    $unsubmitted++;
                    break;
                case ApprovalStatus::SUBMITTED:
                    $submitted++;
                    break;
                case ApprovalStatus::APPROVED:
                    $approved++;
                    break;
                case ApprovalStatus::DENIED:
                    $denied++;
                    break;
                default:
                    // For any other status, consider it as pending
                    $pending++;
                    break;
            }
        }

        return [
            'unsubmitted' => $unsubmitted,
            'submitted' => $submitted,
            'pending' => $pending,
            'approved' => $approved,
            'denied' => $denied,
            'total' => $total
        ];
    }

    private function getLatestHistory($approval)
    {
        $history = $approval->getHistory();
        if (empty($history)) {
            return null;
        }
        
        // Sort by date descending and get the latest
        usort($history, function ($a, $b) {
            return $b->getDate() <=> $a->getDate();
        });
        
        return $history[0];
    }

    private function getUsers(): array
    {
        if ($this->securityTool->canViewAllApprovals()) {
            return $this->userRepository->findAll();
        }

        if ($this->securityTool->canViewTeamApprovals()) {
            return $this->securityTool->getUsers();
        }

        // If user can only view their own approvals
        return [$this->getUser()];
    }
} 