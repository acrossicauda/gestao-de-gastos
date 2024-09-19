# Usar a imagem base do Node.js
FROM node:22-alpine

# Definir diretório de trabalho
WORKDIR /var/www/app/frontend


# Copiar package.json e instalar dependências
COPY ./../../app/frontend/package.json package.json
COPY ./../../app/frontend/package-lock.json package-lock.json
# app/frontend/package*.json ./
# COPY ./../../app/backend/composer.json composer.json

RUN npm -v 

RUN npm cache clean --force

RUN npm install


# Copiar o restante dos arquivos do projeto
# COPY . .

# Compilar o projeto para produção
# RUN npm run build

# Expor a porta padrão do Vue (8080)
EXPOSE 8080

# Comando para iniciar o servidor
# Comando para iniciar o servidor de desenvolvimento
# CMD ["npm", "run", "serve", "--", "--host", "0.0.0.0", "--port", "8080"]
CMD ["npm", "run", "serve", "--"]