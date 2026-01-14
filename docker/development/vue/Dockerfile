FROM node:20-alpine

# Set environment variable to indicate development mode
ENV NODE_ENV=development

WORKDIR /app

COPY ./frontend/package*.json /app

# Install project dependencies
# --mount=type=cache: Creates a persistent cache to speed up subsequent builds
# target=/root/.npm: Points to npm's internal cache folder to reuse downloaded packages
RUN --mount=type=cache,target=/root/.npm npm install

# COPY ./frontend /app/

RUN chown -R node:node /app

USER node

EXPOSE 5173

# Start the development server
# --: tells npm to pass the following arguments to the underlying script (Vite)
# --host: exposes the server to the network (0.0.0.0) so it's accessible outside the container
CMD [ "npm", "run", "dev", "--", "--host" ]

