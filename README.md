# ServLink API 🏨🍽️

> Conectando a economia gig da hotelaria e gastronomia em Florianópolis.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)
![Docker](https://img.shields.io/badge/Docker-Sail-2496ED?style=for-the-badge&logo=docker)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

O **ServLink** é uma plataforma projetada para resolver o "hiato de qualidade e quantidade" no mercado de trabalho temporário de Florianópolis. Conectamos estabelecimentos (hotéis, restaurantes, eventos) a profissionais qualificados (garçons, cozinheiros, bartenders) de forma rápida, segura e verificada.

Este repositório contém a **API Backend** da aplicação.

---

## 🚀 Funcionalidades Principais

O backend fornece uma API RESTful completa para suportar as operações da plataforma:

-   **🔐 Autenticação & Perfis:**
    -   Login/Registro com validação de CPF/CNPJ.
    -   Perfis distintos para Profissionais e Estabelecimentos.
    -   Upload de documentos e fotos.

-   **📢 Gestão de Vagas (Jobs):**
    -   Criação, edição e cancelamento de vagas.
    -   Definição de requisitos, horários e remuneração.

-   **🤝 Candidaturas (Applications):**
    -   Fluxo completo: Candidatura -> Aprovação/Rejeição -> Contratação.
    -   Validação de conflitos de horário.

-   **⏱️ Turnos (Shifts):**
    -   Geração automática de turnos após contratação.
    -   **Check-in/Check-out** (simulação de QR Code).
    -   Monitoramento de status em tempo real.

-   **💰 Financeiro (Payments):**
    -   Cálculo automático de valores e comissões.
    -   Histórico financeiro detalhado.

-   **⭐ Reputação (Ratings):**
    -   Sistema de avaliação mútua (dupla via).
    -   Cálculo de média de reputação.

-   **🔔 Notificações:**
    -   Alertas sobre status de vagas, pagamentos e turnos.

---

## 🛠️ Tecnologias Utilizadas

-   **Framework:** [Laravel 11](https://laravel.com)
-   **Banco de Dados:** MySQL 8.0
-   **Autenticação:** Laravel Sanctum
-   **Ambiente de Dev:** Laravel Sail (Docker)
-   **Testes:** PHPUnit (Configurado)

---

## ⚡ Como Rodar o Projeto

### Pré-requisitos
-   [Docker Desktop](https://www.docker.com/products/docker-desktop) instalado e rodando.
-   [WSL2](https://docs.microsoft.com/pt-br/windows/wsl/install) (se estiver no Windows).

### Passo a Passo

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/mscarpenter/servlink-api.git
    cd servlink-api
    ```

2.  **Configure o ambiente:**
    ```bash
    cp .env.example .env
    ```
    *Ajuste as variáveis de banco de dados no `.env` se necessário, mas o padrão do Sail já funciona.*

3.  **Instale as dependências (via Container):**
    ```bash
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        composer install --ignore-platform-reqs
    ```

4.  **Suba os containers:**
    ```bash
    ./vendor/bin/sail up -d
    ```

5.  **Gere a chave da aplicação e rode as migrations:**
    ```bash
    ./vendor/bin/sail artisan key:generate
    ./vendor/bin/sail artisan migrate
    ```

6.  **Acesse a API:**
    -   A API estará disponível em: `http://localhost/api`
    -   Health check: `http://localhost/api/jobs` (deve retornar lista vazia ou vagas)

---

## 📚 Documentação da API

A API segue os padrões REST. Abaixo, alguns dos principais endpoints:

| Método | Endpoint | Descrição | Auth? |
| :--- | :--- | :--- | :---: |
| `POST` | `/api/register` | Registrar novo usuário | ❌ |
| `POST` | `/api/login` | Autenticar usuário | ❌ |
| `GET` | `/api/jobs` | Listar vagas disponíveis | ❌ |
| `POST` | `/api/jobs` | Criar nova vaga | ✅ (Estab.) |
| `POST` | `/api/applications` | Candidatar-se a uma vaga | ✅ (Prof.) |
| `POST` | `/api/shifts` | Realizar Check-in | ✅ |
| `GET` | `/api/notifications` | Listar notificações | ✅ |

---

## 🤝 Como Contribuir

Contribuições são bem-vindas! Por favor, leia o arquivo [CONTRIBUTING.md](CONTRIBUTING.md) para detalhes sobre nosso código de conduta e o processo de envio de pull requests.

---

## 📄 Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para mais detalhes.
