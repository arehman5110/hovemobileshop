<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Helpers\UserRole;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    // ── Relationships ─────────────────────────────────────────────
    public function permissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    // ── Permission resolution ─────────────────────────────────────
    /**
     * Check if this user has a permission.
     * Priority: individual override > role default
     */
    public function hasPermission(string $permission): bool
    {
        $override = $this->getPermissionOverride($permission);
        if ($override !== null) {
            return $override;
        }
        return UserRole::can($this->role ?? 'viewer', $permission);
    }

    protected function getPermissionOverride(string $permission): ?bool
    {
        if (!isset($this->_permissionCache)) {
            $this->_permissionCache = $this->permissions()
                ->pluck('granted', 'permission')
                ->toArray();
        }
        return isset($this->_permissionCache[$permission])
            ? (bool) $this->_permissionCache[$permission]
            : null;
    }

    public function clearPermissionCache(): void
    {
        unset($this->_permissionCache);
    }

    public function effectivePermissions(): array
    {
        $rolePerms = array_fill_keys(UserRole::permissionsFor($this->role ?? 'viewer'), true);
        $overrides = $this->permissions()->pluck('granted', 'permission')->toArray();
        return array_merge($rolePerms, array_map('boolval', $overrides));
    }

    public function can($permission, $arguments = []): bool
    {
        if (is_array($arguments) && count($arguments)) {
            return parent::can($permission, $arguments);
        }
        if (isset(UserRole::PERMISSIONS[$permission])) {
            return $this->hasPermission($permission);
        }
        return parent::can($permission, $arguments);
    }

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isManager(): bool  { return in_array($this->role, ['admin','manager']); }

    public function roleConfig(): array
    {
        return UserRole::config($this->role ?? 'viewer');
    }
}