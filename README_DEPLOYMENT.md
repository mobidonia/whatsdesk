# Production Deployment Quick Reference

## 🚀 Coolify Deployment (Recommended)

Coolify automatically handles SSL, reverse proxy, and domain configuration.

### Steps:
1. **Connect your Git repository** to Coolify
2. **Select "Docker Compose"** as deployment type
3. **Use `docker-compose.yml`** or `docker-compose.coolify.yml`
4. **Set environment variables** in Coolify UI:
   ```
   DB_HOST=db
   DB_DATABASE=your_database
   DB_USERNAME=your_user
   DB_PASSWORD=your_password
   DB_ROOT_PASSWORD=your_root_password
   APP_ENV=production
   APP_DEBUG=false
   FORCE_HTTPS=true
   REVERB_APP_SECRET=<generate-strong-secret>
   ```
5. **Deploy** - Coolify handles the rest!

**Coolify automatically:**
- ✅ Provisions SSL certificates (via Caddy)
- ✅ Sets up reverse proxy
- ✅ Configures domain routing
- ✅ Handles WebSocket upgrades for Reverb
- ✅ Manages container networking

---

## 🔄 Traefik Deployment

If you have Traefik running:

1. **Uncomment Traefik labels** in `docker-compose.prod.yml`
2. **Set environment variables:**
   ```bash
   export DOMAIN=yourdomain.com
   export DB_PASSWORD=your_password
   # ... other variables
   ```
3. **Deploy:**
   ```bash
   docker-compose -f docker-compose.prod.yml up -d
   ```

Traefik will automatically:
- Detect containers via labels
- Provision SSL via Let's Encrypt
- Route traffic with HTTPS

---

## 🎯 Caddy Deployment

1. **Update `Caddyfile`** with your domain
2. **Add Caddy service** to docker-compose:
   ```yaml
   caddy:
     image: caddy:latest
     ports:
       - "80:80"
       - "443:443"
     volumes:
       - ./Caddyfile:/etc/caddy/Caddyfile
       - caddy_data:/data
   ```
3. **Deploy** - Caddy handles SSL automatically

---

## 📋 Environment Variables Checklist

**Required for Production:**
- [ ] `DB_PASSWORD` - Strong database password
- [ ] `DB_ROOT_PASSWORD` - Strong root password  
- [ ] `REVERB_APP_SECRET` - Generate with: `openssl rand -base64 32`
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `FORCE_HTTPS=true`
- [ ] `DOMAIN` - Your domain name

**Optional but Recommended:**
- [ ] `REVERB_APP_ID` - Custom app ID
- [ ] `REVERB_APP_KEY` - Custom app key
- [ ] `DOCKER_IMAGE` - Your registry image

---

## 🔒 Security Checklist

Before going live:
- [ ] All passwords changed from defaults
- [ ] `APP_DEBUG=false` set
- [ ] `FORCE_HTTPS=true` enabled
- [ ] Strong `REVERB_APP_SECRET` generated
- [ ] Database backups configured
- [ ] Monitoring set up
- [ ] Log rotation configured

---

## 📚 Full Documentation

See [DEPLOYMENT.md](./DEPLOYMENT.md) for detailed instructions for all platforms.

