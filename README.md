# 🎓 Sistema de Emissão de Certificados — POO (PHP + MySQL)

Sistema completo para gerenciamento de cursos, alunos e **emissão automática de certificados** com cálculo inteligente de carga horária, distribuição por dias e registro das emissões.  
Desenvolvido em **PHP Orientado a Objetos**, **MySQL**, **HTML/CSS/JS** e **GD** para geração de imagens.

---

## 🚀 Funcionalidades Principais

- Cadastro de cursos com datas e horários.
- Cadastro de alunos.
- Seleção de cursos para gerar certificação.
- **Cálculo automático da carga horária**, seguindo regras:
  - Máximo de **10h por dia**.
  - Distribuição entre cursos quando datas coincidem.
  - Respeita ordem de seleção do usuário.
  - Impede emissão com menos de 2h.
- Emissão de certificado com:
  - Nome do aluno
  - Nome do curso
  - Carga horária final
  - Intervalo de realização (somente datas)
  - Serial/código único
- Certificado gerado como **imagem (JPG)** usando GD + TTF.
- Tela de visualização e botão de download.
- Relatório de certificados emitidos.

---

## 🛠️ Tecnologias Utilizadas

- **PHP 8+**
- **MySQL**
- **HTML5, CSS3 e JavaScript**
- **Biblioteca GD (PHP)**
- **Arquitetura MVC simples**
- **Composer (opcional)**

---

## 📂 Estrutura do Projeto

├── app/  
│   ├── Ajuda.php  
│   ├── CalculoCertificado.php  
│   ├── Sessao.php  
│   ├── Validador.php  
│  
├── arquivos/  
│   ├── css/  
│   │   ├── style.css  
│   ├── img/  
│   ├── js/  
│       ├── script.js  
│   ├── fonts  
│       ├── arial.ttf  
│  
├── config/  
│   ├── database.php  
│   ├── env.php  
│  
├── controles/  
│   ├── CertificadoControle.php  
│   ├── CursoControle.php  
│   ├── HomeControle.php  
│   ├── UsuarioControle.php  
│  
├── core/  
│   ├── Autoload.php  
│   ├── Controle.php  
│   ├── Database.php  
│   ├── Model.php  
│   ├── Roteador.php  
│  
├── modelos/  
│   ├── Certificado.php  
│   ├── Curso.php  
│   ├── Usuario.php  
│  
├── public/  
│   ├── .htaccess  
│   ├── index.php  
│  
├── rotas/  
│   ├── web.php  
│  
├── testes/  
│   ├── TesteCalculoTempo.php  
│   ├── TesteCertificados.php  
│   ├── TesteCursos.php  
│  
├── views/  
│   ├── certificados/  
│   │   ├── lista.php  
│   │   ├── certificado_png.php  
│   ├── componentes/  
│   │   ├── alertas.php  
│   │   ├── cabecalho.php  
│   │   ├── menu.php  
│   │   ├── rodape.php  
│   ├── cursos/  
│   │   ├── lista.php  
│   ├── emitir/  
│   │   ├── formulario.php  
│   ├── home/  
│   │   ├── index.php  
│   ├── layouts/  
│       ├── principal.php  

## 🧮 Regras de Cálculo da Carga Horária

1. O banco armazena data e hora completas.  
2. Cada dia possui limite de **10 horas**.  
3. Se dois cursos ocorrerem no mesmo dia:  
   - o sistema divide as horas conforme ordem de seleção.  
4. Horas já utilizadas em certificados anteriores são descontadas.  
5. Se o cálculo gerar menos que **2 horas**, a emissão é recusada.  
  
O arquivo PHP utiliza GD:  
  
- `imagettftext()` para escrever nome, curso, horas e datas  
- Fonte TTF personalizada  
- Texto branco ou preto configurável  
- Tamanho e posição ajustados manualmente  
- Exportação em JPG  
  
## 📸 Prints das Telas

_(Cole suas imagens aqui depois de subir para o GitHub)_

### ✔️ Tela — Página Inicial
**Local para imagem:**

![print-pagina-inicial](assets/prints/pagina-inicial.png)

---

### ✔️ Tela — Seleção de Cursos
![print-selecao-cursos](assets/prints/selecao-cursos.png)

---

### ✔️ Tela — Visualizar Certificado
![print-visualizar](assets/prints/visualizar-certificado.png)

---

### ✔️ Certificado Final (exemplo)
![print-certificado](assets/prints/certificado-final.png)

---

## 📦 Como Instalar

1. Copiar o projeto para `htdocs` do XAMPP:  
2. Criar o banco no phpMyAdmin e importar o arquivo `.sql`:  
- Tabelas: alunos, cursos, certificados, etc.  
3. Configurar o arquivo `conexao.php`:  
```php
$host = 'localhost';  
$dbname = 'certificados';  
$user = 'root';  
$pass = '';
```

## Abra no navegador
http://localhost/certificados-poo/

##📜 Licença

Este projeto está sob a licença MIT — livre para uso, cópia e modificação.

##👨‍💻 Autor

Willy Borges
Projeto desenvolvido para estudos e uso prático com PHP + POO.


