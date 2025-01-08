document.addEventListener('DOMContentLoaded', function() {
    // const searchInput = document.getElementById('search-input');
    // const modal = document.getElementById('budgetModal');
    // const newBudgetButton = document.getElementById('new-budget'); // Seleciona o botão
    // const profileLink = document.querySelector('.profile'); // Seleciona o link da foto

    // // Função para abrir o modal
    // window.openModal = function() {
    //     modal.style.display = 'block';
    // };

    // // Função para fechar o modal
    // function closeModal() {
    //     modal.style.display = 'none';
    // }

    // // Limpar pesquisa modal
    // window.limparPesquisa = function() {
    //     if (searchInput) {
    //         searchInput.value = ''; // Limpa o valor do input
    //     }
    // };

    // // Fechar modal ao clicar no "x"
    // const closeButton = document.querySelector('.close');
    // if (closeButton) {
    //     closeButton.addEventListener('click', closeModal);
    // }

    // // Adiciona evento de clique ao botão "Nova Ficha de Trabalho"
    // if (newBudgetButton) {
    //     newBudgetButton.addEventListener('click', function(event) {
    //         event.preventDefault(); // Evita o comportamento padrão do link
    //         openModal(); // Abre o modal
    //     });
    // }

    // // Adiciona evento de clique no link ou imagem da foto
    // if (profileLink) {
    //     profileLink.addEventListener('click', function(event) {
    //         const modal = document.getElementById('budgetModal');
            
    //         // Alterna a exibição do modal
    //         if (modal.style.display === 'block') {
    //             modal.style.display = 'none'; // Fecha o modal se ele estiver aberto
    //         } else {
    //             modal.style.display = 'block'; // Abre o modal se ele estiver fechado
    //         }
    //     });
    // }

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
