<?php $this->extend('Layout/principal'); ?>

<?php $this->section('titulo'); ?>

<?php echo $titulo; ?>

<?php $this->endSection(); ?>


<?php echo $this->section('estilos'); ?>



<?php $this->endSection(); ?>


<?php echo $this->section('conteudo'); ?>

<div class="row">

    <div class="col-lg-6">


        <div class="block">

            <div class="block-body">
                <!-- Exibirá os retornos do backend  -->
                <div id="response">

                </div>
                         <!-- Para o form_open do CI4 funcionar eu tenho que colocar o local, os atributos e o campo hidden  -->
                <?php echo form_open('/', ['id' => 'form'], ['id' => "$usuario->id"]) ?>

                <?php echo $this->include('Usuarios/_form'); ?> <!-- Incluir o aquivo na página, no caso o arquivo _form que está dentro da views/Usuarios/_form -->

                

                <div class="form-group mt-5 mb-4">

                    <input id="btn-salvar" type="submit" value="Salvar" class="btn btn-danger btn-sm mr2">

                    <a href="<?php echo site_url("usuarios/exibir/$usuario->id") ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>

                </div>

                <?php form_close(); ?>

            </div>



        </div> <!-- ./ block termina aqui -->




    </div>

</div>

<?php $this->endSection(); ?>


<?php echo $this->section('scripts'); ?>

<!-- Aqui que começa o Javascript para editar o formulário  -->
<!-- Aqui que começa o Javascript com Ajax o formulário através do ID  -->
<script>

$(document).ready(function(){

    $("#form").on('submit', function(e){

        e.preventDefault();

        $.ajax({

            type: 'POST',
            url: '<?php echo site_url('usuarios/atualizar'); ?>',
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function(){

                $("#response").html('');
                $("#btn-salvar").val('Por favor aguarde...');

            },
            success: function(response){

                $("#btn-salvar").val('Salvar');
                $("#btn-salvar").removeAttr("disabled");

                $('[name=csrf_ordem]').val(response.token);

                if(!response.erro){
 

                    if(response.info){

                        $("#response").html('<div class="alert alert-info">'+ response.info + '</div>');

                    }else{

                        // Tudo certo com a atualização do usuário
                        // Podemos agora redirecioná-lo tranquilamente

                        window.location.href = "<?php echo site_url("usuarios/exibir/$usuario->id"); ?>";

                    }


                } 
                
                if(response.erro){

                    // Existem erros de validação

                    $("#response").html('<div class="alert alert-danger">'+ response.erro + '</div>');

                    // Eu tenho que verificar se eu tenho vários erros de validação.
                    // response.erros_model no caso o erros_model vai ser tudo de erro que tiver em erros_model
                    if(response.erros_model){

                        // Percorrendo erro via javascript com Jquery
                        $.each(response.erros_model, function(key, value){

                            $("#response").append('<ul class="list-unstyled"><li class="text-danger">'+ value +'</li></ul>');

                        });

                        
                    }


                }
                    
            },
            error: function(){

                alert('Não foi possível processar a solicitação.Entre em contato com Suporte Técnico. ');
                $("#btn-salvar").val('Salvar');
                $("#btn-salvar").removeAttr("disabled");

            }


        });

    });

    // Desabilitando o dublo click
    $("#form").submit(function () {

        $(this).find(":submit").attr('disabled', 'disabled');

    });

});


</script>
<!-- Aqui termina o Javascript para editar o formulário  -->

<?php $this->endSection(); ?>