# Guia de Referência: Setup de Ambiente Dev (WSL + Laravel Sail)

Este documento é o passo a passo de como configuramos seu ambiente de desenvolvimento do zero no Windows 11, otimizado para Laravel (PHP) e Análise de Dados (BI).

## Fase 1: Configuração do Sistema (Windows 11)

1.  **Instalar o Windows Terminal:** Baixado da Microsoft Store.
2.  **Instalar o WSL 2 (Subsistema do Windows para Linux):**
    * Comando (Terminal Admin): `wsl --install`
3.  **Instalar a Distribuição Linux:**
    * Comando (Terminal): `wsl --list --online` (Para ver as opções)
    * Comando (Terminal): `wsl --install Ubuntu` (Instala o Ubuntu)
    * *Ação: Criação do usuário e senha do Linux.*
4.  **Definir Terminal Padrão:**
    * *Ação: Abrir Configurações do Windows Terminal e definir o "Perfil Padrão" como "Ubuntu".*

## Fase 2: Ferramentas de Desenvolvimento

1.  **VS Code:** Instalado no Windows.
2.  **Extensões do VS Code (Instaladas no WSL):**
    * `PHP Intelephense` (Para inteligência de código PHP)
    * `Laravel Blade Snippets` (Para views)
    * `Docker` (Para gerenciar contêineres)
3.  **Git:**
    * Comando (Ubuntu): `sudo apt install git`
4.  **Docker Desktop:**
    * *Ação: Instalado no Windows (da Microsoft Store ou site oficial).*
    * *Ação: Login feito com a conta do GitHub.*
    * *Ação: Garantir que o Docker Desktop esteja LIGADO no Windows antes de usar o Sail.*

## Fase 3: Criação do Projeto (Laravel Sail)

1.  **Navegar para a pasta "home" e criar pasta de projetos:**
    * Comando (Ubuntu): `cd ~`
    * Comando (Ubuntu): `mkdir projects`
2.  **Instalar o `curl`:**
    * Comando (Ubuntu): `sudo apt install curl`
3.  **Criar o Projeto Laravel (usando Sail/Docker):**
    * Comando (Ubuntu): `cd ~/projects`
    * Comando (Ubuntu): `curl -s "https://laravel.build/servlink-api" | bash`
    * *Nota: O Sail baixa e constrói automaticamente os contêineres de PHP, MySQL, Redis, etc.*

## Fase 4: Fluxo de Trabalho Diário (Operação)

1.  **Ligar os Servidores (Início do dia):**
    * *Ação: Abrir o "Docker Desktop" no Windows.*
    * Comando (Ubuntu): `cd ~/servlink-api`
    * Comando (Ubuntu): `./vendor/bin/sail up -d`
2.  **Abrir o Projeto (VS Code):**
    * Comando (Ubuntu): `code .` (O ponto abre o diretório atual)
    * *Verificação: O VS Code deve mostrar "WSL: Ubuntu" no canto inferior esquerdo.*
3.  **Verificar o Site:**
    * *Ação: Abrir `http://localhost` no navegador.*
4.  **Parar os Servidores (Fim do dia):**
    * Comando (Ubuntu): `./vendor/bin/sail down`

## Fase 5: Conexão com Banco de Dados (SGBD)

1.  **Servidor:** O MySQL já está rodando via Docker (Serviço `mysql`).
2.  **Cliente Visual (SGBD):**
    * **Opção A (VS Code):** Instalar extensão `SQLTools` + `SQLTools MySQL/MariaDB`.
    * **Opção B (Windows):** Instalar o `MySQL Workbench` (sem o MySQL Server!).
3.  **Credenciais de Conexão (padrão do Sail):**
    * **Host:** `127.0.0.1` (ou `localhost`)
    * **Porta:** `3306`
    * **Usuário:** `sail`
    * **Senha:** `password`
    * **Banco de Dados:** `laravel`

4.  **Correção de Acesso (Se der "Access Denied"):**
    * *Necessário para permitir que ferramentas externas (SQLTools/Workbench) acessem o MySQL do Docker.*
    * Comando (Ubuntu): `./vendor/bin/sail exec mysql mysql -uroot -ppassword -e "CREATE USER IF NOT EXISTS 'sail'@'%' IDENTIFIED WITH mysql_native_password BY 'password';"`
    * Comando (Ubuntu): `./vendor/bin/sail exec mysql mysql -uroot -ppassword -e "GRANT ALL PRIVILEGES ON *.* TO 'sail'@'%';"`
    * Comando (Ubuntu): `./vendor/bin/sail exec mysql mysql -uroot -ppassword -e "FLUSH PRIVILEGES;"`

## Fase 6: Gerenciamento do Banco de Dados (Migrations)

* **Criar um novo arquivo de Migration (Planta da Tabela):**
    * Comando: `./vendor/bin/sail artisan make:migration NOME_DA_MIGRATION`
* **Aplicar Migrations (Construir as Tabelas):**
    * Comando: `./vendor/bin/sail artisan migrate`
```eof

---

### 🚀 Próxima Ação Imediata (Sprint 3)

O Laravel Sail já criou os Models para `User` e `Job`. Mas nós criamos as tabelas `profiles_professional` e `applications`, então precisamos criar os Models para elas.

Você está pronto para criar o Model **`ProfilesProfessional.php`** e definir o relacionamento "Um-para-Um" com o Model `User`?