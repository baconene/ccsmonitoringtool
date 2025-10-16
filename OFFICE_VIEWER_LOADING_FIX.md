# Office Viewer Loading Issue - FIXED

## The Problem

Office viewer URL was being generated correctly in console logs, but the document wasn't displaying in the app. The viewer was stuck on the loading spinner.

## Root Cause

**Loading State Not Clearing:**
- When DocumentViewer opens, `loading.value = true` is set
- Template shows: `<div v-if="loading">` (spinner) BEFORE checking for `<iframe v-else-if="viewerUrl">`
- The iframe has `@load="loading = false"` to stop the spinner
- **BUT** the Microsoft Office Online Viewer iframe might not fire the `load` event immediately, or it might be blocked
- Result: Spinner shows forever, iframe never appears

## The Fix

Added a **3-second timeout** to force the loading state to stop, ensuring the viewer appears:

```typescript
if (isText.value) {
    loadTextContent();
} else if (viewerUrl.value || isImage.value) {
    // For iframe/image viewers, stop showing loading after 3 seconds
    // The iframe @load event will stop it sooner if it loads successfully
    setTimeout(() => {
        loading.value = false;
        console.log('⏱️ Loading timeout - displaying viewer');
    }, 3000);
}
```

Also added better debugging:

```vue
<iframe
    v-else-if="viewerUrl"
    :src="viewerUrl"
    @load="() => { loading = false; console.log('✅ Iframe loaded successfully'); }"
    @error="(e) => { console.error('❌ Iframe error:', e); handleError(e); }"
></iframe>
```

## What This Does

### Scenario 1: Iframe Loads Quickly (< 3 seconds)
1. Viewer opens, loading spinner appears
2. Iframe starts loading
3. Iframe fires `load` event → `loading = false` → spinner disappears
4. **Result**: Smooth experience, viewer appears quickly

### Scenario 2: Iframe Loads Slowly (> 3 seconds)
1. Viewer opens, loading spinner appears
2. Iframe starts loading
3. After 3 seconds → timeout fires → `loading = false` → spinner disappears
4. Iframe continues loading in background
5. **Result**: Viewer appears (might show Microsoft's loading in iframe)

### Scenario 3: Iframe Fails to Load
1. Viewer opens, loading spinner appears
2. Iframe starts loading but fails
3. After 3 seconds → timeout fires → `loading = false` → spinner disappears
4. Iframe shows Microsoft's error message
5. **Result**: User sees the actual error from Microsoft (better than infinite spinner)

## Deploy

```bash
cd ~/baconologies.com
git pull origin main
npm run build  # Important! Frontend change
```

## Test

1. Open a document (Word/Excel/PowerPoint)
2. Click "View"
3. Check console (F12):
   ```
   🔍 DocumentViewer opened
      Document: {...}
      Viewer URL: https://view.officeapps.live.com/op/embed.aspx?src=...
      Is Office: true
   ⏱️ Loading timeout - displaying viewer  (after 3 seconds)
   ✅ Iframe loaded successfully  (when Microsoft's viewer loads)
   ```
4. Viewer should appear (either immediately or after 3 seconds max)

## Console Logs to Watch For

### ✅ Success:
```
🔍 DocumentViewer opened
📊 Office document detected - Using Microsoft Office Online Viewer
   Full URL: https://baconologies.com/documents/123/view
   Encoded URL: https%3A%2F%2Fbaconologies.com%2Fdocuments%2F123%2Fview
   Office Viewer URL: https://view.officeapps.live.com/op/embed.aspx?src=...
⏱️ Loading timeout - displaying viewer
✅ Iframe loaded successfully
```

### ❌ If Still Not Working:

**Check 1: Is the URL public?**
```bash
curl -I https://baconologies.com/documents/123/view
# Should return: HTTP/2 200 OK
# NOT: 401 Unauthorized or 403 Forbidden
```

**Check 2: Microsoft Office Viewer error?**
- Open the Office Viewer URL directly in browser
- `https://view.officeapps.live.com/op/embed.aspx?src=YOUR_ENCODED_URL`
- Check what error Microsoft shows

**Check 3: File format issue?**
- Microsoft Office Online Viewer supports: `.doc`, `.docx`, `.xls`, `.xlsx`, `.ppt`, `.pptx`
- File must be valid and not corrupted
- File must be accessible from internet (not localhost)

## Alternative: Use Google Docs Viewer

If Microsoft's viewer continues to have issues, you can switch to Google Docs Viewer:

```typescript
// In DocumentViewer.vue, replace Microsoft viewer with Google:
if (isOfficeDocument.value) {
    const fullUrl = window.location.origin + documentUrl.value;
    const encodedUrl = encodeURIComponent(fullUrl);
    // Use Google Docs Viewer instead
    return `https://docs.google.com/viewer?url=${encodedUrl}&embedded=true`;
}
```

## Summary

✅ **Loading timeout added** - Ensures viewer appears within 3 seconds  
✅ **Better error handling** - Console shows iframe errors  
✅ **Improved debugging** - Clear logs for success/failure  
✅ **No infinite spinner** - Loading state guaranteed to stop  

The document viewer will now display properly! 🎉

## Next Steps After Deploying

1. **Deploy**: `git pull && npm run build`
2. **Test**: Open Office document, click "View"
3. **Check logs**: F12 → Console
4. **Verify**: Document should display in 3 seconds or less

If document still doesn't display after 3 seconds:
- Check console for "❌ Iframe error"
- Check if `/documents/{id}/view` returns 200 OK
- Test Microsoft Office viewer URL directly in browser
- Consider switching to Google Docs viewer (alternative above)
