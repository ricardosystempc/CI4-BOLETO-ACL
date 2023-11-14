<?php

namespace App\Controllers;


use App\Controllers\BaseController;
use App\Entities\Usuario;

class Usuarios extends BaseController
{

    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os usuários do sistema',
        ];

        return view('Usuarios/index', $data);
    }

    // como estou fazendo uma requisição via ajax do dataTable eu tenho que fazer uma validação, pois só funciona se for via ajax.
    public function recuperaUsuarios(){

        if(!$this->request->isAJAX()){

            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'email',
            'ativo',
            'imagem',
        ];

      $usuarios = $this->usuarioModel->select($atributos)
                                     ->orderBy('id', 'DESC')
                                     ->findAll();
      
      // receberá o array de objetos de usuários                               
      $data = [];

      foreach($usuarios as $usuario){


            $data[] = [
                'imagem' => $usuario->imagem,
                'nome' =>  anchor("usuarios/exibir/$usuario->id", esc($usuario->nome), 'title="Exibir usuário '.esc($usuario->nome). '."'),
                'email' => esc($usuario->email),
                'ativo' => ($usuario->ativo == true ? '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo' : '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo'),
            ];

      }

      $retorno = [
            'data' => $data, //  o plugin do dataTable pede retorno $data
      ];

      //echo '<pre>';
      //print_r($retorno);
      //exit;

      return $this->response->setJSON($retorno);

    }

    public function criar()
    {
        $usuario = new Usuario(); // new Usuario(); o Objeto retorna atravez de uma entidade do entities - tenho que colocar use App\Entities\Usuario;
        
         //dd($usuario);

        $data = [
            
            'titulo' => "Criando novo usuário",
            'usuario' => $usuario,
        ];

        //dd($usuario);
        return view('Usuarios/criar', $data);
    }

    public function cadastrar(){ // Serve para cadastrar usuário

        if(!$this->request->isAJAX()){

            return redirect()->back();
        }


        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();


        // Recupero o post da requisição
        $post = $this->request->getPost();

        
        // Crio novo objeto da Entidade usuário
        $usuario = new Usuario($post);
        
        // No UsuárioModel não tem o campo Atido de usuário e nem Admin, pois o Sistema que estou fazendo é com ACL
        // Na linha abaixo eu tenho que desativar a proteção para ele verificar e salvar as informações quando eu editar um usuário.
        if($this->usuarioModel->protect(false)->save($usuario)){

            $btnCriar = anchor("usuarios/criar", 'Cadastrar novo usuário', ['class' => 'btn btn-danger mt-2'] );
            
            session()->setFlashdata('sucesso', "Dados salvos com sucesso!<br> $btnCriar"); 

            // Tem que retornar ID do usuário pq no formulário criar não retorna id.
            // Retornamos o último ID inserido na tabela de Usuário. Ou seja o usuário recem criado.
            $retorno['id'] = $this->usuarioModel->getInsertID(); 

            return $this->response->setjSON($retorno);

        }

        // Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
        $retorno['erros_model'] = $this->usuarioModel->errors();


        // Retorno para ajax request
        return $this->response->setjSON($retorno);
    }


    
    public function exibir(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        //dd($usuario);

        $data = [
            
            'titulo' => "Detalhando o usuário".esc($usuario->nome),
            'usuario' => $usuario,
        ];

        //dd($usuario);
        return view('Usuarios/exibir', $data);
    }
    public function editar(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        //dd($usuario);

        $data = [
            
            'titulo' => "Editando o usuário".esc($usuario->nome),
            'usuario' => $usuario,
        ];

        //dd($usuario);
        return view('Usuarios/editar', $data);
    }

    public function atualizar(){ // Serve para atualizar os dados do usuário

        if(!$this->request->isAJAX()){

            return redirect()->back();
        }


        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();


        // Recupero o post da requisição
        $post = $this->request->getPost();

        
        // Validamos a existência do usuário
        $usuario = $this->buscaUsuarioOu404($post['id']);

        
        // Verifico se está vindo do post a senha
        // Se está fazio o post password ele vai lê as 2 linhas de baixo removendo do campo passoword e password_confirmation
        if(empty($post['password'])){
            
            unset($post['password']);
            unset($post['password_confirmation']);
    
            }


        // Preenchemos os atributos do usuário com os valores do POST
        $usuario->fill($post);

        // Verificar se houve alguma alteração no objeto
        if($usuario->hasChanged() == false){

            $retorno['info'] = 'Não ha dados para serem atualizados';
            return $this->response->setjSON($retorno);
        }
        
        // No UsuárioModel não tem o campo Atido de usuário e nem Admin, pois o Sistema que estou fazendo é com ACL
        // Na linha abaixo eu tenho que desativar a proteção para ele verificar e salvar as informações quando eu editar um usuário.
        if($this->usuarioModel->protect(false)->save($usuario)){

            
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setjSON($retorno);

        }

        // Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
        $retorno['erros_model'] = $this->usuarioModel->errors();


        // Retorno para ajax request
        return $this->response->setjSON($retorno);
    }

    public function editarImagem(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        //dd($usuario);

        $data = [
            
            'titulo' => "Alterando a imagem do usuário".esc($usuario->nome),
            'usuario' => $usuario,
        ];

        //dd($usuario);
        return view('Usuarios/editar_imagem', $data);
    }

    public function upload() // Serve para fazer upload da imagem do usuário
    { 

        if(!$this->request->isAJAX()){

            return redirect()->back();
        }


        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        // Validação de upload de imagem do usuário - documentação do codeIgniter 4
        $validacao = service('validation');

        $regras = [
                'imagem' => 'uploaded[imagem]|max_size[imagem,1048]|ext_in[imagem,png,jpg,jpeg,webp]',          
        ];

        $mensagens =  [   // Errors
            'imagem' =>[
                'uploaded' => 'Escolha uma imagem',
                'ext_in' => 'Só aceitamos imagens com formato PNG, JPG, JPEG ou WEBP',
                'max_size' => 'Só aceitamos imagens com tamanho máximo 1024x1024',
            ],        

        ];

        $validacao->setRules($regras, $mensagens);

        if($validacao->withRequest($this->request)->run() == false){

            $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
            $retorno['erros_model'] = $validacao->getErrors();
    
    
            // Retorno para ajax request
            return $this->response->setjSON($retorno);
        }


        // Recupero o post da requisição
        $post = $this->request->getPost();

        
        // Validamos a existência do usuário
        $usuario = $this->buscaUsuarioOu404($post['id']);


        // Recuperamos a imagem que veio no post - esse procedimento é o certo recomendado na documentação do CI4
        $imagem = $this->request->getFile('imagem');

        list($largura, $altura) = getimagesize($imagem->getPathName());

        if($largura < '300' || $altura < '300'){

            $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
            $retorno['erros_model'] = ['dimensao' => 'A imagem não pode ser menor que 300x300 pixels'];
    
    
            // Retorno para ajax request
            return $this->response->setjSON($retorno);
        }

        $caminhoImagem = $imagem->store('usuarios');

        $caminhoImagem = WRITEPATH . "uploads/$caminhoImagem";

        // Podemos manipular a imagem que está salva no diretório

       // Redimensionamos a imagem para 300 x 300 e para ficar no centro  puxando método manipulaImagem 
       $this->manipulaImagem($caminhoImagem, $usuario->id);


        // A partir daqui podemos atualizar a tabela de usuários no caso foto do usuário do banco

        // Recupero a possível imagem antiga
        $imagemAntiga = $usuario->imagem;

        // $usuario->imagem Armazenamos no atributo imagem - $imagem->getName() pegamos o atributo e nome da imagem
        $usuario->imagem = $imagem->getName();

        $this->usuarioModel->save($usuario);

        // Verificando a imagem antiga se for diferente de nulo ai chama a função para remover a imagem antiga.        
        if($imagemAntiga != null){

            $this->removeImagemDoFileSystem($imagemAntiga);

        }
        
        
        session()->setFlashdata('sucesso', 'Imagem Salva com sucesso!');


        // Retorno para ajax request - Em editar_imagem.php tem o Javascript que quando retorna com essa linha abaixo ele manda comando para window.location.href e ai redireciona para usuarios/exibir
        return $this->response->setjSON($retorno);
    }

    public function imagem(string $imagem = null)
    {
        if($imagem != null) {
            $this->exibeArquivo('usuarios', $imagem);

        }
    }


    /**
     * Método privado que recupera o usuário
     * 
     * #param integer $id
     * @return Exceptions|object
     */
    private function buscaUsuarioOu404(int $id = null)
    {
        
        // Se eu não consegui encontrar o usuário eu jogo uma mensagem de erro
        if(!$id || !$usuario = $this->usuarioModel->withDeleted(true)->find($id)){
          
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");
    
    }

    return $usuario;
   
    }

    private function manipulaImagem(string $caminhoImagem, int $usuario_id){

          // Redimensionamos a imagem para 300 x 300 e para ficar no centro
          service('image')
          ->withFile($caminhoImagem)
          ->fit(300, 300, 'center')
          ->save($caminhoImagem);

      $anoAtual = date('Y');
      // Adicionar uma marca d'água de texto
      \Config\Services::image('imagick')
          ->withFile($caminhoImagem)
          ->text("Ordem $anoAtual - User-ID $usuario_id", [
              'color'         => '#fff',
              'opacity'       => 0.5,
              'withShadow'    => false,
              'hAlign'        => 'center',
              'vAlign'        => 'bottom',
              'fontSize'      => 10

          ])
          ->save($caminhoImagem);
    }
    
    // Método para remover imagem antiga do usuário no diretório.
    private function removeImagemDoFileSystem(string $imagem){

        $caminhoImagem = WRITEPATH . "uploads/usuarios/$imagem";

        if(is_file($caminhoImagem))
        {
            unlink($caminhoImagem);

        }

    }

}
