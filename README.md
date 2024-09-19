# Proejeto de estudos

### Rodando sem Docker:

Iniciar Projeto:
```
php artisa serve
```
OU 

Acessar a pasta 'public'
```
cd backend/public
php -S localhost:8000
```

Rodando migrations:
```
run php artisan migrations:migrate
```


### Rodando com Docker:
Iniciar container
```
docker-compose up --build
```

Rodando migrations:
```
docker-compose run php artisan migrations:migrate
```


URL LOCAL
```
http://localhost:8000/api
```


## FRONTEND

Instalação de dependências
```
npm install
```

Inicializar server
```
npm run serve
```

