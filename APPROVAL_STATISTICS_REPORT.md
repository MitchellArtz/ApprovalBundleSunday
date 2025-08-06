# Approval Statistics Report

This document describes the new Approval Statistics Report feature that has been added to the Kimai Approval Bundle.

## Overview

The Approval Statistics Report provides a comprehensive overview of approval status counts for all users in the system. It shows how many weeks each user has in each approval status (unsubmitted, submitted, pending, approved, denied).

## Features

- **User-based Statistics**: Shows approval statistics for each user
- **Status Breakdown**: Displays counts for each approval status:
  - Not Submitted (unsubmitted weeks)
  - Submitted (weeks submitted for approval)
  - Pending (weeks awaiting approval decision)
  - Approved (approved weeks)
  - Denied (rejected weeks)
- **Total Counts**: Shows totals for each status across all users
- **Permission-based Access**: Users can only see statistics for users they have permission to view
- **Multi-language Support**: Available in English, German, and Croatian

## Access

The report can be accessed through:

1. **Timesheet Approval Section**: The report appears as a "Statistics" tab within the timesheet approval section
2. **Direct URL**: `/approval/statistics` (requires `view_hours_approval` permission)

## Permissions

The report respects the following permissions:

- **`view_hours_approval`**: Required to access the report
- **`view_all_approval`**: Can see statistics for all users
- **`view_team_approval`**: Can see statistics for team members
- **No special permissions**: Can only see their own statistics

## Technical Implementation

### Files Added/Modified

1. **Controller/ApprovalStatisticsReportController.php** - Handles the report logic and data processing
2. **Resources/views/approval_statistics_report.html.twig** - The report template
3. **Resources/views/navigation.html.twig** - Updated to include the statistics tab
4. **Resources/translations/messages.*.xlf** - Translation files for all supported languages

### Key Components

#### Controller Logic
- Calculates approval statistics for each user
- Determines the latest approval status for each week
- Respects user permissions for data access
- Sorts users alphabetically by display name

#### Template Features
- Responsive table design
- Color-coded status badges
- Totals row with summary statistics
- User profile links
- Empty state handling

#### Data Processing
- Uses the existing `ApprovalRepository` to fetch approval data
- Processes approval history to determine current status
- Handles edge cases (no history, multiple status changes)

## Usage

1. Navigate to the Timesheet Approval section in Kimai
2. Click on the "Statistics" tab
3. View the statistics table showing all users and their approval counts
4. Use the color-coded badges to quickly identify status distributions

## Status Definitions

- **Not Submitted**: Weeks that have not been submitted for approval
- **Submitted**: Weeks that have been submitted but not yet processed
- **Pending**: Weeks awaiting approval decision (intermediate statuses)
- **Approved**: Weeks that have been approved
- **Denied**: Weeks that have been rejected

## Future Enhancements

Potential improvements for future versions:

- Date range filtering
- Export functionality (CSV, PDF)
- Charts and graphs
- Historical trend analysis
- Team-based filtering
- Email notifications for statistics

## Troubleshooting

### Common Issues

1. **No data shown**: Ensure users have approval records in the database
2. **Permission errors**: Check user permissions for `view_hours_approval`
3. **Missing translations**: Clear cache and ensure translation files are properly loaded

### Debug Information

The report includes debug information in the template for troubleshooting:
- User count verification
- Permission level display
- Data availability checks 