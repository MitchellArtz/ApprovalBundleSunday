# Approval Bundle Permissions

This document describes the permissions used by the Approval Bundle.

## Available Permissions

### `view_team_approval`
- **Description**: Allows viewing and managing approvals for team members
- **Required for**: 
  - "To Approve" page
  - Approving team members' timesheets
- **Default**: Granted to team leads

### `view_all_approval`
- **Description**: Allows viewing and managing all approvals
- **Required for**: 
  - "To Approve" page
  - Approving any user's timesheets
- **Default**: Granted to administrators

## Settings Access

### Settings Pages
The following settings pages are restricted to **Super Administrators only** (`ROLE_SUPER_ADMIN`):

- **General Settings** (`/approval/settings`)
- **Workday History Settings** (`/approval/settings_workday_history`)
- **Overtime History Settings** (`/approval/settings_overtime`)

### UI Display
Settings links in the navigation are only visible to users with the `ROLE_SUPER_ADMIN` role.

## Permission Hierarchy

- **Super Admin**: Has all permissions by default, including access to all settings pages
- **Admin**: Has `view_all_approval` by default, but cannot access settings pages
- **Team Lead**: Has `view_team_approval` by default, but cannot access settings pages
- **User**: No approval permissions by default

## Troubleshooting

If settings pages are not accessible:

1. **Check Role**: Ensure the user has the `ROLE_SUPER_ADMIN` role
2. **Check Permissions**: Verify the user has the necessary approval permissions (`view_team_approval` or `view_all_approval`)
3. **Clear Cache**: Clear Symfony cache if changes were made to roles or permissions 