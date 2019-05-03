<!DOCTYPE html>
<html lang="en">
  <head>
  <!--
/*******************************************************************************
 * Badminton Intranet System
 * Copyright 2017-2019
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 ******************************************************************************/
 -->
    <meta charset="utf-8">
    <title>{{$pageTitle}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="/static/img/favicon.ico">
    <link rel="icon" type="image/png" href="/static/img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="/static/img/favicon-16x16.png" sizes="16x16" />

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>


    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
      <script src="../design/dist/js/vendor/html5shiv.js"></script>
      <script src="../design/dist/js/vendor/respond.min.js"></script>
    <![endif]-->
    <style>
        <!--
          html, body, .login-screen{ height: 100%;}
        -->
    </style>

    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.js"></script>
    <script>
    window.addEventListener("load", function(){
    window.cookieconsent.initialise({
      "palette": {
        "popup": {
          "background": "#252e39"
        },
        "button": {
          "background": "#14a7d0"
        }
      },
      "theme": "edgeless",
      "position": "bottom-right",
      "content": {
        "message": "Auf dieser Webseite werden Cookies erfasst. ",
        "dismiss": "Okay, Verstanden!",
        "link": "Mehr dazu",
        "href": "/"
      }
    })});
    </script>
</head>
