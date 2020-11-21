<!DOCTYPE html>
<?php 
session_set_cookie_params([
    'lifetime' => $cookie_timeout,
    'path' => '/',
    'domain' => $cookie_domain,
    'secure' => 'True',
    'httponly' => $cookie_httponly,
    'samesite' => 'None'
]);
session_start();
?>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title>Morpion</title>
    <meta name="description" content="Online morpion game. (lostsh) ᓚᘏᗢ">
    <style>
        body{
            font-family: monospace;
            font-size: x-large;
        }
        h1,p{
            margin: 0 0 15px 0;
        }
        .main{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .vicMsg{
            clear: both;
            position: fixed;
            background: grey;
            margin: 0;
            padding: 0;
            top: 0;
            left: 0;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            line-height: 100vh;
            text-align: center;
        }
        .vicMsg a{
            text-decoration: none;
            border: 3px solid black;
            padding: 3px 4px 3px 4px;
        }
        table td { border: 10px solid black; width: min(25vw, 25vh); height: min(25vw, 25vh); text-align: center; }
        table a{
            display: block;
            width: 100%;
            height: 100%;
        }
        table .zero{
        }
        /*
        table .one{
            background-color: black;
            clip-path: polygon(5% 0%, 50% 45%, 95% 0%, 100% 5%, 55% 50%, 100% 95%, 95% 100%, 50% 55%, 5% 100%, 0% 95%, 45% 50%, 0% 5%);
        }
        table .two{
            border: 10% solid black;
            border-radius: 100%;
        }*/
    </style>
    <?php 
        echo("<style>");
        if(isset($_SESSION['theme'])){
            echo("table .one{background-color: red; clip-path: polygon(5% 0%, 50% 45%, 95% 0%, 100% 5%, 55% 50%, 100% 95%, 95% 100%, 50% 55%, 5% 100%, 0% 95%, 45% 50%, 0% 5%);} table .two{ border: 10% solid red; border-radius: 100%; }");
        }else{
            echo("table .one{background-color: black; clip-path: polygon(5% 0%, 50% 45%, 95% 0%, 100% 5%, 55% 50%, 100% 95%, 95% 100%, 50% 55%, 5% 100%, 0% 95%, 45% 50%, 0% 5%);} table .two{ border: 10% solid black; border-radius: 100%; }");
        }
        echo("</style>");
    ?>
</head>
<body>
    <h1 hidden>Morpion</h1>
    <div class="main">
                <?php
                    
                    /**
                     * Configure the 3*3 Tab who contains the game
                     */
                    $gameStatus = initTab();
                    if(isset($_SESSION['gameStatus'])){
                        $gameStatus = $_SESSION['gameStatus'];
                    }else{
                        $_SESSION['gameStatus'] = $gameStatus;
                    }
                    /**
                     * Player settings, current player is differant than the precedant ...
                     */
                    if(isset($_SESSION['player'])){
                        if($_SESSION['player'] == 1){
                            $_SESSION['player'] = 2;
                        }else{
                            $_SESSION['player'] = 1;
                        }
                    }else{
                        $_SESSION['player'] = 2;
                    }

                    /**
                     * Theme settings
                     */
                    if(isset($_GET['theme'])){
                        $_SESSION['theme'] = $_GET['theme'];
                    }

                    /**
                     * If someone juste clic on a cell, then update the tab
                     */
                    if(isset($_GET['col']) && isset($_GET['lig']) && isset($_GET['player'])){
                        $gameStatus[$_GET['lig']][$_GET['col']] = $_GET['player'];
                        $_SESSION['gameStatus'] = $gameStatus;
                    }

                    /**
                     * Management of victory and equality conditions
                     */
                    $winer = whoWin($gameStatus);
                    if($winer != 0){
                        echo("<p class=\"vicMsg\">The winer is <strong>Player ".$winer."</strong>.<a href=\"index.php\">New Game</a></p>");
                        $gameStatus = initTab();
                        $_SESSION['gameStatus'] = $gameStatus;
                    }else if(isMatrixFull($gameStatus)){
                        echo("<p class=\"vicMsg\">No winner, <strong>tie</strong>.<a href=\"index.php\">New Game</a></p>");
                        $gameStatus = initTab();
                        $_SESSION['gameStatus'] = $gameStatus;
                    }

                    /**
                     * Draw the main game ground
                     * [x][x][x]
                     * [x][x][x]
                     * [x][x][x]
                     */
		            echo("<table><tbody>");
                    for($i=0;$i<3;$i++){
                        echo("<tr>");
                        for($j=0;$j<3;$j++){
                            if($gameStatus[$i][$j] == 0){
                                echo("<td class=\"zero\"><a href=\"?col=".$j."&lig=".$i."&player=".$_SESSION['player']."\"></a></td>");
                            }else{
                                if($gameStatus[$i][$j] == 1){
                                    echo("<td class=\"one\"></td>");
                                }else{
                                    echo("<td class=\"two\"></td>");
                                }
                            }
                        }
                        echo("</tr>");
                    }
		            echo("</tbody></table>");

                    /**
                     * Return an int 2D tab filed with 0
                     * [0][0][0]
                     * [0][0][0]
                     * [0][0][0]
                     */
                    function initTab(){
                        $tab = array();
                        for($i=0;$i<3;$i++){
                            for($j=0;$j<3;$j++){
                                $tab[$i][$j] = 0;
                            }
                        }
                        return $tab;
                    }

                    /**
                     * Return 0 if no winer
                     * Else return the numer of the winer {1 or 2}
                     */
                    function whoWin($matrix){
                        $winer = 0;
                        $i=0;
                        while($i<3 and $winer==0){
                            if(isLineSameValue($i, $matrix) != 0){
                                $winer = isLineSameValue($i, $matrix);
                            }else if(isColSameValue($i, $matrix) != 0){
                                $winer = isColSameValue($i, $matrix);
                            }else if(isDiagonalSameValue($matrix)){
                                $winer = isDiagonalSameValue($matrix);
                            }
                            $i++;
                        }
                        return $winer;
                    }
                    /**
                     * Return the numer 
                     * if there is the same numer on all the $line of the $tab
                     * else retrurn 0
                     */
                    function isLineSameValue($line, $tab){
                        $value = $tab[$line][0];
                        for($i=0;$i<count($tab[$line])-1;$i++){
                            if($tab[$line][$i] != $tab[$line][$i+1]){
                                $value = 0;
                            }
                        }
                        return $value;
                    }
                    /**
                     * Return the numer 
                     * if there is the same numer on all the $col of the $tab
                     * else retrurn 0
                     */
                    function isColSameValue($col, $tab){
                        $value = $tab[0][$col];
                        $colSize = (count($tab, COUNT_RECURSIVE) - count($tab[0])*count($tab[0]));
                        for($i=0;$i<$colSize-1;$i++){
                            if($tab[$i][$col] != $tab[$i+1][$col]){
                                $value = 0;
                            }
                        }
                        return $value;
                    }
                    /**
                     * Return the numer duplicated on all a diagonale
                     * if there is no same values, then it return 0
                     */
                    function isDiagonalSameValue($tab){
                        $size = 3;
                        $diagOneVal = $tab[0][0];
                        $diagTwoVal = $tab[0][$size-1];
                        for($i=0;$i<$size-1;$i++){
                            if($tab[$i][$i] != $tab[$i+1][$i+1]){
                                $diagOneVal = 0;
                            }
                            if($tab[$i][$size-1-$i] != $tab[$i+1][$size-2-$i]){
                                $diagTwoVal = 0;
                            }
                        }
                        return ($diagOneVal!=0 || $diagTwoVal!=0)?($diagOneVal!=0?$diagOneVal:$diagTwoVal):0;
                    }
                    /**
                     * Return true if the matrix is full
                     */
                    function isMatrixFull($matrix){
                        $isFull = true;
                        $size = count($matrix);
                        $i = 0;
                        while($i<$size && $isFull){
                            $j = 0;
                            while($j<$size && $isFull){
                                if($matrix[$i][$j] == 0){
                                    $isFull = false;
                                }
                                $j++;
                            }
                            $i++;
                        }
                        return $isFull;
                    }
                ?>
    </div>
    <script>
        console.log("[*]\tWelcome to the "+"%cMorption game%c BY:lostsh ᓚᘏᗢ\n","background: #222; color: #bada55;","color: #bada55;");
        welcomeMsg();
        help();
        function help(){
            console.log("\n%c[%c=%c] >%c░░░░░░░░░░░░░░░░░░░░░░░░░░░░░%c<\n","color: #fffff;","color: #ff0000;","color: #fffff;","color: #ff0000;","color: #fffff;");
            console.log("%c[%c+%c]\t"+"%cChoose your theme\n","color: #fffff;","color: #6833ff;","color: #fffff;","color: #6833ff;");
            console.log("[+] https://lostsh.github.io\n");
            console.log("╔═════════════════════════════╗                                    \n║ ╭─────────────────────────╮ ║▒                                   \n║ │     Choice of theme     │ ║▒                                   \n╟─┴─────────────────────────┴─╢▒                                   \n║ To choose a theme please,   ║▒                                   \n║ click the theme you want    ║▒                                   \n║ in the list below.          ║▒                                   \n╠══════════════╦══════════════╣▒                                   \n║     NAME     ║     LINK     ║▒                                   \n╠══════════════╬══════════════╩══════════════════════════════════╗ \n║ DARK  HACKER │ http://zthost.local/morpion/morpion.php?theme=1 ║▒\n╟──────────────┼─────────────────────────────────────────────────╣▒\n║ LIGHT SPIRIT │ http://zthost.local/morpion/morpion.php?theme=2 ║▒\n╟──────────────┼─────────────────────────────────────────────────╣▒\n║  DEEP WATER  │ http://zthost.local/morpion/morpion.php?theme=3 ║▒\n╟──────────────┼─────────────────────────────────────────────────╣▒\n║  GELLY FISH  │ http://zthost.local/morpion/morpion.php?theme=4 ║▒\n╚══════════════╧═════════════════════════════════════════════════╝▒\n ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒\n");
        }
        function welcomeMsg(){
            console.log("┌─────────────────────────────┐\n│  ╔═════════╗     ┏┅┅┅┅┅┅┓   │▒\n│  ║ MORPION ║     ┇ GAME ┇   │▒\n│  ╚════╦════╝     ┗┅┅┅┅┅┅┛   │▒\n╞═╤═════╩═════╤═══════════════╡▒\n│ ├───┬───┬───┤               │▒\n│ │ X │   │ O │   ╭╴ 	  ╶╮  │▒\n│ ├───┼───┼───┤   │   BY   │  │▒\n│ │   │ X │ O │   │        │  │▒\n│ ├───┼───┼───┤   │ LOSTSH │  │▒\n│ │   │   │ X │   ╰╴      ╶╯  │▒\n│ └───┴───┴───┘               │▒\n└─────────────────────────────┘▒\n ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒\n");
        }
    </script>
</body>

</html>
