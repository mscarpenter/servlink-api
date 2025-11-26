# 🧪 Guia de Testes - ServLink API

## ✅ Pré-requisitos

1. **Docker Desktop** rodando
2. **Containers ativos**:
   ```bash
   cd ~/projetos/servlink-api
   ./vendor/bin/sail up -d
   ```
3. **Migrations executadas** ✅ (já feito!)
4. **Postman** ou **Insomnia** instalado

## 📍 Base URL
```
http://localhost/api
```

---

## 🔐 1. AUTENTICAÇÃO

### 1.1 Registrar Estabelecimento
```http
POST http://localhost/api/register
Content-Type: application/json

{
  "name": "Mariana Silva",
  "email": "mariana@restaurante.com",
  "password": "senha123",
  "password_confirmation": "senha123",
  "role": "establishment",
  "company_name": "Restaurante da Lagoa"
}
```

**Resposta esperada (201):**
```json
{
  "access_token": "1|xxxxx...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Mariana Silva",
    "email": "mariana@restaurante.com",
    "role": "establishment"
  }
}
```

💾 **IMPORTANTE:** Salve o `access_token` como `TOKEN_ESTABLISHMENT`

---

### 1.2 Registrar Profissional
```http
POST http://localhost/api/register
Content-Type: application/json

{
  "name": "Lucas Santos",
  "email": "lucas@email.com",
  "password": "senha123",
  "password_confirmation": "senha123",
  "role": "professional",
  "full_name": "Lucas Santos Bartender"
}
```

💾 **IMPORTANTE:** Salve o `access_token` como `TOKEN_PROFESSIONAL`

---

### 1.3 Login
```http
POST http://localhost/api/login
Content-Type: application/json

{
  "email": "mariana@restaurante.com",
  "password": "senha123"
}
```

---

### 1.4 Logout
```http
POST http://localhost/api/logout
Authorization: Bearer {TOKEN_ESTABLISHMENT}
```

---

## 💼 2. JOBS (VAGAS)

### 2.1 Listar Vagas (Público)
```http
GET http://localhost/api/jobs
```

**Com filtros:**
```http
GET http://localhost/api/jobs?role=Garçom&status=Open&min_rate=50&max_rate=150
```

---

### 2.2 Criar Vaga (Estabelecimento)
```http
POST http://localhost/api/jobs
Authorization: Bearer {TOKEN_ESTABLISHMENT}
Content-Type: application/json

{
  "title": "Garçom para Fim de Semana",
  "description": "Procuramos garçom experiente para trabalhar no sábado à noite",
  "role": "Garçom",
  "rate": 80.00,
  "rate_type": "Fixed",
  "start_time": "2025-11-30 19:00:00",
  "end_time": "2025-11-30 23:00:00"
}
```

💾 **IMPORTANTE:** Salve o `id` da vaga como `JOB_ID`

**Teste com rate_type Hourly:**
```json
{
  "title": "Bartender Noturno",
  "description": "Bartender para evento corporativo",
  "role": "Bartender",
  "rate": 45.00,
  "rate_type": "Hourly",
  "start_time": "2025-12-01 20:00:00",
  "end_time": "2025-12-02 02:00:00"
}
```

---

### 2.3 Ver Detalhes da Vaga (Público)
```http
GET http://localhost/api/jobs/{JOB_ID}
```

---

### 2.4 Editar Vaga (Apenas Dono)
```http
PUT http://localhost/api/jobs/{JOB_ID}
Authorization: Bearer {TOKEN_ESTABLISHMENT}
Content-Type: application/json

{
  "rate": 90.00,
  "description": "Atualização: Oferecemos vale-transporte"
}
```

---

### 2.5 Cancelar Vaga (Apenas Dono)
```http
DELETE http://localhost/api/jobs/{JOB_ID}
Authorization: Bearer {TOKEN_ESTABLISHMENT}
```

---

## 📝 3. APPLICATIONS (CANDIDATURAS)

### 3.1 Candidatar-se (Profissional)
```http
POST http://localhost/api/applications
Authorization: Bearer {TOKEN_PROFESSIONAL}
Content-Type: application/json

{
  "job_id": {JOB_ID}
}
```

💾 **IMPORTANTE:** Salve o `id` da candidatura como `APPLICATION_ID`

**Teste de validação - Candidatura duplicada:**
```http
POST http://localhost/api/applications
Authorization: Bearer {TOKEN_PROFESSIONAL}
Content-Type: application/json

{
  "job_id": {JOB_ID}
}
```
❌ **Deve retornar 409 Conflict**

---

### 3.2 Listar Candidaturas
```http
GET http://localhost/api/applications
Authorization: Bearer {TOKEN_PROFESSIONAL}
```

---

### 3.3 Ver Detalhes da Candidatura
```http
GET http://localhost/api/applications/{APPLICATION_ID}
Authorization: Bearer {TOKEN_PROFESSIONAL}
```

---

### 3.4 Aceitar Candidatura (Estabelecimento)
```http
PUT http://localhost/api/applications/{APPLICATION_ID}
Authorization: Bearer {TOKEN_ESTABLISHMENT}
Content-Type: application/json

{
  "status": "accepted"
}
```

**O que acontece automaticamente:**
- ✅ Cria um Shift com QR Code único
- ✅ Muda status da vaga para "Filled"
- ✅ Rejeita todas as outras candidaturas pendentes

💾 **IMPORTANTE:** Na resposta, salve `shift.id` como `SHIFT_ID` e `shift.qr_code` como `QR_CODE`

---

### 3.5 Retirar Candidatura (Profissional)
```http
DELETE http://localhost/api/applications/{APPLICATION_ID}
Authorization: Bearer {TOKEN_PROFESSIONAL}
```

---

## ⏰ 4. SHIFTS (TURNOS)

### 4.1 Listar Turnos
```http
GET http://localhost/api/shifts
Authorization: Bearer {TOKEN_PROFESSIONAL}
```

---

### 4.2 Ver Detalhes do Turno
```http
GET http://localhost/api/shifts/{SHIFT_ID}
Authorization: Bearer {TOKEN_PROFESSIONAL}
```

---

### 4.3 Check-in (Profissional)
```http
POST http://localhost/api/shifts
Authorization: Bearer {TOKEN_PROFESSIONAL}
Content-Type: application/json

{
  "qr_code": "{QR_CODE}"
}
```

**Validações testadas:**
- ✅ Apenas o profissional atribuído pode fazer check-in
- ✅ Check-in só é permitido 30min antes até 15min depois do horário
- ✅ Check-in muito tarde = no-show automático

---

### 4.4 Check-out (Profissional)
```http
PUT http://localhost/api/shifts/{SHIFT_ID}
Authorization: Bearer {TOKEN_PROFESSIONAL}
Content-Type: application/json

{
  "qr_code": "{QR_CODE}"
}
```

**O que acontece automaticamente:**
- ✅ Calcula `confirmed_hours` automaticamente
- ✅ Cria Payment automaticamente
- ✅ Status muda para "completed"

💾 **IMPORTANTE:** Na resposta, verifique `confirmed_hours` e `payment`

---

### 4.5 Confirmar Horas (Estabelecimento)
```http
PUT http://localhost/api/shifts/{SHIFT_ID}
Authorization: Bearer {TOKEN_ESTABLISHMENT}
Content-Type: application/json

{
  "confirmed_hours": 4.5
}
```

---

### 4.6 Cancelar Turno (Estabelecimento)
```http
DELETE http://localhost/api/shifts/{SHIFT_ID}
Authorization: Bearer {TOKEN_ESTABLISHMENT}
```

**O que acontece automaticamente:**
- ✅ Vaga volta para status "Open"
- ✅ Candidatura volta para "pending"

---

## 💰 5. PAYMENTS (PAGAMENTOS)

### 5.1 Listar Pagamentos
```http
GET http://localhost/api/payments
Authorization: Bearer {TOKEN_PROFESSIONAL}
```

---

### 5.2 Ver Detalhes do Pagamento
```http
GET http://localhost/api/payments/{PAYMENT_ID}
Authorization: Bearer {TOKEN_PROFESSIONAL}
```

**Verifique os campos:**
- `base_amount`: valor base
- `commission_rate`: 0.18 (18%)
- `commission_amount`: comissão calculada
- `professional_pay`: = base_amount
- `total_charge_establishment`: base + comissão
- `transaction_id`: TXN-XXXXXXXXXXXXXXXX
- `status`: "processed"

---

### 5.3 Processar Pagamento Manualmente (Estabelecimento)
```http
POST http://localhost/api/payments
Authorization: Bearer {TOKEN_ESTABLISHMENT}
Content-Type: application/json

{
  "shift_id": {SHIFT_ID}
}
```

**Teste de validação - Pagamento duplicado:**
❌ **Deve retornar 409 Conflict** se já existe pagamento para o turno

---

## ⭐ 6. RATINGS (AVALIAÇÕES)

### 6.1 Profissional Avalia Estabelecimento
```http
POST http://localhost/api/ratings
Authorization: Bearer {TOKEN_PROFESSIONAL}
Content-Type: application/json

{
  "shift_id": {SHIFT_ID},
  "score": 5,
  "comments": "Excelente ambiente de trabalho! Recomendo."
}
```

**O que acontece automaticamente:**
- ✅ Sistema identifica que profissional está avaliando estabelecimento
- ✅ Atualiza `average_rating` do estabelecimento

---

### 6.2 Estabelecimento Avalia Profissional
```http
POST http://localhost/api/ratings
Authorization: Bearer {TOKEN_ESTABLISHMENT}
Content-Type: application/json

{
  "shift_id": {SHIFT_ID},
  "score": 4,
  "comments": "Profissional pontual e competente."
}
```

**O que acontece automaticamente:**
- ✅ Sistema identifica que estabelecimento está avaliando profissional
- ✅ Atualiza `overall_rating` do profissional

---

### 6.3 Listar Avaliações Recebidas
```http
GET http://localhost/api/ratings
Authorization: Bearer {TOKEN_PROFESSIONAL}
```

---

### 6.4 Ver Avaliações de um Usuário (Público)
```http
GET http://localhost/api/users/{USER_ID}/ratings
```

**Resposta inclui estatísticas:**
```json
{
  "ratings": [...],
  "stats": {
    "total_ratings": 10,
    "average_rating": 4.5,
    "rating_distribution": {
      "5_stars": 6,
      "4_stars": 3,
      "3_stars": 1,
      "2_stars": 0,
      "1_star": 0
    }
  }
}
```

---

## 🧪 CENÁRIOS DE TESTE COMPLETOS

### Cenário 1: Fluxo Completo de Vaga (Happy Path)

1. ✅ Estabelecimento cria vaga
2. ✅ Profissional se candidata
3. ✅ Estabelecimento aceita candidatura (shift criado automaticamente)
4. ✅ Profissional faz check-in com QR Code
5. ✅ Profissional faz check-out (payment criado automaticamente)
6. ✅ Profissional avalia estabelecimento
7. ✅ Estabelecimento avalia profissional

### Cenário 2: Validações de Negócio

1. ❌ Profissional tenta se candidatar duas vezes → 409 Conflict
2. ❌ Profissional tenta fazer check-in muito cedo → 400 Bad Request
3. ❌ Profissional tenta fazer check-in muito tarde → no-show automático
4. ❌ Tentar avaliar turno não concluído → 400 Bad Request
5. ❌ Tentar avaliar duas vezes → 409 Conflict

### Cenário 3: Autorização

1. ❌ Profissional tenta criar vaga → 403 Forbidden
2. ❌ Estabelecimento tenta se candidatar → 403 Forbidden
3. ❌ Usuário tenta editar vaga de outro → 403 Forbidden
4. ❌ Profissional tenta aceitar candidatura → 403 Forbidden

---

## 📊 CHECKLIST DE TESTES

### Jobs
- [ ] Criar vaga (Fixed e Hourly)
- [ ] Listar vagas com filtros
- [ ] Ver detalhes
- [ ] Editar vaga
- [ ] Cancelar vaga
- [ ] Tentar editar vaga de outro usuário (403)

### Applications
- [ ] Candidatar-se
- [ ] Candidatura duplicada (409)
- [ ] Aceitar candidatura (shift criado)
- [ ] Rejeitar candidatura
- [ ] Retirar candidatura

### Shifts
- [ ] Check-in com QR Code
- [ ] Check-in muito cedo (400)
- [ ] Check-in muito tarde (no-show)
- [ ] Check-out (payment criado)
- [ ] Confirmar horas manualmente
- [ ] Cancelar turno (vaga reabre)

### Payments
- [ ] Listar pagamentos
- [ ] Ver detalhes
- [ ] Verificar cálculo de comissão (18%)
- [ ] Verificar rate_type (Hourly vs Fixed)
- [ ] Pagamento duplicado (409)

### Ratings
- [ ] Profissional avalia estabelecimento
- [ ] Estabelecimento avalia profissional
- [ ] Avaliação duplicada (409)
- [ ] Avaliar turno não concluído (400)
- [ ] Ver estatísticas públicas
- [ ] Verificar atualização de overall_rating

---

## 🎯 PRÓXIMOS PASSOS

Após testar o backend:

1. **Criar seeders** com dados de exemplo
2. **Implementar Auth extras** (CNPJ/CPF validation, file upload)
3. **Iniciar Sprint 2** - Integração com frontend

---

## 💡 DICAS

- Use **Postman Collections** para salvar todos os requests
- Configure **Environment Variables** para tokens e IDs
- Use **Tests** no Postman para validar respostas automaticamente
- Verifique sempre os **status codes** corretos
