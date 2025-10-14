# Document Storage - Quick Deployment Checklist

## ✅ Local Development is Working
- [x] Storage symlink created (`php artisan storage:link`)
- [x] Files accessible at `http://localhost:8000/storage/documents/...`
- [x] DocumentViewer component working
- [x] Upload functionality working

## 🚀 For Production Deployment (Laravel Forge / Live Server)

### Step 1: SSH into your server
```bash
ssh forge@your-server-ip
cd /home/forge/yourdomain.com
```

### Step 2: Run this single command
```bash
php artisan storage:link && chmod -R 775 storage && php artisan config:clear && php artisan cache:clear
```

### Step 3: Verify (Optional)
```bash
# Check if symlink exists
ls -la public/storage

# Expected output:
# lrwxrwxrwx 1 forge forge 36 Oct 15 12:00 storage -> /home/forge/yourdomain.com/storage/app/public
```

### Step 4: Test Upload
1. Log into your app
2. Go to any module/course
3. Upload a test document
4. Try to view/download it
5. ✅ It should work!

## 🔧 If It's Still Not Working

### Quick Debug Commands
```bash
# Check symlink
ls -la public/storage

# Check permissions
ls -la storage/app/public

# Check if files exist
ls -la storage/app/public/documents/

# Fix permissions
chmod -R 775 storage
chmod -R 775 public/storage

# Recreate symlink
rm public/storage
php artisan storage:link

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Test in Browser
1. Upload a document
2. Check database: Look at `documents` table for the `file_path`
3. Try accessing directly: `https://yourdomain.com/storage/{file_path}`
4. Should download/display the file

## 📝 Common Issues

### Issue: 404 Not Found
**Solution:** Storage symlink missing
```bash
php artisan storage:link
```

### Issue: Permission Denied
**Solution:** Fix permissions
```bash
chmod -R 775 storage
chown -R forge:forge storage  # or www-data:www-data
```

### Issue: Office Viewer Not Working
**Reason:** Microsoft Office Online Viewer requires:
- ✅ HTTPS (not HTTP)
- ✅ Publicly accessible URL (not localhost)
- ✅ Files under 10MB

**For localhost/development:**
- PDFs will work (browser native viewer)
- Office docs won't work (requires public HTTPS)

## ⚡ Laravel Forge Auto-Deploy Script

Add this to your Forge deployment script (if not already there):

```bash
cd /home/forge/yourdomain.com
git pull origin $FORGE_SITE_BRANCH

$FORGE_COMPOSER install --no-interaction --prefer-dist --optimize-autoloader

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

if [ -f artisan ]; then
    $FORGE_PHP artisan storage:link --force
    $FORGE_PHP artisan migrate --force
    $FORGE_PHP artisan config:clear
    $FORGE_PHP artisan cache:clear
    $FORGE_PHP artisan view:clear
fi
```

## 🎯 Final Verification

Run this test route (add temporarily to `routes/web.php`):

```php
Route::get('/test-storage', function () {
    return [
        'symlink_exists' => file_exists(public_path('storage')),
        'storage_path' => storage_path('app/public'),
        'public_path' => public_path('storage'),
        'sample_document' => \App\Models\Document::latest()->first()?->file_url,
        'app_url' => config('app.url'),
    ];
})->middleware('auth');
```

Visit: `https://yourdomain.com/test-storage`

**Expected:**
```json
{
  "symlink_exists": true,
  "storage_path": "/home/forge/yourdomain.com/storage/app/public",
  "public_path": "/home/forge/yourdomain.com/public/storage",
  "sample_document": "https://yourdomain.com/storage/documents/module/abc123.pdf",
  "app_url": "https://yourdomain.com"
}
```

## 🎉 Success!

If you can:
- ✅ Upload documents
- ✅ View documents in DocumentViewer
- ✅ Download documents
- ✅ See PDFs inline

**You're all set!** 🚀

---

## Need Help?

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check permissions: `ls -la storage`
3. Re-run: `php artisan storage:link`
4. Clear caches: `php artisan config:clear && php artisan cache:clear`
