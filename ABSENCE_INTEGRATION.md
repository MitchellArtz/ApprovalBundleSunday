# Absence Integration with WorkContractBundle

## Overview

This feature integrates absences from the WorkContractBundle into the ApprovalBundle weeks report. When a user has approved absences (sick leave, holiday, time off, etc.) on specific days, these will be displayed as icons in the weekly report.

## Features

- **Absence Icons**: Different colored icons for different types of absences
- **Tooltips**: Hover over icons to see absence details (type, comment, half-day status)
- **Legend**: Clear explanation of what each icon represents
- **Multi-language Support**: Available in English, German, and Croatian

## Absence Types and Icons

| Absence Type | Icon | Color | Description |
|--------------|------|-------|-------------|
| Sick Leave | 🏥 (fa-user-injured) | Red | User is sick |
| Holiday | 🏖️ (fa-umbrella-beach) | Green | User is on holiday |
| Time Off | 📅 (fa-calendar-day) | Blue | User has time off |
| Other | ➖ (fa-calendar-minus) | Yellow | Other types of absence |

## How It Works

1. **Data Fetching**: The `AbsenceService` queries the WorkContractBundle database for approved absences
2. **Integration**: Absence data is passed to the weekly report template
3. **Display**: Icons appear next to daily totals in the weekly report table
4. **Tooltips**: Hover over icons to see detailed absence information

## Technical Implementation

### Files Modified

- `Controller/WeekReportController.php` - Added absence service integration
- `Toolbox/AbsenceService.php` - New service for fetching absence data
- `Resources/views/report_by_user.html.twig` - Added absence icons and legend
- `Resources/translations/messages.*.xlf` - Added translation keys

### Dependencies

- Requires WorkContractBundle to be installed and active
- Uses Doctrine ORM for database queries
- Integrates with existing approval workflow

## Usage

1. Navigate to the weekly report in the ApprovalBundle
2. Select a user and week
3. Absence icons will automatically appear for days with approved absences
4. Hover over icons to see absence details
5. Refer to the legend below the table for icon meanings

## Configuration

No additional configuration is required. The feature automatically:
- Detects if WorkContractBundle is available
- Fetches only approved absences (status: approved or locked)
- Integrates seamlessly with existing approval workflow

## Benefits

- **Better Visibility**: Managers can see at a glance when team members are absent
- **Improved Planning**: Helps with resource allocation and project planning
- **Audit Trail**: Clear record of absences alongside time tracking data
- **User Experience**: Intuitive icons make reports easier to read

## Future Enhancements

Potential improvements could include:
- Absence duration display in the weekly totals
- Integration with approval workflow for absence requests
- Export functionality including absence data
- Calendar view integration 