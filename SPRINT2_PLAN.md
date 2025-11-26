# Sprint 2: Frontend Integration - Plano de Execução

## 🎯 Objetivo
Integrar completamente o frontend (servlink-web) com o backend (servlink-api), implementando todos os fluxos core de usuário.

## 📅 Duração: 2-3 semanas

---

## Semana 1: Setup e Autenticação

### Dia 1-2: Configuração Inicial

**Backend (servlink-api):**
- [x] Configurar CORS para permitir requisições do frontend
- [x] Verificar rotas da API
- [x] Testar endpoints com Postman

**Frontend (servlink-web):**
- [ ] Instalar dependências necessárias
  ```bash
  npm install axios
  npm install @tanstack/react-query  # Para gerenciamento de estado de API
  npm install zustand  # Para estado global leve
  ```
- [ ] Criar estrutura de pastas para API
  ```
  src/
    lib/
      api/
        axios.ts
        endpoints.ts
      hooks/
        useAuth.ts
        useJobs.ts
        useApplications.ts
    stores/
      authStore.ts
  ```

### Dia 3-4: Sistema de Autenticação

**Criar serviços de API:**
- [ ] `lib/api/axios.ts` - Configuração do Axios
- [ ] `lib/api/auth.ts` - Endpoints de autenticação
- [ ] `stores/authStore.ts` - Estado global de autenticação

**Implementar componentes:**
- [ ] Atualizar página de Login (`/login`)
- [ ] Atualizar página de Registro (`/register`)
- [ ] Criar componente de Protected Route
- [ ] Implementar persistência de token (localStorage)
- [ ] Implementar auto-logout em erro 401

**Testes:**
- [ ] Testar login com credenciais válidas
- [ ] Testar login com credenciais inválidas
- [ ] Testar registro de profissional
- [ ] Testar registro de estabelecimento
- [ ] Testar logout
- [ ] Testar redirecionamento de rotas protegidas

### Dia 5: Navegação e Layout

- [ ] Implementar navbar dinâmica (baseada em autenticação)
- [ ] Criar menu diferenciado por role (professional/establishment)
- [ ] Implementar loading states globais
- [ ] Criar componente de erro global

---

## Semana 2: Fluxos Core

### Dia 1-2: Sistema de Vagas (Jobs)

**Criar serviços:**
- [ ] `lib/api/jobs.ts` - CRUD de vagas
- [ ] `lib/hooks/useJobs.ts` - Hook para gerenciar vagas

**Páginas e Componentes:**
- [ ] Atualizar `/vagas` - Listagem com filtros
  - Filtro por role
  - Filtro por faixa de preço
  - Filtro por data
  - Filtro por rate_type
- [ ] Atualizar `/vagas/[id]` - Detalhes da vaga
- [ ] Criar `/dashboard/vagas/nova` - Criar vaga (establishment)
- [ ] Criar `/dashboard/vagas/[id]/editar` - Editar vaga (establishment)

**Funcionalidades:**
- [ ] Listar vagas públicas
- [ ] Ver detalhes da vaga
- [ ] Criar vaga (apenas establishment)
- [ ] Editar vaga (apenas dono)
- [ ] Cancelar vaga (apenas dono)
- [ ] Paginação
- [ ] Loading states
- [ ] Error handling

### Dia 3: Sistema de Candidaturas (Applications)

**Criar serviços:**
- [ ] `lib/api/applications.ts`
- [ ] `lib/hooks/useApplications.ts`

**Páginas e Componentes:**
- [ ] Criar `/dashboard/candidaturas` - Listar candidaturas
- [ ] Componente de card de candidatura
- [ ] Modal de confirmação de candidatura
- [ ] Modal de aceitar/rejeitar candidatura (establishment)

**Funcionalidades:**
- [ ] Candidatar-se a vaga (professional)
- [ ] Listar minhas candidaturas (professional)
- [ ] Listar candidaturas recebidas (establishment)
- [ ] Aceitar candidatura (establishment)
- [ ] Rejeitar candidatura (establishment)
- [ ] Retirar candidatura (professional)
- [ ] Validação de candidatura duplicada

### Dia 4: Sistema de Turnos (Shifts)

**Criar serviços:**
- [ ] `lib/api/shifts.ts`
- [ ] `lib/hooks/useShifts.ts`

**Páginas e Componentes:**
- [ ] Criar `/dashboard/turnos` - Listar turnos
- [ ] Componente de card de turno
- [ ] Modal de check-in (com QR Code scanner)
- [ ] Modal de check-out
- [ ] Componente de status do turno

**Funcionalidades:**
- [ ] Listar meus turnos
- [ ] Ver detalhes do turno
- [ ] Check-in com QR Code (professional)
- [ ] Check-out (professional)
- [ ] Confirmar horas (establishment)
- [ ] Cancelar turno (establishment)
- [ ] Exibir status do turno (scheduled, in_progress, completed, no_show, cancelled)

### Dia 5: Sistema de Pagamentos

**Criar serviços:**
- [ ] `lib/api/payments.ts`
- [ ] `lib/hooks/usePayments.ts`

**Páginas e Componentes:**
- [ ] Criar `/dashboard/pagamentos` - Listar pagamentos
- [ ] Componente de card de pagamento
- [ ] Componente de detalhes do pagamento

**Funcionalidades:**
- [ ] Listar meus pagamentos
- [ ] Ver detalhes do pagamento
- [ ] Exibir breakdown de valores (base, comissão, total)
- [ ] Processar pagamento manualmente (establishment)

---

## Semana 3: Perfis e Avaliações

### Dia 1-2: Sistema de Perfis

**Criar serviços:**
- [ ] `lib/api/profile.ts`
- [ ] `lib/hooks/useProfile.ts`

**Páginas e Componentes:**
- [ ] Criar `/perfil` - Ver meu perfil
- [ ] Criar `/perfil/editar` - Editar perfil
- [ ] Componente de upload de foto
- [ ] Componente de upload de documentos (professional)
- [ ] Validação de CPF (frontend)
- [ ] Validação de CNPJ (frontend)

**Funcionalidades:**
- [ ] Ver perfil completo
- [ ] Editar perfil profissional
- [ ] Editar perfil estabelecimento
- [ ] Upload de foto de perfil
- [ ] Upload de documentos (professional)
- [ ] Validação de CPF/CNPJ no frontend

### Dia 3-4: Sistema de Avaliações

**Criar serviços:**
- [ ] `lib/api/ratings.ts`
- [ ] `lib/hooks/useRatings.ts`

**Páginas e Componentes:**
- [ ] Criar `/dashboard/avaliacoes` - Minhas avaliações
- [ ] Criar `/perfil/[userId]` - Ver perfil público com avaliações
- [ ] Componente de formulário de avaliação
- [ ] Componente de exibição de avaliação
- [ ] Componente de estatísticas de avaliações

**Funcionalidades:**
- [ ] Avaliar estabelecimento (professional)
- [ ] Avaliar profissional (establishment)
- [ ] Ver minhas avaliações recebidas
- [ ] Ver avaliações públicas de um usuário
- [ ] Exibir estatísticas (média, distribuição)
- [ ] Validação: apenas após turno completo

### Dia 5: Polimento e Bug Fixes

- [ ] Revisar todos os fluxos
- [ ] Corrigir bugs encontrados
- [ ] Melhorar UX (loading, errors, feedback)
- [ ] Adicionar animações
- [ ] Otimizar performance
- [ ] Testar em diferentes resoluções
- [ ] Testar em diferentes navegadores

---

## 🛠️ Tecnologias e Bibliotecas

### Essenciais
```json
{
  "axios": "^1.6.0",
  "@tanstack/react-query": "^5.0.0",
  "zustand": "^4.4.0"
}
```

### Opcionais (mas recomendadas)
```json
{
  "react-hook-form": "^7.48.0",  // Formulários
  "zod": "^3.22.0",  // Validação
  "sonner": "^1.2.0",  // Toasts
  "react-qr-scanner": "^1.0.0",  // QR Code scanner
  "date-fns": "^2.30.0"  // Manipulação de datas
}
```

---

## 📁 Estrutura de Arquivos Sugerida

```
servlink-web/
  src/
    lib/
      api/
        axios.ts           # Configuração do Axios
        auth.ts            # Endpoints de autenticação
        jobs.ts            # Endpoints de vagas
        applications.ts    # Endpoints de candidaturas
        shifts.ts          # Endpoints de turnos
        payments.ts        # Endpoints de pagamentos
        ratings.ts         # Endpoints de avaliações
        profile.ts         # Endpoints de perfil
      hooks/
        useAuth.ts
        useJobs.ts
        useApplications.ts
        useShifts.ts
        usePayments.ts
        useRatings.ts
        useProfile.ts
      utils/
        validators.ts      # Validações (CPF, CNPJ, etc)
        formatters.ts      # Formatadores (moeda, data, etc)
    stores/
      authStore.ts         # Estado global de autenticação
    components/
      ui/                  # Componentes de UI reutilizáveis
      jobs/
        JobCard.tsx
        JobFilters.tsx
        JobForm.tsx
      applications/
        ApplicationCard.tsx
        ApplicationModal.tsx
      shifts/
        ShiftCard.tsx
        CheckInModal.tsx
        CheckOutModal.tsx
      payments/
        PaymentCard.tsx
        PaymentDetails.tsx
      ratings/
        RatingForm.tsx
        RatingCard.tsx
        RatingStats.tsx
      profile/
        ProfileForm.tsx
        PhotoUpload.tsx
        DocumentUpload.tsx
```

---

## 🧪 Checklist de Testes

### Autenticação
- [ ] Login com credenciais válidas
- [ ] Login com credenciais inválidas
- [ ] Registro de profissional
- [ ] Registro de estabelecimento
- [ ] Logout
- [ ] Persistência de sessão
- [ ] Redirecionamento de rotas protegidas

### Vagas
- [ ] Listar vagas
- [ ] Filtrar vagas
- [ ] Ver detalhes
- [ ] Criar vaga (establishment)
- [ ] Editar vaga (establishment)
- [ ] Cancelar vaga (establishment)
- [ ] Tentar criar vaga como professional (deve falhar)

### Candidaturas
- [ ] Candidatar-se a vaga
- [ ] Candidatura duplicada (deve falhar)
- [ ] Aceitar candidatura (establishment)
- [ ] Rejeitar candidatura (establishment)
- [ ] Retirar candidatura (professional)
- [ ] Ver shift criado após aceite

### Turnos
- [ ] Listar turnos
- [ ] Check-in com QR Code
- [ ] Check-out
- [ ] Confirmar horas (establishment)
- [ ] Cancelar turno (establishment)
- [ ] Ver payment criado após check-out

### Pagamentos
- [ ] Listar pagamentos
- [ ] Ver detalhes
- [ ] Verificar cálculos corretos

### Avaliações
- [ ] Avaliar após turno completo
- [ ] Avaliação duplicada (deve falhar)
- [ ] Ver estatísticas
- [ ] Ver avaliações públicas

### Perfis
- [ ] Ver perfil
- [ ] Editar perfil
- [ ] Upload de foto
- [ ] Upload de documento
- [ ] Validação de CPF/CNPJ

---

## 📊 Métricas de Sucesso

- [ ] 100% dos fluxos core funcionais
- [ ] Todos os endpoints integrados
- [ ] Loading states em todas as requisições
- [ ] Error handling em todas as requisições
- [ ] Feedback visual para todas as ações
- [ ] Responsivo em mobile, tablet e desktop
- [ ] 0 bugs críticos
- [ ] Performance: < 3s para carregar páginas

---

## 🚀 Próximos Passos Após Sprint 2

1. Implementar notificações (Sprint 3)
2. Implementar chat em tempo real (Sprint 3)
3. Criar dashboards com analytics (Sprint 3)
4. Testes automatizados (Sprint 4)
5. Deploy em produção (Sprint 4)

---

**Data de Início:** 2025-11-24
**Data Prevista de Conclusão:** 2025-12-15
**Status:** 🟢 Pronto para Iniciar
