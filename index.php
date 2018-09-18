<?php 
    require_once('db_con.php');

    if(isset($_POST['submit0'])) {

        $sql = 'UPDATE user SET state = 0';
        $stmt = $con->prepare($sql);
        $stmt->execute();
        if ($stmt->affected_rows > 0){
            echo 'State updated to 0!<br>';
        }
    }

    function SendSms($msisdn, $message, $from) {
        $username = 'cphsf124';
        $apikey = '80ba815b-6962-484a-b369-7fb59bac43af';
        $basicauth = base64_encode($username.':'.$apikey);

        //$url = 'https://'.$username.':'.$apikey.'@api.cpsms.dk/v2/simplesend/'.$msisdn.'/'.urlencode($message).'/'.urlencode($from);

        $url = 'https://api.cpsms.dk/v2/simplesend/'.$msisdn.'/'.urlencode($message).'/'.urlencode($from);

        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Authorization: Basic ".$basicauth
            )
        );

        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        $file = file_get_contents($url, false, $context);

        return $file;
    }



?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Reminder system</title>
    </head>
    <body>
        <?php 

            $sql = 'SELECT * FROM user';
            $stmt = $con->prepare($sql);
            $stmt->execute();
            $stmt->bind_result($id, $name, $email, $phone, $state, $date);

            while($stmt->fetch()) {
                echo $id."<br>";
                echo $name."<br>";
                echo $email."<br>";
                echo $phone."<br>";
                echo $state."<br>";
                echo $date."<br>";
                echo "<br>";
            }

            echo "<hr>";

            if(isset($_POST['submit-check'])) {

                $sql = 'SELECT * FROM user WHERE state = 0 AND date < NOW() + INTERVAL 4 DAY';
                $stmt = $con->prepare($sql);
                $stmt->execute();
                $stmt->bind_result($id, $name, $email, $phone, $state, $date);
                $stmt->store_result();

                while($stmt->fetch()) {
                    echo $id."<br>";
                    echo $name."<br>";
                    echo $email."<br>";
                    echo $phone."<br>";
                    echo $state."<br>";
                    echo $date."<br>";
                    echo "<br>";

                    $sql1 = 'UPDATE user SET state = 1 WHERE user_id = ?';
                    $stmt1 = $con->prepare($sql1);
                    $stmt1->bind_param('i', $id);
                    $stmt1->execute();
                    if ($stmt1->affected_rows > 0){
                            echo '<br>1 måned til tandlægetid, men i dette trin sker der ikke noget<br><br><br><hr>';
                        }
                }
                $stmt->free_result();

/*-----------------------------------------------------------------------------------------------------------------------------*/

                $sql = 'SELECT * FROM user WHERE state = 1 AND date < NOW() + INTERVAL 4 DAY';
                $stmt = $con->prepare($sql);
                $stmt->execute();
                $stmt->bind_result($id, $name, $email, $phone, $state, $date);
                $stmt->store_result();

                while($stmt->fetch()) {
                    echo $id."<br>";
                    echo $name."<br>";
                    echo $email."<br>";
                    echo $phone."<br>";
                    echo $state."<br>";
                    echo $date."<br>";
                    echo "<br>";

                    $to = $email;
                    $subject = "My subject";
                    $txt = "Hi " . $name . " remember your dentist appointment at " . $date;
                    $headers = "From: amanda@amandaap.dk" . "\r\n";

                    mail($to,$subject,$txt,$headers);

                    echo 'Sending mail to '.$email;

                    $sql1 = 'UPDATE user SET state = 2 WHERE user_id = ?';
                    $stmt1 = $con->prepare($sql1);
                    $stmt1->bind_param('i', $id);
                    $stmt1->execute();
                    if ($stmt1->affected_rows > 0){
                            echo '<br>Du skal til tandlægen om 1 måned! Så her får du en mail som bekræftigelse<br><br><br><hr>';
                        }
                }
                $stmt->free_result();

/*-----------------------------------------------------------------------------------------------------------------------------*/              

                $sql = 'SELECT * FROM user WHERE state = 2 AND date < NOW() + INTERVAL 1 DAY';
                $stmt = $con->prepare($sql);
                $stmt->execute();
                $stmt->bind_result($id, $name, $email, $phone, $state, $date);
                $stmt->store_result();

                while($stmt->fetch()) {
                    echo $id."<br>";
                    echo $name."<br>";
                    echo $email."<br>";
                    echo $phone."<br>";
                    echo $state."<br>";
                    echo $date."<br>";
                    echo "<br>";

                    $sql1 = 'UPDATE user SET state = 3 WHERE user_id = ?';
                    $stmt1 = $con->prepare($sql1);
                    $stmt1->bind_param('i', $id);
                    $stmt1->execute();
                    if ($stmt1->affected_rows > 0){
                        echo '<br>Du skal til tandlægen om 1 dag, men i dette trin sker der ikke noget<br><br><br><hr>';
                    }
                }
                $stmt->free_result();   

/*-----------------------------------------------------------------------------------------------------------------------------*/

                $sql = 'SELECT * FROM user WHERE state = 3 AND date < NOW() + INTERVAL 1 DAY';
                $stmt = $con->prepare($sql);
                $stmt->execute();
                $stmt->bind_result($id, $name, $email, $phone, $state, $date);
                $stmt->store_result();

                while($stmt->fetch()) {
                    echo $id."<br>";
                    echo $name."<br>";
                    echo $email."<br>";
                    echo $phone."<br>";
                    echo $state."<br>";
                    echo $date."<br>";
                    echo "<br>";


                    /*$username = 'cphsf124';
                    $apikey = '80ba815b-6962-484a-b369-7fb59bac43af';

                    $to = '4527142975'; // msisdn: 4511223344
                    $from = 'Dentist'; // from: Den
                    $message = 'Remember your appointment tomorrow mofo';

                    $url = 'https://'.$username.':'.$apikey.'@api.cpsms.dk/v2/simplesend/'.$to.'/'.urlencode($message).'/'.urlencode($from);
                    echo "gw: ".$url;*/





                    $msisdn = '4527142975'; // msisdn: 4511223344
                    $message = 'Husk at du skal til tandlægen den ' . $date . "Mvh Toothbook";
                    $from = 'Toothbook'; // from: Den
                    
                    SendSms($msisdn, $message, $from);



                    /*$curl = curl_init();

                    curl_setopt_array($curl, array(
                    CURLOPT_URL => $file,  
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_RETURNTRANSFER => true,  
                    ));

                    $response = curl_exec($curl);
                    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                    curl_close($curl);

                    if($httpCode == 200) {
                        echo 'OK: ' . $response;
                    } else {

                        echo 'Read response message for details: ' . $response;
                    }*/



                    $sql1 = 'UPDATE user SET state = 4 WHERE user_id = ?';
                    $stmt1 = $con->prepare($sql1);
                    $stmt1->bind_param('i', $id);
                    $stmt1->execute();
                    if ($stmt1->affected_rows > 0){
                        echo '<br>Du skal til tandlægen om 1 dag! Så her får du en sms som bekræftigelse<br><br><br><hr>';
                    }
                }
                $stmt->free_result();

            } 
        ?>

        <form action="#" method="post">
            <input type="submit" name="submit-check" value="Check for appointments">
        </form>

        <form action="#" method="post">
            <input type="submit" name="submit0" value="State 0">
        </form>
        
    </body>
</html>