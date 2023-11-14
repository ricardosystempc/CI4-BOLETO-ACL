
<!--- Na sessão existe um item chamado sucesso, se tiver joga o aviso que peguei do bootstraps na tela  -->
<?php if(session()->has('sucesso')): ?>

    <div class="alert alert-success alert-dismissible fade show" role="alert">
  <!--setFlashdata está no controle Usuarios dizendo Dizendo:  session()->setFlashdata('sucesso', 'Dados salvos com sucesso!'); -->
  <strong>Tudo certo!</strong><?php echo session('sucesso'); ?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<?php endif; ?>

<!--- Na sessão existe um item chamado Info, se tiver joga o aviso que peguei do bootstraps na tela  -->
<?php if(session()->has('info')): ?>

    <div class="alert alert-success alert-dismissible fade show" role="alert">
  <!--setFlashdata está no controle Usuarios dizendo Dizendo:  session()->setFlashdata('sucesso', 'Dados salvos com sucesso!'); -->
  <strong>Informação!</strong><?php echo session('info'); ?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<?php endif; ?>

<!--- Na sessão existe um item chamado atencao, se tiver joga o aviso que peguei do bootstraps na tela  -->
<?php if(session()->has('atencao')): ?>

    <div class="alert alert-success alert-dismissible fade show" role="alert">
  <!--setFlashdata está no controle Usuarios dizendo Dizendo:  session()->setFlashdata('sucesso', 'Dados salvos com sucesso!'); -->
  <strong>Atenção!</strong><?php echo session('atencao'); ?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<?php endif; ?>

<!--- Na sessão existe um item chamado erros_model, se tiver joga o aviso que peguei do bootstraps na tela  -->
<!--- Isso aqui é para utilizar só quando fizermos um post sem ajax request -->
<?php if(session()->has('erros_model')): ?>

   <ul>

      <?php foreach($erros_model as $erro): ?>

        <li class="text-danger"><?php echo $erro; ?></li>

      <?php endforeach; ?>  

   </ul>

<?php endif; ?>


<!--- Utilizamos quando o formulário é interceptado, por erro no backend ou quando estamos fazendo um debug 
para ver o que está vindo do POST -->
<?php if(session()->has('error')): ?>

<div class="alert alert-danger alert-dismissible fade show" role="alert">
<!--setFlashdata está no controle Usuarios dizendo Dizendo:  session()->setFlashdata('sucesso', 'Dados salvos com sucesso!'); -->
<strong>Error!</strong><?php echo session('error'); ?>
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>

<?php endif; ?>