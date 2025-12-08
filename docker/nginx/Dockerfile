FROM nginx:alpine

# Copy nginx configuration files
COPY conf.d/default.conf /etc/nginx/conf.d/default.conf
COPY conf.d/default-ssl.conf /etc/nginx/conf.d/default-ssl.conf

# Remove default nginx config if it exists
RUN rm -f /etc/nginx/conf.d/default.conf.bak 2>/dev/null || true

# Nginx will use the configs from /etc/nginx/conf.d/
# The default.conf will handle HTTP requests
# The default-ssl.conf can be used if SSL certificates are mounted

