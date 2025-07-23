<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\ApprovalBundle\Controller;

use App\Controller\AbstractController;
use KimaiPlugin\ApprovalBundle\Toolbox\SettingsTool;

class BaseApprovalController extends AbstractController
{
    protected function canManageTeam(): bool
    {
        return $this->isGranted('view_team_approval');
    }

    protected function canManageAllPerson(): bool
    {
        return $this->isGranted('view_all_approval');
    }

    protected function canViewHoursApproval(): bool
    {
        return $this->isGranted('view_hours_approval');
    }

    protected function canViewSettingsHoursApproval(): bool
    {
        return $this->isGranted('settings_hours_approval');
    }

    protected function getDefaultTemplateParams(SettingsTool $settingsTool): array
    {
        return [
            'showToApproveTab' => $this->canViewHoursApproval(),
            'showSettings' => $this->canViewSettingsHoursApproval(),
            'showSettingsHoursApproval' => $this->canViewSettingsHoursApproval(),
            'showSettingsWorkdays' => $this->isGranted('ROLE_SUPER_ADMIN') && $settingsTool->isOvertimeCheckActive(),
            'showOvertime' => $settingsTool->isOvertimeCheckActive(),
            'canViewHoursApproval' => $this->canViewHoursApproval(),
        ];
    }
}
