# Tenant Manager

Sistema abrangente de gestão multi-tenancy desenvolvido com Laravel 12, Vue 3 e tecnologias web modernas. Esta aplicação fornece gestão completa do ciclo de vida de tenants, gestão de subscrições e monitorização de recursos com segurança e isolamento integrados.

## Visão Geral

O Tenant Manager é uma plataforma multi-tenancy pronta para SaaS que permite às organizações gerir múltiplos tenants isolados numa única instância de aplicação. Cada tenant opera de forma independente com os seus próprios utilizadores, permissões e planos de subscrição, mantendo isolamento completo de dados.

## Stack Tecnológica

**Backend**
- Laravel 12.x
- PHP 8.3+
- MySQL 8.0+

**Frontend**
- Vue 3 (Composition API)
- TailwindCSS 3.x
- Vite

**Autenticação e Autorização**
- Laravel Fortify
- Permissões personalizadas com âmbito de tenant

**Ferramentas Adicionais**
- Laravel Scheduler para tarefas automatizadas
- Laravel Notifications para comunicação por email
- Migrations de base de dados com controlo de versão

## Funcionalidades Principais

### Gestão de Tenants
Operações CRUD completas para entidades tenant incluindo criação, edição, ativação/desativação e eliminação lógica. Cada tenant possui um slug único para identificação e suporte opcional para domínio personalizado.

### Suporte Multi-Utilizador
Os tenants podem ter múltiplos utilizadores com diferentes funções (proprietário, administrador, membro) e permissões granulares. O sistema aplica controlo de acesso baseado em funções rigoroso dentro do âmbito de cada tenant.

### Sistema de Subscrições
Modelo de subscrição de três níveis com atribuição automática de plano, capacidades de upgrade/downgrade e gestão de período de trial. Inclui registo completo de auditoria de todas as alterações de subscrição.

**Planos Disponíveis:**
- Free: Funcionalidades básicas com limites de utilizadores e armazenamento
- Pro: Funcionalidades profissionais com limites aumentados e trial de 14 dias
- Enterprise: Recursos ilimitados com trial de 30 dias

### Monitorização de Recursos
Dashboard em tempo real exibindo utilização atual de recursos face aos limites do plano com indicadores visuais para consumo de quota. Alertas automáticos quando se aproximam dos limites de recursos.

### Onboarding Self-Service
Processo simplificado de criação de tenant permitindo aos utilizadores estabelecer novos tenants com atribuição automática de proprietário e subscrição do plano gratuito por defeito.

### Notificações de Trial
Notificações automáticas por email enviadas um dia antes da expiração do trial, agendadas diariamente através do task scheduler do Laravel com mecanismos de prevenção de duplicação.

### Registo de Auditoria
Histórico abrangente de alterações de subscrição rastreando quem fez alterações, quando e o que foi modificado. Inclui valores antigos e novos para transparência completa.

## Arquitetura

### Isolamento de Dados
O sistema implementa multi-tenancy ao nível de linha usando uma abordagem de base de dados partilhada com restrições de chave estrangeira rigorosas e contexto de tenant aplicado por middleware.

### Contexto de Tenant
O middleware vincula automaticamente o tenant ativo ao container da aplicação com base em dados de sessão, tornando-o disponível ao longo do ciclo de vida do pedido através de funções helper.

### Camada de Serviço
A lógica de negócio está encapsulada em classes de serviço dedicadas, separando responsabilidades dos controllers e promovendo reutilização e testabilidade do código.

## Estrutura do Projeto

### Models
- **Tenant**: Entidade principal de tenant com relações para utilizadores e subscrições
- **User**: Utilizadores da aplicação com relações many-to-many para tenants
- **Plan**: Definições de planos de subscrição com funcionalidades e limites
- **Subscription**: Subscrições ativas ligando tenants a planos
- **SubscriptionLog**: Trilha de auditoria de alterações de subscrição

### Controllers
- **TenantController**: Operações CRUD de tenants
- **TenantUserController**: Gestão de utilizadores dentro do âmbito do tenant
- **SubscriptionController**: Subscrição e alterações de planos
- **SubscriptionLogController**: Visualização de histórico de subscrições
- **DashboardController**: Monitorização de recursos e estatísticas

### Services
- **TenantService**: Lógica de criação e gestão de tenants
- **SubscriptionService**: Gestão do ciclo de vida de subscrições

### Middleware
- **SetTenantContext**: Estabelece âmbito de tenant para pedidos
- **CheckTenantAccess**: Valida acesso do utilizador aos recursos do tenant

### Policies
- **TenantPolicy**: Regras de autorização para operações de tenant
- **Aplica permissões de proprietário/administrador para ações sensíveis

## Conceitos-Chave

### Permissões com Âmbito de Tenant
As permissões são avaliadas dentro do contexto de um tenant específico. Um utilizador pode ter direitos de administrador num tenant enquanto é um membro básico noutro.

### Atribuição Automática de Plano
Novos tenants são automaticamente subscritos ao plano Free após criação, garantindo acesso imediato sem intervenção manual.

### Transições de Subscrição
Upgrades e downgrades são aplicados imediatamente para todas as alterações de plano. O sistema valida compatibilidade antes de permitir transições.

### Limites de Recursos
Os planos definem limites para vários recursos (utilizadores, armazenamento). O sistema aplica estes limites em tempo real, prevenindo ações que excedam as quotas.

## Fluxo de Subscrição

### Criação de Novo Tenant
1. Utilizador inicia criação de tenant através do fluxo de onboarding
2. Sistema valida input e gera slug único
3. Registo de tenant criado com utilizador como proprietário
4. Subscrição automática ao plano Free
5. Entrada inicial de log de subscrição criada
6. Utilizador redirecionado para dashboard do tenant

### Alterações de Plano
1. Utilizador seleciona novo plano das opções disponíveis
2. Sistema valida permissões do utilizador (apenas proprietário/administrador)
3. Verificação de compatibilidade face à utilização atual de recursos
4. Subscrição atual marcada como cancelada
5. Nova subscrição criada com datas apropriadas
6. Entrada de log de auditoria registada com detalhes da alteração
7. Notificação ao utilizador de alteração bem-sucedida

### Expiração de Trial
1. Tarefa agendada diária verifica trials a expirar em 24 horas
2. Sistema identifica subscrições que requerem notificação
3. Email enviado ao proprietário do tenant com detalhes do trial
4. Subscrição continua como paga após fim do trial

## Componentes UI

### Dashboard
Hub central exibindo informação do tenant ativo, status de subscrição atual e métricas de utilização de recursos com indicadores de progresso visuais.

### Tenant Switcher
Componente Vue permitindo aos utilizadores alternar entre tenants aos quais têm acesso, atualizando o contexto de sessão dinamicamente.

### Vista de Planos de Subscrição
Layout responsivo de cards apresentando planos disponíveis com funcionalidades, preços, informação de trial e botões de ação baseados em permissões do utilizador e compatibilidade.

### Histórico de Subscrições
Tabela paginada de todas as alterações de subscrição com tipos de ação codificados por cor, transições de plano e diferenças de preço.


## Licença

ste projeto foi desenvolvido em contexto académico(estágio).
