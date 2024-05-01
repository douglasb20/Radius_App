<?php

namespace App\Services;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
class TwigService{

    private $functions;
    public $twig;
    public function __construct()
    {
        $this->functions = $this->get_defined_functions_in_file(ROOT_PATH . "/Config/funcoes.php") ;
        $loader          = new FilesystemLoader(ROOT_PATH . "/App/View");
        $this->twig      = new Environment($loader);

        foreach($this->functions as $func){
            $f = new \Twig\TwigFunction($func, $func);
        
            $this->twig->addFunction($f);
        }

        $this->twig->addExtension(new \Twig\Extra\Intl\IntlExtension());

        $this->twig->addGlobal("ENV", $_ENV);
        $this->twig->addGlobal("cookie", $_COOKIE);
    }

    public function getFunction(){
        return $this->functions;
    }

    public function addGlobal($key, $value){
        $this->twig->addGlobal($key, $value);
    }

    public function load($name){
        return $this->twig->load($name);
    }

    public function addFunction(string $name, $callback){
        $f = new \Twig\TwigFunction($name, $callback);
        $this->twig->addFunction($f);
    }

    public function render($name, ...$args){
        return $this->twig->render($name, ...$args);
    }

    private function get_defined_functions_in_file($file) {
        $source           = file_get_contents($file);
        $tokens           = token_get_all($source);
    
        $functions        = array();
        $nextStringIsFunc = false;
        $inClass          = false;
        $bracesCount      = 0;
    
        foreach($tokens as $token) {
            switch($token[0]) {
                case T_CLASS:
                    $inClass = true;
                    break;
                case T_FUNCTION:
                    if(!$inClass) $nextStringIsFunc = true;
                    break;
    
                case T_STRING:
                    if($nextStringIsFunc) {
                        $nextStringIsFunc = false;
                        $functions[] = $token[1];
                    }
                    break;
    
                // Anonymous functions
                case '(':
                case ';':
                    $nextStringIsFunc = false;
                    break;
    
                // Exclude Classes
                case '{':
                    if($inClass) $bracesCount++;
                    break;
    
                case '}':
                    if($inClass) {
                        $bracesCount--;
                        if($bracesCount === 0) $inClass = false;
                    }
                    break;
            }
        }
    
        return $functions;
    }

}