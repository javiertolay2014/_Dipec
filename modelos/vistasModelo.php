<?php

    class vistasModelo{
        protected function obtener_vistas_modelo($vistas){
            $listaBlanca= ["adminlist","adminsearch","admin","empleado","empleadoconfig","empleadoinfo",
                        "catalog","category","categorylist","client","clientlist","clientsearch","company",
                        "companylist","home","login","myaccoun","mydata","provider","providerlist","search"];
            if (in_array($vistas, $listaBlanca)){
                if(is_file("./vistas/contenidos/".$vistas."-view.php")){
                    $contenido="./vistas/contenidos/".$vistas."-view.php";
                }else{
                    $contenido="login";
                }
            }elseif($vistas=="login"){
                $contenido="login";
            }elseif($vistas=="index"){
                $contenido="login";
            }else{
                $contenido="404";
            }
            return $contenido;
        }
        
    }