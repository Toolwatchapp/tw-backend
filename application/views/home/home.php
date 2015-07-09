<div class="home-intro">

    <?php $this->load->view('home/intro'); ?>

</div>

<div class="home-picto">
    <div id="demo-screen" class="container container-fluid">
        
        <?php 
            $this->load->view('home/demo'); 
            $this->load->view('home/slogan'); 
        ?>
         
    </div>
</div>

<?php $this->load->view('home/mosa'); ?>