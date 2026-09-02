# Feature: Akun Petani dari Admin

## Requirements (EARS)

- While a Super Admin creates a user with the `farmer` role, when the user is saved, the system shall create a linked farmer profile in the same transaction.
- While a user with the `farmer` role has no linked profile, when an administrator edits that user and supplies the minimum farmer data, the system shall create the missing linked profile.
- While a farmer profile is linked to an account, when its account is used to open `/petani/dashboard`, the system shall render the farmer dashboard instead of returning the missing-profile error.
- While a user is assigned the `farmer` role, the system shall require a valid region and phone number for the farmer profile.

## Architecture

### Frontend

- Extend the admin user form with farmer-only fields: region, phone, village, farmer group, and land area.
- Show these fields only when the `farmer` role is selected.
- Change the public call-to-action to link to the appropriate dashboard when a user is already authenticated.
- Reuse existing server-side validation errors and Inertia processing state.

### Backend

- Extend `StoreUserRequest` and `UpdateUserRequest` with farmer profile validation.
- Make `UserService` create or update the linked `Farmer` record inside the existing database transaction.
- Return the farmer relation to the admin user form so existing farmer accounts can be repaired.
- Preserve the existing self-service registration flow.

### Security

- Keep user-management endpoints protected by the existing `ManageUsers` permission / admin middleware.
- Validate all farmer foreign keys and enforce village/group region consistency server-side.
- Use Eloquent relations and transactions; do not concatenate SQL or accept a client-supplied account owner.
- Continue excluding passwords and tokens from user resources.

## Implementation Plan

- [x] Document requirements, architecture, and security controls.
- [x] Add backend validation and transactional farmer-profile synchronization.
- [x] Update the admin user form for farmer profile data.
- [x] Add regression tests for create, repair, and dashboard access.
