<?php
use Illuminate\Support\Facades\Mail;
Mail::raw('Test envoi email INPTIC', function($msg) {
    $msg->to('optisystem01@gmail.com')->subject('Test INPTIC');
});
echo 'Email envoye avec succes!';
