#!/bin/bash

# Build and Push Script for WhatsDesk Docker Image
# Usage: ./build-and-push.sh [version] [dockerhub-username]
# Example: ./build-and-push.sh 1.0.0 mobidonia

set -e

# Configuration
VERSION=${1:-latest}
DOCKERHUB_USERNAME=${2:-mobidonia}
IMAGE_NAME="${DOCKERHUB_USERNAME}/whatsdesk"
BUILD_ARGS="--build-arg user=laravel --build-arg uid=1000"

echo "=========================================="
echo "Building WhatsDesk Docker Image"
echo "=========================================="
echo "Image: ${IMAGE_NAME}:${VERSION}"
echo "Build Args: ${BUILD_ARGS}"
echo ""

# Build the image
echo "Step 1: Building Docker image..."
docker build ${BUILD_ARGS} -t ${IMAGE_NAME}:${VERSION} .

if [ $? -ne 0 ]; then
    echo "❌ Build failed!"
    exit 1
fi

echo "✅ Build successful!"
echo ""

# Tag as latest if version is not latest
if [ "$VERSION" != "latest" ]; then
    echo "Step 2: Tagging as latest..."
    docker tag ${IMAGE_NAME}:${VERSION} ${IMAGE_NAME}:latest
    echo "✅ Tagged as latest"
    echo ""
fi

# Ask if user wants to test locally
read -p "Do you want to test the image locally before pushing? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Step 3: Testing image locally..."
    echo "Running: docker run --rm ${IMAGE_NAME}:${VERSION} php --version"
    docker run --rm ${IMAGE_NAME}:${VERSION} php --version
    
    if [ $? -eq 0 ]; then
        echo "✅ Local test passed!"
    else
        echo "❌ Local test failed!"
        exit 1
    fi
    echo ""
fi

# Ask if user wants to push to Docker Hub
read -p "Do you want to push to Docker Hub? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Step 4: Pushing to Docker Hub..."
    echo "Make sure you're logged in: docker login"
    echo ""
    
    # Push versioned tag
    echo "Pushing ${IMAGE_NAME}:${VERSION}..."
    docker push ${IMAGE_NAME}:${VERSION}
    
    # Push latest tag if version is not latest
    if [ "$VERSION" != "latest" ]; then
        echo "Pushing ${IMAGE_NAME}:latest..."
        docker push ${IMAGE_NAME}:latest
    fi
    
    echo ""
    echo "✅ Push successful!"
    echo ""
    echo "Image available at: https://hub.docker.com/r/${DOCKERHUB_USERNAME}/whatsdesk"
fi

echo ""
echo "=========================================="
echo "Build and Push Complete!"
echo "=========================================="

