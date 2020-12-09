# Processo Seletivo Wiser - Desafio de Tecnologia

O aplicativo possui interação com o serviço de storage da Box.com utilizando credenciais do modo Server Authentication (Client Credentials Grant), contendo as seguintes funçoes no Sistema: 
 - Sistema de Login e Logout
 - Cadastro de novos usuários
 - Possibilidade de salvar/alterar as credenciais de acesso ao Box.com no sistema
 - Visualizar informações armazenadas do usuário e alterar sua senha
 - Listagem de arquivos e pastas
 - Fazer upload e download de arquivos
 - Excluir pastas e arquivos
 - Editar dados das pastas e arquivos
 - Inserir novas versões dos arquivos
 - Verificar informaçoes mais detalhadas dos arquivos e das pastas
 - Verificar as versões dos arquivos, podendo excluir a versão ou torna-la a versão primaria/oficial
 - Criar novas pastas
 - Navergar entre as pastas e seus arquivos

As ferramentas utilizadas foram: PHP Version 7.2.19, Framework  Yii2, SqLite3, PHP built-in web server / Laragon Full 4.0.16 + Apache 2.4.35, IDE PhpStorm 2020.3.

Para iniciar o projeto, realize o download no git dos arquivos, colocando eles no diretorio correto para iniciazar o servidor.

Em relação ao servidor, foi testado com as seguintes formas: Para o uso do Laragon, foram habilitadas as extensões 'sqlite3, pdo_sqlite, curl e ldap', além das já habilitadas e acessado com o link 'http://localhost/Nome_da_Pasta>/web/index.php', estando o projeto dentro da pasta 'www' no diretório do Laragon. Com PHP web server, foi utilizado o código 'php -S localhost:8000' no terminal no diretório do projeto e acessado com o link 'http://localhost:8000/web/index.php' (Testado após habilitar as extensões com o Laragon). 

No arquivo 'db.php', dentro da pasta 'config', consta as tabelas e as colunas utilizadas, que são criadas no acesso caso não exista.

As credenciais do Box.com necessárias são 'Client Id', 'Client Secret' e 'User Id'. Elas podem ser inseridas dentro do sistema ou via banco.

Link Yii:
https://www.yiiframework.com/doc/guide/2.0/en/start-installation

Link AdminLte2:
https://github.com/dmstr/yii2-adminlte-asset

Link Laragon Full:
https://laragon.org/download/

API Reference:
https://developer.box.com/reference/
