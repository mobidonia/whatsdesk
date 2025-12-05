# Fix: Code Not Mounting in Coolify

## Problem
The application code (`composer.json`, `artisan`) is not being found in the container, causing:
- `composer.json not found`
- `Could not open input file: artisan`

This means Coolify isn't mounting the repository code correctly.

## Solution 1: Rebuild Image WITH Code (Recommended)

Modify the `Dockerfile` to copy the code into the image during build:

```dockerfile
# Add this after line 45 (after copying entrypoint)
# Copy application code into the image
COPY --chown=www-data:www-data . /var/www

# Install dependencies during build (optional but recommended)
RUN if [ -f composer.json ]; then \
    composer install --no-dev --optimize-autoloader --no-interaction || true; \
fi
```

Then rebuild and push:
```bash
docker build -t mobidonia/whatsdesk:latest .
docker push mobidonia/whatsdesk:latest
```

**After rebuilding**, remove volume mounts from `docker-compose.coolify-prebuilt.yml`:
```yaml
app:
  # Remove or comment out:
  # volumes:
  #   - ./:/var/www
```

## Solution 2: Fix Coolify Volume Mounting

1. **In Coolify Dashboard:**
   - Go to your service settings
   - Check "Source" directory - should point to repository root
   - Verify compose file path is correct
   - Ensure repository is fully cloned

2. **Verify Repository Structure:**
   - `docker-compose.coolify-prebuilt.yml` should be in repository root
   - `composer.json` and `artisan` should be in repository root
   - All files committed to git

3. **Check Coolify Logs:**
   - Look for volume mounting errors
   - Verify the compose file is being read correctly

## Solution 3: Use Build Instead of Pre-built Image

Switch to `docker-compose.coolify.yml` which builds from source:
- Code is copied during build
- No volume mounting needed
- Slower deployments but more reliable

## Current Status

The compose file is configured to mount `./:/var/www`, but Coolify may not be mounting it correctly. The entrypoint script now provides better error messages to help diagnose the issue.

## Quick Check

After deploying, check the app container logs. If you see:
```
WARNING: Application code not found in /var/www
```

Then volume mounting failed. Use Solution 1 (rebuild with code) for the most reliable fix.

