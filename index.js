document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const modal = document.getElementById('budgetModal');
    const newBudgetButton = document.getElementById('new-budget'); // Seleciona o botão
    const profileLink = document.querySelector('.profile'); // Seleciona o link da foto

    // Função para abrir o modal
    window.openModal = function() {
        modal.style.display = 'block';
        localStorage.setItem('modalOpen', 'true'); // Armazena no Local Storage
    };

    // Função para fechar o modal
    function closeModal() {
        modal.style.display = 'none';
        localStorage.removeItem('modalOpen'); // Remove do Local Storage
    }

    // Verifica se o modal deve ser aberto ao carregar a página
    if (localStorage.getItem('modalOpen') === 'true' || (searchInput && searchInput.value)) {
        openModal();
    }

    // Evento para abrir o modal ao pressionar Enter
    if (searchInput) {
        searchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault(); // Evita o envio do formulário
                openModal();
                window.location.href = window.location.pathname + '?search-input=' + encodeURIComponent(searchInput.value);
            }
        });
    }

    // Limpar pesquisa modal
    window.limparPesquisa = function() {
        if (searchInput) {
            searchInput.value = ''; // Limpa o valor do input
        }
        window.location.href = window.location.pathname; // Recarrega a página
        openModal(); // Abre o modal sem valor
    };

    // Fechar modal ao clicar no "x"
    const closeButton = document.querySelector('.close');
    if (closeButton) {
        closeButton.addEventListener('click', closeModal);
    }

    // Adiciona evento de clique ao botão "Nova Ficha de Trabalho"
    if (newBudgetButton) {
        newBudgetButton.addEventListener('click', function(event) {
            event.preventDefault(); // Evita o comportamento padrão do link
            openModal(); // Abre o modal
        });
    }

    // Adiciona evento de clique no link ou imagem da foto
    if (profileLink) {
        profileLink.addEventListener('click', function(event) {
            const modal = document.getElementById('budgetModal');
            
            // Alterna a exibição do modal
            if (modal.style.display === 'block') {
                modal.style.display = 'none'; // Fecha o modal se ele estiver aberto
            } else {
                modal.style.display = 'block'; // Abre o modal se ele estiver fechado
            }
        });
    }
});

function handleRowClick(id, action) {
    if (action == "budget") {
        window.location.href = "novoOrcamento.php?idClient=" + id;
    }
    else if (action == "worksheet") {
        window.location.href = "novaFichaTrabalho.php?idBudget=" + id;
    }
    else if (action == "editBudget") {
        window.location.href = "editBudget.php?idBudget=" + id;
    }
    else if (action == "editAdmin") {
        window.location.href = "editAdmin.php?id=" + id;
    }
    else if (action == "editWorksheet") {
        window.location.href = "editFichatrabalho.php?idWorksheet=" + id;
    }
}