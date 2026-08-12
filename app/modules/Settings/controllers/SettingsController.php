<?php

declare(strict_types=1);

namespace App\Modules\Settings\controllers;

use App\Core\Controller;
use App\Core\Permission;
use PDO;

class SettingsController extends Controller
{
    /**
     * Manage Institutional Branding & Theme Colors
     */
    public function theme(): void
    {
        Permission::enforce('settings.manage');

        $error = null;
        $success = null;

        // Fetch current theme settings
        $stmt = db()->prepare('SELECT * FROM theme_settings WHERE college_id = 1 LIMIT 1');
        $stmt->execute();
        $theme = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $collegeName    = $this->input('college_name');
                $colorPrimary   = $this->input('color_primary');
                $colorSecondary = $this->input('color_secondary');
                $fontFamily     = $this->input('font_family', 'Inter');
                $borderRadius   = $this->input('border_radius', '8px');
                $address        = $this->input('address');
                $contact        = $this->input('contact_details');
                $gstin          = $this->input('gstin');

                $logoPath       = $theme['logo_path'] ?? '/assets/images/logo.png';
                $letterheadPath = $theme['letterhead_path'] ?? null;
                $sealPath       = $theme['seal_path'] ?? null;

                // File upload directory
                $uploadDir = public_path('/uploads/branding');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Handle Logo Upload
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $val = validate_upload($_FILES['logo']);
                    if ($val['ok']) {
                        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                        $logoName = 'logo_' . time() . '.' . $ext;
                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . '/' . $logoName)) {
                            $logoPath = '/uploads/branding/' . $logoName;
                        }
                    } else {
                        $error = 'Logo upload error: ' . $val['error'];
                    }
                }

                // Handle Letterhead Upload
                if (!$error && isset($_FILES['letterhead']) && $_FILES['letterhead']['error'] === UPLOAD_ERR_OK) {
                    $val = validate_upload($_FILES['letterhead']);
                    if ($val['ok']) {
                        $ext = pathinfo($_FILES['letterhead']['name'], PATHINFO_EXTENSION);
                        $lhName = 'letterhead_' . time() . '.' . $ext;
                        if (move_uploaded_file($_FILES['letterhead']['tmp_name'], $uploadDir . '/' . $lhName)) {
                            $letterheadPath = '/uploads/branding/' . $lhName;
                        }
                    } else {
                        $error = 'Letterhead upload error: ' . $val['error'];
                    }
                }

                // Handle Official Seal Upload
                if (!$error && isset($_FILES['seal']) && $_FILES['seal']['error'] === UPLOAD_ERR_OK) {
                    $val = validate_upload($_FILES['seal']);
                    if ($val['ok']) {
                        $ext = pathinfo($_FILES['seal']['name'], PATHINFO_EXTENSION);
                        $sealName = 'seal_' . time() . '.' . $ext;
                        if (move_uploaded_file($_FILES['seal']['tmp_name'], $uploadDir . '/' . $sealName)) {
                            $sealPath = '/uploads/branding/' . $sealName;
                        }
                    } else {
                        $error = 'Seal upload error: ' . $val['error'];
                    }
                }

                if (!$error) {
                    if ($theme) {
                        $update = db()->prepare('
                            UPDATE theme_settings 
                            SET college_name = :name, logo_path = :logo, letterhead_path = :lh, seal_path = :seal,
                                color_primary = :primary, color_secondary = :secondary, font_family = :font, 
                                border_radius = :radius, address = :address, contact_details = :contact, gstin = :gstin
                            WHERE college_id = 1
                        ');
                    } else {
                        $update = db()->prepare('
                            INSERT INTO theme_settings (
                                college_id, college_name, logo_path, letterhead_path, seal_path,
                                color_primary, color_secondary, font_family, border_radius, address, contact_details, gstin
                            ) VALUES (
                                1, :name, :logo, :lh, :seal, :primary, :secondary, :font, :radius, :address, :contact, :gstin
                            )
                        ');
                    }

                    $successExec = $update->execute([
                        ':name'      => $collegeName,
                        ':logo'      => $logoPath,
                        ':lh'        => $letterheadPath,
                        ':seal'      => $sealPath,
                        ':primary'   => $colorPrimary,
                        ':secondary' => $colorSecondary,
                        ':font'      => $fontFamily,
                        ':radius'    => $borderRadius,
                        ':address'   => $address,
                        ':contact'   => $contact,
                        ':gstin'     => $gstin,
                    ]);

                    if ($successExec) {
                        $success = 'Branding & theme settings updated successfully!';
                        // Refresh theme object
                        $stmt = db()->prepare('SELECT * FROM theme_settings WHERE college_id = 1 LIMIT 1');
                        $stmt->execute();
                        $theme = $stmt->fetch(PDO::FETCH_ASSOC);
                    } else {
                        $error = 'Failed to save theme settings to database.';
                    }
                }
            }
        }

        $this->render('Settings/views/theme', [
            'title'   => 'Branding & Theme Settings',
            'theme'   => $theme,
            'error'   => $error,
            'success' => $success,
        ], 'layout');
    }

    /**
     * Manage Roles and Reporting Hierarchy
     */
    public function roles(): void
    {
        Permission::enforce('settings.manage');

        $error = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $name        = $this->input('name');
                $code        = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $this->input('code')));
                $description = $this->input('description');
                $parentRole  = $this->input('parent_role_id') !== '' ? (int)$this->input('parent_role_id') : null;

                if (empty($name) || empty($code)) {
                    $error = 'Role name and code are required.';
                } else {
                    // Check if role code exists
                    $check = db()->prepare('SELECT COUNT(*) FROM roles WHERE code = :code');
                    $check->execute([':code' => $code]);
                    if ($check->fetchColumn() > 0) {
                        $error = 'A role with this code already exists.';
                    } else {
                        $insert = db()->prepare('
                            INSERT INTO roles (college_id, name, code, is_system_role, description, parent_role_id, status)
                            VALUES (1, :name, :code, 0, :description, :parent, 1)
                        ');
                        if ($insert->execute([
                            ':name'        => $name,
                            ':code'        => $code,
                            ':description' => $description,
                            ':parent'      => $parentRole,
                        ])) {
                            $success = 'Custom role created successfully.';
                        } else {
                            $error = 'Failed to create role.';
                        }
                    }
                }
            }
        }

        // Fetch all roles with their parent roles
        $rolesStmt = db()->query('
            SELECT r.*, p.name AS parent_name 
            FROM roles r 
            LEFT JOIN roles p ON p.id = r.parent_role_id 
            ORDER BY r.id ASC
        ');
        $roles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('Settings/views/roles', [
            'title'   => 'Role Permissions Builder',
            'roles'   => $roles,
            'error'   => $error,
            'success' => $success,
        ], 'layout');
    }

    /**
     * Manage Specific Role Permissions Checklist
     */
    public function rolePermissions(int $roleId): void
    {
        Permission::enforce('settings.manage');

        $error = null;
        $success = null;

        // Fetch role details
        $stmt = db()->prepare('SELECT * FROM roles WHERE id = :id');
        $stmt->execute([':id' => $roleId]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$role) {
            abort(404, 'Role not found.');
        }

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $checkedPerms = $this->input('permissions', []); // Array of checked permission IDs
                
                db()->beginTransaction();
                try {
                    // Remove all existing mappings
                    $del = db()->prepare('DELETE FROM role_permissions WHERE role_id = :role_id');
                    $del->execute([':role_id' => $roleId]);

                    // Add new mappings
                    $ins = db()->prepare('
                        INSERT INTO role_permissions (role_id, permission_id, granted)
                        VALUES (:role_id, :permission_id, 1)
                    ');
                    foreach ($checkedPerms as $permId) {
                        $ins->execute([
                            ':role_id'       => $roleId,
                            ':permission_id' => (int) $permId,
                        ]);
                    }

                    db()->commit();
                    $success = 'Role permissions updated successfully!';
                } catch (\Exception $e) {
                    db()->rollBack();
                    $error = 'Failed to save permissions: ' . $e->getMessage();
                }
            }
        }

        // Fetch all modules
        $modulesStmt = db()->query('SELECT * FROM modules WHERE status = 1 ORDER BY sort_order ASC');
        $modules = $modulesStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch all permissions grouped by module_id
        $permsStmt = db()->query('SELECT * FROM permissions ORDER BY module_id ASC, id ASC');
        $permissionsList = $permsStmt->fetchAll(PDO::FETCH_ASSOC);

        $permissionsByModule = [];
        foreach ($permissionsList as $p) {
            $permissionsByModule[$p['module_id']][] = $p;
        }

        // Fetch active mappings for this role
        $mapStmt = db()->prepare('SELECT permission_id FROM role_permissions WHERE role_id = :role_id AND granted = 1');
        $mapStmt->execute([':role_id' => $roleId]);
        $activePerms = $mapStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

        $this->render('Settings/views/role_permissions', [
            'title'               => 'Edit Permissions for ' . $role['name'],
            'role'                => $role,
            'modules'             => $modules,
            'permissionsByModule' => $permissionsByModule,
            'activePerms'         => $activePerms,
            'error'               => $error,
            'success'             => $success,
        ], 'layout');
    }
}
