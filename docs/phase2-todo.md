# Phase 2 Database & Backend Wiring To-Do Checklist

This checklist tracks every control and action across **Antigo Advisory WebApp** that has been outfitted with the `data-phase2` placeholder attribute. When the database and authentication layers are connected in Phase 2, this document serves as the implementation specification.

---

## 1. Authentication & Onboarding

| Page | Control / Element | Attribute Value | Intended Database & Backend Behavior |
|---|---|---|---|
| `/login` | `button#loginSubmitBtn` | `data-phase2="login_submit"` | Validates email and hashed password against `users` table, generates secure session, and resolves user role (Admin vs Client) directly from the database (no role toggle in UI). Executes **Redirect-by-role** via `AuthenticationSuccessHandler`: redirects admins (`ROLE_ADMIN`) to `/admin` and clients (`ROLE_CLIENT`) to `/client`. |
| `/login` | `button[data-phase2="send_recovery_link"]` | `data-phase2="send_recovery_link"` | Looks up user by email, generates cryptographically signed password reset token with expiration, and sends recovery email via Mailer. |
| `/register` | `button#registerSubmitBtn` | `data-phase2="register_submit"` | Validates name/email/passwords, checks for existing duplicate email, hashes password with `PASSWORD_DEFAULT`, inserts into `users` (`role = 'client'`), links any pending guest inquiry/booking ID when `hasPendingSubmission` is true, and authenticates session. |

> **Design Decision (Role Selector Dropped):** The client/admin role toggle on the login page was intentionally eliminated. A user's role is stored in their database record; inferring role from account prevents the role-mismatch bugs present in the legacy application. Redirect-by-role will route users automatically after authentication.

---

## 2. Admin Project Detail (`/admin/projects/{id}`)

| Page | Control / Element | Attribute Value | Intended Database & Backend Behavior |
|---|---|---|---|
| `/admin/projects/{id}` | `button[data-phase2="update_phase"]` | `data-phase2="update_phase"` | Reads selected phase (1–5) from `#stageSelector`, updates `projects` row (`current_phase`, `phase_name`, `progress`, `status`, `updated_at = NOW()`), which synchronously updates the client portal stepper. |
| `/admin/projects/{id}` | `button[data-phase2="mark_complete"]` | `data-phase2="mark_complete"` | Sets project phase to 5 (Delivered, 100%), updates `status_type = 'completed'`, and prepares final deliverable package for client handover. |
| `/admin/projects/{id}` | `button[data-phase2="archive_project"]` | `data-phase2="archive_project"` | Marks project `status_type = 'completed'` in `projects` table, sets progress to 100%, and archives active sprint from active command center view. |
| `/admin/projects/{id}` | `button[data-phase2="send_invoice"]` | `data-phase2="send_invoice"` | Sets `invoice_sent_at = NOW()` on `projects`, triggers notification email and bell alert to client, and unlocks online invoice payment in the client workspace. |
| `/admin/projects/{id}` | `button[data-phase2="confirm_cancel_project"]` | `data-phase2="confirm_cancel_project"` | Updates project `status_type = 'cancelled'`, logs `cancelled_by = current_user_id`, sets `cancelled_at = NOW()`, records cancellation reason, and sets project to read-only audit mode. |
| `/admin/projects/{id}` | `button[data-phase2="delete_permanently"]` | `data-phase2="delete_permanently"` | Available only in Cancelled state: performs hard deletion of project record, files, and chat messages after administrative confirmation. |
| ~~/admin/projects/{id}~~ | ~~`div[data-phase2="upload_file"]`~~ | ~~`data-phase2="upload_file"`~~ | ~~Receives uploaded file (Figma, PDF, JSON tokens, ZIP), validates MIME type and 25MB limit, stores to disk under `uploads/projects/{id}/`, and inserts metadata into `project_files`.~~ **Implemented via `ProjectFileType` and `/admin/projects/{id}/upload-file`** |
| ~~/admin/projects/{id}~~ | ~~`button[data-phase2="download_file"]`~~ | ~~`data-phase2="download_file"`~~ | ~~Streams project artifact from storage with authenticated access control.~~ **Implemented via `/admin/projects/{id}/files/{fileId}/download` (`BinaryFileResponse`)** |
| `/admin/projects/{id}` | `button[data-phase2="delete_file"]` | `data-phase2="delete_file"` | Deletes file record from `project_files` and safely unlinks file asset from disk. |
| ~~/admin/projects/{id}~~ | ~~`button[data-phase2="send_message"]`~~ | ~~`data-phase2="send_message"`~~ | ~~Inserts message into `project_messages` (`role = 'designer'`, `sender = 'Kimberly Jayne Antigo'`, `read_at = NULL`), updating unread badges on client topbar.~~ **Implemented via `MessageType` and `/admin/projects/{id}/message`** |
| `/admin/projects/{id}` | `button[data-phase2="save_notes"]` | `data-phase2="save_notes"` | Updates `internal_notes` column in `projects` table; preserves confidential studio billing and technical notes. |

---

## 3. Client Project Detail (`/client/projects/{id}`)

| Page | Control / Element | Attribute Value | Intended Database & Backend Behavior |
|---|---|---|---|
| ~~/client/projects/{id}~~ | ~~`button[data-phase2="send_message"]`~~ | ~~`data-phase2="send_message"`~~ | ~~Inserts client message into `project_messages` (`role = 'client'`, `sender = current_client_name`, `read_at = NULL`), updating unread badge count on admin topbar.~~ **Implemented via `MessageType` and `/client/projects/{id}/message`** |
| ~~/client/projects/{id}~~ | ~~`button[data-phase2="submit_payment"]`~~ | ~~`data-phase2="submit_payment"`~~ | ~~Initiates checkout flow or processes bank wire reference, inserting record into `payments` table with status `'pending'` for admin verification.~~ **Implemented via `PaymentType` and `/client/projects/{id}/payment`** |
| ~~/client/projects/{id}~~ | ~~`button[data-phase2="confirm_remove_client_file"]`~~ | ~~`data-phase2="confirm_remove_client_file"`~~ | **Implemented via `/client/projects/{id}/files/{fileId}/remove-view` (POST with CSRF)** |
| ~~/client/projects/{id}~~ | ~~`button[data-phase2="confirm_cancel_project"]`~~ | ~~`data-phase2="confirm_cancel_project"`~~ | **Implemented via `CancelProjectType` and `/client/projects/{id}/cancel`** |
| ~~/client/settings~~ & ~~/admin/settings~~ | ~~Profile & Password Forms~~ | `ProfileType`, `ChangePasswordType` | **Implemented via `ProfileType`, `ChangePasswordType`, and `UserPasswordHasherInterface`** |

---

## 4. Topbar & Navigation Shells

| Page | Control / Element | Attribute Value | Intended Database & Backend Behavior |
|---|---|---|---|
| Admin Topbar | `form#quickAddProjectForm` | `data-phase2="quick_add_project"` | Validates client selection, project title, category, budget (USD `$`), and deadline, then inserts row into `projects` table without requiring prior inquiry flow. |
| Client Topbar | `button#startProjectBtn` | `data-phase2="start_project_wizard"` | Initiates requirement intake form / new project submission workflow. |
| Client Dashboard | `button[data-phase2="client_start_project"]` | `data-phase2="client_start_project"` | Opens intake modal / new project wizard from client home screen. |

---

## 5. Summary & Verification

* All above controls display a toast: **"Available once the database is connected."**
* Neither forms nor action buttons silently fail or cause empty reloads.
* Standardized 5-phase lifecycle is driven centrally by `App\Config\ProjectPhases` across all views.

---

## 6. Public Pages Note (Prompt 2 — Form Manipulation)
* **Start a Project** & **Book a Consultation**: Public consumer-facing pages (`/start-project`, `/book-consultation`) have not been built yet in `Antigo_Advisory_WebApp`. Per prompt instructions, these forms are skipped and will be wired with `InquiryType` and `BookingType` when the public landing and booking pages are introduced.

