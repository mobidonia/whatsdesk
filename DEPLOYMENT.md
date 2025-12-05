# Production Deployment Guide

This guide covers deploying WhatsDesk to production environments with SSL support using various reverse proxies.

## Supported Deployment Platforms

- **Coolify** - Automatic SSL with Caddy
- **Traefik** - With Let's Encrypt
- **Caddy** - Automatic HTTPS
- **DigitalOcean App Platform** - Managed platform
- **Direct Nginx** - With SSL certificates

## Quick Start

### 1. Environment Variables

Create a `.env` file in your project root (or use your platform's environment variable system):

```bash
# Copy the example
cp docker-compose.env.example .env

# Edit with your production values
nano .env
```

**Required Production Variables:**
```bash
# Database (REQUIRED - no defaults in production)
DB_HOST=db
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_strong_password
DB_ROOT_PASSWORD=your_root_password

# Application
APP_ENV=production
APP_DEBUG=false
FORCE_HTTPS=true
DOMAIN=yourdomain.com

# Docker
DOCKER_IMAGE=your-registry/whatsdesk:latest
COMPOSE_PROJECT_NAME=whatsdesk

# Reverb (Websockets)
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-strong-secret
REVERB_SCHEME=https
```

## Deployment Options

### Option 1: Coolify Deployment

Coolify automatically handles SSL with Caddy. Simply:

1. **Connect your repository** to Coolify
2. **Select Docker Compose** as deployment type
3. **Use `docker-compose.yml`** (the main file)
4. **Set environment variables** in Coolify's UI
5. **Deploy**

Coolify will:
- Automatically provision SSL certificates
- Set up reverse proxy
- Handle domain configuration
- Manage container lifecycle

**Coolify-specific notes:**
- Coolify uses its own network, so remove `networks` section or set `EXTERNAL_NETWORK=true`
- Database can be managed separately or included in compose
- Environment variables are set via Coolify UI

### Option 2: Traefik Reverse Proxy

If you have Traefik running:

1. **Use `docker-compose.prod.yml`**:
```bash
docker-compose -f docker-compose.prod.yml up -d
```

2. **Set environment variables**:
```bash
export DOMAIN=yourdomain.com
export DB_PASSWORD=your_password
# ... other variables
```

3. **Traefik will automatically:**
   - Detect containers via labels
   - Provision SSL certificates via Let's Encrypt
   - Route traffic to your services

**Traefik Labels Explained:**
- `traefik.enable=true` - Enable Traefik for this service
- `traefik.http.routers.app.rule=Host(...)` - Domain routing
- `traefik.http.routers.app.tls.certresolver=letsencrypt` - SSL certificate
- `traefik.http.services.app.loadbalancer.server.port=9000` - Backend port

### Option 3: Caddy Reverse Proxy

Caddy provides automatic HTTPS:

1. **Create `Caddyfile`**:
```
yourdomain.com {
    reverse_proxy app:9000
    reverse_proxy /app reverb:8080 {
        transport http {
            versions h2c
        }
    }
}
```

2. **Add Caddy service to docker-compose**:
```yaml
caddy:
  image: caddy:latest
  container_name: whatsdesk-caddy
  restart: unless-stopped
  ports:
    - "80:80"
    - "443:443"
  volumes:
    - ./Caddyfile:/etc/caddy/Caddyfile
    - caddy_data:/data
    - caddy_config:/config
  networks:
    - laravel
```

### Option 4: Direct Nginx with SSL

For manual SSL certificate management:

1. **Place SSL certificates** in `docker/nginx/ssl/`:
   - `cert.pem` - Your SSL certificate
   - `key.pem` - Your private key

2. **Use SSL nginx config**:
```bash
# Copy SSL config
cp docker/nginx/conf.d/default-ssl.conf docker/nginx/conf.d/default.conf
```

3. **Update docker-compose.yml**:
```yaml
nginx:
  ports:
    - "80:80"
    - "443:443"
  volumes:
    - ./docker/nginx/ssl:/etc/nginx/ssl:ro
```

4. **Set domain**:
```bash
export DOMAIN=yourdomain.com
```

### Option 5: DigitalOcean App Platform

1. **Create `app.yaml`**:
```yaml
name: whatsdesk
services:
- name: app
  github:
    repo: your-org/whatsdesk
    branch: main
  dockerfile_path: Dockerfile
  http_port: 9000
  instance_count: 1
  instance_size_slug: basic-xxs
  envs:
  - key: DB_HOST
    value: ${db.HOSTNAME}
  - key: DB_PASSWORD
    value: ${db.PASSWORD}
    type: SECRET
  - key: APP_ENV
    value: production
  - key: FORCE_HTTPS
    value: "true"

databases:
- name: db
  engine: MYSQL
  version: "8"
  production: true
```

2. **Deploy via DigitalOcean CLI** or web interface

## Environment-Specific Configurations

### Development (Local)
```bash
docker-compose up -d
```
- Uses `docker-compose.yml`
- HTTP on port 8080
- Default passwords (change in production!)

### Production (Coolify/Traefik)
```bash
docker-compose -f docker-compose.prod.yml up -d
```
- Uses `docker-compose.prod.yml`
- SSL via reverse proxy
- Production environment variables required

### Production (Direct Nginx)
```bash
# Set SSL certificates first
export DOMAIN=yourdomain.com
docker-compose -f docker-compose.prod.yml up -d
```
- Manual SSL certificate management
- Nginx handles SSL termination

## Security Checklist

Before deploying to production:

- [ ] Change all default passwords
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Set `FORCE_HTTPS=true`
- [ ] Use strong `DB_PASSWORD` and `DB_ROOT_PASSWORD`
- [ ] Set strong `REVERB_APP_SECRET`
- [ ] Configure proper `DOMAIN`
- [ ] Enable SSL/TLS (automatic with Coolify/Traefik/Caddy)
- [ ] Review and restrict exposed ports
- [ ] Set up database backups
- [ ] Configure log rotation
- [ ] Set up monitoring and alerts

## Database Configuration

### Using External Database

If using an external database (managed service):

1. **Remove db service** from docker-compose.yml
2. **Set DB_HOST** to external database hostname
3. **Set DB_PORT** to external database port (usually 3306)
4. **Configure firewall** to allow connections

Example:
```yaml
environment:
  - DB_HOST=your-db-host.example.com
  - DB_PORT=3306
  - DB_USERNAME=your_user
  - DB_PASSWORD=your_password
  - DB_DATABASE=your_database
```

### Using Internal Database

Keep the `db` service in docker-compose.yml. The database will be:
- Accessible only within Docker network
- Persistent via Docker volumes
- Automatically backed up with volume backups

## Troubleshooting

### SSL Certificate Issues

**Problem:** SSL not working
- **Coolify/Traefik:** Check domain DNS points to server
- **Caddy:** Verify Caddyfile syntax
- **Nginx:** Check certificate paths and permissions

### Database Connection Issues

**Problem:** Can't connect to database
- Check `DB_HOST` matches service name (`db`) or external hostname
- Verify `DB_PASSWORD` matches MySQL `MYSQL_PASSWORD`
- Check network connectivity: `docker exec app ping db`
- Verify database is healthy: `docker-compose ps db`

### WebSocket (Reverb) Issues

**Problem:** WebSockets not working
- Ensure reverse proxy supports WebSocket upgrades
- Check `REVERB_SCHEME=https` in production
- Verify `/app` path is proxied correctly
- Check firewall allows WebSocket connections

## Monitoring

### Health Checks

All services have health checks:
```bash
# Check service health
docker-compose ps

# Check specific service logs
docker-compose logs app
docker-compose logs db
docker-compose logs reverb
```

### Application Logs

```bash
# Laravel logs
docker exec app tail -f storage/logs/laravel.log

# Nginx logs
docker exec nginx tail -f /var/log/nginx/error.log
```

## Backup Strategy

### Database Backups

```bash
# Manual backup
docker exec db mysqldump -u root -p${DB_ROOT_PASSWORD} laravel > backup.sql

# Automated backup (add to cron)
0 2 * * * docker exec whatsdesk-mysql mysqldump -u root -proot laravel | gzip > /backups/backup-$(date +\%Y\%m\%d).sql.gz
```

### Volume Backups

```bash
# Backup Docker volume
docker run --rm -v whatsdesk_dbdata:/data -v $(pwd):/backup alpine tar czf /backup/db-backup.tar.gz /data
```

## Scaling

### Horizontal Scaling

For high-traffic deployments:

1. **Scale app containers:**
```bash
docker-compose up -d --scale app=3
```

2. **Use load balancer** (Traefik/Caddy handle this automatically)

3. **Scale queue workers:**
```bash
docker-compose up -d --scale queue=2
```

### Resource Limits

Add to docker-compose.yml:
```yaml
services:
  app:
    deploy:
      resources:
        limits:
          cpus: '1'
          memory: 512M
        reservations:
          cpus: '0.5'
          memory: 256M
```

## Support

For platform-specific issues:
- **Coolify:** https://coolify.io/docs
- **Traefik:** https://doc.traefik.io/traefik/
- **Caddy:** https://caddyserver.com/docs/
- **DigitalOcean:** https://docs.digitalocean.com/products/app-platform/

