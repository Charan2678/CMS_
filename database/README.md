# Database Architecture & Schema Management

This directory contains the database definition, schema scripts, and seed files for the **College Management System (CMS)**.

---

## 📜 Source of Truth: `schema.sql`

- **Authoritative Definition**: `database/schema.sql` is the primary, always-up-to-date source of truth for the entire database structure.
- **Single Database Strategy**: For single-environment and deployment environments, changes to tables or indexes are added to `schema.sql`.
- **Migrations Strategy (Option A)**:
  - If schema alterations are required between deployment versions, dated diff files (e.g. `database/migrations/2026_08_06_add_column_name.sql`) may be created as transient migration scripts.
  - Periodic consolidation ensures all structural changes are folded back into `schema.sql`.

---

## 🌱 Seed Execution Sequence

When setting up a new development or production database instance, apply `schema.sql` first, followed by seed scripts in numerical order:

```sql
1. schema.sql                         -- Creates all 34+ database tables
2. seeds/00_seed_everything.sql      -- Master seed runner
3. seeds/01_initial_seed.sql         -- Default College & System Roles
4. seeds/02_permissions_seed.sql     -- Core RBAC permissions
5. seeds/03_student_permissions_seed.sql
6. seeds/04_faculty_permissions_seed.sql
7. seeds/05_staff_permissions_seed.sql
8. seeds/06_academic_permissions_seed.sql
9. seeds/07_fee_permissions_seed.sql
10. seeds/08_reports_permissions_seed.sql
11. seeds/09_hostel_transport_permissions_seed.sql
12. seeds/10_results_permissions_seed.sql
13. seeds/11_library_permissions_seed.sql
14. seeds/12_canteen_and_roles_seed.sql
15. seeds/13_grant_all_permissions_seed.sql
```
