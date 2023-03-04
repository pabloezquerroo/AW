<?php
/* nose si esta bien asi */
namespace es\ucm\fdi\aw\animales;

use es\ucm\fdi\aw\Formulario;
use es\ucm\fdi\aw\protectora\Protectora;
/* */

class FormularioFiltroAnimales extends Formulario
{


    private $tiposGenero=[
        0=>"Macho",
        1=>"Hembra",
        2 =>"SN",
    ];
    
    public function __construct() {
        parent::__construct('formFiltro', ['urlRedireccion' => null, 'method' => 'GET']);
    }
    
    protected function generaCamposTipo(&$datos){
        $html='';
        if(Animal::getTipos()){

            foreach(Animal::getTipos() as $tipoAnimal ){
                $checked="";
                if(isset($datos['tipo']))
                    $checked =in_array($tipoAnimal, $datos['tipo'])?"checked":"";
           
                $html.="<div><input type=\"checkbox\" name=\"tipo[]\" value=\"$tipoAnimal\" $checked>$tipoAnimal</div>";
            }
        }
            return $html;
    }

    protected function generaCamposRaza(&$datos){
        $html='';
        if(Animal::getTipos()){
            foreach(Animal::getRazas() as $razaAnimal ){
                $checked="";
                if(isset($datos['raza']))
                    $checked =in_array($razaAnimal, $datos['raza'])?"checked":"";
                $html.="<div><input type=\"checkbox\" name=\"raza[]\" value=\"$razaAnimal\" $checked>$razaAnimal</div>";
            }
        }
        return $html;
    }

    protected function generaCamposProtectora(&$datos){
        $html='';
        $protectoras = Protectora::buscaPorEstado(Protectora::ACTIVA);
        if($protectoras){
            foreach($protectoras as $protectora ){
                $nombre=$protectora->getNombre();
                $id = $protectora->getId();
                $checked="";
                if(isset($datos['protectora']))
                    $checked =in_array($id,$datos['protectora'])?"checked":"";
                $html.="<div><input type=\"checkbox\" name=\"protectora[]\" value=\"$id\" $checked />$nombre</div>";
            }
        }
        return $html;
    }

    protected function generaCamposGenero(&$datos){
        $html='';
        foreach($this->tiposGenero as $genero ){
            $checked="";
            if(isset($datos['genero']))
                $checked =in_array($genero, $datos['genero'])?"checked":"";
            $html.="<div><input type=\"checkbox\" name=\"genero[]\" value=\"$genero\"$ />$genero</div>";
        }
        return $html;
    }

    protected function generaCamposFormulario(&$datos){
        $tipo = self::generaCamposTipo($datos);
        $raza = self::generaCamposRaza($datos);
        $protectora = self::generaCamposProtectora($datos);
        $genero =  self::generaCamposGenero($datos);
        $edadmin = $datos['edadmin'] ?? '1';
        $edadmax = $datos['edadmax'] ?? '100';
        $pesomin = $datos['pesomin'] ?? '1';
        $pesomax = $datos['pesomax'] ?? '100';

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['tipo','raza','protectora','genero','edadmin', 'edadmax', 'pesomin', 'pesomax'], $this->errores, 'span', array('class' => 'error'));

        $html = <<<EOF
        $htmlErroresGlobales
            <div class="campoFiltroCheckbox">
                <label>Tipo de animal</label>
                <div>
                    $tipo
                </div>
            </div>
            <div class="campoFiltroCheckbox">
            <label for="genero">Género</label>
                <div>
                    $genero
                </div>
            </div>
            <div class="campoFiltroCheckbox">
                <label for="raza">Raza</label>
                <div>
                    $raza
                </div>
            </div>
            <div class="campoFiltro">
                <label for="edadmin">Edad mínima</label>
                <input id="edadmin" type="number" name="edadmin" value="$edadmin" min ="0"/>
                {$erroresCampos['edadmin']}
            </div>
            <div class="campoFiltro">
                <label for="edadmax">Edad máxima</label>
                <input id="edadmax" type="number" name="edadmax" value="$edadmax" min ="0" />
                <span id="errorEdad"></span>
                {$erroresCampos['edadmax']}
            </div>
            <div class="campoFiltro">
                <label for="pesomin">Peso mínimo</label>
                <input id="pesomin" type="number" name="pesomin" value="$pesomin" min ="0" />
                {$erroresCampos['pesomin']}
            </div>
            <div class="campoFiltro">
                <label for="pesomax">Peso máximo</label>
                <input id="pesomax" type="number" name="pesomax" value="$pesomax"min ="0"/>
                <span id="errorPeso"></span>
                {$erroresCampos['pesomax']}
            </div>
            <div class="campoFiltroCheckbox">
                <label for="">Protectora</label>
                <div>
                    $protectora
                </div>
            </div>
            <div id="botonesFiltro">
                <button type="submit" id='botonFiltro'>Aplicar</button>
                <input type="button" id='botonFiltro' onclick=" location.href='animales.php' " value="Reset" name="Reset" />
            </div>
        EOF;
        return $html;
    }


    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];
        
        $tipo = $datos['tipo']?? [];
        $getTipos = Animal::getTipos();
        foreach($tipo as $recorreTipo){
            if(in_array($recorreTipo, $getTipos)){
                $this->errores['tipo'] = 'El tipo no es valido';
            }
        }

        $raza = $datos['raza'] ?? [];
        $getRaza = Animal::getRazas();
        foreach($raza as $recorreRaza){
            if(in_array($recorreRaza, $getTipos)){
                $this->errores['raza'] = 'La raza no es valida';
            }
        }


        $edadmin = trim($datos['edadmin'] ?? '');
        $edadmin = filter_var($edadmin, FILTER_SANITIZE_NUMBER_INT);
        if ( $edadmin < 0 && $edadmin != '') {
            $this->errores['edadmin'] = 'La edad no es válida.';
        }

        $edadmax = trim($datos['edadmax'] ?? '');
        $edadmax = filter_var($edadmax, FILTER_SANITIZE_NUMBER_INT);
        if ( $edadmax < 0 && $edadmax != '') {
            $this->errores['edadmax'] = 'La edad no es válida.';
        }

        $pesomin = trim($datos['pesomin'] ?? '');
        $pesomin = filter_var($pesomin, FILTER_SANITIZE_NUMBER_FLOAT);
            
            if ( $pesomin < 0 && $pesomin!='') {
                $this->errores['pesomin'] = 'El peso no es valido.';
            }
        
        $pesomax = trim($datos['pesomax'] ?? '');
        $pesomax = filter_var($pesomax, FILTER_SANITIZE_NUMBER_FLOAT);
            if ( $pesomax < 0 && $pesomax != '') {
                $this->errores['pesomax'] = 'El peso no es valido.';
            }
            
              
        $genero = $datos['genero'] ?? [];
        

        foreach($genero as $gen){
            if(in_array($gen, $this->tiposGenero)){
                $this->errores['genero'] = 'El genero no es valido';
            }
        }

        $protectora = $datos['protectora']??[];
        $getProtectoras = Protectora::buscaPorEstado(Protectora::ACTIVA);
        foreach($protectora as $prot){
            if(in_array($prot, $getProtectoras)){
                $this->errores['protectora'] = 'La protectora no es valida';
            }
        }
        
        $this->errores['general'] = "error";

    }
}
