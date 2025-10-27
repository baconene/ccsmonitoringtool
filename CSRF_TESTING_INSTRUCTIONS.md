# CSRF Token Fix - Testing Instructions

## ✅ Fix Applied Successfully

The CSRF token 419 error has been fixed by adding axios request interceptors that fetch a fresh CSRF token before every request.

## 🧪 Testing Steps

### 1. Clear Browser Cache
Before testing, clear your browser cache and hard refresh:
- **Chrome/Edge**: Press `Ctrl + Shift + Delete` or `Ctrl + F5`
- **Firefox**: Press `Ctrl + Shift + Delete`

### 2. Test Activity Creation (PRIMARY TEST)
This was the main failing endpoint from your screenshot.

1. Log in as an instructor
2. Navigate to **Activity Management** (`/activity-management`)
3. Click **"Create New Activity"** button
4. Fill in the form:
   - Title: `TEST CSRF FIX`
   - Description: `Testing CSRF token fix`
   - Activity Type: `Assignment`
   - Due Date: Select any future date
5. Click **"Create Activity"**

**Expected Result**: ✅ Activity created successfully, redirected to activity list

**If it fails**: ❌ Check browser console (F12) for errors

### 3. Test NotificationBell (SECONDARY TEST)
The NotificationBell polls every 10 seconds and should work without errors.

1. Stay logged in as instructor
2. Open browser DevTools (F12) → Network tab
3. Filter by: `notifications`
4. Wait for 10-30 seconds and observe the polling requests

**Expected Result**: ✅ No 419 errors in Network tab
- `GET /instructor/notifications/unread-count` → Status 200
- `GET /instructor/notifications/test` → Status 200
- `POST /instructor/notifications/{id}/read` → Status 200 (if marking as read)

### 4. Test Other Forms (OPTIONAL)

#### Course Creation
1. Navigate to **Course Management**
2. Click **"Create Course"** or edit existing course
3. Submit the form

**Expected**: ✅ No 419 errors

#### User Management
1. Navigate to **Role Management**
2. Try creating a new user
3. Submit the form

**Expected**: ✅ No 419 errors

#### Quiz Questions
1. Navigate to any Quiz activity
2. Try adding a new question
3. Submit the form

**Expected**: ✅ No 419 errors

## 🔍 How to Verify the Fix is Active

### Check Console for Token Refresh
Open browser console (F12) and run:

```javascript
// This should show the current CSRF token
console.log(document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

// Check if axios interceptors are active
console.log('Axios interceptors:', axios.interceptors.request.handlers.length);
// Should show > 0 if interceptor is registered
```

### Monitor Network Requests
1. Open DevTools (F12) → Network tab
2. Click on any POST/PUT/DELETE request
3. Check **Request Headers** section
4. Verify `X-CSRF-TOKEN` header is present

**Example**:
```
X-CSRF-TOKEN: aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890
X-Requested-With: XMLHttpRequest
Accept: application/json
```

## 📊 Expected vs Previous Behavior

### Before Fix ❌
```
User creates activity
  → POST /activities
  → Headers: X-CSRF-TOKEN: [stale token from page load]
  → Response: 419 (CSRF Token Mismatch)
  → Form submission fails
```

### After Fix ✅
```
User creates activity
  → Axios interceptor runs
  → Reads fresh token from DOM
  → POST /activities
  → Headers: X-CSRF-TOKEN: [fresh token]
  → Response: 200/201 (Success)
  → Form submission succeeds
```

## 🐛 Troubleshooting

### If you still see 419 errors:

#### 1. Check if build was applied
```powershell
# Verify build timestamp
Get-ChildItem public/build/manifest.json | Select-Object LastWriteTime
```
Should show recent timestamp (today's date).

#### 2. Hard refresh browser
- Press `Ctrl + Shift + R` (Chrome/Firefox)
- Or `Ctrl + F5`
- Or clear browser cache completely

#### 3. Check Laravel session
```powershell
# Clear Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

#### 4. Verify CSRF token in HTML
View page source and search for:
```html
<meta name="csrf-token" content="...">
```
Should be present in the `<head>` section.

#### 5. Check axios is using interceptor
In browser console:
```javascript
// Should log the interceptor function
console.log(axios.interceptors.request.handlers);
```

### If errors persist:

1. Check browser console for JavaScript errors
2. Check Laravel logs: `storage/logs/laravel.log`
3. Verify session driver in `.env`: `SESSION_DRIVER=file` (or cookie/redis)
4. Check if cookies are enabled in browser

## 📝 What Was Changed

### Files Modified:
1. ✅ `resources/js/app.ts` - Added request interceptor for global axios
2. ✅ `resources/js/utils/api.ts` - Added request interceptor for apiClient
3. ✅ Frontend rebuilt (`npm run build`)

### Technical Changes:
- Added `getCsrfToken()` function that reads from meta tag on every call
- Added `axios.interceptors.request.use()` to inject fresh token before each request
- Works for both global axios and apiClient instances
- Compatible with all existing axios calls and Inertia forms

## ✅ Success Indicators

You'll know the fix is working when:
- ✅ Activity creation succeeds without 419 errors
- ✅ Course forms submit successfully
- ✅ NotificationBell polls without errors in console
- ✅ No 419 errors in Network tab (DevTools)
- ✅ All POST/PUT/PATCH/DELETE requests work correctly

## 📞 Need Help?

If issues persist after following these steps:
1. Check browser console for errors
2. Check Network tab for failed requests
3. Review Laravel logs for server-side errors
4. Verify the build was applied (check manifest.json timestamp)

---

**Last Updated**: January 2025
**Status**: ✅ Fix Applied - Ready for Testing
**Build**: ✅ Completed Successfully (27.45s)
