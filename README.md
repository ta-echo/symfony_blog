# symfony_blog

symfony 5.3 + PHP 8
-----------------------------------------------------------

// /!\ Error de parameter upload_dir
//Rajouter ça dans services.yaml (ligne7) < config

parameters:
    upload_dir: '%kernel.project_dir%/public/assets/images/'
