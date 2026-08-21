# Task Breakdown — Online Job Portal System (4-Hour Build)

Companion to `PROJECT_CONTEXT.md`. Read that file for the schema, folder structure, endpoint contract, and UI/AI rules — this doc is the timeline and role split.

---

## Phase 0a — All 4 Members Together (0:00 – 0:10)

1. Read the spec together, confirm the 2 actors and all required features are understood
2. Confirm the DB schema and endpoint contract in `PROJECT_CONTEXT.md` match your interpretation — adjust field names now if needed, not later
3. Confirm the mock payment approach as a team (no real gateway) so nobody builds something different
4. Nominate the **Repo Setup Lead** — per the spec this should be the **Team Leader**, since they're the one required to create the GitHub repo and invite members

---

## Phase 0b — Repo Setup Lead Only (0:10 – 0:20)

**Goal: architecture exists before anyone starts, so nobody guesses folder/file names.**

| Step | Action |
|---|---|
| 1 | Create the GitHub repo, invite all 3 other members as collaborators immediately |
| 2 | Create the full folder structure from `PROJECT_CONTEXT.md` Section 3 — every folder, even empty ones |
| 3 | Create empty placeholder files for every file in the endpoint contract, each with a one-line comment noting the owner, e.g. `<?php // TODO: add_job.php logic (owner: Member A) ?>` |
| 4 | Add `PROJECT_CONTEXT.md` to the repo root |
| 5 | Add `README.md`: project name, link to `PROJECT_CONTEXT.md`, and a table of file ownership per member |
| 6 | Commit: `git commit -m "chore: project scaffold and conventions"`, push |

**Important for grading:** the spec states commit history is graded and a member with zero commits gets no coding credit — so from this point on, every member must commit their own work under their own Git identity, not have someone else commit on their behalf.

While this happens, the other 3 members re-read the UI guidelines and endpoint contract so they're ready to code at 0:20.

---

## Branching

- `main` — always the current working state, starts from the Phase 0b scaffold
- One branch per member: `member-a-backend`, `member-b-auth-payments`, `member-c-frontend`, `member-d-js`
- Merge into `main` at the sync points below (~1:00, ~2:30, and mandatory before Integration at 3:00)
- Merges should be clean fast-forwards since file ownership prevents overlap — a conflict means someone touched a file outside their ownership

---

**Standing rule for every table below:** comment your code as you write it, commit under your own name every 15–20 min, and only touch the files listed as yours.

## Member A — Database & Job Management (Admin CRUD)

| Time | Step |
|---|---|
| 0:20–0:35 | Create the database, run the schema from `PROJECT_CONTEXT.md`, insert 5–8 sample jobs across categories/job types plus 2–3 sample applications |
| 0:35–0:45 | Write `config/db.php`, share it with the team immediately |
| 0:45–1:10 | Write `admin/add_job.php` (Create) using prepared statements |
| 1:10–1:35 | Write `admin/dashboard.php` (Read — admin's job list) |
| 1:35–2:00 | Write `admin/edit_job.php` (Update — salary, description, status) |
| 2:00–2:20 | Write `admin/delete_job.php` (Delete) |
| 2:20–2:50 | Write the JOIN query for `admin/view_applications.php` (applications + job title) — hand off to Member B to wire into the page |
| 2:50–3:00 | Merge to `main`, pull latest, resolve anything |
| 3:00–3:30 | Integration: test all admin CRUD against the live DB, fix query bugs |
| 3:30–4:00 | Rehearse: schema, foreign keys, prepared statements — you'll lead the "System Architecture" presentation section |

---

## Member B — Auth, Public Job Board Backend & Mock Payment

| Time | Step |
|---|---|
| 0:20–0:40 | Write `admin/login.php` (`password_verify()`, `session_start()`, store `admin_id` in `$_SESSION`) |
| 0:40–0:50 | Write `admin/logout.php` |
| 0:50–1:10 | Write `includes/validate.php` — shared server-side validation functions reused across forms |
| 1:10–1:40 | Write `jobs/index.php` backend logic — pull jobs with optional `search` (title `LIKE`), `category_id`, `job_type` filters |
| 1:40–2:10 | Write `apply/submit_application.php` — validate input, insert into `applications`, generate a mock `transaction_ref` (e.g. `uniqid('TXN')`), set `payment_status = 'paid'` |
| 2:10–2:40 | Write `apply/payment.php` backend logic (process the mock payment form, pass data to `submit_application.php`) |
| 2:40–2:50 | Wire the JOIN query from Member A into `admin/view_applications.php` |
| 2:50–3:00 | Merge to `main`, pull latest |
| 3:00–3:30 | Integration: test login, search/filter, and the full apply→pay→confirm flow end to end |
| 3:30–4:00 | Rehearse: session handling, password hashing, and clearly explaining the mock payment design decision for the "AI Usage"/architecture Q&A |

---

## Member C — Frontend Structure, Styling & Mock Logos

| Time | Step |
|---|---|
| 0:20–0:40 | Write `css/variables.css` and `css/base.css` first — palette, font, spacing tokens everything else depends on |
| 0:40–1:00 | Build `admin/login.php`'s HTML form and `includes/header.php` / `footer.php` (nav changes based on admin login state) |
| 1:00–1:30 | Build `jobs/index.php` HTML — job card grid with search bar + category/job-type filter dropdowns, mock logo avatar per card |
| 1:30–2:00 | Build `apply/application_form.php` HTML and `apply/payment.php` HTML (mock card fields) |
| 2:00–2:30 | Build `admin/dashboard.php` HTML (job table with edit/delete actions) and the add/edit job form |
| 2:30–2:50 | Build `admin/view_applications.php` HTML (applications table with payment status badges) |
| 2:50–3:10 | Write `css/layout.css`, `css/forms.css`, `css/tables.css`, `css/components.css` (job cards, logo avatars, status badges) |
| 3:10–3:00 | Merge to `main`, pull latest |
| 3:00–3:30 | Integration pass with Member D — confirm every ID/class JS needs actually exists in the HTML |
| 3:30–4:00 | Rehearse explaining the layout/CSS organization and mock logo approach |

---

## Member D — Client-Side Logic (JavaScript)

| Time | Step |
|---|---|
| 0:20–0:40 | List every form needing validation (login, application form, payment form) and its rules |
| 0:40–1:10 | Write `js/validation.js` for the admin login form and the add/edit job form (required fields, numeric salary checks) |
| 1:10–1:40 | Write validation for the application form (name/email required, email format, cover letter min length) |
| 1:40–2:10 | Write validation for the mock payment form (format checks only — card number length/digits, expiry format, CVV length — clearly commented as format-only, not real validation) |
| 2:10–2:40 | Build the live search/filter behavior on `jobs/index.php` (filter job cards by title/category/type without full reload, or via `fetch()` to `jobs/index.php`) |
| 2:40–3:00 | Add UX touches: delete-confirmation dialogs in admin dashboard, disable submit button while processing |
| 3:00 | Merge to `main`, pull latest once Member C's HTML is in |
| 3:00–3:30 | Attach and test all validation scripts against the live pages; full click-through of every form |
| 3:30–4:00 | Rehearse explaining client vs server-side validation, and the search/filter logic line by line |

---

## Phase Final — All 4 Together (3:30 – 4:00, overlapping with individual wrap-up)

1. Run the full flow as the examiner will see it: **admin logs in → posts a new job → job seeker finds it via search/filter → applies → mock payment → application shows up in admin's application view with correct payment status**
2. Fix any last integration breaks (mismatched field names are the most common culprit)
3. Each person states in one sentence what their part does and one key decision they made — this is your presentation rehearsal
4. Confirm the DB has clean sample data left for the live demo (spec explicitly requires a live simulation)
5. Confirm every member has commits visible in the repo history under their own account (spec explicitly grades this)

---

## Notes on Timing

- If Phase 0a runs long, compress individual task blocks, not the final integration/rehearsal time — the live demo and Q&A matter more than an extra 10 minutes of coding.
- Every member's first action after 0:20 is: **pull the repo, confirm the scaffold matches expectations, point your AI assistant at `PROJECT_CONTEXT.md`** — then start.
- Members A and B sync around 2:20–2:50 since `view_applications.php` needs A's JOIN query wired into B's page.
- Member C should have basic HTML shells ready early (even before full styling) so Member D isn't blocked waiting to attach JS.
