-- ============================================================
-- DISABLED: GRANT ALL PERMISSIONS SEED
-- ============================================================
-- This seed previously granted every permission to every role,
-- which silently defeated the RBAC design set up in
-- 11_strict_role_permissions_seed.sql and 12_canteen_and_roles_seed.sql.
--
-- It has been disabled (turned into a no-op) so role-based
-- restrictions actually take effect. Do NOT re-enable this file
-- unless you genuinely want every role to have admin-level access.
-- ============================================================

SELECT 'Seed 13 is intentionally disabled - role permissions are managed by seed 11 & 12' AS notice;
