# 🚀 Guia Completo - Como Rodar o Backend

## 📋 Pré-requisitos

- Docker Desktop instalado e rodando
- WSL2 configurado
- Git

---

## 🔧 Passo a Passo

### 1. Navegar até o Projeto Backend

```bash
cd ~/projetos/servlink-api
```

### 2. Verificar se o Docker está Rodando

```bash
docker --version
docker ps
```

### 3. Subir os Containers (Laravel Sail)

```bash
# Primeira vez ou se os containers não existem
./vendor/bin/sail up -d

# OU se preferir usar o alias
sail up -d
```

**Flags:**
- `-d` = detached mode (roda em background)

### 4. Verificar se os Containers Subiram

```bash
./vendor/bin/sail ps

# Você deve ver algo como:
# NAME                    STATUS
# servlink-api-laravel    Up
# servlink-api-mysql      Up
# servlink-api-redis      Up (se configurado)
```

### 5. Rodar Migrations (Se Necessário)

```bash
# Rodar migrations
./vendor/bin/sail artisan migrate

# OU resetar banco e rodar migrations
./vendor/bin/sail artisan migrate:fresh

# OU com seeders
./vendor/bin/sail artisan migrate:fresh --seed
```

### 6. Verificar se o Backend Está Funcionando

```bash
# Testar endpoint
curl http://localhost/api/health

# OU abrir no navegador
# http://localhost
```

---

## 🎯 Comandos Úteis

### Gerenciar Containers

```bash
# Subir containers
./vendor/bin/sail up -d

# Parar containers
./vendor/bin/sail stop

# Parar e remover containers
./vendor/bin/sail down

# Ver logs
./vendor/bin/sail logs

# Ver logs em tempo real
./vendor/bin/sail logs -f
```

### Artisan Commands

```bash
# Rodar migrations
./vendor/bin/sail artisan migrate

# Resetar banco
./vendor/bin/sail artisan migrate:fresh

# Rodar seeders
./vendor/bin/sail artisan db:seed

# Limpar cache
./vendor/bin/sail artisan cache:clear

# Ver rotas
./vendor/bin/sail artisan route:list
```

### Testes

```bash
# Rodar todos os testes
./vendor/bin/sail artisan test

# Rodar testes específicos
./vendor/bin/sail artisan test --filter JobTest

# Com coverage
./vendor/bin/sail artisan test --coverage
```

### Composer

```bash
# Instalar dependências
./vendor/bin/sail composer install

# Atualizar dependências
./vendor/bin/sail composer update

# Adicionar pacote
./vendor/bin/sail composer require nome/pacote
```

### Banco de Dados

```bash
# Acessar MySQL
./vendor/bin/sail mysql

# Executar query
./vendor/bin/sail mysql -e "SELECT * FROM users;"

# Backup do banco
./vendor/bin/sail exec mysql mysqldump -u sail -p servlink > backup.sql
```

---

## 🔍 Verificar se Está Tudo OK

### 1. Testar API

```bash
# Health check
curl http://localhost/api/health

# Listar vagas (público)
curl http://localhost/api/jobs

# Registrar usuário
curl -X POST http://localhost/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Teste",
    "email": "teste@example.com",
    "password": "senha123",
    "password_confirmation": "senha123",
    "role": "professional"
  }'
```

### 2. Verificar Logs

```bash
# Ver logs do Laravel
./vendor/bin/sail logs laravel.test

# Ver logs do MySQL
./vendor/bin/sail logs mysql

# Ver todos os logs
./vendor/bin/sail logs
```

### 3. Verificar Banco de Dados

```bash
# Entrar no MySQL
./vendor/bin/sail mysql

# Dentro do MySQL:
SHOW DATABASES;
USE servlink;
SHOW TABLES;
SELECT * FROM users;
exit;
```

---

## 🐛 Problemas Comuns

### Erro: "Port 80 already in use"

```bash
# Parar Apache/Nginx local
sudo service apache2 stop
sudo service nginx stop

# OU mudar porta no docker-compose.yml
# Alterar: "80:80" para "8000:80"
```

### Erro: "Connection refused"

```bash
# Verificar se containers estão rodando
./vendor/bin/sail ps

# Reiniciar containers
./vendor/bin/sail restart
```

### Erro: "Database connection failed"

```bash
# Verificar .env
cat .env | grep DB_

# Deve ter:
# DB_CONNECTION=mysql
# DB_HOST=mysql
# DB_PORT=3306
# DB_DATABASE=servlink
# DB_USERNAME=sail
# DB_PASSWORD=password

# Recriar banco
./vendor/bin/sail artisan migrate:fresh
```

### Erro: "Permission denied"

```bash
# Dar permissão ao Sail
chmod +x ./vendor/bin/sail

# OU rodar com sudo
sudo ./vendor/bin/sail up -d
```

---

## 📊 Estrutura de URLs

### Backend (API)

- **Base URL:** `http://localhost`
- **API URL:** `http://localhost/api`

### Endpoints Principais

```
GET    /api/jobs              - Listar vagas
POST   /api/register          - Registrar
POST   /api/login             - Login
POST   /api/logout            - Logout
GET    /api/user              - Usuário autenticado
GET    /api/applications      - Candidaturas
GET    /api/shifts            - Turnos
GET    /api/payments          - Pagamentos
GET    /api/ratings           - Avaliações
```

---

## 🚀 Workflow Completo

### Iniciar Desenvolvimento

```bash
# 1. Navegar para o projeto
cd ~/projetos/servlink-api

# 2. Subir containers
./vendor/bin/sail up -d

# 3. Verificar status
./vendor/bin/sail ps

# 4. Ver logs (opcional)
./vendor/bin/sail logs -f
```

### Parar Desenvolvimento

```bash
# Parar containers (mantém dados)
./vendor/bin/sail stop

# OU parar e remover (limpa tudo)
./vendor/bin/sail down
```

### Resetar Banco de Dados

```bash
# Resetar e rodar migrations
./vendor/bin/sail artisan migrate:fresh

# Resetar com seeders
./vendor/bin/sail artisan migrate:fresh --seed
```

---

## 🔗 Integração com Frontend

### Frontend deve apontar para:

```env
# servlink-web/.env.local
NEXT_PUBLIC_API_URL=http://localhost/api
```

### Testar Integração

```bash
# 1. Backend rodando
cd ~/projetos/servlink-api
./vendor/bin/sail up -d

# 2. Frontend rodando
cd ~/projetos/servlink-web
npm run dev

# 3. Acessar
# Frontend: http://localhost:3000
# Backend: http://localhost/api
```

---

## 📝 Alias Útil (Opcional)

Adicione ao `~/.bashrc` ou `~/.zshrc`:

```bash
# Alias para Sail
alias sail='./vendor/bin/sail'

# Recarregar
source ~/.bashrc
```

Agora pode usar apenas:

```bash
sail up -d
sail artisan migrate
sail test
```

---

## ✅ Checklist de Verificação

Após iniciar o backend, verifique:

- [ ] Containers rodando (`sail ps`)
- [ ] API respondendo (`curl http://localhost/api/jobs`)
- [ ] Banco de dados acessível (`sail mysql`)
- [ ] Migrations rodadas (`sail artisan migrate:status`)
- [ ] Sem erros nos logs (`sail logs`)

---

## 🎯 Resumo Rápido

```bash
# INICIAR BACKEND
cd ~/projetos/servlink-api
./vendor/bin/sail up -d

# VERIFICAR
./vendor/bin/sail ps
curl http://localhost/api/jobs

# PARAR
./vendor/bin/sail stop
```

---

**Status:** 🟢 Guia Completo
**Backend URL:** http://localhost/api
**Data:** 2025-11-24 21:09

**🚀 BACKEND PRONTO PARA RODAR!**
