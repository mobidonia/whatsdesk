# Rebuild Instructions for Coolify Deployment

## What Changed

1. **Dockerfile**: Now copies application code into the image during build
2. **docker-compose.coolify-prebuilt.yml**: Removed volume mounts from `app`, `reverb`, and `queue` services
3. **docker-entrypoint.sh**: Improved handling for code that's already in the image

## Why This Fix

Coolify wasn't mounting the repository code correctly via volumes. By baking the code into the Docker image during build, we eliminate the dependency on volume mounting for the application containers.

## Next Steps

### 1. Build the New Image

Build the image with code included:

```bash
docker build -t mobidonia/whatsdesk:latest .
```

Or with build arguments if needed:

```bash
docker build \
  --build-arg user=laravel \
  --build-arg uid=1000 \
  -t mobidonia/whatsdesk:latest .
```

### 2. Push to Your Registry

Push the image to your Docker registry:

```bash
docker push mobidonia/whatsdesk:latest
```

If using Docker Hub:
```bash
docker login
docker push mobidonia/whatsdesk:latest
```

If using a private registry:
```bash
docker tag mobidonia/whatsdesk:latest your-registry.com/mobidonia/whatsdesk:latest
docker push your-registry.com/mobidonia/whatsdesk:latest
```

### 3. Update Coolify Environment Variable

In Coolify, set the `DOCKER_IMAGE` environment variable to point to your image:

```
DOCKER_IMAGE=mobidonia/whatsdesk:latest
```

Or if using a private registry:
```
DOCKER_IMAGE=your-registry.com/mobidonia/whatsdesk:latest
```

### 4. Redeploy in Coolify

1. Go to your service in Coolify
2. Click "Redeploy" or trigger a new deployment
3. The new image will be pulled and deployed

## What's Different Now

### Before (Volume Mounting)
- Code was mounted via `./:/var/www` volume
- Coolify had to mount the repository code correctly
- If mounting failed, code wasn't available

### After (Code in Image)
- Code is copied into the image during build
- No volume mounting needed for app/reverb/queue
- Code is always available, regardless of volume mounting
- Nginx still uses volumes for static files and configs

## Benefits

1. **More Reliable**: Code is always in the image
2. **Faster Deployments**: No need to wait for volume mounting
3. **Easier Debugging**: Code location is consistent
4. **Production Ready**: Matches best practices for containerized applications

## Notes

- **Nginx still uses volumes** for static files (`./:/var/www`) and configs (`./docker/nginx/conf.d/`)
- **Database uses volumes** for data persistence (`dbdata` volume)
- **Application code** is now in the image, not mounted
- **Composer dependencies** are installed during build (production optimized)

## Troubleshooting

If you still see "composer.json not found" after rebuilding:
1. Verify the Dockerfile has the `COPY . /var/www` line
2. Check that `composer.json` is in your repository root
3. Verify the build completed successfully
4. Check that the pushed image contains the code:
   ```bash
   docker run --rm mobidonia/whatsdesk:latest ls -la /var/www
   ```

## Future Updates

When you update your code:
1. Rebuild the image with new code
2. Push the new image
3. Redeploy in Coolify

Consider setting up CI/CD to automate this process!

