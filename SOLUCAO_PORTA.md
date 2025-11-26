# 🔧 SOLUÇÃO: Porta 5173 Já Está em Uso

## ❌ Erro:
```
Error: Bind for 0.0.0.0:5173 failed: port is already allocated
```

## 🎯 SOLUÇÕES (Escolha UMA):

---

### ✅ SOLUÇÃO 1: Parar o Frontend (Recomendado)

Se você está rodando `npm run dev` no frontend, pare ele primeiro:

```bash
# No terminal do frontend, pressione:
Ctrl + C

# Depois tente novamente no backend:
cd ~/projetos/servlink-api
./vendor/bin/sail up -d
```

---

### ✅ SOLUÇÃO 2: Mudar a Porta do Docker

Edite o arquivo `docker-compose.yml`:

```bash
cd ~/projetos/servlink-api
nano docker-compose.yml
```

Procure por `5173:5173` e mude para outra porta, por exemplo `5174:5173`:

```yaml
# ANTES:
ports:
    - '5173:5173'

# DEPOIS:
ports:
    - '5174:5173'
```

Salve (Ctrl+O, Enter, Ctrl+X) e rode novamente:

```bash
./vendor/bin/sail up -d
```

---

### ✅ SOLUÇÃO 3: Remover a Porta do Vite (Mais Simples)

Se você não precisa do Vite no backend, comente a linha no `docker-compose.yml`:

```yaml
# ANTES:
ports:
    - '5173:5173'

# DEPOIS:
# ports:
#     - '5173:5173'
```

---

### ✅ SOLUÇÃO 4: Parar Containers e Tentar Novamente

```bash
# Parar todos os containers
./vendor/bin/sail down

# Matar processo na porta 5173 (se existir)
# No Windows/WSL, feche o terminal do frontend

# Subir novamente
./vendor/bin/sail up -d
```

---

## 🚀 SOLUÇÃO RÁPIDA (Recomendada):

**Execute estes comandos:**

```bash
# 1. Parar containers
./vendor/bin/sail down

# 2. Editar docker-compose.yml e comentar a porta 5173
# OU simplesmente fechar o terminal do frontend

# 3. Subir novamente
./vendor/bin/sail up -d
```

---

## ✅ VERIFICAR SE FUNCIONOU:

```bash
# Ver containers rodando
./vendor/bin/sail ps

# Testar API
curl http://localhost/api/jobs
```

---

## 📝 NOTA IMPORTANTE:

A porta 5173 é usada pelo Vite (frontend). Você tem duas opções:

1. **Rodar apenas o backend** (sem Vite no Docker)
2. **Rodar frontend separado** com `npm run dev`

**Recomendação:** Comente a porta 5173 no `docker-compose.yml` e rode o frontend separadamente com `npm run dev`.

---

**Data:** 2025-11-24 21:15
**Status:** 🔧 Problema Identificado - Porta em Uso
