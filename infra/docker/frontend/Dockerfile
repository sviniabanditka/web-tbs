FROM node:20-alpine

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy project files
COPY . .

# Expose port 5173
EXPOSE 5173

# Start development server with host 0.0.0.0
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"] 