<?php

function encryptPass(String $pass) {
    return password_hash($pass, PASSWORD_DEFAULT, ['cost'=>12]);
}