# Tenant Manager

Projeto Laravel 12 com Vue 3, TailwindCSS, Shadcn Vue e pacotes de segurança.

## Stack Tecnológica

- **Laravel 12** - Framework PHP
- **Vue 3** - Framework JavaScript
- **TailwindCSS 4** - Framework CSS
- **Shadcn Vue** - Componentes UI
- **MySQL** - Banco de dados
- **Laravel Fortify** - Autenticação com 2FA
- **Spatie Permission** - Gerenciamento de permissões

## Instalação

### 1. Instalar dependências PHP

```bash
composer install
```

### 2. Instalar dependências Node

```bash
npm install
```

### 3. Configurar ambiente

Copie o arquivo `.env.example` para `.env`:

```bash
cp .env.example .env
```

Configure as variáveis de ambiente no arquivo `.env`, especialmente:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tenant_manager
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

### 4. Gerar chave da aplicação

```bash
php artisan key:generate
```

### 5. Executar migrations

```bash
php artisan migrate
```

### 6. Compilar assets

```bash
npm run build
```

Ou para desenvolvimento com hot reload:

```bash
npm run dev
```

## Estrutura do Projeto

```
tenant-manager/
├── app/
│   ├── Actions/
│   │   └── Fortify/          # Ações do Fortify
│   ├── Models/
│   │   └── User.php          # Model User com traits
│   └── Providers/
│       └── FortifyServiceProvider.php
├── config/
│   ├── fortify.php           # Configuração do Fortify (2FA habilitado)
│   └── permission.php        # Configuração do Spatie Permission
├── database/
│   └── migrations/           # Migrations incluindo Fortify e Permission
├── resources/
│   ├── js/
│   │   ├── App.vue           # Componente principal Vue
│   │   ├── app.js            # Entry point Vue
│   │   └── lib/
│   │       └── utils.js      # Utilitários Shadcn Vue
│   ├── css/
│   │   └── app.css           # Estilos TailwindCSS
│   └── views/
│       └── app.blade.php     # View principal
└── routes/
    └── web.php               # Rotas da aplicação
```

## Funcionalidades Configuradas

### Laravel Fortify (2FA)

- Autenticação completa configurada
- Two-Factor Authentication (2FA) habilitado
- Features disponíveis:
  - Registro de usuários
  - Reset de senha
  - Verificação de email
  - Atualização de perfil
  - Atualização de senha
  - Autenticação de dois fatores

### Spatie Permission

- Gerenciamento de roles e permissões
- Trait `HasRoles` adicionado ao modelo User
- Migrations publicadas

## Comandos Úteis

### Desenvolvimento

```bash
# Iniciar servidor Laravel
php artisan serve

# Compilar assets em modo desenvolvimento
npm run dev

# Compilar assets para produção
npm run build
```

### Banco de Dados

```bash
# Executar migrations
php artisan migrate

# Reverter última migration
php artisan migrate:rollback

# Criar nova migration
php artisan make:migration nome_da_migration
```

### Permissões

```bash
# Criar role
php artisan tinker
>>> $role = Spatie\Permission\Models\Role::create(['name' => 'admin']);

# Atribuir role a usuário
>>> $user->assignRole('admin');
```

## Próximos Passos

1. Configurar o banco de dados MySQL
2. Executar as migrations
3. Criar o primeiro usuário
4. Configurar roles e permissões iniciais
5. Começar a desenvolver as funcionalidades do Tenant Manager

## Documentação

- [Laravel 12](https://laravel.com/docs/12.x)
- [Vue 3](https://vuejs.org/)
- [TailwindCSS](https://tailwindcss.com/)
- [Shadcn Vue](https://www.shadcn-vue.com/)
- [Laravel Fortify](https://laravel.com/docs/fortify)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
