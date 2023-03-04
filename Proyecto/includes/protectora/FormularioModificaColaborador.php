<?php
/*nose si esta bien */
namespace es\ucm\fdi\aw\protectora;

use es\ucm\fdi\aw\Formulario;
/* */

class FormularioModificaColaborador extends Formulario
{
    private $idProtectora;
    private $idUsuario;

    //Pasar el url de colaboradores con el parametro id protectora.
    public function __construct($idProtectora, $idUsuario) {
        parent::__construct('formModificaColaborador', ['urlRedireccion' => "colaboradores.php?id=$idProtectora"]);
        $this->idProtectora = $idProtectora;
        $this->idUsuario = $idUsuario;
    }

    protected function generaCamposFormulario(&$datos)
    {
        
        $colaborador = Colabora::buscaColaborador($this->idUsuario, $this->idProtectora);
        $permisos = $colaborador->getRol();
        $auxiliar1 = ' ';
        $rol = $datos['rol'] ?? '';

        $colabora = Colabora::COLABORA;
        $pendiente = Colabora::PENDIENTE;
        $creador = Colabora::CREADOR;

        //Mirar lo del checkbox.
        if($permisos == Colabora::PENDIENTE){
            $auxiliar1 = <<<EOF
            <select name="rol" id="rol" class="selectbox">
            <option selected='selected' value=$pendiente>Pendiente</option>
            <option value=$colabora >Colaborador</option>
            <option value=$creador>Creador</option>
            </select>
            EOF;
        }
        if($permisos == Colabora::CREADOR){
            $auxiliar1 = <<<EOF
            <select name="rol" id="rol" class="selectbox">
            <option  value=$pendiente >Pendiente</option>
            <option  value=$colabora >Colaborador</option>
            <option selected='selected' value=$creador >Creador</option>
            </select>
            EOF;
        }
        if($permisos == Colabora::COLABORA){
            $auxiliar1 = <<<EOF
            <select name="rol" id="rol" class="selectbox">
            <option value=$pendiente>Pendiente</option>
            <option selected='selected'   value=$colabora >Colaborador</option>
            <option value=$creador>Creador</option>
            </select>
            EOF;
        }
    
        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['rol'], $this->errores, 'span', array('class' => 'error'));

        $html = <<<EOF
        $htmlErroresGlobales
        
            <div id="campoSelectboxColaborador">
                <label for="rol">Permiso</label>
                $auxiliar1
            </div>
            <div>
                <button type="submit" name="registro">Modificar Permiso</button>
            </div>
            <div>
                {$erroresCampos['rol']}
            </div>
       
        EOF;

        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $rol=trim($datos['rol'] ?? '');
        $this->errores = [];

        if (count($this->errores) === 0) {
            $colaborador = Colabora::buscaColaborador($this->idUsuario, $this->idProtectora);
            $colaborador->modifica($rol);
        }
    }
}

?>