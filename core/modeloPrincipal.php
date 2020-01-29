<?php
    if($peticionAjax){
        require_once "../core/configAPP.php";
    }else{
        require_once "./core/configAPP.php";

    }
    //Configuracion de la base de datos (corazon de la aplicacion)
    class modeloPrincipal{

        protected function conectar(){
            $enlace=new PDO(SGBD,USUARIO,CLAVE);
            return $enlace;
        }

        protected function  ejecutar_consulta_simple($consulta){
            $respuesta=self::conectar()->prepare($consulta);
            $respuesta->execute();
            return $respuesta;
        } 

        protected function agregar_cuenta($datos){
            $sql= self::conectar()->prepare("INSERT INTO cuenta(CuentaCodigo,CuentaPrivilegio,
            CuentaUsuario,CuentaClave,CuentaEmail,CuentaEstado,CuentaTipo,CuentaGenero,CuentaFoto) 
            VALUES(:Codigo,:Privilegio,:Usuario,:Clave,:Email,:Estado,:Tipo,:Genero,:Foto)");
            $sql->bindParam(":Codigo",$datos['Codigo']);
            $sql->bindParam(":Privilegio",$datos['Privilegio']);
            $sql->bindParam(":Usuario",$datos['Usuario']);
            $sql->bindParam(":Clave",$datos['Clave']);
            $sql->bindParam(":Email",$datos['Email']);
            $sql->bindParam(":Estado",$datos['Estado']);
            $sql->bindParam(":Tipo",$datos['Tipo']);
            $sql->bindParam(":Genero",$datos['Genero']);
            $sql->bindParam(":Foto",$datos['Foto']);
            $sql->execute();
            return $sql;
        }

        protected function eliminar_cuenta($codigo){
            $sql=self::conectar()->prepare("DELETE FROM cuenta WHERE
            CuentaCodigo=:Codigo");
            $sql->bindParam(":Codigo",$codigo);
            $sql->execute();
            return $sql;
        }

        //-------------Acceso al sistema---------------
        //La funcion guarda el inicio y fin de sesion
        protected function guardar_bitacora($datos){
            $sql=self::conectar()->prepare("INSERT INTO bitacora(BitacoraCodigo,
            BitacoraFecha,BitacoraHoraInicio,BitacoraHoraFinal,BitacoraTipo,BitacoraAnio,CuentaCodigo)
            VALUES (:Codigo,:Fecha,:HoraInicio,:HoraFinal,:Tipo,:Anio,:Cuenta)");
            $sql->bindParam(":Codigo",$datos['Codigo']);
            $sql->bindParam(":Fecha",$datos['Fecha']);
            $sql->bindParam(":HoraInicio",$datos['HoraInicio']);
            $sql->bindParam(":HoraFinal",$datos['HoraFinal']);
            $sql->bindParam(":Tipo",$datos['Tipo']);
            $sql->bindParam(":Anio",$datos['Anio']);
            $sql->bindParam(":Cuenta",$datos['Cuenta']);
            $sql->execute();
            return $sql;
        }

        //Funcion para Actualizar la bitacora
        protected function actualizar_bitacora($codigo,$hora){
            $sql=self::conectar()->prepare("UPDATE bitacora SET
            BitacoraHoraFinal=:Hora WHERE BitacoraCodigo=:Codigo");
            $sql->bindParam(":Hora",$hora);
            $sql->bindParam(":Codigo",$codigo);
            $sql->execute();
            return $sql;
        }

        //Funcion para eliminar la bitacora
        protected function eliminar_bitacora($codigo){
            $sql=self::conectar()->prepare("DELETE FROM bitacora 
            WHERE CuentaCodigo=:Codigo");
            $sql->bindParam(":Codigo",$codigo);
            $sql->execute();
            return $sql;

        }
        //Encriptar y Desencriptar las contraseñas
        public function encriptacion($string){
            $output=FALSE;
            $key=hash('sha256', SECRET_KEY);
            $iv=substr(hash('sha256', SECRET_IV),0,16);
            $output=openssl_encrypt($string, METHOD, $key,0,$iv);
            $output=base64_encode($output);
            return $output;
        }

        protected function desencriptacion($string){
            $key=hash('sha256', SECRET_KEY);
            $iv=Substr(hash('sha256', SECRET_IV),0,16);
            $output=openssl_decrypt(base64_decode($string), METHOD, $key,0,$iv);
            return $output;
        }

        protected function generar_codigo_aleatorio($letra,$longitud,$num){
            for($i=1; $i<=$longitud; $i++){
                $numero = rand(0,9);
                $letra.= $numero;
            }
            return $letra.$num;
        }
//Limpiar cadenas de textos que vienen en los formularios
        protected function limpiar_cadena($cadena){
            $cadena=trim($cadena);
            $cadena=stripcslashes($cadena);
            $cadena=str_ireplace("<script>","",$cadena);
            $cadena=str_ireplace("</script src","",$cadena);
            $cadena=str_ireplace("<script type=","",$cadena);
            $cadena=str_ireplace("SELECT * FROM","",$cadena);
            $cadena=str_ireplace("DELETE FROM","",$cadena);
            $cadena=str_ireplace("INSERT INTO","",$cadena);
            $cadena=str_ireplace("--","",$cadena);
            $cadena=str_ireplace("^","",$cadena);
            $cadena=str_ireplace("[","",$cadena);
            $cadena=str_ireplace("]","",$cadena);
            $cadena=str_ireplace("==","",$cadena);
            $cadena=str_ireplace(";","",$cadena);
            return $cadena;
        }

//Manejo de mensajes del sistema
        protected function sweet_alert($datos){
            if($datos['Alerta']=="simple"){
                //titulo de mensaje
                //mensaje
                //tipo
                $alerta=" 
                <script>
                    swal(
                        '".$datos['Titulo']."'  , 
                        '".$datos['Texto']."'   , 
                        '".$datos['Tipo']."'
                    );
                </script>
                ";
            }elseif (($datos['Alerta']=="recargar")){
                echo '<p class="alert alert-success agileits" role="alert">ENTRAAA MENSAJE DE ERROR333----!p>';
                $alerta=" 
                <script>
                    swal(
                        {
                        title: '".$datos['Titulo']."'  ,
                        text: '".$datos['Texto']."'    , 
                        type:  '".$datos['Tipo']."'   
                         }
                         );
                </script>
                ";
            }elseif (($datos['Alerta']=="limpiar")){
                echo '<p class="alert alert-success agileits" role="alert"> REGISTROOOO BUENAA !p>';
                $alerta=" 
                <script>
                    swal(
                    {
                        title: '".$datos['Titulo']."'   ,
                        text: '".$datos['Texto']."'     , 
                        type:  '".$datos['Tipo']."'   
                    });
                </script>
                ";

            }
            return $alerta;
        }
    }
?>