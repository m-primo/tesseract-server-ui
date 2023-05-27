FROM php:8.1-cli-alpine

# Set the environment variable for the port to be used
ARG PORT=8080
ENV PORT=$PORT

# Set the working directory to /app
WORKDIR /app

# Copy the files to the working directory
COPY ./app .

# Expose the port to the outside world
EXPOSE $PORT

# Run the script using PHP's built-in web server
ENTRYPOINT ["sh", "-c", "php -S 0.0.0.0:${PORT}"]