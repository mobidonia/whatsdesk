# Coolify Deployment Guide

## Quick Start

1. **Connect your Git repository** to Coolify
2. **Select "Docker Compose"** as deployment type
3. **Use `docker-compose.coolify.yml`** as your compose file
4. **Set environment variables** in Coolify UI (see below)
5. **Deploy**

## Environment Variables in Coolify

Set these in Coolify's environment variables section:

### Required Variables:
```
DB_HOST=db
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_strong_password
DB_ROOT_PASSWORD=your_root_password
```

### Application Variables:
```
APP_ENV=production
APP_DEBUG=false
FORCE_HTTPS=true
```

### Reverb (Websockets):
```
REVERB_APP_ID=whatsdesk
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=<generate-strong-secret>
REVERB_SCHEME=https
```

### Optional:
```
DOCKER_USER=laravel
DOCKER_UID=1000
DOCKER_IMAGE=your-registry/whatsdesk:latest
```

## Troubleshooting

### Issue: "Dockerfile: no such file or directory"

**Solution 1: Fix Build Context**
1. Ensure `Dockerfile` is in the repository root and committed to git
2. Verify `dockerfile: ./Dockerfile` is specified in docker-compose.coolify.yml
3. Check that `context: .` points to the repository root
4. In Coolify, verify the build context directory is set correctly

**Solution 2: Use Pre-built Image (Recommended)**
If builds keep failing, use a pre-built image instead:

1. **Build and push image locally or via CI/CD:**
   ```bash
   docker build -t mobidonia/whatsdesk:latest .
   docker push mobidonia/whatsdesk:latest
   ```

2. **Use `docker-compose.coolify-prebuilt.yml`** instead:
   - This file uses pre-built images (no build step)
   - Faster deployments
   - More reliable

3. **Set `DOCKER_IMAGE` environment variable** in Coolify:
   ```
   DOCKER_IMAGE=mobidonia/whatsdesk:latest
   ```

**Solution 3: Check Coolify Build Settings**
- In Coolify, check the "Build Context" setting
- Ensure it's set to repository root (`/` or `.`)
- Verify Dockerfile path is correct

### Issue: Build fails

**Check:**
- All required files are in the repository (Dockerfile, docker-entrypoint.sh)
- Build arguments are set correctly
- Repository is accessible to Coolify

### Issue: "composer.json not found" or "Could not open input file: artisan"

**This means the application code is not being mounted correctly.**

**Solution 1: Verify Repository Structure**
- Ensure `composer.json` and `artisan` are in the repository root
- The `docker-compose.coolify-prebuilt.yml` file should be in the repository root
- Coolify clones the entire repository, so all files should be accessible

**Solution 2: Check Coolify Volume Mounting**
- In Coolify, go to your service settings
- Verify the "Source" directory is set correctly (usually repository root)
- Check that the compose file path is correct
- Ensure the repository is fully cloned (check Coolify logs)

**Solution 3: Verify File Permissions**
- Ensure repository files are readable
- Check that `.gitignore` isn't excluding important files
- Verify `composer.json` and `artisan` are committed to git

**Solution 4: Use Build Instead of Pre-built Image**
- If volumes aren't mounting correctly, build the image WITH code:
  - Modify `Dockerfile` to `COPY . /var/www` before the entrypoint
  - Build and push the image with code included
  - Then you won't need volume mounts

### Issue: Database connection fails

**Check:**
- Database service is running: `docker-compose ps db`
- Environment variables are set correctly in Coolify
- Database credentials match between app and db services

### Issue: SSL not working

Coolify automatically handles SSL via Caddy. If SSL isn't working:
- Check domain DNS points to Coolify server
- Verify domain is configured in Coolify
- Check Coolify logs for SSL certificate issues

## Service Architecture

```
Internet
  ↓
Coolify Caddy (HTTPS/SSL) ← Automatic SSL certificates
  ↓
Nginx (HTTP, port 80) ← Handles static files, URL rewriting
  ↓
PHP-FPM (FastCGI, port 9000) ← Your Laravel application
```

## Port Configuration

- **Nginx**: Exposes port 80 (Coolify proxies to this)
- **PHP-FPM**: Exposes port 9000 (Nginx connects via FastCGI)
- **Reverb**: Exposes port 8080 (Nginx proxies WebSockets)
- **Database**: Internal only (not exposed)

## Network

Coolify automatically creates and manages the Docker network. All services communicate via service names (`app`, `db`, `nginx`, `reverb`).

## Database Options

### Option 1: Internal Database (Current Setup)
- Database runs in the same compose file
- Data persists via Docker volumes
- Good for single-server deployments

### Option 2: External Database
- Remove `db` service from docker-compose.coolify.yml
- Set `DB_HOST` to external database hostname
- Use Coolify's database service or external provider

## Deployment Checklist

- [ ] Repository connected to Coolify
- [ ] `docker-compose.coolify.yml` selected
- [ ] All environment variables set
- [ ] Domain configured in Coolify
- [ ] DNS records point to Coolify server
- [ ] Database credentials are strong and secure
- [ ] `REVERB_APP_SECRET` is a strong random value
- [ ] `APP_DEBUG=false` for production
- [ ] `FORCE_HTTPS=true` for production

## After Deployment

1. **Check logs**: View application logs in Coolify dashboard
2. **Verify SSL**: Visit your domain - should show HTTPS
3. **Test WebSockets**: Verify Reverb connections work
4. **Monitor**: Set up monitoring and alerts in Coolify

## Updating the Application

1. **Push changes** to your Git repository
2. **Coolify will detect** the changes
3. **Rebuild and redeploy** automatically (if auto-deploy enabled)
4. Or **manually trigger** deployment in Coolify dashboard

