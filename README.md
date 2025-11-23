projeto integrado unifeob - modulo 8 - trimestre novembro/2025

segurança em sistemas computacionais
inteligência artificial

participantes do grupo
Eduardo Corbano Lourenço, RA 24001663
Elton Mazzali Pinto, RA 24002287
Joice Grazieli Vieira, RA 24002213
Kathlyn Rose Silveira, RA 24002286



Oficina Inteligente
Oficina Inteligente é um sistema web de gestão para oficinas mecânicas e centros automotivos. O objetivo do projeto é facilitar o controle de ordens de serviço, gestão de clientes, veículos e estoque de peças, oferecendo uma interface simples e eficiente para o dia a dia da oficina.

🚀 Funcionalidades
Gestão de Clientes: Cadastro completo com histórico de serviços.

Gestão de Veículos: Associação de carros/motos aos clientes (Placa, Modelo, Marca).

Ordens de Serviço (O.S.): Criação, edição e acompanhamento de status (Orçamento, Aprovado, Em Andamento, Concluído).

Controle de Estoque: Gerenciamento de peças e produtos.

Dashboard: Visão geral dos serviços do dia e faturamento.

Relatórios: Geração de relatórios simples de serviços prestados.

🛠️ Tecnologias Utilizadas
Este projeto foi desenvolvido utilizando as seguintes tecnologias:

Back-end: Python com framework Flask

Banco de Dados: MySQL / SQLAlchemy (ORM)

Front-end: HTML5, CSS3, Bootstrap 5, JavaScript

Deploy: Configurado para rodar em VPS (Gunicorn/Nginx)

📂 Estrutura do Projeto
oficina-inteligente/
├── app/
│   ├── controllers/     # Lógica das rotas
│   ├── models/          # Modelos do banco de dados
│   ├── static/          # Arquivos CSS, JS e Imagens
│   ├── templates/       # Arquivos HTML (Jinja2)
│   └── __init__.py      # Inicialização do Flask
├── config.py            # Configurações de ambiente
├── run.py               # Arquivo principal de execução
├── requirements.txt     # Dependências do projeto
└── README.md
⚡ Como Executar o Projeto
Pré-requisitos
Python 3.8 ou superior instalado.

Git instalado.

Um ambiente virtual (recomendado).

Passo a Passo
Clone o repositório:

Bash

git clone https://github.com/seu-usuario/oficina-inteligente.git
cd oficina-inteligente
Crie e ative um ambiente virtual:

Windows:

Bash

python -m venv venv
venv\Scripts\activate
Linux/Mac:

Bash

python3 -m venv venv
source venv/bin/activate
Instale as dependências:

Bash

pip install -r requirements.txt
Configure as variáveis de ambiente: Crie um arquivo .env na raiz do projeto com as configurações do banco de dados e chave secreta:

Snippet de código

FLASK_APP=run.py
FLASK_ENV=development
SECRET_KEY=sua_chave_secreta_aqui
SQLALCHEMY_DATABASE_URI=mysql+pymysql://usuario:senha@localhost/nome_do_banco
Inicialize o Banco de Dados (se necessário):

Bash

flask db init
flask db migrate
flask db upgrade
Execute o servidor:

Bash

flask run
Acesse no navegador: O sistema estará rodando em: http://127.0.0.1:5000

🤝 Como Contribuir
Faça um fork do projeto.

Crie uma nova branch com as suas alterações: git checkout -b feature/minha-feature

Salve as alterações e crie uma mensagem de commit contando o que você fez: git commit -m "feature: Minha nova feature"

Envie as suas alterações: git push origin feature/minha-feature

Abra um Pull Request no repositório original.

📄 Licença
Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.
