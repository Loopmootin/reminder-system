<?php 

    //connecting to the database
    require_once('db_con.php');


    //resetting the states of all appointments to 0
    if(isset($_POST['submit0'])) {

        $sql = 'UPDATE user SET state = 0';
        $stmt = $con->prepare($sql);
        $stmt->execute();
        if ($stmt->affected_rows > 0){
            echo 'State updated to 0!<br>';
        }
    }

    //creating function to send text reminders
    function SendSms($msisdn, $message, $from) {
        $username = 'cphsf124';
        $apikey = '80ba815b-6962-484a-b369-7fb59bac43af';
        $basicauth = base64_encode($username.':'.$apikey);

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

            //pulling all users from the database and listing them on the site
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

            //running an if on the check appointments button
            if(isset($_POST['submit-check'])) {

                //checking for users at state = 0 with less than a month to their appointment 
                $sql = 'SELECT * FROM user WHERE state = 0 AND date < NOW() + INTERVAL 4 DAY'; //note that this should be 30 and not 4 days
                $stmt = $con->prepare($sql);
                $stmt->execute();
                //binding the results to variables that makes sense name wise
                $stmt->bind_result($id, $name, $email, $phone, $state, $date);
                //storing the statement results
                $stmt->store_result();

                while($stmt->fetch()) {
                    echo $id."<br>";
                    echo $name."<br>";
                    echo $email."<br>";
                    echo $phone."<br>";
                    echo $state."<br>";
                    echo $date."<br>";
                    echo "<br>";

                    //updating the state to 1 of the user with mactching id 
                    $sql1 = 'UPDATE user SET state = 1 WHERE user_id = ?';
                    $stmt1 = $con->prepare($sql1);
                    $stmt1->bind_param('i', $id);
                    $stmt1->execute();
                    if ($stmt1->affected_rows > 0){
                            echo '<br>1 måned til tandlægetid, men i dette trin sker der ikke noget<br><br><br><hr>';
                        }
                }
                //clearing the statement results
                $stmt->free_result();

/*-----------------------------------------------------------------------------------------------------------------------------*/

                //checking for users at state = 1 with less than a month to their appointment 
                $sql = 'SELECT * FROM user WHERE state = 1 AND date < NOW() + INTERVAL 4 DAY'; //note that this should be 30 and not 4 days
                $stmt = $con->prepare($sql);
                $stmt->execute();
                //binding the results to variables that makes sense name wise
                $stmt->bind_result($id, $name, $email, $phone, $state, $date);
                //storing the statement results
                $stmt->store_result();

                while($stmt->fetch()) {
                    echo $id."<br>";
                    echo $name."<br>";
                    echo $email."<br>";
                    echo $phone."<br>";
                    echo $state."<br>";
                    echo $date."<br>";
                    echo "<br>";

                    //running the mail form and sending it to the person whos contact information is saved in the variables above
                    $to = $email;
                    $subject = "My subject";
                    $txt = "Hi " . $name . " remember your dentist appointment at " . $date;
                    $headers = "From: amanda@amandaap.dk" . "\r\n";

                    mail($to,$subject,$txt,$headers);

                    echo 'Sending mail to '.$email;

                    //updating the state of the user from 1 to 2 of the users who has recieved a mail
                    $sql1 = 'UPDATE user SET state = 2 WHERE user_id = ?';
                    $stmt1 = $con->prepare($sql1);
                    $stmt1->bind_param('i', $id);
                    $stmt1->execute();
                    if ($stmt1->affected_rows > 0){
                            echo '<br>Du skal til tandlægen om 1 måned! Så her får du en mail som bekræftigelse<br><br><br><hr>';
                        }
                }
                //clearing the statement results
                $stmt->free_result();

/*-----------------------------------------------------------------------------------------------------------------------------*/              

                //checking for users at state = 2 with less than 1 day to their appointment 
                $sql = 'SELECT * FROM user WHERE state = 2 AND date < NOW() + INTERVAL 1 DAY';
                $stmt = $con->prepare($sql);
                $stmt->execute();
                $stmt->bind_result($id, $name, $email, $phone, $state, $date);
                //storing the statement results
                $stmt->store_result();

                while($stmt->fetch()) {
                    echo $id."<br>";
                    echo $name."<br>";
                    echo $email."<br>";
                    echo $phone."<br>";
                    echo $state."<br>";
                    echo $date."<br>";
                    echo "<br>";

                    //updating their state to 3
                    $sql1 = 'UPDATE user SET state = 3 WHERE user_id = ?';
                    $stmt1 = $con->prepare($sql1);
                    $stmt1->bind_param('i', $id);
                    $stmt1->execute();
                    if ($stmt1->affected_rows > 0){
                        echo '<br>Du skal til tandlægen om 1 dag, men i dette trin sker der ikke noget<br><br><br><hr>';
                    }
                }
                //clearing the statement results
                $stmt->free_result();   

/*-----------------------------------------------------------------------------------------------------------------------------*/

                //checking for users at state = 3 with less than 1 day to their appointment 
                $sql = 'SELECT * FROM user WHERE state = 3 AND date < NOW() + INTERVAL 1 DAY';
                $stmt = $con->prepare($sql);
                $stmt->execute();
                $stmt->bind_result($id, $name, $email, $phone, $state, $date);
                //storing the statement results
                $stmt->store_result();

                while($stmt->fetch()) {
                    echo $id."<br>";
                    echo $name."<br>";
                    echo $email."<br>";
                    echo $phone."<br>";
                    echo $state."<br>";
                    echo $date."<br>";
                    echo "<br>";


                    //saving the contact information of the user in variables
                    $msisdn = '45'. $phone; // msisdn: 4511223344
                    $message = 'Hej ' . $name . ' husk at du skal til tandlægen den ' . $date . " Mvh Toothbook";
                    $from = 'Toothbook'; // from: Den
                    

                    //calling the function from the top of the code, to send the actual message
                    //NOTE!! THIS FUNCTION SHOULD ALWAYS BE COMMENTED OUT WHEN TESTING!! -- unless testing the actual text message
                    //SendSms($msisdn, $message, $from);

                    //updating their state to 4 after sending the text message
                    $sql1 = 'UPDATE user SET state = 4 WHERE user_id = ?';
                    $stmt1 = $con->prepare($sql1);
                    $stmt1->bind_param('i', $id);
                    $stmt1->execute();
                    if ($stmt1->affected_rows > 0){
                        echo '<br>Du skal til tandlægen om 1 dag! Så her får du en sms som bekræftigelse<br><br><br><hr>';
                    }
                }
                //clearing the statement results
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