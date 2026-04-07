<?php

namespace App\Helpers;

/**
 * Single source of truth for user roles and permissions.
 * To add a new role or permission — edit this file only.
 */
class UserRole
{
    // ── Role definitions ──────────────────────────────────────────
    const ROLES = [
        'admin' => [
            'label' => 'Admin',
            'color' => 'danger',
            'icon'  => '👑',
            'description' => 'Full access — manage users, settings, everything',
        ],
        'manager' => [
            'label' => 'Manager',
            'color' => 'primary',
            'icon'  => '🏢',
            'description' => 'Full access except user management',
        ],
        'technician' => [
            'label' => 'Technician',
            'color' => 'success',
            'icon'  => '🔧',
            'description' => 'Create and manage jobs, view customers and stock',
        ],
        'viewer' => [
            'label' => 'Viewer',
            'color' => 'secondary',
            'icon'  => '👁️',
            'description' => 'Read-only access to all sections',
        ],
    ];

    // ── Permission definitions ────────────────────────────────────
    // Each permission maps to which roles can perform it
    const PERMISSIONS = [
        // Dashboard
        'dashboard.view'        => ['admin','manager','technician','viewer'],

        // Jobs
        'jobs.view'             => ['admin','manager','technician','viewer'],
        'jobs.create'           => ['admin','manager','technician'],
        'jobs.edit'             => ['admin','manager','technician'],
        'jobs.delete'           => ['admin','manager'],
        'jobs.update-status'    => ['admin','manager','technician'],

        // Customers
        'customers.view'        => ['admin','manager','technician','viewer'],
        'customers.create'      => ['admin','manager','technician'],
        'customers.edit'        => ['admin','manager','technician'],
        'customers.delete'      => ['admin','manager'],

        // Payments
        'payments.create'       => ['admin','manager','technician'],
        'payments.edit'         => ['admin','manager'],
        'payments.delete'       => ['admin','manager'],

        // Parts / Stock
        'parts.view'            => ['admin','manager','technician','viewer'],
        'parts.create'          => ['admin','manager'],
        'parts.edit'            => ['admin','manager'],
        'parts.delete'          => ['admin'],
        'parts.topup'           => ['admin','manager','technician'],

        // Repair Types
        'repair-types.manage'   => ['admin','manager'],

        // Vouchers
        'vouchers.view'         => ['admin','manager','viewer'],
        'vouchers.manage'       => ['admin','manager'],

        // Phone Deals
        'phone-deals.view'      => ['admin','manager','technician','viewer'],
        'phone-deals.create'    => ['admin','manager'],
        'phone-deals.edit'      => ['admin','manager'],
        'phone-deals.delete'    => ['admin'],

        // Settings
        'settings.view'         => ['admin','manager','viewer'],
        'settings.edit'         => ['admin'],

        // Users
        'users.view'            => ['admin'],
        'users.manage'          => ['admin'],

        // Reports
        'reports.view'          => ['admin','manager'],

        // Refunds
        'refunds.create'        => ['admin','manager'],
        'refunds.delete'        => ['admin'],
    ];

    // ── Helpers ───────────────────────────────────────────────────

    /** Check if a role has a permission */
    public static function can(string $role, string $permission): bool
    {
        return in_array($role, self::PERMISSIONS[$permission] ?? []);
    }

    /** Get all role keys */
    public static function all(): array
    {
        return array_keys(self::ROLES);
    }

    /** Get config for a role */
    public static function config(string $role): array
    {
        return self::ROLES[$role] ?? self::ROLES['viewer'];
    }

    /** Get all permissions a role has */
    public static function permissionsFor(string $role): array
    {
        return array_keys(array_filter(
            self::PERMISSIONS,
            fn($roles) => in_array($role, $roles)
        ));
    }
}