<?php
if ($peticionAjax) {
    require_once "../core/modeloPrincipal.php";
} else {
    require_once "./core/modeloPrincipal.php";
}

class loginModelo extends modeloPrincipal
{
    protected function iniciar_sesion_modelo($datos)
    {
        $sql = modeloPrincipal::conectar()->prepare("SELECT * FROM cuenta
       WHERE CuentaUsuario=:Usuario AND CuentaClave=:Clave AND CuentaEstado='Activo'");
        $sql->bindParam(":Usuario", $datos['Usuario']);
        $sql->bindParam(":Clave", $datos['Clave']);
        $sql->execute();
        return $sql;
    }
}
