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
        $username = '';
        $apikey = '';
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

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" media="screen" href="style.css" />

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>

        <title>Reminder system</title>
    </head>
    <body>
        <header>
            <h1>Toothbook</h1>
        </header>
        <article>
        <h4 class="center"><img class="step-img" src="tooth.svg" alt="">All users in the database:</h4>
        <div class="users center-box">
            <?php 

                //pulling all users from the database and listing them on the site
                $sql = 'SELECT * FROM user';
                $stmt = $con->prepare($sql);
                $stmt->execute();
                $stmt->bind_result($id, $name, $email, $phone, $state, $date);

                while($stmt->fetch()) {
                    echo '<div class="user-box">';
                    echo    "ID: " . $id."<br>";
                    echo    "Name: " . $name."<br>";
                    echo    "Email: " . $email."<br>";
                    echo    "Phone: " . $phone."<br>";
                    echo    "State: " . $state."<br>";
                    echo    "Date: " . $date."<br>";
                    echo "</div>";
                    echo "<br>";
                }
            ?>
        </div>
        <hr>

        <?php
            //running an if on the check appointments button
            if(isset($_POST['submit-check'])) {
                echo '<h4 class="center"><img class="step-img" src="calender.svg" alt="">All users at stage 0 in the database:</h4>';
                echo '<div class="users">';

                //checking for users at state = 0 with less than a month to their appointment 
                $sql = 'SELECT * FROM user WHERE state = 0 AND date < NOW() + INTERVAL 30 DAY'; //note that this should be 30 and not 4 days
                $stmt = $con->prepare($sql);
                $stmt->execute();
                //binding the results to variables that makes sense name wise
                $stmt->bind_result($id, $name, $email, $phone, $state, $date);
                //storing the statement results
                $stmt->store_result();

                while($stmt->fetch()) {
                    echo '<div class="user-box">';
                    echo    "ID: " . $id."<br>";
                    echo    "Name: " . $name."<br>";
                    echo    "Email: " . $email."<br>";
                    echo    "Phone: " . $phone."<br>";
                    echo    "State: " . $state."<br>";
                    echo    "Date: " . $date."<br>";
                    echo "</div>";
                    echo "<br>";

                    //updating the state to 1 of the user with mactching id 
                    $sql1 = 'UPDATE user SET state = 1 WHERE user_id = ?';
                    $stmt1 = $con->prepare($sql1);
                    $stmt1->bind_param('i', $id);
                    $stmt1->execute();
                    if ($stmt1->affected_rows > 0){
                            //echo '<br>1 måned til tandlægetid, men i dette trin sker der ikke noget<br><br><br><hr>';
                        }
                }
                //clearing the statement results
                $stmt->free_result();
            echo '</div>';
/*-----------------------------------------------------------------------------------------------------------------------------*/
            echo '<hr>';
            echo '<h4 class="center"><img class="step-img" src="mail.svg" alt="">All users at stage 1 in the database:</h4>';
            echo '<div class="users">';
                //checking for users at state = 1 with less than a month to their appointment 
                $sql = 'SELECT * FROM user WHERE state = 1 AND date < NOW() + INTERVAL 30 DAY'; //note that this should be 30 and not 4 days
                $stmt = $con->prepare($sql);
                $stmt->execute();
                //binding the results to variables that makes sense name wise
                $stmt->bind_result($id, $name, $email, $phone, $state, $date);
                //storing the statement results
                $stmt->store_result();

                while($stmt->fetch()) {
                    echo '<div class="user-box">';
                    echo    "ID: " . $id."<br>";
                    echo    "Name: " . $name."<br>";
                    echo    "Email: " . $email."<br>";
                    echo    "Phone: " . $phone."<br>";
                    echo    "State: " . $state."<br>";
                    echo    "Date: " . $date."<br>";
                    echo "</div>";
                    echo "<br>";

                    //running the mail form and sending it to the person whos contact information is saved in the variables above
                    $to = $email;
                    $subject = "My subject";
                    $txt = "Hi " . $name . " remember your dentist appointment at " . $date;
                    $headers = "From: amanda@amandaap.dk" . "\r\n";

                    mail($to,$subject,$txt,$headers);

                    //echo 'Sending mail to '.$email;

                    //updating the state of the user from 1 to 2 of the users who has recieved a mail
                    $sql1 = 'UPDATE user SET state = 2 WHERE user_id = ?';
                    $stmt1 = $con->prepare($sql1);
                    $stmt1->bind_param('i', $id);
                    $stmt1->execute();
                    if ($stmt1->affected_rows > 0){
                            //echo '<br>Du skal til tandlægen om 1 måned! Så her får du en mail som bekræftigelse<br><br><br><hr>';
                        }
                }
                //clearing the statement results
                $stmt->free_result();
            echo '</div>';
/*-----------------------------------------------------------------------------------------------------------------------------*/              
            echo '<hr>';
            echo '<h4 class="center"><img class="step-img" src="calender.svg" alt="">All users at stage 2 in the database:</h4>';
            echo '<div class="users">';
                //checking for users at state = 2 with less than 1 day to their appointment 
                $sql = 'SELECT * FROM user WHERE state = 2 AND date < NOW() + INTERVAL 1 DAY';
                $stmt = $con->prepare($sql);
                $stmt->execute();
                $stmt->bind_result($id, $name, $email, $phone, $state, $date);
                //storing the statement results
                $stmt->store_result();

                while($stmt->fetch()) {
                    echo '<div class="user-box">';
                    echo    "ID: " . $id."<br>";
                    echo    "Name: " . $name."<br>";
                    echo    "Email: " . $email."<br>";
                    echo    "Phone: " . $phone."<br>";
                    echo    "State: " . $state."<br>";
                    echo    "Date: " . $date."<br>";
                    echo "</div>";
                    echo "<br>";

                    //updating their state to 3
                    $sql1 = 'UPDATE user SET state = 3 WHERE user_id = ?';
                    $stmt1 = $con->prepare($sql1);
                    $stmt1->bind_param('i', $id);
                    $stmt1->execute();
                    if ($stmt1->affected_rows > 0){
                        //echo '<br>Du skal til tandlægen om 1 dag, men i dette trin sker der ikke noget<br><br><br><hr>';
                    }
                }
                //clearing the statement results
                $stmt->free_result();   
            echo '</div>';
/*-----------------------------------------------------------------------------------------------------------------------------*/
            echo '<hr>';
            echo '<h4 class="center"><img class="step-img" src="phone.svg" alt="">All users at stage 3 in the database:</h4>';
            echo '<div class="users">';
                //checking for users at state = 3 with less than 1 day to their appointment 
                $sql = 'SELECT * FROM user WHERE state = 3 AND date < NOW() + INTERVAL 1 DAY';
                $stmt = $con->prepare($sql);
                $stmt->execute();
                $stmt->bind_result($id, $name, $email, $phone, $state, $date);
                //storing the statement results
                $stmt->store_result();

                while($stmt->fetch()) {
                    echo '<div class="user-box">';
                    echo    "ID: " . $id."<br>";
                    echo    "Name: " . $name."<br>";
                    echo    "Email: " . $email."<br>";
                    echo    "Phone: " . $phone."<br>";
                    echo    "State: " . $state."<br>";
                    echo    "Date: " . $date."<br>";
                    echo "</div>";
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
                        //echo '<br>Du skal til tandlægen om 1 dag! Så her får du en sms som bekræftigelse<br><br><br><hr>';
                    }
                }
                //clearing the statement results
                $stmt->free_result();
            echo '</div>';
            } 
        ?>
        <hr>
        <form class="center" action="#" method="post">
            <input class="btn" type="submit" name="submit-check" value="Check for appointments">
        </form>

        <br>

        <form class="center" action="#" method="post">
            <input class="btn" type="submit" name="submit0" value="State 0">
        </form>

        </article>
        
    </body>
</html>