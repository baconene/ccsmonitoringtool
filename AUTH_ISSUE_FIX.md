# Authentication Issue Fix - Student Pages

## Problem
Users were getting logged out when switching between student pages.

## Root Cause
The `role` relationship was not being eagerly loaded when checking user permissions in the middleware. This caused the `hasRole()` method to return `false` even for authenticated users with the correct role, triggering the authentication failure and logout.

## Files Modified

### 1. `app/Http/Middleware/HandleInertiaRequests.php`
**Issue:** The user object shared with Inertia was not loading the `role` relationship.

**Fix:** Changed from:
```php
'auth' => [
    'user' => $request->user(),
],
```

To:
```php
'auth' => [
    'user' => $request->user() ? $request->user()->load('role') : null,
],
```

### 2. `app/Http/Middleware/EnsureUserHasRole.php`
**Issue:** The middleware was checking roles without ensuring the relationship was loaded.

**Fix:** Changed from:
```php
$user = $request->user();
```

To:
```php
$user = $request->user()->load('role');
```

## How It Works

The `hasRole()` method in the User model relies on the role relationship:
```php
public function hasRole(string $roleName): bool
{
    return $this->role && $this->role->hasName($roleName);
}
```

Without eager loading the `role` relationship, `$this->role` would be `null` on subsequent requests, causing:
1. The middleware check to fail
2. The user to be redirected as unauthorized
3. Appearing as if they were "logged out"

## Testing
1. Log in as a student user
2. Navigate between different student pages (courses, activities, reports, etc.)
3. Verify that navigation works smoothly without unexpected logouts
4. Check that the role-based access control still works correctly

## Prevention
- Always eager load relationships that are required for authorization checks
- Consider using `$with` property on the User model if role is frequently needed
- Add logging to middleware for debugging authorization issues

---

**Status:** ✅ Fixed
**Date:** October 14, 2025
