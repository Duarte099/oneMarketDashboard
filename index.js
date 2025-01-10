document.addEventListener('DOMContentLoaded', function() {
    const themeSwitch = document.querySelector('#theme-switch');
    const logoImage = document.querySelector('#logoImage');

    // Carregar a preferência de tema do localStorage
    const savedTheme = localStorage.getItem('theme'); // Obter o tema armazenado
    if (savedTheme === 'light') {
        document.documentElement.classList.add('light-mode');
        themeSwitch.checked = true;
        logoImage.src = '../images/LogoOnemarketPreto.png';
    } else {
        document.documentElement.classList.remove('light-mode');
        themeSwitch.checked = false;
        logoImage.src = '../images/LogoOnemarketBranco.png';
    }

    // Alternar tema e salvar a preferência no localStorage
    themeSwitch.addEventListener('change', () => {
        if (themeSwitch.checked) {
            document.documentElement.classList.add('light-mode'); // Ativar modo claro
            logoImage.src = '../images/LogoOnemarketPreto.png';
            localStorage.setItem('theme', 'light'); // Salvar preferência
        } else {
            document.documentElement.classList.remove('light-mode'); // Ativar modo escuro
            logoImage.src = '../images/LogoOnemarketBranco.png';
            localStorage.setItem('theme', 'dark'); // Salvar preferência
        }
    });
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
