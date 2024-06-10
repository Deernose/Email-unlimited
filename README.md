# Autor: Vitor Deernose
"Email Verifier Software" is the solution to keeping your contact list clean. by www.email-unlimited.com
## Este repositorio
Este repositorio compoe versões atualizadas de scripts para o email-soft, assim suportando php 7.0 até 8.2 (março/2024)

## Siga tambem abaixo um pequeno tutorial para estar configurando seu email-soft 

## Guia de Instalação e Configuração do WSL com Debian e Apache

## Instalar o WSL

1. Abra o PowerShell como administrador e execute o seguinte comando para instalar o WSL com Debian:
    ```bash
    wsl --install -d Debian
    ```
2. Aguarde a conclusão da instalação.

## Configuração do Debian

1. Acesse o Debian e digite o comando abaixo para obter o IP do seu WSL:
    ```bash
    ip addr
    ```
2. Anote o IP exibido (por exemplo, `172.19.130.129`).

## Redirecionamento de Portas

1. Abra o CMD do Windows como administrador e execute os seguintes comandos para redirecionar as portas do Windows para o WSL:

    Porta 80:
    ```bash
    netsh interface portproxy add v4tov4 listenport=80 listenaddress=0.0.0.0 connectport=80 connectaddress=172.19.130.129
    ```

    Porta 25:
    ```bash
    netsh interface portproxy add v4tov4 listenport=25 listenaddress=0.0.0.0 connectport=25 connectaddress=172.19.130.129
    ```

2. Verifique se os redirecionamentos foram configurados corretamente:
    ```bash
    netsh interface portproxy show all
    ```

## Instalar Dependências no Debian

1. Atualize o sistema:
    ```bash
    sudo apt update 
    sudo apt upgrade
    ```

2. Instale o UFW para gerenciar as portas (pode ser substituído pelo iptables):
    ```bash
    sudo apt install ufw
    sudo ufw allow 80/tcp
    sudo ufw allow 25/tcp
    ```
Caso esteja acessando via SSH sujiro o comando 
    ```bash
    sudo ufw allow ssh
    sudo ufw allow 22/tcp
    ```
Depois disso `habilite` e `verifique` o UFW
    ```bash
    sudo ufw enable
    sudo ufw status
    ```

3. Instale o Apache2:
    ```bash
    sudo apt install apache2
    ```

4. Instale o PHP:
    ```bash
    sudo apt install php
    ```

5. Instale as dependências do Apache para funcionar com o PHP:
    ```bash
    sudo apt install php-common libapache2-mod-php php-cli
    ```

6. Inicie o Apache2:
    ```bash
    sudo service apache2 start
    ```

## Verificar Instalação do PHP

1. Crie um arquivo `info.php` para verificar se o Apache2 e o PHP estão funcionando corretamente:
    ```bash
    sudo nano /var/www/html/info.php
    ```

2. Escreva o seguinte conteúdo no arquivo:
    ```php
    <?php
    phpinfo();
    ?>
    ```

3. Acesse o IP da máquina ou `localhost` no navegador com `/info.php` para verificar (use ip addr na maquina se precisar):
    ```url
    http://192.168.1.1/info.php
    ou
    http://localhost/info.php
    ```

## Configurar Scripts PHP

1. Crie os arquivos `verifyemail.php` e `sendemail.php` com os comandos:
     ```bash
    cd /var/www/html/
    ```
    ```bash
    wget https://raw.githubusercontent.com/Deernose/Email-unlimited/main/Web%20Script/verifyemail.php
    wget https://raw.githubusercontent.com/Deernose/Email-unlimited/main/Web%20Script/sendemail.php
    ```

## Teste de Portas

1. Faça um teste de portas online para garantir que a porta 80 está aberta. Caso ainda esteja fechada, será necessário abrir as portas também pelo Firewall do Windows Defender.

## Segurança

1. Lembre-se de alterar a senha no script para maior segurança!

## Observação

- Os programas precisam que você escreva `http://` para funcionar. Escrever somente o IP, domínio ou subdomínio resultará em mau funcionamento.

# Suporte
Além de reportar sugestoes e bugs aqui no GitHub 
você pode entrar no meu Discord e tirar suas dúvidas agora mesmo!

[![Discord support](https://discordapp.com/api/guilds/789283433955852289/widget.png?style=banner2)](https://discord.gg/kWdJFzf4rj)
