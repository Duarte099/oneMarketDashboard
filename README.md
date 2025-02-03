ver erro ao inserir fotos

botao responsivo nao funciona na pagina da ficha de trabalho


TODOS:


Documento PDF com a descrição do projeto e manual de utilizador. 
Documentação técnica (explicação do projeto, tecnologias usadas, requisitos de instalação, base de dados(sql)).
Relatório sobre mecânicas e estrutura do código. 



Código-fonte compactado (.zip ou .rar) com estrutura organizada. - FEITO
APK para testes ou instruções para instalação. - FEITO
Link para o repositório (GitHub, GitLab). - FEITO
Ficheiro README com instruções claras de execução. - FEITO

https://github.com/paularcsarruda/Stock#%EF%B8%8F-recursos-utilizados


OneMarket - Gestor de Orçamentos e Fichas de Trabalho

    Gestor Orçamentos e Fichas de Trabalho desenvolvido em PHP e JavaScript. O projeto foi desenvolvido para a Prova de Aptidão Profissional(PAP) do curso programador de informática.

Funcionalidades 

    1. Dashboard

        - Permite ver os ganhos de cada mês
        - Acompanhamento do progresso dos projetos ("Em Desenvolvimento", "Pendente", "Em Obra", "Concluído").
        - Ultimas acções dentro da plataforma (logs)  
        - Ver os produtos que se encontram fora de stock

    2. Fichas de Trabalho
        
        - Ver, Criar, Editar e Apagar de Fichas de Trabalho
        - Ver versões anteriores das ficha de trabalho
        - Impressão das fichas de trabalho
        - Galeria de fotos por secções

    3. Gestão de Orçamentos
        - Ver, Criar, Editar e Apagar orçamentos detalhados
        - Impressão do orcamento
        - Ver versões anteriores do orcamento

    4. Stock de Produtos
        - Ver, Criar, Editar e desativar os produtos

    5. Clientes
        - Ver, Criar, Editar e desativar os clientes

    6. Administração
        - Ver, Criar, Editar e Apagar os administradores
        - Adicionar ou alterar as permissões dos administradores
        - Ver todas as acções dentro da plataforma (logs)

    7. Perfil
        - Ver e Editar o seu perfil

Instruções de execução

    1. Fazer download do projeto

    2. Fazer download do XAMPP
        - https://www.apachefriends.org

    3. Colocar o Projeto na Pasta do XAMPP.
        - Localizar a pasta htdocs do XAMPP (normalmente em C:\xampp\htdocs\)
        - Copiar a pasta do projeto para dentro da pasta htdocs
    
    4. Iniciar o Servidor Local
        - Abrir o XAMPP Control Panel
        - Clicar em Start nos módulos Apache e MySQL
    
    5. Base de Dados
        - Aceder ao phpMyAdmin através: http://localhost/phpmyadmin
        - Clicar em SQL (2 aba em cima) 
        - Pegar no codigo raiz do projeto e colocar na consulta SQL e clicar no botao de continuar

    6. Aceder ao Projeto
        - Abrir o navegador e colocar : http://localhost/nome-da-pasta-do-projeto/
        - Substituir o nome_da_pasta_do_projeto pelo nome que esta na sua pasta em htdocs

Recursos Utilizados 

    - Visual Studio Code
    - PHP
    - JavaScript
    - WAMPP
    - MYSQL
    - GitHub
    - GIMP


1. Arquitetura Geral
    A aplicação pode seguir o padrão MVC (Model-View-Controller) ou Clean Architecture, dependendo da complexidade e tecnologia escolhida.

    Frontend (Interface do Usuário)
    Pode ser desenvolvido com React, Angular ou Vue.js (para web) ou Flutter/React Native (para mobile).
    Consome APIs para exibir e manipular os dados.
    Permite criar, visualizar e editar orçamentos e fichas de trabalho.

    Backend (Lógica de Negócio e Processamento)
    Pode ser construído em Node.js (Express, NestJS), Python (Django, FastAPI), Java (Spring Boot) ou outras linguagens/frameworks.
    Fornece APIs REST ou GraphQL para comunicação com o frontend.
    Gerencia autenticação, regras de negócio e persistência de dados.

    Banco de Dados (Armazenamento de Dados)
    SQL (PostgreSQL, MySQL) para dados estruturados e relações complexas.
    NoSQL (MongoDB, Firebase) para maior flexibilidade e escalabilidade.

    Contém tabelas/coleções como:
    Usuários (dados de login, permissões)
    Clientes (informações cadastrais)
    Orçamentos (valores, status, detalhes)
    Fichas de Trabalho (tarefas, responsáveis, prazos)
    Produtos/Serviços (itens que compõem os orçamentos)
2. Fluxo de Funcionamento
    Usuário faz login e acessa o sistema.
    Pode criar um orçamento, selecionando produtos/serviços e gerando um valor final.
    O orçamento pode ser enviado para o cliente e ficar com status "Aguardando Aprovação".
    Após aprovação, gera-se uma Ficha de Trabalho, contendo tarefas e responsáveis.
    O usuário pode acompanhar a execução das tarefas e atualizar o status.
    No final, pode-se gerar relatórios e analisar métricas.
3. Tecnologias Possíveis
    Frontend: React.js + TailwindCSS / Material UI
    Backend: Node.js (NestJS) ou Python (Django)
    Banco de Dados: PostgreSQL / MongoDB
    Autenticação: JWT / OAuth
    Hospedagem: AWS, Vercel, Firebase