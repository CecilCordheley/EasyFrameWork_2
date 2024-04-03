<?php
namespace vendor\easyFrameWork\Core;
    abstract class Main{
        public static $links = [
            [
                "href" => "cours.php",
                "PageName" => "Les cours"
            ], [
                "href" => "video.php",
                "PageName" => "Les vidéos",
                "title"=>"voir les vidéos tuto et autres"
            ], [
                "href" => "MesLives/",
                "PageName" => "Lives",
                "title" => "Planning des lives"
            ], [
                "href" => "assets.php",
                "PageName" => "Accès assets",
                "title"=>"Voir les assets et widget"
            ]
        ];
    }
