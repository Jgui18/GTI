# Documentação do Back-end — Sistema de Autenticação e API

Documentação da implementação de cadastro, login e endpoints PHP com MySQL.

## Estrutura de arquivos PHP

| Arquivo | Descrição |
|---------|-----------|
| `conexao.php` | Conexão PDO singleton com MySQL |
| `cadastro.php` | Cadastro de novos usuários |
| `login.php` | Login e autenticação |
| `verificar_sessao.php` | Verificação de sessão ativa |
| `logout.php` | Logout e destruição de sessão |
| `processar_pagamento.php` | Processamento de pagamentos |
| `update_usuario.php`, `update_pagamento.php`, `update_cacamba.php` | Updates |
| `delete_usuario.php`, `delete_pagamento.php`, `delete_cacamba.php` | Deletes |

O banco de dados é criado e populado pelo arquivo `bd.sql` (banco `gti_bd`).

---

## Endpoints da API

### POST `/cadastro.php`

Cadastra um novo usuário.

**Corpo (JSON):**
```json
{
  "nome": "João Silva",
  "email": "joao@example.com",
  "senha": "123456",
  "cpf": "123.456.789-00",
  "telefone": "(21) 98765-4321",
  "tipoUsuario": "cliente"
}
```

**Campos obrigatórios:** `nome`, `email`, `senha`

---

### POST `/login.php`

Autentica o usuário e inicia sessão PHP.

**Corpo (JSON):**
```json
{
  "email": "joao@example.com",
  "senha": "123456",
  "remember": true
}
```

---

### GET `/verificar_sessao.php`

Verifica se há sessão ativa. Retorna `autenticado: true/false` e dados do usuário quando autenticado.

---

### POST `/logout.php`

Destrói a sessão do usuário.

---

## Segurança

- **Hash de senhas:** `password_hash()` com bcrypt
- **Prepared statements:** prevenção de SQL Injection
- **Validação de e-mail** no backend
- **Sessões PHP** para autenticação
- **Sanitização** dos dados de entrada

---

## Fluxo de autenticação

1. **Cadastro** → formulário em `cadastro.html` → Fetch API → `cadastro.php` → MySQL
2. **Login** → formulário em `login.html` → Fetch API → `login.php` → sessão iniciada
3. **Verificação** → `auth.js` chama `verificar_sessao.php` em páginas protegidas
4. **Logout** → `auth.js` chama `logout.php` e limpa localStorage

---

## Troubleshooting

| Erro | Solução |
|------|---------|
| "Erro ao conectar com o banco de dados" | Verifique MySQL, credenciais em `conexao.php` e se o banco `gti_bd` existe |
| "Table doesn't exist" | Importe o arquivo `bd.sql` no phpMyAdmin |
| Sessão não persiste | Verifique `php.ini` e permissões do diretório de sessões |
| CORS errors | Confira os headers CORS nos arquivos PHP |

---

## Exemplo com cURL

```bash
# Cadastro
curl -X POST http://localhost/GTI/cadastro.php \
  -H "Content-Type: application/json" \
  -d '{"nome":"Teste","email":"teste@email.com","senha":"123456"}'

# Login
curl -X POST http://localhost/GTI/login.php \
  -H "Content-Type: application/json" \
  -c cookies.txt \
  -d '{"email":"teste@email.com","senha":"123456"}'
```
