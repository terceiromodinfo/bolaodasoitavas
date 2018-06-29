<?php
include './FuncoesBDL.php';
include './mostra_erros.php';
$get = get();
session_start();
if (!(isset($_SESSION['login']) && isset($_SESSION['senha']))) {
    if (!($_SESSION['apostadores'])) {
        abrirDados();
    }
}
/*
 * Atualizar dados da session
 */

?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <link href="estilo.css" style="" rel="stylesheet">
        <!-- Bootstrap -->
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="estilo.css" rel="stylesheet">
    </head>
    <body>
        <!--Começo do cabeçario-->
        <nav class="navbar navbar-default navbar-fixed-top corFundoAzul">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#menu-navegacao">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span> 
                        <span class="icon-bar"></span>  
                    </button>
                    <a href="#pageTop" class="navbar-brand" style="font-weight: bold">Bolão</a>
                </div>
                <div class="collapse navbar-collapse menu-navegacao" id="menu-navegacao">
                    <ul class="nav navbar-nav navbar-right">
                        
                        
                        <li>
                            <a class="glyphicon glyphicon-repeat" href="?abrirDados"></a>
                        </li>
                        
                        <li>
                            <a class="" href="index.php">Pagina Inicial</a>
                        </li>
                        <?php
                            if (isset($_SESSION['login']) && isset($_SESSION['senha'])) {
                        ?>
                        <li>
                            <a class="" href="Cadastra.php">Cadastra</a>
                        </li>
                        
                        <li>
                            <a class="" href="Configuracoes.php">Configurações</a>
                        </li>
                        <li>
                                <a class="" href="Gols.php">Iserir gols</a>

                        </li>               
                        <li>
                            <a class="" href="Sair.php">Sair</a>
                        </li>
                        <?php
                            }
                        ?>
                        <?php
                            if (!(isset($_SESSION['login']) && isset($_SESSION['senha']))) {
                        ?>
                        <li>
                            <a class="btn butaoLogar letraswhite" href="login.php">Admin</a>
                        </li>
                        <?php
                            }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="fundo1">
            <br><br><br>
            <!--Fim do cabeçario-->

            <div class="container">
                <div class="row">

                    <div class="col-md-12">
                        <div class="col-md-5"></div>
                        <div class="col-md-2">
                            <form method="POST" class="form-group" action="LogicasPrincipal.php">
                                <input class="letrasAmarela btn btn-success" type="submit" name="atualizarPontuacao" value="Atualizar pontuação"/>
                            </form>
                        </div>
                        <div class="col-md-5"></div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-6">
                            <table class="tabelas table table-hover">
                                <tr> 
                                    <th class="letrasBolds">#</th>
                                    <th class="letrasBolds">Competidores</th>
                                    <th class="letrasBolds">Pontos</th>
                                </tr>

                                <?php
                                //print_r($A = getInfoTabela("apostadores"));
                                /*
                                 * Este FOR rodará de acordo a quantidade de apostadores que ouver na tabela do banco de dados
                                 * para a exibição dos apostadores e a sua quantidade de pontos.
                                 */
                                $apostadores = getOrdenaUsuarioPorPontos();
                                $posicao = 1;
                                $posicaoDoPrimeiro = 0;
                                $primeiroCarasDoMesmoPontos = [];
                                $ultimoCarasDoMesmoPontos = [];
                                $rodarNovamente = 0;
                                $primeiro = 0;
                                $ultimo = 0;
                                $liberarOrganizar = 0;
                                $apostadoresComMesmaPontucao = [];
                                $bacupsApostadores = [];
                                //Descobrir se há ao menos duas pessoas com a mesma pontuação
                                for ($a = 0; $a < getQuantLinhasTabela("apostadores"); $a++) {
                                    if ($a > 0) {
                                        if ($apostadores[$a]['pontos'] == $apostadores[$a - 1]['pontos'] && $rodarNovamente == 0) {
                                            $primeiroCarasDoMesmoPontos[0] = $apostadores[$a - 1];
                                            $primeiro = $a - 1;
                                            $rodarNovamente = 1;
                                        }
                                        if ($primeiroCarasDoMesmoPontos[0]['pontos'] == $apostadores[$a]['pontos'] && $rodarNovamente == 1) {
                                            $ultimoCarasDoMesmoPontos[0] = $apostadores[$a];
                                            $ultimo = $a;
                                        }
                                        if (!($apostadores[$a]['pontos'] == $apostadores[$a - 1]['pontos']) && $rodarNovamente == 1) {
                                            $rodarNovamente = 0;
                                            $liberarOrganizar = 1;
                                        }
                                        if ($liberarOrganizar == 1) {
                                            for ($e = $primeiro; $e <= $ultimo; $e++) {
                                                $golsDosCapitaes = getGols($apostadores[$e]['capitao']);
                                                $golsDosCapitaes = $golsDosCapitaes + getGols($apostadores[$e]['capitao2']);
                                                $golsDosCapitaes = $golsDosCapitaes + getGols($apostadores[$e]['capitao3']);
                                                $golsDosCapitaes = $golsDosCapitaes + getGols($apostadores[$e]['capitao4']);

                                                $apostadores[$e]['desempate'] = $golsDosCapitaes;
                                                $apostadoresComMesmaPontucao[$e] = $apostadores[$e];
                                            }
                                            for ($z = $primeiro; $z <= $ultimo; $z++) {
                                                $bacupsApostadores[$z] = $apostadoresComMesmaPontucao[$z];
                                            }   
                                            //Botar em hordem decrecente os dados
                                            $apostadorMaior;
                                            $zerarDesempate;

                                            for ($u = $primeiro; $u <= $ultimo; $u++) {
                                                //print count($bacupsApostadores)." ";
                                                $maior = 1000;
                                                for ($b = $primeiro; $b <= $ultimo; $b++) {

                                                    if ($apostadoresComMesmaPontucao[$b]['desempate'] < $maior) {
                                                        $maior = $apostadoresComMesmaPontucao[$b]['desempate'];
                                                        $apostadorMaior[$u] = $apostadoresComMesmaPontucao[$b];
                                                        $zerarDesempate = $b;
                                                    }
                                                }
                                                for ($c = $primeiro; $c <= $ultimo; $c++) {
                                                    if ($c == $zerarDesempate) {
                                                        $apostadoresComMesmaPontucao[$c]['desempate'] = 2000;
                                                    }
                                                }
                                            }
                                            //Botar em ordem crecente
                                            $contador2 = $primeiro;
                                            for ($f = $ultimo; $f >= $primeiro; $f--) {
                                                $apostadoresComMesmaPontucao[$contador2] = $apostadorMaior[$f];
                                                $contador2++;
                                            }
                                            /*
                                            for ($d = $primeiro; $d <= $ultimo; $d++) {
                                                //print $d;
                                                print $apostadoresComMesmaPontucao[$d]['nome']."<br>";
                                            }
                                             * 
                                             */
                                            //print_r($apostadorMaior);
                                            
                                            //include './OrdenandoArray.php';
                                            
                                            //print $primeiro." ".$ultimo;
                                            //$apostadoresComMesmaPontucao = getOrdenaUsuarioPorPontos2($apostadoresComMesmaPontucao);
                                            
                                            
                                            for ($o = $primeiro; $o <= $ultimo; $o++) {
                                                $apostadores[$o] = $apostadoresComMesmaPontucao[$o];                                           
                                            }
                                            
                                             
                                             
                                            
                                             
                                        }
                                    }    

                                }

                                //print_r($apostadoresComMesmaPontucao);
                                //Armasenar em um array os apostador com os mesmos pontos   
                                for ($i = 0; $i < getQuantLinhasTabela("apostadores"); $i++) {
                                    $_SESSION['id'] = $apostadores[$i]['id'];
                                    print "";
                                    print "<tr>";  
                                    if (($apostadores[$i]['pontos'] == $apostadores[$i-1]['pontos']) && ($apostadores[$i]['desempate'] == $apostadores[$i-1]['desempate'])) {
                                        print "<td></td>";
                                    }  else {
                                        print "<td>" . ($posicao) . "</td>";
                                    }                                                                                                         
                                    print "<td><a class='letrasBrancas' href='Apostador.php?id=" . $apostadores[$i]['id'] . "'>" . $apostadores[$i]['nome'] . "</a></td>";
                                    print "<td class='letrasLaranja'>" . $apostadores[$i]['pontos'] . "</td>";
                                    if ($apostadores[$i]['desempate'] > (-1)) {
                                        print "<td class='capitao'>" . $apostadores[$i]['desempate'] . "</td>";
                                    }                                    
                                    print "</a>"; 
                                    
                                        $posicao++;
                                    
                                    
                                    
                                }
                                ?>
                            </table>
                        </div>
                        <div class="col-md-3 "></div>
                    </div>

                </div>    
            </div>
        </div>
        <div class="fundo2">
            <div class="container">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <h1 style="text-align: center">Jogadores</h1>
                        <table class="tabelas table table-hover">
                            <tr>
                                <th>Nome</th>
                                <th>Gols</th>
                            </tr>


                            <?php
                            /*
                             * Este FOR rodará de acordo a quantidade de jogadpres que ouver na tabela do banco de dados
                             * para a exibição dos jogadores e a sua quantidade de gols.
                             * 
                             */
                            $jogadores = getOrdenaJogadorPorGols();
                            for ($i = 0; $i < getQuantLinhasTabela("jogadores"); $i++) {
                                if ($jogadores[$i]["gols"] > 0) {
                                    print "<tr>";

                                print "<td>" . $jogadores[$i]["nome"] . "</td>";
                                print "<td>" . $jogadores[$i]["gols"] . "</td>";

                                print "</tr>";
                                }                                
                            }
                             
                            ?>

                        </table>
                    </div>
                    <div class="col-md-2"></div>                    
                </div>
            </div>           
        </div>




        <!-- jQuery (obrigatório para plugins Java do Bootstrap) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Inclui todos os plugins compilados (abaixo), ou inclua arquivos separadados se necessário -->
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>
<?php
//if(getQuantLinhasTabela("apostadores")){print_r(getColExpecifica("apostadores","nome"));};
//getQuantLinhasTabela("apostadores");
//Teste
//$query = mysql_query("SELECT column_name FROM information_schema.columns WHERE table_name = 'apostadores'");
//setPontuacao();
?>