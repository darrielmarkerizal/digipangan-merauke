# Feature: Upload Foto Profil Distrik

## Requirements (EARS Format)

- While an administrator is creating or editing a district, when they select an image, the system shall show a local preview before saving.
- While a district form contains a new image, when the administrator saves it, the system shall upload the image to the temporary media endpoint and attach it to the district's `cover` collection.
- While an edited district has existing gallery images, when the administrator saves it, the system shall retain only the images still selected and append newly uploaded images.
- While a district has a cover image, when a visitor opens the district directory or profile, the system shall use that image instead of the sprout fallback.

## Architecture

### Frontend

- Add a reusable `RegionMediaForm` component to the admin create/edit forms.
- Support one cover image and optional gallery images with previews, removal, and cover promotion.
- Validate raster image type and 8 MB maximum before calling the upload endpoint.
- Show upload, save, and validation errors without submitting invalid files.

### Backend

- Reuse `POST /admin/media/upload` for temporary uploads.
- Pass the returned folder UUID as `cover` and `gallery` to the existing region create/update service.
- Keep `cover` single-file and synchronize `gallery` using retained media IDs.
- Existing public resources already expose cover URLs used by `RegionCard` and the region detail page.

### Security

- Keep the admin region routes behind the existing authenticated role middleware and district access check.
- Super Admin and Admin retain global region management; `admin_distrik` is limited to its assigned region; Farmer has no region-management permission.
- Apply the same scope checks to the authenticated API so a district administrator cannot access another region by changing an ID.
- Keep server-side image validation (`image`, raster MIME allowlist, 8 MB limit); client validation is only a usability guard.
- Do not accept SVG/HTML uploads through the existing media endpoint.
- Keep media URLs and public region fields limited to the existing resource response.
- Model changes remain covered by the existing auditable Region model.

## Implementation Plan

- [x] Add the reusable admin media form component.
- [x] Connect media fields to region create/edit submission.
- [x] Add client-side file validation and error states.
- [x] Add/adjust RBAC tests and run build/type checks.
