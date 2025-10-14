#!/bin/bash
# Document Storage Debug Script for Production Server

echo "=== Document Storage Diagnostic ==="
echo ""

# 1. Check if storage symlink exists
echo "1. Checking storage symlink..."
if [ -L "public/storage" ]; then
    echo "   ✓ Symlink EXISTS"
    echo "   Target: $(readlink -f public/storage)"
else
    echo "   ✗ Symlink MISSING"
fi
echo ""

# 2. Check storage directory structure
echo "2. Checking storage directories..."
if [ -d "storage/app/public/documents" ]; then
    echo "   ✓ storage/app/public/documents exists"
    echo "   Contents:"
    ls -la storage/app/public/documents/
else
    echo "   ✗ storage/app/public/documents MISSING"
fi
echo ""

# 3. Check if files exist
echo "3. Checking for uploaded files..."
file_count=$(find storage/app/public/documents -type f 2>/dev/null | wc -l)
echo "   Found $file_count file(s)"
if [ $file_count -gt 0 ]; then
    echo "   Sample files:"
    find storage/app/public/documents -type f -print | head -5
fi
echo ""

# 4. Check database for document records
echo "4. Checking database for document records..."
php artisan tinker --execute="
    echo 'Recent documents from database:' . PHP_EOL;
    \$docs = App\Models\Document::latest()->take(5)->get(['id', 'name', 'file_path', 'extension']);
    if (\$docs->isEmpty()) {
        echo '   No documents found in database' . PHP_EOL;
    } else {
        foreach (\$docs as \$doc) {
            echo '   ID: ' . \$doc->id . ' | Name: ' . \$doc->name . ' | Path: ' . \$doc->file_path . PHP_EOL;
            \$fullPath = storage_path('app/public/' . \$doc->file_path);
            echo '     File exists: ' . (file_exists(\$fullPath) ? 'YES' : 'NO') . PHP_EOL;
        }
    }
"
echo ""

# 5. Check permissions
echo "5. Checking permissions..."
ls -la storage/app/public | head -10
echo ""

# 6. Test file access via web
echo "6. Testing web access..."
if [ -f "storage/app/public/documents/course/test.txt" ]; then
    echo "test" > storage/app/public/documents/course/test.txt
    echo "   Created test file: storage/app/public/documents/course/test.txt"
    echo "   Try accessing: https://baconologies.com/storage/documents/course/test.txt"
fi
echo ""

echo "=== Diagnostic Complete ==="
echo ""
echo "Next steps:"
echo "1. If symlink is missing: php artisan storage:link --force"
echo "2. If permissions are wrong: chmod -R 775 storage"
echo "3. If files exist but not accessible: Check .htaccess or nginx config"
