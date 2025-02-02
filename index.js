document.addEventListener('DOMContentLoaded', function() {
    // Variáveis globais
    const themeSwitch = document.querySelector('#theme-switch');
    const themeSwitchMobile = document.querySelector('#theme-switch-mobile');
    const logoImage = document.querySelector('#logoImage');
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileHeader = document.querySelector('.mobile-header');
    const sidebarLinks = document.querySelectorAll('.sidebar .side-menu li a');

    // Função para definir o tema
    const setTheme = (isLight) => {
        document.documentElement.classList.toggle('light-mode', isLight);
        if(logoImage) {
            logoImage.src = isLight ? './images/LogoOnemarketPreto.png' : './images/LogoOnemarketBranco.png';
        }
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        if(themeSwitch) themeSwitch.checked = isLight;
        if(themeSwitchMobile) themeSwitchMobile.checked = isLight;
    };

    // Carregar tema salvo
    const savedTheme = localStorage.getItem('theme');
    if(savedTheme) setTheme(savedTheme === 'light');

    // Event listeners para alternância de tema
    if(themeSwitch) themeSwitch.addEventListener('change', (e) => setTheme(e.target.checked));
    if(themeSwitchMobile) themeSwitchMobile.addEventListener('change', (e) => setTheme(e.target.checked));

    // Controle do sidebar
    if(sidebarToggle && sidebar) {
        // Abrir/fechar menu
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // Fechar ao clicar em links (mobile)
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                if(window.innerWidth <= 768) {
                    sidebar.classList.remove('open');
                }
            });
        });

        // Esconder botão ao rolar
        let lastScrollTop = 0;
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if(scrollTop > lastScrollTop && scrollTop > 50) {
                sidebarToggle.classList.add('hidden');
            } else {
                sidebarToggle.classList.remove('hidden');
            }
            
            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        }, false);
    }
});

function handleRowClick(id, action) {
    if (action == "budget") {
        window.location.href = "orcamentoCriar.php?idClient=" + id;
    }
    else if (action == "worksheet") {
        window.location.href = "fichaTrabalhoCriar.php?idBudget=" + id;
    }
    else if (action == "editBudget") {
        window.location.href = "orcamentoEdit.php?idBudget=" + id;
    }
    else if (action == "editClient") {
        window.location.href = "clienteEdit.php?idClient=" + id;
    }
    else if (action == "editAdmin") {
        window.location.href = "adminEdit.php?idAdmin=" + id;
    }
    else if (action == "editWorksheet") {
        window.location.href = "fichaTrabalhoEdit.php?idWorksheet=" + id;
    }
    else if (action == "stock") {
        window.location.href = "produtoEdit.php?idProduct=" + id;
    }
}

$(document).ready(function () {
    function adjustHeight(element) {
        element.style.height = 'auto'; // Reseta a altura para calcular corretamente
        element.style.height = (element.scrollHeight) + 'px'; // Define a altura com base no scrollHeight
    }

    // Aplica o ajuste ao carregar a página e em eventos
    $('.autoExpand').each(function () {
        adjustHeight(this); // Ajusta a altura para texto padrão
    }).on('input change', function () {
        adjustHeight(this); // Ajusta a altura ao digitar ou alterar texto
    });

    $("#preloader").fadeOut("slow");  //Adiciona a classe para efeito fade-out
     
});

// Fechar sidebar ao clicar fora (mobile)
function closeSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if(sidebar && window.innerWidth <= 768) {
        sidebar.classList.remove('open');
    }
}