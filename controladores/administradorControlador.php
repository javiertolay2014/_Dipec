<?php
    if($peticionAjax){
        require_once "../modelos/administradorModelo.php";
        require_once "../core/modeloPrincipal.php";
    }else{
        require_once "./modelos/administradorModelo.php";
        require_once "../core/modeloPrincipal.php";
    }

    class administradorControlador extends  administradorModelo{
        public function agregar_administrador_controlador(){
            $dni=modeloPrincipal::limpiar_cadena($_POST['dni-reg']);
            $nombre=modeloPrincipal::limpiar_cadena($_POST['nombre-reg']);
            $apellido=modeloPrincipal::limpiar_cadena($_POST['apellido-reg']);
            $telefono=modeloPrincipal::limpiar_cadena($_POST['telefono-reg']);
            $direccion=modeloPrincipal::limpiar_cadena($_POST['direccion-reg']);

            $usuario=modeloPrincipal::limpiar_cadena($_POST['usuario-reg']);
            $password1=modeloPrincipal::limpiar_cadena($_POST['password1-reg']);
            $password2=modeloPrincipal::limpiar_cadena($_POST['password2-reg']);
            $email=modeloPrincipal::limpiar_cadena($_POST['email-reg']);
            $genero=modeloPrincipal::limpiar_cadena($_POST['optionsGenero']);
            $privilegio=modeloPrincipal::limpiar_cadena($_POST['optionsPrivilegio']);
            
            if($genero=="Masculino"){
                $foto="MaleAvatar.png";
            }else{
                $foto="FemaleAvatar.png";
            }

            if($password1!=$password2){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrio un error inesperado",
                    "Texto"=> "Las contraseñas que acabas de ingresar NO coinciden, por favor intente nuevamente",
                    "Tipo"=>"error"
                ];
            }else{
                $consulta1=modeloPrincipal::ejecutar_consulta_simple("SELECT AdminDNI 
                FROM admin WHERE AdminDNI='$dni'");
                if($consulta1->rowCount()>=1){
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrio un error inesperado",
                        "Texto"=> "El DNI que acaba de ingresar ya se encuentra registrado en el sistema",
                        "Tipo"=>"error"
                    ];

                }else{
                    if($email!=""){
                        $consulta2=modeloPrincipal::ejecutar_consulta_simple("SELECT CuentaEmail 
                        FROM cuenta WHERE CuentaEmail='$email'");
                        $ec=$consulta2->rowCount();
                    }else{
                        $ec=0;
                    }
                    if($ec>=1){
                        $alerta=[
                            "Alerta"=>"simple",
                            "Titulo"=>"Ocurrio un error inesperado",
                            "Texto"=> "El EMAIL que acaba de ingresar ya se encuentra registrado en el sistema",
                            "Tipo"=>"error"
                        ];
                    }else{
                        $consulta3=modeloPrincipal::ejecutar_consulta_simple("SELECT CuentaUsuario
                        FROM cuenta WHERE CuentaUsuario='$usuario'");
                        if($consulta3->rowCount()>=1){
                            $alerta=[
                                "Alerta"=>"simple",
                                "Titulo"=>"Ocurrio un error inesperado",
                                "Texto"=> "El USUARIO que acaba de ingresar ya se encuentra registrado en el sistema",
                                "Tipo"=>"error"
                            ];
                        }else{
                            $consulta4=modeloPrincipal::ejecutar_consulta_simple("SELECT id  FROM cuenta");
                            $numero=($consulta4->rowCount())+1;
                            $codigo=modeloPrincipal::generar_codigo_aleatorio("DIPEC",3,$numero);
                            $clave=modeloPrincipal::encriptacion($password1);
                            $dataAC=[
                                "Codigo"=>$codigo,
                                "Privilegio"=>$privilegio,
                                "Usuario"=>$usuario,
                                "Clave"=>$clave,
                                "Email"=>$email,
                                "Estado"=>"Activo",
                                "Tipo"=>"Administrador",
                                "Genero"=>$genero,
                                "Foto"=>$foto
                            ];
                            
                            $guardarCuenta=modeloPrincipal::agregar_cuenta($dataAC);

                            if($guardarCuenta->rowCount()>=1){
                                $dataAD=[
                                    "DNI"=>$dni,
                                    "Nombre"=>$nombre,
                                    "Apellido"=>$apellido,
                                    "Telefono"=>$telefono,
                                    "Direccion"=>$direccion,
                                    "Codigo"=>$codigo
                                ];
                                $guardarAdmin=administradorModelo::agregar_administrador_modelo($dataAD);
                                
                                if($guardarAdmin->rowCount()>=1){
                                    $alerta=[
                                        "Alerta"=>"limpiar",
                                        "Titulo"=>"Administrador registrado",
                                        "Texto"=>"El administrador se registro con exito en sistema",
                                        "Tipo"=>"success"
                                    ];
                                }else{
                                    modeloPrincipal::eliminar_cuenta($codigo);
                                    $alerta=[
                                        "Alerta"=>"simple",
                                        "Titulo"=>"Ocurrio un error inesperado",
                                        "Texto"=>"No hemos podido registrar el administrador",
                                        "Tipo"=>"error"
                                    ];
                                }
                            }else{
                                $alerta=[
                                    "Alerta"=>"simple",
                                    "Titulo"=>"Ocurrio un error inesperado",
                                    "Texto"=> "No hemos podido registrar el administrador",
                                    "Tipo"=>"error"
                                ];
                            }
                        }
                    }
                }
            }
            return modeloPrincipal::sweet_alert($alerta);
        } 

    }