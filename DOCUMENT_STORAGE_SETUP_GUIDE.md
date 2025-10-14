# Document Storage Setup and Troubleshooting Guide

## Problem
Documents were not uploading or viewing in production because:
1. ❌ **Storage symlink was missing** - Required for Laravel to serve files from `storage/app/public`
2. ❌ Files uploaded but not accessible via browser
3. ❌ DocumentViewer showing 404 errors

## Storage Architecture

### Directory Structure
```
storage/
├── app/
│   ├── private/          # Private files (not web-accessible)
│   └── public/           # Public files (web-accessible via symlink)
│       ├── documents/    # Uploaded documents
│       │   ├── activity/
│       │   ├── course/
│       │   ├── lesson/
│       │   └── module/
│       └── module-documents/  # Legacy module documents
public/
└── storage/             # Symlink to storage/app/public ⚠️ MUST EXIST
```

### How Laravel Storage Works

1. **Files are stored in:** `storage/app/public/documents/`
2. **Symlink points:** `public/storage/` → `storage/app/public/`
3. **Files are accessed via:** `http://yourdomain.com/storage/documents/...`

## Solution: Create Storage Symlink

### Step 1: Create the Symlink (Run on Server)

#### Option A: Using Artisan Command (Recommended)
```bash
php artisan storage:link
```

**Expected Output:**
```
INFO  The [public/storage] link has been connected to [storage/app/public].
```

#### Option B: Manual Symlink Creation (If Artisan Fails)

**On Linux/Mac:**
```bash
ln -s ../storage/app/public public/storage
```

**On Windows (PowerShell as Administrator):**
```powershell
New-Item -ItemType SymbolicLink -Path "public/storage" -Target "storage/app/public"
```

### Step 2: Verify the Symlink

#### Check if symlink exists:
```bash
# Linux/Mac
ls -la public/storage

# Windows PowerShell
Get-Item public/storage | Select-Object Target, LinkType
```

**Expected Output (Windows):**
```
Target                                                          LinkType
------                                                          --------
{C:\path\to\project\storage\app\public}                        Junction
```

#### Check if files are accessible:
```bash
# Check storage directory
ls -la storage/app/public/documents/

# Check public symlink
ls -la public/storage/documents/
```

### Step 3: Verify Filesystem Configuration

#### Check `.env` file:
```env
FILESYSTEM_DISK=local
```

**Note:** Even though set to `local`, the DocumentController uses `'public'` disk explicitly:
```php
$path = $file->store('documents/' . $request->model_type, 'public');
```

#### Check `config/filesystems.php`:
```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
        'throw' => false,
    ],
],

'links' => [
    public_path('storage') => storage_path('app/public'),
],
```

## Document Upload Flow

### 1. File Upload (DocumentController.php)
```php
public function upload(Request $request)
{
    // Validate
    $request->validate([
        'files.*' => 'required|file|max:20480', // 20MB max
    ]);

    foreach ($request->file('files') as $file) {
        // Store file in storage/app/public/documents/{type}
        $path = $file->store('documents/' . $request->model_type, 'public');
        
        // Create document record
        $document = Document::create([
            'name' => $file->getClientOriginalName(),
            'file_path' => $path,  // e.g., "documents/module/abc123.pdf"
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'uploaded_by' => auth()->id(),
        ]);
    }
}
```

### 2. File URL Generation (Document Model)
```php
public function getFileUrlAttribute()
{
    // Converts: "documents/module/abc123.pdf"
    // To: "http://yourdomain.com/storage/documents/module/abc123.pdf"
    return asset('storage/' . $this->file_path);
}
```

### 3. File Viewing (DocumentController.php)
```php
public function view(Document $document)
{
    // Read from: storage/app/public/{file_path}
    $filePath = storage_path('app/public/' . $document->file_path);
    
    if (!file_exists($filePath)) {
        abort(404, 'File not found');
    }
    
    return response()->file($filePath, [
        'Content-Type' => $document->mime_type,
        'Content-Disposition' => 'inline; filename="' . $document->original_name . '"'
    ]);
}
```

### 4. File Download (DocumentController.php)
```php
public function download(Document $document)
{
    $filePath = storage_path('app/public/' . $document->file_path);
    
    if (!file_exists($filePath)) {
        abort(404, 'File not found');
    }
    
    return response()->download($filePath, $document->original_name);
}
```

## DocumentViewer Component

### URL Generation
```typescript
const documentUrl = computed(() => {
  if (!props.document?.id) return null;
  // Points to: /documents/{id}/view
  return `/documents/${props.document.id}/view`;
});
```

### Office Document Viewer
```typescript
const viewerUrl = computed(() => {
  if (isOfficeDocument.value) {
    const encodedUrl = encodeURIComponent(window.location.origin + documentUrl.value);
    // Uses Microsoft Office Online Viewer
    return `https://view.officeapps.live.com/op/embed.aspx?src=${encodedUrl}`;
  }
  return documentUrl.value;
});
```

## Troubleshooting

### Issue 1: 404 Not Found When Accessing Documents

**Symptoms:**
- Files upload successfully
- Database has document records
- `/storage/documents/...` returns 404

**Solution:**
```bash
# Create the storage symlink
php artisan storage:link

# Verify it exists
ls -la public/storage  # Linux/Mac
Get-Item public/storage  # Windows PowerShell
```

### Issue 2: Files Upload But Can't Be Found

**Check:**
```php
// In tinker or a route
php artisan tinker
>>> $doc = App\Models\Document::first();
>>> $doc->file_path;  // Should be: "documents/module/abc123.pdf"
>>> storage_path('app/public/' . $doc->file_path);
>>> file_exists(storage_path('app/public/' . $doc->file_path));  // Should be true
```

**Solution:**
```bash
# Check if file actually exists
ls -la storage/app/public/documents/

# Check permissions
chmod -R 775 storage/app/public  # Linux/Mac
```

### Issue 3: Permission Denied Errors

**Linux/Mac:**
```bash
# Set proper ownership
chown -R www-data:www-data storage/app/public

# Set proper permissions
chmod -R 775 storage/app/public
chmod -R 775 public/storage
```

**Windows:**
- Ensure IIS/Apache user has read/write permissions to `storage` and `public` folders

### Issue 4: Symlink Not Created (Shared Hosting)

Some shared hosting providers don't allow symlinks. Solutions:

#### Option A: Use `.htaccess` rewrite (Apache)
```apache
# In public/.htaccess
RewriteEngine On
RewriteRule ^storage/(.*)$ ../storage/app/public/$1 [L]
```

#### Option B: Copy files instead of symlink
```bash
# Not recommended, but works
cp -r storage/app/public public/storage
```

**Note:** If you copy files, you'll need to re-copy after every deployment.

### Issue 5: Office Viewer Not Working

**Symptoms:**
- Office documents won't load in viewer
- Microsoft viewer shows error

**Causes & Solutions:**

1. **Document not publicly accessible:**
   ```bash
   # Ensure your domain is accessible from internet
   # Microsoft's servers need to access the document
   # Won't work on localhost!
   ```

2. **HTTPS Required:**
   - Microsoft Office Online Viewer requires HTTPS
   - Local development: Use PDF preview instead

3. **File too large:**
   - Office viewer has file size limits (usually 10MB)
   - Large files should be downloaded instead

### Issue 6: Development vs Production Differences

**Development (Local):**
```env
APP_URL=http://localhost:8000
FILESYSTEM_DISK=local
```

**Production (Live Server):**
```env
APP_URL=https://yourdomain.com
FILESYSTEM_DISK=public  # Optional, but recommended
```

After changing `.env`:
```bash
php artisan config:clear
php artisan cache:clear
```

## Deployment Checklist

When deploying to production (e.g., Laravel Forge, Cloudways):

### ✅ Pre-Deployment
1. [ ] Commit all code changes
2. [ ] Push to repository
3. [ ] Ensure `.env.example` has correct `FILESYSTEM_DISK` setting

### ✅ On Server (After Deployment)
```bash
# 1. Create storage symlink
php artisan storage:link

# 2. Set permissions (Linux/Mac)
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# 3. Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 4. Verify symlink
ls -la public/storage

# 5. Test file upload
# Upload a test document through the UI
# Verify it appears in: storage/app/public/documents/

# 6. Test file access
# Try accessing: https://yourdomain.com/storage/documents/...
```

### ✅ For Laravel Forge Specifically

**Forge automatically creates the storage symlink on deployment**, but verify:

1. Go to your site in Forge dashboard
2. Navigate to "Files" > "Storage"
3. Confirm symlink exists
4. If not, run deployment script with:
   ```bash
   php artisan storage:link
   ```

### ✅ For Cloudways/Other Hosts

Add to deployment script or run manually:
```bash
cd /path/to/your/app
php artisan storage:link
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Testing Document Upload and View

### Test Script (Create a test route)
```php
// routes/web.php
Route::get('/test-documents', function () {
    $documents = \App\Models\Document::latest()->take(5)->get();
    
    $results = $documents->map(function ($doc) {
        $fullPath = storage_path('app/public/' . $doc->file_path);
        
        return [
            'id' => $doc->id,
            'name' => $doc->name,
            'file_path' => $doc->file_path,
            'file_exists' => file_exists($fullPath),
            'file_url' => $doc->file_url,
            'view_url' => route('documents.view', $doc),
            'download_url' => route('documents.download', $doc),
        ];
    });
    
    return [
        'symlink_exists' => is_link(public_path('storage')),
        'symlink_target' => is_link(public_path('storage')) ? readlink(public_path('storage')) : null,
        'storage_path' => storage_path('app/public'),
        'documents' => $results,
    ];
})->middleware('auth');
```

Access: `http://yourdomain.com/test-documents`

**Expected Output:**
```json
{
  "symlink_exists": true,
  "symlink_target": "C:/path/to/project/storage/app/public",
  "storage_path": "C:/path/to/project/storage/app/public",
  "documents": [
    {
      "id": 1,
      "name": "sample.pdf",
      "file_path": "documents/module/abc123.pdf",
      "file_exists": true,
      "file_url": "http://yourdomain.com/storage/documents/module/abc123.pdf",
      "view_url": "http://yourdomain.com/documents/1/view",
      "download_url": "http://yourdomain.com/documents/1/download"
    }
  ]
}
```

## Security Considerations

### File Upload Validation
```php
$request->validate([
    'files.*' => [
        'required',
        'file',
        'max:20480',  // 20MB
        'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png',
    ],
]);
```

### File Access Control
```php
// In DocumentController
public function view(Document $document)
{
    // Add authorization check
    if (!auth()->user()->canViewDocument($document)) {
        abort(403, 'Unauthorized');
    }
    
    // ... serve file
}
```

### Prevent Directory Traversal
```php
// Always use Laravel's storage methods
// DON'T manually construct paths like:
// ❌ $path = '../../../' . $userInput;

// DO use Laravel's storage:
// ✅ Storage::disk('public')->get($document->file_path);
```

## Summary

✅ **Storage symlink is REQUIRED** for document uploads/downloads to work  
✅ **Run `php artisan storage:link` on every server** (dev, staging, production)  
✅ **Verify symlink exists** using `ls -la public/storage` or `Get-Item public/storage`  
✅ **Set proper permissions** (775) on `storage/app/public`  
✅ **Test with uploaded file** to ensure full flow works  
✅ **Office viewer requires HTTPS** and publicly accessible URLs  
✅ **Always clear cache** after configuration changes  

## Quick Fix Commands

```bash
# Full reset if things aren't working
php artisan storage:link
chmod -R 775 storage
chmod -R 775 bootstrap/cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Test file access
php artisan tinker
>>> $doc = App\Models\Document::first();
>>> $doc->file_url;
>>> file_exists(storage_path('app/public/' . $doc->file_path));
```
