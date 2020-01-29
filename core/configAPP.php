<?php 
//Para pasar facilmente de un servisor local a un servidor remoto
    const SERVER="localhost"; //CONEXION A LA BASE DE DATOS
    const DB="legajos_dipec";
    const USUARIO="root";
    const CLAVE="";


    const SGBD="mysql:host=".SERVER.";dbname=".DB;


    const METHOD="AES-256-CBC";
    const SECRET_KEY='$Dipec@2020';
    const SECRET_IV='37635195';
