<?php

class math {

    private function __construct(){
    }

    public static function anneeForm($action, $submitText = 'Valider'){
        $r = <<<HTML
        <div class="limiter">
          <div class="container-table100">
      			<div class="wrap-table100">
              <form method="get" action=$action>
                <div class="form-row">
                  <div class="form-group col-md-3">
                    <label>Nombre d'années</label>
                    <input type="number" class="form-control" name="nba" maxlength="40" placeholder = "Nombre d'années">
                  </div>
                  <div class="form-group col-md-3">
                    <label for="i">Taux d'intérêt annuel</label>
                    <input type = "number" id="i" class="form-control" name="i" maxlength ="10" placeholder="Taux d'intérêt annuel (en %)">
                  </div>
                  <div class="form-group col-md-2">
                    <label for="inputPassword4">Montant (en €)</label>
                    <input type="number" class="form-control" name="montant" maxlength ="40" placeholder="Montant (en €)">
                  </div>
                  <div class="form-group col-md-3">
                    <label for="inputState">Périodicité</label>
                    <select name="periode" class="form-control">
                      <option value="1">Mensuel</option>
                      <option value="2" >Trimestriel</option>
                      <option value="3" >Semestriel</option>
                      <option value="4" selected>Annuel</option>
                    </select>
                  </div>
                  <button name="valider" type="submit" class="btn btn-primary" value=$submitText>$submitText</button>
                </div>
          <!--
            <input placeholder = "Nombre d'années" type="number" name="nba" maxlength="40">
            <input placeholder = "Taux d'intérêt annuel (en %)" type = "number" name="i" maxlength ="10">
            <input placeholder = "Montant (en €)" type = "number" name="montant" maxlength ="40">
            <select name="periode">
                <option value="1">Mensuel</option>
                <option value="2" >Trimestriel</option>
                <option value="3" >Semestriel</option>
                <option value="4" selected>Annuel</option>
            </select>
            <br>
            <input name="valider" type="submit" value=$submitText>
            -->
        </form>


      				<div class="table100">
                <table>
                  <thead>
                        <tr class="table100-head" >
                            <th class="column1" >Période</th>
                            <th class="column2" >Capital restant en debut de période</th>
                            <th class="column3" >Intérêts de la période</th>
                            <th class="column4" >Amortissement du capital</th>
                            <th class="column5">Annuité d'emprunt</th>
                            <th class="column6" >Capital restant dû en fin de période</th>
                        </tr>
                    </thead>
                  <tbody>
HTML;
        return $r;
    }

    public static function calculAnnee($r){
        $pr = $_GET['periode'];

        $i = self::getI();

        $a =math::getAnnee();
        if ($r[0] == 0){
            $r[0] = 1;
        }
        else{
            $r[0] = $r[0]+1;
        }

        if ($r[1] == 0){
            $r[1]= $_GET['montant'];
        }
        else{
            $r[1] = $r[5];
        }
        $r[2]= round($r[1]*($i),2);
        $r[4]= round($_GET['montant']*($i/(1-(1+$i)**(-$a))),2);
        $r[3]= round($r[4]-$r[2],2);
        $r[5]= round($r[1]-$r[3],2);
        if ($r[5]<0.1){
            $r[5] = 0;
        }
        return $r;
    }


    public static function calculForm(){
        $html = "";
        $r = [0,0,0,0,0,0];
        $pr = $_GET['periode'];
        $tI = 0;
        $tA = 0;
        $tAn = 0;
        for ($i = 0;$i<math::getAnnee();$i++){
            $an = $i+1;
            $r = math::calculAnnee($r);
            $html .= <<<HTML
                  <tr>
                      <td class = "column 1">$r[0]</td>
                      <td class = "column 2">$r[1]</td>
                      <td class = "column 3">$r[2]</td>
                      <td class = "column 4">$r[3]</td>
                      <td class = "column 5">$r[4]</td>
                      <td class = "column 6">$r[5]</td>
                      
                  </tr>
HTML;
            $tI += $r[2];
            $tA += $r[3];
            $tAn += $r[4];
        }
        if ($tA !=$_GET['montant']){
            $v = $_GET['montant'] - $tA;
            $tA = $_GET['montant'];
            $tAn = $tAn + $v;
        }
        $html .= <<<HTML
                    <tr>
                        <td class = "column 1">Totaux</td>
                        <td class = "column 2"></td>
                        <td class = "column 1">$tI</td>
                        <td class = "column 1">$tA</td>
                        <td class = "column 1">$tAn</td>
                        <td class = "column 1"></td>
                        <td class = "column 1"></td>
                    </tr>
              </tbody>
            </table>
          </div>
  			</div>
  		</div>
  	</div>
    <!--===============================================================================================-->
    	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <!--===============================================================================================-->
    	<script src="vendor/bootstrap/js/popper.js"></script>
    	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <!--===============================================================================================-->
    	<script src="vendor/select2/select2.min.js"></script>
    <!--===============================================================================================-->
    	<script src="js/main.js"></script>
HTML;
        return $html;

    }

    public static function getI(){
        $pr = $_GET['periode'];
        $i = $_GET['i']/100;

        if ($pr == 3){
            $i = (1+$i)**(1/2)-1;
        }
        if ($pr == 2){
            $i = (1+$i)**(1/4)-1;
        }

        if($pr == 1){
            $i = (1+$i)**(1/12)-1;


        }
        return $i;
    }

    public static function getAnnee(){
        $an = $_GET['nba'];
        $pr = $_GET['periode'];
        if ($pr == 3){
            $an = $an * 2;
        }
        if($pr ==2){
            $an = $an * 4;
        }
        if($pr == 1){
            $an = $an * 12;
        }

        return $an;
    }
}
