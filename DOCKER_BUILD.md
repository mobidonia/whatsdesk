# Docker Build and Push Guide

This guide explains how to build the WhatsDesk Docker image for local testing and push it to Docker Hub.

## Prerequisites

1. Docker installed and running
2. Docker Hub account (if pushing to registry)
3. Logged into Docker Hub: `docker login`

## Quick Start

### Option 1: Using the Build Script (Recommended)

```bash
# Build and push with version tag
./build-and-push.sh 1.0.0 mobidonia

# Build latest only
./build-and-push.sh latest mobidonia
```

The script will:
1. Build the image with correct build arguments
2. Optionally test it locally
3. Optionally push to Docker Hub

### Option 2: Manual Build Commands

#### 1. Build the Image Locally

```bash
# Build with build arguments
docker build \
  --build-arg user=laravel \
  --build-arg uid=1000 \
  -t mobidonia/whatsdesk:latest \
  .

# Or build with a specific version
docker build \
  --build-arg user=laravel \
  --build-arg uid=1000 \
  -t mobidonia/whatsdesk:1.0.0 \
  .
```

#### 2. Test the Image Locally

```bash
# Test PHP version
docker run --rm mobidonia/whatsdesk:latest php --version

# Test Composer
docker run --rm mobidonia/whatsdesk:latest composer --version

# Test entrypoint script
docker run --rm mobidonia/whatsdesk:latest ls -la /usr/local/bin/docker-entrypoint.sh

# Run a full test with docker-compose
docker-compose up -d
# Visit http://localhost
docker-compose down
```

#### 3. Tag the Image (if needed)

```bash
# Tag with version
docker tag mobidonia/whatsdesk:latest mobidonia/whatsdesk:1.0.0

# Tag for different registry
docker tag mobidonia/whatsdesk:latest your-registry.com/whatsdesk:latest
```

#### 4. Push to Docker Hub

```bash
# Login to Docker Hub first
docker login

# Push latest tag
docker push mobidonia/whatsdesk:latest

# Push versioned tag
docker push mobidonia/whatsdesk:1.0.0
```

## Using Docker Buildx (Multi-platform)

For building multi-platform images (ARM64, AMD64):

```bash
# Create a buildx builder (one-time setup)
docker buildx create --name multiarch --use

# Build and push for multiple platforms
docker buildx build \
  --platform linux/amd64,linux/arm64 \
  --build-arg user=laravel \
  --build-arg uid=1000 \
  -t mobidonia/whatsdesk:latest \
  -t mobidonia/whatsdesk:1.0.0 \
  --push \
  .
```

## Testing Locally with Docker Compose

After building, test with docker-compose:

```bash
# Build and start all services
docker-compose up -d --build

# Check logs
docker-compose logs -f app

# Stop services
docker-compose down

# Stop and remove volumes
docker-compose down -v
```

## Image Information

- **Base Image**: `php:8.2-fpm`
- **Working Directory**: `/var/www`
- **Default User**: `laravel` (UID: 1000)
- **Exposed Ports**: None (used via docker-compose)
- **Entrypoint**: `/usr/local/bin/docker-entrypoint.sh`

## Build Arguments

- `user`: System user name (default: `laravel`)
- `uid`: User ID (default: `1000`)

## Environment Variables

The image supports these environment variables (set in docker-compose.yml):

- `DB_HOST`: Database host
- `DB_PORT`: Database port
- `DB_USERNAME`: Database username
- `DB_PASSWORD`: Database password
- `DB_DATABASE`: Database name
- `REVERB_APP_ID`: Reverb application ID
- `REVERB_APP_KEY`: Reverb application key
- `REVERB_APP_SECRET`: Reverb application secret
- `REVERB_HOST`: Reverb host (default: 0.0.0.0)
- `REVERB_PORT`: Reverb port (default: 8080)
- `REVERB_SCHEME`: Reverb scheme (default: http)

## Troubleshooting

### Build fails with "docker-entrypoint.sh not found"

Make sure `docker-entrypoint.sh` is not in `.dockerignore`. Check the `.dockerignore` file.

### Composer install fails

The entrypoint script checks for `composer.json` before installing. Make sure your code is mounted or copied into the container.

### Permission errors

The image sets up proper permissions for Laravel directories. If you encounter permission issues, check that volumes are mounted correctly.

### Database connection errors

Ensure the database service is running and environment variables are set correctly in docker-compose.yml.

## Production Deployment

For production, consider:

1. **Use specific version tags** instead of `latest`
2. **Build multi-platform images** if supporting ARM64
3. **Scan images for vulnerabilities**: `docker scan mobidonia/whatsdesk:latest`
4. **Use private registries** for sensitive deployments
5. **Set proper resource limits** in docker-compose.yml

