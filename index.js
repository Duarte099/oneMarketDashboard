document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const modal = document.getElementById('budgetModal');
    const newBudgetButton = document.getElementById('new-budget'); // Seleciona o botão

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
    if (localStorage.getItem('modalOpen') === 'true' || searchInput.value) {
        openModal();
    }

    // Evento para abrir o modal ao pressionar Enter
    searchInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Evita o envio do formulário
            openModal();
            window.location.href = window.location.pathname + '?search-input=' + encodeURIComponent(searchInput.value);
        }
    });

    // Limpar pesquisa modal
    window.limparPesquisa = function() {
        searchInput.value = ''; // Limpa o valor do input
        window.location.href = window.location.pathname; // Recarrega a página
        openModal(); // Abre o modal sem valor
    };

    // Fechar modal ao clicar no "x"
    document.querySelector('.close').addEventListener('click', closeModal);
    //adicionar recarregar pagina para resetar url : window.location.href = window.location.pathname;

    // Adiciona evento de clique ao botão "Nova Ficha de Trabalho"
    newBudgetButton.addEventListener('click', function(event) {
        event.preventDefault(); // Evita o comportamento padrão do link
        openModal(); // Abre o modal
    });
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
}