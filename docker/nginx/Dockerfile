# builder stage
FROM node:20-alpine AS builder

WORKDIR /app

COPY ./frontend/package*.json /app

# Install project dependencies using npm ci (ensures a clean, reproducible install)
RUN --mount=type=cache,target=/root/.npm npm ci

COPY ./frontend /app
RUN npm run build

#production stage
FROM nginx:alpine AS production

COPY ./docker/nginx/nginx.conf /etc/nginx/templates/default.conf.template

WORKDIR /var/www

COPY ./backend /var/www

COPY --from=builder /app/dist /var/www/public/dist

# Ensure correct permissions
RUN chown -R nginx:nginx /var/www

# Expose port 80 and start Nginx
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]


