<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Inputs Sem JS</title>
</head>
<body>
    <?php
    // Captura o valor selecionado
    $selectedOption = isset($_GET['options']) ? $_GET['options'] : '';
    $inputValue = '';

    // Define o valor do input com base na seleção
    switch ($selectedOption) {
        case 'opcao1':
            $inputValue = 'Valor para Opção 1';
            break;
        case 'opcao2':
            $inputValue = 'Valor para Opção 2';
            break;
        case 'opcao3':
            $inputValue = 'Valor para Opção 3';
            break;
        default:
            $inputValue = 'Nenhuma opção selecionada';
            break;
    }
    ?>

    <form action="" method="GET">
        <label for="options">Escolha uma opção:</label>
        <select id="options" name="options" onchange="this.form.submit()">
            <option value="">--Selecione--</option>
            <option value="opcao1" <?= $selectedOption === 'opcao1' ? 'selected' : '' ?>>Opção 1</option>
            <option value="opcao2" <?= $selectedOption === 'opcao2' ? 'selected' : '' ?>>Opção 2</option>
            <option value="opcao3" <?= $selectedOption === 'opcao3' ? 'selected' : '' ?>>Opção 3</option>
        </select>

        <br><br>

        <label for="input">Valor associado:</label>
        <input type="text" id="input" name="input" value="<?= htmlspecialchars($inputValue) ?>" readonly>
    </form>
</body>
</html>