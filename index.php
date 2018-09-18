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

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Reminder system</title>
        <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
        <script src="main.js"></script>
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
                            echo '<br>Du skal til tandlægen om en måned yo! MEN DER SKER IKKE NOGET ENDNU<br><br><br><hr>';
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
                            echo '<br>Du skal til tandlægen om en måned yo! SÅ NU FÅR DU EN MAIL<br><br><br><hr>';
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
                        echo '<br>Du skal til tandlægen i morgen for FANDEN! MEN DER SKER IKKE NOGET ENDNU<br><br><br><hr>';
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


                    $username = 'cphsf124';
                    $apikey = 'Y3Boc2YxMjQ6ODBiYTgxNWItNjk2Mi00ODRhLWIzNjktN2ZiNTliYWM0M2Fm';

                    $to = '4527142975'; // msisdn: 4511223344
                    $from = 'Dentist'; // from: Den
                    $message = 'Remember your appointment tomorrow mofo';

                    $url = 'https://'.$username.':'.$apikey.'@api.cpsms.dk/v2/simplesend/'.$to.'/'.urlencode($message).'/'.urlencode($from);
                    echo "gw: ".file_get_contents($url);


                    $sql1 = 'UPDATE user SET state = 4 WHERE user_id = ?';
                    $stmt1 = $con->prepare($sql1);
                    $stmt1->bind_param('i', $id);
                    $stmt1->execute();
                    if ($stmt1->affected_rows > 0){
                        echo '<br>Du skal til tandlægen i morgen for FANDEN! SÅ DU FÅR EN SMS<br><br><br><hr>';
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