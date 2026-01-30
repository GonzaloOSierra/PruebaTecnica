FROM node:18-slim

# Dependencias del sistema para Playwright
RUN apt-get update && apt-get install -y \
    wget curl git \
    libnss3 libatk1.0-0 libatk-bridge2.0-0 \
    libcups2 libx11-xcb1 libxcomposite1 \
    libxdamage1 libxrandr2 libgbm1 \
    libasound2 libpangocairo-1.0-0 \
    libgtk-3-0 \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /usr/src/app

# 1️⃣ Copiamos SOLO los manifests
COPY scraper/package.json scraper/package-lock.json ./

# 2️⃣ Instalamos dependencias (ACÁ se instala playwright)
RUN npm ci

# 3️⃣ Copiamos el resto del código
COPY scraper/ ./

# 4️⃣ Instalamos navegadores
RUN npx playwright install --with-deps

CMD ["tail", "-f", "/dev/null"]
