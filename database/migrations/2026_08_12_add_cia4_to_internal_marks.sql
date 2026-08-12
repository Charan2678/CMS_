-- ============================================================
-- Migration: Add cia4 to internal_marks.exam_type
-- Date: 2026-08-12
-- Description:
--   Adds 'cia4' (Mid Examination 4) to the exam_type enum.
--   This is a NON-DESTRUCTIVE change — no existing data is
--   modified, deleted, or affected.
--
-- Run this ONCE on each environment (local, staging, production)
-- BEFORE deploying the updated PHP code.
--
-- Safe to re-run: ALTER TABLE on an enum that already has
-- the value is ignored by MySQL (no error).
-- ============================================================

ALTER TABLE `internal_marks`
  MODIFY COLUMN `exam_type`
    ENUM('cia1','cia2','cia3','cia4','assignment','practical') NOT NULL;

-- Verify the change
DESCRIBE `internal_marks`;
