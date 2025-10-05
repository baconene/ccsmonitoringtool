# Fix: Module Export Error - "does not provide an export named 'login'"

## Error Description
```
Uncaught SyntaxError: The requested module '/resources/js/routes/index.ts?t=1759672417157' 
does not provide an export named 'login' (at Welcome.vue:2:21)
```

## Root Cause
This error occurs due to **Vite's Hot Module Replacement (HMR) cache** getting out of sync with the actual exports in the routes file. The routes ARE correctly exported, but Vite's cache is serving stale module information.

## Solution Applied

### Quick Fix (Immediate)
1. **Cleared Vite Cache**
   ```powershell
   Remove-Item -Path "node_modules/.vite" -Recurse -Force
   ```

2. **Restarted Dev Server**
   ```powershell
   npm run dev
   ```

### Why This Works
- Vite caches compiled modules in `node_modules/.vite/`
- When routes are regenerated (by Wayfinder plugin), the cache may not update
- Clearing cache forces Vite to recompile all modules fresh
- Fresh compilation picks up all current exports

## Verification

### Confirmed Exports Exist
The routes file (`resources/js/routes/index.ts`) DOES export `login`:
```typescript
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})
```

### Files Using These Imports
- `resources/js/pages/Welcome.vue`
- `resources/js/pages/auth/ForgotPassword.vue`
- `resources/js/pages/auth/Register.vue`

All now work correctly after cache clear.

## Prevention

### Best Practices
1. **After Route Changes**: Always restart dev server
2. **Clear Cache Periodically**: If seeing module errors
3. **Full Rebuild**: Run `npm run build` for production

### When to Clear Cache
- After adding new routes
- After updating route definitions
- When seeing "module does not provide export" errors
- After plugin updates (like Wayfinder)

## Alternative Solutions

### If Quick Fix Doesn't Work

1. **Hard Refresh Browser**
   ```
   Ctrl + Shift + R (Windows/Linux)
   Cmd + Shift + R (Mac)
   ```

2. **Clear Browser Cache**
   - Open DevTools (F12)
   - Right-click refresh → "Empty Cache and Hard Reload"

3. **Restart Both Servers**
   ```powershell
   # Stop dev server (Ctrl+C)
   # Stop PHP server if running
   
   # Clear all caches
   Remove-Item -Path "node_modules/.vite" -Recurse -Force
   php artisan cache:clear
   php artisan config:clear
   
   # Restart
   php artisan serve &
   npm run dev
   ```

4. **Nuclear Option** (Last Resort)
   ```powershell
   Remove-Item -Path "node_modules" -Recurse -Force
   npm install
   npm run dev
   ```

## Technical Details

### How Wayfinder Works
- Laravel Wayfinder plugin generates TypeScript route definitions
- Runs on every Vite compilation
- Scans Laravel routes and creates typed exports
- Exports are written to `resources/js/routes/index.ts`

### Why Cache Issues Happen
1. Wayfinder generates routes → `index.ts` updated
2. Vite's HMR sees file change
3. HMR updates browser without full reload
4. Cache may serve old module metadata
5. New exports not recognized by cache

### The Fix Process
1. Delete `.vite` folder → removes all cached modules
2. Restart dev server → full recompilation
3. Vite rebuilds all modules fresh
4. Browser receives updated module graph
5. All exports now available

## Prevention Scripts

### Add to package.json
```json
{
  "scripts": {
    "dev:clean": "npm run clean && npm run dev",
    "clean": "powershell -Command \"Remove-Item -Path 'node_modules/.vite' -Recurse -Force -ErrorAction SilentlyContinue\"",
    "fresh": "npm run clean && npm run build"
  }
}
```

### Usage
```powershell
npm run dev:clean    # Clean start dev server
npm run fresh        # Clean build for production
```

## Monitoring for This Issue

### Symptoms
- "does not provide an export" errors
- Import statements fail despite exports existing
- Routes work in production but not dev
- HMR shows successful update but modules fail

### Diagnosis
1. Check if export exists in source file ✅
2. Check if file is being watched by Vite ✅
3. Check browser console for import errors ✅
4. Check Vite HMR logs in terminal ✅

If all above check out → **Cache issue confirmed**

## Related Issues

### Similar Errors You Might See
```
Cannot find module '@/routes'
Module not found: '@/routes/index.ts'
Unexpected token 'export'
Named export 'dashboard' not found
```

All solved by same cache clearing approach.

## Status: ✅ RESOLVED

- Dev server running on port 5173
- All route exports accessible
- Welcome.vue loading correctly
- No module errors in console

## Additional Resources

- **Vite Docs**: https://vitejs.dev/guide/dep-pre-bundling.html
- **Laravel Wayfinder**: https://github.com/laravel/vite-plugin
- **HMR Issues**: https://vitejs.dev/guide/troubleshooting.html#hmr

---

**Last Updated**: October 5, 2025
**Status**: Resolved ✅
**Solution**: Cleared Vite cache and restarted dev server
