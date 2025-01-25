document.addEventListener('DOMContentLoaded', function() {
    const themeSwitch = document.querySelector('#theme-switch');
    const themeSwitchMobile = document.querySelector('#theme-switch-mobile');
    const logoImage = document.querySelector('#logoImage');
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileHeader = document.querySelector('.mobile-header');

    function setTheme(isLight) {
        document.documentElement.classList.toggle('light-mode', isLight);
        logoImage.src = isLight ? '../images/LogoOnemarketPreto.png' : '../images/LogoOnemarketBranco.png';
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        if (themeSwitch) themeSwitch.checked = isLight;
        if (themeSwitchMobile) themeSwitchMobile.checked = isLight;
    }

    // Carregar a preferência de tema do localStorage
    const savedTheme = localStorage.getItem('theme');
    setTheme(savedTheme === 'light');

    // Alternar tema e salvar a preferência no localStorage
    if (themeSwitch) {
        themeSwitch.addEventListener('change', () => setTheme(themeSwitch.checked));
    }
    if (themeSwitchMobile) {
        themeSwitchMobile.addEventListener('change', () => setTheme(themeSwitchMobile.checked));
    }

    // Controle do sidebar e header móvel
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            if (window.innerWidth <= 768 && mobileHeader) {
                mobileHeader.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
            }
        });
    }

    // Fechar o sidebar ao clicar em um link (opcional)
    const sidebarLinks = document.querySelectorAll('.sidebar .side-menu li a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('open');
                if (mobileHeader) mobileHeader.style.display = 'none';
            }
        });
    });

    // Novo código para controlar a visibilidade do botão de toggle
    let lastScrollTop = 0;
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > lastScrollTop && scrollTop > 50) {
            // Rolando para baixo
            sidebarToggle.classList.add('hidden');
        } else {
            // Rolando para cima ou no topo
            sidebarToggle.classList.remove('hidden');
        }
        
        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // Para Mobile ou browsers negativos
    }, false);
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
        window.location.href = "clienteEdit.php?id=" + id;
    }
    else if (action == "editAdmin") {
        window.location.href = "adminEdit.php?id=" + id;
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
});