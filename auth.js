// auth.js - Sistema de autenticação completo

// Função para fazer login (mantida para compatibilidade, mas login agora é feito via login.html)
function fazerLogin(email, password) {
    // Esta função é mantida apenas para compatibilidade
    // O login real agora é feito via login.php através do formulário
    const usuarioLogado = getUsuarioLogado();
    if (usuarioLogado && usuarioLogado.email === email) {
        return true;
    }
    return false;
}

// Função para fazer logout
async function fazerLogout() {
    try {
        // Chama o backend para fazer logout e destruir a sessão
        await fetch('logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });
    } catch (error) {
        console.error('Erro ao fazer logout no servidor:', error);
    } finally {
        // Remove do localStorage também
        localStorage.removeItem('usuarioLogado');
        atualizarHeader();
        atualizarLinks();
        window.location.href = 'index.html';
    }
}

// Função para obter dados do usuário logado
function getUsuarioLogado() {
    const usuario = localStorage.getItem('usuarioLogado');
    return usuario ? JSON.parse(usuario) : null;
}

// Adicione este código para controlar o comportamento do cabeçalho
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('header');
    const accessibilityBar = document.querySelector('.accessibility-container');
    let lastScroll = 0;
    const headerHeight = header.offsetHeight;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        // No topo da página - mostra ambos os elementos
        if (currentScroll <= 0) {
            header.classList.remove('hidden');
            if (accessibilityBar) accessibilityBar.classList.remove('hidden');
            return;
        }
        
        // Rolando para baixo - esconde ambos
        if (currentScroll > lastScroll && currentScroll > headerHeight) {
            header.classList.add('hidden');
            if (accessibilityBar) accessibilityBar.classList.add('hidden');
        } 
        // Rolando para cima - mostra ambos
        else if (currentScroll < lastScroll) {
            header.classList.remove('hidden');
            if (accessibilityBar) accessibilityBar.classList.remove('hidden');
        }
        
        lastScroll = currentScroll;
    });
    
    // Mantenha sua função existente
    atualizarHeader();
});

// Mantenha sua função atualizarHeader() existente sem modificações
function atualizarHeader() {
    const usuarioLogado = getUsuarioLogado();
    const userActions = document.querySelector('.user-actions');
    
    if (!userActions) return;
    
    if (usuarioLogado) {
        const inicial = usuarioLogado.nome.charAt(0).toUpperCase();
        
        userActions.innerHTML = `
            <div class="user-logged">
                <button class="profile-btn" onclick="abrirPerfil()">${inicial}</button>
                <span class="user-name">${usuarioLogado.nome.split(' ')[0]}</span>
                <button onclick="fazerLogout()" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </button>
            </div>
        `;
    } else {
        userActions.innerHTML = `
            <a href="login.html" class="btn-login">Entrar</a>
            <a href="cadastro.html" class="btn-register">Cadastrar</a>
        `;
    }
}

// Função para atualizar links de login/cadastro
function atualizarLinks() {
    const usuarioLogado = getUsuarioLogado();
    // Seleciona todos os links relevantes
    const links = document.querySelectorAll('a[href="login.html"], a[href="cadastro.html"]');
    // Seleciona todos os cards de serviço com onclick para cadastro.html
    const serviceCards = document.querySelectorAll('.service-card[onclick*="cadastro.html"]');

    if (usuarioLogado) {
        links.forEach(link => {
            if (!link.closest('.user-actions')) {
                link.href = 'servicos.html';
                if (link.classList.contains('btn-hero') || 
                    link.classList.contains('btn-secondary') || 
                    link.classList.contains('btn-primary')) {
                    link.textContent = 'Acessar Serviços';
                    const icon = link.querySelector('i');
                    if (icon) icon.remove();
                }
            }
        });
        // Atualiza o onclick dos cards para servicos.html
        serviceCards.forEach(card => {
            card.setAttribute('onclick', "location.href='servicos.html'");
        });
    } else {
        // Garante que cards voltem para cadastro.html se deslogar
        serviceCards.forEach(card => {
            card.setAttribute('onclick', "location.href='cadastro.html'");
        });
    }
}

// Verificação ao carregar a página
async function verificarAutenticacao() {
    // Primeiro verifica se há sessão no servidor
    try {
        const response = await fetch('verificar_sessao.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include' // Importante para enviar cookies de sessão
        });
        
        const resultado = await response.json();
        
        if (resultado.autenticado && resultado.usuario) {
            // Sincroniza com localStorage para compatibilidade
            localStorage.setItem('usuarioLogado', JSON.stringify({
                email: resultado.usuario.email,
                nome: resultado.usuario.nome,
                tipoUsuario: resultado.usuario.tipoUsuario
            }));
        } else {
            // Se não há sessão no servidor, limpa localStorage
            localStorage.removeItem('usuarioLogado');
        }
    } catch (error) {
        console.error('Erro ao verificar sessão:', error);
        // Em caso de erro, usa localStorage como fallback
    }
    
    const usuarioLogado = getUsuarioLogado();
    
    // Protege páginas que requerem login
    if (window.location.pathname.includes('servicos.html') && !usuarioLogado) {
        window.location.href = 'index.html';
        return false;
    }
    
    // Atualiza a interface
    atualizarHeader();
    atualizarLinks();
    return !!usuarioLogado;
}

function abrirPerfil() {
    // Por enquanto só mostra um alerta
    const usuario = getUsuarioLogado();
    window.location.href = 'niveis.html';
}

// Inicialização
document.addEventListener('DOMContentLoaded', verificarAutenticacao);