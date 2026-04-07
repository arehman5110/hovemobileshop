<?php

namespace App\Helpers;

/**
 * Single source of truth for Job statuses.
 * To add a new status: add one entry to STATUSES below — nothing else needed.
 */
class JobStatus
{
    const STATUSES = [
        'In Progress' => [
            'label'      => 'In Progress',
            'icon'       => '🔧',
            'bi'         => 'bi-tools',
            'color'      => '#0d6efd',
            'bg'         => 'rgba(13,110,253,.12)',
            'badge'      => 'bg-primary',
            'text'       => 'text-primary',
        ],
        'Waiting Parts' => [
            'label'      => 'Waiting Parts',
            'icon'       => '⏳',
            'bi'         => 'bi-clock',
            'color'      => '#cc9a00',
            'bg'         => 'rgba(255,193,7,.15)',
            'badge'      => 'bg-warning text-dark',
            'text'       => 'text-warning',
        ],
        'Ready for Collection' => [
            'label'      => 'Ready for Collection',
            'icon'       => '📦',
            'bi'         => 'bi-bag-check',
            'color'      => '#087990',
            'bg'         => 'rgba(13,202,240,.12)',
            'badge'      => 'bg-info',
            'text'       => 'text-info',
        ],
        'Completed' => [
            'label'      => 'Completed',
            'icon'       => '✅',
            'bi'         => 'bi-check-circle',
            'color'      => '#198754',
            'bg'         => 'rgba(25,135,84,.12)',
            'badge'      => 'bg-success',
            'text'       => 'text-success',
        ],
        'Cancelled' => [
            'label'      => 'Cancelled',
            'icon'       => '✕',
            'bi'         => 'bi-x-circle',
            'color'      => '#6c757d',
            'bg'         => 'rgba(108,117,125,.1)',
            'badge'      => 'bg-secondary',
            'text'       => 'text-secondary',
        ],
    ];

    /** All status keys as a plain array — for validation rules */
    public static function all(): array
    {
        return array_keys(self::STATUSES);
    }

    /** Comma-separated string for Laravel 'in:' validation rule */
    public static function validationRule(): string
    {
        return 'required|in:' . implode(',', self::all());
    }

    /** Config array for a specific status */
    public static function config(string $status): array
    {
        return self::STATUSES[$status] ?? self::STATUSES['Cancelled'];
    }

    /** Statuses that count as "active" (shown by default on index) */
    public static function activeStatuses(): array
    {
        return ['In Progress', 'Waiting Parts', 'Ready for Collection'];
    }

    /** Returns JSON-safe array for use in JS */
    public static function toJson(): string
    {
        return json_encode(self::STATUSES);
    }
}