# builder stage
FROM node:20-alpine AS builder

WORKDIR /app

COPY ./frontend/package*.json /app
RUN npm install

COPY ./frontend /app
RUN npm run build

#production stage
FROM nginx:alpine AS production

COPY nginx.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www

COPY ./backend /var/www

COPY --from=builder /app/dist /var/www/public/dist

# Ensure correct permissions
RUN chown -R nginx:nginx /var/www

# Expose port 80 and start Nginx
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]


