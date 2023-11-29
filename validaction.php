<?php

function usernameValidation($u, $c)
{

    $query = "SELECT * FROM `users` WHERE `username` = '$u'";
    $result = $c->query($query);

    if (empty($u)) {
        return "Username cannot be blank";
    } elseif (preg_match('/\s/', $u)) {                      //izmedju / / se stavlja sta se trazi \s je space
        return "Username cannot contain spaces";
    } elseif (strlen($u) < 5 || strlen($u) > 25) {
        return "Username must be between 5 and 25 characters";
    } elseif ($result->num_rows > 0) {
        return "Username is reserved, please choose another one";
    } else {
        return "";
    }
}


function passwordValidation($u)
{


    if (empty($u)) {
        return "Password cannot be blank";
    } elseif (preg_match('/\s/', $u)) {                      //izmedju / / se stavlja sta se trazi \s je space
        return "Password cannot contain spaces";
    } elseif (strlen($u) < 5 || strlen($u) > 50) {
        return "Password must be between 5 and 50 characters";
    } else {
        return "";
    }
}


function nameValidation($n)
{
    $n = str_replace(' ', '', $n);
    if (empty($n)) {
        return "Name cannot be empty";
    } elseif (strlen($n) > 50) {
        return "Name cannot contain more than 50 characters";
    } elseif (preg_match("/^[a-zA-ZŠšĐđŽžČčĆć]+$/", $n) == false) {
        return "Name must contain only letters";
    } else {
        return "";
    }
}

function genderValidation($g)
{
    if ($g != 'm' && $g != 'f' & $g != 'o') {
        return "Unknown gender";
    } else {
        return "";
    }
}

function dobValidation($d)
{
    if (empty($d)) {
        return "";
    } elseif ($d < "1900-01-01") {
        return "Date of birth not valid";
    } else {
        return "";
    }
}


function profileExists($id, $conn)
{
    $q = "SELECT * FROM `profiles` WHERE `id_user` = $id";
    $result = $conn->query($q);

    if ($result->num_rows == 0) {
        return false;
    } else {
        $row = $result->fetch_assoc();
        return $row;
    }
}

//za ispis slike
function printImg($u)
{
    echo "<img class='profil' src= $u alt='Avatar'>";
}


//za proveru pola 
function checkGender($gender)
{
    $avatar = 'images/otherAvatar.jpg';
    $mAvatar = 'images/mAvatar.png';
    $fAvatar = 'images/fAvatar.jpg';
    if ($gender == 'm') {
        printImg($mAvatar);
    } elseif ($gender == 'f') {
        printImg($fAvatar);
    } elseif ($gender == 'o') {
        printImg($avatar);
    }
}


// ispis slike korisnika

function ProfileImg($conn, $id)
{
    $q = "SELECT `u`.`id` as `user`,
    `p`.`gender` as `gender`,
    `p`.`id` as `profileId`, 
    `img`
    FROM `users` AS `u`
    LEFT JOIN `profiles` AS `p`
    ON `u`.`id` = `p`.`id_user`
    WHERE `u`.`id` = $id";  // kad je user id == $id iz sesije
    $result = $conn->query($q);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row['profileId']) {
                if ($row['img'] != NULL) {
                    $i = "images/" . $row['img']; //imaju svoju sliku
                    printImg($i);
                } else {
                    checkGender($row['gender']);    //poziva f-ju  i stampa avatara po polu                
                }
            }
        }
    }
}


//brpj pracenja za sve korisnike
function NumberFollowFromOther($conn, $userId)
{
    $praceni = "SELECT COUNT(`id_receiver`) as 'Num' FROM `followers` WHERE `id_sender` =$userId";
    $result = $conn->query($praceni);
    if ($result->num_rows == 0) {
        return "No following users";
    } else {
        while ($row = $result->fetch_assoc()) {
            return $row['Num'];
        }
    }
}

//broj pratilaca za sve korisnike
function NumberFollowsFromOther($conn, $userId)
{
    $pratioci = "SELECT COUNT(`id_sender`) as 'Num' FROM `followers` WHERE `id_receiver` =$userId";
    $result = $conn->query($pratioci);
    if ($result->num_rows == 0) {
        return "No followers";
    } else {
        while ($row = $result->fetch_assoc()) {
            return $row['Num'];
        }
    }
}

// funckije koriscene u myprofile.php
function DobofUsers($conn, $id)
{
    $dob = "";

    $q = "SELECT `p`.`dob`
    
    FROM `users` AS `u`
    LEFT JOIN `profiles` AS `p` ON `u`.`id`= `p`.`id_user`
    WHERE `u`.`id` = $id;";
    $result = $conn->query($q);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dob = $row['dob'];
            return $dob;
        }
    }
}

function GenderofUsers($conn, $id)
{
    $gender = "";

    $q = "SELECT `p`.`gender`
    
    FROM `users` AS `u`
    LEFT JOIN `profiles` AS `p` ON `u`.`id`= `p`.`id_user`
    WHERE `u`.`id` = $id;";
    $result = $conn->query($q);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // $gender = $row['gender'];
            if ($row['gender'] == 'f') {
                $gender = "female";
            }
            elseif ($row['gender'] == 'm') {
                $gender = "male";
            }
            else{
                $gender = "other";
            }
            return $gender;
        }
    }
}

function bioOfUsers($conn, $id)
{
    $bio = "";

    $q = "SELECT `p`.`bio`
    
    FROM `users` AS `u`
    LEFT JOIN `profiles` AS `p` ON `u`.`id`= `p`.`id_user`
    WHERE `u`.`id` = $id;";
    $result = $conn->query($q);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bio = $row['bio'];
            return $bio;
        }
    }
}

//broj pracenja
function NumberFollow($conn, $id)
{
    $follow = "";
    $q = "SELECT 
    COUNT(followers.id_sender) AS`numb`
    FROM followers
    WHERE id_sender= $id;";
    $result = $conn->query($q);
    if ($result->num_rows == 0) {

        return "No follow";
    } else {
        while ($row = $result->fetch_assoc()) {
            $follow = $row['numb'];
            return $follow;
        }
    }
}

//broj pratilaca 

function NumberFollows($conn, $id)
{
    $follow = "";
    $q = "SELECT 
    COUNT(followers.id_receiver) AS `numb`
    FROM followers
    WHERE id_receiver=$id;";
    $result = $conn->query($q);
    if ($result->num_rows == 0) {

        return "No followers";
    } else {
        while ($row = $result->fetch_assoc()) {
            $follow = $row['numb'];
            return $follow;
        }
    }
}
