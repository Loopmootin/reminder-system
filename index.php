<?php 
    require_once('db_con.php');

    if(isset($_POST['submit1'])) {

        $sql = 'UPDATE user SET state = 1';
        $stmt = $con->prepare($sql);
        $stmt->execute();
        if ($stmt->affected_rows > 0){
            echo 'State updated to 1!';
        }
    }

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
                $i = 0;

                do {

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
                                echo '<br>Du skal til tandlægen om en måned yo!<br><br><br><hr>';
                            }
                        }

                    $to = $email;
                    $from = 'mail.efif.dk 25';
                    $subject = 'Tandlægen kalder på dig';
                    $msg = $name . ' du skal til tandlægen om en måned din gris!';

                    function sendHtmlMail($to, $from, $subject, $msg) {
                        $header = "MIME-Version: 1.0" . "\r\n";
                        $header .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
                        $header .= "from:".$from;
                        return mail($to, $subject, $msg, $header);
                    }

                    $stmt->free_result();


                    $sql = 'SELECT * FROM user WHERE state = 1 AND date < NOW() + INTERVAL 1 DAY';
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

                        $sql1 = 'UPDATE user SET state = 2 WHERE user_id = ?';
                        $stmt1 = $con->prepare($sql1);
                        $stmt1->bind_param('i', $id);
                        $stmt1->execute();
                        if ($stmt1->affected_rows > 0){
                            echo '<br>Du skal til tandlægen i morgen for FANDEN!<br><br><br><hr>';
                        }
                    }

                    $stmt->free_result();   

                    echo $i;
                    $i++;

                } while($i < 3);

            } 
        ?>

        <form action="#" method="post">
            <input type="submit" name="submit-check" value="Check for appointments">
        </form>

        <form action="#" method="post">
            <input type="submit" name="submit0" value="State 0">
        </form>

        <form action="#" method="post">
            <input type="submit" name="submit1" value="State 1">
        </form>
        
    </body>
</html>