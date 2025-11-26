# 📋 ServLink - Roadmap de Melhorias e Próximas Fases

## 🎯 Visão Geral

Com o **Sprint 1 100% completo**, este documento define a estratégia para as próximas fases do projeto ServLink, priorizando funcionalidades, definindo roadmap de implementação e estratégia de testes.

---

## 📊 Estado Atual (Sprint 1 - Completo)

### ✅ Backend Core (100%)
- Jobs, Applications, Shifts, Payments, Ratings
- Auth completo com validação CPF/CNPJ
- Upload de arquivos
- Role-based authorization
- 35+ endpoints funcionais

### 🔄 Frontend (Parcial)
- Estrutura básica Next.js
- Páginas criadas mas sem integração com API
- UI/UX definida (Material-UI)

### ❌ Funcionalidades Avançadas (Não Implementadas)
- Notificações
- Sistema de mensagens
- Dashboards e analytics
- Testes automatizados
- Deploy em produção

---

## 🎯 Priorização de Funcionalidades

### Prioridade ALTA (Sprint 2)
**Impacto:** Crítico para MVP funcional
**Esforço:** Médio
**Prazo:** 2-3 semanas

1. **Integração Frontend-Backend**
   - Conectar servlink-web com API
   - Implementar fluxos completos de usuário
   - Gerenciamento de estado (Context API/Redux)
   - Autenticação persistente

2. **Sistema de Notificações Básico**
   - Notificações in-app
   - Emails transacionais (candidatura aceita, turno criado, etc)
   - Biblioteca: Laravel Notifications + Mailtrap/SendGrid

3. **Seeders e Dados de Teste**
   - Popular banco com dados realistas
   - Facilitar testes e demonstrações

### Prioridade MÉDIA (Sprint 3)
**Impacto:** Importante para experiência completa
**Esforço:** Alto
**Prazo:** 3-4 semanas

1. **Sistema de Mensagens/Chat**
   - Chat em tempo real (Laravel Reverb/Pusher)
   - Histórico de conversas
   - Notificações de mensagens

2. **Dashboards e Analytics**
   - Dashboard profissional (ganhos, turnos, avaliações)
   - Dashboard estabelecimento (vagas, candidatos, custos)
   - Gráficos e estatísticas

3. **Sistema de Notificações Avançado**
   - Push notifications (PWA)
   - Preferências de notificação
   - Centro de notificações

4. **Melhorias de UX**
   - Loading states
   - Error handling
   - Feedback visual
   - Animações

### Prioridade BAIXA (Sprint 4+)
**Impacto:** Nice to have
**Esforço:** Variável
**Prazo:** 2-3 semanas

1. **Funcionalidades Extras**
   - Favoritar vagas
   - Histórico de trabalhos
   - Certificações e badges
   - Sistema de referências

2. **Integrações Externas**
   - Gateway de pagamento real (Stripe/PagSeguro)
   - Validação CNPJ via API externa
   - Geolocalização
   - Calendário (Google Calendar)

3. **Otimizações**
   - Cache (Redis)
   - Queue jobs (Laravel Queue)
   - CDN para arquivos
   - Otimização de imagens

---

## 🗓️ Roadmap de Implementação

### Sprint 2: Frontend Integration (2-3 semanas)

**Semana 1: Setup e Autenticação**
- [ ] Configurar Axios/Fetch para API
- [ ] Implementar Context API para auth
- [ ] Criar componente de login funcional
- [ ] Criar componente de registro funcional
- [ ] Persistir token em localStorage
- [ ] Implementar logout
- [ ] Protected routes

**Semana 2: Fluxos Core**
- [ ] Listar vagas (com filtros)
- [ ] Detalhes da vaga
- [ ] Criar vaga (estabelecimento)
- [ ] Candidatar-se (profissional)
- [ ] Gerenciar candidaturas (estabelecimento)
- [ ] Ver meus turnos
- [ ] Ver meus pagamentos

**Semana 3: Perfis e Avaliações**
- [ ] Ver perfil
- [ ] Editar perfil
- [ ] Upload de foto
- [ ] Sistema de avaliações
- [ ] Ver avaliações de usuários
- [ ] Polimento e bug fixes

**Entregáveis:**
- Frontend 100% integrado com backend
- Todos os fluxos funcionais
- Seeders com dados de teste

---

### Sprint 3: Funcionalidades Avançadas (3-4 semanas)

**Semana 1: Notificações**
- [ ] Setup Laravel Notifications
- [ ] Notificações in-app (banco de dados)
- [ ] Emails transacionais (Mailtrap/SendGrid)
- [ ] Frontend: centro de notificações
- [ ] Frontend: badges de notificações não lidas

**Semana 2: Sistema de Mensagens**
- [ ] Setup Laravel Reverb/Pusher
- [ ] Backend: endpoints de mensagens
- [ ] Backend: broadcast events
- [ ] Frontend: componente de chat
- [ ] Frontend: lista de conversas
- [ ] Frontend: notificações em tempo real

**Semana 3-4: Dashboards**
- [ ] Backend: endpoints de analytics
- [ ] Frontend: dashboard profissional
  - Ganhos totais
  - Turnos concluídos
  - Avaliação média
  - Gráfico de ganhos
- [ ] Frontend: dashboard estabelecimento
  - Vagas ativas
  - Candidatos
  - Custos totais
  - Gráfico de contratações
- [ ] Componentes de gráficos (Chart.js/Recharts)

**Entregáveis:**
- Sistema de notificações completo
- Chat em tempo real
- Dashboards funcionais

---

### Sprint 4: Qualidade e Deploy (2-3 semanas)

**Semana 1: Testes**
- [ ] Testes unitários backend (PHPUnit)
- [ ] Testes de integração backend
- [ ] Testes E2E frontend (Cypress/Playwright)
- [ ] Coverage mínimo de 70%

**Semana 2: CI/CD e Documentação**
- [ ] GitHub Actions para CI/CD
- [ ] Documentação Swagger/OpenAPI
- [ ] README completo
- [ ] Guia de deployment

**Semana 3: Deploy**
- [ ] Setup servidor (DigitalOcean/AWS)
- [ ] Deploy backend (Laravel Forge/Vapor)
- [ ] Deploy frontend (Vercel/Netlify)
- [ ] Configurar domínio e SSL
- [ ] Monitoramento (Sentry/New Relic)

**Entregáveis:**
- Aplicação em produção
- Testes automatizados
- CI/CD configurado
- Documentação completa

---

## 🧪 Estratégia de Testes

### 1. Testes Backend (Laravel)

#### Testes Unitários (PHPUnit)
**Objetivo:** Testar lógica isolada

```php
// Exemplo: tests/Unit/PaymentTest.php
public function test_calculate_payment_values()
{
    $values = Payment::calculatePaymentValues(100, 0.18);
    
    $this->assertEquals(100, $values['base_amount']);
    $this->assertEquals(18, $values['commission_amount']);
    $this->assertEquals(100, $values['professional_pay']);
    $this->assertEquals(118, $values['total_charge_establishment']);
}

public function test_validate_cpf()
{
    $controller = new ProfileController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('validateCPF');
    $method->setAccessible(true);
    
    $this->assertTrue($method->invoke($controller, '12345678909'));
    $this->assertFalse($method->invoke($controller, '11111111111'));
}
```

**Cobertura:**
- [ ] Validação de CPF/CNPJ
- [ ] Cálculos de pagamento
- [ ] Cálculo de horas trabalhadas
- [ ] Atualização de ratings
- [ ] Geração de QR Code

#### Testes de Feature (PHPUnit)
**Objetivo:** Testar endpoints completos

```php
// Exemplo: tests/Feature/JobTest.php
public function test_establishment_can_create_job()
{
    $establishment = User::factory()->create(['role' => 'establishment']);
    
    $response = $this->actingAs($establishment)
        ->postJson('/api/jobs', [
            'title' => 'Garçom',
            'rate' => 80,
            'rate_type' => 'Fixed',
            'start_time' => now()->addDays(1),
            'end_time' => now()->addDays(1)->addHours(4),
        ]);
    
    $response->assertStatus(201)
        ->assertJsonStructure(['message', 'job']);
}

public function test_professional_cannot_create_job()
{
    $professional = User::factory()->create(['role' => 'professional']);
    
    $response = $this->actingAs($professional)
        ->postJson('/api/jobs', [...]);
    
    $response->assertStatus(403);
}
```

**Cobertura:**
- [ ] Todos os endpoints de Jobs
- [ ] Todos os endpoints de Applications
- [ ] Todos os endpoints de Shifts
- [ ] Todos os endpoints de Payments
- [ ] Todos os endpoints de Ratings
- [ ] Todos os endpoints de Profile
- [ ] Validações de autorização
- [ ] Validações de negócio

#### Testes de Integração
**Objetivo:** Testar fluxos completos

```php
// Exemplo: tests/Integration/JobApplicationFlowTest.php
public function test_complete_job_application_flow()
{
    // 1. Estabelecimento cria vaga
    $establishment = User::factory()->create(['role' => 'establishment']);
    $job = Job::factory()->create(['establishment_id' => $establishment->id]);
    
    // 2. Profissional se candidata
    $professional = User::factory()->create(['role' => 'professional']);
    $application = Application::factory()->create([
        'job_id' => $job->id,
        'user_id' => $professional->id,
    ]);
    
    // 3. Estabelecimento aceita
    $this->actingAs($establishment)
        ->putJson("/api/applications/{$application->id}", ['status' => 'accepted']);
    
    // 4. Verificar shift criado
    $this->assertDatabaseHas('shifts', [
        'application_id' => $application->id,
        'status' => 'scheduled',
    ]);
    
    // 5. Verificar vaga preenchida
    $this->assertDatabaseHas('jobs', [
        'id' => $job->id,
        'status' => 'Filled',
    ]);
}
```

**Cobertura:**
- [ ] Fluxo completo de vaga (criação → candidatura → aceite → shift)
- [ ] Fluxo de check-in/check-out
- [ ] Fluxo de pagamento
- [ ] Fluxo de avaliação

---

### 2. Testes Frontend (React/Next.js)

#### Testes de Componente (Jest + React Testing Library)
**Objetivo:** Testar componentes isolados

```javascript
// Exemplo: __tests__/components/JobCard.test.js
import { render, screen } from '@testing-library/react';
import JobCard from '@/components/JobCard';

test('renders job information correctly', () => {
  const job = {
    title: 'Garçom',
    rate: 80,
    rate_type: 'Fixed',
    start_time: '2025-11-30 19:00:00',
  };
  
  render(<JobCard job={job} />);
  
  expect(screen.getByText('Garçom')).toBeInTheDocument();
  expect(screen.getByText('R$ 80,00')).toBeInTheDocument();
});
```

**Cobertura:**
- [ ] Componentes de UI (cards, forms, buttons)
- [ ] Componentes de layout (navbar, footer)
- [ ] Componentes de feedback (loading, error)

#### Testes de Integração (Jest)
**Objetivo:** Testar hooks e context

```javascript
// Exemplo: __tests__/hooks/useAuth.test.js
import { renderHook, act } from '@testing-library/react';
import { useAuth } from '@/hooks/useAuth';

test('login sets user and token', async () => {
  const { result } = renderHook(() => useAuth());
  
  await act(async () => {
    await result.current.login('email@test.com', 'password');
  });
  
  expect(result.current.user).toBeDefined();
  expect(result.current.token).toBeDefined();
});
```

**Cobertura:**
- [ ] useAuth hook
- [ ] AuthContext
- [ ] API integration hooks

#### Testes E2E (Cypress/Playwright)
**Objetivo:** Testar fluxos completos de usuário

```javascript
// Exemplo: cypress/e2e/job-application.cy.js
describe('Job Application Flow', () => {
  it('professional can apply to a job', () => {
    // Login como profissional
    cy.visit('/login');
    cy.get('[data-testid="email"]').type('professional@test.com');
    cy.get('[data-testid="password"]').type('password');
    cy.get('[data-testid="login-button"]').click();
    
    // Navegar para vagas
    cy.visit('/vagas');
    
    // Clicar na primeira vaga
    cy.get('[data-testid="job-card"]').first().click();
    
    // Candidatar-se
    cy.get('[data-testid="apply-button"]').click();
    
    // Verificar sucesso
    cy.contains('Candidatura enviada com sucesso!').should('be.visible');
  });
});
```

**Cobertura:**
- [ ] Fluxo de registro
- [ ] Fluxo de login
- [ ] Fluxo de criação de vaga
- [ ] Fluxo de candidatura
- [ ] Fluxo de avaliação
- [ ] Fluxo de perfil

---

### 3. Estratégia de Testes por Sprint

#### Sprint 2 (Frontend Integration)
**Foco:** Testes de integração frontend-backend
- [ ] Testes E2E dos fluxos principais
- [ ] Testes de componentes críticos
- [ ] Testes de hooks de API

**Meta de Coverage:** 50%

#### Sprint 3 (Funcionalidades Avançadas)
**Foco:** Testes de features novas
- [ ] Testes de notificações
- [ ] Testes de chat
- [ ] Testes de dashboards

**Meta de Coverage:** 60%

#### Sprint 4 (Qualidade)
**Foco:** Completar cobertura
- [ ] Testes unitários backend (70%+)
- [ ] Testes de feature backend (80%+)
- [ ] Testes E2E completos (100% dos fluxos)

**Meta de Coverage:** 70%+ geral

---

### 4. CI/CD Pipeline

```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  backend-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test --coverage
      
  frontend-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup Node
        uses: actions/setup-node@v2
        with:
          node-version: '18'
      - name: Install Dependencies
        run: npm install
      - name: Run Tests
        run: npm test
      
  e2e-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Run Cypress
        uses: cypress-io/github-action@v5
        with:
          start: npm run dev
          wait-on: 'http://localhost:3000'
```

---

## 📈 Métricas de Sucesso

### Sprint 2
- [ ] 100% dos fluxos core funcionais
- [ ] 50%+ de cobertura de testes
- [ ] 0 bugs críticos

### Sprint 3
- [ ] Notificações funcionando
- [ ] Chat em tempo real
- [ ] Dashboards com dados reais
- [ ] 60%+ de cobertura de testes

### Sprint 4
- [ ] Aplicação em produção
- [ ] 70%+ de cobertura de testes
- [ ] CI/CD funcionando
- [ ] Documentação completa
- [ ] Performance otimizada (< 3s load time)

---

## 🎯 Próximos Passos Imediatos

1. **Revisar e aprovar este roadmap**
2. **Iniciar Sprint 2:**
   - Setup do ambiente frontend
   - Configurar integração com API
   - Implementar autenticação
3. **Criar branch de desenvolvimento**
4. **Configurar ambiente de staging**

---

## 📝 Notas Finais

Este roadmap é flexível e pode ser ajustado conforme necessário. As prioridades podem mudar baseado em:
- Feedback de usuários
- Necessidades de negócio
- Descobertas técnicas
- Recursos disponíveis

**Última atualização:** 2025-11-24
**Versão:** 1.0
**Status:** Aguardando aprovação
