<?php $this->extend('Layout/principal'); ?>

<?php $this->section('titulo'); ?> 

<?php echo $titulo; ?>

<?php $this->endSection(); ?>


<?php echo $this->section('estilos'); ?> 



<?php $this->endSection(); ?>


<?php echo $this->section('conteudo'); ?> 

<h1>Estendendo layout principal</h1>

<?php $this->endSection(); ?>


<?php echo $this->section('scripts'); ?> 



<?php $this->endSection(); ?>






