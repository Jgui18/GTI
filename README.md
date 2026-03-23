# GTI – Get Trash Intelligence

> Projeto acadêmico — Plataforma que conecta pessoas e empresas a serviços de coleta seletiva e reciclagem.

Sistema web desenvolvido para a faculdade, com cadastro, login, planos de coleta, pagamento e área empresarial. Funciona localmente com XAMPP.

---

## O que faz

- **Cadastro e login** de usuários (cliente, empresa ou admin)
- **Planos de coleta** (Básico, Empresarial, Premium) com diferentes tamanhos de caçamba
- **Checkout** simulando pagamento (cartão, PIX, boleto)
- **Área empresarial** para solicitações específicas
- **Dark mode** e ajuste de fonte
- **Layout responsivo**

---

## Tecnologias

| Camada     | Stack                       |
|------------|-----------------------------|
| Frontend   | HTML5, CSS3, JavaScript     |
| Backend    | PHP (PDO, sessões)          |
| Banco      | MySQL (`gti_bd`)            |
| Servidor   | XAMPP (Apache + PHP + MySQL)|

---

## Como rodar

### 1. Pré-requisitos

- [XAMPP](https://www.apachefriends.org/) (Apache + PHP + MySQL)

### 2. Banco de dados

1. Inicie o MySQL pelo painel do XAMPP  
2. Abra o phpMyAdmin: `http://localhost/phpmyadmin`  
3. Crie um novo banco ou importe o arquivo `bd.sql` (ele cria o banco `gti_bd` e as tabelas)

### 3. Configurar conexão

Se precisar alterar host, usuário ou senha do MySQL, edite o arquivo `conexao.php`:

```php
private const HOST = 'localhost';
private const DB_NAME = 'gti_bd';
private const USER = 'root';
private const PASSWORD = '';  // sua senha aqui
```

### 4. Rodar o projeto

1. Copie a pasta `GTI` para `C:\xampp\htdocs\` (ou use o caminho do seu XAMPP)  
2. Acesse: `http://localhost/GTI/`  
3. A página inicial deve carregar e você pode testar cadastro, login e fluxo de serviços

---

## Estrutura do projeto

```
GTI/
├── index.html          # Página inicial
├── login.html          # Login
├── cadastro.html       # Cadastro
├── servicos.html       # Serviços e planos
├── pagamento.html      # Checkout
├── niveis.html         # Gamificação / níveis
├── empresarial.html    # Área empresarial
├── conexao.php         # Conexão com o banco
├── cadastro.php        # API de cadastro
├── login.php           # API de login
├── auth.js             # Autenticação no front
├── bd.sql              # Script do banco de dados
├── docs/               # Documentação adicional
│   └── BACKEND.md      # Detalhes da API e backend
├── CRUDs/              # Scripts SQL de consulta
├── images/             # Imagens e favicon
└── logs/               # Logs (não versionados)
```

---

## Documentação

- **[docs/BACKEND.md](docs/BACKEND.md)** — Endpoints da API, autenticação, segurança e exemplos de uso

---

## Observações

- Projeto de faculdade, não é produção real  
- Pagamentos são simulados (não há integração com gateway real)  
- Use apenas em ambiente local ou de desenvolvimento  
- Se algo não funcionar, confira se o MySQL está rodando e se o `bd.sql` foi importado

---

## Publicar no GitHub

Se quiser colocar o projeto no GitHub:

1. Crie um repositório novo em [github.com/new](https://github.com/new) (pode ser público e sem README inicial)
2. No terminal, dentro da pasta do projeto:
   ```bash
   git remote add origin https://github.com/SEU_USUARIO/GTI.git
   git branch -M main
   git push -u origin main
   ```
3. Troque `SEU_USUARIO` pelo seu usuário do GitHub

---

## Licença

Projeto acadêmico — uso livre para fins educacionais.
