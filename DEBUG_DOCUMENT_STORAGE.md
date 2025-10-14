# Document Storage Debug - Quick Test

Add this route temporarily to `routes/web.php`:

```php
// TEMPORARY DEBUG ROUTE - Remove after testing
Route::get('/debug-documents', function () {
    $results = [];
    
    // 1. Check symlink
    $results['symlink_exists'] = file_exists(public_path('storage'));
    $results['symlink_is_link'] = is_link(public_path('storage'));
    
    // 2. Check recent documents
    $docs = \App\Models\Document::latest()->take(5)->get();
    $results['total_documents'] = \App\Models\Document::count();
    $results['documents'] = [];
    
    foreach ($docs as $doc) {
        $fullPath = storage_path('app/public/' . $doc->file_path);
        $publicPath = public_path('storage/' . $doc->file_path);
        
        $results['documents'][] = [
            'id' => $doc->id,
            'name' => $doc->name,
            'file_path_db' => $doc->file_path,
            'storage_full_path' => $fullPath,
            'storage_file_exists' => file_exists($fullPath),
            'public_full_path' => $publicPath,
            'public_file_exists' => file_exists($publicPath),
            'file_url' => $doc->file_url,
            'view_url' => route('documents.view', $doc),
            'download_url' => route('documents.download', $doc),
        ];
    }
    
    // 3. Check directory structure
    $results['directories'] = [
        'storage/app/public exists' => file_exists(storage_path('app/public')),
        'storage/app/public/documents exists' => file_exists(storage_path('app/public/documents')),
        'public/storage exists' => file_exists(public_path('storage')),
    ];
    
    // 4. Check Laravel config
    $results['config'] = [
        'app_url' => config('app.url'),
        'filesystem_disk' => config('filesystems.default'),
        'public_disk_root' => config('filesystems.disks.public.root'),
        'public_disk_url' => config('filesystems.disks.public.url'),
    ];
    
    return response()->json($results, 200, [], JSON_PRETTY_PRINT);
})->middleware('auth');
```

## On Your Production Server:

1. **SSH into server:**
```bash
ssh forge@your-server
cd ~/baconologies.com
```

2. **Run the debug script:**
```bash
chmod +x debug-storage.sh
./debug-storage.sh
```

Or manually check:

```bash
# Check symlink
ls -la public/storage

# Should show something like:
# lrwxrwxrwx 1 forge forge 36 Oct 15 12:00 storage -> /home/forge/baconologies.com/storage/app/public

# Check if files exist
ls -la storage/app/public/documents/course/

# Check if accessible via public
ls -la public/storage/documents/course/

# They should show the SAME files (because of symlink)
```

3. **Test in browser:**
Visit: `https://baconologies.com/debug-documents`

This will show you exactly what's wrong.

## Most Likely Issues:

### Issue 1: Symlink Broken
**Fix:**
```bash
rm public/storage
php artisan storage:link --force
```

### Issue 2: Wrong File Path in Database
If files are uploaded to `/storage/app/public/` but database has wrong path:

**Check:**
```bash
php artisan tinker
>>> $doc = App\Models\Document::first();
>>> $doc->file_path;  // Should be: "documents/course/abc123.pdf"
>>> storage_path('app/public/' . $doc->file_path);  // Should point to actual file
>>> file_exists(storage_path('app/public/' . $doc->file_path));  // Should be true
```

### Issue 3: Permissions
**Fix:**
```bash
chmod -R 775 storage
chown -R forge:forge storage
```

## Quick Test File

Create a test file manually:
```bash
echo "This is a test" > storage/app/public/documents/course/test.txt
```

Then try accessing:
```
https://baconologies.com/storage/documents/course/test.txt
```

If you can't access it, the symlink is broken or web server config is wrong.

Let me know what the debug output shows!
