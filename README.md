# GTI – Get Trash Intelligence

> Projeto acadêmico — Plataforma que conecta pessoas e empresas a serviços de coleta seletiva e reciclagem.

Sistema web desenvolvido para a faculdade, com cadastro, login, planos de coleta, pagamento e área empresarial. Funciona localmente com XAMPP.

**[Acessar o projeto no ar](https://gettrashintelligence.netlify.app/)**

---

## Sobre a GTI – A ideia

A **Get Trash Intelligence** nasceu para transformar a gestão de resíduos em um processo acessível, eficiente e recompensador. A plataforma conecta **geradores de resíduos** (pessoas físicas, condomínios e empresas) a **coletores e recicladores** qualificados, facilitando o agendamento de coletas seletivas, o descarte correto de materiais recicláveis e o acompanhamento do impacto ambiental gerado.

Com uma abordagem que une tecnologia e sustentabilidade, a GTI oferece planos de coleta personalizados, gamificação para engajar os usuários e uma área empresarial para demandas específicas. O objetivo é promover a economia circular, reduzir o envio de resíduos para aterros e incentivar práticas responsáveis por meio de uma experiência simples e transparente.

---

## O que faz

- **Cadastro e login** de usuários (cliente, empresa ou admin)
- **Planos de coleta** (Básico, Empresarial, Premium) com diferentes tamanhos de caçamba
- **Checkout** simulando pagamento (cartão, PIX, boleto)
- **Área empresarial** para solicitações específicas
- **Dark mode** e ajuste de fonte
- **Layout responsivo**
- **API de usuários** para administração (`GET /api/usuarios.php`)
- **Autorização por perfil** para endpoints administrativos

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

## Segurança aplicada

- Senhas com `password_hash()` e login com `password_verify()`
- Rehash automático de senha quando necessário (`password_needs_rehash()`)
- Sessões PHP endurecidas (`HttpOnly`, `SameSite=Lax`, `session.use_strict_mode`)
- Regeneração de sessão no login/cadastro (`session_regenerate_id(true)`)
- Operações de update/delete protegidas para perfil `admin`

---

## Observações

- Projeto de faculdade, não é produção real  
- Pagamentos são simulados (não há integração com gateway real)  
- Use apenas em ambiente local ou de desenvolvimento  
- Se algo não funcionar, confira se o MySQL está rodando e se o `bd.sql` foi importado

---

## Licença

Projeto acadêmico — uso livre para fins educacionais.
