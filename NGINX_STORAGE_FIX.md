# Nginx Storage Configuration Fix for Laravel Forge

## Add this to your Nginx configuration in Forge:

Go to: **Forge Dashboard → Sites → baconologies.com → Files → Edit Nginx Configuration**

Add this block AFTER the `error_page 404 /index.php;` line and BEFORE the `location ~ \.php$` block:

```nginx
    error_page 404 /index.php;

    # Storage access configuration - allows access to uploaded files
    location /storage/ {
        alias /home/forge/baconologies.com/storage/app/public/;
        access_log off;
        expires max;
        add_header Cache-Control "public, immutable";
        
        # Ensure proper permissions
        autoindex off;
        
        # Handle CORS for Office viewer
        add_header Access-Control-Allow-Origin *;
        add_header Access-Control-Allow-Methods "GET, OPTIONS";
        add_header Access-Control-Allow-Headers "Origin, X-Requested-With, Content-Type, Accept";
        
        # Try to serve file directly
        try_files $uri =404;
    }

    location ~ \.php$ {
```

Save and Forge will automatically reload Nginx.

## After adding, test:

1. Visit: `https://baconologies.com/storage/documents/module/xiiYkWacXO1OGC8KueM6VMIpG8GAlPFn2LEPYKIt.pptx`
2. Should download the file instead of 403

## Alternative: If you don't want to edit Nginx

Disable Office viewer and use download-only for Office files. Edit `DocumentViewer.vue`:

```typescript
const viewerUrl = computed(() => {
  if (!documentUrl.value) return null;
  
  // PDF files - use browser's native PDF viewer
  if (isPdf.value) {
    return documentUrl.value;
  }
  
  // Office documents - Don't use viewer, show download option instead
  // Microsoft viewer requires public direct URL access which may not work with auth
  if (isOfficeDocument.value) {
    return null; // Will show "Preview Not Available" with download button
  }
  
  return null;
});
```

This way:
- ✅ PDFs will preview
- ✅ Images will preview  
- ✅ Office files will show "Download to view" message
- ✅ Everything works without fixing Nginx

Which approach do you prefer?
