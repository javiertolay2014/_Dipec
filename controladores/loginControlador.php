<?php
if($peticionAjax){
    require_once "../modelos/loginModelo.php";
    require_once "../core/modeloPrincipal.php";
}else{
    require_once "./modelos/loginModelo.php";
    require_once "./core/modeloPrincipal.php";
}

class loginControlador extends loginModelo{
    public function iniciar_sesion_controlador(){  
        $usuario=modeloPrincipal::limpiar_cadena($_POST['usuario']);
        $clave=modeloPrincipal::limpiar_cadena($_POST['clave']);

        $clave=modeloPrincipal::encriptacion($clave);

        $datosLogin=[
            "Usuario"=>$usuario,
            "Clave"=>$clave
        ];
        
        $datosCuenta=loginModelo::iniciar_sesion_modelo($datosLogin);

        if($datosCuenta->rowCount()==1){
            $row=$datosCuenta->fetch();

            $fechaActual= date("Y-m-a");
            $yearActual=date("Y");
            $horaActual=date("h:i:s a");

            $consulta1=modeloPrincipal::ejecutar_consulta_simple("SELECT id FROM bitacora");

            $numero=($consulta1->rowCount())+1;

            $codigoB=modeloPrincipal::generar_codigo_aleatorio("CB",4,$numero);

            $datosBitacora=[
                "Codigo"=>$codigoB,
                "Fecha"=>$fechaActual,
                "HoraInicio"=>$horaActual,
                "HoraFinal"=>"Sin Registro",
                "Tipo"=>$row['CuentaTipo'],
                "Anio"=>$yearActual,
                "Cuenta"=>$row['CuentaCodigo']
            ];

            $insertarBitacora=modeloPrincipal::guardar_bitacora($datosBitacora);

            if($insertarBitacora->rowCount()>=1){
                session_start(['name'=>'DIPEC']);
                $_SESSION['usuario_dipec']=$row['CuentaUsuario'];
                $_SESSION['tipo_dipec']=$row['CuentaTipo'];
                $_SESSION['privilegio_dipec']=$row['CuentaPrivilegio'];
                $_SESSION['foto_dipec']=$row['CuentaFoto'];
                $_SESSION['token_dipec']=md5(uniqid(mt_rand(),true));
                $_SESSION['codigo_cuenta_dipec']=$row['CuentaCodigo'];
                $_SESSION['codigo_bitacora_dipec']=$codigoB;

                //Segun el tipo de usuario donde ingresa
                if($row['CuentaTipo']=="Administrador"){
                    $url=SERVERURL."home/";
                }else{
                    $url=SERVERURL."catalog/";
                }
                return $urlLocation='<script> window.location="'.$url.'"</script>';

            }else{
                echo "no se puede inciar sesion";
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrio un error inesperado",
                    "Texto"=>"No hemos podido inciar la sesión por problemas técnicos, por favor intente nuevamente",
                    "Tipo"=>"error"
                ];
                return modeloPrincipal::sweet_alert($alerta);
            }

        }else{
            echo "DATOS CUENTA No encontrado";
            $alerta=[
                "Alerta"=>"simple",
                "Titulo"=>"Ocurrio un error inesperado",
                "Texto"=>"El nombre de usuario y contraseña no son correctos o su cuenta puede estar deshabilitada",
                "Tipo"=>"error"
            ];
            return modeloPrincipal::sweet_alert($alerta);
        }
    }
    
    //Funcion para forzar cierre de sesion
    public function forzar_cierre_sesion_controlador(){
        session_destroy();
        return header("Location: ".SERVERURL."login/");
    } 
}

?>